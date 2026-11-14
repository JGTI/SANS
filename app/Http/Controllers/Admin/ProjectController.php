<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return DataTables::eloquent(Project::query()->select(['id', 'name', 'company', 'contact_name', 'contact_email', 'status', 'link_enabled', 'expires_at', 'submitted_at', 'draft_saved_at', 'created_at']))
                ->filter(function ($query) use ($request) {
                    if ($request->filled('status')) {
                        $query->where('status', $request->string('status')->toString());
                    }
                }, true)
                ->addColumn('status_label', fn ($p) => config('onboarding.statuses.'.$p->status))
                ->addColumn('onboarding_state', fn ($p) => $p->submitted_at ? 'Recibido' : ($p->draft_saved_at ? 'En progreso' : 'Pendiente'))
                ->addColumn('link_state', fn ($p) => ! $p->link_enabled ? 'Desactivado' : ($p->expires_at?->isPast() ? 'Vencido' : 'Activo'))
                ->editColumn('created_at', fn ($p) => $p->created_at->format('d/m/Y'))
                ->toJson();
        }

        return view('admin.projects');
    }

    public function show(Project $project)
    {
        return response()->json($project->toArray() + [
            'url' => $project->invitationUrl(),
            'attachments' => $project->attachments->map(fn ($file) => $file->toArray() + [
                'url' => route('admin.projects.files', [$project, $file->id]),
            ]),
        ])->header('Cache-Control', 'no-store');
    }

    public function store(SaveProjectRequest $request)
    {
        $project = new Project($request->validated());
        $project->expires_at = $project->expires_at?->endOfDay();
        $project->rotateToken();
        $project->save();
        activity()->causedBy($request->user())->performedOn($project)->log('Proyecto creado');

        return response()->json(['message' => 'Proyecto creado. Ya puedes copiar su enlace.', 'id' => $project->id], 201);
    }

    public function update(SaveProjectRequest $request, Project $project)
    {
        DB::transaction(function () use ($request, $project) {
            $project = Project::lockForUpdate()->findOrFail($project->id);
            $project->fill($request->validated());
            $project->expires_at = $project->expires_at?->endOfDay();
            $project->save();
            activity()->causedBy($request->user())->performedOn($project)->log('Proyecto actualizado');
        });

        return response()->json(['message' => 'Proyecto actualizado.']);
    }

    public function rotate(Request $request, Project $project)
    {
        DB::transaction(function () use ($request, $project) {
            $project = Project::lockForUpdate()->findOrFail($project->id);
            $project->rotateToken();
            $project->save();
            activity()->causedBy($request->user())->performedOn($project)->log('Enlace de onboarding renovado');
        });

        return response()->json(['message' => 'Enlace renovado. El anterior ya no funciona.']);
    }

    public function reopen(Request $request, Project $project)
    {
        DB::transaction(function () use ($request, $project) {
            $project = Project::lockForUpdate()->findOrFail($project->id);
            $project->update(['submitted_at' => null]);
            activity()->causedBy($request->user())->performedOn($project)->log('Onboarding reabierto para correcciones');
        });

        return response()->json(['message' => 'El cliente puede corregir y enviar nuevamente.']);
    }

    public function download(Project $project, int $file)
    {
        $attachment = $project->attachments()->findOrFail($file);

        return Storage::disk('local')->download($attachment->path, $attachment->original_name,
            ['Content-Type' => 'application/octet-stream', 'Cache-Control' => 'private, no-store']);
    }
}
