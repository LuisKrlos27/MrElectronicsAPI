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
    <h2 class="text-3xl font-bold text-center text-primary mb-8">LISTADO DE CLIENTES</h2>

    <div class="flex justify-end mb-4">
        <a href="{{ route('clientes.create') }}" class="font-bold btn btn-outline btn-success">REGISTRAR</a>
    </div>

    @if(empty($clientes) || count($clientes) === 0)
        <p class="text-center text-gray-600">No hay clientes registrados.</p>
    @else
        <div class="overflow-x-auto overflow-y-auto rounded-box border border-base-content/5 bg-base-100" style="max-height: 390px;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-sm font-semibold text-gray-600">#</th>
                        <th class="text-sm font-semibold text-gray-600">Nombre</th>
                        <th class="text-sm font-semibold text-gray-600">Documento</th>
                        <th class="text-sm font-semibold text-gray-600">Teléfono</th>
                        <th class="text-sm font-semibold text-gray-600">Dirección</th>
                        <th class="text-sm font-semibold text-gray-600">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cli)
                        <tr>
                            <td>{{ $cli['id'] ?? '-' }}</td>
                            <td>{{ $cli['nombre'] ?? '-' }}</td>
                            <td>{{ $cli['documento'] ?? '-' }}</td>
                            <td>{{ $cli['telefono'] ?? '-' }}</td>
                            <td>{{ $cli['direccion'] ?? '-' }}</td>
                            <td class="flex flex-col sm:flex-row gap-1">
                                <a href="{{ route('clientes.edit', $cli['id']) }}" class="font-bold btn-sm btn btn-outline btn-warning">Editar</a>
                                <form action="{{ route('clientes.destroy', $cli['id']) }}" method="POST" onsubmit="return confirm('¿Estas seguro de eliminar este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-bold btn-sm btn btn-outline btn-error" type="submit">Eliminar</button>
                                </form>
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
