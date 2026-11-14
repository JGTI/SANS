<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OnboardingReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $details, public readonly bool $internal = false) {}

    public function build(): self
    {
        if ($this->internal) {
            $this->replyTo($this->details['email'], $this->details['contact']);
        }

        return $this->subject($this->internal
            ? 'Onboarding recibido · '.$this->details['project']
            : 'Tu proyecto ya tiene un punto de partida · NEXUS DIGITAL DB')
            ->view('mail.onboarding-received')
            ->text('mail.onboarding-received-text');
    }
}
