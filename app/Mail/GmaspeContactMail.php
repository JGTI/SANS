<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GmaspeContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $inquiry) {}

    public function build(): self
    {
        return $this
            ->replyTo($this->inquiry['email'], $this->inquiry['name'])
            ->subject('Nueva solicitud desde gmaspe.com · '.($this->inquiry['service'] ?? 'Información'))
            ->view('mail.gmaspe-contact');
    }
}
