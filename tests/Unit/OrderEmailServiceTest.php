<?php

namespace Tests\Unit;

use App\Mail\SalesOrderCreatedMail;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\OrderEmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderEmailServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_email_is_sent_separately_to_customer_sales_and_each_admin(): void
    {
        Mail::fake();
        config([
            'mail.admin_email' => 'admin@pas.local, real-admin@example.com; admin@pas.local',
        ]);

        $customer = Customer::query()->create([
            'customer_code' => 'CUST-EMAIL-001',
            'full_name' => 'Buyer One',
            'account_type' => 'Retail',
            'ktp_number' => '1234567890',
            'email' => 'buyer@example.com',
            'password' => 'password',
            'phone' => '081200000001',
            'contact_person' => 'Buyer One',
            'status' => 'active',
        ]);

        $sales = User::query()->create([
            'name' => 'Sales One',
            'email' => 'sales@example.com',
            'password' => 'password',
            'role' => 'sales',
        ]);

        $order = SalesOrder::query()->create([
            'order_no' => 'W2608100001',
            'order_date' => now(),
            'customer_id' => $customer->id,
            'sales_person_id' => $sales->id,
            'status' => SalesOrder::STATUS_NEW,
        ]);

        app(OrderEmailService::class)->send($order);

        Mail::assertQueued(SalesOrderCreatedMail::class, 4);

        foreach (['buyer@example.com', 'sales@example.com', 'admin@pas.local', 'real-admin@example.com'] as $recipient) {
            Mail::assertQueued(
                SalesOrderCreatedMail::class,
                fn (SalesOrderCreatedMail $mail): bool => $mail->hasTo($recipient),
            );
        }
    }
}
