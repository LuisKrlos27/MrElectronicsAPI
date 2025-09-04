@extends('welcome')

@section('content')
<div class="max-w-3xl mx-auto mt-10 bg-base-100 p-6 rounded shadow">
    <h2 class="text-2xl text-center font-bold mb-8 text-primary">EDITAR VENTA</h2>

    <form action="{{ route('ventas.update', $venta['id']) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        {{-- Mensaje de error si existe --}}
        @if(session('error'))
            <div class="md:col-span-2 bg-red-100 text-red-800 p-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Cliente -->
        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-600">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="select select-bordered w-full">
                <option value="">Selecciona un cliente</option>
                @foreach($cliente as $cli)
                    <option value="{{ $cli['id'] }}" {{ $venta['cliente']['id'] == $cli['id'] ? 'selected' : '' }}>
                        {{ $cli['nombre'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Productos -->
        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-600">Productos</label>
            <div id="productos_container" class="space-y-4">
                @foreach($venta['detalles'] ?? [] as $i => $detalle)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 producto_item">
                        <select name="productos[{{ $i }}][producto_id]" class="select select-bordered w-full producto_select" required>
                            <option value="">Selecciona un producto</option>
                            @foreach($producto as $pro)
                                <option value="{{ $pro['id'] }}" data-precio="{{ $pro['precio'] }}"
                                    {{ $detalle['producto_id'] == $pro['id'] ? 'selected' : '' }}>
                                    {{ $pro['tipo']['nombre'] }} - {{ $pro['marca']['nombre'] }} - {{ $pro['modelo']['nombre'] }} (Stock: {{ $pro['cantidad'] }})
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="productos[{{ $i }}][cantidad]" min="1" class="input input-bordered w-full cantidad_input"
                            value="{{ $detalle['cantidad'] }}" required>
                        <input type="text" class="input input-bordered w-full precio_input"
                            value="{{ number_format($detalle['precio_unitario'], 2) }}" disabled data-raw="{{ $detalle['precio_unitario'] }}">
                        <input type="text" class="input input-bordered w-full subtotal_input"
                            value="{{ number_format($detalle['subtotal'], 2) }}" disabled data-raw="{{ $detalle['subtotal'] }}">
                    </div>
                @endforeach
            </div>

            <button type="button" id="add_producto" class="btn btn-sm btn-secondary mt-2">+ Agregar otro producto</button>
        </div>

        <!-- Fecha -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Fecha</label>
            <input type="date" name="fecha_venta" class="input input-bordered w-full"
                value="{{ old('fecha_venta', \Carbon\Carbon::parse($venta['fecha_venta'])->format('Y-m-d')) }}" required>
        </div>

        <!-- Pago -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Pago</label>
            <input type="number" step="0.01" name="pago" class="input input-bordered w-full"
                id="pago_input" value="{{ old('pago', $venta['pago']) }}" required>
        </div>

        <!-- Total -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Total</label>
            <input type="text" class="input input-bordered w-full" id="total_input"
                value="${{ number_format($venta['total'], 2) }}" disabled data-raw="{{ $venta['total'] }}">
        </div>

        <!-- Cambio -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Cambio</label>
            <input type="text" class="input input-bordered w-full" id="cambio_input"
                value="${{ number_format($venta['cambio'], 2) }}" disabled>
        </div>

        <div class="md:col-span-2 flex justify-center gap-4 pt-4">
            <a href="{{ route('ventas.index') }}" class="btn btn-outline btn-warning">Cancelar</a>
            <button type="submit" class="btn btn-outline btn-primary">Actualizar</button>
        </div>
    </form>
</div>

{{-- Scripts para cálculos --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let index = {{ count($venta['detalles'] ?? []) }};
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

    container.querySelectorAll('.producto_item').forEach(row => bindEvents(row));

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
</script>
@endsection
