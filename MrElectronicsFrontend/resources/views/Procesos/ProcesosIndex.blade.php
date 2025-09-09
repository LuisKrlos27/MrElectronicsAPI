{{-- resources/views/procesos/index.blade.php --}}
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
    <h2 class="text-3xl font-bold text-center text-primary mb-8">LISTADO DE PROCESOS</h2>

    <div class="flex justify-end mb-4">
        <a href="{{ route('procesos.create') }}" class="font-bold btn btn-outline btn-success">REGISTRAR</a>
    </div>

    @if(empty($procesos) || $procesos === 0)
        <p class="text-center text-gray-600">No hay procesos registrados.</p>
    @else
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table">
            <thead>
                <tr>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">ID</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Cliente</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Marca</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Modelo</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Pulgadas</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Falla</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Descripcion</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Estado</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Fecha de ingreso</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Fecha de entrega</th>
                <th class="whitespace-nowrap text-sm font-semibold text-gray-600">Opciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($procesos as $proceso)
                <tr>
                    <td class=" whitespace-nowrap ">{{ $proceso['id'] }}</td>
                    <td class=" whitespace-nowrap ">{{ $proceso['cliente']['nombre'] }}</td>
                    <td class=" whitespace-nowrap ">{{ $proceso['marca']['nombre'] }}</td>
                    <td class=" whitespace-nowrap ">{{ $proceso['modelo']['nombre'] }}</td>
                    <td class=" whitespace-nowrap ">{{ $proceso['pulgada']['medida'] ?? 'No asignada' }}</td>
                    <td class=" whitespace-nowrap ">{{ $proceso['falla'] }}</td>
                    <td class=" whitespace-nowrap ">{{ $proceso['descripcion'] }}</td>
                    <td class=" whitespace-nowrap ">
                        @if ($proceso['estado'])
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-error">Inactivo</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap">{{ $proceso['fecha_ingreso']->format('Y-m-d') }}</td>
                    <td class="whitespace-nowrap">
                        @if($proceso['fecha_cierre'])
                            {{ $proceso['fecha_cierre']->format('Y-m-d') }}
                        @else
                            Pendiente
                        @endif

                        </td><td class="flex flex-col sm:flex-row gap-1">
                            <a href="{{ route('procesos.show', $proceso['id']) }}" class="font-bold btn-sm btn btn-outline btn-info">Ver evidencias</a>
                            <a href="{{ route('procesos.factura', $proceso['id']) }}" class="font-bold btn-sm btn btn-outline">Factura</a>
                            <a href="{{ route('procesos.imprimirFactura', $proceso['id']) }}" class="font-bold btn-sm btn btn-outline btn-primary">Imprimir</a>
                            <a href="{{ route('procesos.edit', $proceso['id']) }}" class="font-bold btn-sm btn btn-outline btn-warning">Editar</a>
                            {{-- <form action="{{ route('procesos.destroy', $proceso->id) }}" method="POST" onsubmit="return confirm('¿Estas seguro de eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button class="font-bold btn-sm btn btn-outline btn-error" type="submit">Eliminar</button>
                            </form> --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay procesos registrados.</td>
                    </tr>
                @endforelse
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
