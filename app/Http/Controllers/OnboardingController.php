<?php

namespace App\Http\Controllers;

use App\Services\ProjectOnboarding;
use App\Services\OnboardingReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function __construct(private ProjectOnboarding $onboarding) {}

    public function show(string $token)
    {
        $project = $this->onboarding->find($token);
        $payload = [
            'name' => $project->name, 'company' => $project->company,
            'answers' => $project->answers ?? ['company' => $project->company, 'contact_name' => $project->contact_name, 'contact_email' => $project->contact_email],
            'submitted' => (bool) $project->submitted_at,
            'attachments' => $project->attachments()->get(['id', 'category', 'original_name', 'size']),
            'sections' => config('onboarding.sections'),
            'maxFiles' => config('onboarding.max_files'), 'maxFileKb' => config('onboarding.max_file_kb'),
            'endpoint' => route('onboarding.show', $token),
            'logos' => ['light' => asset('landing/logos/logo black.png'), 'dark' => asset('landing/logos/logo white.png')],
        ];

        return response()->view('public.onboarding', compact('payload'));
    }

    public function save(Request $request, string $token)
    {
        $request->validate(['answers' => ['required', 'array'], 'submit' => ['required', 'boolean']]);

        $project = DB::transaction(function () use ($request, $token) {
            $project = $this->onboarding->find($token, true);
            abort_if($project->submitted_at, 409, 'Ya recibimos este formulario. Contacta a nuestro equipo si necesitas corregirlo.');
            $answers = $this->onboarding->answers($request->input('answers'), $request->boolean('submit'));
            $project->answers = $answers;
            $project->draft_saved_at = now();
            if ($request->boolean('submit')) {
                $project->submitted_at = now();
            }
            $project->save();

            return $project;
        });

        if ($request->boolean('submit')) {
            app(OnboardingReceipt::class)->send($project);
        }

        return response()->json(['message' => $project->submitted_at ? 'Información recibida.' : 'Borrador guardado.', 'submitted' => (bool) $project->submitted_at]);
    }

    public function upload(Request $request, string $token)
    {
        $categories = collect(config('onboarding.sections'))->pluck('fields')->flatten(1)->where('type', 'files')->pluck('key')->all();
        $request->validate([
            'category' => ['required', Rule::in($categories)],
            'file' => ['required', 'file', 'min:1', 'max:'.config('onboarding.max_file_kb'),
                'mimes:jpg,jpeg,png,webp,gif,svg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx,mp4,mp3',
                'extensions:jpg,jpeg,png,webp,gif,svg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx,mp4,mp3'],
        ], [
            'file.required' => 'Selecciona un archivo para compartir.',
            'file.uploaded' => 'No se pudo recibir el archivo. Revisa su tamaño e intenta nuevamente.',
            'file.file' => 'Selecciona un archivo válido.',
            'file.min' => 'El archivo está vacío o es demasiado pequeño. Revisa su contenido.',
            'file.max' => 'El archivo supera el límite de '.(config('onboarding.max_file_kb') / 1024).' MB. Comparte una versión más pequeña.',
            'file.mimes' => 'Este formato no está permitido. Comparte una imagen, PDF, documento de Office, TXT, CSV, MP3 o MP4.',
            'file.extensions' => 'La extensión del archivo no está permitida. Revisa los formatos indicados en el formulario.',
            'category.required' => 'Selecciona la sección donde quieres compartir el archivo.',
            'category.in' => 'Esta sección no permite recibir archivos.',
        ]);
        $path = null;
        try {
            return DB::transaction(function () use ($request, $token, &$path) {
                $project = $this->onboarding->find($token, true);
                abort_if($project->submitted_at, 409, 'El formulario ya fue enviado.');
                abort_if($project->attachments()->count() >= config('onboarding.max_files'), 422, 'Alcanzaste el límite de archivos.');
                if ($request->input('category') === 'logo') {
                    $request->validate(['file' => ['mimes:jpg,jpeg,png,webp,gif,svg,pdf', 'extensions:jpg,jpeg,png,webp,gif,svg,pdf']], [
                        'file.mimes' => 'Para el logotipo, comparte una imagen o PDF.',
                        'file.extensions' => 'Para el logotipo, comparte JPG, PNG, WEBP, GIF, SVG o PDF.',
                    ]);
                }
                $path = $request->file('file')->store('onboarding/'.$project->id, 'local');
                abort_unless($path, 503, 'No pudimos guardar el archivo. Intenta nuevamente.');
                $file = $project->attachments()->create([
                    'category' => $request->input('category'), 'path' => $path,
                    'original_name' => mb_substr(basename(str_replace('\\', '/', $request->file('file')->getClientOriginalName())), 0, 200),
                    'size' => $request->file('file')->getSize(),
                ]);

                return response()->json($file, 201);
            });
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            throw $exception;
        }
    }

    public function removeFile(string $token, int $file)
    {
        return DB::transaction(function () use ($token, $file) {
            $project = $this->onboarding->find($token, true);
            abort_if($project->submitted_at, 409, 'El formulario ya fue enviado.');
            $attachment = $project->attachments()->findOrFail($file);
            abort_unless(Storage::disk('local')->delete($attachment->path), 503, 'No pudimos eliminar el archivo.');
            $attachment->delete();

            return response()->json(['message' => 'Archivo eliminado.']);
        });
    }
}
