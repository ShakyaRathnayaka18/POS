-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 05, 2025 at 07:51 PM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vpos_hmart`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `account_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_type_id` bigint UNSIGNED NOT NULL,
  `parent_account_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_code`, `account_name`, `account_type_id`, `parent_account_id`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '1000', 'Current Assets', 1, NULL, 'Assets expected to be converted to cash within one year', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(2, '1100', 'Cash and Cash Equivalents', 1, 1, 'Cash on hand and in bank accounts', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(3, '1110', 'Cash in Hand', 1, 2, 'Physical cash at the business location', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(4, '1120', 'Cash in Bank', 1, 2, 'Cash held in bank accounts', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(5, '1200', 'Accounts Receivable', 1, 1, 'Money owed to the business by customers', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(6, '1300', 'Inventory', 1, 1, 'Goods held for sale', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(7, '1500', 'Fixed Assets', 1, NULL, 'Long-term tangible assets', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(8, '2000', 'Current Liabilities', 2, NULL, 'Obligations due within one year', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(9, '2100', 'Accounts Payable', 2, 8, 'Money owed to suppliers', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(10, '2200', 'Salaries Payable', 2, 8, 'Unpaid employee salaries', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(11, '2210', 'EPF Payable', 2, 8, 'Employee Provident Fund contributions payable', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(12, '2220', 'ETF Payable', 2, 8, 'Employees Trust Fund contributions payable', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(13, '3000', 'Owner\'s Equity', 3, NULL, 'Owner\'s investment and retained earnings', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(14, '3100', 'Capital', 3, 13, 'Initial and additional investments by owner', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(15, '3200', 'Retained Earnings', 3, 13, 'Cumulative net income retained in the business', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(16, '4000', 'Sales Revenue', 4, NULL, 'Revenue from sales of goods', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(17, '4100', 'Product Sales', 4, 16, 'Revenue from product sales', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(18, '4200', 'Sales Returns and Allowances', 4, 16, 'Contra-revenue account for returns', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(19, '5000', 'Cost of Goods Sold', 5, NULL, 'Direct costs of goods sold', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(20, '5100', 'Purchases', 5, 19, 'Cost of inventory purchased', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(21, '6000', 'Operating Expenses', 5, NULL, 'Regular business operating expenses', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(22, '6100', 'Salaries and Wages', 5, 21, 'Employee compensation expenses', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(23, '6200', 'Rent Expense', 5, 21, 'Cost of renting business premises', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(24, '6300', 'Utilities Expense', 5, 21, 'Electricity, water, internet, etc.', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(25, '6400', 'Office Supplies Expense', 5, 21, 'Cost of office supplies consumed', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(26, '6500', 'Marketing and Advertising', 5, 21, 'Marketing and promotional expenses', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(27, '6600', 'Maintenance and Repairs', 5, 21, 'Costs to maintain and repair assets', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(28, '6700', 'Transportation Expense', 5, 21, 'Delivery and transportation costs', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(29, '6800', 'Professional Fees', 5, 21, 'Legal, accounting, and consulting fees', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(30, '6900', 'Miscellaneous Expenses', 5, 21, 'Other operating expenses', 1, '2025-11-25 16:39:56', '2025-11-25 16:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `account_balances`
--

CREATE TABLE `account_balances` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `fiscal_year` int NOT NULL,
  `fiscal_period` int NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `debit_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `closing_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_balances`
--

INSERT INTO `account_balances` (`id`, `account_id`, `fiscal_year`, `fiscal_period`, `opening_balance`, `debit_total`, `credit_total`, `closing_balance`, `created_at`, `updated_at`) VALUES
(1, 6, 2025, 11, 0.00, 0.00, 0.00, 0.00, '2025-11-27 04:30:35', '2025-11-27 05:04:22'),
(2, 3, 2025, 11, 0.00, 0.00, 0.00, 0.00, '2025-11-27 04:30:35', '2025-11-27 05:04:22'),
(3, 3, 2025, 12, 0.00, 7704.87, 0.00, 7704.87, '2025-12-05 10:16:58', '2025-12-05 16:20:47'),
(4, 17, 2025, 12, 0.00, 0.00, 7704.87, 7704.87, '2025-12-05 10:16:58', '2025-12-05 16:20:47'),
(5, 20, 2025, 12, 0.00, 7247.00, 0.00, 7247.00, '2025-12-05 10:16:58', '2025-12-05 16:20:47'),
(6, 6, 2025, 12, 0.00, 0.00, 7247.00, -7247.00, '2025-12-05 10:16:58', '2025-12-05 16:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `normal_balance` enum('debit','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_types`
--

INSERT INTO `account_types` (`id`, `name`, `normal_balance`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Asset', 'debit', 'Balance Sheet', '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(2, 'Liability', 'credit', 'Balance Sheet', '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(3, 'Equity', 'credit', 'Balance Sheet', '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(4, 'Revenue', 'credit', 'Income Statement', '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(5, 'Expense', 'debit', 'Income Statement', '2025-11-25 16:39:56', '2025-11-25 16:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` bigint UNSIGNED NOT NULL,
  `batch_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `good_receive_note_id` bigint UNSIGNED NOT NULL,
  `manufacture_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `brand_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `brand_name`, `description`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'Elephant House', NULL, NULL, '2025-12-05 16:54:00', '2025-12-05 16:54:00'),
(2, 'SMACK', NULL, NULL, '2025-12-05 17:12:28', '2025-12-05 17:12:28'),
(3, 'Wijaya Products', NULL, NULL, '2025-12-05 17:46:56', '2025-12-05 17:46:56'),
(4, 'Welldoor Lanka', NULL, NULL, '2025-12-05 17:49:23', '2025-12-05 17:49:23');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('pos_cache_0a1fcd9e39114ce9c68fb3f3efe401e0', 'i:1;', 1764929360),
('pos_cache_0a1fcd9e39114ce9c68fb3f3efe401e0:timer', 'i:1764929360;', 1764929360),
('pos_cache_0d379fc15b89b1a351626579bac86477', 'i:1;', 1764962366),
('pos_cache_0d379fc15b89b1a351626579bac86477:timer', 'i:1764962366;', 1764962366),
('pos_cache_15a1fa09b555dde892bddac19e04af70', 'i:1;', 1764163103),
('pos_cache_15a1fa09b555dde892bddac19e04af70:timer', 'i:1764163103;', 1764163103),
('pos_cache_204f957bddf41f604ef6e9cf433c4aa9', 'i:1;', 1764933042),
('pos_cache_204f957bddf41f604ef6e9cf433c4aa9:timer', 'i:1764933042;', 1764933042),
('pos_cache_21241f57841b5c398f8ffa38295059cf', 'i:2;', 1764352025),
('pos_cache_21241f57841b5c398f8ffa38295059cf:timer', 'i:1764352025;', 1764352025),
('pos_cache_3071037d3a2171b179747b40167d8173', 'i:1;', 1764217103),
('pos_cache_3071037d3a2171b179747b40167d8173:timer', 'i:1764217103;', 1764217103),
('pos_cache_30e0494869ce17e7a3850cf829e567b2', 'i:3;', 1764179305),
('pos_cache_30e0494869ce17e7a3850cf829e567b2:timer', 'i:1764179305;', 1764179305),
('pos_cache_384bad7f655c8882a1bc185d1590c3e8', 'i:2;', 1764945328),
('pos_cache_384bad7f655c8882a1bc185d1590c3e8:timer', 'i:1764945328;', 1764945328),
('pos_cache_4e56a103242c036b0c42689ae3eed5ac', 'i:1;', 1764352041),
('pos_cache_4e56a103242c036b0c42689ae3eed5ac:timer', 'i:1764352041;', 1764352041),
('pos_cache_4f38eaf18300151e644f430d5fffc200', 'i:1;', 1764933004),
('pos_cache_4f38eaf18300151e644f430d5fffc200:timer', 'i:1764933004;', 1764933004),
('pos_cache_5045ff511b0d1636cb4c9ae90506be5f', 'i:1;', 1764221336),
('pos_cache_5045ff511b0d1636cb4c9ae90506be5f:timer', 'i:1764221336;', 1764221336),
('pos_cache_67910940c9233875044f6e60baf01d0c', 'i:1;', 1764954031),
('pos_cache_67910940c9233875044f6e60baf01d0c:timer', 'i:1764954031;', 1764954031),
('pos_cache_6bf9c6f24f7fc6ad31b55359ba842f75', 'i:1;', 1764939818),
('pos_cache_6bf9c6f24f7fc6ad31b55359ba842f75:timer', 'i:1764939818;', 1764939818),
('pos_cache_70c5eb0772bd7b41729357d24029ddf3', 'i:1;', 1764773030),
('pos_cache_70c5eb0772bd7b41729357d24029ddf3:timer', 'i:1764773030;', 1764773030),
('pos_cache_7255095b6f04eda89d71e989567f16ef', 'i:1;', 1764209445),
('pos_cache_7255095b6f04eda89d71e989567f16ef:timer', 'i:1764209445;', 1764209445),
('pos_cache_7f111a54f411e9298339c3299846c06c', 'i:1;', 1764840082),
('pos_cache_7f111a54f411e9298339c3299846c06c:timer', 'i:1764840082;', 1764840082),
('pos_cache_8c3f2fa374f486fed5854904d8fe84b4', 'i:1;', 1764933835),
('pos_cache_8c3f2fa374f486fed5854904d8fe84b4:timer', 'i:1764933835;', 1764933835),
('pos_cache_8d552cbe6f7c18639274a25258d789f2', 'i:1;', 1764928987),
('pos_cache_8d552cbe6f7c18639274a25258d789f2:timer', 'i:1764928987;', 1764928987),
('pos_cache_98104268f3cfa3eac1cd3081fc4413f0', 'i:1;', 1764939880),
('pos_cache_98104268f3cfa3eac1cd3081fc4413f0:timer', 'i:1764939880;', 1764939880),
('pos_cache_9a415c0982f78fd1511c5c1de5e30e9e', 'i:5;', 1764696721),
('pos_cache_9a415c0982f78fd1511c5c1de5e30e9e:timer', 'i:1764696721;', 1764696721),
('pos_cache_a3894bf097714a495bc2bce136109a28', 'i:1;', 1764929314),
('pos_cache_a3894bf097714a495bc2bce136109a28:timer', 'i:1764929314;', 1764929314),
('pos_cache_a412803483c4fee291452e15a4b84086', 'i:3;', 1764929325),
('pos_cache_a412803483c4fee291452e15a4b84086:timer', 'i:1764929325;', 1764929325),
('pos_cache_a62e0c4e08cd50818457050bda4b7fd5', 'i:3;', 1764217073),
('pos_cache_a62e0c4e08cd50818457050bda4b7fd5:timer', 'i:1764217073;', 1764217073),
('pos_cache_a892ae406be6a809e54a9a1efda4efde', 'i:5;', 1764697039),
('pos_cache_a892ae406be6a809e54a9a1efda4efde:timer', 'i:1764697039;', 1764697039),
('pos_cache_admn|111.223.182.39', 'i:1;', 1764160579),
('pos_cache_admn|111.223.182.39:timer', 'i:1764160579;', 1764160579),
('pos_cache_bxoi|175.157.43.145', 'i:5;', 1764697025),
('pos_cache_bxoi|175.157.43.145:timer', 'i:1764697025;', 1764697025),
('pos_cache_c3dc12966e0feada2488b07d7c635adb', 'i:1;', 1764160579),
('pos_cache_c3dc12966e0feada2488b07d7c635adb:timer', 'i:1764160579;', 1764160579),
('pos_cache_c530f1399511dd4cf13f5b7b54b2d2bc', 'i:1;', 1764929519),
('pos_cache_c530f1399511dd4cf13f5b7b54b2d2bc:timer', 'i:1764929519;', 1764929519),
('pos_cache_c81b7d9e8f1339673a8b602e7fc2d2e6', 'i:5;', 1764697015),
('pos_cache_c81b7d9e8f1339673a8b602e7fc2d2e6:timer', 'i:1764697015;', 1764697015),
('pos_cache_d4ba05a9b7a07385717f53acd84aa9c9', 'i:5;', 1764697025),
('pos_cache_d4ba05a9b7a07385717f53acd84aa9c9:timer', 'i:1764697025;', 1764697025),
('pos_cache_dashboard.active_customers', 'i:0;', 1764961623),
('pos_cache_dashboard.customer_credits', 'a:3:{s:5:\"count\";i:0;s:12:\"total_amount\";i:0;s:7:\"credits\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1764961323),
('pos_cache_dashboard.expiring_batches', 'a:4:{s:7:\"days_30\";i:0;s:7:\"days_60\";i:0;s:7:\"days_90\";i:0;s:7:\"batches\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1764961623);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('pos_cache_dashboard.out_of_stock', 'a:2:{s:5:\"count\";i:34;s:8:\"products\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:34:{i:0;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:1;s:12:\"product_name\";s:9:\"EGB 250ML\";s:3:\"sku\";s:10:\"SKU-000001\";s:11:\"description\";N;s:13:\"initial_stock\";i:12;s:13:\"minimum_stock\";i:1;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:07:33\";s:10:\"updated_at\";s:19:\"2025-12-05 18:25:39\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:1;s:12:\"product_name\";s:9:\"EGB 250ML\";s:3:\"sku\";s:10:\"SKU-000001\";s:11:\"description\";N;s:13:\"initial_stock\";i:12;s:13:\"minimum_stock\";i:1;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:07:33\";s:10:\"updated_at\";s:19:\"2025-12-05 18:25:39\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";O:19:\"App\\Models\\Category\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:1;s:8:\"cat_name\";s:9:\"Beverages\";s:11:\"description\";s:26:\"Cola , Soda , Yogurt Drink\";s:4:\"icon\";s:13:\"beverages.png\";s:10:\"created_at\";s:19:\"2025-12-05 17:44:32\";s:10:\"updated_at\";s:19:\"2025-12-05 17:51:14\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:1;s:8:\"cat_name\";s:9:\"Beverages\";s:11:\"description\";s:26:\"Cola , Soda , Yogurt Drink\";s:4:\"icon\";s:13:\"beverages.png\";s:10:\"created_at\";s:19:\"2025-12-05 17:44:32\";s:10:\"updated_at\";s:19:\"2025-12-05 17:51:14\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:8:\"cat_name\";i:1;s:11:\"description\";i:2;s:4:\"icon\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:5:\"brand\";O:16:\"App\\Models\\Brand\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"brands\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:1;s:10:\"brand_name\";s:14:\"Elephant House\";s:11:\"description\";N;s:4:\"logo\";N;s:10:\"created_at\";s:19:\"2025-12-05 17:54:00\";s:10:\"updated_at\";s:19:\"2025-12-05 17:54:00\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:1;s:10:\"brand_name\";s:14:\"Elephant House\";s:11:\"description\";N;s:4:\"logo\";N;s:10:\"created_at\";s:19:\"2025-12-05 17:54:00\";s:10:\"updated_at\";s:19:\"2025-12-05 17:54:00\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:10:\"brand_name\";i:1;s:11:\"description\";i:2;s:4:\"logo\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:2;s:12:\"product_name\";s:6:\"EGB 1L\";s:3:\"sku\";s:10:\"SKU-000002\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";N;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:07:56\";s:10:\"updated_at\";s:19:\"2025-12-05 18:37:27\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:2;s:12:\"product_name\";s:6:\"EGB 1L\";s:3:\"sku\";s:10:\"SKU-000002\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";N;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:07:56\";s:10:\"updated_at\";s:19:\"2025-12-05 18:37:27\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:3;s:12:\"product_name\";s:24:\"NECTAR-MIXED FRUIT 200ML\";s:3:\"sku\";s:10:\"SKU-000003\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:15;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:13:09\";s:10:\"updated_at\";s:19:\"2025-12-05 18:43:58\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:3;s:12:\"product_name\";s:24:\"NECTAR-MIXED FRUIT 200ML\";s:3:\"sku\";s:10:\"SKU-000003\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:15;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:13:09\";s:10:\"updated_at\";s:19:\"2025-12-05 18:43:58\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";O:16:\"App\\Models\\Brand\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"brands\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:2;s:10:\"brand_name\";s:5:\"SMACK\";s:11:\"description\";N;s:4:\"logo\";N;s:10:\"created_at\";s:19:\"2025-12-05 18:12:28\";s:10:\"updated_at\";s:19:\"2025-12-05 18:12:28\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:2;s:10:\"brand_name\";s:5:\"SMACK\";s:11:\"description\";N;s:4:\"logo\";N;s:10:\"created_at\";s:19:\"2025-12-05 18:12:28\";s:10:\"updated_at\";s:19:\"2025-12-05 18:12:28\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:10:\"brand_name\";i:1;s:11:\"description\";i:2;s:4:\"logo\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:4;s:12:\"product_name\";s:22:\"NECTAR-ALOE VERA 200ML\";s:3:\"sku\";s:10:\"SKU-000004\";s:11:\"description\";N;s:13:\"initial_stock\";i:15;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:15;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:13:46\";s:10:\"updated_at\";s:19:\"2025-12-05 18:44:23\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:4;s:12:\"product_name\";s:22:\"NECTAR-ALOE VERA 200ML\";s:3:\"sku\";s:10:\"SKU-000004\";s:11:\"description\";N;s:13:\"initial_stock\";i:15;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:15;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:13:46\";s:10:\"updated_at\";s:19:\"2025-12-05 18:44:23\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:4;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:5;s:12:\"product_name\";s:24:\"NECTAR-MIXED FRUIT 500ML\";s:3:\"sku\";s:10:\"SKU-000005\";s:11:\"description\";N;s:13:\"initial_stock\";i:10;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:10;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:15:47\";s:10:\"updated_at\";s:19:\"2025-12-05 18:45:38\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:5;s:12:\"product_name\";s:24:\"NECTAR-MIXED FRUIT 500ML\";s:3:\"sku\";s:10:\"SKU-000005\";s:11:\"description\";N;s:13:\"initial_stock\";i:10;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:10;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:15:47\";s:10:\"updated_at\";s:19:\"2025-12-05 18:45:38\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:5;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:6;s:12:\"product_name\";s:23:\"NECTAR-WOOD APPLE 500ML\";s:3:\"sku\";s:10:\"SKU-000006\";s:11:\"description\";N;s:13:\"initial_stock\";i:6;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:16:29\";s:10:\"updated_at\";s:19:\"2025-12-05 18:46:04\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:6;s:12:\"product_name\";s:23:\"NECTAR-WOOD APPLE 500ML\";s:3:\"sku\";s:10:\"SKU-000006\";s:11:\"description\";N;s:13:\"initial_stock\";i:6;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:16:29\";s:10:\"updated_at\";s:19:\"2025-12-05 18:46:04\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:6;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:7;s:12:\"product_name\";s:22:\"NECTAR-ALOE VERA 500ML\";s:3:\"sku\";s:10:\"SKU-000007\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:10;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:24:27\";s:10:\"updated_at\";s:19:\"2025-12-05 18:46:15\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:7;s:12:\"product_name\";s:22:\"NECTAR-ALOE VERA 500ML\";s:3:\"sku\";s:10:\"SKU-000007\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:10;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:24:27\";s:10:\"updated_at\";s:19:\"2025-12-05 18:46:15\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:7;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:8;s:12:\"product_name\";s:23:\"NECTAR-WOOD APPLE 200ML\";s:3:\"sku\";s:10:\"SKU-000008\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:25:01\";s:10:\"updated_at\";s:19:\"2025-12-05 18:44:39\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:8;s:12:\"product_name\";s:23:\"NECTAR-WOOD APPLE 200ML\";s:3:\"sku\";s:10:\"SKU-000008\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:25:01\";s:10:\"updated_at\";s:19:\"2025-12-05 18:44:39\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:8;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:9;s:12:\"product_name\";s:21:\"NECTAR-MIXED FRUIT 1L\";s:3:\"sku\";s:10:\"SKU-000009\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:25:52\";s:10:\"updated_at\";s:19:\"2025-12-05 18:46:30\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:9;s:12:\"product_name\";s:21:\"NECTAR-MIXED FRUIT 1L\";s:3:\"sku\";s:10:\"SKU-000009\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:25:52\";s:10:\"updated_at\";s:19:\"2025-12-05 18:46:30\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:9;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:10;s:12:\"product_name\";s:9:\"EGB 500ML\";s:3:\"sku\";s:10:\"SKU-000010\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";i:1;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:3:\"pcs\";s:13:\"purchase_unit\";N;s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:26:21\";s:10:\"updated_at\";s:19:\"2025-12-05 18:26:21\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:10;s:12:\"product_name\";s:9:\"EGB 500ML\";s:3:\"sku\";s:10:\"SKU-000010\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";i:1;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:3:\"pcs\";s:13:\"purchase_unit\";N;s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:26:21\";s:10:\"updated_at\";s:19:\"2025-12-05 18:26:21\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:10;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:11;s:12:\"product_name\";s:20:\"NECTAR-WOOD APPLE 1L\";s:3:\"sku\";s:10:\"SKU-000011\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:2;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:26:37\";s:10:\"updated_at\";s:19:\"2025-12-05 18:46:54\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:11;s:12:\"product_name\";s:20:\"NECTAR-WOOD APPLE 1L\";s:3:\"sku\";s:10:\"SKU-000011\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:2;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:26:37\";s:10:\"updated_at\";s:19:\"2025-12-05 18:46:54\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:11;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:12;s:12:\"product_name\";s:8:\"EGB 1.5L\";s:3:\"sku\";s:10:\"SKU-000012\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";N;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:27:09\";s:10:\"updated_at\";s:19:\"2025-12-05 18:37:38\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:12;s:12:\"product_name\";s:8:\"EGB 1.5L\";s:3:\"sku\";s:10:\"SKU-000012\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";N;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:27:09\";s:10:\"updated_at\";s:19:\"2025-12-05 18:37:38\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:12;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:13;s:12:\"product_name\";s:18:\"ORANGE CRUSH 250ML\";s:3:\"sku\";s:10:\"SKU-000013\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:27:39\";s:10:\"updated_at\";s:19:\"2025-12-05 18:29:48\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:13;s:12:\"product_name\";s:18:\"ORANGE CRUSH 250ML\";s:3:\"sku\";s:10:\"SKU-000013\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:27:39\";s:10:\"updated_at\";s:19:\"2025-12-05 18:29:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:13;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:14;s:12:\"product_name\";s:18:\"ORANGE CRUSH 500ML\";s:3:\"sku\";s:10:\"SKU-000014\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:8;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:28:11\";s:10:\"updated_at\";s:19:\"2025-12-05 18:30:00\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:14;s:12:\"product_name\";s:18:\"ORANGE CRUSH 500ML\";s:3:\"sku\";s:10:\"SKU-000014\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:8;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:28:11\";s:10:\"updated_at\";s:19:\"2025-12-05 18:30:00\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:14;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:15;s:12:\"product_name\";s:15:\"ORANGE CRUSH 1L\";s:3:\"sku\";s:10:\"SKU-000015\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:28:32\";s:10:\"updated_at\";s:19:\"2025-12-05 18:37:50\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:15;s:12:\"product_name\";s:15:\"ORANGE CRUSH 1L\";s:3:\"sku\";s:10:\"SKU-000015\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:28:32\";s:10:\"updated_at\";s:19:\"2025-12-05 18:37:50\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:15;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:16;s:12:\"product_name\";s:17:\"ORANGE CRUSH 1.5L\";s:3:\"sku\";s:10:\"SKU-000016\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:28:51\";s:10:\"updated_at\";s:19:\"2025-12-05 18:37:20\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:16;s:12:\"product_name\";s:17:\"ORANGE CRUSH 1.5L\";s:3:\"sku\";s:10:\"SKU-000016\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:28:51\";s:10:\"updated_at\";s:19:\"2025-12-05 18:37:20\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:16;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:17;s:12:\"product_name\";s:16:\"CREAM SODA 250ML\";s:3:\"sku\";s:10:\"SKU-000017\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:29:17\";s:10:\"updated_at\";s:19:\"2025-12-05 18:29:17\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:17;s:12:\"product_name\";s:16:\"CREAM SODA 250ML\";s:3:\"sku\";s:10:\"SKU-000017\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:29:17\";s:10:\"updated_at\";s:19:\"2025-12-05 18:29:17\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:17;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:18;s:12:\"product_name\";s:24:\"CASAVA - HOT & SPICY 50G\";s:3:\"sku\";s:10:\"SKU-000018\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:3;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"g\";s:13:\"purchase_unit\";s:1:\"g\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:29:54\";s:10:\"updated_at\";s:19:\"2025-12-05 18:47:22\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:18;s:12:\"product_name\";s:24:\"CASAVA - HOT & SPICY 50G\";s:3:\"sku\";s:10:\"SKU-000018\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:3;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"g\";s:13:\"purchase_unit\";s:1:\"g\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:29:54\";s:10:\"updated_at\";s:19:\"2025-12-05 18:47:22\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";O:19:\"App\\Models\\Category\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:3;s:8:\"cat_name\";s:6:\"Snacks\";s:11:\"description\";s:31:\"Chips , Peanuts , Mixture Bites\";s:4:\"icon\";s:10:\"snacks.png\";s:10:\"created_at\";s:19:\"2025-12-05 17:45:09\";s:10:\"updated_at\";s:19:\"2025-12-05 17:50:53\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:3;s:8:\"cat_name\";s:6:\"Snacks\";s:11:\"description\";s:31:\"Chips , Peanuts , Mixture Bites\";s:4:\"icon\";s:10:\"snacks.png\";s:10:\"created_at\";s:19:\"2025-12-05 17:45:09\";s:10:\"updated_at\";s:19:\"2025-12-05 17:50:53\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:8:\"cat_name\";i:1;s:11:\"description\";i:2;s:4:\"icon\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:18;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:19;s:12:\"product_name\";s:19:\"CASAVA - TOMATO 50G\";s:3:\"sku\";s:10:\"SKU-000019\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:3;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"g\";s:13:\"purchase_unit\";s:1:\"g\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:30:23\";s:10:\"updated_at\";s:19:\"2025-12-05 18:48:10\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:19;s:12:\"product_name\";s:19:\"CASAVA - TOMATO 50G\";s:3:\"sku\";s:10:\"SKU-000019\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:3;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"g\";s:13:\"purchase_unit\";s:1:\"g\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:30:23\";s:10:\"updated_at\";s:19:\"2025-12-05 18:48:10\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:1692;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:19;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:20;s:12:\"product_name\";s:16:\"CASAVA - BBQ 50G\";s:3:\"sku\";s:10:\"SKU-000020\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:3;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"g\";s:13:\"purchase_unit\";s:1:\"g\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:30:45\";s:10:\"updated_at\";s:19:\"2025-12-05 18:48:22\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:20;s:12:\"product_name\";s:16:\"CASAVA - BBQ 50G\";s:3:\"sku\";s:10:\"SKU-000020\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:3;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"g\";s:13:\"purchase_unit\";s:1:\"g\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:30:45\";s:10:\"updated_at\";s:19:\"2025-12-05 18:48:22\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:1692;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:20;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:21;s:12:\"product_name\";s:16:\"CREAM SODA 500ML\";s:3:\"sku\";s:10:\"SKU-000021\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:8;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:34:02\";s:10:\"updated_at\";s:19:\"2025-12-05 18:34:02\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:21;s:12:\"product_name\";s:16:\"CREAM SODA 500ML\";s:3:\"sku\";s:10:\"SKU-000021\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:8;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:34:02\";s:10:\"updated_at\";s:19:\"2025-12-05 18:34:02\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:21;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:22;s:12:\"product_name\";s:13:\"CREAM SODA 1L\";s:3:\"sku\";s:10:\"SKU-000022\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:34:26\";s:10:\"updated_at\";s:19:\"2025-12-05 18:36:55\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:22;s:12:\"product_name\";s:13:\"CREAM SODA 1L\";s:3:\"sku\";s:10:\"SKU-000022\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:34:26\";s:10:\"updated_at\";s:19:\"2025-12-05 18:36:55\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:22;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:23;s:12:\"product_name\";s:15:\"CREAM SODA 1.5L\";s:3:\"sku\";s:10:\"SKU-000023\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:5;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:35:02\";s:10:\"updated_at\";s:19:\"2025-12-05 18:36:46\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:23;s:12:\"product_name\";s:15:\"CREAM SODA 1.5L\";s:3:\"sku\";s:10:\"SKU-000023\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:5;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:35:02\";s:10:\"updated_at\";s:19:\"2025-12-05 18:36:46\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:23;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:24;s:12:\"product_name\";s:11:\"NECTO 250ML\";s:3:\"sku\";s:10:\"SKU-000024\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:35:35\";s:10:\"updated_at\";s:19:\"2025-12-05 18:35:35\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:24;s:12:\"product_name\";s:11:\"NECTO 250ML\";s:3:\"sku\";s:10:\"SKU-000024\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:35:35\";s:10:\"updated_at\";s:19:\"2025-12-05 18:35:35\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:24;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:25;s:12:\"product_name\";s:11:\"NECTO 500ML\";s:3:\"sku\";s:10:\"SKU-000025\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:22;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:36:03\";s:10:\"updated_at\";s:19:\"2025-12-05 18:36:03\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:25;s:12:\"product_name\";s:11:\"NECTO 500ML\";s:3:\"sku\";s:10:\"SKU-000025\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:22;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:36:03\";s:10:\"updated_at\";s:19:\"2025-12-05 18:36:03\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:25;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:26;s:12:\"product_name\";s:8:\"NECTO 1L\";s:3:\"sku\";s:10:\"SKU-000026\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:36:25\";s:10:\"updated_at\";s:19:\"2025-12-05 18:36:37\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:26;s:12:\"product_name\";s:8:\"NECTO 1L\";s:3:\"sku\";s:10:\"SKU-000026\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:36:25\";s:10:\"updated_at\";s:19:\"2025-12-05 18:36:37\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:26;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:27;s:12:\"product_name\";s:10:\"NECTO 1.5L\";s:3:\"sku\";s:10:\"SKU-000027\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:38:32\";s:10:\"updated_at\";s:19:\"2025-12-05 18:38:32\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:27;s:12:\"product_name\";s:10:\"NECTO 1.5L\";s:3:\"sku\";s:10:\"SKU-000027\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:4;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:38:32\";s:10:\"updated_at\";s:19:\"2025-12-05 18:38:32\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:27;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:28;s:12:\"product_name\";s:10:\"SODA 500ML\";s:3:\"sku\";s:10:\"SKU-000028\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:38:56\";s:10:\"updated_at\";s:19:\"2025-12-05 18:38:56\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:28;s:12:\"product_name\";s:10:\"SODA 500ML\";s:3:\"sku\";s:10:\"SKU-000028\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:38:56\";s:10:\"updated_at\";s:19:\"2025-12-05 18:38:56\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:28;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:29;s:12:\"product_name\";s:7:\"SODA 1L\";s:3:\"sku\";s:10:\"SKU-000029\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:39:23\";s:10:\"updated_at\";s:19:\"2025-12-05 18:39:23\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:29;s:12:\"product_name\";s:7:\"SODA 1L\";s:3:\"sku\";s:10:\"SKU-000029\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:39:23\";s:10:\"updated_at\";s:19:\"2025-12-05 18:39:23\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:29;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:30;s:12:\"product_name\";s:35:\"WATER BOTTLE (elephant house) 500ML\";s:3:\"sku\";s:10:\"SKU-000030\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:24;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:41:16\";s:10:\"updated_at\";s:19:\"2025-12-05 18:41:16\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:30;s:12:\"product_name\";s:35:\"WATER BOTTLE (elephant house) 500ML\";s:3:\"sku\";s:10:\"SKU-000030\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:24;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:41:16\";s:10:\"updated_at\";s:19:\"2025-12-05 18:41:16\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:30;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:31;s:12:\"product_name\";s:32:\"WATER BOTTLE (elephant house) 1L\";s:3:\"sku\";s:10:\"SKU-000031\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:41:41\";s:10:\"updated_at\";s:19:\"2025-12-05 18:41:41\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:31;s:12:\"product_name\";s:32:\"WATER BOTTLE (elephant house) 1L\";s:3:\"sku\";s:10:\"SKU-000031\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:12;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:41:41\";s:10:\"updated_at\";s:19:\"2025-12-05 18:41:41\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:31;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:32;s:12:\"product_name\";s:34:\"WATER BOTTLE (elephant house) 1.5L\";s:3:\"sku\";s:10:\"SKU-000032\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";N;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:41:59\";s:10:\"updated_at\";s:19:\"2025-12-05 18:41:59\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:32;s:12:\"product_name\";s:34:\"WATER BOTTLE (elephant house) 1.5L\";s:3:\"sku\";s:10:\"SKU-000032\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";N;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:1;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"L\";s:13:\"purchase_unit\";s:1:\"L\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:41:59\";s:10:\"updated_at\";s:19:\"2025-12-05 18:41:59\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:116;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:32;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:33;s:12:\"product_name\";s:18:\"NECTAR-MANGO 200ML\";s:3:\"sku\";s:10:\"SKU-000033\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";N;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:45:22\";s:10:\"updated_at\";s:19:\"2025-12-05 18:45:22\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:33;s:12:\"product_name\";s:18:\"NECTAR-MANGO 200ML\";s:3:\"sku\";s:10:\"SKU-000033\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";N;s:13:\"product_image\";N;s:11:\"category_id\";i:1;s:8:\"brand_id\";i:2;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:2:\"ml\";s:13:\"purchase_unit\";s:2:\"ml\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:45:22\";s:10:\"updated_at\";s:19:\"2025-12-05 18:45:22\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:66;s:5:\"brand\";r:339;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:33;O:18:\"App\\Models\\Product\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";i:34;s:12:\"product_name\";s:11:\"MIXTURE 50G\";s:3:\"sku\";s:10:\"SKU-000034\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:20;s:13:\"product_image\";N;s:11:\"category_id\";i:3;s:8:\"brand_id\";i:4;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"g\";s:13:\"purchase_unit\";s:1:\"g\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:51:15\";s:10:\"updated_at\";s:19:\"2025-12-05 18:51:15\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";i:34;s:12:\"product_name\";s:11:\"MIXTURE 50G\";s:3:\"sku\";s:10:\"SKU-000034\";s:11:\"description\";N;s:13:\"initial_stock\";N;s:13:\"minimum_stock\";N;s:13:\"maximum_stock\";i:20;s:13:\"product_image\";N;s:11:\"category_id\";i:3;s:8:\"brand_id\";i:4;s:4:\"unit\";s:3:\"pcs\";s:9:\"base_unit\";s:1:\"g\";s:13:\"purchase_unit\";s:1:\"g\";s:17:\"conversion_factor\";s:6:\"1.0000\";s:19:\"allow_decimal_sales\";i:0;s:10:\"created_at\";s:19:\"2025-12-05 18:51:15\";s:10:\"updated_at\";s:19:\"2025-12-05 18:51:15\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"conversion_factor\";s:9:\"decimal:4\";s:19:\"allow_decimal_sales\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:8:\"category\";r:1692;s:5:\"brand\";O:16:\"App\\Models\\Brand\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"brands\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:4;s:10:\"brand_name\";s:14:\"Welldoor Lanka\";s:11:\"description\";N;s:4:\"logo\";N;s:10:\"created_at\";s:19:\"2025-12-05 18:49:23\";s:10:\"updated_at\";s:19:\"2025-12-05 18:49:23\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:4;s:10:\"brand_name\";s:14:\"Welldoor Lanka\";s:11:\"description\";N;s:4:\"logo\";N;s:10:\"created_at\";s:19:\"2025-12-05 18:49:23\";s:10:\"updated_at\";s:19:\"2025-12-05 18:49:23\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:10:\"brand_name\";i:1;s:11:\"description\";i:2;s:4:\"logo\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:12:\"product_name\";i:1;s:3:\"sku\";i:2;s:11:\"description\";i:3;s:13:\"initial_stock\";i:4;s:13:\"minimum_stock\";i:5;s:13:\"maximum_stock\";i:6;s:13:\"product_image\";i:7;s:11:\"category_id\";i:8;s:8:\"brand_id\";i:9;s:4:\"unit\";i:10;s:9:\"base_unit\";i:11;s:13:\"purchase_unit\";i:12;s:17:\"conversion_factor\";i:13;s:19:\"allow_decimal_sales\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1764961323);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('pos_cache_dashboard.overdue_credits', 'a:3:{s:5:\"count\";i:0;s:12:\"total_amount\";i:0;s:7:\"credits\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1764961323),
('pos_cache_dashboard.profit_margin', 'd:5.94;', 1764961623),
('pos_cache_dashboard.profit_over_time.daily', 'a:3:{s:6:\"labels\";a:7:{i:0;s:6:\"Nov 29\";i:1;s:6:\"Nov 30\";i:2;s:6:\"Dec 01\";i:3;s:6:\"Dec 02\";i:4;s:6:\"Dec 03\";i:5;s:6:\"Dec 04\";i:6;s:6:\"Dec 05\";}s:12:\"gross_profit\";a:7:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;d:457.87;}s:10:\"net_profit\";a:7:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;d:457.87;}}', 1764961323),
('pos_cache_dashboard.profit_over_time.monthly', 'a:3:{s:6:\"labels\";a:12:{i:0;s:8:\"Jan 2025\";i:1;s:8:\"Feb 2025\";i:2;s:8:\"Mar 2025\";i:3;s:8:\"Apr 2025\";i:4;s:8:\"May 2025\";i:5;s:8:\"Jun 2025\";i:6;s:8:\"Jul 2025\";i:7;s:8:\"Aug 2025\";i:8;s:8:\"Sep 2025\";i:9;s:8:\"Oct 2025\";i:10;s:8:\"Nov 2025\";i:11;s:8:\"Dec 2025\";}s:12:\"gross_profit\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;d:197.87;}s:10:\"net_profit\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;d:197.87;}}', 1764940337),
('pos_cache_dashboard.supplier_credits', 'a:3:{s:5:\"count\";i:0;s:12:\"total_amount\";i:0;s:7:\"credits\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1764961323),
('pos_cache_dashboard.todays_sales', 'i:0;', 1764961023),
('pos_cache_dashboard.top_selling_products.month', 'O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:8:\"stdClass\":2:{s:12:\"product_name\";s:12:\"Test Product\";s:14:\"total_quantity\";s:1:\"3\";}i:1;O:8:\"stdClass\":2:{s:12:\"product_name\";s:12:\"demo-product\";s:14:\"total_quantity\";s:1:\"2\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1764940323),
('pos_cache_dashboard.top_selling_products.today', 'O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1764961323),
('pos_cache_dashboard.top_selling_products.week', 'O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:8:\"stdClass\":2:{s:12:\"product_name\";s:12:\"Test Product\";s:14:\"total_quantity\";s:1:\"3\";}i:1;O:8:\"stdClass\":2:{s:12:\"product_name\";s:12:\"demo-product\";s:14:\"total_quantity\";s:1:\"2\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1764940323),
('pos_cache_fd9d81bde122ed58e10e6f8c93aac643', 'i:1;', 1764179335),
('pos_cache_fd9d81bde122ed58e10e6f8c93aac643:timer', 'i:1764179335;', 1764179335),
('pos_cache_lzir|175.157.43.145', 'i:5;', 1764697015),
('pos_cache_lzir|175.157.43.145:timer', 'i:1764697015;', 1764697015),
('pos_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:193:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:13:\"view products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:15:\"create products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:13:\"edit products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:15:\"delete products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"view categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:17:\"create categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:15:\"edit categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:17:\"delete categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:11:\"view brands\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:13:\"create brands\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:11:\"edit brands\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:13:\"delete brands\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:17:\"view vendor codes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:19:\"create vendor codes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:17:\"edit vendor codes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:19:\"delete vendor codes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:14:\"view suppliers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:16:\"create suppliers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:14:\"edit suppliers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:16:\"delete suppliers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:9:\"view grns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:11:\"create grns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:9:\"edit grns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:11:\"delete grns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:12:\"view batches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:21:\"view expiring batches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:11:\"view stocks\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:15:\"manage stock in\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:12:\"create sales\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:10:\"view sales\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:17:\"view sale details\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:18:\"view sales returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:20:\"create sales returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:18:\"edit sales returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:20:\"delete sales returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:20:\"refund sales returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:20:\"cancel sales returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:21:\"view supplier returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:23:\"create supplier returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:21:\"edit supplier returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:23:\"delete supplier returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:24:\"approve supplier returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:25:\"complete supplier returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:23:\"cancel supplier returns\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:12:\"view reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:13:\"view expenses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:15:\"manage expenses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:6;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:10:\"view users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:12:\"create users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:10:\"edit users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:12:\"delete users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:12:\"assign roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:10:\"view roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:12:\"create roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:10:\"edit roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:12:\"delete roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:16:\"view permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:18:\"manage permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:18:\"manage saved carts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:17:\"manage own shifts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:11:\"view shifts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:13:\"manage shifts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:14:\"approve shifts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:14:\"view employees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:16:\"create employees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:14:\"edit employees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:16:\"delete employees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:12:\"view payroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:15:\"process payroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:6;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:15:\"approve payroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:16:\"view own payroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:20:\"view payroll reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:21:\"view supplier credits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:23:\"create supplier credits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:21:\"edit supplier credits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:23:\"delete supplier credits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:22:\"view supplier payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:24:\"create supplier payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:22:\"edit supplier payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:79;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:24:\"delete supplier payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:80;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:21:\"view creditor reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:81;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:19:\"view creditor aging\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:82;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:24:\"view supplier statements\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:83;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:24:\"manage payment reminders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:84;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:22:\"view chart of accounts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:85;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:15:\"create accounts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:86;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:13:\"edit accounts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:87;a:4:{s:1:\"a\";i:88;s:1:\"b\";s:15:\"delete accounts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:88;a:4:{s:1:\"a\";i:89;s:1:\"b\";s:20:\"view journal entries\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:89;a:4:{s:1:\"a\";i:90;s:1:\"b\";s:22:\"create journal entries\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:90;a:4:{s:1:\"a\";i:91;s:1:\"b\";s:20:\"post journal entries\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:91;a:4:{s:1:\"a\";i:92;s:1:\"b\";s:20:\"void journal entries\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:92;a:4:{s:1:\"a\";i:93;s:1:\"b\";s:21:\"view income statement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:93;a:4:{s:1:\"a\";i:94;s:1:\"b\";s:18:\"view balance sheet\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:94;a:4:{s:1:\"a\";i:95;s:1:\"b\";s:18:\"view trial balance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:95;a:4:{s:1:\"a\";i:96;s:1:\"b\";s:19:\"view general ledger\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:96;a:4:{s:1:\"a\";i:97;s:1:\"b\";s:19:\"view fiscal periods\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:97;a:4:{s:1:\"a\";i:98;s:1:\"b\";s:21:\"manage fiscal periods\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:98;a:4:{s:1:\"a\";i:99;s:1:\"b\";s:20:\"close fiscal periods\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:99;a:3:{s:1:\"a\";i:100;s:1:\"b\";s:5:\"login\";s:1:\"c\";s:3:\"web\";}i:100;a:3:{s:1:\"a\";i:101;s:1:\"b\";s:12:\"create login\";s:1:\"c\";s:3:\"web\";}i:101;a:3:{s:1:\"a\";i:102;s:1:\"b\";s:6:\"logout\";s:1:\"c\";s:3:\"web\";}i:102;a:3:{s:1:\"a\";i:103;s:1:\"b\";s:16:\"request password\";s:1:\"c\";s:3:\"web\";}i:103;a:3:{s:1:\"a\";i:104;s:1:\"b\";s:14:\"reset password\";s:1:\"c\";s:3:\"web\";}i:104;a:3:{s:1:\"a\";i:105;s:1:\"b\";s:14:\"email password\";s:1:\"c\";s:3:\"web\";}i:105;a:3:{s:1:\"a\";i:106;s:1:\"b\";s:13:\"edit password\";s:1:\"c\";s:3:\"web\";}i:106;a:3:{s:1:\"a\";i:107;s:1:\"b\";s:29:\"edit user-profile-information\";s:1:\"c\";s:3:\"web\";}i:107;a:3:{s:1:\"a\";i:108;s:1:\"b\";s:18:\"edit user-password\";s:1:\"c\";s:3:\"web\";}i:108;a:3:{s:1:\"a\";i:109;s:1:\"b\";s:16:\"confirm password\";s:1:\"c\";s:3:\"web\";}i:109;a:3:{s:1:\"a\";i:110;s:1:\"b\";s:21:\"confirmation password\";s:1:\"c\";s:3:\"web\";}i:110;a:3:{s:1:\"a\";i:111;s:1:\"b\";s:23:\"create password confirm\";s:1:\"c\";s:3:\"web\";}i:111;a:3:{s:1:\"a\";i:112;s:1:\"b\";s:17:\"dashboard cashier\";s:1:\"c\";s:3:\"web\";}i:112;a:3:{s:1:\"a\";i:113;s:1:\"b\";s:15:\"clock-in shifts\";s:1:\"c\";s:3:\"web\";}i:113;a:3:{s:1:\"a\";i:114;s:1:\"b\";s:16:\"clock-out shifts\";s:1:\"c\";s:3:\"web\";}i:114;a:3:{s:1:\"a\";i:115;s:1:\"b\";s:14:\"current shifts\";s:1:\"c\";s:3:\"web\";}i:115;a:3:{s:1:\"a\";i:116;s:1:\"b\";s:16:\"my-shifts shifts\";s:1:\"c\";s:3:\"web\";}i:116;a:3:{s:1:\"a\";i:117;s:1:\"b\";s:15:\"create expenses\";s:1:\"c\";s:3:\"web\";}i:117;a:3:{s:1:\"a\";i:118;s:1:\"b\";s:25:\"create expense-categories\";s:1:\"c\";s:3:\"web\";}i:118;a:3:{s:1:\"a\";i:119;s:1:\"b\";s:27:\"view api expense-categories\";s:1:\"c\";s:3:\"web\";}i:119;a:3:{s:1:\"a\";i:120;s:1:\"b\";s:13:\"edit expenses\";s:1:\"c\";s:3:\"web\";}i:120;a:3:{s:1:\"a\";i:121;s:1:\"b\";s:15:\"delete expenses\";s:1:\"c\";s:3:\"web\";}i:121;a:3:{s:1:\"a\";i:122;s:1:\"b\";s:16:\"approve expenses\";s:1:\"c\";s:3:\"web\";}i:122;a:3:{s:1:\"a\";i:123;s:1:\"b\";s:15:\"reject expenses\";s:1:\"c\";s:3:\"web\";}i:123;a:3:{s:1:\"a\";i:124;s:1:\"b\";s:21:\"mark-as-paid expenses\";s:1:\"c\";s:3:\"web\";}i:124;a:3:{s:1:\"a\";i:125;s:1:\"b\";s:18:\"view sales-returns\";s:1:\"c\";s:3:\"web\";}i:125;a:3:{s:1:\"a\";i:126;s:1:\"b\";s:20:\"create sales-returns\";s:1:\"c\";s:3:\"web\";}i:126;a:3:{s:1:\"a\";i:127;s:1:\"b\";s:20:\"refund sales-returns\";s:1:\"c\";s:3:\"web\";}i:127;a:3:{s:1:\"a\";i:128;s:1:\"b\";s:20:\"cancel sales-returns\";s:1:\"c\";s:3:\"web\";}i:128;a:3:{s:1:\"a\";i:129;s:1:\"b\";s:22:\"returnable-items sales\";s:1:\"c\";s:3:\"web\";}i:129;a:3:{s:1:\"a\";i:130;s:1:\"b\";s:21:\"view supplier-returns\";s:1:\"c\";s:3:\"web\";}i:130;a:3:{s:1:\"a\";i:131;s:1:\"b\";s:23:\"create supplier-returns\";s:1:\"c\";s:3:\"web\";}i:131;a:3:{s:1:\"a\";i:132;s:1:\"b\";s:24:\"approve supplier-returns\";s:1:\"c\";s:3:\"web\";}i:132;a:3:{s:1:\"a\";i:133;s:1:\"b\";s:25:\"complete supplier-returns\";s:1:\"c\";s:3:\"web\";}i:133;a:3:{s:1:\"a\";i:134;s:1:\"b\";s:23:\"cancel supplier-returns\";s:1:\"c\";s:3:\"web\";}i:134;a:3:{s:1:\"a\";i:135;s:1:\"b\";s:35:\"returnable-stock good-receive-notes\";s:1:\"c\";s:3:\"web\";}i:135;a:3:{s:1:\"a\";i:136;s:1:\"b\";s:19:\"search api products\";s:1:\"c\";s:3:\"web\";}i:136;a:3:{s:1:\"a\";i:137;s:1:\"b\";s:18:\"stock api products\";s:1:\"c\";s:3:\"web\";}i:137;a:3:{s:1:\"a\";i:138;s:1:\"b\";s:20:\"view api saved-carts\";s:1:\"c\";s:3:\"web\";}i:138;a:3:{s:1:\"a\";i:139;s:1:\"b\";s:22:\"create api saved-carts\";s:1:\"c\";s:3:\"web\";}i:139;a:3:{s:1:\"a\";i:140;s:1:\"b\";s:22:\"delete api saved-carts\";s:1:\"c\";s:3:\"web\";}i:140;a:3:{s:1:\"a\";i:141;s:1:\"b\";s:18:\"products suppliers\";s:1:\"c\";s:3:\"web\";}i:141;a:3:{s:1:\"a\";i:142;s:1:\"b\";s:21:\"credit-info suppliers\";s:1:\"c\";s:3:\"web\";}i:142;a:3:{s:1:\"a\";i:143;s:1:\"b\";s:23:\"view good-receive-notes\";s:1:\"c\";s:3:\"web\";}i:143;a:3:{s:1:\"a\";i:144;s:1:\"b\";s:25:\"create good-receive-notes\";s:1:\"c\";s:3:\"web\";}i:144;a:3:{s:1:\"a\";i:145;s:1:\"b\";s:23:\"edit good-receive-notes\";s:1:\"c\";s:3:\"web\";}i:145;a:3:{s:1:\"a\";i:146;s:1:\"b\";s:25:\"delete good-receive-notes\";s:1:\"c\";s:3:\"web\";}i:146;a:3:{s:1:\"a\";i:147;s:1:\"b\";s:16:\"expiring batches\";s:1:\"c\";s:3:\"web\";}i:147;a:3:{s:1:\"a\";i:148;s:1:\"b\";s:13:\"view stock-in\";s:1:\"c\";s:3:\"web\";}i:148;a:3:{s:1:\"a\";i:149;s:1:\"b\";s:15:\"create stock-in\";s:1:\"c\";s:3:\"web\";}i:149;a:3:{s:1:\"a\";i:150;s:1:\"b\";s:17:\"view vendor-codes\";s:1:\"c\";s:3:\"web\";}i:150;a:3:{s:1:\"a\";i:151;s:1:\"b\";s:19:\"create vendor-codes\";s:1:\"c\";s:3:\"web\";}i:151;a:3:{s:1:\"a\";i:152;s:1:\"b\";s:17:\"edit vendor-codes\";s:1:\"c\";s:3:\"web\";}i:152;a:3:{s:1:\"a\";i:153;s:1:\"b\";s:19:\"delete vendor-codes\";s:1:\"c\";s:3:\"web\";}i:153;a:3:{s:1:\"a\";i:154;s:1:\"b\";s:22:\"view roles-permissions\";s:1:\"c\";s:3:\"web\";}i:154;a:3:{s:1:\"a\";i:155;s:1:\"b\";s:19:\"terminate employees\";s:1:\"c\";s:3:\"web\";}i:155;a:3:{s:1:\"a\";i:156;s:1:\"b\";s:20:\"reactivate employees\";s:1:\"c\";s:3:\"web\";}i:156;a:3:{s:1:\"a\";i:157;s:1:\"b\";s:18:\"my-payroll payroll\";s:1:\"c\";s:3:\"web\";}i:157;a:3:{s:1:\"a\";i:158;s:1:\"b\";s:14:\"create payroll\";s:1:\"c\";s:3:\"web\";}i:158;a:3:{s:1:\"a\";i:159;s:1:\"b\";s:14:\"delete payroll\";s:1:\"c\";s:3:\"web\";}i:159;a:3:{s:1:\"a\";i:160;s:1:\"b\";s:21:\"edit payroll settings\";s:1:\"c\";s:3:\"web\";}i:160;a:3:{s:1:\"a\";i:161;s:1:\"b\";s:17:\"mark-paid payroll\";s:1:\"c\";s:3:\"web\";}i:161;a:3:{s:1:\"a\";i:162;s:1:\"b\";s:15:\"reports payroll\";s:1:\"c\";s:3:\"web\";}i:162;a:3:{s:1:\"a\";i:163;s:1:\"b\";s:14:\"export payroll\";s:1:\"c\";s:3:\"web\";}i:163;a:3:{s:1:\"a\";i:164;s:1:\"b\";s:21:\"view supplier-credits\";s:1:\"c\";s:3:\"web\";}i:164;a:3:{s:1:\"a\";i:165;s:1:\"b\";s:22:\"view supplier-payments\";s:1:\"c\";s:3:\"web\";}i:165;a:3:{s:1:\"a\";i:166;s:1:\"b\";s:24:\"create supplier-payments\";s:1:\"c\";s:3:\"web\";}i:166;a:3:{s:1:\"a\";i:167;s:1:\"b\";s:13:\"view accounts\";s:1:\"c\";s:3:\"web\";}i:167;a:3:{s:1:\"a\";i:168;s:1:\"b\";s:20:\"view journal-entries\";s:1:\"c\";s:3:\"web\";}i:168;a:3:{s:1:\"a\";i:169;s:1:\"b\";s:22:\"create journal-entries\";s:1:\"c\";s:3:\"web\";}i:169;a:3:{s:1:\"a\";i:170;s:1:\"b\";s:20:\"post journal-entries\";s:1:\"c\";s:3:\"web\";}i:170;a:3:{s:1:\"a\";i:171;s:1:\"b\";s:20:\"void journal-entries\";s:1:\"c\";s:3:\"web\";}i:171;a:3:{s:1:\"a\";i:172;s:1:\"b\";s:24:\"income-statement reports\";s:1:\"c\";s:3:\"web\";}i:172;a:3:{s:1:\"a\";i:173;s:1:\"b\";s:21:\"balance-sheet reports\";s:1:\"c\";s:3:\"web\";}i:173;a:3:{s:1:\"a\";i:174;s:1:\"b\";s:21:\"trial-balance reports\";s:1:\"c\";s:3:\"web\";}i:174;a:3:{s:1:\"a\";i:175;s:1:\"b\";s:22:\"general-ledger reports\";s:1:\"c\";s:3:\"web\";}i:175;a:3:{s:1:\"a\";i:176;s:1:\"b\";s:13:\"local storage\";s:1:\"c\";s:3:\"web\";}i:176;a:4:{s:1:\"a\";i:177;s:1:\"b\";s:14:\"view dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:177;a:3:{s:1:\"a\";i:178;s:1:\"b\";s:30:\"top-selling-products dashboard\";s:1:\"c\";s:3:\"web\";}i:178;a:3:{s:1:\"a\";i:179;s:1:\"b\";s:21:\"profit-data dashboard\";s:1:\"c\";s:3:\"web\";}i:179;a:3:{s:1:\"a\";i:180;s:1:\"b\";s:21:\"clear-cache dashboard\";s:1:\"c\";s:3:\"web\";}i:180;a:4:{s:1:\"a\";i:181;s:1:\"b\";s:14:\"view customers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:181;a:4:{s:1:\"a\";i:182;s:1:\"b\";s:16:\"create customers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:182;a:4:{s:1:\"a\";i:183;s:1:\"b\";s:14:\"edit customers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:183;a:4:{s:1:\"a\";i:184;s:1:\"b\";s:16:\"delete customers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:184;a:3:{s:1:\"a\";i:185;s:1:\"b\";s:25:\"process-payment customers\";s:1:\"c\";s:3:\"web\";}i:185;a:4:{s:1:\"a\";i:186;s:1:\"b\";s:21:\"view customer credits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:186;a:4:{s:1:\"a\";i:187;s:1:\"b\";s:23:\"create customer credits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:187;a:4:{s:1:\"a\";i:188;s:1:\"b\";s:21:\"edit customer credits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:188;a:4:{s:1:\"a\";i:189;s:1:\"b\";s:23:\"delete customer credits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:189;a:4:{s:1:\"a\";i:190;s:1:\"b\";s:22:\"view customer payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:190;a:4:{s:1:\"a\";i:191;s:1:\"b\";s:24:\"create customer payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:191;a:4:{s:1:\"a\";i:192;s:1:\"b\";s:22:\"edit customer payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:192;a:4:{s:1:\"a\";i:193;s:1:\"b\";s:24:\"delete customer payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:6:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"Manager\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:7:\"Cashier\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"Stock Clerk\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:10:\"Accountant\";s:1:\"c\";s:3:\"web\";}}}', 1765019449),
('pos_cache_super admin|112.134.168.116', 'i:3;', 1764929325),
('pos_cache_super admin|112.134.168.116:timer', 'i:1764929325;', 1764929325),
('pos_cache_super admin|112.134.168.132', 'i:3;', 1764217074),
('pos_cache_super admin|112.134.168.132:timer', 'i:1764217074;', 1764217074),
('pos_cache_super admin|116.206.246.59', 'i:2;', 1764352026),
('pos_cache_super admin|116.206.246.59:timer', 'i:1764352026;', 1764352026),
('pos_cache_super admin|175.157.139.212', 'i:2;', 1764945328),
('pos_cache_super admin|175.157.139.212:timer', 'i:1764945328;', 1764945328),
('pos_cache_super admin|175.157.41.160', 'i:3;', 1764179305),
('pos_cache_super admin|175.157.41.160:timer', 'i:1764179305;', 1764179305),
('pos_cache_super admin|175.157.62.76', 'i:1;', 1764933005),
('pos_cache_super admin|175.157.62.76:timer', 'i:1764933005;', 1764933005),
('pos_cache_superadmin|112.134.142.248', 'i:1;', 1764939880),
('pos_cache_superadmin|112.134.142.248:timer', 'i:1764939880;', 1764939880),
('pos_cache_superadmin|112.134.168.116', 'i:1;', 1764929314),
('pos_cache_superadmin|112.134.168.116:timer', 'i:1764929314;', 1764929314),
('pos_cache_wjph|175.157.43.145', 'i:5;', 1764696721),
('pos_cache_wjph|175.157.43.145:timer', 'i:1764696721;', 1764696721),
('pos_cache_ywue|175.157.43.145', 'i:5;', 1764697039),
('pos_cache_ywue|175.157.43.145:timer', 'i:1764697039;', 1764697039);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `cat_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `cat_name`, `description`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Beverages', 'Cola , Soda , Yogurt Drink', 'beverages.png', '2025-12-05 16:44:32', '2025-12-05 16:51:14'),
(2, 'Dairy', 'Cheese , Fresh Milk , Milk Powder ...', 'dairy.png', '2025-12-05 16:44:53', '2025-12-05 16:51:31'),
(3, 'Snacks', 'Chips , Peanuts , Mixture Bites', 'snacks.png', '2025-12-05 16:45:09', '2025-12-05 16:50:53'),
(4, 'Grains', 'Rice , Dhal , Chick Pea ...', 'grains.png', '2025-12-05 16:45:38', '2025-12-05 16:45:38'),
(5, 'Canned Goods', 'Macorol , Tuna ....', 'canned.png', '2025-12-05 16:46:01', '2025-12-05 16:46:01'),
(6, 'Grocery Items', 'Spices , Jam , Sauces', 'household.png', '2025-12-05 16:46:40', '2025-12-05 16:50:32'),
(7, 'Sweet Products', 'Chocalates , Toffe , Chewing Gum ...', 'sweets.png', '2025-12-05 16:47:10', '2025-12-05 16:47:24'),
(8, 'Frozen Food', 'Meat , Sauceges , Procceded food', 'frozen.png', '2025-12-05 16:52:12', '2025-12-05 16:52:12'),
(9, 'Cleaning Products', NULL, 'cleaning.png', '2025-12-05 16:52:56', '2025-12-05 16:52:56'),
(10, 'Personal Care', 'Tooth Brush , Toothpaste ....', 'personal-care.png', '2025-12-05 16:53:33', '2025-12-05 16:53:33'),
(11, 'Health & Medicine', 'Balms , Pas Panguwa ...', 'medicine.png', '2025-12-05 16:54:18', '2025-12-05 16:54:18');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `tax_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_credit_used` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_credits`
--

CREATE TABLE `customer_credits` (
  `id` bigint UNSIGNED NOT NULL,
  `credit_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `credit_terms` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit_days` int NOT NULL,
  `original_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `outstanding_amount` decimal(15,2) NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `customer_credit_id` bigint UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `processed_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `employee_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hire_date` date NOT NULL,
  `termination_date` date DEFAULT NULL,
  `employment_type` enum('hourly','salaried') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hourly',
  `hourly_rate` decimal(15,2) DEFAULT NULL,
  `base_salary` decimal(15,2) DEFAULT NULL,
  `pay_frequency` enum('weekly','biweekly','monthly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `department` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `epf_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','terminated','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `expense_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_category_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `amount` decimal(15,2) NOT NULL,
  `expense_date` date NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_by` bigint UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `category_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fiscal_periods`
--

CREATE TABLE `fiscal_periods` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int NOT NULL,
  `month` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `closed_by` bigint UNSIGNED DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fiscal_periods`
--

INSERT INTO `fiscal_periods` (`id`, `name`, `year`, `month`, `start_date`, `end_date`, `status`, `closed_by`, `closed_at`, `created_at`, `updated_at`) VALUES
(1, 'January 2025', 2025, 1, '2025-01-01', '2025-01-31', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(2, 'February 2025', 2025, 2, '2025-02-01', '2025-02-28', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(3, 'March 2025', 2025, 3, '2025-03-01', '2025-03-31', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(4, 'April 2025', 2025, 4, '2025-04-01', '2025-04-30', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(5, 'May 2025', 2025, 5, '2025-05-01', '2025-05-31', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(6, 'June 2025', 2025, 6, '2025-06-01', '2025-06-30', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(7, 'July 2025', 2025, 7, '2025-07-01', '2025-07-31', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(8, 'August 2025', 2025, 8, '2025-08-01', '2025-08-31', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(9, 'September 2025', 2025, 9, '2025-09-01', '2025-09-30', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(10, 'October 2025', 2025, 10, '2025-10-01', '2025-10-31', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(11, 'November 2025', 2025, 11, '2025-11-01', '2025-11-30', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(12, 'December 2025', 2025, 12, '2025-12-01', '2025-12-31', 'open', NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `good_receive_notes`
--

CREATE TABLE `good_receive_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `grn_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `received_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `payment_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `is_credit` tinyint(1) NOT NULL DEFAULT '0',
  `supplier_credit_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(15,2) NOT NULL DEFAULT '0.00',
  `shipping` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint UNSIGNED NOT NULL,
  `entry_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entry_date` date NOT NULL,
  `fiscal_year` int NOT NULL,
  `fiscal_period` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('draft','posted','void') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED NOT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `voided_by` bigint UNSIGNED DEFAULT NULL,
  `voided_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `entry_number`, `entry_date`, `fiscal_year`, `fiscal_period`, `description`, `reference_type`, `reference_id`, `status`, `created_by`, `approved_by`, `posted_at`, `voided_by`, `voided_at`, `created_at`, `updated_at`) VALUES
(1, 'JE-2025-11-001', '2025-11-27', 2025, 11, 'Goods receipt GRN-000001', 'App\\Models\\GoodReceiveNote', 1, 'posted', 2, 2, '2025-11-27 04:30:35', NULL, NULL, '2025-11-27 04:30:35', '2025-11-27 04:30:35'),
(3, 'JE-2025-11-002', '2025-11-27', 2025, 11, 'Goods receipt GRN-000002', 'App\\Models\\GoodReceiveNote', 3, 'posted', 2, 2, '2025-11-27 05:04:22', NULL, NULL, '2025-11-27 05:04:22', '2025-11-27 05:04:22'),
(4, 'JE-2025-12-001', '2025-12-05', 2025, 12, 'Sale transaction SALE-0001', 'App\\Models\\Sale', 1, 'posted', 1, 1, '2025-12-05 10:16:58', NULL, NULL, '2025-12-05 10:16:58', '2025-12-05 10:16:58'),
(5, 'JE-2025-12-002', '2025-12-05', 2025, 12, 'Sale transaction SALE-0002', 'App\\Models\\Sale', 2, 'posted', 2, 2, '2025-12-05 10:23:52', NULL, NULL, '2025-12-05 10:23:52', '2025-12-05 10:23:52'),
(6, 'JE-2025-12-003', '2025-12-05', 2025, 12, 'Goods receipt GRN-000003', 'App\\Models\\GoodReceiveNote', 4, 'posted', 1, 1, '2025-12-05 10:32:53', NULL, NULL, '2025-12-05 10:32:53', '2025-12-05 10:32:53'),
(7, 'JE-2025-12-004', '2025-12-05', 2025, 12, 'Sale transaction SALE-0003', 'App\\Models\\Sale', 3, 'posted', 2, 2, '2025-12-05 10:33:25', NULL, NULL, '2025-12-05 10:33:25', '2025-12-05 10:33:25'),
(8, 'JE-2025-12-005', '2025-12-05', 2025, 12, 'Goods receipt GRN-000004', 'App\\Models\\GoodReceiveNote', 5, 'posted', 2, 2, '2025-12-05 10:42:47', NULL, NULL, '2025-12-05 10:42:47', '2025-12-05 10:42:47'),
(9, 'JE-2025-12-006', '2025-12-05', 2025, 12, 'Sale transaction SALE-0004', 'App\\Models\\Sale', 4, 'posted', 2, 2, '2025-12-05 10:43:14', NULL, NULL, '2025-12-05 10:43:14', '2025-12-05 10:43:14'),
(10, 'JE-2025-12-007', '2025-12-05', 2025, 12, 'Sale transaction SALE-0005', 'App\\Models\\Sale', 5, 'posted', 2, 2, '2025-12-05 11:28:07', NULL, NULL, '2025-12-05 11:28:07', '2025-12-05 11:28:07'),
(20, 'JE-2025-12-008', '2025-12-05', 2025, 12, 'Sale transaction SALE-0006', 'App\\Models\\Sale', 6, 'posted', 2, 2, '2025-12-05 12:05:23', NULL, NULL, '2025-12-05 12:05:23', '2025-12-05 12:05:23'),
(22, 'JE-2025-12-009', '2025-12-05', 2025, 12, 'Goods receipt GRN-000005', 'App\\Models\\GoodReceiveNote', 16, 'posted', 2, 2, '2025-12-05 12:52:37', NULL, NULL, '2025-12-05 12:52:37', '2025-12-05 12:52:37'),
(25, 'JE-2025-12-010', '2025-12-05', 2025, 12, 'Sale transaction SALE-0007', 'App\\Models\\Sale', 7, 'posted', 2, 2, '2025-12-05 12:55:14', NULL, NULL, '2025-12-05 12:55:14', '2025-12-05 12:55:14'),
(27, 'JE-2025-12-011', '2025-12-05', 2025, 12, 'Goods receipt GRN-000006', 'App\\Models\\GoodReceiveNote', 20, 'posted', 2, 2, '2025-12-05 15:40:59', NULL, NULL, '2025-12-05 15:40:59', '2025-12-05 15:40:59'),
(28, 'JE-2025-12-012', '2025-12-05', 2025, 12, 'Goods receipt GRN-000007', 'App\\Models\\GoodReceiveNote', 21, 'posted', 2, 2, '2025-12-05 15:42:25', NULL, NULL, '2025-12-05 15:42:25', '2025-12-05 15:42:25'),
(29, 'JE-2025-12-013', '2025-12-05', 2025, 12, 'Goods receipt GRN-000008', 'App\\Models\\GoodReceiveNote', 22, 'posted', 2, 2, '2025-12-05 16:12:44', NULL, NULL, '2025-12-05 16:12:44', '2025-12-05 16:12:44'),
(30, 'JE-2025-12-014', '2025-12-05', 2025, 12, 'Sale transaction SALE-0008', 'App\\Models\\Sale', 8, 'posted', 2, 2, '2025-12-05 16:17:42', NULL, NULL, '2025-12-05 16:17:42', '2025-12-05 16:17:42'),
(31, 'JE-2025-12-015', '2025-12-05', 2025, 12, 'Sale transaction SALE-0009', 'App\\Models\\Sale', 9, 'posted', 2, 2, '2025-12-05 16:20:47', NULL, NULL, '2025-12-05 16:20:47', '2025-12-05 16:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry_lines`
--

CREATE TABLE `journal_entry_lines` (
  `id` bigint UNSIGNED NOT NULL,
  `journal_entry_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `debit_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `line_number` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_entry_lines`
--

INSERT INTO `journal_entry_lines` (`id`, `journal_entry_id`, `account_id`, `debit_amount`, `credit_amount`, `description`, `line_number`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 0.00, 0.00, 'Inventory purchase from GRN GRN-000001', 1, '2025-11-27 04:30:35', '2025-11-27 04:30:35'),
(2, 1, 3, 0.00, 0.00, 'Cash payment for GRN GRN-000001', 2, '2025-11-27 04:30:35', '2025-11-27 04:30:35'),
(5, 3, 6, 0.00, 0.00, 'Inventory purchase from GRN GRN-000002', 1, '2025-11-27 05:04:22', '2025-11-27 05:04:22'),
(6, 3, 3, 0.00, 0.00, 'Cash payment for GRN GRN-000002', 2, '2025-11-27 05:04:22', '2025-11-27 05:04:22'),
(7, 4, 3, 1146.47, 0.00, 'Payment received from sale SALE-0001 via Cash', 1, '2025-12-05 10:16:58', '2025-12-05 10:16:58'),
(8, 4, 17, 0.00, 1146.47, 'Sales revenue from SALE-0001', 2, '2025-12-05 10:16:58', '2025-12-05 10:16:58'),
(9, 4, 20, 1230.00, 0.00, 'Cost of goods sold for SALE-0001', 3, '2025-12-05 10:16:58', '2025-12-05 10:16:58'),
(10, 4, 6, 0.00, 1230.00, 'Inventory reduction for SALE-0001', 4, '2025-12-05 10:16:58', '2025-12-05 10:16:58'),
(11, 5, 3, 334.00, 0.00, 'Payment received from sale SALE-0002 via Cash', 1, '2025-12-05 10:23:52', '2025-12-05 10:23:52'),
(12, 5, 17, 0.00, 334.00, 'Sales revenue from SALE-0002', 2, '2025-12-05 10:23:52', '2025-12-05 10:23:52'),
(13, 5, 20, 1123.00, 0.00, 'Cost of goods sold for SALE-0002', 3, '2025-12-05 10:23:52', '2025-12-05 10:23:52'),
(14, 5, 6, 0.00, 1123.00, 'Inventory reduction for SALE-0002', 4, '2025-12-05 10:23:52', '2025-12-05 10:23:52'),
(15, 6, 6, 0.00, 0.00, 'Inventory purchase from GRN GRN-000003', 1, '2025-12-05 10:32:53', '2025-12-05 10:32:53'),
(16, 6, 3, 0.00, 0.00, 'Cash payment for GRN GRN-000003', 2, '2025-12-05 10:32:53', '2025-12-05 10:32:53'),
(17, 7, 3, 300.00, 0.00, 'Payment received from sale SALE-0003 via Cash', 1, '2025-12-05 10:33:25', '2025-12-05 10:33:25'),
(18, 7, 17, 0.00, 300.00, 'Sales revenue from SALE-0003', 2, '2025-12-05 10:33:25', '2025-12-05 10:33:25'),
(19, 7, 20, 250.00, 0.00, 'Cost of goods sold for SALE-0003', 3, '2025-12-05 10:33:25', '2025-12-05 10:33:25'),
(20, 7, 6, 0.00, 250.00, 'Inventory reduction for SALE-0003', 4, '2025-12-05 10:33:25', '2025-12-05 10:33:25'),
(21, 8, 6, 0.00, 0.00, 'Inventory purchase from GRN GRN-000004', 1, '2025-12-05 10:42:47', '2025-12-05 10:42:47'),
(22, 8, 3, 0.00, 0.00, 'Cash payment for GRN GRN-000004', 2, '2025-12-05 10:42:47', '2025-12-05 10:42:47'),
(23, 9, 3, 2204.40, 0.00, 'Payment received from sale SALE-0004 via Cash', 1, '2025-12-05 10:43:14', '2025-12-05 10:43:14'),
(24, 9, 17, 0.00, 2204.40, 'Sales revenue from SALE-0004', 2, '2025-12-05 10:43:14', '2025-12-05 10:43:14'),
(25, 9, 20, 1234.00, 0.00, 'Cost of goods sold for SALE-0004', 3, '2025-12-05 10:43:14', '2025-12-05 10:43:14'),
(26, 9, 6, 0.00, 1234.00, 'Inventory reduction for SALE-0004', 4, '2025-12-05 10:43:14', '2025-12-05 10:43:14'),
(27, 10, 3, 300.00, 0.00, 'Payment received from sale SALE-0005 via Cash', 1, '2025-12-05 11:28:07', '2025-12-05 11:28:07'),
(28, 10, 17, 0.00, 300.00, 'Sales revenue from SALE-0005', 2, '2025-12-05 11:28:07', '2025-12-05 11:28:07'),
(29, 10, 20, 250.00, 0.00, 'Cost of goods sold for SALE-0005', 3, '2025-12-05 11:28:07', '2025-12-05 11:28:07'),
(30, 10, 6, 0.00, 250.00, 'Inventory reduction for SALE-0005', 4, '2025-12-05 11:28:07', '2025-12-05 11:28:07'),
(49, 20, 3, 600.00, 0.00, 'Payment received from sale SALE-0006 via Cash', 1, '2025-12-05 12:05:23', '2025-12-05 12:05:23'),
(50, 20, 17, 0.00, 600.00, 'Sales revenue from SALE-0006', 2, '2025-12-05 12:05:23', '2025-12-05 12:05:23'),
(51, 20, 20, 500.00, 0.00, 'Cost of goods sold for SALE-0006', 3, '2025-12-05 12:05:23', '2025-12-05 12:05:23'),
(52, 20, 6, 0.00, 500.00, 'Inventory reduction for SALE-0006', 4, '2025-12-05 12:05:23', '2025-12-05 12:05:23'),
(55, 22, 6, 0.00, 0.00, 'Inventory purchase from GRN GRN-000005', 1, '2025-12-05 12:52:37', '2025-12-05 12:52:37'),
(56, 22, 3, 0.00, 0.00, 'Cash payment for GRN GRN-000005', 2, '2025-12-05 12:52:37', '2025-12-05 12:52:37'),
(61, 25, 3, 220.00, 0.00, 'Payment received from sale SALE-0007 via Cash', 1, '2025-12-05 12:55:14', '2025-12-05 12:55:14'),
(62, 25, 17, 0.00, 220.00, 'Sales revenue from SALE-0007', 2, '2025-12-05 12:55:14', '2025-12-05 12:55:14'),
(63, 25, 20, 200.00, 0.00, 'Cost of goods sold for SALE-0007', 3, '2025-12-05 12:55:14', '2025-12-05 12:55:14'),
(64, 25, 6, 0.00, 200.00, 'Inventory reduction for SALE-0007', 4, '2025-12-05 12:55:14', '2025-12-05 12:55:14'),
(67, 27, 6, 0.00, 0.00, 'Inventory purchase from GRN GRN-000006', 1, '2025-12-05 15:40:59', '2025-12-05 15:40:59'),
(68, 27, 3, 0.00, 0.00, 'Cash payment for GRN GRN-000006', 2, '2025-12-05 15:40:59', '2025-12-05 15:40:59'),
(69, 28, 6, 0.00, 0.00, 'Inventory purchase from GRN GRN-000007', 1, '2025-12-05 15:42:25', '2025-12-05 15:42:25'),
(70, 28, 3, 0.00, 0.00, 'Cash payment for GRN GRN-000007', 2, '2025-12-05 15:42:25', '2025-12-05 15:42:25'),
(71, 29, 6, 0.00, 0.00, 'Inventory purchase from GRN GRN-000008', 1, '2025-12-05 16:12:44', '2025-12-05 16:12:44'),
(72, 29, 3, 0.00, 0.00, 'Cash payment for GRN GRN-000008', 2, '2025-12-05 16:12:44', '2025-12-05 16:12:44'),
(73, 30, 3, 1300.00, 0.00, 'Payment received from sale SALE-0008 via Cash', 1, '2025-12-05 16:17:42', '2025-12-05 16:17:42'),
(74, 30, 17, 0.00, 1300.00, 'Sales revenue from SALE-0008', 2, '2025-12-05 16:17:42', '2025-12-05 16:17:42'),
(75, 30, 20, 1230.00, 0.00, 'Cost of goods sold for SALE-0008', 3, '2025-12-05 16:17:42', '2025-12-05 16:17:42'),
(76, 30, 6, 0.00, 1230.00, 'Inventory reduction for SALE-0008', 4, '2025-12-05 16:17:42', '2025-12-05 16:17:42'),
(77, 31, 3, 1300.00, 0.00, 'Payment received from sale SALE-0009 via Cash', 1, '2025-12-05 16:20:47', '2025-12-05 16:20:47'),
(78, 31, 17, 0.00, 1300.00, 'Sales revenue from SALE-0009', 2, '2025-12-05 16:20:47', '2025-12-05 16:20:47'),
(79, 31, 20, 1230.00, 0.00, 'Cost of goods sold for SALE-0009', 3, '2025-12-05 16:20:47', '2025-12-05 16:20:47'),
(80, 31, 6, 0.00, 1230.00, 'Inventory reduction for SALE-0009', 4, '2025-12-05 16:20:47', '2025-12-05 16:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_24_041324_create_categories_table', 1),
(5, '2025_06_24_041332_create_brands_table', 1),
(6, '2025_06_24_041337_create_products_table', 1),
(7, '2025_07_10_092813_create_suppliers_table', 1),
(8, '2025_07_10_093810_create_good_receive_notes_table', 1),
(9, '2025_11_05_105229_create_batches_table', 1),
(10, '2025_11_05_105236_create_stocks_table', 1),
(11, '2025_11_08_101845_create_product_supplier_table', 1),
(12, '2025_11_08_120000_create_supplier_returns_table', 1),
(13, '2025_11_08_120001_create_supplier_return_items_table', 1),
(14, '2025_11_08_130000_create_sales_tables', 1),
(15, '2025_11_08_130001_create_sales_returns_table', 1),
(16, '2025_11_08_130002_create_sales_return_items_table', 1),
(17, '2025_11_10_154834_create_saved_carts_table', 1),
(18, '2025_11_10_154843_create_saved_cart_items_table', 1),
(19, '2025_11_12_154344_create_permission_tables', 1),
(20, '2025_11_13_103626_create_shifts_table', 1),
(21, '2025_11_13_153636_create_employees_table', 1),
(22, '2025_11_13_155048_create_payroll_periods_table', 1),
(23, '2025_11_13_155049_create_payroll_entries_table', 1),
(24, '2025_11_13_155141_create_payroll_entry_shift_table', 1),
(25, '2025_11_13_172737_create_supplier_credits_table', 1),
(26, '2025_11_13_172746_create_supplier_payments_table', 1),
(27, '2025_11_13_172757_create_payment_reminders_table', 1),
(28, '2025_11_14_090332_create_expense_categories_table', 1),
(29, '2025_11_14_090343_create_expenses_table', 1),
(30, '2025_11_14_102748_create_account_types_table', 1),
(31, '2025_11_14_102752_create_accounts_table', 1),
(32, '2025_11_14_102754_create_journal_entries_table', 1),
(33, '2025_11_14_102757_create_journal_entry_lines_table', 1),
(34, '2025_11_14_102759_create_fiscal_periods_table', 1),
(35, '2025_11_14_102801_create_account_balances_table', 1),
(36, '2025_11_16_143338_create_payroll_settings_table', 1),
(37, '2025_11_26_084504_create_customers_table', 2),
(38, '2025_11_26_084640_create_customer_credits_table', 2),
(39, '2025_11_26_084804_create_customer_payments_table', 2),
(40, '2025_11_26_084914_add_customer_id_to_sales_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_reminders`
--

CREATE TABLE `payment_reminders` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_credit_id` bigint UNSIGNED NOT NULL,
  `reminder_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `days_before_due` int DEFAULT NULL,
  `days_overdue` int DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_entries`
--

CREATE TABLE `payroll_entries` (
  `id` bigint UNSIGNED NOT NULL,
  `payroll_period_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `regular_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  `overtime_hours_2x` decimal(10,2) NOT NULL DEFAULT '0.00',
  `base_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `overtime_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `overtime_amount_2x` decimal(15,2) NOT NULL DEFAULT '0.00',
  `gross_pay` decimal(15,2) NOT NULL DEFAULT '0.00',
  `epf_employee` decimal(15,2) NOT NULL DEFAULT '0.00',
  `epf_employer` decimal(15,2) NOT NULL DEFAULT '0.00',
  `etf_employer` decimal(15,2) NOT NULL DEFAULT '0.00',
  `other_deductions` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_pay` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','approved','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_entry_shift`
--

CREATE TABLE `payroll_entry_shift` (
  `payroll_entry_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_periods`
--

CREATE TABLE `payroll_periods` (
  `id` bigint UNSIGNED NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `status` enum('draft','processing','approved','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_settings`
--

CREATE TABLE `payroll_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `ot_weekday_multiplier` decimal(5,2) NOT NULL DEFAULT '1.50',
  `ot_weekend_multiplier` decimal(5,2) NOT NULL DEFAULT '2.00',
  `daily_hours_threshold` decimal(5,2) NOT NULL DEFAULT '8.00',
  `ot_calculation_mode` enum('multiplier','fixed_rate') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'multiplier',
  `ot_weekday_fixed_rate` decimal(10,2) DEFAULT NULL,
  `ot_weekend_fixed_rate` decimal(10,2) DEFAULT NULL,
  `epf_employee_percentage` decimal(5,2) NOT NULL DEFAULT '8.00',
  `epf_employer_percentage` decimal(5,2) NOT NULL DEFAULT '12.00',
  `etf_employer_percentage` decimal(5,2) NOT NULL DEFAULT '3.00',
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_settings`
--

INSERT INTO `payroll_settings` (`id`, `ot_weekday_multiplier`, `ot_weekend_multiplier`, `daily_hours_threshold`, `ot_calculation_mode`, `ot_weekday_fixed_rate`, `ot_weekend_fixed_rate`, `epf_employee_percentage`, `epf_employer_percentage`, `etf_employer_percentage`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1.50, 2.00, 8.00, 'multiplier', NULL, NULL, 8.00, 12.00, 3.00, NULL, '2025-11-26 15:43:30', '2025-11-26 15:43:30');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view products', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(2, 'create products', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(3, 'edit products', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(4, 'delete products', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(5, 'view categories', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(6, 'create categories', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(7, 'edit categories', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(8, 'delete categories', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(9, 'view brands', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(10, 'create brands', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(11, 'edit brands', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(12, 'delete brands', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(13, 'view vendor codes', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(14, 'create vendor codes', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(15, 'edit vendor codes', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(16, 'delete vendor codes', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(17, 'view suppliers', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(18, 'create suppliers', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(19, 'edit suppliers', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(20, 'delete suppliers', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(21, 'view grns', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(22, 'create grns', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(23, 'edit grns', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(24, 'delete grns', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(25, 'view batches', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(26, 'view expiring batches', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(27, 'view stocks', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(28, 'manage stock in', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(29, 'create sales', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(30, 'view sales', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(31, 'view sale details', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(32, 'view sales returns', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(33, 'create sales returns', 'web', '2025-11-25 16:39:53', '2025-11-25 16:39:53'),
(34, 'edit sales returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(35, 'delete sales returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(36, 'refund sales returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(37, 'cancel sales returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(38, 'view supplier returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(39, 'create supplier returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(40, 'edit supplier returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(41, 'delete supplier returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(42, 'approve supplier returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(43, 'complete supplier returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(44, 'cancel supplier returns', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(45, 'view reports', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(46, 'view expenses', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(47, 'manage expenses', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(48, 'view users', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(49, 'create users', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(50, 'edit users', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(51, 'delete users', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(52, 'assign roles', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(53, 'view roles', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(54, 'create roles', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(55, 'edit roles', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(56, 'delete roles', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(57, 'view permissions', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(58, 'manage permissions', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(59, 'manage saved carts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(60, 'manage own shifts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(61, 'view shifts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(62, 'manage shifts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(63, 'approve shifts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(64, 'view employees', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(65, 'create employees', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(66, 'edit employees', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(67, 'delete employees', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(68, 'view payroll', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(69, 'process payroll', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(70, 'approve payroll', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(71, 'view own payroll', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(72, 'view payroll reports', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(73, 'view supplier credits', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(74, 'create supplier credits', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(75, 'edit supplier credits', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(76, 'delete supplier credits', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(77, 'view supplier payments', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(78, 'create supplier payments', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(79, 'edit supplier payments', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(80, 'delete supplier payments', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(81, 'view creditor reports', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(82, 'view creditor aging', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(83, 'view supplier statements', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(84, 'manage payment reminders', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(85, 'view chart of accounts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(86, 'create accounts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(87, 'edit accounts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(88, 'delete accounts', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(89, 'view journal entries', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(90, 'create journal entries', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(91, 'post journal entries', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(92, 'void journal entries', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(93, 'view income statement', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(94, 'view balance sheet', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(95, 'view trial balance', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(96, 'view general ledger', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(97, 'view fiscal periods', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(98, 'manage fiscal periods', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(99, 'close fiscal periods', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(100, 'login', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(101, 'create login', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(102, 'logout', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(103, 'request password', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(104, 'reset password', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(105, 'email password', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(106, 'edit password', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(107, 'edit user-profile-information', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(108, 'edit user-password', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(109, 'confirm password', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(110, 'confirmation password', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(111, 'create password confirm', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(112, 'dashboard cashier', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(113, 'clock-in shifts', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(114, 'clock-out shifts', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(115, 'current shifts', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(116, 'my-shifts shifts', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(117, 'create expenses', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(118, 'create expense-categories', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(119, 'view api expense-categories', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(120, 'edit expenses', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(121, 'delete expenses', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(122, 'approve expenses', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(123, 'reject expenses', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(124, 'mark-as-paid expenses', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(125, 'view sales-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(126, 'create sales-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(127, 'refund sales-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(128, 'cancel sales-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(129, 'returnable-items sales', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(130, 'view supplier-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(131, 'create supplier-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(132, 'approve supplier-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(133, 'complete supplier-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(134, 'cancel supplier-returns', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(135, 'returnable-stock good-receive-notes', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(136, 'search api products', 'web', '2025-11-25 17:44:18', '2025-11-25 17:44:18'),
(137, 'stock api products', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(138, 'view api saved-carts', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(139, 'create api saved-carts', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(140, 'delete api saved-carts', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(141, 'products suppliers', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(142, 'credit-info suppliers', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(143, 'view good-receive-notes', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(144, 'create good-receive-notes', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(145, 'edit good-receive-notes', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(146, 'delete good-receive-notes', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(147, 'expiring batches', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(148, 'view stock-in', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(149, 'create stock-in', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(150, 'view vendor-codes', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(151, 'create vendor-codes', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(152, 'edit vendor-codes', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(153, 'delete vendor-codes', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(154, 'view roles-permissions', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(155, 'terminate employees', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(156, 'reactivate employees', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(157, 'my-payroll payroll', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(158, 'create payroll', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(159, 'delete payroll', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(160, 'edit payroll settings', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(161, 'mark-paid payroll', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(162, 'reports payroll', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(163, 'export payroll', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(164, 'view supplier-credits', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(165, 'view supplier-payments', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(166, 'create supplier-payments', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(167, 'view accounts', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(168, 'view journal-entries', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(169, 'create journal-entries', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(170, 'post journal-entries', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(171, 'void journal-entries', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(172, 'income-statement reports', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(173, 'balance-sheet reports', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(174, 'trial-balance reports', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(175, 'general-ledger reports', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(176, 'local storage', 'web', '2025-11-25 17:44:19', '2025-11-25 17:44:19'),
(177, 'view dashboard', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(178, 'top-selling-products dashboard', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(179, 'profit-data dashboard', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(180, 'clear-cache dashboard', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(181, 'view customers', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(182, 'create customers', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(183, 'edit customers', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(184, 'delete customers', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(185, 'process-payment customers', 'web', '2025-11-26 16:48:01', '2025-11-26 16:48:01'),
(186, 'view customer credits', 'web', '2025-11-26 16:48:50', '2025-11-26 16:48:50'),
(187, 'create customer credits', 'web', '2025-11-26 16:48:50', '2025-11-26 16:48:50'),
(188, 'edit customer credits', 'web', '2025-11-26 16:48:50', '2025-11-26 16:48:50'),
(189, 'delete customer credits', 'web', '2025-11-26 16:48:50', '2025-11-26 16:48:50'),
(190, 'view customer payments', 'web', '2025-11-26 16:48:50', '2025-11-26 16:48:50'),
(191, 'create customer payments', 'web', '2025-11-26 16:48:50', '2025-11-26 16:48:50'),
(192, 'edit customer payments', 'web', '2025-11-26 16:48:50', '2025-11-26 16:48:50'),
(193, 'delete customer payments', 'web', '2025-11-26 16:48:50', '2025-11-26 16:48:50');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `initial_stock` int DEFAULT NULL,
  `minimum_stock` int DEFAULT NULL,
  `maximum_stock` int DEFAULT NULL,
  `product_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `brand_id` bigint UNSIGNED NOT NULL,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `base_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `purchase_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conversion_factor` decimal(10,4) NOT NULL DEFAULT '1.0000',
  `allow_decimal_sales` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `sku`, `description`, `initial_stock`, `minimum_stock`, `maximum_stock`, `product_image`, `category_id`, `brand_id`, `unit`, `base_unit`, `purchase_unit`, `conversion_factor`, `allow_decimal_sales`, `created_at`, `updated_at`) VALUES
(1, 'EGB 250ML', 'SKU-000001', NULL, 12, 1, 12, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:07:33', '2025-12-05 17:25:39'),
(2, 'EGB 1L', 'SKU-000002', NULL, NULL, NULL, NULL, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:07:56', '2025-12-05 17:37:27'),
(3, 'NECTAR-MIXED FRUIT 200ML', 'SKU-000003', NULL, NULL, NULL, 15, NULL, 1, 2, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:13:09', '2025-12-05 17:43:58'),
(4, 'NECTAR-ALOE VERA 200ML', 'SKU-000004', NULL, 15, NULL, 15, NULL, 1, 2, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:13:46', '2025-12-05 17:44:23'),
(5, 'NECTAR-MIXED FRUIT 500ML', 'SKU-000005', NULL, 10, NULL, 10, NULL, 1, 2, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:15:47', '2025-12-05 17:45:38'),
(6, 'NECTAR-WOOD APPLE 500ML', 'SKU-000006', NULL, 6, NULL, 6, NULL, 1, 2, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:16:29', '2025-12-05 17:46:04'),
(7, 'NECTAR-ALOE VERA 500ML', 'SKU-000007', NULL, NULL, NULL, 10, NULL, 1, 2, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:24:27', '2025-12-05 17:46:15'),
(8, 'NECTAR-WOOD APPLE 200ML', 'SKU-000008', NULL, NULL, NULL, 12, NULL, 1, 2, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:25:01', '2025-12-05 17:44:39'),
(9, 'NECTAR-MIXED FRUIT 1L', 'SKU-000009', NULL, NULL, NULL, 4, NULL, 1, 2, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:25:52', '2025-12-05 17:46:30'),
(10, 'EGB 500ML', 'SKU-000010', NULL, NULL, 1, 12, NULL, 1, 1, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 17:26:21', '2025-12-05 17:26:21'),
(11, 'NECTAR-WOOD APPLE 1L', 'SKU-000011', NULL, NULL, NULL, 2, NULL, 1, 2, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:26:37', '2025-12-05 17:46:54'),
(12, 'EGB 1.5L', 'SKU-000012', NULL, NULL, NULL, NULL, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:27:09', '2025-12-05 17:37:38'),
(13, 'ORANGE CRUSH 250ML', 'SKU-000013', NULL, NULL, NULL, 12, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:27:39', '2025-12-05 17:29:48'),
(14, 'ORANGE CRUSH 500ML', 'SKU-000014', NULL, NULL, NULL, 8, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:28:11', '2025-12-05 17:30:00'),
(15, 'ORANGE CRUSH 1L', 'SKU-000015', NULL, NULL, NULL, 4, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:28:32', '2025-12-05 17:37:50'),
(16, 'ORANGE CRUSH 1.5L', 'SKU-000016', NULL, NULL, NULL, 4, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:28:51', '2025-12-05 17:37:20'),
(17, 'CREAM SODA 250ML', 'SKU-000017', NULL, NULL, NULL, 4, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:29:17', '2025-12-05 17:29:17'),
(18, 'CASAVA - HOT & SPICY 50G', 'SKU-000018', NULL, NULL, NULL, 6, NULL, 3, 2, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 17:29:54', '2025-12-05 17:47:22'),
(19, 'CASAVA - TOMATO 50G', 'SKU-000019', NULL, NULL, NULL, 6, NULL, 3, 2, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 17:30:23', '2025-12-05 17:48:10'),
(20, 'CASAVA - BBQ 50G', 'SKU-000020', NULL, NULL, NULL, 6, NULL, 3, 2, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 17:30:45', '2025-12-05 17:48:22'),
(21, 'CREAM SODA 500ML', 'SKU-000021', NULL, NULL, NULL, 8, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:34:02', '2025-12-05 17:34:02'),
(22, 'CREAM SODA 1L', 'SKU-000022', NULL, NULL, NULL, 4, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:34:26', '2025-12-05 17:36:55'),
(23, 'CREAM SODA 1.5L', 'SKU-000023', NULL, NULL, NULL, 5, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:35:02', '2025-12-05 17:36:46'),
(24, 'NECTO 250ML', 'SKU-000024', NULL, NULL, NULL, 6, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:35:35', '2025-12-05 17:35:35'),
(25, 'NECTO 500ML', 'SKU-000025', NULL, NULL, NULL, 22, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:36:03', '2025-12-05 17:36:03'),
(26, 'NECTO 1L', 'SKU-000026', NULL, NULL, NULL, 6, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:36:25', '2025-12-05 17:36:37'),
(27, 'NECTO 1.5L', 'SKU-000027', NULL, NULL, NULL, 4, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:38:32', '2025-12-05 17:38:32'),
(28, 'SODA 500ML', 'SKU-000028', NULL, NULL, NULL, 6, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:38:56', '2025-12-05 17:38:56'),
(29, 'SODA 1L', 'SKU-000029', NULL, NULL, NULL, 12, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:39:23', '2025-12-05 17:39:23'),
(30, 'WATER BOTTLE (elephant house) 500ML', 'SKU-000030', NULL, NULL, NULL, 24, NULL, 1, 1, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:41:16', '2025-12-05 17:41:16'),
(31, 'WATER BOTTLE (elephant house) 1L', 'SKU-000031', NULL, NULL, NULL, 12, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:41:41', '2025-12-05 17:41:41'),
(32, 'WATER BOTTLE (elephant house) 1.5L', 'SKU-000032', NULL, NULL, NULL, NULL, NULL, 1, 1, 'pcs', 'L', 'L', 1.0000, 0, '2025-12-05 17:41:59', '2025-12-05 17:41:59'),
(33, 'NECTAR-MANGO 200ML', 'SKU-000033', NULL, NULL, NULL, NULL, NULL, 1, 2, 'pcs', 'ml', 'ml', 1.0000, 0, '2025-12-05 17:45:22', '2025-12-05 17:45:22'),
(34, 'MIXTURE 40G', 'SKU-000034', NULL, NULL, NULL, 20, NULL, 3, 4, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 17:51:15', '2025-12-05 17:52:35'),
(35, 'MIXTURE 80G', 'SKU-000035', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 17:53:10', '2025-12-05 17:53:10'),
(36, 'CHILI POWDER-1KG', 'SKU-000036', NULL, NULL, NULL, NULL, NULL, 6, 3, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:23:36', '2025-12-05 18:23:36'),
(37, 'MIXTURE 40G', 'SKU-000037', NULL, NULL, NULL, 20, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:29:15', '2025-12-05 18:29:15'),
(38, 'MIXTURE 80G', 'SKU-000038', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:29:45', '2025-12-05 18:29:45'),
(39, 'MIXTURE 130G', 'SKU-000039', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:30:37', '2025-12-05 18:30:37'),
(40, 'DHAL 37G', 'SKU-000040', NULL, NULL, NULL, 20, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:31:19', '2025-12-05 18:31:19'),
(41, 'TASTE GRAM 35G', 'SKU-000041', NULL, NULL, NULL, 20, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:32:10', '2025-12-05 18:32:10'),
(42, 'KAHA KADALA 40G', 'SKU-000042', NULL, NULL, NULL, 20, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:33:44', '2025-12-05 18:33:44'),
(43, 'MANIOC CHIPS 37G', 'SKU-000043', NULL, NULL, NULL, 20, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:34:45', '2025-12-05 18:34:45'),
(44, 'GREEN PEAS 37G', 'SKU-000044', NULL, NULL, NULL, 20, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:35:43', '2025-12-05 18:35:43'),
(45, 'TASTE COWPEA 35G', 'SKU-000045', NULL, NULL, NULL, 20, NULL, 3, 4, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:36:32', '2025-12-05 18:36:32'),
(46, 'CHILI POWDER-500G', 'SKU-000046', NULL, NULL, NULL, NULL, NULL, 6, 3, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:42:35', '2025-12-05 18:42:35'),
(47, 'PEANUT 42G', 'SKU-000047', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 18:46:31', '2025-12-05 18:46:31'),
(48, 'POSHANAYA 65G', 'SKU-000048', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 18:47:14', '2025-12-05 18:47:14'),
(49, 'MANIOC CHIPS 80G', 'SKU-000049', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 18:47:53', '2025-12-05 18:47:53'),
(50, 'DHAL 75G', 'SKU-000050', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 18:48:45', '2025-12-05 18:48:45'),
(51, 'CHILI POWDER-50G', 'SKU-000051', NULL, NULL, NULL, NULL, NULL, 6, 3, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:48:48', '2025-12-05 18:48:48'),
(52, 'TASTE GRAM 65G', 'SKU-000052', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 18:49:32', '2025-12-05 18:49:32'),
(53, 'CHILI POWDER-100G', 'SKU-000053', NULL, NULL, NULL, NULL, NULL, 6, 3, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:49:53', '2025-12-05 18:49:53'),
(54, 'CHILI POWDER-250G', 'SKU-000054', NULL, NULL, NULL, NULL, NULL, 6, 3, 'pcs', 'pcs', NULL, 1.0000, 0, '2025-12-05 18:51:06', '2025-12-05 18:51:06'),
(55, 'TASTE BITS 80G', 'SKU-000055', NULL, NULL, NULL, 10, NULL, 3, 4, 'pcs', 'g', 'g', 1.0000, 0, '2025-12-05 18:51:25', '2025-12-05 18:51:25');

-- --------------------------------------------------------

--
-- Table structure for table `product_supplier`
--

CREATE TABLE `product_supplier` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `vendor_product_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_preferred` tinyint(1) NOT NULL DEFAULT '0',
  `lead_time_days` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_supplier`
--

INSERT INTO `product_supplier` (`id`, `product_id`, `supplier_id`, `vendor_product_code`, `is_preferred`, `lead_time_days`, `created_at`, `updated_at`) VALUES
(1, 20, 6, 'SMK-000020', 1, NULL, '2025-12-05 17:44:05', '2025-12-05 17:44:05'),
(2, 18, 6, 'SMK-000018', 0, NULL, '2025-12-05 17:44:31', '2025-12-05 17:44:31'),
(3, 46, 7, 'DIS-000046', 0, NULL, '2025-12-05 18:42:35', '2025-12-05 18:42:35'),
(4, 51, 7, 'DIS-000051', 0, NULL, '2025-12-05 18:48:48', '2025-12-05 18:48:48');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(2, 'Admin', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(3, 'Manager', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(4, 'Cashier', 'web', '2025-11-25 16:39:54', '2025-11-25 16:39:54'),
(5, 'Stock Clerk', 'web', '2025-11-25 16:39:55', '2025-11-25 16:39:55'),
(6, 'Accountant', 'web', '2025-11-25 16:39:55', '2025-11-25 16:39:55');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(177, 1),
(181, 1),
(182, 1),
(183, 1),
(184, 1),
(186, 1),
(187, 1),
(188, 1),
(189, 1),
(190, 1),
(191, 1),
(192, 1),
(193, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(45, 2),
(46, 2),
(47, 2),
(48, 2),
(49, 2),
(50, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(89, 2),
(91, 2),
(92, 2),
(93, 2),
(94, 2),
(95, 2),
(96, 2),
(97, 2),
(98, 2),
(99, 2),
(177, 2),
(181, 2),
(182, 2),
(183, 2),
(184, 2),
(186, 2),
(187, 2),
(188, 2),
(189, 2),
(190, 2),
(191, 2),
(192, 2),
(193, 2),
(1, 3),
(3, 3),
(5, 3),
(9, 3),
(13, 3),
(17, 3),
(18, 3),
(19, 3),
(21, 3),
(22, 3),
(23, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 3),
(30, 3),
(31, 3),
(32, 3),
(33, 3),
(36, 3),
(37, 3),
(38, 3),
(39, 3),
(40, 3),
(42, 3),
(43, 3),
(44, 3),
(45, 3),
(46, 3),
(60, 3),
(61, 3),
(63, 3),
(64, 3),
(68, 3),
(70, 3),
(72, 3),
(1, 4),
(5, 4),
(9, 4),
(27, 4),
(29, 4),
(30, 4),
(31, 4),
(32, 4),
(33, 4),
(59, 4),
(60, 4),
(71, 4),
(1, 5),
(3, 5),
(5, 5),
(9, 5),
(13, 5),
(17, 5),
(21, 5),
(22, 5),
(23, 5),
(25, 5),
(26, 5),
(27, 5),
(28, 5),
(38, 5),
(39, 5),
(1, 6),
(5, 6),
(9, 6),
(30, 6),
(31, 6),
(32, 6),
(45, 6),
(46, 6),
(47, 6),
(64, 6),
(68, 6),
(69, 6),
(72, 6);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `tax` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_returns`
--

CREATE TABLE `sales_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `return_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_date` date NOT NULL,
  `return_reason` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `refund_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `refund_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_return_items`
--

CREATE TABLE `sales_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sales_return_id` bigint UNSIGNED NOT NULL,
  `sale_item_id` bigint UNSIGNED NOT NULL,
  `stock_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity_returned` int NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `tax` decimal(5,2) NOT NULL DEFAULT '0.00',
  `item_total` decimal(10,2) NOT NULL,
  `condition` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Good',
  `restore_to_stock` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `stock_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_carts`
--

CREATE TABLE `saved_carts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `cart_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_cart_items`
--

CREATE TABLE `saved_cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `saved_cart_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `stock_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `tax` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('hRg0Q7qaklg9cAg9sVGLM5qPu8M0MfmlbcDmsznA', 2, '175.157.139.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUjhkdUlIZXNOWU80WE9abkJiZU5PTm0yN3B4N0NzT2FwZEZCT1BkQSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vdmVydGV4Y29yZWFpLmNvbS92cG9zL2htYXJ0L3Byb2R1Y3RzIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1764964266),
('oNbwlfg1biFeDj1zorzKRCKbnTkwvgbvRE8p0sgA', 2, '112.134.142.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQlJBdkd1VzBEOERLUkVES2trQXFnOUZYTHpvZlVBMUZadEN6bmROeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6ODQ6Imh0dHBzOi8vdmVydGV4Y29yZWFpLmNvbS92cG9zL2htYXJ0L3Byb2R1Y3RzP2JyYW5kX2lkPTQmY2F0ZWdvcnlfaWQ9JnNlYXJjaD0mc3RhdHVzPSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1764964295);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `shift_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clock_in_at` timestamp NOT NULL,
  `clock_out_at` timestamp NULL DEFAULT NULL,
  `opening_cash` decimal(15,2) DEFAULT NULL,
  `closing_cash` decimal(15,2) DEFAULT NULL,
  `expected_cash` decimal(15,2) DEFAULT NULL,
  `cash_difference` decimal(15,2) DEFAULT NULL,
  `total_sales` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_sales_count` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','completed','approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `tax` decimal(5,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `available_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT NULL,
  `current_credit_used` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_name`, `business_type`, `tax_id`, `contact_person`, `phone`, `mobile`, `payment_terms`, `credit_limit`, `current_credit_used`, `created_at`, `updated_at`) VALUES
(6, 'SMACK', 'Agent', NULL, 'Anusha Kumari', '0112925027', '0772015555', NULL, NULL, 0.00, '2025-12-05 17:38:20', '2025-12-05 17:42:44'),
(7, 'Dissanayaka Distributors (Badulla)', 'Agent', NULL, 'Tharindu Dhananjaya', '0768213537', NULL, NULL, NULL, 0.00, '2025-12-05 18:31:56', '2025-12-05 18:31:56');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_credits`
--

CREATE TABLE `supplier_credits` (
  `id` bigint UNSIGNED NOT NULL,
  `credit_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `good_receive_note_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `credit_terms` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit_days` int NOT NULL,
  `original_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `outstanding_amount` decimal(15,2) NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `supplier_credit_id` bigint UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `processed_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_returns`
--

CREATE TABLE `supplier_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `return_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `good_receive_note_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `return_reason` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(15,2) NOT NULL DEFAULT '0.00',
  `adjustment` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_return_items`
--

CREATE TABLE `supplier_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_return_id` bigint UNSIGNED NOT NULL,
  `stock_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `quantity_returned` int NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `tax` decimal(5,2) NOT NULL DEFAULT '0.00',
  `item_total` decimal(10,2) NOT NULL,
  `condition` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Damaged',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'superadmin@pos.com', '2025-11-25 16:39:55', '$2y$12$0EfjhcrZZchnt1kBrx9e0OtBlHPU28S/IuBYFITwSRc4qoSuvXXGu', NULL, NULL, NULL, NULL, '2025-11-25 16:39:55', '2025-11-25 16:39:55'),
(2, 'admin', 'admin@pos.com', '2025-11-25 16:39:55', '$2y$12$Pde/Hz37WwlGyQV78qYvbeRYH7Q5cvLg1rpnz1fpk4YyAHkAjQ6Mm', NULL, NULL, NULL, 'vc7LzRWO0AB64OM85JzmaA1bKSqalS4PGB4NOEXKdkX8Ex5Hh7P32ptZppnM', '2025-11-25 16:39:55', '2025-11-25 16:39:55'),
(3, 'cashier1', 'cashier1@pos.com', '2025-11-25 16:39:56', '$2y$12$HEV0H7twD1uVJDPiCDexdumOFR0efnRXp4Pi7ZOuvFtBKiZWTpiJm', NULL, NULL, NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56'),
(4, 'cashier2', 'cashier2@pos.com', '2025-11-25 16:39:56', '$2y$12$kTnSk1ulEXM8Cse01jwyeuLKXQ6eZzwMo9zGRwiTVFe1t8q3C4wNC', NULL, NULL, NULL, NULL, '2025-11-25 16:39:56', '2025-11-25 16:39:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounts_account_code_unique` (`account_code`),
  ADD KEY `accounts_account_type_id_foreign` (`account_type_id`),
  ADD KEY `accounts_parent_account_id_foreign` (`parent_account_id`);

--
-- Indexes for table `account_balances`
--
ALTER TABLE `account_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_balances_account_id_fiscal_year_fiscal_period_unique` (`account_id`,`fiscal_year`,`fiscal_period`),
  ADD KEY `account_balances_fiscal_year_fiscal_period_index` (`fiscal_year`,`fiscal_period`);

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_types_name_unique` (`name`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `batches_batch_number_unique` (`batch_number`),
  ADD UNIQUE KEY `batches_barcode_unique` (`barcode`),
  ADD KEY `batches_good_receive_note_id_foreign` (`good_receive_note_id`),
  ADD KEY `batches_expiry_date_index` (`expiry_date`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_brand_name_unique` (`brand_name`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_cat_name_unique` (`cat_name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_customer_number_unique` (`customer_number`);

--
-- Indexes for table `customer_credits`
--
ALTER TABLE `customer_credits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_credits_credit_number_unique` (`credit_number`),
  ADD KEY `customer_credits_sale_id_foreign` (`sale_id`),
  ADD KEY `customer_credits_created_by_foreign` (`created_by`),
  ADD KEY `customer_credits_customer_id_status_index` (`customer_id`,`status`),
  ADD KEY `customer_credits_due_date_index` (`due_date`),
  ADD KEY `customer_credits_status_index` (`status`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_payments_payment_number_unique` (`payment_number`),
  ADD KEY `customer_payments_processed_by_foreign` (`processed_by`),
  ADD KEY `customer_payments_customer_id_payment_date_index` (`customer_id`,`payment_date`),
  ADD KEY `customer_payments_customer_credit_id_index` (`customer_credit_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  ADD KEY `employees_user_id_status_index` (`user_id`,`status`),
  ADD KEY `employees_employee_number_index` (`employee_number`),
  ADD KEY `employees_status_index` (`status`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  ADD KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  ADD KEY `expenses_approved_by_foreign` (`approved_by`),
  ADD KEY `expenses_paid_by_foreign` (`paid_by`),
  ADD KEY `expenses_created_by_foreign` (`created_by`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_categories_category_name_unique` (`category_name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fiscal_periods`
--
ALTER TABLE `fiscal_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fiscal_periods_year_month_unique` (`year`,`month`),
  ADD KEY `fiscal_periods_closed_by_foreign` (`closed_by`),
  ADD KEY `fiscal_periods_status_index` (`status`);

--
-- Indexes for table `good_receive_notes`
--
ALTER TABLE `good_receive_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `good_receive_notes_grn_number_unique` (`grn_number`),
  ADD KEY `good_receive_notes_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_entries_entry_number_unique` (`entry_number`),
  ADD KEY `journal_entries_created_by_foreign` (`created_by`),
  ADD KEY `journal_entries_approved_by_foreign` (`approved_by`),
  ADD KEY `journal_entries_voided_by_foreign` (`voided_by`),
  ADD KEY `journal_entries_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  ADD KEY `journal_entries_entry_date_status_index` (`entry_date`,`status`);

--
-- Indexes for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entry_lines_journal_entry_id_line_number_index` (`journal_entry_id`,`line_number`),
  ADD KEY `journal_entry_lines_account_id_index` (`account_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_reminders`
--
ALTER TABLE `payment_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_reminders_supplier_credit_id_status_index` (`supplier_credit_id`,`status`);

--
-- Indexes for table `payroll_entries`
--
ALTER TABLE `payroll_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_entries_payroll_period_id_employee_id_unique` (`payroll_period_id`,`employee_id`),
  ADD KEY `payroll_entries_employee_id_foreign` (`employee_id`),
  ADD KEY `payroll_entries_payroll_period_id_employee_id_index` (`payroll_period_id`,`employee_id`),
  ADD KEY `payroll_entries_status_index` (`status`);

--
-- Indexes for table `payroll_entry_shift`
--
ALTER TABLE `payroll_entry_shift`
  ADD PRIMARY KEY (`payroll_entry_id`,`shift_id`),
  ADD KEY `payroll_entry_shift_shift_id_foreign` (`shift_id`);

--
-- Indexes for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_periods_processed_by_foreign` (`processed_by`),
  ADD KEY `payroll_periods_approved_by_foreign` (`approved_by`),
  ADD KEY `payroll_periods_period_start_period_end_index` (`period_start`,`period_end`),
  ADD KEY `payroll_periods_status_index` (`status`);

--
-- Indexes for table `payroll_settings`
--
ALTER TABLE `payroll_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_settings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_vendor_code_unique` (`supplier_id`,`vendor_product_code`),
  ADD KEY `product_supplier_product_id_foreign` (`product_id`),
  ADD KEY `product_supplier_vendor_product_code_index` (`vendor_product_code`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_sale_number_unique` (`sale_number`),
  ADD KEY `sales_shift_id_index` (`shift_id`),
  ADD KEY `sales_customer_id_foreign` (`customer_id`),
  ADD KEY `sales_created_at_index` (`created_at`),
  ADD KEY `sales_created_at_total_index` (`created_at`,`total`),
  ADD KEY `sales_user_id_index` (`user_id`);

--
-- Indexes for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_returns_return_number_unique` (`return_number`),
  ADD KEY `sales_returns_processed_by_foreign` (`processed_by`),
  ADD KEY `sales_returns_sale_id_index` (`sale_id`),
  ADD KEY `sales_returns_status_index` (`status`);

--
-- Indexes for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_return_items_stock_id_foreign` (`stock_id`),
  ADD KEY `sales_return_items_product_id_foreign` (`product_id`),
  ADD KEY `sales_return_items_sales_return_id_index` (`sales_return_id`),
  ADD KEY `sales_return_items_sale_item_id_index` (`sale_item_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_items_stock_id_foreign` (`stock_id`),
  ADD KEY `sale_items_product_id_index` (`product_id`),
  ADD KEY `sale_items_product_id_quantity_index` (`product_id`,`quantity`);

--
-- Indexes for table `saved_carts`
--
ALTER TABLE `saved_carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saved_carts_user_id_foreign` (`user_id`);

--
-- Indexes for table `saved_cart_items`
--
ALTER TABLE `saved_cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saved_cart_items_saved_cart_id_foreign` (`saved_cart_id`),
  ADD KEY `saved_cart_items_product_id_foreign` (`product_id`),
  ADD KEY `saved_cart_items_stock_id_foreign` (`stock_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shifts_shift_number_unique` (`shift_number`),
  ADD KEY `shifts_user_id_status_index` (`user_id`,`status`),
  ADD KEY `shifts_clock_in_at_index` (`clock_in_at`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stocks_product_id_foreign` (`product_id`),
  ADD KEY `stocks_batch_id_foreign` (`batch_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_credits`
--
ALTER TABLE `supplier_credits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_credits_credit_number_unique` (`credit_number`),
  ADD KEY `supplier_credits_good_receive_note_id_foreign` (`good_receive_note_id`),
  ADD KEY `supplier_credits_created_by_foreign` (`created_by`),
  ADD KEY `supplier_credits_supplier_id_status_index` (`supplier_id`,`status`),
  ADD KEY `supplier_credits_due_date_index` (`due_date`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_payments_payment_number_unique` (`payment_number`),
  ADD KEY `supplier_payments_processed_by_foreign` (`processed_by`),
  ADD KEY `supplier_payments_supplier_id_payment_date_index` (`supplier_id`,`payment_date`),
  ADD KEY `supplier_payments_supplier_credit_id_index` (`supplier_credit_id`);

--
-- Indexes for table `supplier_returns`
--
ALTER TABLE `supplier_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_returns_return_number_unique` (`return_number`),
  ADD KEY `supplier_returns_created_by_foreign` (`created_by`),
  ADD KEY `supplier_returns_approved_by_foreign` (`approved_by`),
  ADD KEY `supplier_returns_good_receive_note_id_index` (`good_receive_note_id`),
  ADD KEY `supplier_returns_supplier_id_index` (`supplier_id`),
  ADD KEY `supplier_returns_status_index` (`status`);

--
-- Indexes for table `supplier_return_items`
--
ALTER TABLE `supplier_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_return_items_product_id_foreign` (`product_id`),
  ADD KEY `supplier_return_items_batch_id_foreign` (`batch_id`),
  ADD KEY `supplier_return_items_supplier_return_id_index` (`supplier_return_id`),
  ADD KEY `supplier_return_items_stock_id_index` (`stock_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `account_balances`
--
ALTER TABLE `account_balances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_credits`
--
ALTER TABLE `customer_credits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiscal_periods`
--
ALTER TABLE `fiscal_periods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `good_receive_notes`
--
ALTER TABLE `good_receive_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `payment_reminders`
--
ALTER TABLE `payment_reminders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_entries`
--
ALTER TABLE `payroll_entries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_settings`
--
ALTER TABLE `payroll_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `product_supplier`
--
ALTER TABLE `product_supplier`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_returns`
--
ALTER TABLE `sales_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_carts`
--
ALTER TABLE `saved_carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_cart_items`
--
ALTER TABLE `saved_cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `supplier_credits`
--
ALTER TABLE `supplier_credits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_returns`
--
ALTER TABLE `supplier_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_return_items`
--
ALTER TABLE `supplier_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_account_type_id_foreign` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounts_parent_account_id_foreign` FOREIGN KEY (`parent_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `account_balances`
--
ALTER TABLE `account_balances`
  ADD CONSTRAINT `account_balances_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_good_receive_note_id_foreign` FOREIGN KEY (`good_receive_note_id`) REFERENCES `good_receive_notes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_credits`
--
ALTER TABLE `customer_credits`
  ADD CONSTRAINT `customer_credits_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `customer_credits_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_credits_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_customer_credit_id_foreign` FOREIGN KEY (`customer_credit_id`) REFERENCES `customer_credits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_payments_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fiscal_periods`
--
ALTER TABLE `fiscal_periods`
  ADD CONSTRAINT `fiscal_periods_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `good_receive_notes`
--
ALTER TABLE `good_receive_notes`
  ADD CONSTRAINT `good_receive_notes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_entries_voided_by_foreign` FOREIGN KEY (`voided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  ADD CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_reminders`
--
ALTER TABLE `payment_reminders`
  ADD CONSTRAINT `payment_reminders_supplier_credit_id_foreign` FOREIGN KEY (`supplier_credit_id`) REFERENCES `supplier_credits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_entries`
--
ALTER TABLE `payroll_entries`
  ADD CONSTRAINT `payroll_entries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_entries_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_entry_shift`
--
ALTER TABLE `payroll_entry_shift`
  ADD CONSTRAINT `payroll_entry_shift_payroll_entry_id_foreign` FOREIGN KEY (`payroll_entry_id`) REFERENCES `payroll_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_entry_shift_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD CONSTRAINT `payroll_periods_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payroll_periods_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payroll_settings`
--
ALTER TABLE `payroll_settings`
  ADD CONSTRAINT `payroll_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD CONSTRAINT `product_supplier_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_supplier_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD CONSTRAINT `sales_returns_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sales_returns_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD CONSTRAINT `sales_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sales_return_items_sale_item_id_foreign` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_items` (`id`),
  ADD CONSTRAINT `sales_return_items_sales_return_id_foreign` FOREIGN KEY (`sales_return_id`) REFERENCES `sales_returns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_return_items_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`);

--
-- Constraints for table `saved_carts`
--
ALTER TABLE `saved_carts`
  ADD CONSTRAINT `saved_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_cart_items`
--
ALTER TABLE `saved_cart_items`
  ADD CONSTRAINT `saved_cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_cart_items_saved_cart_id_foreign` FOREIGN KEY (`saved_cart_id`) REFERENCES `saved_carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_cart_items_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_credits`
--
ALTER TABLE `supplier_credits`
  ADD CONSTRAINT `supplier_credits_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `supplier_credits_good_receive_note_id_foreign` FOREIGN KEY (`good_receive_note_id`) REFERENCES `good_receive_notes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `supplier_credits_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `supplier_payments_supplier_credit_id_foreign` FOREIGN KEY (`supplier_credit_id`) REFERENCES `supplier_credits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_returns`
--
ALTER TABLE `supplier_returns`
  ADD CONSTRAINT `supplier_returns_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `supplier_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `supplier_returns_good_receive_note_id_foreign` FOREIGN KEY (`good_receive_note_id`) REFERENCES `good_receive_notes` (`id`),
  ADD CONSTRAINT `supplier_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `supplier_return_items`
--
ALTER TABLE `supplier_return_items`
  ADD CONSTRAINT `supplier_return_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`),
  ADD CONSTRAINT `supplier_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `supplier_return_items_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`),
  ADD CONSTRAINT `supplier_return_items_supplier_return_id_foreign` FOREIGN KEY (`supplier_return_id`) REFERENCES `supplier_returns` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
