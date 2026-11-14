<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UserAdministration;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'nexus:create-admin {email} {--name=Administrador}';

    protected $description = 'Crea un superadministrador solicitando la contraseña de forma oculta';

    public function handle(): int
    {
        $email = mb_strtolower(trim($this->argument('email')));
        if (User::where('email', $email)->exists()) {
            $this->error('El correo ya existe; no se modificó la cuenta.');

            return self::FAILURE;
        }
        $data = ['name' => $this->option('name'), 'email' => $email, 'password' => $this->secret('Contraseña')];
        $validator = Validator::make($data, ['name' => 'required|string|max:255', 'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:72']);
        if ($validator->fails()) {
            $this->error('Datos inválidos: revisa nombre, correo y contraseña (8 a 72 caracteres).');

            return self::FAILURE;
        }
        DB::transaction(function () use ($data) {
            (new RolesAndPermissionsSeeder)->run();
            app(UserAdministration::class)->lock();
            $user = User::create($data);
            $user->assignRole('super-admin');
            activity('administration')->performedOn($user)->withProperties(['email' => $user->email])
                ->log('Superadministrador creado desde consola');
        });
        $this->info('Superadministrador creado correctamente: '.$email);

        return self::SUCCESS;
    }
}
