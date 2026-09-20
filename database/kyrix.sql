-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2026 at 03:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kyrix`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Western Muse — เดรสสายฝอ', 'เดรสสไตล์สายฝอ ดีไซน์เรียบหรู เซ็กซี่ มั่นใจ โดดเด่นทุกงาน', 'active', '2026-09-12 11:08:17', '2026-09-16 00:35:18'),
(2, 'Sweet Romance — เดรสหวาน', 'เดรสสไตล์หวาน น่ารัก อ่อนหวาน เหมาะสำหรับงานเลี้ยงและโอกาสพิเศษ', 'active', '2026-09-12 11:08:17', '2026-09-16 00:35:18'),
(3, 'Mini Chic — มินิเดรส', 'มินิเดรสเก๋ๆ คล่องตัว สวมใส่ง่าย เหมาะสำหรับปาร์ตี้และสังสรรค์', 'active', '2026-09-12 11:08:17', '2026-09-16 00:35:18'),
(4, 'Elegant Tops — เสื้อ & ท็อปส์', 'เสื้อและท็อปส์ดีไซน์หรูหรา แมตช์ง่าย สวมใส่ได้หลายโอกาส', 'active', '2026-09-12 11:08:17', '2026-09-16 00:35:18'),
(5, 'Feminine Skirts — กระโปรง', 'กระโปรงทรงสวย ตัดเย็บประณีต เพิ่มเสน่ห์และความอ่อนหวาน', 'active', '2026-09-12 11:08:17', '2026-09-16 00:35:18'),
(6, 'Modern Pants — กางเกง', 'กางเกงทรงโมเดิร์น ดีไซน์ทันสมัย สวมใส่สบายและสง่างาม', 'active', '2026-09-12 11:08:17', '2026-09-16 00:35:18'),
(7, 'Elegant Shoes — รองเท้า', 'รองเท้าส้นสูงและรองเท้าดีไซน์หรู เติมเต็มลุคให้สมบูรณ์แบบ', 'active', '2026-09-16 00:35:18', '2026-09-16 00:35:18'),
(8, 'Luxury Bags — กระเป๋า', 'กระเป๋าถือและออกงานระดับแบรนด์พรีเมียม เสริมความหรูหรา', 'active', '2026-09-16 00:35:18', '2026-09-16 00:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `user_id`, `first_name`, `last_name`, `email`, `password`, `profile_image`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(5, NULL, 'อภัสรา', 'แคะมะดัน', 'aphatsara.kh@rmuti.ac.th', '$2y$12$V5eqqA7WD2Lmu/IOrftIIu3ZhB0804Eh2x3ET8Y8vZJZGl41Xz166', 'profile/5C1hSsnuVRYXLtvQKMmkbLEMyDHJRisy9QNDTsxx.jpg', '0652599072', '202/3', '2026-09-12 12:04:32', '2026-09-17 03:43:30'),
(6, NULL, 'ธนภรณ์', 'พรโคกกรวด', 'pornkhokkruad@gmail.com', '$2y$12$VJS6jVV2b9wltSELXCGc0.u87SiaZX4HygWaS6422J/ak9q.2/RCe', NULL, '0828429670', '111/2', '2026-09-12 13:07:13', '2026-09-16 02:01:48'),
(8, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-16 07:27:23', '2026-09-16 07:27:23'),
(9, NULL, 'khaofang', '-', 'praphatson290947@gmail.com', '$2y$12$idG9HZlMac20dtltsSamOOFyorM10Mvqn7CPvtnvFXldzd5L8UT2G', NULL, '0615879312', '44', '2026-09-19 03:37:21', '2026-09-19 03:37:21');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_09_12_000001_create_dress_rental_tables', 1),
(2, '2026_09_13_000002_enhance_dress_rental_customer_system', 2),
(3, '2026_09_13_000003_fix_customers_columns', 3),
(4, '2026_09_14_171053_add_social_columns_to_users_table', 4),
(5, '2026_09_14_171832_add_social_columns_to_users_table', 4),
(6, '2026_09_15_180000_add_user_id_to_customers_table', 4),
(7, '2026_09_15_210000_add_inspection_and_deposit_to_rentals_table', 4),
(8, '2026_09_16_200000_add_color_name_to_product_images_table', 5),
(9, '2026_09_16_300000_add_discount_to_rentals_table', 5),
(10, '2026_09_17_102032_add_profile_image_to_customers_table', 6),
(11, '2026_09_19_104710_create_payments_table', 7),
(12, '2026_09_19_112632_add_shipping_and_return_tracking_to_rentals_table', 8),
(13, '2026_09_19_112932_add_shipping_and_return_tracking_to_rentals_table', 8),
(14, '2026_09_20_090128_create_sessions_table', 9),
(15, '2026_09_20_094758_add_return_shipping_tracking_fields_to_rentals_table', 9);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `rental_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_ref` varchar(100) DEFAULT NULL,
  `slip_image` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `rental_id`, `amount`, `payment_method`, `status`, `payment_ref`, `slip_image`, `paid_at`, `note`, `created_at`, `updated_at`) VALUES
(1, 23, 0.00, 'qr', 'pending', NULL, 'uploads/slips/slip_1789815139_6aae6963c3949.jpg', NULL, 'ชำระเงินค่าเช่าชุด (ค่าเช่า ฿2,000.00 + มัดจำ ฿100.00 + ค่าบริการ ฿0.00)', '2026-09-19 03:52:19', '2026-09-19 03:52:19'),
(2, 24, 0.00, 'qr', 'pending', NULL, 'uploads/slips/slip_1789894531_6aaf9f8320b57.jpg', NULL, 'ชำระเงินค่าเช่าชุด (ค่าเช่า ฿1,050.00 + มัดจำ ฿100.00 + ค่าบริการ ฿0.00)', '2026-09-20 01:55:31', '2026-09-20 01:55:31');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `available_sizes` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `available_colors` varchar(150) DEFAULT NULL,
  `bust` varchar(50) DEFAULT NULL,
  `waist` varchar(50) DEFAULT NULL,
  `hips` varchar(50) DEFAULT NULL,
  `length` varchar(50) DEFAULT NULL,
  `rental_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deposit` decimal(10,2) NOT NULL DEFAULT 100.00,
  `stock` int(11) NOT NULL DEFAULT 1,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `rental_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('available','rented','maintenance','inactive') NOT NULL DEFAULT 'available',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `is_new` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_code`, `product_name`, `description`, `size`, `available_sizes`, `color`, `available_colors`, `bust`, `waist`, `hips`, `length`, `rental_price`, `deposit`, `stock`, `views_count`, `rental_count`, `status`, `is_featured`, `is_popular`, `is_new`, `created_at`, `updated_at`) VALUES
(1, 1, 'KY-EVN-001', 'มินิเดรสผ้าลูกไม้สายเดี่ยวผูกโบว์', 'มินิเดรสผ้าลูกไม้สายเดี่ยวสีขาวครีม (สไตล์ Fairycore / Coquette)', 'Free Size', 'Free Size', 'สีขาวงาช้าง', 'สีขาวงาช้าง', '32-34 นิ้ว', '26-27 นิ้ว', 'ฟรีไซส์', '76 - 81 ซม.', 450.00, 100.00, 2, 146, 21, 'available', 1, 1, 1, '2026-09-12 11:08:17', '2026-09-17 13:22:32'),
(2, 1, 'KY-EVN-002', 'เดรสหน้าสั้นหลังยาว', 'เดรสสไตล์สไตล์แฟรี่คอร์ (Fairycore)', 'Free Size', 'Free Size', 'สีครีม', 'สีครีม', '31-33 นิ้ว', '24-26 นิ้ว', '34-36 นิ้ว', '148 ซม.', 850.00, 100.00, 4, 212, 26, 'available', 1, 1, 0, '2026-09-12 11:08:17', '2026-09-17 13:37:51'),
(8, 1, 'KY-EVN-003', 'เดรสสั้นรัดรูป', 'เดรสสั้นรัดรูปสีน้ำเงิน เนื้อผ้าซาติน', 'Free Size', 'Free Size', 'Detached Sleeves / Arm Warmers: ปลอกแขนที่แยกชิ้นอ', 'Detached Sleeves / Arm Warmers: ปลอกแขนที่แยกชิ้นออกจากตัวชุด โดยมีดีเทลปลายแขนบาน', '30-32 นิ้ว', '23-25 นิ้ว', '33-35 นิ้ว', '71 - 78 ซม', 390.00, 100.00, 3, 161, 19, 'available', 1, 1, 1, '2026-09-12 11:08:17', '2026-09-17 13:16:15'),
(9, 1, 'KY-WM-001', 'เดรสสั้นคล้องคอ', 'เดรสสายเดี่ยวเข้ารูปดีไซน์สายฝอ ผ้าซาตินกำมะหยี่เกรดนำเข้า โทนสีแดงเบอร์กันดีขับผิว โชว์แผ่นหลังสุดเซ็กซี่เรียบหรู', 'Free Size', 'Free Size', 'น้ำตาล', 'น้ำตาล', '36-38 นิ้ว', '29-31 นิ้ว', '39-41 นิ้ว', '73 - 84 ซม.', 450.00, 100.00, 0, 155, 22, 'rented', 1, 1, 1, '2026-09-16 00:39:05', '2026-09-17 05:04:28'),
(10, 1, 'KY-WM-002', 'เดรสยาวเข้ารูป', 'เดรสสไตล์สายฝอ ดีไซน์กระชับสัดส่วน', 'S', 'S, M, L, XL', 'ดำ, แดงเบอกันดี', 'ดำ, แดงเบอกันดี', '31-33 นิ้ว', '24-26 นิ้ว', '34-36 นิ้ว', '148 ซม.', 1800.00, 100.00, 3, 210, 24, 'available', 1, 1, 0, '2026-09-16 00:39:05', '2026-09-17 04:59:29'),
(11, 2, 'KY-SR-001', 'เดรสซาตินหวานละมุน', 'เดรสหวานพริ้ว ผ้าซาตินเงางามสีชมพูนม ลุคคุณหนูหวานละมุน เหมาะสำหรับงานเลี้ยง ดินเนอร์ หรืองานแต่งงาน', 'S', 'S, M, L, XL', 'สีชมพูนม', 'สีชมพูนม', '32-35 นิ้ว', '25-28 นิ้ว', '35-38 นิ้ว', '110 ซม.', 1200.00, 100.00, 4, 98, 12, 'available', 0, 1, 1, '2026-09-16 00:39:05', '2026-09-17 10:57:29'),
(12, 2, 'KY-SR-002', 'เดรสคล้องคอลายผีเสื้อพาสเทลสายหวาน', 'เดรสยาวผ้าชีฟองพริ้วไหว ลายผีเสื้อพาสเทลอ่อนหวาน คัตติ้งระบายชั้นๆ ให้ความรู้สึกน่ารัก อบอุ่น สไตล์ Sweet Romance', 'Free Size', 'Free Size', 'สีขาวครีม', 'สีขาวครีม', '31-33 นิ้ว', '24-26 นิ้ว', 'ฟรีไซซ์', '130 ซม.', 1100.00, 100.00, 3, 89, 10, 'available', 1, 0, 1, '2026-09-16 00:39:05', '2026-09-17 13:05:53'),
(13, 3, 'KY-MC-001', 'มินิเดรสปักเลื่อมระยิบระยับสายปาร์ตี้', 'มินิเดรสสั้นดีไซน์ชิค ปักเลื่อมระยิบระยับทั้งตัว คล่องตัว ทรงเข้ารูปสวยเป๊ะ เหมาะสำหรับงานปาร์ตี้สังสรรค์ยามค่ำคืน', 'S', 'S, M, L, XL', 'ดำชิมเมอร์', 'ดำชิมเมอร์', '31-34 นิ้ว', '24-27 นิ้ว', '34-37 นิ้ว', '82 ซม.', 850.00, 100.00, 5, 177, 24, 'available', 1, 1, 1, '2026-09-16 00:39:05', '2026-09-17 13:30:47'),
(14, 3, 'KY-MC-002', 'มินิเดรสเกาะอกทรงชิคเว้าเอว', 'มินิเดรสเกาะอกทรงชิค มีดีเทลเว้าเอวเล็กน้อยเพิ่มความเก๋และเปรี้ยวเท่ สวมใส่ง่าย เข้าได้กับทุกโอกาส', 'S', 'S, M, L, XL', 'สีครีม', 'สีครีม', '32-35 นิ้ว', '25-28 นิ้ว', '35-38 นิ้ว', '85 ซม.', 2500.00, 100.00, 3, 136, 16, 'available', 0, 1, 0, '2026-09-16 00:39:05', '2026-09-19 04:10:38'),
(15, 4, 'KY-TOP-001', 'เสื้อครอปคล้องคอผ้าซาตินหรูหรา', 'เสื้อครอปคล้องคอผ้าซาตินเนื้อหนา คัตติ้งเน้นสัดส่วนเพรียวสวย แมตช์คู่กับกระโปรงหรือกางเกงเพิ่มความหรูหรา', 'Free Size', 'Free Size', 'สีขาว', 'สีขาว', '31-34 นิ้ว', '24-26 นิ้ว', 'N/A', '40 ซม.', 650.00, 100.00, 4, 110, 10, 'available', 1, 0, 1, '2026-09-16 00:39:05', '2026-09-17 10:55:49'),
(16, 4, 'KY-TOP-002', 'เสื้อปาดไหล่แต่งขนนก', 'เสื้อท็อปส์ปาดไหล่แต่งขนนกพรีเมียมรอบอก ให้ลุคโดดเด่น สวยหรูสง่างามสไตล์ Elegant Tops', 'Free Size', 'Free Size', 'ดำชาร์โคล', 'ดำชาร์โคล', '32-35 นิ้ว', '25-28 นิ้ว', 'N/A', '42 ซม.', 750.00, 100.00, 3, 95, 14, 'available', 0, 1, 0, '2026-09-16 00:39:05', '2026-09-17 10:55:22'),
(17, 5, 'KY-SKT-001', 'กระโปรงยาวทรงหางปลาผ้าซาติน', 'กระโปรงเอวสูงทรงหางปลาผ้าซาตินเงางาม ช่วยเน้นทรวดทรงเอวเอสและสะโพกสวย สง่างามสไตล์ Feminine Skirts', 'Free Size', 'Free Size', 'แชมเปญ', 'แชมเปญ', 'N/A', '25-27 นิ้ว', '35-38 นิ้ว', '105 ซม.', 700.00, 100.00, 4, 140, 20, 'available', 1, 1, 0, '2026-09-16 00:39:05', '2026-09-17 10:54:32'),
(18, 5, 'KY-SKT-002', 'กระโปรงอัดพรีทเมทัลลิกทรงยาวพริ้ว', 'กระโปรง midi อัดพรีทผ้าเมทัลลิกเงาวาว พริ้วไหวสวยงามตามการเคลื่อนไหว สวมใส่สบาย แมตช์ง่ายกับเสื้อทุกแบบ', 'Free Size', 'Free Size', 'น้ำตาล', 'น้ำตาล', 'N/A', '24-32 นิ้ว (เอวยางยืด)', 'ฟรีไซซ์', '85 ซม.', 600.00, 100.00, 5, 79, 8, 'available', 0, 0, 1, '2026-09-16 00:39:05', '2026-09-17 11:59:46'),
(19, 6, 'KY-PNT-001', 'กางเกงยีนส์ขาสั้น', 'กางเกงยีนส์ขาสั้นเอวต่ำคาร์โกแต่งเข็มขัดคาวบอยสไตล์ Y2K', 'S', 'S, M, L, XL', 'น้ำตาล', 'น้ำตาล', 'N/A', '30-32 นิ้ว', '39-41 นิ้ว', '102 ซม.', 450.00, 100.00, 3, 107, 11, 'available', 1, 0, 1, '2026-09-16 00:39:05', '2026-09-17 10:59:10'),
(20, 6, 'KY-PNT-002', 'กางเกงกระโปรงยีนส์สั้น', 'กระโปรงยีนส์มินิสเกิร์ตสไตล์ Y2K วินเทจ', 'S', 'S, M, L, XL', 'ฟ้ายีนส์หม่น', 'ฟ้ายีนส์หม่น', 'N/A', '26-28 นิ้ว', '36-39 นิ้ว', '104 ซม.', 750.00, 100.00, 4, 120, 16, 'available', 0, 1, 0, '2026-09-16 00:39:05', '2026-09-17 10:47:19'),
(21, 7, 'KY-SHO-001', 'รองเท้าส้นสูงหัวแหลม', 'รองเท้าส้นสูงทรงหัวแหลม สวมใส่สบาย เสริมบุคลิกภาพให้ดูสง่างาม', 'S', 'S, M, L', 'ขาวเบจ', 'ขาวเบจ', 'N/A', 'N/A', 'N/A', 'ส้นสูง 3.5 นิ้ว', 5000.00, 100.00, 3, 160, 25, 'available', 1, 1, 1, '2026-09-16 00:39:05', '2026-09-17 09:29:58'),
(22, 7, 'KY-SHO-002', 'รองเท้าส้นสูง', 'รองเท้าส้นสูง แบรนด์ YSL เรียบหรูดูแพง สวมใส่ง่าย แมตช์ได้กับเดรสทุกแบบ', 'S', 'S, M, L', 'ดำคลาสสิก', 'ดำคลาสสิก', 'N/A', 'N/A', 'N/A', 'ส้นสูง 3 นิ้ว', 5500.00, 100.00, 4, 90, 12, 'available', 0, 0, 1, '2026-09-16 00:39:05', '2026-09-17 10:45:30'),
(23, 8, 'KY-BAG-001', 'กระเป๋า Chanel 11.12 ทรงคลาสสิก หนังแกะสีดำ อะไหล่ทอง', 'ปรับสายโซ่เป็นสายคู่สะพายไหล่ขนาบลำตัวเพื่อความโก้หรูเป็นทางการในวันทำงานหรือออกงานกลางคืน หรือปรับเป็นสายเดี่ยวสะพายข้างก็ได้', 'Free Size', 'Free Size', 'ดำ', 'ดำ', 'N/A', 'N/A', 'N/A', '20 x 12 ซม.', 550.00, 100.00, 3, 180, 30, 'available', 1, 1, 1, '2026-09-16 00:39:05', '2026-09-17 10:46:45'),
(24, 8, 'KY-BAG-002', 'กระเป๋า Mini Lady Dior', 'ไซส์ Mini ยอดนิยม ขนาดกะทัดรัด ถือแล้วดูน่ารัก พรางหุ่นให้ดูเพรียวสวย', 'Free Size', 'Free Size', 'ขาวมุก', 'ขาวมุก', 'N/A', 'N/A', 'N/A', '18 x 10 ซม.', 5000.00, 100.00, 2, 140, 18, 'available', 0, 1, 0, '2026-09-16 00:39:05', '2026-09-17 10:46:03'),
(25, 5, 'KY-DRS-0025', 'กระโปรงมินิสเกิร์ตจับเดรปห้อยชายสไตล์กรีกโรมัน', 'เผยเสน่ห์ความสง่างามที่เซ็กซี่สะกดทุกสายตากับกระโปรงดีไซน์ลักชูรีตัวแม่!', 'Free Size', 'Free Size', 'ขาวเบจ', 'ขาวเบจ', NULL, NULL, NULL, NULL, 350.00, 100.00, 3, 1, 1, 'available', 0, 0, 0, '2026-09-17 10:25:23', '2026-09-20 06:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `color_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_path`, `is_main`, `color_name`, `created_at`) VALUES
(52, 1, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:35:18'),
(53, 1, 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:35:18'),
(54, 1, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:35:18'),
(55, 2, 'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:35:18'),
(56, 2, 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:35:18'),
(67, 8, 'https://images.unsplash.com/photo-1568252542512-9fe8fe9c87bb?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:35:18'),
(68, 8, 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:35:18'),
(88, 9, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(89, 9, 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(90, 10, 'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(91, 10, 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(92, 11, 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(93, 11, 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(94, 12, 'https://images.unsplash.com/photo-1525457136159-8878648a7ad0?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(95, 13, 'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(96, 14, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(97, 15, 'https://images.unsplash.com/photo-1594552072238-b8a33785b261?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(98, 16, 'https://images.unsplash.com/photo-1549416864-228c7f24cf7a?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(99, 17, 'https://images.unsplash.com/photo-1568252542512-9fe8fe9c87bb?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(100, 18, 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(101, 19, 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(102, 20, 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(103, 21, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(104, 22, 'https://images.unsplash.com/photo-1560343776-97e7d202ff0e?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(105, 23, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(106, 24, 'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?w=800&auto=format&fit=crop&q=80', 0, NULL, '2026-09-16 00:39:30'),
(113, 10, 'products/LxAU4jS60dZwrdf9jXyA8naGkghjQlywh341fShz.jpg', 1, 'ดำ', '2026-09-17 11:58:26'),
(114, 10, 'products/ppXzNuFTE35EnKkVPgMiVpzSaNyu5o2whoPKxm4r.jpg', 0, 'แดงเบอกันดี', '2026-09-17 11:58:26'),
(115, 9, 'products/QGO3VcoXnwK9tItTRIBYBrBvMuD8hpl8JUDx0iWf.jpg', 1, 'น้ำตาล', '2026-09-17 12:04:28'),
(116, 8, 'products/vnH8XMEA4qE12bdvKqWWL1VrZVOX8L9emmAmjncE.jpg', 1, 'Detached Sleeves / Arm Warmers: ปลอกแขนที่แยกชิ้นออกจากตัวชุด โดยมีดีเทลปลายแขนบาน', '2026-09-17 12:08:50'),
(117, 2, 'products/1kE87wNZjHoshymmqk6iAfZM199uzzo49M94GoYi.jpg', 1, 'สีครีม', '2026-09-17 12:27:40'),
(118, 1, 'products/NAgzSiwvnkVit1FCdeSVIV1dX88zNUZ2HdXpfZyA.jpg', 1, 'สีขาวงาช้าง', '2026-09-17 12:32:03'),
(119, 12, 'products/sD3BFNoReDllheeoQ2gOjW0zKtKUilAUM7EMb7o6.jpg', 1, 'สีขาวครีม', '2026-09-17 12:38:33'),
(120, 11, 'products/c6g0wYCvy1u25nPgfTt1EWEEgX87zdgMXCp8ZUQa.jpg', 1, 'สีชมพูนม', '2026-09-17 12:42:11'),
(121, 14, 'products/BfRL9eGfuEprydKw0ZuQcbndyl4KzUm0DFq8A1gC.jpg', 1, 'สีครีม', '2026-09-17 12:46:03'),
(122, 13, 'products/UULLoGCpmK6Bho008hRm2HhjKbi8bVYKjnPUm6RU.jpg', 1, 'ดำชิมเมอร์', '2026-09-17 12:52:41'),
(123, 16, 'products/itoi0RNPuAEgCVLfGzdvBHhjMJqJjPLQAKZWW5bE.jpg', 1, 'ดำชาร์โคล', '2026-09-17 15:27:57'),
(124, 15, 'products/eJqFUUIDPbILO3lbBRRKaKuBnqBRGTlliWAePJeQ.jpg', 1, 'สีขาว', '2026-09-17 15:38:08'),
(125, 18, 'products/0h0QSRkwiJBNOraOfbcVbYnfTs0xoGJaAwbZXmV9.jpg', 1, 'น้ำตาล', '2026-09-17 15:51:54'),
(126, 17, 'products/hBgmaXIFOELt8FMDoHP5gK5C5t3aNbvgTWzXRjlf.jpg', 1, 'แชมเปญ', '2026-09-17 15:52:28'),
(127, 20, 'products/GsNB8VZCNNw2wnAtTR34JIifriZO80OjGXC6TydM.jpg', 1, 'ฟ้ายีนส์หม่น', '2026-09-17 16:08:21'),
(129, 22, 'products/pKmooKWiyHVDybcapLcq1gpJJeR3Qvsj3uDk7xGO.jpg', 1, 'ดำคลาสสิก', '2026-09-17 16:27:44'),
(130, 21, 'products/DO7ncrVQSpz2lDrvdU7KBS9bFLjivVIt8v4xvmzs.jpg', 1, 'ขาวเบจ', '2026-09-17 16:29:58'),
(131, 24, 'products/FlFIEakUCszHB1GexidR2uTaHqO3cMsVUUPDnn36.jpg', 1, 'ขาวมุก', '2026-09-17 16:36:58'),
(132, 23, 'products/GpQVU0IWmVilf4LeHhMgfndeK6bRwIwiTKdboULU.jpg', 1, 'ดำ', '2026-09-17 16:39:31'),
(133, 25, 'products/OmNiAyV17i2hLRmOdvht6nhRDgdc9UnVRbN7sMRK.jpg', 1, NULL, '2026-09-17 17:25:23'),
(134, 19, 'products/ejVgQXZrWIDPLaswNk82wnzQvLatXQMW4t3WCxDI.jpg', 1, 'น้ำตาล', '2026-09-17 17:53:42');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` bigint(20) UNSIGNED NOT NULL,
  `rental_code` varchar(50) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `rental_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_reason` varchar(150) DEFAULT NULL,
  `deposit_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_type` varchar(100) DEFAULT NULL,
  `service_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_method` varchar(50) NOT NULL DEFAULT 'pickup',
  `delivery_address` text DEFAULT NULL,
  `recipient_phone` varchar(30) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipping_carrier` varchar(100) DEFAULT NULL,
  `shipping_status` varchar(100) DEFAULT NULL,
  `tracking_url` text DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `estimated_delivery_at` datetime DEFAULT NULL,
  `return_due_at` datetime DEFAULT NULL,
  `return_tracking_no` varchar(100) DEFAULT NULL,
  `return_shipping_carrier` varchar(100) DEFAULT NULL,
  `return_shipping_status` varchar(100) DEFAULT NULL,
  `return_shipped_at` datetime DEFAULT NULL,
  `return_estimated_delivery_at` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending_payment',
  `condition_status` varchar(20) DEFAULT NULL,
  `deposit_status` varchar(20) NOT NULL DEFAULT 'pending',
  `deposit_refund_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `damage_note` text DEFAULT NULL,
  `damage_image` varchar(255) DEFAULT NULL,
  `refund_slip` varchar(255) DEFAULT NULL,
  `inspected_at` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`rental_id`, `rental_code`, `customer_id`, `rental_date`, `start_date`, `end_date`, `total_amount`, `discount_amount`, `discount_reason`, `deposit_amount`, `service_type`, `service_fee`, `delivery_method`, `delivery_address`, `recipient_phone`, `tracking_number`, `shipping_carrier`, `shipping_status`, `tracking_url`, `shipped_at`, `estimated_delivery_at`, `return_due_at`, `return_tracking_no`, `return_shipping_carrier`, `return_shipping_status`, `return_shipped_at`, `return_estimated_delivery_at`, `status`, `condition_status`, `deposit_status`, `deposit_refund_amount`, `damage_note`, `damage_image`, `refund_slip`, `inspected_at`, `note`, `created_at`, `updated_at`) VALUES
(1, 'KR-202609-0001', 6, '2026-09-02', '2026-09-04', '2026-09-07', 1500.00, 0.00, NULL, 2000.00, 'ซักแห้งรีดไอน้ำพรีเมียม', 150.00, 'delivery', '88/9 คอนโดพาร์ควิลล่า ถ.สุขุมวิท 55 แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพฯ 10110', '089-123-4567', 'TH26091100998B', NULL, NULL, NULL, NULL, NULL, NULL, 'TH26091555667K', NULL, NULL, NULL, NULL, 'completed', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, 'ส่งคืนเรียบร้อย สภาพชุดสมบูรณ์ คืนเงินมัดจำแล้ว', '2026-09-12 11:08:17', '2026-09-16 02:19:08'),
(2, 'KR-202609-0002', 6, '2026-09-10', '2026-09-11', '2026-09-14', 1800.00, 0.00, NULL, 2500.00, 'จัดส่งด่วนแมสเซนเจอร์', 100.00, 'delivery', '88/9 คอนโดพาร์ควิลล่า ถ.สุขุมวิท 55 แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพฯ 10110', '089-123-4567', 'GRAB-891100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'returned', 'damaged', 'forfeited', 0.00, 'เดรสขาด', 'uploads/returns/damage_1789630370_6aab97a25f254.png', NULL, '2026-09-17 00:32:50', 'ลูกค้าได้รับชุดแล้ว ใช้งานได้ถึงวันที่นัดหมาย\nรับคืนชุดเรียบร้อย: ตรวจพบชุดชำรุด/เสียหาย | ยึดเงินมัดจำ ฿100.00 ไม่คืนเงินมัดจำ (สาเหตุ: เดรสขาด) | บันทึกเพิ่มเติม: -', '2026-09-12 11:08:17', '2026-09-17 00:32:50'),
(3, 'KR-202609-0003', 6, '2026-09-12', '2026-09-12', '2026-09-15', 6000.00, 0.00, NULL, 2000.00, 'cleaning', 150.00, 'pickup', 'รับที่หน้าร้าน KYRIX', '089-123-4567', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending_payment', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, 'ขอรับชุดช่วงบ่ายสองโมง', '2026-09-12 11:08:42', '2026-09-16 02:19:08'),
(4, '#R00004', 6, '2026-09-12', '2026-09-12', '2026-09-14', 9000.00, 0.00, NULL, 4000.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, 'ทดสอบจอง 2 ชุด', '2026-09-12 11:22:17', '2026-09-16 02:19:08'),
(5, '#R00005', 6, '2026-09-12', '2026-09-12', '2026-09-14', 9000.00, 0.00, NULL, 4000.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, 'ทดสอบจอง 2 ชุด', '2026-09-12 11:22:32', '2026-09-16 02:19:08'),
(6, NULL, 5, '2026-09-12', '2026-09-12', '2026-09-14', 4500.00, 0.00, NULL, 2000.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', 'good', 'refunded', 100.00, NULL, NULL, 'uploads/returns/refund_1789673698_6aac40e210411.jpg', '2026-09-17 12:34:58', 'แจ้งคืนจากลูกค้า: นำมาคืนที่หน้าร้าน KYRIX (โคราช)\nรับคืนชุดเรียบร้อย: สภาพชุดสมบูรณ์ ไม่พบความเสียหาย | คืนเงินมัดจำเต็มจำนวน ฿100.00', '2026-09-12 13:04:15', '2026-09-17 13:40:07'),
(7, NULL, 6, '2026-09-12', '2026-09-12', '2026-09-14', 4200.00, 0.00, NULL, 2000.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'returned', 'good', 'refunded', 100.00, NULL, NULL, NULL, '2026-09-17 13:16:15', 'รับคืนชุดเรียบร้อย: สภาพชุดสมบูรณ์ ไม่พบความเสียหาย | คืนเงินมัดจำเต็มจำนวน ฿100.00', '2026-09-12 13:08:18', '2026-09-17 13:16:15'),
(8, 'KR-202609-0008', 6, '2026-09-16', '2026-09-16', '2026-09-17', 900.00, 0.00, NULL, 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-', NULL, NULL, NULL, NULL, 'completed', 'good', 'refunded', 100.00, NULL, NULL, NULL, '2026-09-16 07:26:53', 'แจ้งคืนจากลูกค้า: นำมาคืนที่หน้าร้านทองหล่อ | เลขพัสดุส่งคืน: -', '2026-09-16 07:24:45', '2026-09-16 08:05:23'),
(9, 'KR-202609-0009', 5, '2026-09-17', '2026-09-17', '2026-09-19', 4500.00, 900.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending_verification', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, NULL, '2026-09-17 00:36:12', '2026-09-17 13:21:49'),
(10, 'KR-202609-0010', 5, '2026-09-17', '2026-09-17', '2026-09-19', 1350.00, 0.00, NULL, 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending_payment', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, NULL, '2026-09-17 01:17:54', '2026-09-17 12:01:47'),
(11, 'KR-202609-0011', 5, '2026-09-17', '2026-09-17', '2026-09-19', 4500.00, 900.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', 'good', 'refunded', 100.00, NULL, NULL, NULL, '2026-09-17 13:22:32', NULL, '2026-09-17 01:51:21', '2026-09-17 13:22:32'),
(12, 'KR-202609-0012', 5, '2026-09-17', '2026-09-17', '2026-09-19', 1350.00, 0.00, NULL, 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending_payment', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, NULL, '2026-09-17 02:50:29', '2026-09-17 02:50:29'),
(13, 'KR-202609-0013', 5, '2026-09-17', '2026-09-17', '2026-09-19', 1350.00, 0.00, NULL, 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending_payment', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, NULL, '2026-09-17 02:59:56', '2026-09-17 02:59:56'),
(14, 'KR-202609-0014', 5, '2026-09-17', '2026-09-17', '2026-09-19', 4500.00, 900.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending_payment', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, NULL, '2026-09-17 03:14:49', '2026-09-17 03:14:49'),
(15, 'KR-202609-0015', 5, '2026-09-17', '2026-09-17', '2026-09-19', 2550.00, 510.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', 'good', 'refunded', 100.00, NULL, NULL, 'uploads/returns/refund_1789676187_6aac4a9bd3873.jpg', '2026-09-17 13:16:27', 'รับคืนชุดเรียบร้อย: สภาพชุดสมบูรณ์ ไม่พบความเสียหาย | คืนเงินมัดจำเต็มจำนวน ฿100.00', '2026-09-17 10:59:55', '2026-09-17 13:49:38'),
(16, 'KR-202609-0016', 5, '2026-09-17', '2026-09-17', '2026-09-19', 2550.00, 510.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', 'good', 'refunded', 100.00, NULL, NULL, NULL, '2026-09-17 13:37:51', NULL, '2026-09-17 11:04:16', '2026-09-17 13:37:51'),
(17, 'KR-202609-0017', 5, '2026-09-17', '2026-09-17', '2026-09-19', 2550.00, 510.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', 'good', 'refunded', 100.00, NULL, NULL, NULL, '2026-09-17 13:30:47', NULL, '2026-09-17 11:04:57', '2026-09-17 13:30:47'),
(18, 'KR-202609-0018', 5, '2026-09-17', '2026-09-17', '2026-09-19', 2550.00, 510.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', 'good', 'refunded', 100.00, NULL, NULL, NULL, '2026-09-17 13:23:44', NULL, '2026-09-17 11:06:11', '2026-09-17 13:38:17'),
(19, 'KR-202609-0019', 5, '2026-09-17', '2026-09-17', '2026-09-19', 3300.00, 660.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', 'good', 'refunded', 100.00, NULL, NULL, 'uploads/returns/refund_1789673713_6aac40f16b8b9.jpg', '2026-09-17 12:35:13', 'แจ้งคืนจากลูกค้า: นำมาคืนที่หน้าร้าน KYRIX (โคราช)\nรับคืนชุดเรียบร้อย: สภาพชุดสมบูรณ์ ไม่พบความเสียหาย | คืนเงินมัดจำเต็มจำนวน ฿100.00', '2026-09-17 11:28:16', '2026-09-17 13:20:47'),
(20, 'KR-202609-0020', 5, '2026-09-17', '2026-09-18', '2026-09-19', 780.00, 0.00, NULL, 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, 'ยกเลิกโดยลูกค้าเมื่อ 17/09/2026 18:56\nเหตุผล: เปลี่ยนใจ', '2026-09-17 11:33:34', '2026-09-17 11:56:38'),
(21, 'KR-202609-0021', 5, '2026-09-17', '2026-09-17', '2026-09-19', 1800.00, 0.00, NULL, 100.00, NULL, 0.00, 'pickup', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, 'pending', 0.00, NULL, NULL, NULL, NULL, 'ยกเลิกโดยลูกค้าเมื่อ 17/09/2026 18:59\nเหตุผล: เปลี่ยนใจ', '2026-09-17 11:59:36', '2026-09-17 11:59:46'),
(23, 'KR-202609-0022', 9, '2026-09-19', '2026-09-19', '2026-09-19', 2500.00, 500.00, 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ 2,000 บาท)', 100.00, NULL, 0.00, 'pickup', NULL, NULL, 'TH123456789', 'J&T Express', 'ส่งพัสดุแล้ว', 'https://www.jtexpress.co.th/service/track?waybillNo=TH123456789', '2026-09-19 18:46:00', '2026-09-19 18:46:00', '2026-09-21 18:47:00', 'TH112233445', NULL, NULL, NULL, NULL, 'pending_return', 'good', 'refunded', 100.00, NULL, NULL, NULL, '2026-09-19 04:10:38', NULL, '2026-09-19 03:44:17', '2026-09-19 05:32:45'),
(24, 'KR-202609-0023', 5, '2026-09-20', '2026-09-20', '2026-09-22', 1050.00, 0.00, NULL, 100.00, NULL, 0.00, 'pickup', NULL, NULL, 'TH123456798', 'ไปรษณีย์ไทย', 'จัดส่งสำเร็จ', NULL, '2026-09-18 16:03:00', '2026-09-20 16:03:00', '2026-09-22 16:04:00', 'TH112233454', 'ไปรษณีย์ไทย', 'ถึงร้านแล้ว', '2026-09-21 19:57:00', '2026-09-22 19:57:00', 'completed', 'good', 'refunded', 100.00, NULL, NULL, NULL, '2026-09-20 06:02:44', 'แจ้งคืนจากลูกค้า: นำมาคืนที่หน้าร้าน KYRIX (โคราช)', '2026-09-20 01:54:59', '2026-09-20 06:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `rental_details`
--

CREATE TABLE `rental_details` (
  `rental_detail_id` bigint(20) UNSIGNED NOT NULL,
  `rental_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `selected_size` varchar(50) DEFAULT NULL,
  `selected_color` varchar(50) DEFAULT NULL,
  `rental_days` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rental_details`
--

INSERT INTO `rental_details` (`rental_detail_id`, `rental_id`, `product_id`, `quantity`, `selected_size`, `selected_color`, `rental_days`, `price`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 1, 'M', 'แดงเบอร์กันดี', 3, 1500.00, 1500.00, '2026-09-02 11:08:17'),
(2, 2, 2, 1, 'S', 'ทองแชมเปญ', 3, 1800.00, 1800.00, '2026-09-10 11:08:17'),
(3, 3, 1, 1, 'M', 'แดงเบอร์กันดี', 4, 1500.00, 6000.00, '2026-09-12 11:08:42'),
(4, 4, 1, 2, 'M', 'แดงเบอร์กันดี', 3, 1500.00, 9000.00, '2026-09-12 11:22:17'),
(5, 5, 1, 2, 'M', 'แดงเบอร์กันดี', 3, 1500.00, 9000.00, '2026-09-12 11:22:32'),
(6, 6, 1, 1, NULL, NULL, 1, 1500.00, 4500.00, '2026-09-12 20:04:15'),
(7, 7, 8, 1, NULL, NULL, 1, 1400.00, 4200.00, '2026-09-12 20:08:18'),
(8, 1, 9, 1, 'M', 'แดงเบอร์กันดี', 3, 1500.00, 1500.00, '2026-09-06 00:39:30'),
(9, 2, 10, 1, 'S', 'ทองแชมเปญ', 3, 1800.00, 1800.00, '2026-09-14 00:39:30'),
(10, 8, 9, 1, NULL, NULL, 1, 450.00, 900.00, '2026-09-16 14:24:45'),
(11, 9, 1, 1, NULL, NULL, 1, 1500.00, 4500.00, '2026-09-17 07:36:12'),
(12, 10, 9, 1, NULL, NULL, 1, 450.00, 1350.00, '2026-09-17 08:17:54'),
(13, 11, 1, 1, NULL, NULL, 1, 1500.00, 4500.00, '2026-09-17 08:51:21'),
(14, 12, 9, 1, NULL, NULL, 1, 450.00, 1350.00, '2026-09-17 09:50:29'),
(15, 13, 9, 1, NULL, NULL, 1, 450.00, 1350.00, '2026-09-17 09:59:56'),
(16, 14, 1, 1, NULL, NULL, 1, 1500.00, 4500.00, '2026-09-17 10:14:49'),
(17, 15, 2, 1, NULL, NULL, 1, 850.00, 2550.00, '2026-09-17 17:59:55'),
(18, 16, 2, 1, NULL, NULL, 1, 850.00, 2550.00, '2026-09-17 18:04:16'),
(19, 17, 13, 1, NULL, NULL, 1, 850.00, 2550.00, '2026-09-17 18:04:57'),
(20, 18, 13, 1, NULL, NULL, 1, 850.00, 2550.00, '2026-09-17 18:06:11'),
(21, 19, 12, 1, NULL, NULL, 1, 1100.00, 3300.00, '2026-09-17 18:28:16'),
(22, 20, 8, 1, NULL, NULL, 1, 390.00, 780.00, '2026-09-17 18:33:34'),
(23, 21, 18, 1, NULL, NULL, 1, 600.00, 1800.00, '2026-09-17 18:59:36'),
(24, 23, 14, 1, NULL, NULL, 1, 2500.00, 2500.00, '2026-09-19 10:44:17'),
(25, 24, 25, 1, 'Free Size', 'ขาวเบจ', 3, 350.00, 1050.00, '2026-09-20 08:54:59');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `rental_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('published','hidden') NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `rental_id`, `product_id`, `customer_id`, `rating`, `comment`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 6, 5, 'ชุดสวยมากกกก ใส่ไปงานกาล่าคนชมทั้งคืนเลยค่ะ ผ้ากำมะหยี่มีน้ำหนักทิ้งตัวสวย คัตติ้งเนี้ยบสุดๆ ทางร้านทำความสะอาดมาหอมสะอาดมาก ประทับใจมากค่ะ!', 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=600&auto=format&fit=crop&q=80', 'published', '2026-09-12 11:08:17', '2026-09-16 02:19:08'),
(9, 8, 9, 6, 5, 'เริ่ดดดดดดด', NULL, 'published', '2026-09-16 08:06:43', '2026-09-16 08:06:43'),
(10, 15, 2, 5, 5, 'สวยมากกก', 'uploads/reviews/review_1789668217_6aac2b798ae4d.jpg', 'published', '2026-09-17 11:03:37', '2026-09-17 11:03:37'),
(11, 19, 12, 5, 5, 'สวยยยยยย', NULL, 'published', '2026-09-17 12:36:07', '2026-09-17 12:36:07'),
(12, 6, 1, 5, 5, 'สวยสุดดด', NULL, 'published', '2026-09-17 13:10:53', '2026-09-17 13:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('owner','customer') NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `status`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `provider`, `provider_id`, `avatar`) VALUES
(1, 'เจ้าของร้าน', 'owner@kyrix.com', '$2y$12$GNSPYBASrtHA3uodcM8FdeZeUDUbjaFR8fiG1pDHfdryo3.MA.w.q', 'owner', 'active', NULL, NULL, '2026-09-12 10:39:49', '2026-09-16 00:02:33', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `customers_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payments_rental_id_foreign` (`rental_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `products_product_code_unique` (`product_code`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD UNIQUE KEY `rentals_rental_code_unique` (`rental_code`),
  ADD KEY `rentals_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `rental_details`
--
ALTER TABLE `rental_details`
  ADD PRIMARY KEY (`rental_detail_id`),
  ADD KEY `rental_details_rental_id_foreign` (`rental_id`),
  ADD KEY `rental_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `reviews_rental_id_foreign` (`rental_id`),
  ADD KEY `reviews_product_id_foreign` (`product_id`),
  ADD KEY `reviews_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_provider_provider_id_unique` (`provider`,`provider_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `rental_details`
--
ALTER TABLE `rental_details`
  MODIFY `rental_detail_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`rental_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
