<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
App\Models\Statistic::create(['value' => 0, 'label' => 'Item Baru', 'order' => 4]);
echo "Created successfully\n";
