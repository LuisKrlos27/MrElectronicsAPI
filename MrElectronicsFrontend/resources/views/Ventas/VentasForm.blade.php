@extends('welcome')
@section('content')

<div class="max-w-3xl mx-auto mt-10 bg-base-100 p-6 rounded shadow">
    <h2 class="text-2xl text-center font-bold mb-8 text-primary">REGISTRO DE VENTAS</h2>

    <form action="{{ route('ventas.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <!-- Cliente -->
        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-600">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="select select-bordered w-full" onchange="toggleNuevoCliente(this)">
                <option value="">Selecciona un cliente</option>
                @foreach($cliente as $cli)
                    <option value="{{ $cli['id'] }}">{{ $cli['nombre'] }}</option>
                @endforeach
                <option value="nuevo">+ Agregar nuevo cliente</option>
            </select>
        </div>

        <!-- Campos para nuevo cliente -->
        <div id="nuevo_cliente_fields" class="md:col-span-2 hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <label class="text-sm font-semibold text-gray-600">Nombre</label>
                <input type="text" name="nuevo_cliente_nombre" class="input input-bordered w-full" placeholder="Nombre completo">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Documento</label>
                <input type="text" name="nuevo_cliente_documento" class="input input-bordered w-full" placeholder="DNI, Cédula, etc.">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Teléfono</label>
                <input type="text" name="nuevo_cliente_telefono" class="input input-bordered w-full" placeholder="Ej: +58 424-1234567">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Dirección</label>
                <input type="text" name="nuevo_cliente_direccion" class="input input-bordered w-full" placeholder="Dirección completa">
            </div>
        </div>

        <!-- Productos -->
        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-600">Productos</label>
            <div id="productos_container" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 producto_item">
                    <select name="productos[0][producto_id]" class="select select-bordered w-full producto_select" required>
                        <option value="">Selecciona un producto</option>
                        @foreach($producto as $pro)
                            <option value="{{ $pro['id'] }}" data-precio="{{ $pro['precio'] }}">
                                {{ $pro['tipo']['nombre'] }} - {{ $pro['marca']['nombre'] }} - {{ $pro['modelo']['nombre'] }} (Stock: {{ $pro['cantidad'] }})
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="productos[0][cantidad]" min="1" class="input input-bordered w-full cantidad_input" placeholder="Cantidad" required>
                    <input type="text" class="input input-bordered w-full precio_input" placeholder="Precio unitario" disabled data-raw="0">
                    <input type="text" class="input input-bordered w-full subtotal_input" placeholder="Subtotal" disabled data-raw="0">
                </div>
            </div>

            <button type="button" id="add_producto" class="btn btn-sm btn-secondary mt-2">+ Agregar otro producto</button>
        </div>

        <!-- Fecha -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Fecha</label>
            <input type="date" name="fecha_venta" class="input input-bordered w-full" required>
        </div>

        <!-- Pago -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Pago</label>
            <input type="number" step="0.01" name="pago" class="input input-bordered w-full" id="pago_input" required>
        </div>

        <!-- Total -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Total</label>
            <input type="text" class="input input-bordered w-full" id="total_input" disabled data-raw="0">
        </div>

        <!-- Cambio -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Cambio</label>
            <input type="text" class="input input-bordered w-full" id="cambio_input" disabled>
        </div>

        <div class="md:col-span-2 flex justify-center gap-4 pt-4">
            <a href="{{ route('ventas.index') }}" class="btn btn-outline btn-warning">Cancelar</a>
            <button type="submit" class="btn btn-outline btn-primary">Guardar</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let index = 1;
    const container = document.getElementById('productos_container');
    const totalInput = document.getElementById('total_input');
    const pagoInput = document.getElementById('pago_input');
    const cambioInput = document.getElementById('cambio_input');

    function formatoMoneda(num) {
        return '$' + num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function recalcularTotal() {
        let total = 0;
        container.querySelectorAll('.subtotal_input').forEach(input => {
            const valor = parseFloat(input.dataset.raw) || 0;
            total += valor;
        });
        totalInput.value = formatoMoneda(total);
        totalInput.dataset.raw = total;
        recalcularCambio();
    }

    function recalcularCambio() {
        const pago = parseFloat(pagoInput.value) || 0;
        const total = parseFloat(totalInput.dataset.raw) || 0;
        let cambio = pago - total;
        if (cambio < 0) cambio = 0;
        cambioInput.value = formatoMoneda(cambio);
    }

    function bindEvents(row) {
        const selectProducto = row.querySelector('.producto_select');
        const cantidadInput = row.querySelector('.cantidad_input');
        const precioInput = row.querySelector('.precio_input');
        const subtotalInput = row.querySelector('.subtotal_input');

        selectProducto.addEventListener('change', function () {
            const precio = parseFloat(this.options[this.selectedIndex].dataset.precio) || 0;
            precioInput.value = formatoMoneda(precio);
            precioInput.dataset.raw = precio;
            const subtotal = (parseFloat(cantidadInput.value) || 0) * precio;
            subtotalInput.value = formatoMoneda(subtotal);
            subtotalInput.dataset.raw = subtotal;
            recalcularTotal();
        });

        cantidadInput.addEventListener('input', function () {
            const cantidad = parseFloat(cantidadInput.value) || 0;
            const precio = parseFloat(precioInput.dataset.raw) || 0;
            const subtotal = cantidad * precio;
            subtotalInput.value = formatoMoneda(subtotal);
            subtotalInput.dataset.raw = subtotal;
            recalcularTotal();
        });
    }

    bindEvents(container.querySelector('.producto_item'));

    document.getElementById('add_producto').addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.classList.add('grid', 'grid-cols-1', 'md:grid-cols-4', 'gap-4', 'producto_item');
        newRow.innerHTML = `
            <select name="productos[${index}][producto_id]" class="select select-bordered w-full producto_select" required>
                <option value="">Selecciona un producto</option>
                @foreach($producto as $pro)
                    <option value="{{ $pro['id'] }}" data-precio="{{ $pro['precio'] }}">
                        {{ $pro['tipo']['nombre'] }} - {{ $pro['marca']['nombre'] }} - {{ $pro['modelo']['nombre'] }} (Stock: {{ $pro['cantidad'] }})
                    </option>
                @endforeach
            </select>
            <input type="number" name="productos[${index}][cantidad]" min="1" class="input input-bordered w-full cantidad_input" placeholder="Cantidad" required>
            <input type="text" class="input input-bordered w-full precio_input" placeholder="Precio unitario" disabled data-raw="0">
            <input type="text" class="input input-bordered w-full subtotal_input" placeholder="Subtotal" disabled data-raw="0">
        `;
        container.appendChild(newRow);
        bindEvents(newRow);
        index++;
    });

    pagoInput.addEventListener('input', recalcularCambio);
});

function toggleNuevoCliente(select) {
    const fields = document.getElementById('nuevo_cliente_fields');
    fields.classList.toggle('hidden', select.value !== 'nuevo');
}
</script>
@endsection
