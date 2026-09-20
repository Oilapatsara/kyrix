<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "========================================\n";
echo "   KYRIX PRODUCT IMAGE DOWNLOADER\n";
echo "========================================\n\n";

$images = app('db')
    ->table('product_images')
    ->orderBy('image_id')
    ->get();

$folder = public_path('images/products');

if (!File::exists($folder)) {
    File::makeDirectory($folder, 0755, true);
}

$total = $images->count();
$success = 0;
$failed = 0;

foreach ($images as $image) {

    echo "Image ID {$image->image_id} | Product {$image->product_id}\n";

    if (empty($image->image_path)) {
        echo "  - ไม่มี URL\n\n";
        $failed++;
        continue;
    }

    $url = trim($image->image_path);

    /*
    |--------------------------------------------------------------------------
    | ถ้าเป็นไฟล์ local อยู่แล้ว ไม่ต้องดาวน์โหลดใหม่
    |--------------------------------------------------------------------------
    */

    if (!str_starts_with($url, 'http://') &&
        !str_starts_with($url, 'https://')) {

        echo "  - เป็นไฟล์ local อยู่แล้ว\n\n";
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | ตั้งชื่อไฟล์
    |--------------------------------------------------------------------------
    */

    $fileName = 'product_' . $image->product_id . '_' . $image->image_id . '.jpg';

    $filePath = $folder . DIRECTORY_SEPARATOR . $fileName;

    /*
    |--------------------------------------------------------------------------
    | ดาวน์โหลด
    |--------------------------------------------------------------------------
    */

    try {

        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0',
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            ])
            ->get($url);

        if (!$response->successful()) {

            echo "  X ดาวน์โหลดไม่ได้ HTTP {$response->status()}\n\n";
            $failed++;
            continue;
        }

        $body = $response->body();

        if (empty($body)) {

            echo "  X ไฟล์รูปว่าง\n\n";
            $failed++;
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | บันทึกไฟล์
        |--------------------------------------------------------------------------
        */

        File::put($filePath, $body);

        /*
        |--------------------------------------------------------------------------
        | เปลี่ยน image_path เป็น path ภายในโปรเจกต์
        |--------------------------------------------------------------------------
        */

        $newPath = 'images/products/' . $fileName;

        app('db')
            ->table('product_images')
            ->where('image_id', $image->image_id)
            ->update([
                'image_path' => $newPath,
            ]);

        echo "  ✓ สำเร็จ\n";
        echo "  -> {$newPath}\n\n";

        $success++;

    } catch (\Throwable $e) {

        echo "  X ERROR: " . $e->getMessage() . "\n\n";
        $failed++;
    }
}

echo "========================================\n";
echo "เสร็จสิ้น\n";
echo "ทั้งหมด : {$total}\n";
echo "สำเร็จ  : {$success}\n";
echo "ไม่สำเร็จ: {$failed}\n";
echo "========================================\n";