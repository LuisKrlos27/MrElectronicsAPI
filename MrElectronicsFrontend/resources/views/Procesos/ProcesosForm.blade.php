@extends('welcome')
@section('content')

<div class="max-w-2xl mx-auto mt-10 bg-base-100 p-6 rounded shadow">
    <h2 class="text-2xl text-center font-bold mb-8 text-primary">REGISTRO DE PROCESOS</h2>

    <form action="{{ route('procesos.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <!-- Cliente -->
        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-600">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="select select-bordered w-full" required>
                <option value="">Selecciona un cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente['id'] }}">{{ $cliente['nombre'] }}</option>
                @endforeach
                <option value="nuevo">+ Agregar nuevo cliente</option>
            </select>
        </div>

        <!-- FORMULARIO DE NUEVO CLIENTE -->
        <div id="nuevo_cliente_form" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 border-t pt-4">
            <h3 class="text-lg font-bold text-primary md:col-span-2">Nuevo Cliente</h3>

            <div>
                <label class="text-sm font-semibold text-gray-600">Nombre</label>
                <input type="text" name="nuevo_cliente_nombre" class="input input-bordered w-full">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Documento</label>
                <input type="number" name="nuevo_cliente_documento" class="input input-bordered w-full">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Teléfono</label>
                <input type="number" name="nuevo_cliente_telefono" class="input input-bordered w-full">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Dirección</label>
                <input type="text" name="nuevo_cliente_direccion" class="input input-bordered w-full">
            </div>

            <h3 class="text-lg font-bold text-primary md:col-span-2">Seguimiento del proceso</h3>
        </div>

        <!-- Marca -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Marca</label>
            <select name="marca_id" id="marca_id" class="select select-bordered w-full" required>
                <option value="">Selecciona una marca</option>
                @foreach($marcas as $marca)
                    <option value="{{ $marca['id'] }}">{{ $marca['nombre'] }}</option>
                @endforeach
                <option value="nueva">+ Agregar nueva marca</option>
            </select>
            <input type="text" name="nueva_marca" id="nueva_marca" class="input input-bordered w-full mt-2 hidden" placeholder="Escribe la nueva marca">
        </div>

        <!-- Modelo -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Modelo</label>
            <select name="modelo_id" id="modelo_id" class="select select-bordered w-full" required>
                <option value="">Selecciona un modelo</option>
                @foreach($modelos as $modelo)
                    <option value="{{ $modelo['id'] }}">{{ $modelo['nombre'] }}</option>
                @endforeach
                <option value="nuevo">+ Agregar nuevo modelo</option>
            </select>
            <input type="text" name="nuevo_modelo" id="nuevo_modelo" class="input input-bordered w-full mt-2 hidden" placeholder="Escribe el nuevo modelo">
        </div>

        <!-- Pulgadas -->
        <div>
            <label class="text-sm font-semibold text-gray-600">Pulgadas</label>
            <select name="pulgada_id" id="pulgada_id" class="select select-bordered w-full" required>
                <option value="">Selecciona una pulgada</option>
                @foreach($pulgadas as $pulgada)
                    <option value="{{ $pulgada['id'] }}">{{ $pulgada['medida'] }}</option>
                @endforeach
                <option value="nuevo">+ Agregar nueva pulgada</option>
            </select>
            <input type="text" name="nueva_pulgada" id="nueva_pulgada" class="input input-bordered w-full mt-2 hidden" placeholder="Escribe la nueva pulgada">
        </div>

        <!-- Falla -->
        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-600">Falla</label>
            <input type="text" name="falla" class="input input-bordered w-full" value="{{ old('falla') }}" required>
        </div>

        <!-- Descripción -->
        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-600">Descripción</label>
            <textarea name="descripcion" class="textarea font-semibold text-gray-600 w-full" placeholder="Descripción del problema">{{ old('descripcion') }}</textarea>
        </div>

        <!-- Estado -->
        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-600">Estado</label>
            <select name="estado" class="select select-bordered w-full" required>
                <option value="1" {{ old('estado') == 1 ? 'selected' : '' }}>Abierto</option>
                <option value="0" {{ old('estado') == 0 ? 'selected' : '' }}>Cerrado</option>
            </select>
        </div>

        <div class="md:col-span-2 flex justify-center gap-4 pt-4">
            <a href="{{ route('procesos.index') }}" class="btn btn-outline btn-warning">Cancelar</a>
            <button type="submit" class="btn btn-outline btn-primary">Guardar</button>
        </div>
    </form>
</div>

<script>
    // Cliente
    document.getElementById('cliente_id').addEventListener('change', function() {
        document.getElementById('nuevo_cliente_form').classList.toggle('hidden', this.value !== 'nuevo');
    });

    // Marca
    document.getElementById('marca_id').addEventListener('change', function() {
        document.getElementById('nueva_marca').classList.toggle('hidden', this.value !== 'nueva');
    });

    // Modelo
    document.getElementById('modelo_id').addEventListener('change', function() {
        document.getElementById('nuevo_modelo').classList.toggle('hidden', this.value !== 'nuevo');
    });

    //pulgada
    document.getElementById('pulgada_id').addEventListener('change', function() {
        document.getElementById('nueva_pulgada').classList.toggle('hidden', this.value !== 'nuevo');
    });
</script>
@endsection
