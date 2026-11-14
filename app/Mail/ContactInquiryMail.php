<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $inquiry)
    {
    }

    public function build(): self
    {
        return $this
            ->replyTo($this->inquiry['email'], $this->inquiry['name'])
            ->subject('Nuevo contacto web · '.$this->inquiry['name'])
            ->view('mail.contact-inquiry');
    }
}
