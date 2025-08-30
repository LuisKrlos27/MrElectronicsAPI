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
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();

            // Relación con clientes
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');

            // Relación con marcas
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade');

            // Relación con modelos
            $table->foreignId('modelo_id')->constrained('modelos')->onDelete('cascade');

            //Relacion con pulgadas
            $table->foreignId('pulgada_id')->constrained('pulgadas')->onDelete('cascade');
            
            // Datos propios de la reparación
            $table->string('falla', 255);
            $table->text('descripcion')->nullable();

            // Estado del proceso
            $table->boolean('estado')->default(true); // TRUE = abierto, FALSE = cerrado

            // Fechas
            $table->timestamp('fecha_inicio')->useCurrent();
            $table->timestamp('fecha_cierre')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};
