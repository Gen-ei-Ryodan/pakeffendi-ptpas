<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BuyerVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public string $code
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifikasi Email - PAS Market',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.buyer-verification',
            with: [
                'customer' => $this->customer,
                'code' => $this->code,
                'verificationUrl' => route('guest.verify-email.direct', ['code' => $this->code]),
            ],
        );
    }
}
