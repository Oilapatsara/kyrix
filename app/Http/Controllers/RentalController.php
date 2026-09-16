<?php
namespace App\Http\Controllers;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function book(Request $request,$productId)
    {
        $product=Product::findOrFail($productId);

        $request->validate([
            'start_date'=>'required|date|after_or_equal:today',
            'end_date'=>'required|date|after_or_equal:start_date',
            'quantity'=>'required|integer|min:1|max:'.$product->stock,
            'size'=>'nullable|string',
            'color'=>'nullable|string',
            'note'=>'nullable|string|max:500',
        ],[
            'start_date.required'=>'กรุณาระบุวันที่เริ่มเช่า',
            'start_date.after_or_equal'=>'วันที่เริ่มเช่าต้องไม่ย้อนหลัง',
            'end_date.required'=>'กรุณาระบุวันที่คืนชุด',
            'end_date.after_or_equal'=>'วันที่คืนชุดต้องไม่น้อยกว่าวันที่เริ่มเช่า',
            'quantity.required'=>'กรุณาระบุจำนวนชุด',
            'quantity.min'=>'จำนวนชุดต้องอย่างน้อย 1 ชุด',
            'quantity.max'=>'จำนวนชุดเกินสต็อกที่มี ('.$product->stock.' ชุด)',
        ]);

        $customer=$this->getCustomer();
        $start=Carbon::parse($request->start_date);
        $end=Carbon::parse($request->end_date);
        $days=max(1,$start->diffInDays($end)+1);
        $quantity=(int)$request->quantity;
        $pricePerDay=(float)$product->rental_price;
        $subtotal=$pricePerDay*$days*$quantity;
        $depositTotal=(float)$product->deposit*$quantity;

        DB::beginTransaction();

        try{
            $product=Product::where('product_id',$productId)
                ->lockForUpdate()
                ->firstOrFail();

            if($product->status!=='available' || $product->stock < $quantity){
                throw new \RuntimeException('ชุดนี้ไม่อยู่ในสถานะว่างพร้อมเช่าหรือสต็อกไม่เพียงพอ');
            }

            $rentalCode='KR-'.date('Ym').'-'.str_pad(Rental::count()+1,4,'0',STR_PAD_LEFT);

            $rental=Rental::create([
                'rental_code'=>$rentalCode,
                'customer_id'=>$customer->customer_id,
                'rental_date'=>now()->toDateString(),
                'start_date'=>$start->toDateString(),
                'end_date'=>$end->toDateString(),
                'total_amount'=>$subtotal,
                'deposit_amount'=>$depositTotal,
                'status'=>'pending_payment',
                'note'=>$request->note,
            ]);

            RentalDetail::create([
                'rental_id'=>$rental->rental_id,
                'product_id'=>$product->product_id,
                'quantity'=>$quantity,
                'price'=>$pricePerDay,
                'subtotal'=>$subtotal,
            ]);

            $product->decrement('stock', $quantity);
            $product->increment('rental_count');

            if ($product->fresh()->stock <= 0) {
                $product->update(['status' => 'rented']);
            }

            DB::commit();

            return redirect()->route('rentals.payment',$rental->rental_id)
                ->with('success','บันทึกการจองเช่าชุดเรียบร้อยแล้ว กรุณาดำเนินการชำระเงิน');
        }catch(\Throwable $e){
            DB::rollBack();
            return back()
                ->with('error','เกิดข้อผิดพลาดในการบันทึกการจอง: '.$e->getMessage())
                ->withInput();
        }
    }

    public function payment($id)
    {
        $customer=$this->getCustomer();

        $rental=Rental::where('customer_id',$customer->customer_id)
            ->with(['details.product.mainImage','payments'])
            ->findOrFail($id);

        return view('rentals.payment',compact('rental'));
    }

    public function submitPayment(Request $request,$id)
    {
        $customer=$this->getCustomer();

        $rental=Rental::where('customer_id',$customer->customer_id)
            ->findOrFail($id);

        $request->validate([
            'payment_method'=>'required|in:transfer,qr,cash,other',
            'slip_image'=>'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'note'=>'nullable|string|max:500',
        ],[
            'payment_method.required'=>'กรุณาเลือกวิธีการชำระเงิน',
            'slip_image.required'=>'กรุณาแนบรูปภาพสลิปหลักฐานการโอนเงิน',
            'slip_image.image'=>'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'slip_image.max'=>'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB',
        ]);

        $uploadDir=public_path('uploads/slips');

        if(!is_dir($uploadDir)){
            mkdir($uploadDir,0755,true);
        }

        $file=$request->file('slip_image');
        $filename='slip_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($uploadDir,$filename);
        $slipPath='uploads/slips/'.$filename;

        Payment::create([
            'rental_id'=>$rental->rental_id,
            'payment_amount'=>$rental->grand_total,
            'payment_date'=>now(),
            'payment_method'=>$request->payment_method,
            'slip_image'=>$slipPath,
            'status'=>'pending',
            'note'=>$request->note??'ชำระเงินค่าเช่าชุด (ค่าเช่า 100% + มัดจำ ฿'.number_format($rental->deposit_amount, 2).')',
        ]);

        $rental->update([
            'status'=>'pending_verification'
        ]);

        return redirect()->route('rentals.show', $rental->rental_id)
            ->with('success','ส่งหลักฐานการชำระเงินเรียบร้อยแล้ว สถานะ: รอตรวจสอบสลิป');
    }

    public function index()
    {
        $customer=$this->getCustomer();

        $activeRentals=Rental::where('customer_id',$customer->customer_id)
            ->whereNotIn('status',['returned','completed','cancelled'])
            ->with(['details.product.mainImage','payments'])
            ->latest()
            ->get();

        return view('rentals.index',compact('activeRentals'));
    }

    public function history()
    {
        $customer=$this->getCustomer();

        $pastRentals=Rental::where('customer_id',$customer->customer_id)
            ->whereIn('status',['returned','completed','cancelled'])
            ->with(['details.product.mainImage','payments'])
            ->latest()
            ->paginate(10);

        return view('rentals.history',compact('pastRentals'));
    }

    public function show($id)
    {
        $customer=$this->getCustomer();

        $rental=Rental::where('customer_id',$customer->customer_id)
            ->with(['details.product.mainImage','payments'])
            ->findOrFail($id);

        return view('rentals.show',compact('rental'));
    }

    public function uploadSlip(Request $request,$id)
    {
        return $this->submitPayment($request,$id);
    }

    public function requestReturn(Request $request,$id)
    {
        $customer=$this->getCustomer();

        $rental=Rental::where('customer_id',$customer->customer_id)
            ->findOrFail($id);

        if(!in_array($rental->status, ['confirmed', 'ready_pickup', 'renting'], true)){
            return back()->with('error','รายการนี้ยังไม่พร้อมแจ้งคืนชุด');
        }

        $data = $request->validate([
            'return_method' => 'nullable|string|max:100',
            'return_tracking_no' => 'nullable|string|max:100',
        ]);

        $returnTrackingNo = $data['return_tracking_no'] ?? null;
        $returnNote = trim(($data['return_method'] ?? '') . ($returnTrackingNo ? ' | เลขพัสดุส่งคืน: '.$returnTrackingNo : ''));
        $note = $rental->note;
        if ($returnNote !== '') {
            $note = $note ? ($note."\nแจ้งคืนจากลูกค้า: ".$returnNote) : ('แจ้งคืนจากลูกค้า: '.$returnNote);
        }

        $rental->update([
            'status'=>'pending_return',
            'return_tracking_no'=>$returnTrackingNo ?? $rental->return_tracking_no,
            'note'=>$note,
        ]);

        return back()->with('success','แจ้งส่งคืนชุดเรียบร้อยแล้ว รอเจ้าของร้านตรวจรับและจัดการเงินมัดจำ');
    }

    private function getCustomer(): Customer
    {
        $customerId=session('customer_id');

        if(!$customerId){
            abort(403,'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่');
        }

        $customer=Customer::find($customerId);

        if(!$customer){
            session()->forget([
                'customer_logged_in',
                'customer_id',
                'customer_name',
                'customer_email',
            ]);

            abort(403,'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่');
        }

        return $customer;
    }
}
