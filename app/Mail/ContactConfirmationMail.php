<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $inquiry)
    {
    }

    public function build(): self
    {
        return $this->subject('Recibimos tu mensaje · NEXUS DIGITAL DB')
            ->view('mail.contact-confirmation');
    }
}
