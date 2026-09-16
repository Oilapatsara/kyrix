<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Rental;
use App\Models\RentalDetail;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DressRentalCustomerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or update Categories
        $categoriesData = [
            [
                'category_name' => 'ชุดราตรียาว (Evening Gowns)',
                'description' => 'ชุดราตรียาวสุดหรู สำหรับงานกาล่าดินเนอร์ งานพรอม และงานรับรางวัล',
                'status' => 'active',
            ],
            [
                'category_name' => 'ชุดราตรีสั้น / ค็อกเทล (Cocktail Dresses)',
                'description' => 'เดรสค็อกเทลดีไซน์ทันสมัย คล่องตัว เหมาะสำหรับงานเลี้ยง ปาร์ตี้สังสรรค์',
                'status' => 'active',
            ],
            [
                'category_name' => 'ชุดไทยประยุกต์ / ชุดไทยดั้งเดิม (Thai Dresses)',
                'description' => 'ชุดไทยบรมพิมาน ชุดไทยศิวาลัย และชุดไทยประยุกต์ร่วมสมัย ตัดเย็บประณีต',
                'status' => 'active',
            ],
            [
                'category_name' => 'ชุดแต่งงาน & พรีเวดดิ้ง (Bridal Gowns)',
                'description' => 'ชุดเจ้าสาวระดับไฮเอนด์ ลูกไม้ฝรั่งเศส คัตติ้งเนี้ยบ ถ่ายพรีเวดดิ้งและวันจริง',
                'status' => 'active',
            ],
            [
                'category_name' => 'ชุดเพื่อนเจ้าสาว (Bridesmaid Collection)',
                'description' => 'ชุดเพื่อนเจ้าสาวโทนสีคุมธีม พาสเทล เอิร์ธโทน แมตช์ได้สวยงามทุกธีมงาน',
                'status' => 'active',
            ],
            [
                'category_name' => 'ชุดสูทสากล & ทักซิโด้ (Suits & Tuxedos)',
                'description' => 'ชุดสูทและทักซิโด้คุณสุภาพบุรุษ เนื้อผ้าพรีเมียม สวมใส่สง่างามทุกโอกาส',
                'status' => 'active',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[] = Category::firstOrCreate(['category_name' => $cat['category_name']], $cat);
        }

        // 2. Products Data
        $productsData = [
            [
                'category_id' => $categories[0]->category_id,
                'product_code' => 'KY-EVN-001',
                'product_name' => 'Royal Burgundy Velvet Gown (ชุดราตรียาวกำมะหยี่สีแดงเบอร์กันดี)',
                'description' => 'ชุดราตรียาวผ้ากำมะหยี่นำเข้าเกรดพรีเมียม โทนสีแดงเบอร์กันดีขับผิว ดีไซน์ผ่าหน้าเพิ่มความเพรียวระหงส์ เหมาะกับงานกาล่าและงานดินเนอร์หรูหรา',
                'size' => 'M',
                'color' => 'แดงเบอร์กันดี (Burgundy)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'แดงเบอร์กันดี, น้ำเงินมิดไนท์บลู, เขียวเอเมอรัลด์',
                'bust' => '32-34 นิ้ว',
                'waist' => '25-27 นิ้ว',
                'hips' => '35-37 นิ้ว',
                'length' => '145 ซม.',
                'rental_price' => 1500.00,
                'deposit' => 2000.00,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 142,
                'rental_count' => 18,
                'images' => [
                    'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[0]->category_id,
                'product_code' => 'KY-EVN-002',
                'product_name' => 'Golden Champagne Sequin Gala Gown (ชุดราตรียาวปักเลื่อมสีทองแชมเปญ)',
                'description' => 'ชุดราตรียาวปักเลื่อมชิมเมอร์ระยิบระยับเล่นแสงไฟอย่างงดงาม ดีไซน์เปิดไหล่ทรงคอร์เซ็ตกระชับสัดส่วน ให้ลุคเฉิดฉายดุจดาราบนพรมแดง',
                'size' => 'S',
                'color' => 'ทองแชมเปญ (Golden Champagne)',
                'available_sizes' => 'S, M',
                'available_colors' => 'ทองแชมเปญ, เงินสปาร์คเกิล',
                'bust' => '31-33 นิ้ว',
                'waist' => '24-26 นิ้ว',
                'hips' => '34-36 นิ้ว',
                'length' => '148 ซม.',
                'rental_price' => 1800.00,
                'deposit' => 2500.00,
                'stock' => 2,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 210,
                'rental_count' => 24,
                'images' => [
                    'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[1]->category_id,
                'product_code' => 'KY-CKT-001',
                'product_name' => 'Rose Gold Satin Cocktail Dress (เดรสค็อกเทลผ้าซาตินสีโรสโกลด์)',
                'description' => 'เดรสสั้นเข้ารูปผ้าซาตินเกรดนำเข้า ทรงสายเดี่ยวดีเทลจับเดรปด้านข้าง ดีไซน์เรียบหรูดูแพง เหมาะสำหรับงานปาร์ตี้ ดินเนอร์วันเกิด หรืองานเลี้ยงบริษัท',
                'size' => 'M',
                'color' => 'โรสโกลด์ (Rose Gold)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'โรสโกลด์, ดำคลาสสิก, นู้ดเบจ',
                'bust' => '32-35 นิ้ว',
                'waist' => '25-28 นิ้ว',
                'hips' => '35-38 นิ้ว',
                'length' => '90 ซม.',
                'rental_price' => 850.00,
                'deposit' => 1200.00,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 98,
                'rental_count' => 12,
                'images' => [
                    'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[2]->category_id,
                'product_code' => 'KY-THAI-001',
                'product_name' => 'ชุดไทยบรมพิมานผ้าไหมแท้ สีกลีบบัวอ่อน ปักเลื่อมมุกโบราณ',
                'description' => 'ชุดไทยบรมพิมานตัดเย็บจากผ้าไหมแท้เนื้อหนา ทรงคอตั้งแขนกระบอก เสื้อและผ้านุ่งยกหน้านางปักมุกและลูกปัดอย่างประณีต สำหรับพิธีหมั้น พิธีเช้า และงานรับพระราชทานน้ำสังข์',
                'size' => 'M',
                'color' => 'ชมพูกลีบบัว (Lotus Pink)',
                'available_sizes' => 'S, M, L, XL',
                'available_colors' => 'ชมพูกลีบบัว, ครีมงาช้าง, ทองศิวาลัย',
                'bust' => '33-35 นิ้ว',
                'waist' => '26-28 นิ้ว',
                'hips' => '36-38 นิ้ว',
                'length' => '102 ซม. (ผ้านุ่ง)',
                'rental_price' => 2500.00,
                'deposit' => 3500.00,
                'stock' => 2,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 320,
                'rental_count' => 35,
                'images' => [
                    'https://images.unsplash.com/photo-1617059063772-34532796cdb5?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[3]->category_id,
                'product_code' => 'KY-BRD-001',
                'product_name' => 'Royal French Lace Ball Gown (ชุดแต่งงานทรงบอลกาวน์ลูกไม้ฝรั่งเศส)',
                'description' => 'ชุดแต่งงานทรงบอลกาวน์ระดับโอต์กูตูร์ ลูกไม้ฝรั่งเศสทอมือพร้อมปักคริสตัลสวารอฟสกี้ ช่วงกระโปรงพองฟูหางยาว 1.5 เมตร เปล่งประกายดุจเจ้าหญิงในเทพนิยาย',
                'size' => 'M',
                'color' => 'ขาวบริสุทธิ์ (Pure White)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'ขาวบริสุทธิ์, ขาวออฟไวท์ (Off-White)',
                'bust' => '32-35 นิ้ว (คอร์เซ็ตปรับได้)',
                'waist' => '25-28 นิ้ว',
                'hips' => 'ฟรีไซซ์',
                'length' => '160 ซม. + ชายกระโปรง 1.5 ม.',
                'rental_price' => 4500.00,
                'deposit' => 5000.00,
                'stock' => 1,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 450,
                'rental_count' => 15,
                'images' => [
                    'https://images.unsplash.com/photo-1594552072238-b8a33785b261?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1549416864-228c7f24cf7a?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[4]->category_id,
                'product_code' => 'KY-BMD-001',
                'product_name' => 'Sage Green Chiffon Bridesmaid Dress (ชุดเพื่อนเจ้าสาวผ้าชีฟองสีเขียวเสจ)',
                'description' => 'ชุดเพื่อนเจ้าสาวโทนสีเขียวเสจ (Sage Green) ผ้าชีฟองพริ้วไหว ซับในเนื้อนุ่ม คัตติ้งเปิดไหล่ทรงยอดนิยม ใส่เป็นแก๊งเพื่อนเจ้าสาวถ่ายรูปขึ้นกล้องสุดๆ',
                'size' => 'Free Size (S-L)',
                'color' => 'เขียวเสจ (Sage Green)',
                'available_sizes' => 'S, M, L, XL',
                'available_colors' => 'เขียวเสจ, ชมพูดัสตี้, ฟ้าหม่น, นู้ดแชมเปญ',
                'bust' => '31-36 นิ้ว',
                'waist' => '24-30 นิ้ว (สม็อกหลัง)',
                'hips' => 'ฟรีไซซ์',
                'length' => '135 ซม.',
                'rental_price' => 700.00,
                'deposit' => 1000.00,
                'stock' => 10,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 180,
                'rental_count' => 48,
                'images' => [
                    'https://images.unsplash.com/photo-1525457136159-8878648a7ad0?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[5]->category_id,
                'product_code' => 'KY-SUT-001',
                'product_name' => 'Midnight Blue Velvet Tuxedo (สูททักซิโด้ผ้ากำมะหยี่สีกรมท่า)',
                'description' => 'สูททักซิโด้สุภาพบุรุษผ้ากำมะหยี่สีน้ำเงินเข้มมิดไนท์บลู ปกกล้วยหอมผ้าซาตินดำเงา คัตติ้งสไตล์อิตาลี พร้อมกางเกงแถบซาตินและหูกระต่ายโบว์ไท',
                'size' => 'L',
                'color' => 'น้ำเงินมิดไนท์บลู (Midnight Blue)',
                'available_sizes' => 'M, L, XL, XXL',
                'available_colors' => 'น้ำเงินมิดไนท์บลู, ดำคลาสสิก, แดงเบอร์กันดี',
                'bust' => 'ไหล่ 18 นิ้ว / อก 40-42 นิ้ว',
                'waist' => 'เอว 32-34 นิ้ว',
                'hips' => 'สะโพก 40 นิ้ว',
                'length' => 'ความยาวเสื้อ 74 ซม.',
                'rental_price' => 1600.00,
                'deposit' => 2000.00,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 125,
                'rental_count' => 14,
                'images' => [
                    'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[0]->category_id,
                'product_code' => 'KY-EVN-003',
                'product_name' => 'Emerald Green Mermaid Satin Gown (ชุดราตรีหางปลาผ้าซาตินสีเขียวมรกต)',
                'description' => 'ชุดราตรียาวทรงหางปลาผ้าซาตินเนื้อหนามันเงาสีเขียวมรกต โชว์ทรวดทรงเอวเอส คอถ่วงด้านหลังเสริมความเซ็กซี่มีระดับ เหมาะกับงานกลางคืนระดับทางการ',
                'size' => 'S',
                'color' => 'เขียวมรกต (Emerald Green)',
                'available_sizes' => 'S, M',
                'available_colors' => 'เขียวมรกต, น้ำเงินไพลิน, ดำชาร์โคล',
                'bust' => '31-33 นิ้ว',
                'waist' => '24-26 นิ้ว',
                'hips' => '35-37 นิ้ว',
                'length' => '146 ซม.',
                'rental_price' => 1400.00,
                'deposit' => 2000.00,
                'stock' => 2,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 160,
                'rental_count' => 19,
                'images' => [
                    'https://images.unsplash.com/photo-1568252542512-9fe8fe9c87bb?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80',
                ],
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $pData) {
            $imgs = $pData['images'];
            unset($pData['images']);

            $product = Product::updateOrCreate(['product_code' => $pData['product_code']], $pData);
            $createdProducts[] = $product;

            // Images
            ProductImage::where('product_id', $product->product_id)->delete();
            foreach ($imgs as $idx => $url) {
                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_path' => $url,
                    'is_main' => $idx === 0,
                    'created_at' => now(),
                ]);
            }
        }

        // 3. Demo Customer User
        $customerUser = User::firstOrCreate(
            ['email' => 'customer@kyrix.com'],
            [
                'name' => 'คุณพิมลภัส รัตนวิเชียร',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'status' => 'active',
            ]
        );

        $customer = Customer::firstOrCreate(
            ['user_id' => $customerUser->user_id],
            [
                'phone' => '0652599072',
                'address' => '77 ตำบลในเมือง อำเภอเมือง จังหวัดนครราชสีมา 30000',
            ]
        );

        // 4. Sample Rental Orders for Customer (To test My Bookings & Tracker)
        $sampleRental1 = Rental::firstOrCreate(
            ['rental_code' => 'KR-202609-0001'],
            [
                'customer_id' => $customer->customer_id,
                'rental_date' => now()->subDays(10),
                'start_date' => now()->subDays(8),
                'end_date' => now()->subDays(5),
                'total_amount' => 1500.00,
                'deposit_amount' => 2000.00,
                'service_type' => 'ซักแห้งรีดไอน้ำพรีเมียม',
                'service_fee' => 150.00,
                'delivery_method' => 'delivery',
                'delivery_address' => $customer->address,
                'recipient_phone' => $customer->phone,
                'tracking_number' => 'TH26091100998B',
                'return_tracking_no' => 'TH26091555667K',
                'status' => 'completed',
                'note' => 'ส่งคืนเรียบร้อย สภาพชุดสมบูรณ์ คืนเงินมัดจำแล้ว',
            ]
        );

        RentalDetail::firstOrCreate(
            ['rental_id' => $sampleRental1->rental_id, 'product_id' => $createdProducts[0]->product_id],
            [
                'quantity' => 1,
                'selected_size' => 'M',
                'selected_color' => 'แดงเบอร์กันดี',
                'rental_days' => 3,
                'price' => 1500.00,
                'subtotal' => 1500.00,
                'created_at' => now()->subDays(10),
            ]
        );

        Payment::firstOrCreate(
            ['rental_id' => $sampleRental1->rental_id],
            [
                'payment_amount' => 3650.00,
                'payment_date' => now()->subDays(10),
                'payment_method' => 'qr',
                'slip_image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&auto=format&fit=crop&q=80',
                'status' => 'approved',
                'note' => 'ชำระค่าเช่า + มัดจำ + ค่าบริการเสริมเรียบร้อย',
            ]
        );

        // Sample Review for Rental 1
        Review::firstOrCreate(
            ['rental_id' => $sampleRental1->rental_id, 'product_id' => $createdProducts[0]->product_id],
            [
                'customer_id' => $customer->customer_id,
                'rating' => 5,
                'comment' => 'ชุดสวยมากกกก ใส่ไปงานกาล่าคนชมทั้งคืนเลยค่ะ ผ้ากำมะหยี่มีน้ำหนักทิ้งตัวสวย คัตติ้งเนี้ยบสุดๆ ทางร้านทำความสะอาดมาหอมสะอาดมาก ประทับใจมากค่ะ!',
                'image_path' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=600&auto=format&fit=crop&q=80',
                'status' => 'published',
            ]
        );

        // Sample Rental 2: In progress (status: renting)
        $sampleRental2 = Rental::firstOrCreate(
            ['rental_code' => 'KR-202609-0002'],
            [
                'customer_id' => $customer->customer_id,
                'rental_date' => now()->subDays(2),
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(2),
                'total_amount' => 1800.00,
                'deposit_amount' => 2500.00,
                'service_type' => 'จัดส่งด่วนแมสเซนเจอร์',
                'service_fee' => 100.00,
                'delivery_method' => 'delivery',
                'delivery_address' => $customer->address,
                'recipient_phone' => $customer->phone,
                'tracking_number' => 'GRAB-891100',
                'status' => 'renting',
                'note' => 'ลูกค้าได้รับชุดแล้ว ใช้งานได้ถึงวันที่นัดหมาย',
            ]
        );

        RentalDetail::firstOrCreate(
            ['rental_id' => $sampleRental2->rental_id, 'product_id' => $createdProducts[1]->product_id],
            [
                'quantity' => 1,
                'selected_size' => 'S',
                'selected_color' => 'ทองแชมเปญ',
                'rental_days' => 3,
                'price' => 1800.00,
                'subtotal' => 1800.00,
                'created_at' => now()->subDays(2),
            ]
        );

        Payment::firstOrCreate(
            ['rental_id' => $sampleRental2->rental_id],
            [
                'payment_amount' => 4400.00,
                'payment_date' => now()->subDays(2),
                'payment_method' => 'qr',
                'slip_image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&auto=format&fit=crop&q=80',
                'status' => 'approved',
                'note' => 'ตรวจสอบสลิปแล้ว อนุมัติการเช่า',
            ]
        );

        // Sample other reviews
        Review::firstOrCreate(
            ['product_id' => $createdProducts[3]->product_id, 'customer_id' => $customer->customer_id],
            [
                'rating' => 5,
                'comment' => 'ชุดไทยบรมพิมานไหมแท้สวยวิจิตรมากค่ะ ผ้านุ่งปักเลื่อมแน่น สวยหรูสมราคา แขกในงานชมไม่ขาดสาย ขอบคุณร้าน KYRIX นะคะ',
                'image_path' => 'https://images.unsplash.com/photo-1617059063772-34532796cdb5?w=600&auto=format&fit=crop&q=80',
                'status' => 'published',
            ]
        );

        Review::firstOrCreate(
            ['product_id' => $createdProducts[4]->product_id, 'customer_id' => $customer->customer_id],
            [
                'rating' => 5,
                'comment' => 'ชุดแต่งงานสวยเกินต้าน ลูกไม้ฝรั่งเศสละมุนตามาก ถ่ายพรีเวดดิ้งออกมาเหมือนภาพวาดในฝันเลยค่ะ แนะนำเลยค่ะ!',
                'image_path' => 'https://images.unsplash.com/photo-1594552072238-b8a33785b261?w=600&auto=format&fit=crop&q=80',
                'status' => 'published',
            ]
        );
    }
}
