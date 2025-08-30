@extends('welcome')

@section('content')
<!-- Mensajes de éxito y error -->
@if (session('success'))
    <div id="success-alert" class="alert alert-success shadow-lg mb-4 md:col-span-4 transition-opacity duration-500">
        <div>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if (session('error'))
    <div id="error-alert" class="alert alert-error shadow-lg mb-4 md:col-span-4 transition-opacity duration-500">
        <div>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

<div class="max-w-6xl mx-auto mt-10 bg-base-100 p-8 rounded-lg shadow-lg">
    <h2 class="text-3xl font-bold text-center text-primary mb-8">LISTADO DE VENTAS</h2>

    <div class="flex justify-end mb-4">
        <a href="{{ route('ventas.create') }}" class="font-bold btn btn-outline btn-success">REGISTRAR</a>
    </div>

    @if($venta->isEmpty())
        <p class="text-center text-gray-600">No hay ventas registradas.</p>
    @else
        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-sm font-semibold text-gray-600">#</th>
                        <th class="text-sm font-semibold text-gray-600">Cliente</th>
                        <th class="text-sm font-semibold text-gray-600">Fecha</th>
                        <th class="text-sm font-semibold text-gray-600">Pago</th>
                        <th class="text-sm font-semibold text-gray-600">Total</th>
                        <th class="text-sm font-semibold text-gray-600">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venta as $ven)
                        <tr>
                            <td>{{ $ven->id }}</td>
                            <td>{{ $ven->cliente->nombre ?? 'N/A' }}</td>
                            <td>{{ $ven->fecha_venta->format('Y-m-d') ?? 'N/A' }}</td>
                            <td>${{ number_format($ven->pago,0,2) }}</td>
                            <td>${{ number_format($ven->total,0,2) }}</td>
                            <td class="flex flex-col sm:flex-row gap-1">
                                <a href="{{ route('ventas.show', $ven->id) }}" class="font-bold btn-sm btn btn-outline btn-info">Ver factura</a>
                                <a href="{{ route('ventas.factura', $ven->id) }}" class="font-bold btn-sm btn btn-outline btn-primary">Imprimir</a>
                                <a href="{{ route('ventas.edit', $ven->id) }}" class="font-bold btn-sm btn btn-outline btn-warning">Editar</a>
                                {{-- <form action="{{ route('ventas.destroy', $ven->id) }}" method="POST" onsubmit="return confirm('¿Estas seguro de eliminar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-bold btn-sm btn btn-outline btn-error" type="submit">Eliminar</button>
                                </form> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Script para ocultar alertas -->
<script>
    setTimeout(() => {
        const success = document.getElementById('success-alert');
        const error = document.getElementById('error-alert');

        if (success) {
            success.style.opacity = '0';
            setTimeout(() => success.remove(), 500);
        }
        if (error) {
            error.style.opacity = '0';
            setTimeout(() => error.remove(), 500);
        }
    }, 3000);
</script>
@endsection
