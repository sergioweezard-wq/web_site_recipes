<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$q = '%український%';
$res = \App\Models\Recipe::where('title', 'ilike', $q)->count();
echo "Found ILIKE lower query on Upper DB: " . $res . "\n";
