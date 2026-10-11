<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    private function comprasEmpresa()
    {
        return Compra::where(
            'empresa_id',
            auth()->user()->empresa_id
        );
    }

    private function proveedoresEmpresa()
    {
        return Proveedor::where(
            'empresa_id',
            auth()->user()->empresa_id
        );
    }

    private function productosEmpresa()
    {
        return Producto::where(
            'empresa_id',
            auth()->user()->empresa_id
        );
    }

    private function datosAuditoria(Compra $compra): array
    {
        return [
            'proveedor_id' => $compra->proveedor_id,
            'fecha_compra' => $compra->fecha_compra?->format('Y-m-d'),
            'estado' => $compra->estado,
            'total' => $compra->total,
            'observaciones' => $compra->observaciones,
            'detalles' => $compra->detalles()
                ->get()
                ->map(fn ($detalle) => [
                    'producto_id' => $detalle->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'costo_unitario' => $detalle->costo_unitario,
                    'subtotal' => $detalle->subtotal,
                ])
                ->all(),
        ];
    }

    private function validarDatos(Request $request): array
    {
        $empresaId = auth()->user()->empresa_id;

        return $request->validate([
            'proveedor_id' => [
                'required',
                Rule::exists('proveedores', 'id')
                    ->where('empresa_id', $empresaId)
                    ->where('estado', true),
            ],

            'fecha_compra' => 'required|date',
            'observaciones' => 'nullable|string|max:2000',

            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => [
                'required',
                'distinct',
                Rule::exists('productos', 'id')
                    ->where('empresa_id', $empresaId)
                    ->where('estado', true),
            ],
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.costo_unitario' => 'required|numeric|min:0|max:99999999.99',
        ]);
    }

    private function guardarDetalles(
        Compra $compra,
        array $productos
    ): void {
        $total = 0;

        foreach ($productos as $item) {
            $cantidad = (int) $item['cantidad'];
            $costo = round((float) $item['costo_unitario'], 2);
            $subtotal = round($cantidad * $costo, 2);

            DetalleCompra::create([
                'compra_id' => $compra->id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $cantidad,
                'costo_unitario' => $costo,
                'subtotal' => $subtotal,
            ]);

            $total += $subtotal;
        }

        $compra->update([
            'total' => round($total, 2),
        ]);
    }

    
        
    public function index(Request $request)
    {
        $compras = $this->comprasEmpresa()
            ->with(['proveedor', 'usuario', 'detalles.producto'])
            ->when($request->filled('proveedor_id'), function ($query) use ($request) {
                $query->where('proveedor_id', $request->input('proveedor_id'));
            })
            ->when($request->filled('producto_id'), function ($query) use ($request) {
                $query->whereHas('detalles', function ($detalleQuery) use ($request) {
                    $detalleQuery->where('producto_id', $request->input('producto_id'));
                });
            })
            ->orderByDesc('fecha_compra')
            ->orderByDesc('id')
            ->get();

        return view('compras.index', compact('compras'));
    }
    




    public function create()
    {
        $proveedores = $this->proveedoresEmpresa()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $productos = $this->productosEmpresa()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'compras.create',
            compact('proveedores', 'productos')
        );
    }

    public function store(Request $request)
    {
        $datos = $this->validarDatos($request);
        $empresaId = auth()->user()->empresa_id;

        DB::transaction(function () use (
            $datos,
            $empresaId
        ) {
            $compra = Compra::create([
                'empresa_id' => $empresaId,
                'proveedor_id' => $datos['proveedor_id'],
                'user_id' => auth()->id(),
                'fecha_compra' => $datos['fecha_compra'],
                'estado' => 'pendiente',
                'total' => 0,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $this->guardarDetalles(
                $compra,
                $datos['productos']
            );

            $compra->refresh();

            app(AuditoriaService::class)->registrar(
                'CREAR',
                $compra,
                null,
                $this->datosAuditoria($compra),
                'Se registró una compra pendiente.'
            );
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'Compra registrada correctamente.');
    }

    public function show(string $id)
    {
        $compra = $this->comprasEmpresa()
            ->with(['proveedor', 'usuario', 'detalles.producto'])
            ->findOrFail($id);

        return view('compras.show', compact('compra'));
    }

    public function edit(string $id)
    {
        $compra = $this->comprasEmpresa()
            ->with('detalles.producto')
            ->findOrFail($id);

        if ($compra->estado !== 'pendiente') {
            return redirect()
                ->route('compras.index')
                ->with('error', 'Solo se pueden editar compras pendientes.');
        }

        $proveedores = $this->proveedoresEmpresa()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $productos = $this->productosEmpresa()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'compras.edit',
            compact('compra', 'proveedores', 'productos')
        );
    }

    public function update(Request $request, string $id)
    {
        $datos = $this->validarDatos($request);

        DB::transaction(function () use ($datos, $id) {
            $compra = $this->comprasEmpresa()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($compra->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden editar compras pendientes.',
                ]);
            }

            $datosAnteriores = $this->datosAuditoria($compra);

            $compra->update([
                'proveedor_id' => $datos['proveedor_id'],
                'fecha_compra' => $datos['fecha_compra'],
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            // Reemplaza los detalles de la compra pendiente.
            $compra->detalles()->delete();

            $this->guardarDetalles(
                $compra,
                $datos['productos']
            );

            $compra->refresh();

            app(AuditoriaService::class)->registrar(
                'ACTUALIZAR',
                $compra,
                $datosAnteriores,
                $this->datosAuditoria($compra),
                'Se actualizó una compra pendiente.'
            );
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'Compra actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $compra = $this->comprasEmpresa()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($compra->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden eliminar compras pendientes.',
                ]);
            }

            $datosAnteriores = $this->datosAuditoria($compra);

            // Los detalles tienen restricción de eliminación.
            $compra->detalles()->delete();
            $compra->delete();

            app(AuditoriaService::class)->registrar(
                'ELIMINAR',
                $compra,
                $datosAnteriores,
                null,
                'Se eliminó una compra pendiente.'
            );
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'Compra eliminada correctamente.');
    }

    
        public function recibir(string $id)
        {
            DB::transaction(function () use ($id) {
                $compra = $this->comprasEmpresa()
                    ->lockForUpdate()
                    ->with('detalles')
                    ->findOrFail($id);

                if ($compra->estado !== 'pendiente') {
                    throw ValidationException::withMessages([
                        'compra' => 'Esta compra ya fue recibida o cancelada.',
                    ]);
                }

                if ($compra->detalles->isEmpty()) {
                    throw ValidationException::withMessages([
                        'compra' => 'No se puede recibir una compra sin productos.',
                    ]);
                }

                $datosAnteriores = $this->datosAuditoria($compra);

                foreach ($compra->detalles as $detalle) {
                    $producto = $this->productosEmpresa()
                        ->lockForUpdate()
                        ->findOrFail($detalle->producto_id);

                    $stockAnterior = (int) $producto->stock;
                    $cantidadRecibida = (int) $detalle->cantidad;

                    $costoAnterior = (float) $producto->costo;
                    $costoCompra = (float) $detalle->costo_unitario;

                    $stockNuevo = $stockAnterior + $cantidadRecibida;

                    if ($stockNuevo <= 0) {
                        throw ValidationException::withMessages([
                            'compra' => 'No se pudo calcular el nuevo inventario.',
                        ]);
                    }

                    // Calcula el costo promedio ponderado.
                    $costoPromedio = (
                        ($stockAnterior * $costoAnterior)
                        + ($cantidadRecibida * $costoCompra)
                    ) / $stockNuevo;

                    $producto->update([
                        'stock' => $stockNuevo,
                        'costo' => round($costoPromedio, 2),
                    ]);
                }

                $compra->update([
                    'estado' => 'recibida',
                ]);

                $compra->refresh();

                app(AuditoriaService::class)->registrar(
                    'RECIBIR',
                    $compra,
                    $datosAnteriores,
                    $this->datosAuditoria($compra),
                    'Se recibió la compra y se actualizó el inventario y el costo promedio ponderado.'
                );
            });

            return redirect()
                ->route('compras.show', $id)
                ->with(
                    'success',
                    'Compra recibida correctamente. Se actualizaron las existencias y los costos promedio.'
                );
        }



    public function cancelar(string $id)
    {
        DB::transaction(function () use ($id) {
            $compra = $this->comprasEmpresa()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($compra->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden cancelar compras pendientes.',
                ]);
            }

            $datosAnteriores = $this->datosAuditoria($compra);

            $compra->update([
                'estado' => 'cancelada',
            ]);

            $compra->refresh();

            app(AuditoriaService::class)->registrar(
                'CANCELAR',
                $compra,
                $datosAnteriores,
                $this->datosAuditoria($compra),
                'Se canceló una compra pendiente.'
            );
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'Compra cancelada correctamente.');
    }
}

