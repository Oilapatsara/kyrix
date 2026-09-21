@extends('layouts.owner')
@section('title','ตรวจสอบการชำระเงิน | KYRIX Admin')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
:root{--maroon-900:#430d17;--maroon-800:#5c1522;--maroon-700:#6f1a2b;--gold:#c79a5c;--gold-dark:#a97f45;--rose-bg:#f7e7ea;--rose-text:#7f2138;--cream:#faf7f4;--cream-dark:#f4eeea;--ink:#241417;--muted:#8a7a7d;--line:#efe6e4;--white:#fff;--green:#2e7d32;--green-bg:#eef7ef;--green-line:#c8e6c9;--orange:#9a6b00;--orange-bg:#fff5dd;--orange-line:#f0d99a;--red:#c62828;--red-bg:#fdf2f2;--red-line:#ffcdd2}
*{box-sizing:border-box}
body,h1,h2,h3,h4,h5,h6,p,span,a,button,input,select,textarea,table,th,td,div{font-family:'Noto Sans Thai',sans-serif!important}
.kyrix-payment-page{width:100%;max-width:1250px;margin:0 auto;color:var(--ink)}
.payment-page-header{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:24px;flex-wrap:wrap}
.payment-page-header-left{min-width:0}
.payment-eyebrow{display:inline-block;margin-bottom:7px;color:var(--gold-dark);font-size:11px;font-weight:800;letter-spacing:2px}
.payment-page-title{margin:0;color:var(--maroon-900);font-size:28px;line-height:1.25;font-weight:800}
.payment-page-description{margin:7px 0 0;color:var(--muted);font-size:13px;line-height:1.7}
.payment-alert{display:flex;align-items:center;gap:10px;margin-bottom:18px;padding:12px 15px;border-radius:10px;font-size:13px;font-weight:600}
.payment-alert.success{background:var(--green-bg);border:1px solid var(--green-line);color:var(--green)}
.payment-alert.error{background:var(--red-bg);border:1px solid var(--red-line);color:var(--red)}
.payment-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:20px}
.payment-stat{position:relative;display:block;padding:20px;background:var(--white);border:1px solid var(--line);border-radius:14px;color:inherit;text-decoration:none;box-shadow:0 3px 15px rgba(111,26,43,.03);transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;overflow:hidden}
.payment-stat:before{content:"";position:absolute;top:0;left:0;width:4px;height:100%}
.payment-stat.pending:before{background:#d97706}
.payment-stat.approved:before{background:#16a34a}
.payment-stat.rejected:before{background:#dc2626}
.payment-stat:hover{transform:translateY(-2px);border-color:var(--gold);box-shadow:0 7px 22px rgba(111,26,43,.08)}
.payment-stat.active{background:var(--cream);border-color:var(--gold);box-shadow:0 0 0 2px rgba(199,154,92,.22),0 7px 22px rgba(111,26,43,.06)}
.payment-stat-label{color:var(--muted);font-size:12px;font-weight:700}
.payment-stat-number{margin-top:6px;color:var(--maroon-900);font-size:23px;line-height:1.3;font-weight:800}
.payment-stat-sub{margin-top:5px;color:var(--muted);font-size:11px}
.payment-filter{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:20px;padding:12px 17px;background:var(--cream);border:1px solid var(--line);border-radius:12px;font-size:13px}
.payment-filter-label{color:var(--muted)}
.payment-filter-label strong{color:var(--maroon-900)}
.payment-filter-reset{flex-shrink:0;color:var(--maroon-800);font-size:12px;font-weight:700;text-decoration:underline}
.payment-filter-reset:hover{color:var(--maroon-900)}
.payment-table-card{overflow:hidden;background:var(--white);border:1px solid var(--line);border-radius:16px;box-shadow:0 3px 18px rgba(111,26,43,.04)}
.payment-table-header{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:16px 20px;background:var(--white);border-bottom:1px solid var(--line)}
.payment-table-heading{color:var(--maroon-900);font-size:14px;font-weight:800}
.payment-table-count{color:var(--muted);font-size:12px}
.payment-table-wrapper{width:100%;overflow-x:auto}
.payment-table{width:100%;min-width:980px;border-collapse:collapse}
.payment-table th{padding:14px 17px;background:var(--cream);border-bottom:1px solid var(--line);color:var(--muted);font-size:11px;font-weight:800;text-align:left;white-space:nowrap}
.payment-table td{padding:15px 17px;border-top:1px solid var(--line);color:#3a2b2e;font-size:13px;vertical-align:middle}
.payment-table tbody tr{transition:background .15s ease}
.payment-table tbody tr:hover{background:#fdfbfb}
.payment-code{color:var(--maroon-900);font-size:13px;font-weight:800;white-space:nowrap}
.payment-date{margin-top:4px;color:var(--muted);font-size:10.5px;white-space:nowrap}
.payment-customer-name{color:#2d1e21;font-size:13px;font-weight:700}
.payment-rental-reference{margin-top:3px;color:var(--muted);font-size:11px}
.payment-method{display:inline-flex;align-items:center;gap:4px;margin-top:5px;padding:2px 7px;background:var(--cream);border-radius:5px;color:var(--muted);font-size:10.5px}
.slip-cell{text-align:center}
.slip-preview{display:inline-flex;align-items:center;gap:9px;cursor:pointer;transition:transform .18s ease}
.slip-preview:hover{transform:translateY(-1px)}
.slip-image{display:block;width:54px;height:54px;object-fit:cover;border-radius:9px;border:1px solid var(--gold);background:var(--cream);box-shadow:0 2px 7px rgba(0,0,0,.08)}
.slip-preview-text{color:var(--maroon-800);font-size:11.5px;font-weight:700;text-decoration:underline}
.slip-empty{display:inline-flex;align-items:center;gap:7px;color:var(--muted);font-size:11.5px}
.slip-empty-icon{display:inline-flex;align-items:center;justify-content:center;width:50px;height:50px;background:var(--rose-bg);border:1px solid var(--line);border-radius:9px;color:var(--rose-text);font-size:17px}
.payment-amount{color:var(--maroon-900);font-size:14px;font-weight:800;text-align:center;white-space:nowrap}
.payment-amount-sub{font-size:10.5px;color:var(--muted);margin-top:4px;line-height:1.45;text-align:center}
.payment-status-cell{text-align:center}
.payment-status{display:inline-flex;align-items:center;justify-content:center;padding:5px 11px;border-radius:999px;font-size:11px;font-weight:800;white-space:nowrap}
.payment-status.pending{background:var(--orange-bg);color:var(--orange)}
.payment-status.approved{background:var(--green-bg);color:var(--green)}
.payment-status.rejected{background:var(--red-bg);color:var(--red)}
.payment-actions{display:flex;align-items:center;justify-content:center;gap:7px}
.payment-action-form{margin:0}
.payment-btn{display:inline-flex;align-items:center;justify-content:center;gap:5px;min-width:74px;padding:7px 10px;border-radius:8px;font-size:11.5px;font-weight:700;cursor:pointer;border:none;transition:background .18s ease,transform .18s ease}
.payment-btn:hover{transform:translateY(-1px)}
.payment-btn.approve{background:var(--green-bg);border:1px solid var(--green-line);color:var(--green)}
.payment-btn.approve:hover{background:#dcefdc}
.payment-btn.reject{background:var(--red-bg);border:1px solid var(--red-line);color:var(--red)}
.payment-btn.reject:hover{background:#f9dddd}
.payment-checked{color:var(--muted);font-size:11.5px;font-weight:700;white-space:nowrap}
.payment-empty{padding:65px 20px;text-align:center}
.payment-empty-icon{margin-bottom:13px;color:var(--rose-text);opacity:.55;font-size:38px}
.payment-empty-title{color:var(--maroon-900);font-size:14px;font-weight:800}
.payment-empty-text{margin-top:5px;color:var(--muted);font-size:12px}
.payment-pagination{padding:15px 20px;background:var(--cream);border-top:1px solid var(--line)}
.kyrix-swal-popup{border-radius:16px!important}
@media(max-width:900px){.payment-stats{grid-template-columns:1fr}}
@media(max-width:600px){.payment-page-title{font-size:23px}.payment-page-description{font-size:12px}.payment-filter{align-items:flex-start;flex-direction:column}.payment-table-header{align-items:flex-start;flex-direction:column}}
</style>
@endpush
@section('content')
<div class="kyrix-payment-page">
<div class="payment-page-header">
<div class="payment-page-header-left">
<span class="payment-eyebrow">KYRIX RENTAL · PAYMENTS</span>
<h1 class="payment-page-title">ตรวจสอบการชำระเงิน / สลิป</h1>
<p class="payment-page-description">ตรวจสอบยอดชำระเงินและหลักฐานการโอนของลูกค้า</p>
</div>
</div>

@if(session('success'))
<div class="payment-alert success"><i class="fa-solid fa-circle-check"></i><span>{{session('success')}}</span></div>
@endif
@if(session('error'))
<div class="payment-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{session('error')}}</span></div>
@endif

<div class="payment-stats">
<a href="{{route('owner.payments.index',['status'=>'pending'])}}" class="payment-stat pending {{request('status')==='pending'?'active':''}}">
<div class="payment-stat-label">รอตรวจสอบ</div>
<div class="payment-stat-number">{{number_format($counts['pending']??0)}} รายการ</div>
<div class="payment-stat-sub">รายการที่รอเจ้าของตรวจสอบสลิป</div>
</a>
<a href="{{route('owner.payments.index',['status'=>'approved'])}}" class="payment-stat approved {{request('status')==='approved'?'active':''}}">
<div class="payment-stat-label">อนุมัติแล้ว</div>
<div class="payment-stat-number">{{number_format($counts['approved']??0)}} รายการ</div>
<div class="payment-stat-sub">รายการที่ตรวจสอบและอนุมัติแล้ว</div>
</a>
<a href="{{route('owner.payments.index',['status'=>'rejected'])}}" class="payment-stat rejected {{request('status')==='rejected'?'active':''}}">
<div class="payment-stat-label">ปฏิเสธ / ไม่ถูกต้อง</div>
<div class="payment-stat-number">{{number_format($counts['rejected']??0)}} รายการ</div>
<div class="payment-stat-sub">รายการที่หลักฐานไม่ถูกต้อง</div>
</a>
</div>

@if(request('status'))
<div class="payment-filter">
<div class="payment-filter-label">กำลังแสดงข้อมูลเฉพาะสถานะ:
<strong>
@if(request('status')==='pending')รอตรวจสอบ
@elseif(request('status')==='approved')อนุมัติแล้ว
@elseif(request('status')==='rejected')ปฏิเสธ
@else{{request('status')}}
@endif
</strong>
</div>
<a href="{{route('owner.payments.index')}}" class="payment-filter-reset">แสดงทั้งหมด</a>
</div>
@endif

<div class="payment-table-card">
<div class="payment-table-header">
<div class="payment-table-heading">รายการชำระเงิน</div>
<div class="payment-table-count">ทั้งหมด {{method_exists($payments,'total')?number_format($payments->total()):$payments->count()}} รายการ</div>
</div>
<div class="payment-table-wrapper">
<table class="payment-table">
<thead>
<tr>
<th style="width:13%;">รหัสการชำระเงิน</th>
<th style="width:22%;">ลูกค้า / การเช่า</th>
<th style="width:21%;text-align:center;">หลักฐานการโอน</th>
<th style="width:12%;text-align:center;">จำนวนเงิน</th>
<th style="width:12%;text-align:center;">สถานะ</th>
<th style="width:20%;text-align:center;">จัดการ</th>
</tr>
</thead>
<tbody>
@forelse($payments as $payment)
@php
$paymentId=(int)$payment->payment_id;
$status=strtolower(trim((string)($payment->status??'pending')));
$statusText=match($status){
'pending'=>'รอตรวจสอบ',
'approved'=>'อนุมัติแล้ว',
'rejected'=>'ปฏิเสธ',
default=>$payment->status??'-'
};
$statusClass=match($status){
'pending'=>'pending',
'approved'=>'approved',
'rejected'=>'rejected',
default=>'pending'
};

$rental=$payment->rental;
$customer=$rental?->customer;
$firstName=trim((string)($customer->first_name??''));
$lastName=trim((string)($customer->last_name??''));
$customerName=trim($firstName.' '.$lastName);
if($customerName==='')$customerName='ไม่ระบุชื่อ';

$storedAmount=is_numeric($payment->amount??null)?(float)$payment->amount:0.00;
$rentalAmount=(float)($rental->total_amount??0);
$discountAmount=(float)($rental->discount_amount??0);
$depositAmount=(float)($rental->deposit_amount??0);
$serviceFee=(float)($rental->service_fee??0);

$calculatedAmount=max(0,$rentalAmount-$discountAmount+$depositAmount+$serviceFee);
$paymentAmount=$storedAmount>0?$storedAmount:$calculatedAmount;
$paymentAmountFormatted=number_format($paymentAmount,2);

$amountSource=$storedAmount>0?'ยอดที่บันทึกการชำระ':'ยอดคำนวณจากรายการเช่า';

$slipImage=trim((string)($payment->slip_image??''));
$slipUrl=null;
if($slipImage!==''){
if(str_starts_with($slipImage,'http://')||str_starts_with($slipImage,'https://')){
$slipUrl=$slipImage;
}else{
$cleanedPath=preg_replace('/^(public\/|storage\/)+/','',$slipImage);
$cleanedPath=ltrim($cleanedPath,'/');
if(!str_starts_with($cleanedPath,'uploads/'))$cleanedPath='uploads/'. $cleanedPath;
$slipUrl=asset($cleanedPath);
}
}

$paymentMethod=match(strtolower(trim((string)($payment->payment_method??'')))){
'cash'=>'เงินสด',
'transfer','bank_transfer'=>'โอนเงิน',
'qr','promptpay'=>'QR / PromptPay',
'card'=>'บัตร',
'other'=>'อื่น ๆ',
default=>'-'
};

$paymentDate='-';
$dateValue=$payment->paid_at??$payment->created_at??null;
if($dateValue){
try{
$paymentDate=$dateValue instanceof \Carbon\Carbon?$dateValue->format('d/m/Y H:i'):date('d/m/Y H:i',strtotime((string)$dateValue));
}catch(\Throwable $e){
$paymentDate='-';
}
}
@endphp

<tr>
<td>
<div class="payment-code">#PAY-{{$paymentId}}</div>
<div class="payment-date">{{$paymentDate}}</div>
</td>

<td>
<div class="payment-customer-name">{{$customerName}}</div>
<div class="payment-rental-reference">เลขอ้างอิงการเช่า: <strong>#{{$payment->rental_id??'-'}}</strong></div>
<div class="payment-method"><i class="fa-solid fa-wallet"></i>{{$paymentMethod}}</div>
</td>

<td class="slip-cell">
@if($slipUrl)
<div class="slip-preview" onclick="showSlipModal(@js($slipUrl),@js('PAY-'.$paymentId),@js($customerName),@js($paymentAmountFormatted))" title="คลิกเพื่อดูสลิป">
<img src="{{$slipUrl}}" alt="หลักฐานการโอนเงิน PAY-{{$paymentId}}" class="slip-image" loading="lazy" onerror="handleSlipError(this)">
<span class="slip-preview-text">ดูสลิป</span>
</div>
@else
<div class="slip-empty">
<div class="slip-empty-icon"><i class="fa-solid fa-image-slash"></i></div>
<span>ไม่มีสลิป</span>
</div>
@endif
</td>

<td>
<div class="payment-amount">฿{{$paymentAmountFormatted}}</div>
<div class="payment-amount-sub">{{$amountSource}}</div>
</td>

<td class="payment-status-cell">
<span class="payment-status {{$statusClass}}">
@if($status==='pending')รอตรวจสอบ
@elseif($status==='approved')อนุมัติแล้ว
@elseif($status==='rejected')ปฏิเสธ
@else{{$statusText}}
@endif
</span>
</td>

<td>
@if($status==='pending')
<div class="payment-actions">
<form action="{{route('owner.payments.approve',$paymentId)}}" method="POST" class="payment-action-form" onsubmit="return confirmApprove(event,this)">
@csrf
<button type="submit" class="payment-btn approve" title="อนุมัติการชำระเงินนี้"><i class="fa-solid fa-check"></i> อนุมัติ</button>
</form>
<form action="{{route('owner.payments.reject',$paymentId)}}" method="POST" class="payment-action-form" onsubmit="return confirmReject(event,this)">
@csrf
<button type="submit" class="payment-btn reject" title="ปฏิเสธการชำระเงินนี้"><i class="fa-solid fa-xmark"></i> ปฏิเสธ</button>
</form>
</div>
@else
<div style="text-align:center"><span class="payment-checked"><i class="fa-solid fa-lock"></i> ดำเนินการแล้ว</span></div>
@endif
</td>
</tr>

@empty
<tr>
<td colspan="6">
<div class="payment-empty">
<div class="payment-empty-icon"><i class="fa-solid fa-receipt"></i></div>
<div class="payment-empty-title">ไม่พบรายการชำระเงิน</div>
<div class="payment-empty-text">ยังไม่มีประวัติการชำระเงินในระบบขณะนี้</div>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

@if(method_exists($payments,'hasPages')&&$payments->hasPages())
<div class="payment-pagination">{{$payments->links()}}</div>
@endif
</div>
</div>
@endsection

@push('scripts')
<script>
function showSlipModal(url,code,customer,amount){
Swal.fire({
title:`หลักฐานการโอนเงิน #${code}`,
html:`
<div style="font-size:14px;margin-bottom:14px;color:#5c1522">
<strong>ลูกค้า:</strong> ${escapeHtml(customer)} &nbsp;|&nbsp; <strong>ยอดเงิน:</strong> ฿${escapeHtml(amount)}
</div>
<div style="max-height:75vh;overflow-y:auto;border-radius:10px;background:#faf7f4;padding:12px;display:flex;justify-content:center;align-items:center">
<img src="${escapeAttribute(url)}" style="max-width:100%;height:auto;object-fit:contain;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.12)" alt="Slip Image">
</div>
<div style="margin-top:14px;text-align:center">
<a href="${escapeAttribute(url)}" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;color:#6f1a2b;font-size:12.5px;font-weight:700;text-decoration:underline">
<i class="fa-solid fa-arrow-up-right-from-square"></i> เปิดรูปขนาดเต็มในแท็บใหม่
</a>
</div>`,
width:'800px',
showCloseButton:true,
showConfirmButton:false,
customClass:{popup:'kyrix-swal-popup'}
});
}

function escapeHtml(value){
return String(value??'').replace(/[&<>"']/g,function(char){
return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char];
});
}

function escapeAttribute(value){
return escapeHtml(value);
}

function handleSlipError(img){
img.onerror=null;
if(img.parentElement){
img.parentElement.innerHTML=`
<div class="slip-empty">
<div class="slip-empty-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
<span>โหลดรูปไม่สำเร็จ</span>
</div>`;
}
}

function confirmApprove(event,form){
event.preventDefault();
Swal.fire({
title:'ยืนยันการอนุมัติ?',
text:'คุณต้องการอนุมัติรายการชำระเงินนี้ใช่หรือไม่',
icon:'question',
showCancelButton:true,
confirmButtonColor:'#2e7d32',
cancelButtonColor:'#8a7a7d',
confirmButtonText:'ใช่, อนุมัติเลย',
cancelButtonText:'ยกเลิก',
customClass:{popup:'kyrix-swal-popup'}
}).then(result=>{
if(result.isConfirmed)form.submit();
});
return false;
}

function confirmReject(event,form){
event.preventDefault();
Swal.fire({
title:'ยืนยันการปฏิเสธ?',
text:'คุณต้องการปฏิเสธการชำระเงินนี้ใช่หรือไม่',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#c62828',
cancelButtonColor:'#8a7a7d',
confirmButtonText:'ใช่, ปฏิเสธรายการ',
cancelButtonText:'ยกเลิก',
customClass:{popup:'kyrix-swal-popup'}
}).then(result=>{
if(result.isConfirmed)form.submit();
});
return false;
}
</script>
@endpush