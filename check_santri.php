<?php

define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$santri = App\Models\Santri::with('user')->first();

echo "\n--- RESULT ---\n";
if ($santri) {
    echo "ID: " . $santri->id . "\n";
    echo "Nama: " . ($santri->nama ?? 'NULL') . "\n";
} else {
    echo "No Santri found.\n";
}
echo "--- END ---\n";
