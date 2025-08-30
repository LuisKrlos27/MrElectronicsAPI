<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->timestamp('fecha_venta')->useCurrent();
            $table->decimal('total', 12, 2);
            $table->decimal('pago', 12, 2);
            // cambio calculado en Laravel, no directamente en SQL porque algunas versiones de Postgres no soportan GENERATED
            $table->decimal('cambio', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
