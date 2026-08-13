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

    public function test_order_email_is_sent_to_all_real_admins_and_skips_dummy_local_addresses(): void
    {
        Mail::fake();
        config([
            'mail.admin_email' => 'env-admin@example.com; env-dummy@pas.local',
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

        $admin = User::query()->create([
            'name' => 'Admin One',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $superAdmin = User::query()->create([
            'name' => 'Super Admin',
            'email' => 'super-admin@example.com',
            'password' => 'password',
            'role' => 'super admin',
        ]);

        $dummyAdmin = User::query()->create([
            'name' => 'Dummy Admin',
            'email' => 'dummy@pas.local',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $order = SalesOrder::query()->create([
            'order_no' => 'W2608100001',
            'order_date' => now(),
            'customer_id' => $customer->id,
            'sales_person_id' => $sales->id,
            'status' => SalesOrder::STATUS_NEW,
        ]);

        app(OrderEmailService::class)->send($order);

        $expected = ['buyer@example.com', 'sales@example.com', 'admin@example.com', 'super-admin@example.com', 'env-admin@example.com'];

        Mail::assertQueued(SalesOrderCreatedMail::class, count($expected));

        foreach ($expected as $recipient) {
            Mail::assertQueued(
                SalesOrderCreatedMail::class,
                fn (SalesOrderCreatedMail $mail): bool => $mail->hasTo($recipient),
            );
        }

        Mail::assertNotQueued(
            SalesOrderCreatedMail::class,
            fn (SalesOrderCreatedMail $mail): bool => $mail->hasTo('dummy@pas.local'),
        );

        Mail::assertNotQueued(
            SalesOrderCreatedMail::class,
            fn (SalesOrderCreatedMail $mail): bool => $mail->hasTo('env-dummy@pas.local'),
        );
    }
}
