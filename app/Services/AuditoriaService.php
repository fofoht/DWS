<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class AuditoriaService
{
    public function registrar(
        string $accion,
        Model $registro,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
        ?string $descripcion = null
    ): void {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->empresa_id) {
            throw new RuntimeException(
                'No se puede registrar la auditoría sin un usuario y una empresa.'
            );
        }

        Auditoria::create([
            'empresa_id' => $usuario->empresa_id,
            'user_id' => $usuario->id,
            'accion' => $accion,
            'modelo' => $registro::class,
            'registro_id' => $registro->getKey(),
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'descripcion' => $descripcion,
        ]);
    }
}