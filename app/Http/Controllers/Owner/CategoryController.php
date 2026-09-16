<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * แสดงรายการหมวดหมู่
     */
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where('name', 'like', '%' . $search . '%');
        }

        $categories = $query
            ->withCount('products')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('owner.categories.index', compact('categories'));
    }

    /**
     * หน้าเพิ่มหมวดหมู่
     */
    public function create()
    {
        return view('owner.categories.create');
    }

    /**
     * บันทึกหมวดหมู่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:categories,name',
            ],
        ], [
            'name.required' => 'กรุณากรอกชื่อหมวดหมู่',
            'name.max' => 'ชื่อหมวดหมู่ต้องไม่เกิน 100 ตัวอักษร',
            'name.unique' => 'มีหมวดหมู่นี้อยู่ในระบบแล้ว',
        ]);

        Category::create([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->route('owner.categories.index')
            ->with('success', 'เพิ่มหมวดหมู่เรียบร้อยแล้ว');
    }

    /**
     * หน้าแก้ไข
     */
    public function edit(Category $category)
    {
        return view('owner.categories.edit', compact('category'));
    }

    /**
     * อัปเดตหมวดหมู่
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:categories,name,' . $category->id,
            ],
        ], [
            'name.required' => 'กรุณากรอกชื่อหมวดหมู่',
            'name.max' => 'ชื่อหมวดหมู่ต้องไม่เกิน 100 ตัวอักษร',
            'name.unique' => 'มีหมวดหมู่นี้อยู่ในระบบแล้ว',
        ]);

        $category->update([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->route('owner.categories.index')
            ->with('success', 'แก้ไขหมวดหมู่เรียบร้อยแล้ว');
    }

    /**
     * ลบหมวดหมู่
     */
    public function destroy(Category $category)
    {
        // ถ้ามีชุดอยู่ในหมวดหมู่นี้ ไม่ให้ลบทิ้ง
        if ($category->products()->exists()) {
            return redirect()
                ->route('owner.categories.index')
                ->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีชุดอยู่ในหมวดหมู่นี้');
        }

        $category->delete();

        return redirect()
            ->route('owner.categories.index')
            ->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
    }
}