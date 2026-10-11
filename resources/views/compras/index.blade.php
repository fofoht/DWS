<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras | TerraByte</title>
</head>
<body>
    <h1>Gestión de compras</h1>

    <p>
        <a href="{{ route('dashboard') }}">Volver al panel</a> |
        <a href="{{ route('proveedores.index') }}">Proveedores</a> |
        <a href="{{ route('productos.index') }}">Productos</a>
    </p>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <strong>Se encontraron errores:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p>
        <a href="{{ route('compras.create') }}">+ Registrar nueva compra</a>
    </p>

    @if ($compras->isEmpty())
        <p>Aún no hay compras registradas.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>N.º</th>
                    <th>Proveedor</th>
                    <th>productos</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($compras as $compra)
                    <tr>
                        <td>{{ $compra->id }}</td>
                        <td>{{ $compra->proveedor->nombre }}</td>                       
                        <td>
                            @forelse ($compra->detalles as $detalle)
                                {{ $detalle->producto->nombre }}
                                (Cantidad: {{ $detalle->cantidad }})
                                @if (!$loop->last)
                                    <br>
                                @endif
                            @empty
                                Sin productos registrados
                            @endforelse
                        </td>
                        <td>{{ $compra->fecha_compra->format('d/m/Y') }}</td>
                        <td>${{ number_format((float) $compra->total, 2) }}</td>
                        <td>{{ ucfirst($compra->estado) }}</td>
                        <td>
                            <a href="{{ route('compras.show', $compra->id) }}">
                                Ver
                            </a>

                            @if ($compra->estado === 'pendiente')
                                |
                                <a href="{{ route('compras.edit', $compra->id) }}">
                                    Editar
                                </a>

                                |
                                <form action="{{ route('compras.destroy', $compra->id) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar esta compra pendiente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>

