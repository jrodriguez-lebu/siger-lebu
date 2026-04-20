<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('type', ['rescate', 'tecnico', 'bombero', 'medico', 'apoyo', 'otro'])->default('otro');
            $table->string('color', 7)->nullable()->comment('Hex color para marcadores en mapa');
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->foreignId('leader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
