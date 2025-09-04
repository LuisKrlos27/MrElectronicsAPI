<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACTURA MR ELECTRONICS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .max-w-4xl {
            max-width: 56rem;
            margin: 0 auto;
        }
        .bg-white {
            background-color: #fff;
        }
        .p-8 {
            padding: 2rem;
        }
        .rounded-lg {
            border-radius: 0.5rem;
        }
        .shadow-lg {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .border {
            border-width: 1px;
        }
        .border-gray-200 {
            border-color: #e5e7eb;
        }
        .mb-8 {
            margin-bottom: 2rem;
        }
        .mb-6 {
            margin-bottom: 1.5rem;
        }
        .mb-4 {
            margin-bottom: 1rem;
        }
        .mt-10 {
            margin-top: 2.5rem;
        }
        .mt-12 {
            margin-top: 3rem;
        }
        .text-3xl {
            font-size: 1.875rem;
        }
        .text-xl {
            font-size: 1.25rem;
        }
        .text-lg {
            font-size: 1.125rem;
        }
        .text-sm {
            font-size: 0.875rem;
        }
        .font-bold {
            font-weight: 700;
        }
        .font-semibold {
            font-weight: 600;
        }
        .text-primary {
            color: #3b82f6;
        }
        .text-blue-600 {
            color: #2563eb;
        }
        .text-gray-500 {
            color: #6b7280;
        }
        .text-gray-600 {
            color: #4b5563;
        }
        .text-black-600 {
            color: #000;
        }
        .border-b-2 {
            border-bottom-width: 2px;
        }
        .border-info {
            border-color: #3b82f6;
        }
        .pb-2 {
            padding-bottom: 0.5rem;
        }
        .overflow-x-auto {
            overflow-x: auto;
        }
        .border-base-content\/5 {
            border-color: rgba(0, 0, 0, 0.05);
        }
        .bg-base-100 {
            background-color: #f8fafc;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f3f4f6;
            font-weight: 600;
        }
        .flex {
            display: flex;
        }
        .flex-col {
            flex-direction: column;
        }
        .justify-right {
            justify-content: right;
        }
        .justify-between {
            justify-content: space-between;
        }
        .items-center {
            align-items: center;
        }
        .text-center {
            text-align: center;
        }
        .text-gray-800 {
            color: #1f2937;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <div class="max-w-4xl bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        <!-- Encabezado -->
        <header class="mb-8">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <h1 class="text-3xl font-bold text-primary">FACTURA</h1>
                        <p class="text-gray-500">MR ELECTRONICS</p>
                    </td>
                    <td style="text-align: right;">
                        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</p>
                        <p><strong>Factura #:</strong> FACVENT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </td>
                </tr>
            </table>
        </header>

        <!-- Datos del Cliente -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-blue-600 border-b-2 border-info pb-2 mb-4">Datos del cliente</h2>
            <div class="overflow-x-auto border border-base-content/5 bg-base-100">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-sm font-semibold text-gray-600">Nombre</th>
                            <th class="text-sm font-semibold text-gray-600">Documento</th>
                            <th class="text-sm font-semibold text-gray-600">Teléfono</th>
                            <th class="text-sm font-semibold text-gray-600">Dirección</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $venta->cliente->nombre }}</td>
                            <td>{{ $venta->cliente->documento }}</td>
                            <td>{{ $venta->cliente->telefono ?? 'N/A' }}</td>
                            <td>{{ $venta->cliente->direccion ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Productos -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-blue-600 border-b-2 border-info pb-2 mb-4">Datos del producto</h2>
            <div class="overflow-x-auto border border-base-content/5 bg-base-100">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-sm font-semibold text-gray-600">Producto</th>
                            <th class="text-sm font-semibold text-gray-600">Cantidad</th>
                            <th class="text-sm font-semibold text-gray-600">Precio Unitario</th>
                            <th class="text-sm font-semibold text-gray-600">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $detalle)
                            <tr>
                                <td class="text-sm font-semibold text-gray-600">
                                    @if($detalle->producto && $detalle->producto->tipo)
                                        {{ $detalle->producto->tipo->nombre }} -
                                        {{ $detalle->producto->marca->nombre }} -
                                        {{ $detalle->producto->modelo->nombre }}
                                        @if($detalle->producto->pulgada)
                                            ({{ $detalle->producto->pulgada->medida }}")
                                        @endif
                                    @else
                                        Producto no disponible
                                    @endif
                                </td>
                                <td class="text-sm font-semibold text-gray-600">{{ $detalle->cantidad }}</td>
                                <td class="text-sm font-semibold text-gray-600">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                                <td class="text-sm font-semibold text-gray-600">${{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totales -->
        <h2 class="text-xl font-semibold text-blue-600 border-b-2 border-info pb-2 mb-4">Datos del pago</h2>
        <div class="flex flex-col justify-right text-gray-800 text-lg font-semibold">
            <div class="overflow-x-auto border border-base-content/5 bg-base-100">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-sm font-semibold text-gray-600">Total</th>
                            <th class="text-sm font-semibold text-gray-600">Pago</th>
                            <th class="text-sm font-semibold text-gray-600">Cambio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-sm font-semibold text-black-600">${{ number_format($venta->total, 0, ',', '.') }}</td>
                            <td class="text-sm font-semibold text-black-600">${{ number_format($venta->pago, 0, ',', '.') }}</td>
                            <td class="text-sm font-semibold text-black-600">${{ number_format($venta->cambio, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Firma -->
        <h2 class="text-xl font-semibold text-blue-600 border-b-2 border-gray-300 pb-2 mb-4 mt-12"></h2>
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-lg font-semibold">
                Total a Pagar: <span class="text-black-600">${{ number_format($venta->total, 0, ',', '.') }}</span>
            </h3>
            <h3 class="text-lg font-semibold">
                Firma: <span>_______________________________</span>
            </h3>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>Gracias por su confianza.</p>
            <p>MR ELECTRONICS</p>
        </footer>
    </div>

</body>
</html>
