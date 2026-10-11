<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores - TerraByte</title>
</head>
<body>

    <h1>Proveedores</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p>{{ session('error') }}</p>
    @endif

    <a href="{{ route('proveedores.create') }}">
        Nuevo proveedor
    </a>

    <hr>

    @if ($proveedores->count())
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($proveedores as $proveedor)
                    <tr>
                        <td>{{ $proveedor->id }}</td>
                        <td>{{ $proveedor->nombre }}</td>
                        <td>{{ $proveedor->nombre_contacto ?? 'Sin contacto' }}</td>
                        <td>{{ $proveedor->correo ?? 'Sin correo' }}</td>
                        <td>{{ $proveedor->telefono ?? 'Sin teléfono' }}</td>
                        <td>{{ $proveedor->estado ? 'Activo' : 'Inactivo' }}</td>

                        
                <td>
                    <a href="{{ route('proveedores.show', $proveedor) }}">
                        Ver
                    </a>

                    |

                    <a href="{{ route('proveedores.edit', $proveedor) }}">
                        Editar
                    </a>

                    |

                    <a href="{{ route('compras.index', ['proveedor_id' => $proveedor->id]) }}">
                        Compras
                    </a>

                    |

                    <form
                        action="{{ route('proveedores.destroy', $proveedor) }}"
                        method="POST"
                        style="display:inline;"
                        onsubmit="return confirm('¿Seguro que deseas eliminar este proveedor?')"
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
        <p>No hay proveedores registrados.</p>
    @endif

    <p><a href="{{ route('dashboard') }}">Volver al panel principal</a></p>

</body>
</html>

