{{-- resources/views/procesos/show.blade.php --}}
@extends('welcome')

@section('content')
<div class="max-w-3xl mx-auto mt-10 bg-base-100 p-8 rounded-lg shadow-lg">
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary">PROCESO</h1>
            <p class="text-gray-500">MR ELECTRONICS</p>
        </div>
        <div>
            <p><strong>Fecha:</strong> {{ $proceso->fecha_inicio->format('d/m/Y') }}</p>
            <p><strong>PROCESO #:</strong> FACPROC-{{ str_pad($proceso->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
    </header>

    <!-- Datos del proceso -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-info border-b-2 border-gray-300 pb-2 mb-4">
            Datos del proceso
        </h2>

        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-sm font-semibold text-gray-600">Cliente</th>
                        <th class="text-sm font-semibold text-gray-600">Marca</th>
                        <th class="text-sm font-semibold text-gray-600">Modelo</th>
                        <th class="text-sm font-semibold text-gray-600">Pulgadas</th>
                        <th class="text-sm font-semibold text-gray-600">Falla</th>
                        <th class="text-sm font-semibold text-gray-600">Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $proceso->cliente->nombre }}</td>
                        <td>{{ $proceso->marca->nombre }}</td>
                        <td>{{ $proceso->modelo->nombre }}</td>
                        <td>{{ $proceso->pulgada->medida }}</td>
                        <td>{{ $proceso->falla }}</td>
                        <td>{{ $proceso->descripcion }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulario agregar evidencias -->
    <form action="{{ route('evidencias.store', $proceso->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div id="contenedor-evidencias" class="space-y-6">
            <div class="bloque-evidencia border p-4 rounded-lg bg-base-200">
                <div>
                    <label class="block font-semibold">Imagen</label>
                    <input type="file" name="imagenes[]" class="file-input file-input-bordered w-full" accept="image/*">
                </div>
                <div>
                    <label class="block font-semibold">Comentario</label>
                    <textarea name="comentarios[]" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <button type="button" id="btn-agregar" class="btn btn-outline btn-info">
                + Agregar evidencia
            </button>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn btn-outline btn-success">Guardar evidencias</button>
        </div>
    </form>

    <!-- Evidencias existentes -->
    <div class="mt-10">
        <h3 class="text-xl font-semibold mb-4">Evidencias registradas</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($proceso->evidencias as $evidencia)
                <div class="card bg-base-200 shadow-md p-4 rounded-lg flex flex-col">
                    @if($evidencia->imagen)
                        <img src="{{ asset('storage/' . $evidencia->imagen) }}"
                            alt="Evidencia"
                            class="w-full h-40 object-cover rounded-md mb-3">
                    @endif

                    <div class="flex justify-between items-start">
                        <p class="text-white-800 flex-1">{{ $evidencia->comentario }}</p>

                        <!-- Botón eliminar -->
                        <form action="{{ route('evidencias.destroy', $evidencia->id) }}" method="POST"
                            onsubmit="return confirm('¿Estás seguro de eliminar esta evidencia?');"
                            class="ml-4 flex-shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline btn-error">Eliminar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('procesos.index') }}" class="btn btn-outline btn-warning">← Volver al listado</a>
    </div>

    <footer class="text-center text-gray-500 mt-12">
        <p>Gracias por su confianza.</p>
        <p>MR ELECTRONICS</p>
    </footer>
</div>

{{-- Script para clonar formulario --}}
<script>
    document.getElementById('btn-agregar').addEventListener('click', function () {
        let contenedor = document.getElementById('contenedor-evidencias');
        let bloque = contenedor.querySelector('.bloque-evidencia').cloneNode(true);
        bloque.querySelectorAll('input, textarea').forEach(el => el.value = '');
        contenedor.appendChild(bloque);
    });
</script>
@endsection
