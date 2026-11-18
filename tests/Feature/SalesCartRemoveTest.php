<?php

namespace Tests\Feature;

use App\Http\Controllers\Guest\CartController;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SalesCartRemoveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    /**
     * @return array{sales: User, buyer: Customer, product: Product}
     */
    private function seedSalesBuyerProduct(): array
    {
        $sales = User::query()->create([
            'name' => 'Sales One',
            'email' => 'sales@example.com',
            'password' => bcrypt('password123'),
            'role' => 'sales',
            'photo_path' => null,
        ]);

        $buyer = Customer::query()->create([
            'customer_code' => 'CUST-001',
            'full_name' => 'Testing Vina',
            'account_type' => 'Retail',
            'ktp_number' => '123',
            'npwp' => null,
            'email' => 'vina@example.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
            'address' => 'Alamat',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'postal_code' => '40111',
            'phone' => '081200000001',
            'contact_person' => 'Testing Vina',
            'company_name' => null,
            'internal_code' => null,
            'sales_id' => $sales->id,
        ]);

        $category = ProductCategory::query()->create(['category_code' => 'POWER_TOOL', 'name' => 'POWER TOOL']);
        $brand = ProductBrand::query()->create(['brand_code' => 'BOSCH', 'brand_name' => 'BOSCH']);

        $product = Product::query()->create([
            'sku' => 'SKU-00001',
            'name' => 'TEST PRODUCT',
            'product_brand_code' => $brand->brand_code,
            'product_category_code' => $category->category_code,
            'description' => 'desc',
            'unit' => 'pcs',
            'weight_kg' => 1,
            'discontinued' => false,
            'photo_path' => null,
            'price_1' => 10000,
            'qty_1' => 1,
            'disc_1' => 0,
            'qty_2' => 0,
            'disc_2' => 0,
            'qty_3' => 0,
            'disc_3' => 0,
        ]);

        return ['sales' => $sales, 'buyer' => $buyer, 'product' => $product];
    }

    public function test_sales_delete_removes_item_from_selected_customer_cart_and_stays_deleted(): void
    {
        ['sales' => $sales, 'buyer' => $buyer, 'product' => $product] = $this->seedSalesBuyerProduct();

        $this->actingAs($sales);

        // Selected customer present in session (fallback path when cookie decrypt fails)
        $this->withSession([CartController::SALES_CUSTOMER_COOKIE => $buyer->id]);

        $this->postJson('/cart/items', ['product_id' => $product->id, 'quantity' => 2])
            ->assertCreated()
            ->assertJsonPath('summary.total_items', 2);

        $cart = Cart::query()->where('customer_id', $buyer->id)->where('status', 'active')->latest('id')->first();
        $this->assertNotNull($cart);
        $this->assertDatabaseHas('cart_items', ['cart_id' => $cart->id, 'product_id' => $product->id]);

        // Delete must target the customer cart (not a guest fallback cart)
        $this->deleteJson('/cart/items/'.$product->id)->assertOk();

        $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id, 'product_id' => $product->id]);

        // Refresh: re-resolve the cart — the item must NOT come back
        $this->get('/cart')->assertOk()->assertDontSee($product->name, false);

        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id]);
    }

    public function test_sales_delete_without_selected_customer_is_rejected_not_guest_fallback(): void
    {
        ['sales' => $sales, 'product' => $product] = $this->seedSalesBuyerProduct();

        // Guest/session cart exists with an item (stale context)
        $guestCart = Cart::query()->create([
            'customer_id' => null,
            'session_id' => 'guest-session-1',
            'status' => 'active',
        ]);
        CartItem::query()->create([
            'cart_id' => $guestCart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $this->actingAs($sales);

        $this->deleteJson('/cart/items/'.$product->id)->assertStatus(422);

        // The guest cart must remain untouched — no silent fallback delete
        $this->assertDatabaseHas('cart_items', ['cart_id' => $guestCart->id, 'product_id' => $product->id]);
    }

    public function test_resolve_skips_guest_cart_merge_for_sales_context(): void
    {
        ['sales' => $sales, 'buyer' => $buyer, 'product' => $product] = $this->seedSalesBuyerProduct();

        $guestCart = Cart::query()->create([
            'customer_id' => null,
            'session_id' => 'guest-session-2',
            'status' => 'active',
        ]);
        CartItem::query()->create([
            'cart_id' => $guestCart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $service = app(CartService::class);

        // Sales context: no merge, guest cookie flushed to customer cart session
        $request = Request::create('/cart', 'GET', cookies: [CartService::COOKIE_NAME => 'guest-session-2']);
        $resolved = $service->resolve($request, $buyer, (int) $sales->id);

        $this->assertSame(0, $resolved['cart']->items()->count(), 'Sales context must not merge guest cart items');
        $this->assertSame($resolved['cart']->session_id, $resolved['cookie']->getValue(), 'Guest cookie must be flushed to customer cart session');
        $this->assertSame(1, $guestCart->items()->count(), 'Guest cart items stay untouched (no merge loop)');

        // Buyer context ($salesId null): merge still happens per business rules
        $request2 = Request::create('/cart', 'GET', cookies: [CartService::COOKIE_NAME => 'guest-session-2']);
        $resolved2 = $service->resolve($request2, $buyer);

        $this->assertSame(1, $resolved2['cart']->items()->count(), 'Buyer login flow must merge guest cart items');
        $this->assertSame('converted', $guestCart->fresh()->status);
    }

    public function test_select_customer_remembers_context_in_session_and_cookie(): void
    {
        ['sales' => $sales, 'buyer' => $buyer] = $this->seedSalesBuyerProduct();

        $this->actingAs($sales);

        $response = $this->get('/cart/select-customer/'.$buyer->id);
        $response->assertRedirect('/cart');
        $response->assertCookie(CartController::SALES_CUSTOMER_COOKIE, (string) $buyer->id);

        $this->assertSame($buyer->id, session(CartController::SALES_CUSTOMER_COOKIE));

        // Clearing removes both session and cookie context
        $clear = $this->get('/cart/clear-customer');
        $clear->assertRedirect('/cart');
        $this->assertNull(session(CartController::SALES_CUSTOMER_COOKIE));
    }

    public function test_photo_url_prefers_webp_variant_when_available(): void
    {
        Storage::fake('public');

        ['product' => $product] = $this->seedSalesBuyerProduct();

        $product->update(['photo_path' => 'products/sample.jpg']);
        Storage::disk('public')->put('products/sample.jpg', 'fake-jpg-bytes');

        // Without .webp → original extension
        $this->assertStringEndsWith('storage/products/sample.jpg', $product->fresh()->photo_url);

        Storage::disk('public')->put('products/sample.webp', 'fake-webp-bytes');

        // With .webp → WebP variant prioritized
        $this->assertStringEndsWith('storage/products/sample.webp', $product->fresh()->photo_url);
    }
}
