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
    /**
     * ดึงข้อมูลลูกค้าปัจจุบันที่ล็อกอินผ่าน Session หรือ Auth
     */
    private function getCustomer(): Customer
    {
        $customerId = session('customer_id');

        if ($customerId) {
            $customer = Customer::find($customerId);

            if ($customer) {
                return $customer;
            }
        }

        if (Auth::check()) {
            $user = Auth::user();

            $customer = $user->customer
                ?? Customer::where('user_id', $user->user_id)->first();

            if ($customer) {
                return $customer;
            }
        }

        abort(
            403,
            'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่'
        );
    }

    /**
     * หน้าเขียนรีวิว
     */
    public function create($rentalId, $productId)
    {
        $customer = $this->getCustomer();

        $rental = Rental::where(
            'customer_id',
            $customer->customer_id
        )->findOrFail($rentalId);

        if (!in_array(
            $rental->status,
            [
                'returned',
                'completed',
            ],
            true
        )) {
            return redirect()
                ->route(
                    'rentals.show',
                    $rentalId
                )
                ->with(
                    'error',
                    'สามารถเขียนรีวิวได้หลังจากคืนชุดเรียบร้อยแล้วเท่านั้น'
                );
        }

        $product = Product::with([
            'images',
            'category',
        ])->findOrFail($productId);

        $existingReview = Review::where(
            'rental_id',
            $rentalId
        )
            ->where(
                'product_id',
                $productId
            )
            ->where(
                'customer_id',
                $customer->customer_id
            )
            ->first();

        return view(
            'reviews.create',
            compact(
                'rental',
                'product',
                'existingReview'
            )
        );
    }

    /**
     * บันทึกรีวิว
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'rental_id' => 'required|exists:rentals,rental_id',
                'product_id' => 'required|exists:products,product_id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:5|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            ],
            [
                'rating.required' => 'กรุณาให้คะแนนความพึงพอใจ',
                'comment.required' => 'กรุณาเขียนข้อความรีวิว',
                'comment.min' => 'ข้อความรีวิวต้องมีอย่างน้อย 5 ตัวอักษร',
                'image.image' => 'ไฟล์แนบต้องเป็นรูปภาพเท่านั้น',
            ]
        );

        $customer = $this->getCustomer();

        /**
         * ตรวจสอบว่ารายการเช่านี้เป็นของลูกค้าท่านนี้จริง
         */
        $rental = Rental::where(
            'customer_id',
            $customer->customer_id
        )->findOrFail(
            $request->rental_id
        );

        /**
         * ตรวจสอบว่าสามารถรีวิวได้หลังคืนชุดแล้ว
         */
        if (!in_array(
            $rental->status,
            [
                'returned',
                'completed',
            ],
            true
        )) {
            return redirect()
                ->route(
                    'rentals.show',
                    $rental->rental_id
                )
                ->with(
                    'error',
                    'สามารถเขียนรีวิวได้หลังจากคืนชุดเรียบร้อยแล้วเท่านั้น'
                );
        }

        /**
         * ดึงรีวิวเดิม ถ้ามี
         */
        $existingReview = Review::where(
            'rental_id',
            $request->rental_id
        )
            ->where(
                'product_id',
                $request->product_id
            )
            ->where(
                'customer_id',
                $customer->customer_id
            )
            ->first();

        $imagePath = $existingReview?->image_path;

        /**
         * อัปโหลดรูปรีวิว
         */
        if ($request->hasFile('image')) {

            /**
             * ลบรูปเก่า
             */
            if (
                $imagePath &&
                file_exists(
                    public_path($imagePath)
                )
            ) {
                @unlink(
                    public_path($imagePath)
                );
            }

            /**
             * สร้างโฟลเดอร์
             */
            $uploadDir = public_path(
                'uploads/reviews'
            );

            if (!is_dir($uploadDir)) {
                mkdir(
                    $uploadDir,
                    0755,
                    true
                );
            }

            /**
             * สร้างชื่อไฟล์ใหม่
             */
            $file = $request->file('image');

            $filename =
                'review_' .
                time() .
                '_' .
                uniqid() .
                '.' .
                $file->getClientOriginalExtension();

            $file->move(
                $uploadDir,
                $filename
            );

            $imagePath =
                'uploads/reviews/' .
                $filename;
        }

        /**
         * บันทึก / แก้ไขรีวิว
         */
        Review::updateOrCreate(
            [
                'rental_id' => $request->rental_id,
                'product_id' => $request->product_id,
                'customer_id' => $customer->customer_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
                'image_path' => $imagePath,
                'status' => 'published',
            ]
        );

        /**
         * หลังส่งรีวิว
         * กลับไปหน้ารายละเอียดชุด
         * และเลื่อนไปยังส่วนรีวิวทันที
         */
        return redirect()
            ->to(
                route(
                    'products.show',
                    $request->product_id
                ) . '#reviews'
            )
            ->with(
                'success',
                '✨ ขอบคุณสำหรับรีวิวของคุณ! ส่งรีวิวเรียบร้อยแล้ว'
            );
    }
}