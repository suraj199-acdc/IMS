-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2026 at 10:08 AM
-- Server version: 9.5.0
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ims`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `type` enum('sale','purchase') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `buyer_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `buyer_contact` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `product_id`, `quantity`, `total_amount`, `type`, `buyer_name`, `buyer_contact`, `order_date`, `created_at`) VALUES
(1, 2, 3, 3900.00, 'sale', NULL, NULL, '2026-04-09 11:16:33', '2026-04-08 06:49:24'),
(2, 8, 1, 9300.00, 'sale', NULL, NULL, '2026-04-09 11:16:33', '2026-04-08 06:49:24'),
(3, 17, 500, 0.00, 'purchase', NULL, NULL, '2026-04-09 11:16:33', '2026-04-08 06:53:54');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cod',
  `payment_status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `amount_received` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `user_name`, `total_amount`, `total_price`, `status`, `created_at`, `payment_method`, `payment_status`, `amount_received`) VALUES
(6, 9, '', 1300.00, 1300.00, '', '2026-02-15 08:02:43', 'cod', 'Pending', 0.00),
(7, 10, '', 10680.00, 10680.00, '', '2026-02-16 05:37:53', 'cod', 'Pending', 0.00),
(8, 9, '', 12000.00, 12000.00, 'rejected', '2026-02-23 02:17:12', 'cod', 'Pending', 0.00),
(9, 12, '', 16000.00, 16000.00, 'approved', '2026-02-23 03:33:42', 'cod', 'Pending', 0.00),
(10, 9, '', 7500.00, 7500.00, '', '2026-03-25 02:25:31', 'cod', 'Pending', 0.00),
(11, 9, '', 13000.00, 13000.00, '', '2026-03-25 02:26:23', 'cod', 'Pending', 0.00),
(12, 9, '', 13000.00, 13000.00, '', '2026-03-25 02:33:25', 'online', 'Pending', 0.00),
(13, 9, '', 13000.00, 13000.00, '', '2026-03-25 02:33:38', 'cod', 'Pending', 0.00),
(14, 9, '', 35000.00, 35000.00, '', '2026-03-25 02:35:13', 'esewa', 'Pending', 0.00),
(15, 9, '', 450000.00, 450000.00, '', '2026-03-25 02:37:17', 'cod', 'Pending', 0.00),
(16, 8, '', 35350.00, 35350.00, '', '2026-03-25 02:40:57', 'cod', 'Pending', 0.00),
(17, 9, '', 35000.00, 35000.00, '', '2026-03-25 02:42:46', 'cod', 'Pending', 0.00),
(18, 9, '', 8000.00, 8000.00, '', '2026-03-25 02:44:56', 'cod', 'Pending', 0.00),
(19, 9, '', 2200.00, 2200.00, '', '2026-03-25 02:50:15', 'cod', 'Pending', 0.00),
(20, 9, '', 2200.00, 2200.00, '', '2026-03-25 02:52:48', 'cod', 'Pending', 0.00),
(21, 9, '', 35000.00, 35000.00, '', '2026-03-25 02:53:17', 'cod', 'Pending', 0.00),
(22, 9, '', 7500.00, 7500.00, '', '2026-03-25 02:59:18', 'COD', 'Pending', 0.00),
(23, 8, '', 91000.00, 91000.00, '', '2026-03-25 03:00:56', 'cod', 'Pending', 0.00),
(24, 9, '', 8000.00, 8000.00, 'approved', '2026-04-07 03:54:15', 'cod', 'Pending', 0.00),
(25, 9, '', 36800.00, 36800.00, '', '2026-04-07 04:03:29', 'cod', 'Pending', 0.00),
(26, 10, '', 7500.00, 7500.00, '', '2026-04-07 04:20:18', 'COD', 'Pending', 0.00),
(27, 10, '', 26000.00, 26000.00, 'approved', '2026-04-07 04:37:42', 'cod', 'Pending', 0.00),
(28, 8, '', 405000.00, 405000.00, '', '2026-04-07 04:39:50', 'cod', 'Pending', 0.00),
(29, 8, '', 243000.00, 243000.00, '', '2026-04-07 10:34:34', 'cod', 'Pending', 0.00),
(30, 10, '', 9000.00, 9000.00, 'pending', '2026-04-08 03:31:02', 'cod', 'Pending', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(7, 6, 2, 1, 1300.00),
(8, 7, 8, 1, 9300.00),
(9, 7, 9, 2, 600.00),
(10, 7, 10, 1, 180.00),
(11, 8, 12, 1, 12000.00),
(12, 9, 17, 2, 8000.00),
(13, 10, 15, 1, 7500.00),
(14, 11, 16, 1, 13000.00),
(15, 12, 16, 1, 13000.00),
(16, 13, 16, 1, 13000.00),
(17, 14, 14, 1, 35000.00),
(18, 15, 13, 500, 900.00),
(19, 16, 14, 1, 35000.00),
(20, 16, 11, 1, 350.00),
(21, 17, 14, 1, 35000.00),
(22, 18, 17, 1, 8000.00),
(23, 19, 6, 1, 2200.00),
(24, 20, 6, 1, 2200.00),
(25, 21, 14, 1, 35000.00),
(26, 22, 15, 1, 7500.00),
(27, 23, 16, 7, 13000.00),
(28, 24, 17, 1, 8000.00),
(29, 25, 14, 1, 35000.00),
(30, 25, 13, 2, 900.00),
(31, 26, 15, 1, 7500.00),
(32, 27, 7, 2, 13000.00),
(33, 28, 13, 450, 900.00),
(34, 29, 16, 16, 13000.00),
(35, 29, 14, 1, 35000.00),
(36, 30, 13, 10, 900.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `department` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `annual_demand` int DEFAULT '0',
  `ordering_cost` decimal(10,2) DEFAULT '0.00',
  `holding_cost` decimal(10,2) DEFAULT '0.00',
  `lead_time` int DEFAULT '0',
  `safety_stock` int DEFAULT '0',
  `reorder_level` int DEFAULT '10'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `quantity`, `created_at`, `department`, `category`, `description`, `image`, `annual_demand`, `ordering_cost`, `holding_cost`, `lead_time`, `safety_stock`, `reorder_level`) VALUES
(2, 'Chivas regal', 1300.00, 8, '2026-02-14 09:47:39', 'Bar', 'Whisky', '20 years aged malt', '1771062459_1770957514_1770790188.jpg', 0, 0.00, 0.00, 0, 0, 10),
(5, 'Stella Artois', 1800.00, 300, '2026-02-15 08:54:59', 'Bar', 'Beer', 'Stella Artois is a premium Belgian pilsner, originally brewed in 1926 in Leuven with roots dating back to 1366, recognized for its iconic chalice, floral aroma, and crisp, slightly bitter taste. Owned by Anheuser-Busch InBev, it is a 5% ABV (4.6%-5.2% ABV depending on region) golden lager, often marketed for its rich heritage and refreshing, dry finish.', '1771145699_stella artois.jpg', 0, 0.00, 0.00, 0, 0, 10),
(6, 'Smrinoff', 2200.00, 93, '2026-02-15 08:56:59', 'Bar', 'Vodka', 'Smirnoff is a globally renowned vodka brand owned by Diageo, known for its, triple-distilled, 10-times filtered, and versatile, smooth, dry-finish', '1771145819_smrinoff.jpg', 0, 0.00, 0.00, 0, 0, 10),
(7, 'Singleton 12 Years', 13000.00, 1, '2026-02-15 08:59:54', 'Bar', 'Single Malt', 'an award-winning range of approachable Single Malt Scotch Whiskies from Diageo, produced at three distinct distilleries—Glen Ord, Glendullan, and Dufftown—in the Scottish Highlands and Speyside. Known for being exceptionally smooth, rich, and easy to enjoy, it is matured primarily in American oak casks with some European oak to deliver notes of brown sugar, dried fruit, and soft spice.', '1771147513_singleton.jpg', 0, 0.00, 0.00, 0, 0, 10),
(8, 'Bombay Sapphire', 9300.00, 0, '2026-02-15 09:01:55', 'Bar', 'Gin', 'a world-renowned premium London Dry gin (40% ABV) recognized by its iconic blue bottle and delicate, balanced flavor profile. Launched in 1987, it features 10 hand-selected botanicals vapor-infused to produce a bright, citrusy, and lightly spiced gin. It is highly versatile, making it ideal for Gin & Tonics', '1771146115_saphire.jpg', 0, 0.00, 0.00, 0, 0, 10),
(9, 'San Miguel', 600.00, 200, '2026-02-15 09:24:38', 'Bar', 'Beer', 'a renowned Filipino lager brand established in 1890, produced by San Miguel Brewery (a subsidiary of San Miguel Corporation). It is one of the world\'s best-selling beers, with a 5% ABV pale pilsen as its flagship product, known for a crisp, refreshing taste.', '1771147478_sanmuigel.jpg', 0, 0.00, 0.00, 0, 0, 10),
(10, 'Sprite can', 180.00, 620, '2026-02-15 09:26:37', 'Bar', 'Soft Drinks', 'the world\'s leading, caffeine-free, lemon-lime flavored soft drink, introduced by The Coca-Cola Company in 1961 as a competitor to 7 Up. Known for its crisp, clear, and refreshing taste,', '1771147597_spritecan.jpg', 0, 0.00, 0.00, 0, 0, 10),
(11, 'Red Bull', 350.00, 80, '2026-02-15 09:28:10', 'Bar', 'Energy Drinks', 'a leading Austrian functional beverage company founded in 1987 by Dietrich Mateschitz, inspired by Thai energy drink Krating Daeng. It dominates the global energy drink market with over 100 billion cans sold, offering caffeine, taurine, and B-group vitamins to enhance alertness and reduce fatigue.', '1771147690_redbull.jpg', 0, 0.00, 0.00, 0, 0, 10),
(12, 'Amarula', 12000.00, 23, '2026-02-21 14:49:57', 'Bar', 'Cream Liquor', 'popular South African cream liqueur made from the exotic fruit of the indigenous Marula tree (Sclerocarya birrea), often called the \"elephant tree\". It features a smooth, creamy texture with distinct notes of caramel, vanilla, and nutty flavors, typically containing 17% alcohol by volume (34 proof)', '1771685397_amaurula.jpg', 0, 0.00, 0.00, 0, 0, 10),
(13, 'Corona Extra', 900.00, 78, '2026-02-21 14:52:08', 'Bar', 'Beer', 'a globally renowned Mexican pale lager with 4.5%–4.6% ABV, produced by Grupo Modelo since 1925. Known for its light, crisp, and refreshing taste', '1771685528_coronas.jpg', 0, 0.00, 0.00, 0, 0, 10),
(14, 'Blue Label', 35000.00, 0, '2026-02-21 14:54:25', 'Bar', 'Whisky', 'a premium, top-tier blended Scotch whisky renowned for its exceptional smoothness, rarity, and complexity.', '1771685665_bluelabel.jpg', 0, 0.00, 0.00, 0, 0, 10),
(15, 'Baileys', 7500.00, 12, '2026-02-21 15:06:30', 'Bar', 'Cream Liquor', 'the world\'s leading cream liqueur, launched in 1974, featuring a blend of Irish whiskey, dairy cream, cocoa, and vanilla. Produced in Ireland, it has a 17% ABV', '1771686390_balleeyes\'.jpg', 0, 0.00, 0.00, 0, 0, 10),
(16, 'Golden Patrón', 13000.00, 45, '2026-02-21 15:18:39', 'Bar', 'Tequila', 'a golden hue, smoother taste, and notes of caramel, oak, and vanilla  tequlia', '1771687119_golden patron.jpg', 0, 0.00, 0.00, 0, 0, 10),
(17, 'Don Julio', 5200.00, 10, '2026-02-21 15:20:17', 'Bar', 'Tequila', 'Handcrafted in Jalisco, Mexico, using traditional methods, including slow-roasting agave in masonry ovens and using a proprietary yeast strain', '1771687217_donjulio.jpg', 0, 0.00, 0.00, 0, 0, 10),
(18, 'Khukuri White Rum', 4200.00, 7, '2026-04-08 01:27:10', 'Bar', 'White Rum', 'a premium Nepalese white rum produced by The Nepal Distilleries, featuring a 40% ABV, complex yet light profile with notes of tropical fruits and creamy nuts. It is charcoal-filtered for smoothness', '1775611630_khukuri.jpg', 0, 0.00, 0.00, 0, 0, 10),
(19, 'Gurkhas and Guns', 4200.00, 3, '2026-04-08 03:37:17', 'Bar', 'Whisky', 'a premium blended whisky produced in Nepal, paying homage to the legendary Gurkha soldiers known for their bravery and valor.  The spirit is crafted by a Master Blender at the Avanish Distillery in Kathmandu, using a blend of famous Highland Malts and a rare 10-year-old Speyside Single Malt sourced from Scotland.', '1775619437_859904930584502612818138353979477904919863.png', 0, 0.00, 0.00, 0, 0, 10),
(21, 'Jagermeister', 7800.00, 6, '2026-04-09 10:37:34', 'Bar', 'Herbal liqueur', 'a 35% ABV German herbal liqueur created in 1934 by Curt Mast, featuring a secret recipe of 56 herbs, roots, and spices. Originally marketed as a digestive tonic, it is characterized by a complex, slightly bitter, and licorice-like flavor profile.', '1775731054_jager.jpg', 0, 0.00, 0.00, 0, 0, 7),
(22, 'Black dog', 7000.00, 5, '2026-04-29 01:27:32', 'Bar', 'Whisky', 'a popular blended Scotch whisky manufactured by United Spirits Ltd (USL), a Diageo company, introduced in 1883 and known for its smoothness.', '1777426052_blackdog.jpg', 0, 0.00, 0.00, 0, 0, 10),
(23, 'Crico Vodka', 9500.00, 7, '2026-04-29 01:32:54', 'Bar', 'vodka', 'a premium vodka brand known for being distilled from fine French grapes rather than the grain typically used in most vodkas. It is characterized by its fresh, smooth, and slightly fruity taste.', '1777426374_circo.jpg', 0, 0.00, 0.00, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `purchase_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `product_image` varchar(255) NOT NULL DEFAULT '',
  `vendor_name` varchar(100) DEFAULT NULL,
  `vendor_phone` varchar(50) DEFAULT NULL,
  `vendor_address` text,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT '0.00',
  `due_amount` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `product_id`, `product_name`, `quantity`, `price`, `purchase_date`, `product_image`, `vendor_name`, `vendor_phone`, `vendor_address`, `payment_method`, `payment_status`, `paid_amount`, `due_amount`) VALUES
(4, 12, 'Amarula', 10, 12000.00, '2026-04-09 10:17:07', '1771685397_amaurula.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(5, 15, 'Baileys', 20, 7500.00, '2026-04-09 10:39:36', '1771686390_balleeyes\'.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(6, 14, 'Blue Label', 9, 35000.00, '2026-04-09 10:39:53', '1771685665_bluelabel.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(7, 17, 'Don Julio', 10, 5200.00, '2026-04-11 10:58:03', '1771687217_donjulio.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(8, 13, 'Corona Extra', 200, 900.00, '2026-04-18 03:50:57', '1771685528_coronas.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(9, 6, 'Smrinoff', 10, 2200.00, '2026-04-18 03:51:05', '1771145819_smrinoff.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(10, 7, 'Singleton 12 Years', 5, 13000.00, '2026-04-18 03:51:11', '1771147513_singleton.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(11, 16, 'Golden Patrón', 6, 13000.00, '2026-04-18 03:51:18', '1771687119_golden patron.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(12, 6, 'Smrinoff', 6, 2200.00, '2026-04-18 03:51:24', '1771145819_smrinoff.jpg', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(13, 19, 'Gurkhas and Guns', 20, 4200.00, '2026-04-29 01:34:22', '1775619437_859904930584502612818138353979477904919863.png', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00),
(14, 12, 'Amarula', 10, 12000.00, '2026-04-29 04:25:33', '1771685397_amaurula.jpg', '', '', '', 'Cash', 'Credit', 0.00, 120000.00),
(15, 12, 'Amarula', 10, 12000.00, '2026-04-29 04:25:43', '1771685397_amaurula.jpg', 'Jonny walker Pvt', '01999999', 'ktm', 'Cash', 'Partial', 100000.00, 20000.00),
(16, 14, 'Blue Label', 2, 35000.00, '2026-04-30 05:34:56', '1771685665_bluelabel.jpg', 'Jonny walker Pvt', '01999999', 'Ktm', 'Bank Transfer', 'Paid', 70000.00, 0.00),
(17, 2, 'Chivas regal', 8, 1300.00, '2026-04-30 06:08:13', '1771062459_1770957514_1770790188.jpg', 'Shyam Liquors', '9865356441', 'Thimi', 'Cash', 'Credit', 0.00, 10400.00),
(18, 2, 'Chivas regal', 8, 1300.00, '2026-04-30 06:08:26', '1771062459_1770957514_1770790188.jpg', 'Shyam Liquors', '9865356441', 'Thimi', 'Cash', 'Credit', 0.00, 10400.00),
(19, 16, 'Golden Patrón', 5, 13000.00, '2026-04-30 06:14:34', '1771687119_golden patron.jpg', 'Shyam Liquors', '01999999', '', 'Cash', 'Paid', 65000.00, 0.00),
(20, 23, 'Crico Vodka', 5, 9500.00, '2026-04-30 06:14:34', '1777426374_circo.jpg', 'Shyam Liquors', '01999999', '', 'eSewa', 'Credit', 0.00, 47500.00),
(21, 22, 'Black dog', 10, 7000.00, '2026-04-30 07:36:01', '1777426052_blackdog.jpg', 'Shyam Liquors', '01999999', 'Thimi', 'Cheque', 'Paid', 70000.00, 0.00),
(22, 16, 'Golden Patrón', 5, 13000.00, '2026-05-07 03:00:34', '1771687119_golden patron.jpg', 'Shyam Liquors', '01999999', 'ktm', 'Cash', 'Credit', 0.00, 65000.00),
(23, 2, 'Chivas regal', 3, 1300.00, '2026-05-07 03:00:34', '1771062459_1770957514_1770790188.jpg', 'Shyam Liquors', '01999999', 'ktm', 'Cash', 'Credit', 0.00, 3900.00),
(24, 16, 'Golden Patrón', 5, 13000.00, '2026-05-07 03:00:48', '1771687119_golden patron.jpg', 'Shyam Liquors', '01999999', 'ktm', 'Cash', 'Credit', 0.00, 65000.00),
(25, 2, 'Chivas regal', 3, 1300.00, '2026-05-07 03:00:48', '1771062459_1770957514_1770790188.jpg', 'Shyam Liquors', '01999999', 'ktm', 'Cash', 'Credit', 0.00, 3900.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_name` varchar(100) NOT NULL DEFAULT '',
  `customer_contact` varchar(50) NOT NULL DEFAULT '',
  `product_image` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `product_name`, `quantity`, `price`, `sale_date`, `customer_name`, `customer_contact`, `product_image`) VALUES
(4, 19, 'Gurkhas and Guns', 5, 4200.00, '2026-04-09 06:20:56', 'demo', '9865321435', '1775619437_859904930584502612818138353979477904919863.png'),
(5, 14, 'Blue Label', 3, 35000.00, '2026-04-09 06:22:09', 'demo', '9865321435', '1771685665_bluelabel.jpg'),
(6, 21, 'Jagermeister', 6, 7800.00, '2026-04-09 10:38:19', 'Rama', '9863654478', '1775731054_jager.jpg'),
(7, 13, 'Corona Extra', 10, 900.00, '2026-04-18 03:51:47', 'Rama', '9865321435', '1771685528_coronas.jpg'),
(8, 20, 'Jameson', 11, 7500.00, '2026-04-18 03:52:04', 'Rama', '9865321435', '1775645342_jameson.jpg'),
(9, 9, 'San Miguel', 50, 600.00, '2026-04-29 01:33:55', 'Rama', '9865321435', '1771147478_sanmuigel.jpg'),
(10, 13, 'Corona Extra', 600, 900.00, '2026-04-30 06:19:50', 'Hari', '9865321435', '1771685528_coronas.jpg'),
(11, 14, 'Blue Label', 12, 35000.00, '2026-04-30 06:20:01', 'Hari', '9865321435', '1771685665_bluelabel.jpg'),
(12, 7, 'Singleton 12 Years', 14, 13000.00, '2026-04-30 06:20:15', 'Hari', '9865321435', '1771147513_singleton.jpg'),
(13, 19, 'Gurkhas and Guns', 32, 4200.00, '2026-04-30 06:20:40', 'Hari', '9865321435', '1775619437_859904930584502612818138353979477904919863.png'),
(14, 22, 'Black dog', 10, 7000.00, '2026-04-30 07:03:25', 'Rama', '9863654478', '1777426052_blackdog.jpg'),
(15, 6, 'Smrinoff', 3, 2200.00, '2026-04-30 07:03:25', 'Rama', '9863654478', '1771145819_smrinoff.jpg'),
(16, 15, 'Baileys', 2, 7500.00, '2026-04-30 07:15:04', 'Hari', '9863654478', '1771686390_balleeyes\'.jpg'),
(17, 18, 'Khukuri White Rum', 3, 4200.00, '2026-04-30 07:15:04', 'Hari', '9863654478', '1775611630_khukuri.jpg'),
(18, 8, 'Bombay Sapphire', 10, 9300.00, '2026-04-30 07:15:56', 'Rama', '9865321435', '1771146115_saphire.jpg'),
(19, 8, 'Bombay Sapphire', 10, 9300.00, '2026-04-30 07:17:16', 'Rama', '9865321435', '1771146115_saphire.jpg'),
(20, 8, 'Bombay Sapphire', 10, 9300.00, '2026-04-30 07:18:04', 'Rama', '9865321435', '1771146115_saphire.jpg'),
(21, 8, 'Bombay Sapphire', 10, 9300.00, '2026-04-30 07:18:41', 'Rama', '9865321435', '1771146115_saphire.jpg'),
(22, 14, 'Blue Label', 2, 35000.00, '2026-04-30 07:19:13', 'Hari', '9863654478', '1771685665_bluelabel.jpg'),
(23, 14, 'Blue Label', 2, 35000.00, '2026-04-30 07:20:59', 'Hari', '9863654478', '1771685665_bluelabel.jpg'),
(24, 11, 'Red Bull', 100, 350.00, '2026-04-30 07:21:26', 'Rama', '9865321435', '1771147690_redbull.jpg'),
(25, 2, 'Chivas regal', 5, 1300.00, '2026-04-30 07:26:56', 'Rama', '9865321435', '1771062459_1770957514_1770790188.jpg'),
(26, 2, 'Chivas regal', 5, 1300.00, '2026-04-30 07:28:26', 'Rama', '9865321435', '1771062459_1770957514_1770790188.jpg'),
(27, 2, 'Chivas regal', 5, 1300.00, '2026-04-30 07:28:29', 'Rama', '9865321435', '1771062459_1770957514_1770790188.jpg'),
(28, 22, 'Black dog', 5, 7000.00, '2026-05-07 03:08:55', 'Dera', '982565661', '1777426052_blackdog.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `created_at`) VALUES
(8, 'Admin', 'admin1@gmail.com', '$2y$10$WX8L/F.4BpcZCMXfQXbHb.WH0Y3oyddlGWeJLbTTzbAk21D14MvrK', '9863654115', '2026-02-15 08:01:30'),
(9, 'Mr.Oggy', 'oggy@gmail.com', '$2y$10$M9Xsp07onicfAfWRebblcOs6IvtiuAjgwyfmW5uCjCXjvG8f30zPO', '9563987446', '2026-02-15 08:02:19'),
(10, 'user', 'user@gmail.com', '$2y$10$4yIP8pYuG2g8IHeasA3Lh.FApTKugcBVSxqPL4nsMcVI1jT0AF1tS', '9865123665', '2026-02-16 05:34:45'),
(11, 'rabin tamang', 'rabin@gmail.com', '$2y$10$5svQYmuBekkCAdoT8rEEsejilWTPdDkCTI8SKaE7zbV8/TjD.2BCC', '9865351554', '2026-02-21 15:23:35'),
(12, 'a', 'a@gmail.com', '$2y$10$qbu8lVfx/Dv6jW2jnZMaNe6JJ6jBXl9keeqfhORrJsuKoZyjoRzha', '9836546224', '2026-02-23 03:27:16'),
(13, 'Demo', 'demo1@gmail.com', '$2y$10$MmbY.aQuPEu9d5KeHtI.n.QxbnNjaQemMmACC8PNkGIFt8E/geE8S', '9852653445', '2026-04-12 02:56:00'),
(14, 'ram magar', 'Ram@gmail.com', '$2y$10$pqPnVWZbs2bsTC2qn4ykle5aC26E62US4nsZ655yGkBrm3ghNigg2', '98653214665', '2026-04-29 03:59:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
