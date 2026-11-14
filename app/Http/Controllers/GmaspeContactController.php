<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGmaspeContactRequest;
use App\Mail\GmaspeContactMail;
use App\Services\GmaspeCaptcha;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class GmaspeContactController extends Controller
{
    public function challenge(GmaspeCaptcha $captcha): JsonResponse
    {
        return response()
            ->json($captcha->issue())
            ->header('Cache-Control', 'no-store, private');
    }

    public function store(StoreGmaspeContactRequest $request, GmaspeCaptcha $captcha): JsonResponse
    {
        if (! $captcha->verify(
            $request->string('captcha_token')->toString(),
            $request->integer('captcha_answer'),
        )) {
            return response()->json([
                'message' => 'La verificación venció o la respuesta no es correcta. Intenta nuevamente.',
                'errors' => ['captcha_answer' => ['Verificación inválida.']],
            ], 422);
        }

        $recipients = config('gmaspe.contact_recipients', []);

        if ($recipients === []) {
            report(new RuntimeException('GMASPE contact recipients are not configured.'));

            return response()->json([
                'message' => 'El formulario no está disponible temporalmente. Intenta de nuevo más tarde.',
            ], 503);
        }

        $inquiry = $request->safe()->except([
            'privacy',
            'website',
            'captcha_token',
            'captcha_answer',
        ]);

        try {
            Mail::to($recipients)->send(new GmaspeContactMail($inquiry));
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'No pudimos enviar tu solicitud en este momento. Intenta nuevamente más tarde.',
            ], 503);
        }

        return response()->json([
            'message' => 'Gracias. Tu solicitud fue enviada a Grupo MASPE.',
        ]);
    }
}
