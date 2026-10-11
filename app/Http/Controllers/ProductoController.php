<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Obtener los productos de la empresa autenticada.
     */
    private function productosEmpresa()
    {
        return Producto::where(
            'empresa_id',
            auth()->user()->empresa_id
        );
    }

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
    private function datosAuditoria(Producto $producto): array
    {
        return $producto->only([
            'categoria_id',
            'nombre',
            'descripcion',
            'costo',
            'precio',
            'stock',
            'stock_minimo',
            'estado',
        ]);
    }

    /**
     * Mostrar los productos.
     */
    public function index()
    {
        $productos = $this->productosEmpresa()
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        return view('productos.index', compact('productos'));
    }

    /**
     * Mostrar el formulario de creación.
     */
    public function create()
    {
        $categorias = $this->categoriasEmpresa()
            ->orderBy('nombre')
            ->get();

        return view('productos.create', compact('categorias'));
    }

    /**
     * Registrar un producto y su auditoría.
     */
    public function store(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        $datos = $request->validate([
            'categoria_id' => [
                'required',
                Rule::exists('categorias', 'id')
                    ->where('empresa_id', $empresaId),
            ],
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'costo' => 'required|numeric|min:0',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        $datos['empresa_id'] = $empresaId;
        $datos['estado'] = $request->boolean('estado');

        DB::transaction(function () use ($datos) {
            $producto = Producto::create($datos);

            app(AuditoriaService::class)->registrar(
                'CREAR',
                $producto,
                null,
                $this->datosAuditoria($producto),
                'Se registró un producto.'
            );
        });

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    /**
     * Mostrar un producto.
     */
    public function show(string $id)
    {
        $producto = $this->productosEmpresa()
            ->with('categoria')
            ->findOrFail($id);

        return view('productos.show', compact('producto'));
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(string $id)
    {
        $producto = $this->productosEmpresa()->findOrFail($id);

        $categorias = $this->categoriasEmpresa()
            ->orderBy('nombre')
            ->get();

        return view(
            'productos.edit',
            compact('producto', 'categorias')
        );
    }

    /**
     * Actualizar un producto y registrar los cambios.
     */
    public function update(Request $request, string $id)
    {
        $empresaId = auth()->user()->empresa_id;

        $producto = $this->productosEmpresa()->findOrFail($id);

        $datos = $request->validate([
            'categoria_id' => [
                'required',
                Rule::exists('categorias', 'id')
                    ->where('empresa_id', $empresaId),
            ],
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'costo' => 'required|numeric|min:0',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        $datos['estado'] = $request->boolean('estado');

        DB::transaction(function () use ($producto, $datos) {
            $datosAnteriores = $this->datosAuditoria($producto);

            $producto->update($datos);

            $producto->refresh();

            app(AuditoriaService::class)->registrar(
                'ACTUALIZAR',
                $producto,
                $datosAnteriores,
                $this->datosAuditoria($producto),
                'Se actualizaron los datos de un producto.'
            );
        });

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar un producto y registrar sus datos anteriores.
     */
    public function destroy(string $id)
    {
        $producto = $this->productosEmpresa()->findOrFail($id);

        DB::transaction(function () use ($producto) {
            $datosAnteriores = $this->datosAuditoria($producto);

            $producto->delete();

            app(AuditoriaService::class)->registrar(
                'ELIMINAR',
                $producto,
                $datosAnteriores,
                null,
                'Se eliminó un producto.'
            );
        });

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}