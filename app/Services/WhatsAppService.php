<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envía un mensaje de WhatsApp al número indicado.
     * El número debe incluir código de país sin '+': ej. 56912345678
     */
    public function send(string $phone, string $message): bool
    {
        if (! config('services.whatsapp.enabled')) {
            Log::info("WhatsApp deshabilitado — mensaje no enviado a {$phone}");
            return false;
        }

        // Normalizar número: quitar espacios, guiones, asegurar que empiece con +
        $phone = $this->normalizePhone($phone);

        $provider = config('services.whatsapp.provider', 'twilio');

        try {
            $sent = match ($provider) {
                'twilio' => $this->sendViaTwilio($phone, $message),
                'meta'   => $this->sendViaMeta($phone, $message),
                default  => throw new \InvalidArgumentException("Proveedor WhatsApp inválido: {$provider}"),
            };

            Log::info("WhatsApp [{$provider}] → {$phone}: " . ($sent ? 'OK' : 'FALLÓ'));
            return $sent;

        } catch (\Throwable $e) {
            Log::error("WhatsApp [{$provider}] error: " . $e->getMessage(), [
                'phone' => $phone,
            ]);
            return false;
        }
    }

    // ── Proveedores ───────────────────────────────────────────

    private function sendViaTwilio(string $phone, string $message): bool
    {
        $sid   = config('services.whatsapp.twilio_sid');
        $token = config('services.whatsapp.twilio_token');
        $from  = config('services.whatsapp.twilio_from');

        if (! $sid || ! $token) {
            throw new \RuntimeException('Credenciales de Twilio no configuradas.');
        }

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->timeout(15)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => "whatsapp:{$from}",
                'To'   => "whatsapp:{$phone}",
                'Body' => $message,
            ]);

        if (! $response->successful()) {
            Log::warning('Twilio error', ['status' => $response->status(), 'body' => $response->body()]);
        }

        return $response->successful();
    }

    private function sendViaMeta(string $phone, string $message): bool
    {
        $token   = config('services.whatsapp.meta_token');
        $phoneId = config('services.whatsapp.meta_phone_id');

        if (! $token || ! $phoneId) {
            throw new \RuntimeException('Credenciales de Meta WhatsApp no configuradas.');
        }

        // Quitar el '+' para Meta API
        $to = ltrim($phone, '+');

        $response = Http::withToken($token)
            ->timeout(15)
            ->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $to,
                'type'              => 'text',
                'text'              => ['body' => $message],
            ]);

        if (! $response->successful()) {
            Log::warning('Meta WhatsApp error', ['status' => $response->status(), 'body' => $response->body()]);
        }

        return $response->successful();
    }

    // ── Helpers ───────────────────────────────────────────────

    private function normalizePhone(string $phone): string
    {
        // Quitar espacios, guiones, paréntesis
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // Si empieza con 0 asumir Chile (+56)
        if (str_starts_with($phone, '0')) {
            $phone = '+56' . substr($phone, 1);
        }

        // Si es un número chileno sin código de país (9 dígitos empezando en 9)
        if (preg_match('/^9\d{8}$/', $phone)) {
            $phone = '+56' . $phone;
        }

        // Asegurar que tenga '+'
        if (! str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}
