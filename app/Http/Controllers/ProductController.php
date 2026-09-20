<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * แสดงรายการสินค้า
     */
    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'mainImage'
        ]);

        // ค้นหาสินค้า
        if ($request->filled('q')) {
            $q = $request->input('q');

            $query->where(function ($sub) use ($q) {
                $sub->where('product_name', 'like', "%{$q}%")
                    ->orWhere('product_code', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('color', 'like', "%{$q}%");
            });
        }

        // กรองตามหมวดหมู่
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->input('category_id')
            );
        }

        // กรองราคาขั้นต่ำ
        if ($request->filled('min_price')) {
            $query->where(
                'rental_price',
                '>=',
                (float) $request->input('min_price')
            );
        }

        // กรองราคาสูงสุด
        if ($request->filled('max_price')) {
            $query->where(
                'rental_price',
                '<=',
                (float) $request->input('max_price')
            );
        }

        // กรองขนาด
        if ($request->filled('size')) {
            $size = $request->input('size');

            $query->where(function ($sub) use ($size) {
                $sub->where('size', $size)
                    ->orWhere(
                        'available_sizes',
                        'like',
                        "%{$size}%"
                    );
            });
        }

        // กรองสี
        if ($request->filled('color')) {
            $color = $request->input('color');

            $query->where(function ($sub) use ($color) {
                $sub->where(
                    'color',
                    'like',
                    "%{$color}%"
                )->orWhere(
                    'available_colors',
                    'like',
                    "%{$color}%"
                );
            });
        }

        // กรองสถานะ
        if (
            $request->filled('status')
            && $request->input('status') !== 'all'
        ) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        // การเรียงสินค้า
        $sort = $request->input('sort', 'newest');

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('rental_price', 'asc')
                    ->orderBy('product_id', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('rental_price', 'desc')
                    ->orderBy('product_id', 'asc');
                break;

            case 'popular':
                $query->orderBy('rental_count', 'desc')
                    ->orderBy('product_id', 'asc');
                break;

            case 'views':
                $query->orderBy('views_count', 'desc')
                    ->orderBy('product_id', 'asc');
                break;

            default:
                // สินค้าใหม่สุด
                // ใช้ product_id เป็นตัวเรียงสำรอง
                // เพื่อให้เครื่องต่าง ๆ แสดงลำดับเดียวกัน
                $query->latest()
                    ->orderBy('product_id', 'desc');
                break;
        }

        // แสดง 9 รายการต่อหน้า
        $products = $query
            ->paginate(9)
            ->withQueryString();

        // หมวดหมู่ที่เปิดใช้งาน
        $categories = Category::where(
            'status',
            'active'
        )
            ->withCount('products')
            ->get();

        return view(
            'products.index',
            compact(
                'products',
                'categories'
            )
        );
    }

    /**
     * แสดงรายละเอียดสินค้า
     */
    public function show($id)
    {
        $product = Product::with([
            'category',
            'images',
            'reviews.customer'
        ])->findOrFail($id);

        // เพิ่มจำนวนการเข้าชม
        $product->increment('views_count');

        // สินค้าที่เกี่ยวข้อง
        $relatedProducts = Product::where(
            'category_id',
            $product->category_id
        )
            ->where(
                'product_id',
                '!=',
                $product->product_id
            )
            ->where(
                'status',
                'available'
            )
            ->with([
                'category',
                'mainImage'
            ])
            ->orderBy('product_id', 'desc')
            ->take(4)
            ->get();

        return view(
            'products.show',
            compact(
                'product',
                'relatedProducts'
            )
        );
    }
}