<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveRoleRequest;
use App\Services\UserAdministration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return DataTables::eloquent(Role::query()->where('guard_name', 'web')->with('permissions')->withCount('users'))
                ->addColumn('permission_names', fn (Role $role) => $role->name === 'super-admin'
                    ? 'Usuarios y roles' : ($role->hasPermissionTo('users.manage') ? 'Administrar usuarios' : 'Acceso al panel'))
                ->addColumn('protected', fn (Role $role) => $this->isProtected($role))
                ->removeColumn('permissions')->toJson();
        }

        return view('admin.roles');
    }

    public function show(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        return response()->json(['id' => $role->id, 'name' => $role->name, 'permissions' => $role->permissions->pluck('name')]);
    }

    public function store(SaveRoleRequest $request)
    {
        return $this->save($request);
    }

    public function update(SaveRoleRequest $request, Role $role)
    {
        return $this->save($request, $role);
    }

    private function save(SaveRoleRequest $request, ?Role $role = null)
    {
        DB::transaction(function () use ($request, $role) {
            app(UserAdministration::class)->lock();
            abort_unless($request->user()->fresh()->hasRole('super-admin'), 403);
            if ($role) {
                $role = $role->fresh();
                abort_unless($role && $role->guard_name === 'web', 404);
                $this->ensureEditable($role);
            }
            $data = $request->validated();
            $created = ! $role;
            $role ??= new Role(['guard_name' => 'web']);
            $role->name = $data['name'];
            $role->save();
            $role->syncPermissions($data['permissions']);
            activity('administration')->causedBy($request->user())->performedOn($role)
                ->withProperties($data)->log($created ? 'Rol creado' : 'Rol actualizado');
        });

        return response()->json(['message' => 'Rol guardado correctamente.'], $request->isMethod('POST') ? 201 : 200);
    }

    public function destroy(Request $request, Role $role)
    {
        DB::transaction(function () use ($request, $role) {
            app(UserAdministration::class)->lock();
            abort_unless($request->user()->fresh()->hasRole('super-admin'), 403);
            $role = $role->fresh();
            abort_unless($role && $role->guard_name === 'web', 404);
            $this->ensureEditable($role);
            if ($role->users()->exists()) {
                throw ValidationException::withMessages(['role' => 'Reasigna primero los usuarios que tienen este rol.']);
            }
            activity('administration')->causedBy($request->user())->performedOn($role)
                ->withProperties(['name' => $role->name])->log('Rol eliminado');
            $role->delete();
        });

        return response()->json(['message' => 'Rol eliminado correctamente.']);
    }

    private function isProtected(Role $role): bool
    {
        return in_array($role->name, ['super-admin', 'admin', 'user'], true);
    }

    private function ensureEditable(Role $role): void
    {
        if ($this->isProtected($role)) {
            throw ValidationException::withMessages(['role' => 'Los roles base del sistema están protegidos.']);
        }
    }
}
