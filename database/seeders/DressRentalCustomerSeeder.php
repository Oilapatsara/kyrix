<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class DressRentalCustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder นี้ปรับตามข้อมูลปัจจุบันจากฐานข้อมูล kyrix (9).sql
        // เน้นเฉพาะ Categories + Products + Product Images
        // ไม่แตะ Customers / Rentals / Payments / Reviews ที่เป็นข้อมูลจากการใช้งานจริง

        // 1. Categories ปัจจุบัน
        $categoriesData = [
            [
                'category_id' => 1,
                'category_name' => 'Western Muse — เดรสสายฝอ',
                'description' => 'เดรสสไตล์สายฝอ ดีไซน์เรียบหรู เซ็กซี่ มั่นใจ โดดเด่นทุกงาน',
                'status' => 'active',
            ],
            [
                'category_id' => 2,
                'category_name' => 'Sweet Romance — เดรสหวาน',
                'description' => 'เดรสสไตล์หวาน น่ารัก อ่อนหวาน เหมาะสำหรับงานเลี้ยงและโอกาสพิเศษ',
                'status' => 'active',
            ],
            [
                'category_id' => 3,
                'category_name' => 'Mini Chic — มินิเดรส',
                'description' => 'มินิเดรสเก๋ๆ คล่องตัว สวมใส่ง่าย เหมาะสำหรับปาร์ตี้และสังสรรค์',
                'status' => 'active',
            ],
            [
                'category_id' => 4,
                'category_name' => 'Elegant Tops — เสื้อ & ท็อปส์',
                'description' => 'เสื้อและท็อปส์ดีไซน์หรูหรา แมตช์ง่าย สวมใส่ได้หลายโอกาส',
                'status' => 'active',
            ],
            [
                'category_id' => 5,
                'category_name' => 'Feminine Skirts — กระโปรง',
                'description' => 'กระโปรงทรงสวย ตัดเย็บประณีต เพิ่มเสน่ห์และความอ่อนหวาน',
                'status' => 'active',
            ],
            [
                'category_id' => 6,
                'category_name' => 'Modern Pants — กางเกง',
                'description' => 'กางเกงทรงโมเดิร์น ดีไซน์ทันสมัย สวมใส่สบายและสง่างาม',
                'status' => 'active',
            ],
            [
                'category_id' => 7,
                'category_name' => 'Elegant Shoes — รองเท้า',
                'description' => 'รองเท้าส้นสูงและรองเท้าดีไซน์หรู เติมเต็มลุคให้สมบูรณ์แบบ',
                'status' => 'active',
            ],
            [
                'category_id' => 8,
                'category_name' => 'Luxury Bags — กระเป๋า',
                'description' => 'กระเป๋าถือและออกงานระดับแบรนด์พรีเมียม เสริมความหรูหรา',
                'status' => 'active',
            ],
        ];

        $keptCategoryIds = [];

        foreach ($categoriesData as $categoryData) {
            $category = Category::updateOrCreate(
                ['category_id' => $categoryData['category_id']],
                $categoryData
            );

            $keptCategoryIds[] = $category->category_id;
        }

        // ลบเฉพาะหมวดหมู่เก่าที่ไม่มีสินค้าใช้งานอยู่
        $oldCategoryIds = Category::whereNotIn('category_id', $keptCategoryIds)
            ->whereDoesntHave('products')
            ->pluck('category_id');

        if ($oldCategoryIds->isNotEmpty()) {
            Category::whereIn('category_id', $oldCategoryIds)->delete();
        }

        // 2. Products ปัจจุบัน
        $productsData = [
            [
                'category_id' => 1,
                'product_code' => 'KY-EVN-001',
                'product_name' => 'มินิเดรสผ้าลูกไม้สายเดี่ยวผูกโบว์',
                'description' => 'มินิเดรสผ้าลูกไม้สายเดี่ยวสีขาวครีม (สไตล์ Fairycore / Coquette)',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'สีขาวงาช้าง',
                'available_colors' => 'สีขาวงาช้าง',
                'bust' => '32-34 นิ้ว',
                'waist' => '26-27 นิ้ว',
                'hips' => 'ฟรีไซส์',
                'length' => '76 - 81 ซม.',
                'rental_price' => 450.0,
                'deposit' => 100.0,
                'stock' => 1,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 147,
                'rental_count' => 22,
            ],
            [
                'category_id' => 1,
                'product_code' => 'KY-EVN-002',
                'product_name' => 'เดรสหน้าสั้นหลังยาว',
                'description' => 'เดรสสไตล์สไตล์แฟรี่คอร์ (Fairycore)',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'สีครีม',
                'available_colors' => 'สีครีม',
                'bust' => '31-33 นิ้ว',
                'waist' => '24-26 นิ้ว',
                'hips' => '34-36 นิ้ว',
                'length' => '148 ซม.',
                'rental_price' => 850.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 213,
                'rental_count' => 27,
            ],
            [
                'category_id' => 1,
                'product_code' => 'KY-EVN-003',
                'product_name' => 'เดรสสั้นรัดรูป',
                'description' => 'เดรสสั้นรัดรูปสีน้ำเงิน เนื้อผ้าซาติน',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'Detached Sleeves / Arm Warmers: ปลอกแขนที่แยกชิ้นอ',
                'available_colors' => 'Detached Sleeves / Arm Warmers: ปลอกแขนที่แยกชิ้นออกจากตัวชุด โดยมีดีเทลปลายแขนบาน',
                'bust' => '30-32 นิ้ว',
                'waist' => '23-25 นิ้ว',
                'hips' => '33-35 นิ้ว',
                'length' => '71 - 78 ซม',
                'rental_price' => 390.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 161,
                'rental_count' => 19,
            ],
            [
                'category_id' => 1,
                'product_code' => 'KY-WM-001',
                'product_name' => 'เดรสสั้นคล้องคอ',
                'description' => 'เดรสสายเดี่ยวเข้ารูปดีไซน์สายฝอ ผ้าซาตินกำมะหยี่เกรดนำเข้า โทนสีแดงเบอร์กันดีขับผิว โชว์แผ่นหลังสุดเซ็กซี่เรียบหรู',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'น้ำตาล',
                'available_colors' => 'น้ำตาล',
                'bust' => '36-38 นิ้ว',
                'waist' => '29-31 นิ้ว',
                'hips' => '39-41 นิ้ว',
                'length' => '73 - 84 ซม.',
                'rental_price' => 450.0,
                'deposit' => 100.0,
                'stock' => 0,
                'status' => 'rented',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 155,
                'rental_count' => 22,
            ],
            [
                'category_id' => 1,
                'product_code' => 'KY-WM-002',
                'product_name' => 'เดรสยาวเข้ารูป',
                'description' => 'เดรสสไตล์สายฝอ ดีไซน์กระชับสัดส่วน',
                'size' => 'S',
                'available_sizes' => 'S, M, L, XL',
                'color' => 'ดำ, แดงเบอกันดี',
                'available_colors' => 'ดำ, แดงเบอกันดี',
                'bust' => '31-33 นิ้ว',
                'waist' => '24-26 นิ้ว',
                'hips' => '34-36 นิ้ว',
                'length' => '148 ซม.',
                'rental_price' => 1800.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 210,
                'rental_count' => 24,
            ],
            [
                'category_id' => 2,
                'product_code' => 'KY-SR-001',
                'product_name' => 'เดรสซาตินหวานละมุน',
                'description' => 'เดรสหวานพริ้ว ผ้าซาตินเงางามสีชมพูนม ลุคคุณหนูหวานละมุน เหมาะสำหรับงานเลี้ยง ดินเนอร์ หรืองานแต่งงาน',
                'size' => 'S',
                'available_sizes' => 'S, M, L, XL',
                'color' => 'สีชมพูนม',
                'available_colors' => 'สีชมพูนม',
                'bust' => '32-35 นิ้ว',
                'waist' => '25-28 นิ้ว',
                'hips' => '35-38 นิ้ว',
                'length' => '110 ซม.',
                'rental_price' => 1200.0,
                'deposit' => 100.0,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 98,
                'rental_count' => 12,
            ],
            [
                'category_id' => 2,
                'product_code' => 'KY-SR-002',
                'product_name' => 'เดรสคล้องคอลายผีเสื้อพาสเทลสายหวาน',
                'description' => 'เดรสยาวผ้าชีฟองพริ้วไหว ลายผีเสื้อพาสเทลอ่อนหวาน คัตติ้งระบายชั้นๆ ให้ความรู้สึกน่ารัก อบอุ่น สไตล์ Sweet Romance',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'สีขาวครีม',
                'available_colors' => 'สีขาวครีม',
                'bust' => '31-33 นิ้ว',
                'waist' => '24-26 นิ้ว',
                'hips' => 'ฟรีไซซ์',
                'length' => '130 ซม.',
                'rental_price' => 1100.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 89,
                'rental_count' => 10,
            ],
            [
                'category_id' => 3,
                'product_code' => 'KY-MC-001',
                'product_name' => 'มินิเดรสปักเลื่อมระยิบระยับสายปาร์ตี้',
                'description' => 'มินิเดรสสั้นดีไซน์ชิค ปักเลื่อมระยิบระยับทั้งตัว คล่องตัว ทรงเข้ารูปสวยเป๊ะ เหมาะสำหรับงานปาร์ตี้สังสรรค์ยามค่ำคืน',
                'size' => 'S',
                'available_sizes' => 'S, M, L, XL',
                'color' => 'ดำชิมเมอร์',
                'available_colors' => 'ดำชิมเมอร์',
                'bust' => '31-34 นิ้ว',
                'waist' => '24-27 นิ้ว',
                'hips' => '34-37 นิ้ว',
                'length' => '82 ซม.',
                'rental_price' => 850.0,
                'deposit' => 100.0,
                'stock' => 5,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 177,
                'rental_count' => 24,
            ],
            [
                'category_id' => 3,
                'product_code' => 'KY-MC-002',
                'product_name' => 'มินิเดรสเกาะอกทรงชิคเว้าเอว',
                'description' => 'มินิเดรสเกาะอกทรงชิค มีดีเทลเว้าเอวเล็กน้อยเพิ่มความเก๋และเปรี้ยวเท่ สวมใส่ง่าย เข้าได้กับทุกโอกาส',
                'size' => 'S',
                'available_sizes' => 'S, M, L, XL',
                'color' => 'สีครีม',
                'available_colors' => 'สีครีม',
                'bust' => '32-35 นิ้ว',
                'waist' => '25-28 นิ้ว',
                'hips' => '35-38 นิ้ว',
                'length' => '85 ซม.',
                'rental_price' => 2500.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 136,
                'rental_count' => 16,
            ],
            [
                'category_id' => 4,
                'product_code' => 'KY-TOP-001',
                'product_name' => 'เสื้อครอปคล้องคอผ้าซาตินหรูหรา',
                'description' => 'เสื้อครอปคล้องคอผ้าซาตินเนื้อหนา คัตติ้งเน้นสัดส่วนเพรียวสวย แมตช์คู่กับกระโปรงหรือกางเกงเพิ่มความหรูหรา',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'สีขาว',
                'available_colors' => 'สีขาว',
                'bust' => '31-34 นิ้ว',
                'waist' => '24-26 นิ้ว',
                'hips' => 'N/A',
                'length' => '40 ซม.',
                'rental_price' => 650.0,
                'deposit' => 100.0,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 110,
                'rental_count' => 10,
            ],
            [
                'category_id' => 4,
                'product_code' => 'KY-TOP-002',
                'product_name' => 'เสื้อปาดไหล่แต่งขนนก',
                'description' => 'เสื้อท็อปส์ปาดไหล่แต่งขนนกพรีเมียมรอบอก ให้ลุคโดดเด่น สวยหรูสง่างามสไตล์ Elegant Tops',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'ดำชาร์โคล',
                'available_colors' => 'ดำชาร์โคล',
                'bust' => '32-35 นิ้ว',
                'waist' => '25-28 นิ้ว',
                'hips' => 'N/A',
                'length' => '42 ซม.',
                'rental_price' => 750.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 95,
                'rental_count' => 14,
            ],
            [
                'category_id' => 5,
                'product_code' => 'KY-SKT-001',
                'product_name' => 'กระโปรงยาวทรงหางปลาผ้าซาติน',
                'description' => 'กระโปรงเอวสูงทรงหางปลาผ้าซาตินเงางาม ช่วยเน้นทรวดทรงเอวเอสและสะโพกสวย สง่างามสไตล์ Feminine Skirts',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'แชมเปญ',
                'available_colors' => 'แชมเปญ',
                'bust' => 'N/A',
                'waist' => '25-27 นิ้ว',
                'hips' => '35-38 นิ้ว',
                'length' => '105 ซม.',
                'rental_price' => 700.0,
                'deposit' => 100.0,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 140,
                'rental_count' => 20,
            ],
            [
                'category_id' => 5,
                'product_code' => 'KY-SKT-002',
                'product_name' => 'กระโปรงอัดพรีทเมทัลลิกทรงยาวพริ้ว',
                'description' => 'กระโปรง midi อัดพรีทผ้าเมทัลลิกเงาวาว พริ้วไหวสวยงามตามการเคลื่อนไหว สวมใส่สบาย แมตช์ง่ายกับเสื้อทุกแบบ',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'น้ำตาล',
                'available_colors' => 'น้ำตาล',
                'bust' => 'N/A',
                'waist' => '24-32 นิ้ว (เอวยางยืด)',
                'hips' => 'ฟรีไซซ์',
                'length' => '85 ซม.',
                'rental_price' => 600.0,
                'deposit' => 100.0,
                'stock' => 5,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 79,
                'rental_count' => 8,
            ],
            [
                'category_id' => 6,
                'product_code' => 'KY-PNT-001',
                'product_name' => 'กางเกงยีนส์ขาสั้น',
                'description' => 'กางเกงยีนส์ขาสั้นเอวต่ำคาร์โกแต่งเข็มขัดคาวบอยสไตล์ Y2K',
                'size' => 'S',
                'available_sizes' => 'S, M, L, XL',
                'color' => 'น้ำตาล',
                'available_colors' => 'น้ำตาล',
                'bust' => 'N/A',
                'waist' => '30-32 นิ้ว',
                'hips' => '39-41 นิ้ว',
                'length' => '102 ซม.',
                'rental_price' => 450.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 107,
                'rental_count' => 11,
            ],
            [
                'category_id' => 6,
                'product_code' => 'KY-PNT-002',
                'product_name' => 'กางเกงกระโปรงยีนส์สั้น',
                'description' => 'กระโปรงยีนส์มินิสเกิร์ตสไตล์ Y2K วินเทจ',
                'size' => 'S',
                'available_sizes' => 'S, M, L, XL',
                'color' => 'ฟ้ายีนส์หม่น',
                'available_colors' => 'ฟ้ายีนส์หม่น',
                'bust' => 'N/A',
                'waist' => '26-28 นิ้ว',
                'hips' => '36-39 นิ้ว',
                'length' => '104 ซม.',
                'rental_price' => 750.0,
                'deposit' => 100.0,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 120,
                'rental_count' => 16,
            ],
            [
                'category_id' => 7,
                'product_code' => 'KY-SHO-001',
                'product_name' => 'รองเท้าส้นสูงหัวแหลม',
                'description' => 'รองเท้าส้นสูงทรงหัวแหลม สวมใส่สบาย เสริมบุคลิกภาพให้ดูสง่างาม',
                'size' => 'S',
                'available_sizes' => 'S, M, L',
                'color' => 'ขาวเบจ',
                'available_colors' => 'ขาวเบจ',
                'bust' => 'N/A',
                'waist' => 'N/A',
                'hips' => 'N/A',
                'length' => 'ส้นสูง 3.5 นิ้ว',
                'rental_price' => 5000.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 160,
                'rental_count' => 25,
            ],
            [
                'category_id' => 7,
                'product_code' => 'KY-SHO-002',
                'product_name' => 'รองเท้าส้นสูง',
                'description' => 'รองเท้าส้นสูง แบรนด์ YSL เรียบหรูดูแพง สวมใส่ง่าย แมตช์ได้กับเดรสทุกแบบ',
                'size' => 'S',
                'available_sizes' => 'S, M, L',
                'color' => 'ดำคลาสสิก',
                'available_colors' => 'ดำคลาสสิก',
                'bust' => 'N/A',
                'waist' => 'N/A',
                'hips' => 'N/A',
                'length' => 'ส้นสูง 3 นิ้ว',
                'rental_price' => 5500.0,
                'deposit' => 100.0,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 90,
                'rental_count' => 12,
            ],
            [
                'category_id' => 8,
                'product_code' => 'KY-BAG-001',
                'product_name' => 'กระเป๋า Chanel 11.12 ทรงคลาสสิก หนังแกะสีดำ อะไหล่ทอง',
                'description' => 'ปรับสายโซ่เป็นสายคู่สะพายไหล่ขนาบลำตัวเพื่อความโก้หรูเป็นทางการในวันทำงานหรือออกงานกลางคืน หรือปรับเป็นสายเดี่ยวสะพายข้างก็ได้',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'ดำ',
                'available_colors' => 'ดำ',
                'bust' => 'N/A',
                'waist' => 'N/A',
                'hips' => 'N/A',
                'length' => '20 x 12 ซม.',
                'rental_price' => 550.0,
                'deposit' => 100.0,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 180,
                'rental_count' => 30,
            ],
            [
                'category_id' => 8,
                'product_code' => 'KY-BAG-002',
                'product_name' => 'กระเป๋า Mini Lady Dior',
                'description' => 'ไซส์ Mini ยอดนิยม ขนาดกะทัดรัด ถือแล้วดูน่ารัก พรางหุ่นให้ดูเพรียวสวย',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'ขาวมุก',
                'available_colors' => 'ขาวมุก',
                'bust' => 'N/A',
                'waist' => 'N/A',
                'hips' => 'N/A',
                'length' => '18 x 10 ซม.',
                'rental_price' => 5000.0,
                'deposit' => 100.0,
                'stock' => 2,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 140,
                'rental_count' => 18,
            ],
            [
                'category_id' => 5,
                'product_code' => 'KY-DRS-0025',
                'product_name' => 'กระโปรงมินิสเกิร์ตจับเดรปห้อยชายสไตล์กรีกโรมัน',
                'description' => 'เผยเสน่ห์ความสง่างามที่เซ็กซี่สะกดทุกสายตากับกระโปรงดีไซน์ลักชูรีตัวแม่!',
                'size' => 'Free Size',
                'available_sizes' => 'Free Size',
                'color' => 'ขาวเบจ',
                'available_colors' => 'ขาวเบจ',
                'bust' => null,
                'waist' => null,
                'hips' => null,
                'length' => null,
                'rental_price' => 350.0,
                'deposit' => 100.0,
                'stock' => 1,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => false,
                'is_new' => false,
                'views_count' => 5,
                'rental_count' => 3,
            ],
        ];

        $keptProductCodes = [];
        $productIdMap = [];

        foreach ($productsData as $productData) {
            $product = Product::updateOrCreate(
                ['product_code' => $productData['product_code']],
                $productData
            );

            $keptProductCodes[] = $product->product_code;
            $productIdMap[$product->product_code] = $product->product_id;
        }

        // 3. Product Images ปัจจุบัน
        $imagesData = [
            1 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/1.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/2.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/3.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/5.jpg',
                    'is_main' => true,
                    'color_name' => 'สีขาวงาช้าง',
                ],
            ],
            2 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/4.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/5.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/1.jpg',
                    'is_main' => true,
                    'color_name' => 'สีครีม',
                ],
            ],
            8 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/6.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/7.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/6.jpg',
                    'is_main' => true,
                    'color_name' => 'Detached Sleeves / Arm Warmers: ปลอกแขนที่แยกชิ้นออกจากตัวชุด โดยมีดีเทลปลายแขนบาน',
                ],
            ],
            9 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/8.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/9.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/3.jpg',
                    'is_main' => true,
                    'color_name' => 'น้ำตาล',
                ],
            ],
            10 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/10.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/11.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/2.jpg',
                    'is_main' => true,
                    'color_name' => 'ดำ',
                ],
                [
                    'image_path' => 'products/ppXzNuFTE35EnKkVPgMiVpzSaNyu5o2whoPKxm4r.jpg',
                    'is_main' => false,
                    'color_name' => 'แดงเบอกันดี',
                ],
            ],
            11 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/12.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/13.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/8.jpg',
                    'is_main' => true,
                    'color_name' => 'สีชมพูนม',
                ],
            ],
            12 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/14.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/7.jpg',
                    'is_main' => true,
                    'color_name' => 'สีขาวครีม',
                ],
            ],
            13 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/15.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/9.jpg',
                    'is_main' => true,
                    'color_name' => 'ดำชิมเมอร์',
                ],
            ],
            14 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/16.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/10.jpg',
                    'is_main' => true,
                    'color_name' => 'สีครีม',
                ],
            ],
            15 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/17.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/13.jpg',
                    'is_main' => true,
                    'color_name' => 'สีขาว',
                ],
            ],
            16 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/18.jpg',
                    'is_main' => false,
                    'color_name' => null,
                ],
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/12.jpg',
                    'is_main' => true,
                    'color_name' => 'ดำชาร์โคล',
                ],
            ],
            17 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/17.jpg',
                    'is_main' => true,
                    'color_name' => 'แชมเปญ',
                ],
            ],
            18 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/16.jpg',
                    'is_main' => true,
                    'color_name' => 'น้ำตาล',
                ],
            ],
            19 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/14.jpg',
                    'is_main' => true,
                    'color_name' => 'น้ำตาล',
                ],
            ],
            20 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/15.jpg',
                    'is_main' => true,
                    'color_name' => 'ฟ้ายีนส์หม่น',
                ],
            ],
            21 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/22.jpg',
                    'is_main' => true,
                    'color_name' => 'ขาวเบจ',
                ],
            ],
            22 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/21.jpg',
                    'is_main' => true,
                    'color_name' => 'ดำคลาสสิก',
                ],
            ],
            23 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/20.jpg',
                    'is_main' => true,
                    'color_name' => 'ดำ',
                ],
            ],
            24 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/19.jpg',
                    'is_main' => true,
                    'color_name' => 'ขาวมุก',
                ],
            ],
            25 => [
                [
                    'image_path' => 'https://raw.githubusercontent.com/Oilapatsara/kyrix/main/public/images/dresses/11.jpg',
                    'is_main' => true,
                    'color_name' => null,
                ],
            ],
        ];

        foreach ($productsData as $productData) {
            $productId = $productIdMap[$productData['product_code']];
            $productImages = $imagesData[$productId] ?? [];

            // ลบรูปเดิมของสินค้านั้นก่อน แล้วใส่ชุดรูปที่ตรงกับฐานข้อมูลปัจจุบัน
            ProductImage::where('product_id', $productId)->delete();

            foreach ($productImages as $image) {
                ProductImage::create([
                    'product_id' => $productId,
                    'image_path' => $image['image_path'],
                    'is_main' => $image['is_main'],
                    'color_name' => $image['color_name'],
                    'created_at' => now(),
                ]);
            }
        }

        // 4. ลบสินค้าเก่าที่ไม่มีรายการเช่าผูกอยู่
        $oldProductIds = Product::whereNotIn('product_code', $keptProductCodes)->pluck('product_id');

        if ($oldProductIds->isNotEmpty()) {
            $usedProductIds = \App\Models\RentalDetail::whereIn('product_id', $oldProductIds)
                ->pluck('product_id')
                ->unique();

            $deletableIds = $oldProductIds->diff($usedProductIds);

            if ($deletableIds->isNotEmpty()) {
                ProductImage::whereIn('product_id', $deletableIds)->delete();
                Product::whereIn('product_id', $deletableIds)->delete();
            }
        }

        $this->command?->info('DressRentalCustomerSeeder: sync categories, products and images from current database completed.');
    }
}
