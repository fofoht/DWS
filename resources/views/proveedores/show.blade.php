<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del proveedor - TerraByte</title>
</head>
<body>

    <h1>Detalle del proveedor</h1>

    <p><strong>ID:</strong> {{ $proveedor->id }}</p>
    <p><strong>Nombre:</strong> {{ $proveedor->nombre }}</p>
    <p><strong>Contacto:</strong> {{ $proveedor->nombre_contacto ?? 'Sin contacto' }}</p>
    <p><strong>Correo:</strong> {{ $proveedor->correo ?? 'Sin correo' }}</p>
    <p><strong>Teléfono:</strong> {{ $proveedor->telefono ?? 'Sin teléfono' }}</p>
    <p><strong>Dirección:</strong> {{ $proveedor->direccion ?? 'Sin dirección' }}</p>
    <p><strong>Estado:</strong> {{ $proveedor->estado ? 'Activo' : 'Inactivo' }}</p>
    <p><strong>Fecha de registro:</strong> {{ $proveedor->created_at->format('d/m/Y H:i') }}</p>

    <a href="{{ route('proveedores.edit', $proveedor) }}">Editar</a>

    <a href="{{ route('proveedores.index') }}">Volver a proveedores</a>

</body>
</html>

