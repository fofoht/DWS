<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo proveedor - TerraByte</title>
</head>
<body>

    <h1>Nuevo proveedor</h1>

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

    <form action="{{ route('proveedores.store') }}" method="POST">
        @csrf

        <div>
            <label for="nombre">Nombre del proveedor:</label>
            <input type="text" id="nombre" name="nombre"
                value="{{ old('nombre') }}" maxlength="150" required>
        </div>

        <br>

        <div>
            <label for="nombre_contacto">Nombre del contacto:</label>
            <input type="text" id="nombre_contacto" name="nombre_contacto"
                value="{{ old('nombre_contacto') }}" maxlength="150">
        </div>

        <br>

        <div>
            <label for="correo">Correo electrónico:</label>
            <input type="email" id="correo" name="correo"
                value="{{ old('correo') }}" maxlength="150">
        </div>

        <br>

        <div>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono"
                value="{{ old('telefono') }}" maxlength="30">
        </div>

        <br>

        <div>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion"
                value="{{ old('direccion') }}" maxlength="255">
        </div>

        <br>

        <div>
            <label>
                <input type="checkbox" name="estado" value="1"
                    {{ old('estado', '1') ? 'checked' : '' }}>
                Proveedor activo
            </label>
        </div>

        <br>

        <button type="submit">Guardar proveedor</button>

        <a href="{{ route('proveedores.index') }}">Cancelar</a>
    </form>

</body>
</html>

