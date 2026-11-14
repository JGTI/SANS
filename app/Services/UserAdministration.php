<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserAdministration
{
    public function privilegedRole(Role $role): bool
    {
        return in_array($role->name, ['super-admin', 'admin'], true)
            || $role->permissions->isNotEmpty();
    }

    public function canManage(User $actor, User $target): bool
    {
        return $actor->hasRole('super-admin') || $actor->is($target)
            || (! $target->hasAnyRole(['super-admin', 'admin']) && $target->getAllPermissions()->isEmpty());
    }

    // All administrative writes share this lock, including changes to roles.
    public function lock(): void
    {
        Role::where('name', 'super-admin')->where('guard_name', 'web')->lockForUpdate()->firstOrFail();
    }

    public function save(User $actor, array $data, ?User $target = null): User
    {
        return DB::transaction(function () use ($actor, $data, $target) {
            $this->lock();
            $actor = $actor->fresh();
            abort_unless($actor->can('users.manage'), 403);
            $target = $target ? User::lockForUpdate()->findOrFail($target->id) : new User;
            abort_unless(! $target->exists || $this->canManage($actor, $target), 403);
            $roles = Role::with('permissions')->where('guard_name', 'web')->whereIn('id', $data['roles'])->get();
            if ($roles->count() !== count($data['roles'])) {
                throw ValidationException::withMessages(['roles' => 'Un rol ya no existe. Actualiza la página.']);
            }
            $oldRoles = $target->exists ? $target->roles->pluck('id')->sort()->values()->all() : [];
            $newRoles = $roles->pluck('id')->sort()->values()->all();
            if ($actor->is($target) && $oldRoles !== $newRoles) {
                throw ValidationException::withMessages(['roles' => 'No puedes cambiar tus propios roles.']);
            }
            if (! $actor->hasRole('super-admin') && ! ($actor->is($target) && $oldRoles === $newRoles)) {
                abort_if($roles->contains(fn (Role $role) => $this->privilegedRole($role)), 403,
                    'Solo un superadministrador puede asignar roles administrativos.');
            }
            if ($target->exists && $target->hasRole('super-admin') && ! $roles->contains('name', 'super-admin')) {
                $this->ensureAnotherSuperAdmin($target);
            }
            $created = ! $target->exists;
            $target->fill(collect($data)->only(['name', 'email'])->all());
            if (! empty($data['password'])) {
                $target->password = $data['password'];
                $target->remember_token = Str::random(60);
            }
            if ($target->isDirty('email')) {
                $target->email_verified_at = null;
            }
            $target->save();
            $target->syncRoles($roles);
            if (! $created && ! empty($data['password'])) {
                $this->revokeSessions($target);
            }
            activity('administration')->causedBy($actor)->performedOn($target)
                ->withProperties(['email' => $target->email, 'roles' => $roles->pluck('name'),
                    'previous_role_ids' => $oldRoles, 'password_changed' => ! empty($data['password'])])
                ->log($created ? 'Usuario creado' : 'Usuario actualizado');

            return $target;
        });
    }

    public function delete(User $actor, User $target): void
    {
        DB::transaction(function () use ($actor, $target) {
            $this->lock();
            $actor = $actor->fresh();
            abort_unless($actor->can('users.manage'), 403);
            $target = User::lockForUpdate()->findOrFail($target->id);
            abort_unless($this->canManage($actor, $target), 403);
            if ($actor->is($target)) {
                throw ValidationException::withMessages(['user' => 'No puedes eliminar tu propia cuenta.']);
            }
            if ($target->hasRole('super-admin')) {
                $this->ensureAnotherSuperAdmin($target);
            }
            activity('administration')->causedBy($actor)->performedOn($target)
                ->withProperties(['email' => $target->email])->log('Usuario eliminado');
            $this->revokeSessions($target);
            $target->delete();
        });
    }

    private function ensureAnotherSuperAdmin(User $target): void
    {
        if (! User::role('super-admin')->whereKeyNot($target->id)->exists()) {
            throw ValidationException::withMessages(['roles' => 'Debe existir al menos un superadministrador.']);
        }
    }

    private function revokeSessions(User $target): void
    {
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', $target->id)->delete();
        }
        // Revoke personal access tokens if the model exposes Sanctum's relation.
        if (method_exists($target, 'tokens')) {
            $target->tokens()->delete();
        }
    }
}
