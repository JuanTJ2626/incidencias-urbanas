<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SyncService
{
    /**
     * Sincronizar foto (antes o despues) con Hostinger
     */
    public static function sincronizarFotoConHostinger(int $incidenciaId, string $rutaArchivoLocal, string $tipo = 'despues')
    {
        $url = env('HOSTINGER_URL', 'https://reporteurbano.site/public/api/upload_photo.php');
        $apiKey = env('SYNC_API_KEY', 'ReporteUrbano2026_SyncKey_Hostinger_Railway');
        
        $fullPath = storage_path('app/public/' . $rutaArchivoLocal);

        if (!file_exists($fullPath)) {
            Log::error("SyncService: Foto no encontrada: $fullPath");
            return false;
        }

        try {
            $response = Http::timeout(30)
                ->attach('foto', file_get_contents($fullPath), basename($fullPath))
                ->post($url, [
                    'incidencia_id' => $incidenciaId,
                    'api_key'       => $apiKey,
                    'tipo'          => $tipo
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data && isset($data['ok']) && $data['ok']) {
                    Log::info("SyncService: Foto sincronizada con Hostinger para incidencia #$incidenciaId");
                    return true;
                }
            }

            Log::error("SyncService: Error sincronizando foto con Hostinger (HTTP {$response->status()}): " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("SyncService: Excepción al sincronizar con Hostinger: " . $e->getMessage());
            return false;
        }
    }
}
