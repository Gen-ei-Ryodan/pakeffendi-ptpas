<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChangePasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public string $code
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi Ubah Password - PAS Market',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.change-password',
            with: [
                'customer' => $this->customer,
                'code' => $this->code,
            ],
        );
    }
}
