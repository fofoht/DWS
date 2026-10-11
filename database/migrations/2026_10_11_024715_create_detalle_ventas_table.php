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
            Schema::create('detalle_ventas', function (Blueprint $table) {
        $table->id();

        $table->foreignId('venta_id')
            ->constrained('ventas')
            ->restrictOnDelete();

        $table->foreignId('producto_id')
            ->constrained('productos')
            ->restrictOnDelete();

        $table->unsignedInteger('cantidad');

        $table->decimal('precio_unitario', 12, 2);

        $table->decimal('costo_unitario', 12, 2);

        $table->decimal('subtotal', 12, 2);

        $table->decimal('costo_total', 12, 2);

        $table->timestamps();

        $table->index(['venta_id', 'producto_id']);
        $table->index('producto_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
