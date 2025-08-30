<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MR ELECTRONICS</title>
    @vite('resources/css/app.css')
    <style>
        /* Animaciones y efectos */
        .card-hover:hover {
            transform: translateY(-5px) scale(1.03);
            transition: transform 0.3s ease;
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-base-100 text-base-content">

<!-- Hero Section -->
<section class="hero bg-gradient-to-r from-primary to-secondary py-20 text-white text-center">
    <div class="container mx-auto">
        <h1 class="text-5xl font-bold mb-4 fade-in">👨🏼‍🔧 MR ELECTRONICS</h1>
        <p class="text-xl mb-6 fade-in">Sistema web moderno para negocios de electrónica que integra inventario, ventas y reparaciones en una sola plataforma eficiente y segura. 🚀</p>
        <a href="{{ route('productos.index') }}" class="btn btn-lg btn-primary fade-in">Descubre más</a>
    </div>
</section>

<!-- Sobre MR ELECTRONICS -->
<section class="py-16 px-6 container mx-auto fade-in">
    <h2 class="text-4xl font-bold mb-6 text-center">Sobre MR ELECTRONICS</h2>
    <p class="text-lg leading-relaxed text-justify max-w-4xl mx-auto ">
        MR Electrónics es un sistema web integral desarrollado en Laravel con PostgreSQL, diseñado para optimizar la gestión de inventario, ventas y reparaciones de equipos electrónicos. Su interfaz moderna y responsiva, potenciada con DaisyUI, permite un manejo ágil de productos, clientes y servicios, incluyendo la creación dinámica de registros sin interrumpir los flujos de trabajo. Las ventas se registran con detalle y permiten generar facturas electrónicas, mientras que los procesos de reparación incorporan seguimiento completo de equipos, registro de fallas, comentarios técnicos y evidencias fotográficas. MR Electrónics garantiza eficiencia operativa, trazabilidad de información y escalabilidad, posicionándose como una solución confiable y moderna para negocios dedicados a la venta y mantenimiento de productos electrónicos.
    </p>
</section>

<!-- Objetivos -->
<section class="py-16 px-6 bg-base-200 fade-in">
    <h2 class="text-4xl font-bold mb-10 text-center">🎯 Objetivos</h2>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            <h3 class="font-bold text-xl mb-2">Control de Inventario</h3>
            <p>Optimizar la gestión de inventario asegurando trazabilidad y stock actualizado.</p>
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            <h3 class="font-bold text-xl mb-2">Ventas Eficientes</h3>
            <p>Registrar y dar seguimiento a ventas y facturación de manera confiable.</p>
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            <h3 class="font-bold text-xl mb-2">Reparaciones</h3>
            <p>Gestionar procesos de reparación con seguimiento de fallas y evidencias.</p>
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            <h3 class="font-bold text-xl mb-2">Interfaz Intuitiva</h3>
            <p>Proporcionar una interfaz dinámica que agilice los procesos administrativos.</p>
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            <h3 class="font-bold text-xl mb-2">Seguridad y Escalabilidad</h3>
            <p>Garantizar seguridad en los datos y preparación para el crecimiento del negocio.</p>
        </div>
    </div>
</section>

<!-- Misión y Visión -->
<section class="py-16 px-6 container mx-auto grid lg:grid-cols-2 gap-12 fade-in">
    <div class="card card-hover bg-base-100 p-8 shadow-xl rounded-lg">
        <h3 class="text-2xl font-bold mb-4">🎯 Misión</h3>
        <p>Proveer una plataforma tecnológica integral que permita a negocios de electrónica gestionar inventario, ventas y reparaciones de manera eficiente y moderna, optimizando procesos y mejorando la experiencia de clientes y colaboradores.</p>
    </div>
    <div class="card card-hover bg-base-100 p-8 shadow-xl rounded-lg">
        <h3 class="text-2xl font-bold mb-4">🎯 Visión</h3>
        <p>Ser la solución líder en sistemas de gestión para negocios de electrónica, reconocida por innovación, confiabilidad y adaptación a las necesidades de los usuarios, contribuyendo a la transformación digital de pequeñas y medianas empresas.</p>
    </div>
</section>

<!-- Tecnologías -->
<section id="tecnologias" class="py-16 px-6 container mx-auto fade-in">
    <h2 class="text-4xl font-bold mb-10 text-center">👨🏼‍💻 Tecnologías utilizadas</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 max-w-6xl mx-auto">
        <div class="card card-hover p-6 shadow-lg rounded-lg text-center">
            <span class="text-xl font-bold">Laravel 10</span>
        </div>
        <div class="card card-hover p-6 shadow-lg rounded-lg text-center">
            <span class="text-xl font-bold">PHP 8.2</span>
        </div>
        <div class="card card-hover p-6 shadow-lg rounded-lg text-center">
            <span class="text-xl font-bold">PostgreSQL 15</span>
        </div>
        <div class="card card-hover p-6 shadow-lg rounded-lg text-center">
            <span class="text-xl font-bold">Tailwind</span>
        </div>
        <div class="card card-hover p-6 shadow-lg rounded-lg text-center">
            <span class="text-xl font-bold">JavaScript</span>
        </div>
        <div class="card card-hover p-6 shadow-lg rounded-lg text-center">
            <span class="text-xl font-bold">DaisyUI</span>
        </div>
    </div>
</section>

<!-- Características clave -->
<section id="caracteristicas" class="py-16 px-6 bg-base-200 fade-in">
    <h2 class="text-4xl font-bold mb-10 text-center">✨ Características clave</h2>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            Registro completo de productos
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            Gestión dinámica de clientes
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            Control de inventario
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            Registro de ventas y facturación
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            Procesos de reparación
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            Interfaz responsiva y moderna
        </div>
        <div class="card card-hover bg-base-100 p-6 shadow-lg rounded-lg text-center">
            Escalable y seguro
        </div>
    </div>
</section>

<!-- Capturas de pantalla -->
<section class="py-16 px-6 container mx-auto fade-in">
    <h2 class="text-4xl font-bold mb-10 text-center">🖥️ Capturas de pantalla</h2>

    <h3 class="text-2xl font-semibold mb-6" id="inventario">Inventario</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <img src="{{ asset('image/Inventario/1755988447335.png') }}" alt="Index de Productos" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
        <img src="{{ asset('image/Inventario/1755991642212.png') }}" alt="Formulario de Registro" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
        <img src="{{ asset('image/Inventario/1755989418351.png') }}" alt="Editar productos" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
    </div>

    <h3 class="text-2xl font-semibold mb-6" id="ventas">Ventas</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <img src="{{ asset('image/Ventas/1755989998787.png') }}" alt="Index de Ventas" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
        <img src="{{ asset('image/Ventas/1755991964754.png') }}" alt="Registro de ventas" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
        <img src="{{ asset('image/Ventas/1755991567842.png')}}" alt="Factura de ventas" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
        <img src="{{ asset('image/Ventas/1755992036058.png') }}" alt="Editar ventas" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
    </div>

    <h3 class="text-2xl font-semibold mb-6" id="procesos">Procesos</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <img src="{{ asset('image/Procesos/1755989614569.png') }}" alt="Index de Procesos" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
        <img src="{{ asset('image/Procesos/1755989875247.png') }}" alt="Evidencias de procesos" class="rounded-lg shadow-lg hover:scale-105 transition-transform">
    </div>
</section>

<!-- Footer -->
<footer class="footer p-10 bg-base-100 text-base-content mt-12 text-center">
    <div>
        <p class="font-bold text-lg">MR ELECTRONICS</p>
        <p>© 2025 Todos los derechos reservados</p>
    </div>
</footer>

</body>
</html>
