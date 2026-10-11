<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | TerraByte</title>
    <style>
        body {
            margin: 0;
            padding: 30px 16px;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #222;
        }

        .contenedor {
            max-width: 420px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .08);
        }

        h1, p {
            text-align: center;
        }

        h1 {
            color: #2b305b;
        }

        label {
            display: block;
            margin-top: 18px;
            font-weight: bold;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            margin-top: 6px;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #f7c800;
            font-weight: bold;
            cursor: pointer;
        }

        .error {
            color: #c62828;
        }

        a {
            color: #2b305b;
        }
    </style>
</head>
<body>
    <main class="contenedor">
        <h1>TerraByte</h1>
        <p>Ingresa a tu cuenta para administrar tu empresa.</p>

        @if ($errors->any())
            <div class="error" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Correo electrónico</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="username"
                required
                autofocus
            >

            <label for="password">Contraseña</label>
            <input
                id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Iniciar sesión</button>
        </form>

        <p>¿Todavía no tienes una cuenta?</p>
        <p><a href="{{ route('register') }}">Registrar mi empresa</a></p>
    </main>
</body>
</html>