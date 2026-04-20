<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siger_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('channel', ['whatsapp', 'email', 'sms', 'sistema'])->default('sistema');
            $table->string('type', 100)->comment('Ej: nueva_emergencia, cambio_estado');
            $table->text('message');
            $table->enum('status', ['pendiente', 'enviado', 'fallido', 'leido'])->default('pendiente');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable()->comment('Datos extra del canal (ID mensaje WhatsApp, etc.)');
            $table->timestamps();

            $table->index('status');
            $table->index('channel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siger_notifications');
    }
};
