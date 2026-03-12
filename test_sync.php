<?php
// test_sync.php - Ejecutar desde el navegador o servidor
// Este archivo sirve para probar la conexión con el servidor de Hostinger

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\SyncService;
use Illuminate\Support\Facades\Log;

echo "--- Iniciando prueba de sincronización con Hostinger ---\n";

// Crear una imagen de prueba temporal (Base64 de un pixel rojo para evitar dependencia de GD)
$testImagePath = storage_path('app/public/test_sync_image.jpg');
$pixelRojo = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==";
file_put_contents($testImagePath, base64_decode($pixelRojo));

echo "1. Imagen de prueba (mini) creada en: $testImagePath\n";

// ID de incidencia de prueba (debe existir en la DB)
$testIncidenciaId = 80; 

echo "2. Intentando sincronizar incidencia #$testIncidenciaId...\n";

// Llamar al servicio directamente
$resultado = SyncService::sincronizarFotoConHostinger($testIncidenciaId, 'test_sync_image.jpg');

if ($resultado) {
    echo "✅ ÉXITO: La foto se sincronizó correctamente.\n";
} else {
    echo "❌ ERROR: Revisa los logs de Laravel (storage/logs/laravel.log) para más detalles.\n";
}

// Limpiar
if (file_exists($testImagePath)) {
    unlink($testImagePath);
}

echo "--- Prueba terminada ---\n";
