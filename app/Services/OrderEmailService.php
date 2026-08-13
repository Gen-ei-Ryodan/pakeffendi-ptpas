<?php

namespace App\Services;

use App\Mail\SalesOrderCreatedMail;
use App\Models\SalesOrder;
use App\Models\User;
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

        $recipients = collect([
            $order->customer?->email,
            $order->salesPerson?->email,
            ...$this->adminEmails(),
        ])
            ->filter(fn ($email) => $this->isDeliverableEmail($email))
            ->map(fn ($email) => strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new SalesOrderCreatedMail($order));
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
     * All admin/super admin users from the database plus any extra addresses
     * configured via ADMIN_EMAIL (comma/semicolon separated).
     *
     * @return array<int, string>
     */
    private function adminEmails(): array
    {
        $dbAdmins = User::query()
            ->whereIn('role', ['admin', 'super admin', 'super_admin', 'superadmin'])
            ->pluck('email')
            ->all();

        $envAdmins = preg_split('/[,;]+/', (string) config('mail.admin_email'), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_merge($dbAdmins, $envAdmins);
    }

    /**
     * Skip invalid addresses and dummy/local-only addresses (e.g. @pas.local)
     * that can never be delivered so they do not block real recipients.
     */
    private function isDeliverableEmail(mixed $email): bool
    {
        if (! is_string($email) || filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $domain = strtolower((string) substr((string) $email, strrpos((string) $email, '@') + 1));

        return ! str_ends_with($domain, '.local') && $domain !== 'localhost';
    }
}
