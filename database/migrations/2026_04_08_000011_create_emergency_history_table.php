<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100)->comment('Ej: cambio_estado, asignacion_equipo, foto_subida');
            $table->text('old_value')->nullable()->comment('JSON del estado anterior');
            $table->text('new_value')->nullable()->comment('JSON del nuevo estado');
            $table->string('description', 500);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('emergency_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_history');
    }
};
