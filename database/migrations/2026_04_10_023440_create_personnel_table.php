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
        Schema::create('personnel', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('rut', 20)->nullable()->unique()->comment('RUT chileno Ej: 12.345.678-9');
            $table->enum('specialty', [
                'bombero', 'paramedico', 'enfermero', 'medico',
                'rescatista', 'logistica', 'comunicaciones',
                'carabinero', 'voluntario', 'otro',
            ])->default('otro');
            $table->string('position', 100)->nullable()->comment('Cargo o función dentro del equipo');
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->date('joined_date')->nullable();
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personnel');
    }
};
