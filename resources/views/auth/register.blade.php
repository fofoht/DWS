<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | TerraByte</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 16px;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #222;
        }

        .contenedor {
            max-width: 650px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-bottom: 8px;
            text-align: center;
            color: #2b305b;
        }

        .descripcion {
            margin-bottom: 25px;
            text-align: center;
            color: #666;
        }

        h2 {
            margin-top: 25px;
            font-size: 18px;
            color: #2b305b;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            margin-top: 6px;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: none;
            border-radius: 6px;
            background: #f7c800;
            color: #222;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #e5b900;
        }

        .error {
            margin-top: 5px;
            color: #c62828;
            font-size: 14px;
        }

        .acceso {
            margin-top: 20px;
            text-align: center;
        }

        a {
            color: #2b305b;
        }

        @media (max-width: 480px) {
            .contenedor {
                padding: 22px;
            }
        }
    </style>
</head>
<body>

    <main class="contenedor">
        <h1>TerraByte</h1>

        <p class="descripcion">
            Registra tu negocio y comienza a administrar tu empresa.
        </p>

        @if ($errors->any())
            <div class="error" role="alert">
                <p>Revisa los siguientes errores:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <h2>Datos del administrador</h2>

            <label for="name">Nombre completo *</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                maxlength="255"
                autocomplete="name"
                required
            >

            <label for="email">Correo electrónico de acceso *</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                maxlength="255"
                autocomplete="email"
                required
            >

            <label for="password">Contraseña *</label>
            <input
                id="password"
                type="password"
                name="password"
                autocomplete="new-password"
                required
            >

            <label for="password_confirmation">Confirmar contraseña *</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                required
            >

            <h2>Datos de la empresa</h2>

            <label for="nombre_comercial">Nombre comercial *</label>
            <input
                id="nombre_comercial"
                type="text"
                name="nombre_comercial"
                value="{{ old('nombre_comercial') }}"
                maxlength="150"
                required
            >

            <label for="razon_social">Razón social</label>
            <input
                id="razon_social"
                type="text"
                name="razon_social"
                value="{{ old('razon_social') }}"
                maxlength="200"
            >

            <label for="correo_empresa">Correo de la empresa</label>
            <input
                id="correo_empresa"
                type="email"
                name="correo_empresa"
                value="{{ old('correo_empresa') }}"
                maxlength="150"
            >

            <label for="telefono">Teléfono</label>
            <input
                id="telefono"
                type="text"
                name="telefono"
                value="{{ old('telefono') }}"
                maxlength="30"
            >

            <label for="direccion">Dirección</label>
            <input
                id="direccion"
                type="text"
                name="direccion"
                value="{{ old('direccion') }}"
                maxlength="255"
            >

            <button type="submit">Registrar mi empresa</button>
        </form>

        <p class="acceso">
            ¿Ya tienes una cuenta?
            <a href="{{ route('login') }}">Inicia sesión</a>
        </p>
    </main>

</body>
</html>