<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$level = \App\Models\Level::with(['userProgress'])->first();
echo $level->toJson() . "\n";
