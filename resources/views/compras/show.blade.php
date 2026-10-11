<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de compra | TerraByte</title>
</head>
<body>
    <h1>Compra #{{ $compra->id }}</h1>

    <p>
        <a href="{{ route('compras.index') }}">Volver a compras</a>
    </p>

    @if (session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p style="color:red;">{{ session('error') }}</p>
    @endif

    <h3>Información general</h3>

    <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre }}</p>
    <p><strong>Fecha:</strong> {{ $compra->fecha_compra->format('d/m/Y') }}</p>
    <p><strong>Registrada por:</strong> {{ $compra->usuario->name }}</p>
    <p><strong>Estado:</strong> {{ ucfirst($compra->estado) }}</p>
    <p><strong>Observaciones:</strong> {{ $compra->observaciones ?: 'Sin observaciones' }}</p>

    <h3>Productos comprados</h3>

    @if ($compra->detalles->isEmpty())
        <p>Esta compra no tiene productos registrados.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Costo unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($compra->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format((float) $detalle->costo_unitario, 2) }}</td>
                        <td>${{ number_format((float) $detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th>${{ number_format((float) $compra->total, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($compra->estado === 'pendiente')
        <h3>Acciones</h3>

        <p>
            <a href="{{ route('compras.edit', $compra->id) }}">Editar compra</a>
        </p>

        <form action="{{ route('compras.recibir', $compra->id) }}"
              method="POST"
              onsubmit="return confirm('¿Confirmas la recepción? Se actualizarán las existencias y los costos de los productos.');">
            @csrf
            <button type="submit">Recibir compra</button>
        </form>

        <br>

        <form action="{{ route('compras.cancelar', $compra->id) }}"
              method="POST"
              onsubmit="return confirm('¿Seguro que deseas cancelar esta compra?');">
            @csrf
            <button type="submit">Cancelar compra</button>
        </form>
    @elseif ($compra->estado === 'recibida')
        <p>Esta compra ya fue recibida y su inventario fue actualizado.</p>
    @elseif ($compra->estado === 'cancelada')
        <p>Esta compra fue cancelada y no modificó el inventario.</p>
    @endif
</body>
</html>

