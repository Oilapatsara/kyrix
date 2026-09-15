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
     * แสดงรายการชุดทั้งหมด พร้อมระบบค้นหา และตัวกรองหมวดหมู่/สถานะ
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'mainImage']);

        // 1. ระบบค้นหา (ชื่อชุด, รหัสชุด, สี)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%");
            });
        }

        // 2. ตัวกรองหมวดหมู่ (Category Filter)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. ตัวกรองสถานะ (Status Filter)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ดึงรายการชุดพร้อม Pagination และรักษาสถานะ Query String ใน URL
        $products = $query->latest('product_id')->paginate(12)->withQueryString();

        // ดึงหมวดหมู่ทั้งหมดสำหรับทำ Dropdown Select
        $categories = Category::all();

        // สถิติสรุปจำนวนชุด
        $stats = [
            'total'       => Product::count(),
            'available'   => Product::where('status', 'available')->count(),
            'rented'      => Product::where('status', 'rented')->count(),
            'maintenance' => Product::where('status', 'maintenance')->count(),
            'inactive'    => Product::where('status', 'inactive')->count(),
        ];

        return view('owner.dresses.index', compact('products', 'categories', 'stats'));
    }

    /**
     * แสดงหน้าฟอร์มเพิ่มชุดใหม่
     */
    public function create()
    {
        $categories = Category::all();
        
        // รันรหัสชุดอัตโนมัติ (KY-DRS-0001)
        $nextId = (Product::max('product_id') ?? 0) + 1;
        $defaultCode = 'KY-DRS-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('owner.dresses.create', compact('categories', 'defaultCode'));
    }

    /**
     * บันทึกข้อมูลชุดใหม่ลงฐานข้อมูล
     */
    public function store(Request $request)
    {
        $categoryKey = (new Category)->getKeyName(); // ดึงชื่อ PK ของ Category เช่น category_id หรือ id

        $data = $request->validate([
            'category_id'      => "required|exists:categories,{$categoryKey}",
            'product_code'     => 'required|string|max:50|unique:products,product_code',
            'product_name'     => 'required|string|max:150',
            'description'      => 'nullable|string',
            'size'             => 'nullable|string|max:20',
            'color'            => 'nullable|string|max:50',
            'available_sizes'  => 'nullable|string|max:100',
            'available_colors' => 'nullable|string|max:150',
            'bust'             => 'nullable|string|max:50',
            'waist'            => 'nullable|string|max:50',
            'hips'             => 'nullable|string|max:50',
            'length'           => 'nullable|string|max:50',
            'rental_price'     => 'required|numeric|min:0',
            'deposit'          => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'status'           => 'required|in:available,rented,maintenance,inactive',
            'is_featured'      => 'nullable|boolean',
            'is_popular'       => 'nullable|boolean',
            'is_new'           => 'nullable|boolean',
            'image_file'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image_url'        => 'nullable|string|max:500',
        ], [
            'product_code.unique'   => 'รหัสชุดนี้มีอยู่ในระบบแล้ว',
            'product_name.required' => 'กรุณากรอกชื่อชุด',
            'category_id.required'  => 'กรุณาเลือกหมวดหมู่ชุด',
            'rental_price.required' => 'กรุณาระบุราคาเช่า',
            'deposit.required'      => 'กรุณาระบุเงินมัดจำ',
        ]);

        $data['is_featured'] = $request->has('is_featured');
        $data['is_popular']  = $request->has('is_popular');
        $data['is_new']      = $request->has('is_new');

        $product = Product::create($data);

        // จัดการอัปโหลดรูปภาพ
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->product_id,
                'image_path' => $path,
                'is_main'    => true,
            ]);
        } elseif (!empty($data['image_url'])) {
            ProductImage::create([
                'product_id' => $product->product_id,
                'image_path' => $data['image_url'],
                'is_main'    => true,
            ]);
        }

        return redirect()->route('owner.dresses.index')->with('success', 'เพิ่มชุดเรียบร้อยแล้ว: ' . $product->product_name);
    }

    /**
     * แสดงหน้าแก้ไขข้อมูลชุด
     */
    public function edit($id)
    {
        $dress = Product::with('images')->findOrFail($id);
        $categories = Category::all();

        return view('owner.dresses.edit', compact('dress', 'categories'));
    }

    /**
     * อัปเดตข้อมูลชุดในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $categoryKey = (new Category)->getKeyName();

        $data = $request->validate([
            'category_id'      => "required|exists:categories,{$categoryKey}",
            'product_code'     => 'required|string|max:50|unique:products,product_code,' . $product->product_id . ',product_id',
            'product_name'     => 'required|string|max:150',
            'description'      => 'nullable|string',
            'size'             => 'nullable|string|max:20',
            'color'            => 'nullable|string|max:50',
            'available_sizes'  => 'nullable|string|max:100',
            'available_colors' => 'nullable|string|max:150',
            'bust'             => 'nullable|string|max:50',
            'waist'            => 'nullable|string|max:50',
            'hips'             => 'nullable|string|max:50',
            'length'           => 'nullable|string|max:50',
            'rental_price'     => 'required|numeric|min:0',
            'deposit'          => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'status'           => 'required|in:available,rented,maintenance,inactive',
            'is_featured'      => 'nullable|boolean',
            'is_popular'       => 'nullable|boolean',
            'is_new'           => 'nullable|boolean',
            'image_file'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image_url'        => 'nullable|string|max:500',
        ]);

        $data['is_featured'] = $request->has('is_featured');
        $data['is_popular']  = $request->has('is_popular');
        $data['is_new']      = $request->has('is_new');

        $product->update($data);

        // จัดการเปลี่ยนรูปภาพหลักใหม่ (พร้อมลบรูปเก่าจาก Storage ถ้ามี)
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            
            // ปรับรูปเก่าให้ไม่เป็นรูปหลัก
            ProductImage::where('product_id', $product->product_id)->update(['is_main' => false]);
            
            ProductImage::create([
                'product_id' => $product->product_id,
                'image_path' => $path,
                'is_main'    => true,
            ]);
        } elseif (!empty($request->image_url)) {
            ProductImage::where('product_id', $product->product_id)->update(['is_main' => false]);
            ProductImage::create([
                'product_id' => $product->product_id,
                'image_path' => $request->image_url,
                'is_main'    => true,
            ]);
        }

        return redirect()->route('owner.dresses.index')->with('success', 'บันทึกการแก้ไขชุดเรียบร้อยแล้ว');
    }

    /**
     * ลบชุดออกจากระบบ
     */
    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);
        $productName = $product->product_name;

        // ตรวจสอบว่าชุดนี้มีรายการเช่าที่ยังไม่จบกระบวนการหรือไม่
        $hasActiveRentals = RentalDetail::where('product_id', $product->product_id)
            ->whereHas('rental', function ($q) {
                $q->whereIn('status', ['pending', 'confirmed', 'renting']);
            })->exists();

        if ($hasActiveRentals) {
            return back()->with('error', 'ไม่สามารถลบชุดนี้ได้ เนื่องจากมีรายการเช่าที่กำลังดำเนินการอยู่ กรุณาเปลี่ยนสถานะเป็น "ปิดใช้งาน" แทน');
        }

        // ลบไฟล์รูปภาพจริงใน Storage disk
        foreach ($product->images as $image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        // ลบข้อมูลรูปภาพและตัวสินค้าในฐานข้อมูล
        $product->images()->delete();
        $product->delete();

        return redirect()->route('owner.dresses.index')->with('success', 'ลบชุด ' . $productName . ' เรียบร้อยแล้ว');
    }

    /**
     * สลับสถานะพร้อมเช่า / ปิดใช้งาน อย่างเร็ว
     */
    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $newStatus = ($product->status === 'available') ? 'inactive' : 'available';
        $product->update(['status' => $newStatus]);

        $statusText = $newStatus === 'available' ? 'พร้อมให้เช่า' : 'ปิดใช้งาน';
        return back()->with('success', "เปลี่ยนสถานะชุด {$product->product_name} เป็น \"{$statusText}\" แล้ว");
    }
}