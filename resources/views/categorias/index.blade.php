<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - TerraByte</title>
</head>
<body>

    <h1>Categorías</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <a href="{{ route('categorias.create') }}">
        Nueva categoría
    </a>

    <hr>

    @if ($categorias->count())
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($categorias as $categoria)
                    <tr>
                        <td>{{ $categoria->id }}</td>
                        <td>{{ $categoria->nombre }}</td>
                        <td>{{ $categoria->descripcion ?? 'Sin descripción' }}</td>

                        <td>
                            <a href="{{ route('categorias.show', $categoria) }}">
                                Ver
                            </a>

                            <a href="{{ route('categorias.edit', $categoria) }}">
                                Editar
                            </a>

                            <form
                                action="{{ route('categorias.destroy', $categoria) }}"
                                method="POST"
                                style="display:inline;"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay categorías registradas.</p>
    @endif

</body>
</html>