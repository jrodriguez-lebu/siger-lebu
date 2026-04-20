<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->comment('Ej: Ambulancia A1');
            $table->string('plate', 20)->unique()->comment('Patente');
            $table->enum('type', [
                'ambulancia',
                'camion_bomberos',
                'camioneta',
                'furgon',
                'moto',
                'helicoptero',
                'bote',
                'otro',
            ]);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('color', 50)->nullable();
            $table->unsignedTinyInteger('capacity')->nullable()->comment('Cantidad de personas');
            $table->enum('status', [
                'disponible',
                'en_servicio',
                'mantenimiento',
                'fuera_de_servicio',
            ])->default('disponible');
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('fuel_type', ['gasolina', 'diesel', 'electrico', 'hibrido'])->nullable();
            $table->unsignedInteger('current_mileage')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('technical_review_expiry')->nullable();
            $table->string('gps_tracking_id', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
