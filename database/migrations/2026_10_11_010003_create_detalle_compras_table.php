<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('compra_id')
                ->constrained('compras')
                ->restrictOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->unsignedInteger('cantidad');
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();

            $table->index(['compra_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};


