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
        // 1. Create, update or delete Categories
        $categoriesData = [
            [
                'category_id'   => 1,
                'category_name' => 'Western Muse — เดรสสายฝอ',
                'description'   => 'เดรสสไตล์สายฝอ ดีไซน์เรียบหรู เซ็กซี่ มั่นใจ โดดเด่นทุกงาน',
                'status'        => 'active',
            ],
            [
                'category_id'   => 2,
                'category_name' => 'Sweet Romance — เดรสหวาน',
                'description'   => 'เดรสสไตล์หวาน น่ารัก อ่อนหวาน เหมาะสำหรับงานเลี้ยงและโอกาสพิเศษ',
                'status'        => 'active',
            ],
            [
                'category_id'   => 3,
                'category_name' => 'Mini Chic — มินิเดรส',
                'description'   => 'มินิเดรสเก๋ๆ คล่องตัว สวมใส่ง่าย เหมาะสำหรับปาร์ตี้และสังสรรค์',
                'status'        => 'active',
            ],
            [
                'category_id'   => 4,
                'category_name' => 'Elegant Tops — เสื้อ & ท็อปส์',
                'description'   => 'เสื้อและท็อปส์ดีไซน์หรูหรา แมตช์ง่าย สวมใส่ได้หลายโอกาส',
                'status'        => 'active',
            ],
            [
                'category_id'   => 5,
                'category_name' => 'Feminine Skirts — กระโปรง',
                'description'   => 'กระโปรงทรงสวย ตัดเย็บประณีต เพิ่มเสน่ห์และความอ่อนหวาน',
                'status'        => 'active',
            ],
            [
                'category_id'   => 6,
                'category_name' => 'Modern Pants — กางเกง',
                'description'   => 'กางเกงทรงโมเดิร์น ดีไซน์ทันสมัย สวมใส่สบายและสง่างาม',
                'status'        => 'active',
            ],
            [
                'category_id'   => 7,
                'category_name' => 'Elegant Shoes — รองเท้า',
                'description'   => 'รองเท้าส้นสูงและรองเท้าดีไซน์หรู เติมเต็มลุคให้สมบูรณ์แบบ',
                'status'        => 'active',
            ],
            [
                'category_id'   => 8,
                'category_name' => 'Luxury Bags — กระเป๋า',
                'description'   => 'กระเป๋าถือและออกงานระดับแบรนด์พรีเมียม เสริมความหรูหรา',
                'status'        => 'active',
            ],
        ];

        $keptIds = [];
        $categories = [];
        foreach ($categoriesData as $cat) {
            $catId = $cat['category_id'] ?? null;
            if ($catId) {
                $category = Category::updateOrCreate(['category_id' => $catId], $cat);
            } else {
                $category = Category::updateOrCreate(['category_name' => $cat['category_name']], $cat);
            }
            $keptIds[] = $category->category_id;
            $categories[] = $category;
        }

        // ลบหมวดหมู่ใน DB ที่ไม่มีใน $categoriesData (เฉพาะหมวดหมู่ที่ไม่มีสินค้าผูกอยู่)
        $deletableIds = Category::whereNotIn('category_id', $keptIds)->whereDoesntHave('products')->pluck('category_id');
        if ($deletableIds->isNotEmpty()) {
            Category::whereIn('category_id', $deletableIds)->delete();
        }

        // 2. Products Data (2 items per category = 16 total products)
        $productsData = [
            // Western Muse — เดรสสายฝอ (Category 0)
            [
                'category_id' => $categories[0]->category_id,
                'product_code' => 'KY-WM-001',
                'product_name' => 'Royal Burgundy Velvet Gown (เดรสสายเดี่ยวซาตินสายฝอสีแดงเบอร์กันดี)',
                'description' => 'เดรสสายเดี่ยวเข้ารูปดีไซน์สายฝอ ผ้าซาตินกำมะหยี่เกรดนำเข้า โทนสีแดงเบอร์กันดีขับผิว โชว์แผ่นหลังสุดเซ็กซี่เรียบหรู',
                'size' => 'M',
                'color' => 'แดงเบอร์กันดี (Burgundy)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'แดงเบอร์กันดี, ดำคลาสสิก, เขียวเอเมอรัลด์',
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
                ],
            ],
            [
                'category_id' => $categories[0]->category_id,
                'product_code' => 'KY-WM-002',
                'product_name' => 'Golden Champagne Sequin Backless Dress (เดรสปักเลื่อมโชว์หลังสีทองแชมเปญ)',
                'description' => 'เดรสปักเลื่อมชิมเมอร์เล่นแสงไฟสไตล์สายฝอ ดีไซน์เปิดหลังทรงคอร์เซ็ตกระชับสัดส่วน ให้ลุคเฉิดฉายโดดเด่นสไตล์ Western Muse',
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
                    'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // Sweet Romance — เดรสหวาน (Category 1)
            [
                'category_id' => $categories[1]->category_id,
                'product_code' => 'KY-SR-001',
                'product_name' => 'Rose Gold Satin Ribbon Dress (เดรสโบว์ซาตินสีโรสโกลด์หวานละมุน)',
                'description' => 'เดรสหวานพริ้วดีไซน์แต่งโบว์ช่วงไหล่ ผ้าซาตินเงางามสีโรสโกลด์ ลุคคุณหนูหวานละมุน เหมาะสำหรับงานเลี้ยง ดินเนอร์ หรืองานแต่งงาน',
                'size' => 'M',
                'color' => 'โรสโกลด์ (Rose Gold)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'โรสโกลด์, ชมพูดัสตี้, ครีมงาช้าง',
                'bust' => '32-35 นิ้ว',
                'waist' => '25-28 นิ้ว',
                'hips' => '35-38 นิ้ว',
                'length' => '110 ซม.',
                'rental_price' => 1200.00,
                'deposit' => 1500.00,
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
                'category_id' => $categories[1]->category_id,
                'product_code' => 'KY-SR-002',
                'product_name' => 'Pastel Chiffon Floral Romantic Dress (เดรสชีฟองลายดอกไม้พาสเทลสายหวาน)',
                'description' => 'เดรสยาวผ้าชีฟองพริ้วไหว ลายดอกไม้พาสเทลอ่อนหวาน คัตติ้งระบายชั้นๆ ให้ความรู้สึกน่ารัก อบอุ่น สไตล์ Sweet Romance',
                'size' => 'S',
                'color' => 'ชมพูพาสเทล (Pastel Pink)',
                'available_sizes' => 'S, M',
                'available_colors' => 'ชมพูพาสเทล, ฟ้าพีช',
                'bust' => '31-33 นิ้ว',
                'waist' => '24-26 นิ้ว',
                'hips' => 'ฟรีไซซ์',
                'length' => '130 ซม.',
                'rental_price' => 1100.00,
                'deposit' => 1500.00,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 85,
                'rental_count' => 9,
                'images' => [
                    'https://images.unsplash.com/photo-1525457136159-8878648a7ad0?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // Mini Chic — มินิเดรส (Category 2)
            [
                'category_id' => $categories[2]->category_id,
                'product_code' => 'KY-MC-001',
                'product_name' => 'Sparkling Sequin Party Mini Dress (มินิเดรสปักเลื่อมระยิบระยับสายปาร์ตี้)',
                'description' => 'มินิเดรสสั้นดีไซน์ชิค ปักเลื่อมระยิบระยับทั้งตัว คล่องตัว ทรงเข้ารูปสวยเป๊ะ เหมาะสำหรับงานปาร์ตี้สังสรรค์ยามค่ำคืน',
                'size' => 'S',
                'color' => 'เงินสปาร์คเกิล (Sparkle Silver)',
                'available_sizes' => 'S, M',
                'available_colors' => 'เงินสปาร์คเกิล, ดำชิมเมอร์',
                'bust' => '31-34 นิ้ว',
                'waist' => '24-27 นิ้ว',
                'hips' => '34-37 นิ้ว',
                'length' => '82 ซม.',
                'rental_price' => 850.00,
                'deposit' => 1200.00,
                'stock' => 5,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 175,
                'rental_count' => 22,
                'images' => [
                    'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[2]->category_id,
                'product_code' => 'KY-MC-002',
                'product_name' => 'Chic Velvet Cut-out Mini Dress (มินิเดรสผ้ากำมะหยี่ทรงชิคเว้าเอว)',
                'description' => 'มินิเดรสผ้ากำมะหยี่สีดำทรงชิค มีดีเทลเว้าเอวเล็กน้อยเพิ่มความเก๋และเปรี้ยวเท่ สวมใส่ง่าย เข้าได้กับทุกโอกาส',
                'size' => 'M',
                'color' => 'ดำคลาสสิก (Classic Black)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'ดำคลาสสิก, แดงไวน์',
                'bust' => '32-35 นิ้ว',
                'waist' => '25-28 นิ้ว',
                'hips' => '35-38 นิ้ว',
                'length' => '85 ซม.',
                'rental_price' => 900.00,
                'deposit' => 1200.00,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 130,
                'rental_count' => 15,
                'images' => [
                    'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // Elegant Tops — เสื้อ & ท็อปส์ (Category 3)
            [
                'category_id' => $categories[3]->category_id,
                'product_code' => 'KY-TOP-001',
                'product_name' => 'Silk Corset Bustier Top (เสื้อครอปคอร์เซ็ตผ้าซาตินหรูหรา)',
                'description' => 'เสื้อท็อปส์คอร์เซ็ตผ้าซาตินเนื้อหนา คัตติ้งเน้นสัดส่วนเพรียวสวย แมตช์คู่กับกระโปรงหรือกางเกงเพิ่มความหรูหรา',
                'size' => 'S',
                'color' => 'ขาวครีม (Cream White)',
                'available_sizes' => 'S, M',
                'available_colors' => 'ขาวครีม, ดำ, โรสโกลด์',
                'bust' => '31-34 นิ้ว',
                'waist' => '24-26 นิ้ว',
                'hips' => 'N/A',
                'length' => '40 ซม.',
                'rental_price' => 650.00,
                'deposit' => 1000.00,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 110,
                'rental_count' => 10,
                'images' => [
                    'https://images.unsplash.com/photo-1594552072238-b8a33785b261?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[3]->category_id,
                'product_code' => 'KY-TOP-002',
                'product_name' => 'Off-Shoulder Feather Satin Top (เสื้อท็อปส์ปาดไหล่แต่งขนนกพรีเมียม)',
                'description' => 'เสื้อท็อปส์ปาดไหล่ผ้าซาตินแต่งขนนกพรีเมียมรอบอก ให้ลุคโดดเด่น สวยหรูสง่างามสไตล์ Elegant Tops',
                'size' => 'M',
                'color' => 'ดำชาร์โคล (Charcoal Black)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'ดำชาร์โคล, ขาวออฟไวท์',
                'bust' => '32-35 นิ้ว',
                'waist' => '25-28 นิ้ว',
                'hips' => 'N/A',
                'length' => '42 ซม.',
                'rental_price' => 750.00,
                'deposit' => 1000.00,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 95,
                'rental_count' => 14,
                'images' => [
                    'https://images.unsplash.com/photo-1549416864-228c7f24cf7a?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // Feminine Skirts — กระโปรง (Category 4)
            [
                'category_id' => $categories[4]->category_id,
                'product_code' => 'KY-SKT-001',
                'product_name' => 'High-Waist Satin Mermaid Skirt (กระโปรงยาวทรงหางปลาผ้าซาติน)',
                'description' => 'กระโปรงเอวสูงทรงหางปลาผ้าซาตินเงางาม ช่วยเน้นทรวดทรงเอวเอสและสะโพกสวย สง่างามสไตล์ Feminine Skirts',
                'size' => 'M',
                'color' => 'เขียวเสจ (Sage Green)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'เขียวเสจ, แชมเปญ, ดำ',
                'bust' => 'N/A',
                'waist' => '25-27 นิ้ว',
                'hips' => '35-38 นิ้ว',
                'length' => '105 ซม.',
                'rental_price' => 700.00,
                'deposit' => 1000.00,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 140,
                'rental_count' => 20,
                'images' => [
                    'https://images.unsplash.com/photo-1568252542512-9fe8fe9c87bb?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[4]->category_id,
                'product_code' => 'KY-SKT-002',
                'product_name' => 'Pleated Metallic Midi Skirt (กระโปรงอัดพรีทเมทัลลิกทรงยาวพริ้วงดงาม)',
                'description' => 'กระโปรง midi อัดพรีทผ้าเมทัลลิกเงาวาว พริ้วไหวสวยงามตามการเคลื่อนไหว สวมใส่สบาย แมตช์ง่ายกับเสื้อทุกแบบ',
                'size' => 'Free Size',
                'color' => 'ทองเมทัลลิก (Metallic Gold)',
                'available_sizes' => 'Free Size (S-XL)',
                'available_colors' => 'ทองเมทัลลิก, เงินเมทัลลิก',
                'bust' => 'N/A',
                'waist' => '24-32 นิ้ว (เอวยางยืด)',
                'hips' => 'ฟรีไซซ์',
                'length' => '85 ซม.',
                'rental_price' => 600.00,
                'deposit' => 1000.00,
                'stock' => 5,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 78,
                'rental_count' => 8,
                'images' => [
                    'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // Modern Pants — กางเกง (Category 5)
            [
                'category_id' => $categories[5]->category_id,
                'product_code' => 'KY-PNT-001',
                'product_name' => 'Midnight Blue Velvet Tailored Pants (กางเกงขายาวผ้ากำมะหยี่สีกรมท่า)',
                'description' => 'กางเกงขายาวผ้ากำมะหยี่เนื้อดี ทรงกระบอกสมาร์ทเท่ ทันสมัย คัตติ้งเนี้ยบ สไตล์ Modern Pants',
                'size' => 'L',
                'color' => 'น้ำเงินมิดไนท์บลู (Midnight Blue)',
                'available_sizes' => 'M, L, XL',
                'available_colors' => 'น้ำเงินมิดไนท์บลู, ดำคลาสสิก',
                'bust' => 'N/A',
                'waist' => '30-32 นิ้ว',
                'hips' => '39-41 นิ้ว',
                'length' => '102 ซม.',
                'rental_price' => 800.00,
                'deposit' => 1200.00,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 105,
                'rental_count' => 11,
                'images' => [
                    'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[5]->category_id,
                'product_code' => 'KY-PNT-002',
                'product_name' => 'High-Waisted Satin Wide-Leg Trousers (กางเกงเอวสูงผ้าซาตินทรงกระบอกใหญ่)',
                'description' => 'กางเกงขายาวเอวสูงผ้าซาตินพริ้วทรง Wide-leg ทรงสวยช่วยให้ขาดูยาวเพรียว สวมใส่สบาย เรียบหรูดูดี',
                'size' => 'M',
                'color' => 'ครีมเบจ (Cream Beige)',
                'available_sizes' => 'S, M, L',
                'available_colors' => 'ครีมเบจ, ดำ, นู้ด',
                'bust' => 'N/A',
                'waist' => '26-28 นิ้ว',
                'hips' => '36-39 นิ้ว',
                'length' => '104 ซม.',
                'rental_price' => 750.00,
                'deposit' => 1000.00,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 120,
                'rental_count' => 16,
                'images' => [
                    'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // Elegant Shoes — รองเท้า (Category 6)
            [
                'category_id' => $categories[6]->category_id,
                'product_code' => 'KY-SHO-001',
                'product_name' => 'Crystal Stiletto High Heels (รองเท้าส้นสูงหัวแหลมประดับคริสตัล)',
                'description' => 'รองเท้าส้นสูง 3.5 นิ้ว ทรงหัวแหลมประดับคริสตัลระยิบระยับ สวมใส่สบาย เสริมบุคลิกภาพให้ดูสง่างามสไตล์ Elegant Shoes',
                'size' => '38',
                'color' => 'เงินคริสตัล (Crystal Silver)',
                'available_sizes' => '36, 37, 38, 39',
                'available_colors' => 'เงินคริสตัล, ทองแชมเปญ',
                'bust' => 'N/A',
                'waist' => 'N/A',
                'hips' => 'N/A',
                'length' => 'ส้นสูง 3.5 นิ้ว',
                'rental_price' => 500.00,
                'deposit' => 800.00,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 160,
                'rental_count' => 25,
                'images' => [
                    'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[6]->category_id,
                'product_code' => 'KY-SHO-002',
                'product_name' => 'Strappy Champagne Metallic Heels (รองเท้าส้นสูงสายพันข้อเมทัลลิกสีทอง)',
                'description' => 'รองเท้าส้นสูงดีไซน์สายพันข้อสีทองเมทัลลิก เรียบหรูดูแพง สวมใส่ง่าย แมตช์ได้กับเดรสทุกแบบ',
                'size' => '37',
                'color' => 'ทองเมทัลลิก (Metallic Gold)',
                'available_sizes' => '36, 37, 38',
                'available_colors' => 'ทองเมทัลลิก, โรสโกลด์',
                'bust' => 'N/A',
                'waist' => 'N/A',
                'hips' => 'N/A',
                'length' => 'ส้นสูง 3 นิ้ว',
                'rental_price' => 450.00,
                'deposit' => 800.00,
                'stock' => 4,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => false,
                'is_new' => true,
                'views_count' => 90,
                'rental_count' => 12,
                'images' => [
                    'https://images.unsplash.com/photo-1560343776-97e7d202ff0e?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // Luxury Bags — กระเป๋า (Category 7)
            [
                'category_id' => $categories[7]->category_id,
                'product_code' => 'KY-BAG-001',
                'product_name' => 'Luxury Gold Chain Evening Clutch (กระเป๋าคลัทช์ออกงานสายโซ่สีทองหรู)',
                'description' => 'กระเป๋าคลัทช์ออกงานสีทองประดับสายโซ่เรียบหรู จุของได้ครบ เหมาะสำหรับงานกาล่า งานแต่งงาน และปาร์ตี้',
                'size' => 'Standard Clutch',
                'color' => 'ทองกลิตเตอร์ (Glitter Gold)',
                'available_sizes' => 'Standard (20x12 cm)',
                'available_colors' => 'ทองกลิตเตอร์, เงินกลิตเตอร์, ดำ',
                'bust' => 'N/A',
                'waist' => 'N/A',
                'hips' => 'N/A',
                'length' => '20 x 12 ซม.',
                'rental_price' => 550.00,
                'deposit' => 1000.00,
                'stock' => 3,
                'status' => 'available',
                'is_featured' => true,
                'is_popular' => true,
                'is_new' => true,
                'views_count' => 180,
                'rental_count' => 30,
                'images' => [
                    'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'category_id' => $categories[7]->category_id,
                'product_code' => 'KY-BAG-002',
                'product_name' => 'Pearl Embellished Mini Luxury Bag (กระเป๋าถือทรงมินิประดับมุกไฮเอนด์)',
                'description' => 'กระเป๋าถือทรงมินิประดับมุกแท้สังเคราะห์รอบตัว ดีไซน์หรูหรา น่ารัก เสริมความโดดเด่นทุกโอกาสสไตล์ Luxury Bags',
                'size' => 'Mini',
                'color' => 'ขาวมุก (Pearl White)',
                'available_sizes' => 'Mini (18x10 cm)',
                'available_colors' => 'ขาวมุก, ครีมงาช้าง',
                'bust' => 'N/A',
                'waist' => 'N/A',
                'hips' => 'N/A',
                'length' => '18 x 10 ซม.',
                'rental_price' => 600.00,
                'deposit' => 1000.00,
                'stock' => 2,
                'status' => 'available',
                'is_featured' => false,
                'is_popular' => true,
                'is_new' => false,
                'views_count' => 140,
                'rental_count' => 18,
                'images' => [
                    'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?w=800&auto=format&fit=crop&q=80',
                ],
            ],
        ];

        $keptCodes = [];
        $createdProducts = [];
        foreach ($productsData as $pData) {
            $imgs = $pData['images'];
            unset($pData['images']);

            $product = Product::updateOrCreate(['product_code' => $pData['product_code']], $pData);
            $keptCodes[] = $product->product_code;
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

        // ลบรหัสสินค้าตัวอย่างเก่าที่ไม่อยู่ใน $productsData แล้วออก (ถ้าไม่มีรายการเช่าผูกอยู่)
        $oldProductIds = Product::whereNotIn('product_code', $keptCodes)->pluck('product_id');
        if ($oldProductIds->isNotEmpty()) {
            $usedProductIds = RentalDetail::whereIn('product_id', $oldProductIds)->pluck('product_id');
            $deletableIds = $oldProductIds->diff($usedProductIds);
            if ($deletableIds->isNotEmpty()) {
                ProductImage::whereIn('product_id', $deletableIds)->delete();
                Product::whereIn('product_id', $deletableIds)->delete();
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
