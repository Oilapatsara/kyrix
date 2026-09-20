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
     * ตรวจว่าเป็น URL รูปออนไลน์ที่ใช้ร่วมกับเครื่องอื่นได้หรือไม่
     */
    private function isExternalUrl(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (!$host) {
            return false;
        }

        /*
         * ไม่ให้บันทึกลิงก์ที่ชี้กลับมาที่เครื่องของผู้ใช้เอง
         */
        $localHosts = [
            'localhost',
            '127.0.0.1',
            '::1',
        ];

        if (in_array(strtolower($host), $localHosts, true)) {
            return false;
        }

        return true;
    }

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
                $q->where(
                    'product_name',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'product_code',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'color',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        // =====================================================
        // 2. ตัวกรองหมวดหมู่
        // =====================================================
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        // =====================================================
        // 3. ตัวกรองสถานะ
        // =====================================================
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                case 'available':
                    $query->whereIn('status', [
                        'active',
                        'available'
                    ]);
                    break;

                case 'rented':
                    $query->whereIn('status', [
                        'rented',
                        'busy'
                    ]);
                    break;

                case 'inactive':
                    $query->where(
                        'status',
                        'inactive'
                    );
                    break;

                case 'maintenance':
                    $query->where(
                        'status',
                        'maintenance'
                    );
                    break;

                default:
                    $query->where(
                        'status',
                        $request->status
                    );
                    break;
            }
        }

        // =====================================================
        // 4. ระบบเรียงลำดับ
        // =====================================================
        $sort = $request->input(
            'sort',
            'latest'
        );

        switch ($sort) {
            case 'name_asc':
                $query->orderBy(
                    'product_name',
                    'asc'
                )
                    ->orderBy(
                        'product_id',
                        'desc'
                    );
                break;

            case 'price_low':
                $query->orderBy(
                    'rental_price',
                    'asc'
                )
                    ->orderBy(
                        'product_id',
                        'desc'
                    );
                break;

            case 'price_high':
                $query->orderBy(
                    'rental_price',
                    'desc'
                )
                    ->orderBy(
                        'product_id',
                        'desc'
                    );
                break;

            case 'latest':
            default:
                $query->orderBy(
                    'product_id',
                    'desc'
                );
                break;
        }

        // =====================================================
        // 5. Pagination
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
        // =====================================================
        $summary = [
            'total' => Product::count(),

            'available' => Product::whereIn(
                'status',
                [
                    'available',
                    'active'
                ]
            )->count(),

            'rented' => Product::whereIn(
                'status',
                [
                    'rented',
                    'busy'
                ]
            )->count(),

            'inactive' => Product::where(
                'status',
                'inactive'
            )->count(),

            'maintenance' => Product::where(
                'status',
                'maintenance'
            )->count(),
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
        $nextId =
            (Product::max('product_id') ?? 0) + 1;

        $defaultCode =
            'KY-DRS-' .
            str_pad(
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
        $categoryKey =
            (new Category)->getKeyName();

        $data = $request->validate(
            [
                'category_id' =>
                    "required|exists:categories,{$categoryKey}",

                'product_code' =>
                    'required|string|max:50|unique:products,product_code',

                'product_name' =>
                    'required|string|max:150',

                'description' =>
                    'nullable|string',

                'size' =>
                    'nullable|string|max:20',

                'color' =>
                    'nullable|string|max:50',

                'available_sizes' =>
                    'nullable|string|max:100',

                'available_colors' =>
                    'nullable|string|max:150',

                'bust' =>
                    'nullable|string|max:50',

                'waist' =>
                    'nullable|string|max:50',

                'hips' =>
                    'nullable|string|max:50',

                'length' =>
                    'nullable|string|max:50',

                'rental_price' =>
                    'required|numeric|min:0',

                'deposit' =>
                    'required|numeric|min:0',

                'stock' =>
                    'required|integer|min:0',

                'status' =>
                    'required|in:available,rented,maintenance,inactive',

                'is_featured' =>
                    'nullable|boolean',

                'is_popular' =>
                    'nullable|boolean',

                'is_new' =>
                    'nullable|boolean',

                /*
                 * รูปหลักต้องเป็น URL ออนไลน์
                 */
                'image_url' =>
                    'nullable|url|max:500',

                /*
                 * URL รูปแยกตามสี
                 */
                'color_image_urls.*' =>
                    'nullable|url|max:500',
            ],
            [
                'product_code.unique' =>
                    'รหัสชุดนี้มีอยู่ในระบบแล้ว',

                'product_name.required' =>
                    'กรุณากรอกชื่อชุด',

                'category_id.required' =>
                    'กรุณาเลือกหมวดหมู่ชุด',

                'rental_price.required' =>
                    'กรุณาระบุราคาเช่า',

                'deposit.required' =>
                    'กรุณาระบุเงินมัดจำ',

                'image_url.url' =>
                    'URL รูปภาพหลักไม่ถูกต้อง',

                'color_image_urls.*.url' =>
                    'URL รูปภาพของสีไม่ถูกต้อง',
            ]
        );

        // =====================================================
        // 1. Checkbox
        // =====================================================
        $data['is_featured'] =
            $request->has('is_featured');

        $data['is_popular'] =
            $request->has('is_popular');

        $data['is_new'] =
            $request->has('is_new');

        // =====================================================
        // 2. จัดการไซซ์
        // =====================================================
        $colorNames = [];

        if ($request->has('sizes')) {
            $sizes = array_filter(
                (array) $request->sizes
            );

            $data['available_sizes'] =
                implode(', ', $sizes);

            $data['size'] =
                $sizes[0] ?? 'M';
        }

        // =====================================================
        // 3. จัดการสี
        // =====================================================
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
                $colorString =
                    implode(
                        ', ',
                        $colorNames
                    );

                $data['available_colors'] =
                    $colorString;

                $data['color'] =
                    Str::limit(
                        $colorString,
                        50,
                        ''
                    );
            }
        }

        /*
         * image_url ไม่ใช่คอลัมน์ของ products
         * จึงเอาออกก่อนสร้าง Product
         */
        $mainImageUrl =
            trim(
                (string) (
                    $request->input(
                        'image_url'
                    ) ?? ''
                )
            );

        unset($data['image_url']);

        // =====================================================
        // 4. สร้างข้อมูลชุด
        // =====================================================
        $product =
            Product::create($data);

        // =====================================================
        // 5. บันทึก URL รูปหลัก
        // =====================================================
        $hasMainImage = false;

        if (
            $this->isExternalUrl(
                $mainImageUrl
            )
        ) {
            ProductImage::create([
                'product_id' =>
                    $product->product_id,

                'image_path' =>
                    $mainImageUrl,

                'is_main' =>
                    true,
            ]);

            $hasMainImage = true;
        }

        // =====================================================
        // 6. บันทึก URL รูปแยกตามสี
        // =====================================================
        $colorImageUrls =
            $request->input(
                'color_image_urls',
                []
            );

        foreach (
            $colorNames as $i => $colorName
        ) {
            $colorUrl =
                trim(
                    (string) (
                        $colorImageUrls[$i] ??
                        ''
                    )
                );

            if (
                !$this->isExternalUrl(
                    $colorUrl
                )
            ) {
                continue;
            }

            /*
             * ถ้ายังไม่มีรูปหลัก
             * รูปสีแรกจะเป็นรูปหลัก
             */
            $makeMain =
                !$hasMainImage;

            ProductImage::create([
                'product_id' =>
                    $product->product_id,

                'image_path' =>
                    $colorUrl,

                'is_main' =>
                    $makeMain,

                'color_name' =>
                    $colorName ?: null,
            ]);

            if ($makeMain) {
                $hasMainImage = true;
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
        $dress =
            Product::with('images')
                ->findOrFail($id);

        $categories =
            Category::all();

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
    public function update(
        Request $request,
        $id
    ) {
        $product =
            Product::findOrFail($id);

        $categoryKey =
            (new Category)->getKeyName();

        $data = $request->validate(
            [
                'category_id' =>
                    "required|exists:categories,{$categoryKey}",

                'product_code' =>
                    'required|string|max:50|unique:products,product_code,' .
                    $product->product_id .
                    ',product_id',

                'product_name' =>
                    'required|string|max:150',

                'description' =>
                    'nullable|string',

                'size' =>
                    'nullable|string|max:20',

                'color' =>
                    'nullable|string|max:50',

                'available_sizes' =>
                    'nullable|string|max:100',

                'available_colors' =>
                    'nullable|string|max:150',

                'bust' =>
                    'nullable|string|max:50',

                'waist' =>
                    'nullable|string|max:50',

                'hips' =>
                    'nullable|string|max:50',

                'length' =>
                    'nullable|string|max:50',

                'rental_price' =>
                    'required|numeric|min:0',

                'deposit' =>
                    'required|numeric|min:0',

                'stock' =>
                    'required|integer|min:0',

                'status' =>
                    'required|in:available,rented,maintenance,inactive',

                'is_featured' =>
                    'nullable|boolean',

                'is_popular' =>
                    'nullable|boolean',

                'is_new' =>
                    'nullable|boolean',

                'image_url' =>
                    'nullable|url|max:500',

                'color_image_urls.*' =>
                    'nullable|url|max:500',
            ],
            [
                'product_code.unique' =>
                    'รหัสชุดนี้มีอยู่ในระบบแล้ว',

                'product_name.required' =>
                    'กรุณากรอกชื่อชุด',

                'category_id.required' =>
                    'กรุณาเลือกหมวดหมู่ชุด',

                'rental_price.required' =>
                    'กรุณาระบุราคาเช่า',

                'deposit.required' =>
                    'กรุณาระบุเงินมัดจำ',

                'image_url.url' =>
                    'URL รูปภาพหลักไม่ถูกต้อง',

                'color_image_urls.*.url' =>
                    'URL รูปภาพของสีไม่ถูกต้อง',
            ]
        );

        // =====================================================
        // 1. Checkbox
        // =====================================================
        $data['is_featured'] =
            $request->has('is_featured');

        $data['is_popular'] =
            $request->has('is_popular');

        $data['is_new'] =
            $request->has('is_new');

        // =====================================================
        // 2. จัดการไซซ์
        // =====================================================
        $colorNames = [];

        if ($request->has('sizes')) {
            $sizes = array_filter(
                (array) $request->sizes
            );

            $data['available_sizes'] =
                implode(
                    ', ',
                    $sizes
                );

            $data['size'] =
                $sizes[0] ?? 'M';
        }

        // =====================================================
        // 3. จัดการสี
        // =====================================================
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
                $colorString =
                    implode(
                        ', ',
                        $colorNames
                    );

                $data['available_colors'] =
                    $colorString;

                $data['color'] =
                    Str::limit(
                        $colorString,
                        50,
                        ''
                    );
            }
        }

        /*
         * เก็บ URL รูปหลักไว้ก่อน
         */
        $mainImageUrl =
            trim(
                (string) (
                    $request->input(
                        'image_url'
                    ) ?? ''
                )
            );

        /*
         * image_url ไม่ใช่คอลัมน์ของ products
         */
        unset($data['image_url']);

        // =====================================================
        // 4. อัปเดตข้อมูล Product
        // =====================================================
        $product->update($data);

        // =====================================================
        // 5. ดึงชื่อสีเดิมจากฟอร์ม
        // =====================================================
        $existingColorNames =
            (array) $request->input(
                'existing_color_names',
                []
            );

        // =====================================================
        // 6. เปลี่ยนชื่อสีเดิมให้ตรงกับสีใหม่
        // =====================================================
        foreach (
            $colorNames as $i => $newColorName
        ) {
            $oldColorName =
                trim(
                    (string) (
                        $existingColorNames[$i] ??
                        ''
                    )
                );

            if (
                $oldColorName !== '' &&
                $newColorName !== '' &&
                $oldColorName !==
                $newColorName
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
                        'color_name' =>
                            $newColorName,
                    ]);
            }
        }

        // =====================================================
        // 7. บันทึก/อัปเดต URL รูปแยกตามสี
        // =====================================================
        $colorImageUrls =
            $request->input(
                'color_image_urls',
                []
            );

        foreach (
            $colorNames as $i => $colorName
        ) {
            $colorName =
                trim($colorName);

            if ($colorName === '') {
                continue;
            }

            $colorUrl =
                trim(
                    (string) (
                        $colorImageUrls[$i] ??
                        ''
                    )
                );

            /*
             * ถ้าไม่มี URL หรือเป็น URL ที่ชี้เครื่องตัวเอง
             * ไม่ต้องเปลี่ยนรูปเดิม
             */
            if (
                !$this->isExternalUrl(
                    $colorUrl
                )
            ) {
                continue;
            }

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

            if ($existingForColor) {
                $existingForColor->update([
                    'image_path' =>
                        $colorUrl,
                ]);
            } else {
                ProductImage::create([
                    'product_id' =>
                        $product->product_id,

                    'image_path' =>
                        $colorUrl,

                    'is_main' =>
                        false,

                    'color_name' =>
                        $colorName,
                ]);
            }
        }

        // =====================================================
        // 8. อัปเดต URL รูปหลัก
        // =====================================================
        if (
            $this->isExternalUrl(
                $mainImageUrl
            )
        ) {
            $mainImage =
                ProductImage::where(
                    'product_id',
                    $product->product_id
                )
                    ->where(
                        'is_main',
                        true
                    )
                    ->first();

            if ($mainImage) {
                $mainImage->update([
                    'image_path' =>
                        $mainImageUrl,
                ]);
            } else {
                ProductImage::create([
                    'product_id' =>
                        $product->product_id,

                    'image_path' =>
                        $mainImageUrl,

                    'is_main' =>
                        true,
                ]);
            }
        }

        // =====================================================
        // 9. ถ้าไม่มีรูปหลักเลย ให้ใช้รูปสีแรก
        // =====================================================
        $hasMain =
            ProductImage::where(
                'product_id',
                $product->product_id
            )
                ->where(
                    'is_main',
                    true
                )
                ->exists();

        if (!$hasMain) {
            $firstColorImage =
                ProductImage::where(
                    'product_id',
                    $product->product_id
                )
                    ->whereNotNull('color_name')
                    ->first();

            if ($firstColorImage) {
                $firstColorImage->update([
                    'is_main' =>
                        true,
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
        $product =
            Product::with('images')
                ->findOrFail($id);

        $productName =
            $product->product_name;

        // =====================================================
        // ตรวจสอบรายการเช่าที่ยังดำเนินการอยู่
        // =====================================================
        $hasActiveRentals =
            RentalDetail::where(
                'product_id',
                $product->product_id
            )
                ->whereHas(
                    'rental',
                    function ($q) {
                        $q->whereIn(
                            'status',
                            [
                                'pending',
                                'confirmed',
                                'renting'
                            ]
                        );
                    }
                )
                ->exists();

        if ($hasActiveRentals) {
            return back()->with(
                'error',
                'ไม่สามารถลบชุดนี้ได้ เนื่องจากมีรายการเช่าที่กำลังดำเนินการอยู่ กรุณาเปลี่ยนสถานะเป็น "ปิดใช้งาน" แทน'
            );
        }

        // =====================================================
        // ลบไฟล์รูปเก่าที่อยู่ใน storage เท่านั้น
        // URL ออนไลน์จะไม่ถูกลบ
        // =====================================================
        foreach ($product->images as $image) {

            $imagePath =
                trim(
                    (string) $image->image_path
                );

            if (
                $imagePath !== '' &&
                !$this->isExternalUrl(
                    $imagePath
                ) &&
                Storage::disk('public')->exists(
                    $imagePath
                )
            ) {
                Storage::disk('public')->delete(
                    $imagePath
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
                'ลบชุด ' .
                $productName .
                ' เรียบร้อยแล้ว'
            );
    }

    /**
     * สลับสถานะพร้อมเช่า / ปิดใช้งาน
     */
    public function toggleStatus($id)
    {
        $product =
            Product::findOrFail($id);

        $newStatus =
            $product->status === 'available'
                ? 'inactive'
                : 'available';

        $product->update([
            'status' =>
                $newStatus
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
    public function storeCategory(
        Request $request
    ) {
        $validated =
            $request->validate(
                [
                    'category_name' =>
                        'required|string|max:100|unique:categories,category_name',

                    'description' =>
                        'nullable|string|max:500',
                ],
                [
                    'category_name.required' =>
                        'กรุณากรอกชื่อหมวดหมู่',

                    'category_name.max' =>
                        'ชื่อหมวดหมู่ต้องไม่เกิน 100 ตัวอักษร',

                    'category_name.unique' =>
                        'มีชื่อหมวดหมู่นี้อยู่ในระบบแล้ว',
                ]
            );

        Category::create([
            'category_name' =>
                trim(
                    $validated['category_name']
                ),

            'description' =>
                !empty(
                    $validated['description']
                )
                    ? trim(
                        $validated['description']
                    )
                    : null,

            'status' =>
                'active',
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'เพิ่มหมวดหมู่ใหม่ "' .
                trim(
                    $validated['category_name']
                ) .
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
        $category =
            Category::findOrFail($id);

        $validated =
            $request->validate(
                [
                    'category_name' =>
                        'required|string|max:100|unique:categories,category_name,' .
                        $category->category_id .
                        ',category_id',

                    'description' =>
                        'nullable|string|max:500',
                ],
                [
                    'category_name.required' =>
                        'กรุณากรอกชื่อหมวดหมู่',

                    'category_name.max' =>
                        'ชื่อหมวดหมู่ต้องไม่เกิน 100 ตัวอักษร',

                    'category_name.unique' =>
                        'มีชื่อหมวดหมู่นี้อยู่ในระบบแล้ว',
                ]
            );

        $category->update([
            'category_name' =>
                trim(
                    $validated['category_name']
                ),

            'description' =>
                !empty(
                    $validated['description']
                )
                    ? trim(
                        $validated['description']
                    )
                    : null,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'แก้ไขชื่อหมวดหมู่เป็น "' .
                trim(
                    $validated['category_name']
                ) .
                '" เรียบร้อยแล้ว'
            );
    }

    /**
     * ลบหมวดหมู่
     */
    public function destroyCategory($id)
    {
        $category =
            Category::findOrFail($id);

        if (
            $category
                ->products()
                ->exists()
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'ไม่สามารถลบหมวดหมู่ "' .
                    $category->category_name .
                    '" ได้ เนื่องจากยังมีชุดผูกอยู่'
                );
        }

        $categoryName =
            $category->category_name;

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