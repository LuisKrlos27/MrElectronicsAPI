@extends('welcome')
@section('content')

<div class="max-w-2xl mx-auto mt-10 bg-base-100 p-6 rounded shadow">
    <h2 class="text-2xl text-center font-bold mb-8 text-primary">REGISTRO DE CLIENTES</h2>

    <form action="{{ route('clientes.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <!-- Nombre -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Nombre</label>
            <input type="text" name="nombre" class="input input-bordered w-full" required>
        </div>

        <!-- Identificacion -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Documento</label>
            <input type="number" name="documento" class="input input-bordered w-full" required>
        </div>

        <!-- Telefono -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Telefono</label>
            <input type="number" name="telefono" class="input input-bordered w-full" required>
        </div>

        <!-- Direccion -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Direccion</label>
            <input type="text" name="direccion" class="input input-bordered w-full" required>
        </div>


        <div class="md:col-span-2 flex justify-center gap-4 pt-4">
            <a href="{{ route('clientes.index') }}" class="btn btn-outline btn-warning">Cancelar</a>
            <button type="submit" class="btn btn-outline btn-primary">Guardar</button>
        </div>
    </form>
</div>

@endsection
