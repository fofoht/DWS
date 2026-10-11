
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto | TerraByte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 750px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f7fb;
            color: #222;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #00000010;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .actions { margin-top: 22px; }
        button, .link {
            display: inline-block;
            padding: 10px 15px;
            border: 0;
            border-radius: 5px;
            background: #26345c;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
        .link { background: #666; }
        .error { color: #b91c1c; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Editar producto</h1>
        <p>Modifica la información de {{ $producto->nombre }}.</p>

        @if ($errors->any())
            <div class="error">
                <p>Revisa los siguientes errores:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('productos.update', $producto) }}">
            @csrf
            @method('PUT')

            <label for="nombre">Nombre *</label>
            <input id="nombre" type="text" name="nombre"
                value="{{ old('nombre', $producto->nombre) }}"
                maxlength="100" required>

            <label for="categoria_id">Categoría *</label>
            <select id="categoria_id" name="categoria_id" required>
                <option value="">Selecciona una categoría</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}"
                        @selected(old('categoria_id', $producto->categoria_id) == $categoria->id)>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion"
                rows="3" maxlength="255">{{ old('descripcion', $producto->descripcion) }}</textarea>

            <label for="costo">Costo *</label>
            <input id="costo" type="number" name="costo"
                value="{{ old('costo', $producto->costo) }}"
                min="0" step="0.01" required>

            <label for="precio">Precio *</label>
            <input id="precio" type="number" name="precio"
                value="{{ old('precio', $producto->precio) }}"
                min="0" step="0.01" required>

            <label for="stock">Existencias *</label>
            <input id="stock" type="number" name="stock"
                value="{{ old('stock', $producto->stock) }}"
                min="0" step="1" required>

            <label for="stock_minimo">Stock mínimo *</label>
            <input id="stock_minimo" type="number" name="stock_minimo"
                value="{{ old('stock_minimo', $producto->stock_minimo) }}"
                min="0" step="1" required>

            <label>
                <input type="checkbox" name="estado" value="1"
                    style="width:auto"
                    @checked(old('estado', $producto->estado) == '1')>
                Producto activo
            </label>

            <div class="actions">
                <button type="submit">Guardar cambios</button>
                <a class="link" href="{{ route('productos.index') }}">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>