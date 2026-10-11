<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    /**
     * Obtener únicamente los proveedores de la empresa autenticada.
     */
    private function proveedoresEmpresa()
    {
        return Proveedor::where(
            'empresa_id',
            auth()->user()->empresa_id
        );
    }

    /**
     * Seleccionar los datos que se guardarán en la auditoría.
     */
    private function datosAuditoria(Proveedor $proveedor): array
    {
        return $proveedor->only([
            'nombre',
            'nombre_contacto',
            'correo',
            'telefono',
            'direccion',
            'estado',
        ]);
    }

    /**
     * Mostrar los proveedores.
     */
    public function index()
    {
        $proveedores = $this->proveedoresEmpresa()
            ->orderBy('nombre')
            ->get();

        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Mostrar el formulario de creación.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Registrar un proveedor y su auditoría.
     */
    public function store(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'nombre_contacto' => 'nullable|string|max:150',
            'correo' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:255',
            'estado' => 'sometimes|boolean',
        ]);

        $datos['empresa_id'] = $empresaId;
        $datos['estado'] = $request->boolean('estado');

        DB::transaction(function () use ($datos) {
            $proveedor = Proveedor::create($datos);

            app(AuditoriaService::class)->registrar(
                'CREAR',
                $proveedor,
                null,
                $this->datosAuditoria($proveedor),
                'Se registró un proveedor.'
            );
        });

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    /**
     * Mostrar un proveedor.
     */
    public function show(string $id)
    {
        $proveedor = $this->proveedoresEmpresa()->findOrFail($id);

        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(string $id)
    {
        $proveedor = $this->proveedoresEmpresa()->findOrFail($id);

        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualizar un proveedor y registrar los cambios.
     */
    public function update(Request $request, string $id)
    {
        $proveedor = $this->proveedoresEmpresa()->findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'nombre_contacto' => 'nullable|string|max:150',
            'correo' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:255',
            'estado' => 'sometimes|boolean',
        ]);

        $datos['estado'] = $request->boolean('estado');

        DB::transaction(function () use ($proveedor, $datos) {
            $datosAnteriores = $this->datosAuditoria($proveedor);

            $proveedor->update($datos);
            $proveedor->refresh();

            app(AuditoriaService::class)->registrar(
                'ACTUALIZAR',
                $proveedor,
                $datosAnteriores,
                $this->datosAuditoria($proveedor),
                'Se actualizaron los datos de un proveedor.'
            );
        });

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Eliminar un proveedor y registrar sus datos anteriores.
     */
    public function destroy(string $id)
    {
        $proveedor = $this->proveedoresEmpresa()->findOrFail($id);

        DB::transaction(function () use ($proveedor) {
            $datosAnteriores = $this->datosAuditoria($proveedor);

            $proveedor->delete();

            app(AuditoriaService::class)->registrar(
                'ELIMINAR',
                $proveedor,
                $datosAnteriores,
                null,
                'Se eliminó un proveedor.'
            );
        });

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
