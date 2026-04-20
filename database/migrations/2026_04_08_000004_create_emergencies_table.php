<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergencies', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique()->comment('Ej: EMG-2026-00001');
            $table->enum('type', [
                'incendio',
                'accidente_transito',
                'rescate',
                'inundacion',
                'emergencia_medica',
                'derrumbe',
                'otro',
            ]);
            $table->enum('priority', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->enum('status', [
                'ingresada',
                'en_proceso',
                'atendida',
                'cerrada',
                'cancelada',
            ])->default('ingresada');

            $table->string('title', 255);
            $table->text('description');
            $table->string('address', 500);
            $table->string('sector', 200)->nullable();
            $table->string('commune', 100)->nullable()->default('Lebu');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Datos del reportante (portal público)
            $table->string('reported_by_name', 200)->nullable();
            $table->string('reported_by_phone', 50)->nullable();
            $table->integer('affected_people')->default(0)->comment('Cantidad de personas afectadas');

            // Asignaciones
            $table->foreignId('assigned_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('notes')->nullable()->comment('Notas internas del coordinador');

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('type');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergencies');
    }
};
