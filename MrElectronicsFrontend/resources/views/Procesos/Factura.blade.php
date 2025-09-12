{{-- @extends('welcome')

@section('content') --}}
<!DOCTYPE html>
<html lang="en" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <title>MRELCTRONICS</title>
    <style>
        .table, .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="max-w-4xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-lg">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-primary">FACTURA</h1>
                <p class="text-gray-500">MR ELECTRONICS</p>
            </div>
            <div>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($proceso['fecha_inicio'])->format('Y-m-d') }}</p>
                <p><strong>Factura #:</strong> FACPROC-{{ str_pad($proceso['id'], 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Columna: Datos del cliente -->
            <div>
                <h2 class="text-xl font-semibold text-info mb-2">Datos del cliente</h2>
                <div class="border border-base-content/5 bg-base-100 overflow-x-auto">
                    <table class="table table-fixed w-full">
                        <thead>
                            <tr>
                                <th class="w-1/4 text-sm font-semibold text-gray-600 whitespace-nowrap">Nombre</th>
                                <th class="w-1/4 text-sm font-semibold text-gray-600 whitespace-nowrap">Documento</th>
                                <th class="w-1/4 text-sm font-semibold text-gray-600 whitespace-nowrap">Teléfono</th>
                                <th class="w-1/4 text-sm font-semibold text-gray-600 whitespace-nowrap">Dirección</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="whitespace-nowrap">{{ $proceso['cliente']['nombre'] }}</td>
                                <td class="whitespace-nowrap">{{ $proceso['cliente']['documento'] }}</td>
                                <td class="whitespace-nowrap">{{ $proceso['cliente']['telefono'] }}</td>
                                <td class="whitespace-nowrap">{{ $proceso['cliente']['direccion'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- Columna: Información del equipo -->
            <div>
                <h2 class="text-xl font-semibold text-info mb-2">Información del equipo</h2>
                <div class="border border-base-content/5 bg-base-100">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="text-sm font-semibold text-gray-600">Marca</th>
                                <th class="text-sm font-semibold text-gray-600">Modelo</th>
                                <th class="text-sm font-semibold text-gray-600">Pulgadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $proceso['marca']['nombre'] }}</td>
                                <td>{{ $proceso['modelo']['nombre'] }}</td>
                                <td>{{ $proceso['pulgada']['medida'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-info mb-2">Detalles de la Reparación</h2>
        <div class="mb-8">
            <div class="border border-base-content/5 bg-base-100">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="text-sm font-semibold text-gray-600">Falla</th>
                            <th class="text-sm font-semibold text-gray-600">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $proceso['falla'] }}</td>
                            <td>{{ $proceso['descripcion'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-between items-center mb-8 border-t pt-4">
            <h3 class="text-lg font-semibold">
                Total a Pagar: <span></span>
            </h3>
            <h3 class="text-lg font-semibold">
                Firma: <span>_______________________________</span>
            </h3>
        </div>
        <!-- Botones de acción -->
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('procesos.factura', $proceso['id']) }}" class="btn btn-outline btn-primary" target="_blank">
                📄 Descargar PDF
            </a>
            <a href="{{ route('procesos.index') }}" class="btn btn-outline btn-warning">
                ← Volver al listado
            </a>
        </div>

        <footer class="text-center text-gray-500 mt-12">
            <p>Gracias por su confianza.</p>
            <p>MR ELECTRONICS</p>
        </footer>
    </div>
</body>
</html>
{{-- @endsection --}}
