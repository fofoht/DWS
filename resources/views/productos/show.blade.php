
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del producto | TerraByte</title>
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
        .dato {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .dato strong {
            display: inline-block;
            min-width: 160px;
        }
        .link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            border-radius: 5px;
            background: #26345c;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Detalle del producto</h1>

        <div class="dato">
            <strong>Nombre:</strong>
            {{ $producto->nombre }}
        </div>

        <div class="dato">
            <strong>Categoría:</strong>
            {{ $producto->categoria?->nombre ?? 'Sin categoría' }}
        </div>

        <div class="dato">
            <strong>Descripción:</strong>
            {{ $producto->descripcion ?: 'Sin descripción' }}
        </div>

        <div class="dato">
            <strong>Costo:</strong>
            ${{ number_format((float) $producto->costo, 2) }}
        </div>

        <div class="dato">
            <strong>Precio:</strong>
            ${{ number_format((float) $producto->precio, 2) }}
        </div>

        <div class="dato">
            <strong>Ganancia:</strong>
            ${{ number_format((float) $producto->precio - (float) $producto->costo, 2) }}
        </div>

        <div class="dato">
            <strong>Existencias:</strong>
            {{ $producto->stock }}
        </div>

        <div class="dato">
            <strong>Stock mínimo:</strong>
            {{ $producto->stock_minimo }}
        </div>

        <div class="dato">
            <strong>Estado:</strong>
            {{ $producto->estado ? 'Activo' : 'Inactivo' }}
        </div>

        <a class="link" href="{{ route('productos.index') }}">
            Volver a productos
        </a>

        <a class="link" href="{{ route('productos.edit', $producto) }}">
            Editar producto
        </a>
    </div>
</body>
</html>