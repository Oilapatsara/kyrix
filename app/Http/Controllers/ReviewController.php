<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Rental;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create($rentalId, $productId)
    {
        $user = Auth::user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->user_id]);

        $rental = Rental::where('customer_id', $customer->customer_id)->findOrFail($rentalId);

        if (!in_array($rental->status, ['returned', 'completed'])) {
            return redirect()->route('rentals.show', $rentalId)->with('error', 'สามารถเขียนรีวิวได้หลังจากคืนชุดเรียบร้อยแล้วเท่านั้น');
        }

        $product = Product::findOrFail($productId);

        $existingReview = Review::where('rental_id', $rentalId)
            ->where('product_id', $productId)
            ->where('customer_id', $customer->customer_id)
            ->first();

        return view('reviews.create', compact('rental', 'product', 'existingReview'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rental_id' => 'required|exists:rentals,rental_id',
            'product_id' => 'required|exists:products,product_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'rating.required' => 'กรุณาให้คะแนนความพึงพอใจ',
            'comment.required' => 'กรุณาเขียนข้อความรีวิว',
            'comment.min' => 'ข้อความรีวิวต้องมีอย่างน้อย 5 ตัวอักษร',
            'image.image' => 'ไฟล์แนบต้องเป็นรูปภาพเท่านั้น',
        ]);

        $user = Auth::user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->user_id]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'review_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/reviews'), $filename);
            $imagePath = 'uploads/reviews/' . $filename;
        }

        Review::updateOrCreate(
            [
                'rental_id' => $request->rental_id,
                'product_id' => $request->product_id,
                'customer_id' => $customer->customer_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
                'image_path' => $imagePath ?? ($existingReview->image_path ?? null),
                'status' => 'published',
            ]
        );

        return redirect()->route('products.show', $request->product_id)
            ->with('success', 'ขอบคุณสำหรับรีวิวของคุณ! ข้อความรีวิวจะถูกเผยแพร่ในหน้ารายละเอียดชุด');
    }
}
