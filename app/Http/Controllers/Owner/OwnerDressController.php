<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OwnerDressController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'mainImage']);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest('product_id')->paginate(12)->withQueryString();
        $categories = Category::where('status', 'active')->get();

        $stats = [
            'total' => Product::count(),
            'available' => Product::where('status', 'available')->count(),
            'rented' => Product::where('status', 'rented')->count(),
            'maintenance' => Product::where('status', 'maintenance')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
        ];

        // แก้ไข: ชี้ไปที่หน้าแอดมินในโฟลเดอร์ owner/dresses
        return view('owner.dresses.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        // Generate auto product code
        $nextId = (Product::max('product_id') ?? 0) + 1;
        $defaultCode = 'KY-DRS-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // แก้ไข: ชี้ไปที่หน้าแอดมินในโฟลเดอร์ owner/dresses
        return view('owner.dresses.create', compact('categories', 'defaultCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'      => 'required|exists:categories,category_id',
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
            'image_file'       => 'nullable|image|max:4096',
            'image_url'        => 'nullable|string|max:500',
        ], [
            'product_code.unique' => 'รหัสชุดนี้มีอยู่ในระบบแล้ว',
            'product_name.required' => 'กรุณากรอกชื่อชุด',
            'category_id.required' => 'กรุณาเลือกหมวดหมู่ชุด',
            'rental_price.required' => 'กรุณาระบุราคาเช่า',
            'deposit.required' => 'กรุณาระบุเงินมัดจำ',
        ]);

        $data['is_featured'] = $request->has('is_featured');
        $data['is_popular']  = $request->has('is_popular');
        $data['is_new']      = $request->has('is_new');

        $product = Product::create($data);

        // Process Image
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

    public function edit($id)
    {
        $dress = Product::with('images')->findOrFail($id);
        $categories = Category::where('status', 'active')->get();

        // แก้ไข: ชี้ไปที่หน้าแอดมินในโฟลเดอร์ owner/dresses
        return view('owner.dresses.edit', compact('dress', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'category_id'      => 'required|exists:categories,category_id',
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
            'image_file'       => 'nullable|image|max:4096',
            'image_url'        => 'nullable|string|max:500',
        ]);

        $data['is_featured'] = $request->has('is_featured');
        $data['is_popular']  = $request->has('is_popular');
        $data['is_new']      = $request->has('is_new');

        $product->update($data);

        // If new image provided
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            // Unset previous main
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

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->product_name;

        // Check if has active rentals
        $hasActiveRentals = \App\Models\RentalDetail::where('product_id', $product->product_id)
            ->whereHas('rental', function ($q) {
                $q->whereIn('status', ['pending', 'confirmed', 'renting']);
            })->exists();

        if ($hasActiveRentals) {
            return back()->with('error', 'ไม่สามารถลบชุดนี้ได้เนื่องจากมีรายการเช่าที่ยังดำเนินการอยู่ กรุณาเปลี่ยนสถานะเป็น Inactive แทน');
        }

        $product->images()->delete();
        $product->delete();

        return redirect()->route('owner.dresses.index')->with('success', 'ลบชุด ' . $productName . ' เรียบร้อยแล้ว');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $newStatus = ($product->status === 'available') ? 'inactive' : 'available';
        $product->update(['status' => $newStatus]);

        $statusText = $newStatus === 'available' ? 'พร้อมเช่า' : 'ปิดใช้งาน';
        return back()->with('success', "เปลี่ยนสถานะชุด {$product->product_name} เป็น \"{$statusText}\" แล้ว");
    }
}