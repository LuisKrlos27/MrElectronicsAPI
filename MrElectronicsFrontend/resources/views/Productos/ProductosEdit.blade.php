@extends('welcome')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-base-100 p-6 rounded shadow">
    <h2 class="text-2xl text-center font-bold mb-8 text-primary">EDITAR PRODUCTO</h2>

    <form action="{{ route('productos.update', $producto['id']) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        {{-- Mensaje de error si existe --}}
        @if(session('error'))
            <div class="md:col-span-2 bg-red-100 text-red-800 p-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tipo -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Tipo</label>
            <select name="tipo_id" class="select select-bordered w-full" required>
                <option value="">Selecciona un tipo</option>
                @foreach($tipo as $tip)
                    <option value="{{ $tip['id'] }}" {{ $producto['tipo']['id'] == $tip['id'] ? 'selected' : '' }}>
                        {{ $tip['nombre'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Pulgadas -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Pulgadas</label>
            <select name="pulgada_id" class="select select-bordered w-full" required>
                <option value="">Selecciona pulgadas</option>
                @foreach($pulgada as $pul)
                    <option value="{{ $pul['id'] }}" {{ $producto['pulgada']['id'] == $pul['id'] ? 'selected' : '' }}>
                        {{ $pul['medida'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Marca -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Marca</label>
            <select name="marca_id" class="select select-bordered w-full" required>
                <option value="">-- Selecciona una marca --</option>
                @foreach($marca as $mar)
                    <option value="{{ $mar['id'] }}" {{ $producto['marca']['id'] == $mar['id'] ? 'selected' : '' }}>
                        {{ $mar['nombre'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Modelo -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Modelo</label>
            <select name="modelo_id" class="select select-bordered w-full" required>
                <option value="">-- Selecciona un modelo --</option>
                @foreach($modelo as $mod)
                    <option value="{{ $mod['id'] }}" {{ $producto['modelo']['id'] == $mod['id'] ? 'selected' : '' }}>
                        {{ $mod['nombre'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Precio -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Precio</label>
            <input type="number" step="0.01" name="precio" class="input input-bordered w-full" value="{{ old('precio', $producto['precio']) }}" required>
        </div>

        <!-- Cantidad -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Cantidad</label>
            <input type="number" name="cantidad" class="input input-bordered w-full" value="{{ old('cantidad', $producto['cantidad']) }}" required>
        </div>

        <!-- Número de pieza -->
        <div>
            <label class="text-sm font-semibold text-gray-600">N° de pieza</label>
            <input type="text" name="numero_pieza" class="input input-bordered w-full" value="{{ old('numero_pieza', $producto['numero_pieza']) }}">
        </div>

        <!-- Descripción -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Descripción</label>
            <textarea name="descripcion" class="textarea font-semibold text-gray-600 w-full" placeholder="Descripción">{{ old('descripcion', $producto['descripcion']) }}</textarea>
        </div>

        <div class="md:col-span-2 flex justify-center gap-4 pt-4">
            <a href="{{ route('productos.index') }}" class="btn btn-outline btn-warning">Cancelar</a>
            <button type="submit" class="btn btn-outline btn-primary">Actualizar</button>
        </div>
    </form>
</div>
@endsection
