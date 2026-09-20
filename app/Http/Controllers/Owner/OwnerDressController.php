<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\RentalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OwnerDressController extends Controller
{
    /**
     * แสดงรายการชุดทั้งหมด พร้อมระบบค้นหา ตัวกรอง และการเรียงลำดับ
     */
    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'images',
            'mainImage'
        ]);

        // =====================================================
        // 1. ระบบค้นหา
        // =====================================================
        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%");
            });
        }

        // =====================================================
        // 2. ตัวกรองหมวดหมู่
        // =====================================================
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // =====================================================
        // 3. ตัวกรองสถานะ
        //
        // หน้า Blade ใช้:
        // active     = พร้อมให้เช่า
        // available  = พร้อมให้เช่า
        // rented     = กำลังเช่า
        // inactive   = ปิดใช้งาน
        // =====================================================
        if ($request->filled('status')) {

            switch ($request->status) {

                case 'active':
                case 'available':
                    $query->whereIn('status', ['active', 'available']);
                    break;

                case 'rented':
                    $query->whereIn('status', ['rented', 'busy']);
                    break;

                case 'inactive':
                    $query->where('status', 'inactive');
                    break;

                case 'maintenance':
                    $query->where('status', 'maintenance');
                    break;

                default:
                    $query->where('status', $request->status);
                    break;
            }
        }

        // =====================================================
        // 4. ระบบเรียงลำดับ
        //
        // latest      = ล่าสุด
        // name_asc    = ชื่อ A-Z
        // price_low   = ราคา ต่ำ → สูง
        // price_high  = ราคา สูง → ต่ำ
        // =====================================================
        $sort = $request->input('sort', 'latest');

        switch ($sort) {

            case 'name_asc':
                $query->orderBy('product_name', 'asc')
                    ->orderBy('product_id', 'desc');
                break;

            case 'price_low':
                $query->orderBy('rental_price', 'asc')
                    ->orderBy('product_id', 'desc');
                break;

            case 'price_high':
                $query->orderBy('rental_price', 'desc')
                    ->orderBy('product_id', 'desc');
                break;

            case 'latest':
            default:
                $query->orderBy('product_id', 'desc');
                break;
        }

        // =====================================================
        // 5. Pagination
        // รักษา category / status / sort / search ใน URL
        // =====================================================
        $products = $query
            ->paginate(12)
            ->withQueryString();

        // =====================================================
        // 6. ดึงหมวดหมู่ทั้งหมด
        // =====================================================
        $categories = Category::all();

        // =====================================================
        // 7. สถิติสรุป
        //
        // ใช้ชื่อ $summary ให้ตรงกับ index.blade.php
        // =====================================================
        $summary = [
            'total' => Product::count(),

            'available' => Product::whereIn('status', [
                'available',
                'active'
            ])->count(),

            'rented' => Product::whereIn('status', [
                'rented',
                'busy'
            ])->count(),

            'inactive' => Product::where('status', 'inactive')->count(),

            'maintenance' => Product::where('status', 'maintenance')->count(),
        ];

        // =====================================================
        // 8. ส่งข้อมูลไปยัง Blade
        // =====================================================
        return view(
            'owner.dresses.index',
            compact(
                'products',
                'categories',
                'summary'
            )
        );
    }

    /**
     * แสดงหน้าฟอร์มเพิ่มชุดใหม่
     */
    public function create()
    {
        $categories = Category::all();

        // รันรหัสชุดอัตโนมัติ
        // ตัวอย่าง: KY-DRS-0001
        $nextId = (Product::max('product_id') ?? 0) + 1;

        $defaultCode = 'KY-DRS-' . str_pad(
            $nextId,
            4,
            '0',
            STR_PAD_LEFT
        );

        return view(
            'owner.dresses.create',
            compact(
                'categories',
                'defaultCode'
            )
        );
    }

    /**
     * บันทึกข้อมูลชุดใหม่ลงฐานข้อมูล
     */
    public function store(Request $request)
    {
        $categoryKey = (new Category)->getKeyName();

        $data = $request->validate([
            'category_id' => "required|exists:categories,{$categoryKey}",
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'product_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'available_sizes' => 'nullable|string|max:100',
            'available_colors' => 'nullable|string|max:150',
            'bust' => 'nullable|string|max:50',
            'waist' => 'nullable|string|max:50',
            'hips' => 'nullable|string|max:50',
            'length' => 'nullable|string|max:50',
            'rental_price' => 'required|numeric|min:0',
            'deposit' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,rented,maintenance,inactive',
            'is_featured' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image_url' => 'nullable|string|max:500',
            'color_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ], [
            'product_code.unique' => 'รหัสชุดนี้มีอยู่ในระบบแล้ว',
            'product_name.required' => 'กรุณากรอกชื่อชุด',
            'category_id.required' => 'กรุณาเลือกหมวดหมู่ชุด',
            'rental_price.required' => 'กรุณาระบุราคาเช่า',
            'deposit.required' => 'กรุณาระบุเงินมัดจำ',
        ]);

        $data['is_featured'] = $request->has('is_featured');
        $data['is_popular'] = $request->has('is_popular');
        $data['is_new'] = $request->has('is_new');

        $colorNames = [];

        if ($request->has('sizes')) {

            $sizes = array_filter(
                (array) $request->sizes
            );

            $data['available_sizes'] = implode(', ', $sizes);
            $data['size'] = $sizes[0] ?? 'M';
        }

        if ($request->has('color_names')) {

            $colorNames = array_values(
                array_filter(
                    array_map(
                        'trim',
                        (array) $request->color_names
                    )
                )
            );

            if (!empty($colorNames)) {

                $colorString = implode(
                    ', ',
                    $colorNames
                );

                $data['available_colors'] = $colorString;

                $data['color'] = Str::limit(
                    $colorString,
                    50,
                    ''
                );
            }
        }

        $product = Product::create($data);

        // =====================================================
        // จัดการอัปโหลดรูปภาพแบบ Per-Color
        // =====================================================

        $colorImages = $request->file('color_images') ?? [];

        $isFirstImage = true;

        foreach ($colorNames as $i => $colorName) {

            if (
                !empty($colorImages[$i]) &&
                $colorImages[$i]->isValid()
            ) {

                $path = $colorImages[$i]->store(
                    'products',
                    'public'
                );

                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $path,
                    'is_main' => $isFirstImage,
                    'color_name' => $colorName ?: null,
                ]);

                $isFirstImage = false;
            }
        }

        // =====================================================
        // Fallback รูปเดี่ยว
        // =====================================================

        if ($isFirstImage) {

            $uploadedFile =
                $request->file('image') ??
                $request->file('image_file');

            if ($uploadedFile) {

                $path = $uploadedFile->store(
                    'products',
                    'public'
                );

                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $path,
                    'is_main' => true,
                ]);

            } elseif (!empty($data['image_url'])) {

                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $data['image_url'],
                    'is_main' => true,
                ]);
            }
        }

        return redirect()
            ->route('owner.dresses.index')
            ->with(
                'success',
                'เพิ่มชุดเรียบร้อยแล้ว: ' .
                $product->product_name
            );
    }

    /**
     * แสดงหน้าแก้ไขข้อมูลชุด
     */
    public function edit($id)
    {
        $dress = Product::with('images')
            ->findOrFail($id);

        $categories = Category::all();

        return view(
            'owner.dresses.edit',
            compact(
                'dress',
                'categories'
            )
        );
    }

    /**
     * อัปเดตข้อมูลชุด
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $categoryKey = (new Category)->getKeyName();

        $data = $request->validate([
            'category_id' => "required|exists:categories,{$categoryKey}",
            'product_code' =>
                'required|string|max:50|unique:products,product_code,' .
                $product->product_id .
                ',product_id',
            'product_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'available_sizes' => 'nullable|string|max:100',
            'available_colors' => 'nullable|string|max:150',
            'bust' => 'nullable|string|max:50',
            'waist' => 'nullable|string|max:50',
            'hips' => 'nullable|string|max:50',
            'length' => 'nullable|string|max:50',
            'rental_price' => 'required|numeric|min:0',
            'deposit' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,rented,maintenance,inactive',
            'is_featured' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image_url' => 'nullable|string|max:500',
            'color_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $data['is_featured'] = $request->has('is_featured');
        $data['is_popular'] = $request->has('is_popular');
        $data['is_new'] = $request->has('is_new');

        $colorNames = [];

        if ($request->has('sizes')) {

            $sizes = array_filter(
                (array) $request->sizes
            );

            $data['available_sizes'] = implode(
                ', ',
                $sizes
            );

            $data['size'] = $sizes[0] ?? 'M';
        }

        if ($request->has('color_names')) {

            $colorNames = array_values(
                array_filter(
                    array_map(
                        'trim',
                        (array) $request->color_names
                    )
                )
            );

            if (!empty($colorNames)) {

                $colorString = implode(
                    ', ',
                    $colorNames
                );

                $data['available_colors'] = $colorString;

                $data['color'] = Str::limit(
                    $colorString,
                    50,
                    ''
                );
            }
        }

        $product->update($data);

        // =====================================================
        // ซิงค์ชื่อสีของรูปเดิม
        // =====================================================

        $existingColorNames =
            (array) $request->input(
                'existing_color_names',
                []
            );

        foreach (
            $colorNames as $i => $newColorName
        ) {

            $oldColorName =
                $existingColorNames[$i] ?? null;

            if (
                $oldColorName &&
                $oldColorName !== $newColorName
            ) {

                ProductImage::where(
                    'product_id',
                    $product->product_id
                )
                    ->where(
                        'color_name',
                        $oldColorName
                    )
                    ->update([
                        'color_name' => $newColorName
                    ]);
            }
        }

        // =====================================================
        // จัดการอัปโหลดรูปภาพแบบ Per-Color
        // =====================================================

        $colorImages =
            $request->file('color_images') ?? [];

        $hasNewColorImage = false;

        foreach (
            $colorNames as $i => $colorName
        ) {

            if (
                !empty($colorImages[$i]) &&
                $colorImages[$i]->isValid()
            ) {

                $existingForColor =
                    ProductImage::where(
                        'product_id',
                        $product->product_id
                    )
                        ->where(
                            'color_name',
                            $colorName
                        )
                        ->first();

                if (
                    $existingForColor &&
                    Storage::disk('public')->exists(
                        $existingForColor->image_path
                    )
                ) {

                    Storage::disk('public')->delete(
                        $existingForColor->image_path
                    );

                    $existingForColor->delete();
                }

                $path =
                    $colorImages[$i]->store(
                        'products',
                        'public'
                    );

                $isFirst =
                    !$hasNewColorImage &&
                    $i === 0;

                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $path,
                    'is_main' => $isFirst,
                    'color_name' => $colorName ?: null,
                ]);

                if ($isFirst) {

                    ProductImage::where(
                        'product_id',
                        $product->product_id
                    )
                        ->where('is_main', true)
                        ->whereNull('color_name')
                        ->update([
                            'is_main' => false
                        ]);
                }

                $hasNewColorImage = true;
            }
        }

        // =====================================================
        // Fallback รูปเดี่ยว
        // =====================================================

        if (!$hasNewColorImage) {

            $uploadedFile =
                $request->file('image') ??
                $request->file('image_file');

            if ($uploadedFile) {

                $path = $uploadedFile->store(
                    'products',
                    'public'
                );

                ProductImage::where(
                    'product_id',
                    $product->product_id
                )->update([
                    'is_main' => false
                ]);

                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $path,
                    'is_main' => true,
                ]);

            } elseif (!empty($request->image_url)) {

                ProductImage::where(
                    'product_id',
                    $product->product_id
                )->update([
                    'is_main' => false
                ]);

                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $request->image_url,
                    'is_main' => true,
                ]);
            }
        }

        return redirect()
            ->route('owner.dresses.index')
            ->with(
                'success',
                'บันทึกการแก้ไขชุดเรียบร้อยแล้ว'
            );
    }

    /**
     * ลบชุดออกจากระบบ
     */
    public function destroy($id)
    {
        $product = Product::with('images')
            ->findOrFail($id);

        $productName = $product->product_name;

        // =====================================================
        // ตรวจสอบรายการเช่าที่ยังดำเนินการอยู่
        // =====================================================

        $hasActiveRentals = RentalDetail::where(
            'product_id',
            $product->product_id
        )
            ->whereHas('rental', function ($q) {
                $q->whereIn(
                    'status',
                    [
                        'pending',
                        'confirmed',
                        'renting'
                    ]
                );
            })
            ->exists();

        if ($hasActiveRentals) {

            return back()->with(
                'error',
                'ไม่สามารถลบชุดนี้ได้ เนื่องจากมีรายการเช่าที่กำลังดำเนินการอยู่ กรุณาเปลี่ยนสถานะเป็น "ปิดใช้งาน" แทน'
            );
        }

        // =====================================================
        // ลบไฟล์รูปภาพจริง
        // =====================================================

        foreach ($product->images as $image) {

            if (
                $image->image_path &&
                Storage::disk('public')->exists(
                    $image->image_path
                )
            ) {

                Storage::disk('public')->delete(
                    $image->image_path
                );
            }
        }

        // =====================================================
        // ลบข้อมูลรูปภาพและสินค้า
        // =====================================================

        $product->images()->delete();
        $product->delete();

        return redirect()
            ->route('owner.dresses.index')
            ->with(
                'success',
                'ลบชุด ' . $productName . ' เรียบร้อยแล้ว'
            );
    }

    /**
     * สลับสถานะพร้อมเช่า / ปิดใช้งาน
     */
    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);

        $newStatus =
            $product->status === 'available'
                ? 'inactive'
                : 'available';

        $product->update([
            'status' => $newStatus
        ]);

        $statusText =
            $newStatus === 'available'
                ? 'พร้อมให้เช่า'
                : 'ปิดใช้งาน';

        return back()->with(
            'success',
            "เปลี่ยนสถานะชุด {$product->product_name} เป็น \"{$statusText}\" แล้ว"
        );
    }

    /**
     * บันทึกหมวดหมู่ใหม่
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' =>
                'required|string|max:100|unique:categories,category_name',

            'description' =>
                'nullable|string|max:500',
        ], [
            'category_name.required' =>
                'กรุณากรอกชื่อหมวดหมู่',

            'category_name.max' =>
                'ชื่อหมวดหมู่ต้องไม่เกิน 100 ตัวอักษร',

            'category_name.unique' =>
                'มีชื่อหมวดหมู่นี้อยู่ในระบบแล้ว',
        ]);

        Category::create([
            'category_name' =>
                trim($validated['category_name']),

            'description' =>
                !empty($validated['description'])
                    ? trim($validated['description'])
                    : null,

            'status' => 'active',
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'เพิ่มหมวดหมู่ใหม่ "' .
                trim($validated['category_name']) .
                '" เรียบร้อยแล้ว'
            );
    }

    /**
     * อัปเดตข้อมูลหมวดหมู่
     */
    public function updateCategory(
        Request $request,
        $id
    ) {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'category_name' =>
                'required|string|max:100|unique:categories,category_name,' .
                $category->category_id .
                ',category_id',

            'description' =>
                'nullable|string|max:500',
        ], [
            'category_name.required' =>
                'กรุณากรอกชื่อหมวดหมู่',

            'category_name.max' =>
                'ชื่อหมวดหมู่ต้องไม่เกิน 100 ตัวอักษร',

            'category_name.unique' =>
                'มีชื่อหมวดหมู่นี้อยู่ในระบบแล้ว',
        ]);

        $category->update([
            'category_name' =>
                trim($validated['category_name']),

            'description' =>
                !empty($validated['description'])
                    ? trim($validated['description'])
                    : null,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'แก้ไขชื่อหมวดหมู่เป็น "' .
                trim($validated['category_name']) .
                '" เรียบร้อยแล้ว'
            );
    }

    /**
     * ลบหมวดหมู่
     */
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'ไม่สามารถลบหมวดหมู่ "' .
                    $category->category_name .
                    '" ได้ เนื่องจากยังมีชุดผูกอยู่'
                );
        }

        $categoryName = $category->category_name;

        $category->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'ลบหมวดหมู่ "' .
                $categoryName .
                '" เรียบร้อยแล้ว'
            );
    }
}