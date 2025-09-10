<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MR ELECTRONICS - FACTURA</title>
    <style>
        /* ESTILOS SIMPLIFICADOS */
        body {
            font-family: Arial, sans-serif;
            color: #000000; /* Todo en negro */
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        table th {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
        }

        .signature {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <table style="border: none; margin-bottom: 0;">
                <tr>
                    <td style="border: none; padding: 0;">
                        <h1 style="font-size: 20px; margin: 0; color: #000;">FACTURA</h1>
                        <p style="margin: 0; color: #000;">MR ELECTRONICS</p>
                    </td>
                    <td style="border: none; padding: 0; text-align: right;">
                        <p style="margin: 2px 0; color: #000;"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta['fecha_venta'])->format('d/m/Y') }}</p>
                        <p style="margin: 2px 0; color: #000;"><strong>Factura #:</strong> FACVENT-{{ str_pad($venta['id'], 5, '0', STR_PAD_LEFT) }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Datos del Cliente -->
        <div class="section">
            <div class="section-title">DATOS DEL CLIENTE</div>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Documento</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $venta['cliente']['nombre'] ?? 'N/A' }}</td>
                        <td>{{ $venta['cliente']['documento'] ?? 'N/A' }}</td>
                        <td>{{ $venta['cliente']['telefono'] ?? 'N/A' }}</td>
                        <td>{{ $venta['cliente']['direccion'] ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Productos -->
        <div class="section">
            <div class="section-title">DATOS DEL PRODUCTO</div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta['productos'] as $producto)
                        <tr>
                            <td>
                                {{ $producto['tipo'] ?? 'N/A' }} -
                                {{ $producto['marca'] ?? 'N/A' }} -
                                @if(isset($producto['pulgada']))
                                    {{ $producto['pulgada'] }}" -
                                @endif
                                {{ $producto['modelo'] ?? 'N/A' }}
                            </td>
                            <td>{{ $producto['cantidad'] }}</td>
                            <td>${{ number_format($producto['precio_unitario'], 0, ',', '.') }}</td>
                            <td>${{ number_format($producto['subtotal'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="section">
            <div class="section-title">DATOS DEL PAGO</div>
            <table>
                <thead>
                    <tr>
                        <th>Total</th>
                        <th>Pago</th>
                        <th>Cambio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-bold">${{ number_format($venta['total'], 0, ',', '.') }}</td>
                        <td class="text-bold">${{ number_format($venta['pago'], 0, ',', '.') }}</td>
                        <td class="text-bold">${{ number_format($venta['cambio'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Firma y Total a Pagar -->
        <div class="signature">
            <table style="border: none;">
                <tr>
                    <td style="border: none; padding: 0;">
                        <p style="margin: 0; font-weight: bold;">Total a Pagar: ${{ number_format($venta['total'], 0, ',', '.') }}</p>
                    </td>
                    <td style="border: none; padding: 0; text-align: right;">
                        <p style="margin: 0; font-weight: bold;">Firma: _______________________________</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 5px 0; color: #000;">Gracias por su confianza.</p>
            <p style="margin: 5px 0; color: #000;">MR ELECTRONICS</p>
        </div>
    </div>

</body>
</html>
