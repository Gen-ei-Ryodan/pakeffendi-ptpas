<?php

namespace App\Services;

use App\Mail\SalesOrderCreatedMail;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderEmailService
{
    /**
     * Send the order email independently to each available recipient.
     */
    public function send(SalesOrder $order): void
    {
        $order->load(['customer', 'salesPerson', 'items.product']);

        $recipients = array_values(array_unique(array_map('trim', array_filter([
            $order->customer?->email,
            $order->salesPerson?->email,
            ...$this->adminEmails(),
        ], static fn ($email): bool => is_string($email)
            && filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false))));

        foreach ($recipients as $recipient) {
            try {
                Mail::to(trim($recipient))->send(new SalesOrderCreatedMail($order));
            } catch (Throwable $exception) {
                Log::error(
                    'Failed to send order email for order #'.$order->order_no.' to '.$recipient.': '
                    .$exception->getMessage(),
                    ['exception' => $exception],
                );
            }
        }
    }

    /**
     * ADMIN_EMAIL supports multiple addresses separated by commas or semicolons.
     * Invalid addresses are filtered before attempting SMTP delivery.
     *
     * @return array<int, string>
     */
    private function adminEmails(): array
    {
        return preg_split('/[,;]+/', (string) config('mail.admin_email'), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
