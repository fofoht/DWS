<?php

namespace App\Actions\Fortify;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Valida los datos y registra una empresa con su administrador.
     *
     * @param array<string, string> $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'nombre_comercial' => [
                'required',
                'string',
                'max:150',
            ],
            'razon_social' => [
                'nullable',
                'string',
                'max:200',
            ],
            'correo_empresa' => [
                'nullable',
                'email',
                'max:150',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:30',
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:255',
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input): User {
            // Buscar el rol de administrador o crearlo si no existe.
            $rol = DB::table('roles')
                ->where('nombre', 'Administrador')
                ->first();

            if (!$rol) {
                $roleId = DB::table('roles')->insertGetId([
                    'nombre' => 'Administrador',
                    'descripcion' => 'Administrador de la empresa',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $roleId = $rol->id;
            }

            // Crear la empresa.
            $empresa = Empresa::create([
                'nombre_comercial' => $input['nombre_comercial'],
                'razon_social' => $input['razon_social'] ?? null,
                'correo' => $input['correo_empresa'] ?? null,
                'telefono' => $input['telefono'] ?? null,
                'direccion' => $input['direccion'] ?? null,
                'estado' => true,
            ]);

            // Crear al usuario administrador de esa empresa.
            return User::create([
                'empresa_id' => $empresa->id,
                'role_id' => $roleId,
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'estado' => true,
            ]);
        });
    }
}