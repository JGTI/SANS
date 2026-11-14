<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactInquiryRequest;
use App\Mail\ContactInquiryMail;
use App\Mail\ContactConfirmationMail;
use App\Models\ContactInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class ContactInquiryController extends Controller
{
    public function __invoke(StoreContactInquiryRequest $request): JsonResponse
    {
        $recipients = config('contact.recipients', []);
        $secretKey = config('recaptcha.secret_key');

        if (! $secretKey) {
            report(new RuntimeException('Contact form configuration is incomplete.'));

            return response()->json([
                'message' => 'El formulario no está disponible temporalmente. Intenta de nuevo más tarde.',
            ], 503);
        }

        $verification = Http::asForm()
            ->timeout(10)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $request->string('recaptcha_token')->toString(),
                'remoteip' => $request->ip(),
            ]);
        $result = $verification->json();
        $allowedHostnames = config('recaptcha.allowed_hostnames', []);
        $validHostname = $allowedHostnames === [] || in_array($result['hostname'] ?? null, $allowedHostnames, true);

        if (! $verification->successful()
            || ($result['success'] ?? false) !== true
            || ($result['action'] ?? null) !== 'contact'
            || ($result['score'] ?? 0) < config('recaptcha.minimum_score')
            || ! $validHostname) {
            return response()->json([
                'message' => 'No fue posible verificar tu solicitud. Intenta nuevamente.',
            ], 422);
        }

        $inquiry = $request->safe()->except(['website', 'recaptcha_token']);

        $record = ContactInquiry::create(collect($inquiry)->except('agree')->all() + ['consented_at' => now()]);

        // The inbox keeps the message even if the email provider is unavailable.
        try {
            if ($recipients !== []) {
                Mail::to($recipients)->cc(config('contact.cc', []))->send(new ContactInquiryMail($inquiry));
                $record->update(['email_sent_at' => now()]);
            }
        } catch (\Throwable $exception) {
            report($exception);
        }

        try {
            if (config('contact.send_confirmation')) {
                Mail::to($inquiry['email'])->send(new ContactConfirmationMail($inquiry));
            }
        } catch (\Throwable $exception) {
            report($exception);
        }

        return response()->json([
            'message' => 'Gracias. Recibimos tu mensaje y te contactaremos pronto.',
        ]);
    }
}
