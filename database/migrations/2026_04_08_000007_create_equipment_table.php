<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('code', 100)->nullable()->unique()->comment('Código de inventario');
            $table->enum('category', [
                'herramienta',
                'equipo_medico',
                'equipo_rescate',
                'comunicacion',
                'proteccion',
                'otro',
            ]);
            $table->text('description')->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 200)->nullable()->unique();
            $table->enum('status', [
                'disponible',
                'en_uso',
                'mantenimiento',
                'dado_de_baja',
            ])->default('disponible');
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->date('purchase_date')->nullable();
            $table->date('last_maintenance')->nullable();
            $table->date('next_maintenance')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
