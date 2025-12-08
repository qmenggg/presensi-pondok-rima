<?php

use App\Models\Santri;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$santri = Santri::with('user')->first();

if ($santri) {
    echo "Santri ID: " . $santri->id . "\n";
    echo "Nama (via accessor): " . $santri->nama . "\n";
    
    if ($santri->nama && $santri->nama !== '-') {
        echo "SUCCESS: Name accessor is working.\n";
    } else {
        echo "WARNING: Name accessor returned empty or default.\n";
    }
} else {
    echo "No santri found to test.\n";
}
