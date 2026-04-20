<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_supplies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('code', 100)->nullable()->unique();
            $table->enum('category', [
                'medicamento',
                'material_curacion',
                'oxigeno',
                'combustible',
                'alimento',
                'ropa',
                'herramienta',
                'otro',
            ]);
            $table->string('unit', 50)->comment('Ej: unidad, litro, kg, caja');
            $table->decimal('stock_current', 10, 2)->default(0);
            $table->decimal('stock_minimum', 10, 2)->default(0)->comment('Umbral de alerta');
            $table->decimal('stock_maximum', 10, 2)->nullable();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location', 200)->nullable()->comment('Ubicación física');
            $table->date('expiry_date')->nullable()->comment('Para medicamentos');
            $table->string('supplier', 200)->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_supplies');
    }
};
