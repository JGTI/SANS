<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveUserRequest;
use App\Models\User;
use App\Services\UserAdministration;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct(private UserAdministration $administration) {}

    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return DataTables::eloquent(User::query()->select(['id', 'name', 'email', 'created_at'])
                ->with(['roles.permissions', 'permissions']))
                ->addColumn('role_names', fn (User $user) => $user->roles->pluck('name')->join(', '))
                ->addColumn('can_edit', fn (User $user) => $this->administration->canManage($request->user(), $user))
                ->addColumn('can_delete', fn (User $user) => ! $request->user()->is($user)
                    && $this->administration->canManage($request->user(), $user))
                ->editColumn('created_at', fn (User $user) => $user->created_at?->format('d/m/Y H:i'))
                ->removeColumn('roles')->removeColumn('permissions')->toJson();
        }
        $roles = Role::with('permissions')->where('guard_name', 'web')->orderBy('name')->get()
            ->filter(fn (Role $role) => $request->user()->hasRole('super-admin')
                || ! $this->administration->privilegedRole($role) || $request->user()->hasRole($role->name));

        return view('admin.users', compact('roles'));
    }

    public function show(Request $request, User $user)
    {
        abort_unless($this->administration->canManage($request->user(), $user), 403);

        return response()->json(['id' => $user->id, 'name' => $user->name, 'email' => $user->email,
            'roles' => $user->roles->pluck('id'), 'is_self' => $request->user()->is($user)]);
    }

    public function store(SaveUserRequest $request)
    {
        $this->administration->save($request->user(), $request->validated());

        return response()->json(['message' => 'Usuario creado correctamente.'], 201);
    }

    public function update(SaveUserRequest $request, User $user)
    {
        $this->administration->save($request->user(), $request->validated(), $user);

        return response()->json(['message' => 'Usuario actualizado correctamente.']);
    }

    public function destroy(Request $request, User $user)
    {
        $this->administration->delete($request->user(), $user);

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }
}
