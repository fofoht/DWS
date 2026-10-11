
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel principal | TerraByte</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #222;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 18px 30px;
            background: #2b305b;
            color: white;
        }

        header h1 {
            margin: 0;
        }

        main {
            max-width: 1000px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .bienvenida {
            padding: 25px;
            background: white;
            border-radius: 10px;
        }

        .modulos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .modulo {
            padding: 22px;
            background: white;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .modulo h3 {
            color: #2b305b;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background: #f7c800;
            cursor: pointer;
            font-weight: bold;
        }

        .empresa {
            color: #666;
        }
    </style>
</head>
<body>

    <header>
        <h1>TerraByte</h1>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </header>

    <main>
        <section class="bienvenida">
            <h2>Bienvenido, {{ auth()->user()->name }}</h2>

            <p>
                Este es el panel principal de tu cuenta de TerraByte.
            </p>

            <p class="empresa">
                Empresa:
                {{ auth()->user()->empresa?->nombre_comercial ?? 'Sin empresa asignada' }}
            </p>
        </section>

        <section class="modulos">
            <article class="modulo">
                <h3>Productos</h3>
                <p>Administra el inventario y consulta tus productos.</p>
            </article>

            <article class="modulo">
                <h3>Categorías</h3>
                <p>Organiza los productos de tu empresa.</p>
            </article>

            <article class="modulo">
                <h3>Usuarios y empleados</h3>
                <p>Próximamente podrás gestionar el personal de tu empresa.</p>
            </article>

            <article class="modulo">
                <h3>Roles y permisos</h3>
                <p>Próximamente podrás administrar los accesos de tus empleados.</p>
            </article>
        </section>
    </main>

</body>
</html>