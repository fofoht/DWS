<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva categoría - TerraByte</title>
</head>
<body>

    <h1>Nueva categoría</h1>

    @if ($errors->any())
        <div>
            <strong>Hay errores:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categorias.store') }}" method="POST">

        @csrf

        <div>
            <label for="nombre">Nombre:</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                value="{{ old('nombre') }}"
                required
            >
        </div>

        <br>

        <div>
            <label for="descripcion">Descripción:</label>
            <textarea
                id="descripcion"
                name="descripcion"
            >{{ old('descripcion') }}</textarea>
        </div>

        <br>

        <button type="submit">
            Guardar
        </button>

        <a href="{{ route('categorias.index') }}">
            Cancelar
        </a>

    </form>

</body>
</html>