<?php

namespace App\Jobs;

use App\Models\Emergency;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30; // segundos entre reintentos

    public function __construct(
        public readonly Emergency $emergency,
        public readonly string    $phone,
        public readonly string    $recipientName,
        public readonly string    $message,
    ) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $sent = $whatsapp->send($this->phone, $this->message);

        // Registrar en log
        WhatsAppLog::create([
            'emergency_id'   => $this->emergency->id,
            'recipient_name' => $this->recipientName,
            'phone'          => $this->phone,
            'message'        => $this->message,
            'status'         => $sent ? 'enviado' : 'fallido',
            'provider'       => config('services.whatsapp.provider'),
        ]);

        if (! $sent) {
            $this->fail("No se pudo enviar WhatsApp a {$this->phone}");
        }
    }

    public function failed(\Throwable $e): void
    {
        WhatsAppLog::where('emergency_id', $this->emergency->id)
            ->where('phone', $this->phone)
            ->latest()
            ->first()
            ?->update(['status' => 'fallido', 'error' => $e->getMessage()]);
    }

    /**
     * Construye el mensaje de alerta para el líder del equipo.
     */
    public static function buildMessage(Emergency $emergency): string
    {
        $appUrl   = config('services.whatsapp.app_url');
        $priority = strtoupper($emergency->getPriorityLabel());
        $type     = $emergency->getTypeLabel();
        $url      = "{$appUrl}/emergencias/{$emergency->id}";

        return <<<MSG
🚨 *EMERGENCIA ASIGNADA — SIGER Lebu*

📋 Folio: *{$emergency->folio}*
{$type}
⚠️ Prioridad: *{$priority}*
📍 {$emergency->address}
👥 Afectados: {$emergency->affected_people}

{$emergency->title}

Accede al sistema para ver el detalle completo:
{$url}

_Municipalidad de Lebu — Unidad de Gestión de Riesgos_
MSG;
    }
}
