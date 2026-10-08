<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver categoría - TerraByte</title>
</head>
<body>

    <h1>Detalle de categoría</h1>

    <p>
        <strong>ID:</strong>
        {{ $categoria->id }}
    </p>

    <p>
        <strong>Nombre:</strong>
        {{ $categoria->nombre }}
    </p>

    <p>
        <strong>Descripción:</strong>
        {{ $categoria->descripcion ?? 'Sin descripción' }}
    </p>

    <a href="{{ route('categorias.edit', $categoria) }}">
        Editar
    </a>

    <a href="{{ route('categorias.index') }}">
        Volver
    </a>

</body>
</html>