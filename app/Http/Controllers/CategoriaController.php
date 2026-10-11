<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    /**
     * Obtener las categorías de la empresa autenticada.
     */
    private function categoriasEmpresa()
    {
        return Categoria::where(
            'empresa_id',
            auth()->user()->empresa_id
        );
    }

    /**
     * Seleccionar los datos relevantes para la auditoría.
     */
    private function datosAuditoria(Categoria $categoria): array
    {
        return $categoria->only([
            'nombre',
            'descripcion',
        ]);
    }

    /**
     * Mostrar las categorías.
     */
    public function index()
    {
        $categorias = $this->categoriasEmpresa()
            ->orderBy('nombre')
            ->get();

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Mostrar el formulario de creación.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Guardar una categoría y registrar la auditoría.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $datos['empresa_id'] = auth()->user()->empresa_id;

        DB::transaction(function () use ($datos) {
            $categoria = Categoria::create($datos);

            app(AuditoriaService::class)->registrar(
                'CREAR',
                $categoria,
                null,
                $this->datosAuditoria($categoria),
                'Se creó una categoría.'
            );
        });

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Mostrar una categoría.
     */
    public function show(string $id)
    {
        $categoria = $this->categoriasEmpresa()->findOrFail($id);

        return view('categorias.show', compact('categoria'));
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(string $id)
    {
        $categoria = $this->categoriasEmpresa()->findOrFail($id);

        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar una categoría y registrar los cambios.
     */
    public function update(Request $request, string $id)
    {
        $categoria = $this->categoriasEmpresa()->findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($categoria, $datos) {
            $datosAnteriores = $this->datosAuditoria($categoria);

            $categoria->update($datos);
            $categoria->refresh();

            app(AuditoriaService::class)->registrar(
                'ACTUALIZAR',
                $categoria,
                $datosAnteriores,
                $this->datosAuditoria($categoria),
                'Se actualizaron los datos de una categoría.'
            );
        });

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Eliminar una categoría y registrar sus datos anteriores.
     */
    public function destroy(string $id)
    {
        $categoria = $this->categoriasEmpresa()->findOrFail($id);

        DB::transaction(function () use ($categoria) {
            $datosAnteriores = $this->datosAuditoria($categoria);

            $categoria->delete();

            app(AuditoriaService::class)->registrar(
                'ELIMINAR',
                $categoria,
                $datosAnteriores,
                null,
                'Se eliminó una categoría.'
            );
        });

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}