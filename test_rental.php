<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = App\Models\Rental::find(6);
echo "Rental ID: " . $r->rental_id . PHP_EOL;
echo "Rental Code: " . $r->rental_code . PHP_EOL;
echo "Formatted Code: " . $r->formatted_code . PHP_EOL;
echo "Total Amount (100%): " . $r->total_amount . PHP_EOL;
echo "Deposit Amount: " . $r->deposit_amount . PHP_EOL;
echo "Service Fee: " . $r->service_fee . PHP_EOL;
echo "Grand Total: " . $r->grand_total . PHP_EOL;
echo "Status: " . $r->status . PHP_EOL;
echo "Status Label: " . $r->status_label . PHP_EOL;
echo "Step Index: " . $r->step_index . PHP_EOL;
