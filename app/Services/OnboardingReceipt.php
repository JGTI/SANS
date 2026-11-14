<?php

namespace App\Services;

use App\Mail\OnboardingReceivedMail;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OnboardingReceipt
{
    public function send(Project $project): void
    {
        $details = [
            'project' => $project->name,
            'company' => $project->answers['company'],
            'contact' => $project->answers['contact_name'],
            'email' => $project->answers['contact_email'],
            'reference' => 'NX-'.str_pad((string) $project->id, 5, '0', STR_PAD_LEFT),
        ];

        // Send independently: a failure must never undo the saved submission
        // or prevent the other recipient from receiving their notification.
        foreach (['client' => [$details['email']], 'team' => config('contact.recipients', [])] as $audience => $recipients) {
            if ($recipients === []) {
                Log::warning('Onboarding notification has no recipients.', ['project_id' => $project->id, 'audience' => $audience]);
                continue;
            }
            try {
                $mail = Mail::to($recipients);
                if ($audience === 'team') {
                    $mail->cc(config('contact.cc', []));
                }
                $mail->send(new OnboardingReceivedMail($details, $audience === 'team'));
                Log::info('Onboarding notification handed to mail transport.', ['project_id' => $project->id, 'audience' => $audience]);
            } catch (\Throwable $exception) {
                // Do not log SMTP credentials, client answers or invitation tokens.
                Log::error('Onboarding notification failed.', ['project_id' => $project->id, 'audience' => $audience, 'exception_type' => get_class($exception)]);
            }
        }
    }
}
