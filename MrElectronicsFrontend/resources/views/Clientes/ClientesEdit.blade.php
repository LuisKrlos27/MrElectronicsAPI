@extends('welcome')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-base-100 p-6 rounded shadow">
    <h2 class="text-2xl text-center font-bold mb-8 text-primary">EDITAR PRODUCTO</h2>

    <form action="{{ route('clientes.update', $cliente['id']) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        {{-- Mensaje de error si existe --}}
        @if(session('error'))
            <div class="md:col-span-2 bg-red-100 text-red-800 p-3 rounded">
                {{ session('error') }}
            </div>
        @endif
        <!-- Nombre -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Nombre</label>
            <input type="text" name="nombre" class="input input-bordered w-full" value="{{$cliente['nombre'] ?? '' }}" required>
        </div>

        <!-- Identificacion -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Documento</label>
            <input type="number" name="documento" class="input input-bordered w-full" value="{{ $cliente['documento'] ?? '' }}" required>
        </div>

        <!-- Telefono -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Telefono</label>
            <input type="number" name="telefono" class="input input-bordered w-full" value="{{ $cliente['telefono'] ?? '' }}" required>
        </div>

        <!-- Direccion -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Direccion</label>
            <input type="text" name="direccion" class="input input-bordered w-full" value="{{ $cliente['direccion'] ?? '' }}" required>
        </div>

        <div class="md:col-span-2 flex justify-center gap-4 pt-4">
            <a href="{{ route('clientes.index') }}" class="btn btn-outline btn-warning">Cancelar</a>
            <button type="submit" class="btn btn-outline btn-primary">Actualizar</button>
        </div>
    </form>
</div>
@endsection
