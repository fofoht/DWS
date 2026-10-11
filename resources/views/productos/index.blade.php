
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - TerraByte</title>
</head>
<body>

    <h1>Productos</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p>{{ session('error') }}</p>
    @endif

    <a href="{{ route('productos.create') }}">Nuevo producto</a>

    <hr>

    @if ($productos->count())
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Costo</th>
                    <th>Precio</th>
                    <th>Ganancia</th>
                    <th>Stock</th>
                    <th>Stock mínimo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($productos as $producto)
                    <tr>  
                        <td>{{ $producto->id }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</td>
                        <td>${{ number_format((float) $producto->costo, 2) }}</td>
                        <td>${{ number_format((float) $producto->precio, 2) }}</td>
                        <td>${{ number_format((float) $producto->precio - (float) $producto->costo, 2) }}</td>
                        <td>{{ $producto->stock }}</td>
                        <td>{{ $producto->stock_minimo }}</td>
                        <td>{{ $producto->estado ? 'Activo' : 'Inactivo' }}</td>

                        
                        <td>
                            <a href="{{ route('productos.show', $producto) }}">
                                Ver
                            </a>

                            |

                            <a href="{{ route('productos.edit', $producto) }}">
                                Editar
                            </a>

                            |

                            <a href="{{ route('compras.index', ['producto_id' => $producto->id]) }}">
                                Historial de compras
                            </a>

                            |

                            <form
                                action="{{ route('productos.destroy', $producto) }}"
                                method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('¿Eliminar este producto?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit">Eliminar</button>
                            </form>
                        </td>


                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay productos registrados.</p>
    @endif

    <p><a href="{{ route('categorias.index') }}">Ir a categorías</a></p>

</body>
</html>