<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva compra | TerraByte</title>
</head>
<body>
    <h1>Registrar compra</h1>

    <p><a href="{{ route('compras.index') }}">Volver a compras</a></p>

    @if ($errors->any())
        <div style="color:red;">
            <strong>Revisa los siguientes errores:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($proveedores->isEmpty())
        <p>No tienes proveedores activos. Registra un proveedor antes de continuar.</p>
    @endif

    @if ($productos->isEmpty())
        <p>No tienes productos activos. Registra un producto antes de continuar.</p>
    @endif

    @if ($proveedores->isNotEmpty() && $productos->isNotEmpty())
        <form action="{{ route('compras.store') }}" method="POST">
            @csrf

            <p>
                <label for="proveedor_id">Proveedor:</label><br>
                <select name="proveedor_id" id="proveedor_id" required>
                    <option value="">Selecciona un proveedor</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}"
                            @selected(old('proveedor_id') == $proveedor->id)>
                            {{ $proveedor->nombre }}
                        </option>
                    @endforeach
                </select>
            </p>

            <p>
                <label for="fecha_compra">Fecha de compra:</label><br>
                <input type="date" name="fecha_compra" id="fecha_compra"
                       value="{{ old('fecha_compra', now()->format('Y-m-d')) }}"
                       required>
            </p>

            <h3>Productos de la compra</h3>

            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Costo unitario ($)</th>
                        <th>Subtotal ($)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="productos-container">
                    @php
                        $lineas = old('productos', [
                            ['producto_id' => '', 'cantidad' => 1, 'costo_unitario' => '0.00']
                        ]);
                    @endphp

                    @foreach ($lineas as $i => $linea)
                        <tr class="producto-row">
                            <td>
                                <select name="productos[{{ $i }}][producto_id]"
                                        class="producto-select" required>
                                    <option value="">Selecciona</option>
                                    @foreach ($productos as $producto)
                                        <option value="{{ $producto->id }}"
                                                data-costo="{{ $producto->costo }}"
                                            @selected(($linea['producto_id'] ?? '') == $producto->id)>
                                            {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number"
                                       name="productos[{{ $i }}][cantidad]"
                                       class="cantidad"
                                       min="1" step="1"
                                       value="{{ $linea['cantidad'] ?? 1 }}" required>
                            </td>
                            <td>
                                <input type="number"
                                       name="productos[{{ $i }}][costo_unitario]"
                                       class="costo"
                                       min="0" max="9999999999.99" step="0.01"
                                       value="{{ $linea['costo_unitario'] ?? '0.00' }}"
                                       required>
                            </td>
                            <td class="subtotal">$0.00</td>
                            <td>
                                <button type="button" class="quitar">Quitar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>
                <button type="button" id="agregar-producto">+ Agregar producto</button>
            </p>

            <h3>Total estimado: <span id="total">$0.00</span></h3>

            <p>
                <label for="observaciones">Observaciones:</label><br>
                <textarea name="observaciones" id="observaciones"
                          rows="3" cols="50"
                          maxlength="5000">{{ old('observaciones') }}</textarea>
            </p>

            <p>La compra se guardará como pendiente. El inventario no cambiará hasta recibirla.</p>

            <button type="submit">Guardar compra</button>
            <a href="{{ route('compras.index') }}">Cancelar</a>
        </form>
    @endif

    <template id="producto-template">
        <tr class="producto-row">
            <td>
                <select name="productos[__INDEX__][producto_id]"
                        class="producto-select" required>
                    <option value="">Selecciona</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}"
                                data-costo="{{ $producto->costo }}">
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="productos[__INDEX__][cantidad]"
                       class="cantidad" min="1" step="1" value="1" required>
            </td>
            <td>
                <input type="number" name="productos[__INDEX__][costo_unitario]"
                       class="costo" min="0" max="9999999999.99"
                       step="0.01" value="0.00" required>
            </td>
            <td class="subtotal">$0.00</td>
            <td><button type="button" class="quitar">Quitar</button></td>
        </tr>
    </template>

    <script>
        let siguienteIndice = {{ count($lineas) }};

        const container = document.getElementById('productos-container');
        const template = document.getElementById('producto-template');

        function actualizarTotales() {
            let total = 0;

            container.querySelectorAll('.producto-row').forEach(row => {
                const cantidad = Number(row.querySelector('.cantidad').value) || 0;
                const costo = Number(row.querySelector('.costo').value) || 0;
                const subtotal = cantidad * costo;

                row.querySelector('.subtotal').textContent =
                    '$' + subtotal.toFixed(2);

                total += subtotal;
            });

            document.getElementById('total').textContent =
                '$' + total.toFixed(2);
        }

        function actualizarOpciones() {
            const selects = [...container.querySelectorAll('.producto-select')];

            selects.forEach(select => {
                const otrosSeleccionados = selects
                    .filter(otro => otro !== select)
                    .map(otro => otro.value)
                    .filter(Boolean);

                [...select.options].forEach(option => {
                    if (!option.value) return;
                    option.disabled = otrosSeleccionados.includes(option.value);
                });
            });
        }

        document.getElementById('agregar-producto').addEventListener('click', () => {
            const html = template.innerHTML.replace(/__INDEX__/g, siguienteIndice++);
            container.insertAdjacentHTML('beforeend', html);
            actualizarOpciones();
            actualizarTotales();
        });

        container.addEventListener('change', event => {
            if (event.target.classList.contains('producto-select')) {
                const option = event.target.selectedOptions[0];
                const row = event.target.closest('.producto-row');

                if (option && option.value) {
                    row.querySelector('.costo').value =
                        Number(option.dataset.costo || 0).toFixed(2);
                }

                actualizarOpciones();
            }

            actualizarTotales();
        });

        container.addEventListener('input', actualizarTotales);

        container.addEventListener('click', event => {
            if (event.target.classList.contains('quitar')) {
                if (container.querySelectorAll('.producto-row').length <= 1) {
                    alert('La compra debe tener al menos un producto.');
                    return;
                }

                event.target.closest('.producto-row').remove();
                actualizarOpciones();
                actualizarTotales();
            }
        });

        actualizarOpciones();
        actualizarTotales();
    </script>
</body>
</html>

