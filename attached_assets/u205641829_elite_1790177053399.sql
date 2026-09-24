-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 23, 2026 at 03:20 PM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u205641829_elite`
--

-- --------------------------------------------------------

--
-- Table structure for table `additionals`
--

CREATE TABLE `additionals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_bn` varchar(255) DEFAULT NULL,
  `price` double(8,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `description` longtext DEFAULT NULL,
  `description_bn` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `additional_orders`
--

CREATE TABLE `additional_orders` (
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `additional_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `additional_services`
--

CREATE TABLE `additional_services` (
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `additional_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `address_name` varchar(255) DEFAULT NULL,
  `road_no` varchar(255) DEFAULT NULL,
  `house_no` varchar(255) DEFAULT NULL,
  `flat_no` varchar(255) DEFAULT NULL,
  `house_name` varchar(255) DEFAULT NULL,
  `block` varchar(255) DEFAULT NULL,
  `sub_district_id` bigint(20) UNSIGNED DEFAULT NULL,
  `district_id` bigint(20) UNSIGNED DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `address_line` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `delivery_note` text DEFAULT NULL,
  `post_code` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_device_keys`
--

CREATE TABLE `admin_device_keys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `thumbnail_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_banner` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `vendor_id`, `title`, `description`, `thumbnail_id`, `is_active`, `is_banner`, `created_at`, `updated_at`) VALUES
(3, NULL, 'test', NULL, 28, 1, 0, '2026-09-22 22:19:59', '2026-09-22 22:19:59');

-- --------------------------------------------------------

--
-- Table structure for table `card_infos`
--

CREATE TABLE `card_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `card` varchar(255) NOT NULL,
  `cvc` varchar(255) NOT NULL,
  `last_no` int(11) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `exp_month` int(11) NOT NULL,
  `exp_year` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(80) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `discount_type` enum('percent','amount') NOT NULL,
  `discount` double(8,2) NOT NULL,
  `min_amount` double(8,2) NOT NULL,
  `started_at` timestamp NOT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_users`
--

CREATE TABLE `coupon_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `coupon_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stripe_customer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `created_at`, `updated_at`, `stripe_customer`) VALUES
(10, 19, '2026-09-22 22:19:02', '2026-09-22 22:19:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_costs`
--

CREATE TABLE `delivery_costs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cost` double(8,2) NOT NULL,
  `fee_cost` double(8,2) NOT NULL,
  `minimum_cost` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_keys`
--

CREATE TABLE `device_keys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `device_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_approve` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_device_keys`
--

CREATE TABLE `driver_device_keys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_histories`
--

CREATE TABLE `driver_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_notifications`
--

CREATE TABLE `driver_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `isRead` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_orders`
--

CREATE TABLE `driver_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `is_accept` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pick-up'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faq_category_id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faq_categories`
--

CREATE TABLE `faq_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_manages`
--

CREATE TABLE `invoice_manages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `thumbnail_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `title`, `name`, `created_at`, `updated_at`, `thumbnail_id`) VALUES
(1, 'English', 'en', '2026-08-06 20:45:09', '2026-08-06 20:45:09', 1),
(2, 'Arabic', 'ar', '2026-08-06 20:45:09', '2026-08-06 20:45:09', 2);

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('image','audio','video','docs','excel','pdf','other') DEFAULT NULL,
  `name` text DEFAULT NULL,
  `src` text NOT NULL,
  `extention` varchar(45) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `path` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `type`, `name`, `src`, `extention`, `description`, `path`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'flags/en.jpg', NULL, NULL, NULL, '2026-08-06 20:45:09', '2026-08-06 20:45:09'),
(2, NULL, NULL, 'flags/ar.jpg', NULL, NULL, NULL, '2026-08-06 20:45:09', '2026-08-06 20:45:09'),
(3, 'image', 'logo -01.jpg', 'images/banners/nWo6dthpOw8sWHBQsOhzVRKthLGdRiFirjIZx9d7.jpg', NULL, 'this image for website slider banner', 'images/banners/nWo6dthpOw8sWHBQsOhzVRKthLGdRiFirjIZx9d7.jpg', '2026-08-11 04:29:01', '2026-08-11 04:29:01'),
(4, 'image', 'Gemini_Generated_Image_espbeiespbeiespb.png', 'images/services/xAyvpz1ifEZYGOGyT6JX9ex68zbMQZTaCTlNus1e.png', NULL, 'this image for service thumbnail', 'images/services/xAyvpz1ifEZYGOGyT6JX9ex68zbMQZTaCTlNus1e.png', '2026-08-11 20:19:40', '2026-08-11 20:19:40'),
(5, 'image', 'logo (1).png', 'images/services/58mKybmQEsHTyaEfaagTmBtHYNAWdVLJEYbj1XSl.png', NULL, 'this image for service thumbnail', 'images/services/58mKybmQEsHTyaEfaagTmBtHYNAWdVLJEYbj1XSl.png', '2026-08-16 23:16:55', '2026-08-16 23:16:55'),
(10, 'image', 'image.jpg', 'images/services/TKUgYkBdoy1Da1VqVp3qfhrKm8slgevsfRyShT5Y.jpg', NULL, 'this image for service thumbnail', 'images/services/TKUgYkBdoy1Da1VqVp3qfhrKm8slgevsfRyShT5Y.jpg', '2026-08-17 02:01:40', '2026-08-17 02:01:40'),
(11, 'image', 'image.jpg', 'images/products/GyGspeYrlHjfJcOGWvVlsTubYPif4NnuflBQEXvw.jpg', NULL, 'this image for product thumbnail', 'images/products/GyGspeYrlHjfJcOGWvVlsTubYPif4NnuflBQEXvw.jpg', '2026-08-17 02:02:29', '2026-08-17 02:02:29'),
(12, 'image', 'Shirts product.jpg', 'images/products/Ery16BCzaD5QsovlupOwIVjmKeFPZ7U0cJRNqLxk.jpg', 'jpg', NULL, 'images/products/Ery16BCzaD5QsovlupOwIVjmKeFPZ7U0cJRNqLxk.jpg', '2026-08-17 22:10:43', '2026-08-17 22:10:43'),
(13, 'image', 'Trousers product.jpg', 'images/products/i0RBDjaJOoSA9dxXEuwTNQrmUk28yhec4qVC1KvW.jpg', 'jpg', NULL, 'images/products/i0RBDjaJOoSA9dxXEuwTNQrmUk28yhec4qVC1KvW.jpg', '2026-08-17 22:10:43', '2026-08-17 22:10:43'),
(14, 'image', 'Dresses product.jpg', 'images/products/ShLOdCVivWso7ePjEgQ32m6pznc1a0x8BYGKDl5R.jpg', 'jpg', NULL, 'images/products/ShLOdCVivWso7ePjEgQ32m6pznc1a0x8BYGKDl5R.jpg', '2026-08-17 22:10:43', '2026-08-17 22:10:43'),
(15, 'image', 'Towels product.jpg', 'images/products/H8uYeqlWfTg0OJG9DsZ2xiEVbdRSUmoc4AatrK1k.jpg', 'jpg', NULL, 'images/products/H8uYeqlWfTg0OJG9DsZ2xiEVbdRSUmoc4AatrK1k.jpg', '2026-08-17 22:10:43', '2026-08-17 22:10:43'),
(16, 'image', 'Coats product.jpg', 'images/products/jzc653Xfl18R4rQHWmOxpsYFUkPa0ADNLEtIwinJ.jpg', 'jpg', NULL, 'images/products/jzc653Xfl18R4rQHWmOxpsYFUkPa0ADNLEtIwinJ.jpg', '2026-08-17 22:10:43', '2026-08-17 22:10:43'),
(17, 'image', 'Bedsheets product.jpg', 'images/products/gy4FwHtWbVMXAIjr0P57BLZqJS3cGfioaNndCxYv.jpg', 'jpg', NULL, 'images/products/gy4FwHtWbVMXAIjr0P57BLZqJS3cGfioaNndCxYv.jpg', '2026-08-17 22:10:43', '2026-08-17 22:10:43'),
(18, 'image', 'Home Laundry service.jpg', 'images/services/yNA398wQRzHLnr57u0UlkWsJTqG2jS4mZgfEbKax.jpg', 'jpg', NULL, 'images/services/yNA398wQRzHLnr57u0UlkWsJTqG2jS4mZgfEbKax.jpg', '2026-08-17 22:17:30', '2026-08-17 22:17:30'),
(19, 'image', 'Ironing & Pressing service.jpg', 'images/services/X1kh7nfFpaVv9UlNBDLHCAG5suzoW8STMj6Kgq3t.jpg', 'jpg', NULL, 'images/services/X1kh7nfFpaVv9UlNBDLHCAG5suzoW8STMj6Kgq3t.jpg', '2026-08-17 22:17:31', '2026-08-17 22:17:31'),
(20, 'image', 'Dry Cleaning service.jpg', 'images/services/IwdkZyYfrn7M4ciFlEhC9zL0Q8125eOomAsPgGJT.jpg', 'jpg', NULL, 'images/services/IwdkZyYfrn7M4ciFlEhC9zL0Q8125eOomAsPgGJT.jpg', '2026-08-17 22:17:31', '2026-08-17 22:17:31'),
(21, 'image', 'dzfs.png', 'images/services/d8TthAe45LxyD0oxfKBhAQyMVq5NWh1NedklJqS3.png', 'jpg', NULL, 'images/services/d8TthAe45LxyD0oxfKBhAQyMVq5NWh1NedklJqS3.png', '2026-08-17 22:17:31', '2026-08-18 03:48:21'),
(22, 'image', 'Sofa Cleaning service.jpg', 'images/services/KiAuchkJpeF6q4IrdsUSlR15Cn8PZjVzoaOHYmQ7.jpg', 'jpg', NULL, 'images/services/KiAuchkJpeF6q4IrdsUSlR15Cn8PZjVzoaOHYmQ7.jpg', '2026-08-17 22:17:31', '2026-08-17 22:17:31'),
(23, 'image', 'banner test.jpg', 'images/banners/sIvhFaG4rUBkqRbepm69VjAnz3YZ1fJWCyQSc5td.jpg', 'jpg', NULL, 'images/banners/sIvhFaG4rUBkqRbepm69VjAnz3YZ1fJWCyQSc5td.jpg', '2026-08-17 22:25:33', '2026-08-17 22:25:33'),
(24, 'image', 'dzfs.png', 'images/products/BX33OVDrZh7rxgFsGHi4OQWQmuf9QKDLlNHKEpxL.png', NULL, 'this image for product thumbnail', 'images/products/BX33OVDrZh7rxgFsGHi4OQWQmuf9QKDLlNHKEpxL.png', '2026-08-18 01:53:06', '2026-08-18 01:53:06'),
(25, 'image', 'LOGO 1DS .png', 'images/products/6K3Ka2FezVnZcXrDYiTRiBllR2KKwaxLN0UTqZVe.png', NULL, 'this image for product thumbnail', 'images/products/6K3Ka2FezVnZcXrDYiTRiBllR2KKwaxLN0UTqZVe.png', '2026-08-18 02:21:45', '2026-08-18 02:21:45'),
(26, 'image', 'Screenshot 2026-08-17 185509.png', 'images/customers/hmhs5Tb64wNjpBPDHbpBisG19YsVqovrRCg4G2mw.png', NULL, 'customer images', 'images/customers/hmhs5Tb64wNjpBPDHbpBisG19YsVqovrRCg4G2mw.png', '2026-08-18 04:05:10', '2026-08-18 04:05:10'),
(27, 'image', 'd0aa278a-d348-4dd9-a693-e0a2c09b5095.jpg', 'images/vendors/ZHGODR9T3n5FlrxDEb7s0U7TTVlgFrSpjYAAfjr8.jpg', NULL, 'vendor logo', 'images/vendors/ZHGODR9T3n5FlrxDEb7s0U7TTVlgFrSpjYAAfjr8.jpg', '2026-09-21 07:03:56', '2026-09-21 07:03:56'),
(28, 'image', 'Gemini_Generated_Image_9nbr7y9nbr7y9nbr.jpg', 'images/banners/A1mYq5PkEzscxNLbhkgJuljHHO6ToOACN3rQfPFe.jpg', NULL, 'this image for website slider banner', 'images/banners/A1mYq5PkEzscxNLbhkgJuljHHO6ToOACN3rQfPFe.jpg', '2026-09-22 22:19:59', '2026-09-22 22:19:59');

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
(1, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(2, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(3, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(4, '2016_06_01_000004_create_oauth_clients_table', 1),
(5, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(6, '2021_09_01_000000_create_media_table', 1),
(7, '2021_09_01_100000_create_users_table', 1),
(8, '2021_09_01_200000_create_password_resets_table', 1),
(9, '2021_09_02_131940_create_failed_jobs_table', 1),
(10, '2021_09_02_131953_create_permission_tables', 1),
(11, '2021_09_08_162725_create_services_table', 1),
(12, '2021_09_11_085103_create_customers_table', 1),
(13, '2021_09_13_171450_create_variants_table', 1),
(14, '2021_09_15_064533_create_products_table', 1),
(15, '2021_09_20_052130_create_addresses_table', 1),
(16, '2021_09_21_045821_create_banners_table', 1),
(17, '2021_09_21_045849_create_coupons_table', 1),
(18, '2021_09_21_045910_create_orders_table', 1),
(19, '2021_09_22_051934_create_order_products_table', 1),
(20, '2021_10_20_105127_create_verification_codes_table', 1),
(21, '2021_10_24_090519_create_service_variants_table', 1),
(22, '2021_10_26_163146_create_settings_table', 1),
(23, '2021_11_02_115237_create_ratings_table', 1),
(24, '2021_11_20_072845_create_coupon_users_table', 1),
(25, '2021_12_20_085405_add_column_instraction_to_order_table', 1),
(26, '2022_01_13_070755_add_position_columns_in_variants_table', 1),
(27, '2022_02_05_141204_add_bangla_columns_to_variants_table', 1),
(28, '2022_02_05_194335_add_bangla_columns_to_services_table', 1),
(29, '2022_02_05_201107_add_bangla_columns_to_products_table', 1),
(30, '2022_02_12_000220_add_remove_status_colmun_orders_table', 1),
(31, '2022_02_12_000230_add_change_status_to_orders_table', 1),
(32, '2022_02_27_213854_add_order_colmun_to_products_table', 1),
(33, '2022_03_05_120307_create_additionals_table', 1),
(34, '2022_03_05_120500_create_additional_services_table', 1),
(35, '2022_03_06_103410_create_additional_orders_table', 1),
(36, '2022_04_13_123324_create_contacts_table', 1),
(37, '2022_06_06_211817_create_delivery_costs_table', 1),
(38, '2022_06_30_152555_create_mobile_app_urls_table', 1),
(39, '2022_07_05_123925_create_payments_table', 1),
(40, '2022_07_19_101634_add_description_to_products_table', 1),
(41, '2022_08_03_114942_create_device_keys_table', 1),
(42, '2022_08_10_160245_add_delete_at_addresses_table', 1),
(43, '2022_08_11_120358_create_notifications_table', 1),
(44, '2022_08_11_163235_create_admin_device_key_table', 1),
(45, '2022_08_21_180225_create_card_infos_table', 1),
(46, '2022_09_10_115554_add_stripe_customer_column_to_customers_table', 1),
(47, '2022_09_21_182517_create_drivers_table', 1),
(48, '2022_09_22_125851_create_driver_orders_table', 1),
(49, '2022_09_26_162755_create_driver_device_key_table', 1),
(50, '2022_09_28_170609_create_driver_notifications_table', 1),
(51, '2022_10_10_111423_add_driver_lience_and_brithday_column_to_user_table', 1),
(52, '2022_10_30_152931_create_social_link_table', 1),
(53, '2023_01_08_130313_add_device_type_column_to_device_key_table', 1),
(54, '2023_01_12_104744_create_stripe_keys_table', 1),
(55, '2023_01_12_114626_create_web_settings_table', 1),
(56, '2023_02_01_105110_add_title_column_to_notifications_table', 1),
(57, '2023_02_01_164032_create_order_schedules_table', 1),
(58, '2023_02_11_111232_add_address_column_to_web_setting_table', 1),
(59, '2023_02_11_122549_create_invoice_manages_table', 1),
(60, '2023_02_12_173746_add_signature_column_to_web_setting_table', 1),
(61, '2023_03_31_115106_create_areas_table', 1),
(62, '2023_04_01_111037_create_driver_histories_table', 1),
(63, '2023_04_01_111855_add_status_column_to_driver_orders_table', 1),
(64, '2023_04_01_114107_add_is_approve_to_drivers_table', 1),
(65, '2023_05_29_160539_add_is_show_column_to_order_table', 1),
(66, '2023_05_30_104843_add_soft_delete_to_product_table', 1),
(67, '2023_06_15_133341_change_mobile_number_colum_to_users_table', 1),
(68, '2023_07_10_160846_add_delivery_charge_to_order_table', 1),
(69, '2023_07_15_111724_add_product_id_to_products_table', 1),
(70, '2023_10_31_115258_create_subscriptions_table', 1),
(71, '2023_10_31_115419_create_user_has_subscriptions', 1),
(72, '2024_02_13_110904_create_notification_manages_table', 1),
(73, '2024_02_14_153629_update_notification_manage_table', 1),
(74, '2024_12_10_123556_create_payment_gateways', 1),
(75, '2024_12_14_110818_change_column_in_orders_table', 1),
(76, '2024_12_17_161054_create_transactions_table', 1),
(77, '2024_12_30_164849_create_languages_table', 1),
(78, '2024_12_30_172224_add_to_thumbnail_id_table', 1),
(79, '2026_02_09_153533_create_website_settings_table', 1),
(80, '2026_02_17_135757_create_faq_categories_table', 1),
(81, '2026_02_17_135758_create_faqs_table', 1),
(82, '2026_05_18_114327_add_your_field_to_web_settings_table', 1),
(83, '2026_06_02_000001_add_tax_rate_to_web_settings_table', 1),
(84, '2026_08_20_000001_add_pos_order_fields_to_orders_and_order_products_tables', 1),
(85, '2026_09_20_000001_create_vendors_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `mobile_app_urls`
--

CREATE TABLE `mobile_app_urls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `android_url` text DEFAULT NULL,
  `ios_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(100) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(100) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 16),
(6, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 19);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL,
  `isRead` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_manages`
--

CREATE TABLE `notification_manages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('00fbb1273e3be1e27d2167209cc6f6a2a201eb05e30f45c27a20f6ec9721dd2fc13346738ee29832', 6, 1, 'user token', '[]', 0, '2026-08-17 22:59:40', '2026-08-17 22:59:40', '2027-08-17 22:59:40'),
('1a0ea3983cc150766f985b788af3783b8bdbea9def042464fb63f48b87e787e0e4447b816d5050e3', 6, 1, 'user token', '[]', 0, '2026-09-12 00:02:31', '2026-09-12 00:02:31', '2027-09-12 00:02:31'),
('22ef9a15ca3955bd2b1df0f603af8424af09c404f4277ed2af4956fb1daaf570ca047b2f1107f792', 6, 1, 'user token', '[]', 0, '2026-09-12 00:07:50', '2026-09-12 00:07:50', '2027-09-12 00:07:50'),
('2c5c07b6e625eeb0808b5366637aa47822da4f3510331e9de2bd614a6d61f97c85c9fda9e9d0d099', 19, 1, 'user token', '[]', 0, '2026-09-22 22:19:03', '2026-09-22 22:19:03', '2027-09-22 22:19:03'),
('2fe1b87e5d3876993991300d3bb224732daa094d8738582de7c89b78dd6353d76e97d8b1e00c0b29', 13, 1, 'user token', '[]', 0, '2026-08-29 19:42:40', '2026-08-29 19:42:40', '2027-08-29 19:42:40'),
('3fdfd94497a88fd74380cd6c403a35739bfe9a333fbcbcaabfa45e35dc16ac5766542b7063168aba', 6, 1, 'user token', '[]', 0, '2026-09-15 15:51:50', '2026-09-15 15:51:50', '2027-09-15 15:51:50'),
('496a03565eb50e80dc8375c6fa588e7ea89c3579090fc0cae9ccdb618737aa343114fd3787cbb87c', 14, 1, 'user token', '[]', 0, '2026-09-15 17:44:00', '2026-09-15 17:44:00', '2027-09-15 17:44:00'),
('5207ff9e95a745c6b3a644f773932df94a191bb96ce66b4d12347fe6f7fa1d674fd2383c2f08fbf3', 6, 1, 'user token', '[]', 0, '2026-08-30 04:04:24', '2026-08-30 04:04:24', '2027-08-30 04:04:24'),
('60f6adea595e7f221274d989564b791ee8122b94bc2868e50e8328b512cb5babd10179c7313bf893', 6, 1, 'user token', '[]', 0, '2026-09-12 00:29:18', '2026-09-12 00:29:18', '2027-09-12 00:29:18'),
('6a99c96bb6094569474977d5dc000ba0da7143275e54942d3519968f97e27c2959b08d277d8ef167', 6, 1, 'user token', '[]', 0, '2026-08-17 03:22:56', '2026-08-17 03:22:56', '2027-08-17 03:22:56'),
('80a0a92282cd3166fece50c55e90523f88594e6e6235d2b813d0e7aa5f13f3d7fc2bbdd8cd8ed468', 15, 1, 'user token', '[]', 0, '2026-09-18 18:25:52', '2026-09-18 18:25:52', '2027-09-18 18:25:52'),
('953e8ce3c0ff0615b2e1f2a0ce7660ef167f916f88d7a74fa63732b79c7409659da0e3056bf75b41', 6, 1, 'user token', '[]', 0, '2026-08-30 04:26:18', '2026-08-30 04:26:18', '2027-08-30 04:26:18'),
('a03cea661dc37c803e434e83227c3f9304a7c233dc353020afd6ac574d7c720e08eeeede2372a777', 6, 1, 'user token', '[]', 0, '2026-08-17 05:29:01', '2026-08-17 05:29:01', '2027-08-17 05:29:01'),
('a0643884579e77f5da4d0df4bbfb502cbce4c4e3869d6435a4b38c5a1785540a553f2561e35f2616', 6, 1, 'user token', '[]', 0, '2026-08-30 17:25:49', '2026-08-30 17:25:49', '2027-08-30 17:25:49'),
('cee3a6ce8d4f782c8879bfb3d2b73128abdad82a7cc490764f42482c1fdf9304d9db649def0101d3', 6, 1, 'user token', '[]', 0, '2026-08-30 04:02:41', '2026-08-30 04:02:41', '2027-08-30 04:02:41'),
('ef55069d162a0579721f2d110fcafa85f2f972ccfe512b0d9775d59c2c7ddc1c216d2356dda2ccb6', 6, 1, 'user token', '[]', 0, '2026-08-30 04:07:32', '2026-08-30 04:07:32', '2027-08-30 04:07:32'),
('f8700aa1d77c0dc99e1c02d4a769808ae9615627a7c436c96644b578ba4809df5f006613289457f5', 6, 1, 'user token', '[]', 0, '2026-09-12 00:03:24', '2026-09-12 00:03:24', '2027-09-12 00:03:24');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Elite Cleaning Personal Access Client', 'l1Pk8TMxSNNZjXo4O6NjsmyGqUvgPY7fFs5RsTQn', NULL, 'http://localhost', 1, 0, 0, '2026-08-06 19:47:36', '2026-08-06 19:47:36'),
(2, NULL, 'Elite Cleaning Password Grant Client', '186FYzQjB0Vcv5c4nK1rNX3CejZyW7B7GdR16Ryf', 'users', 'http://localhost', 0, 1, 0, '2026-08-06 19:47:36', '2026-08-06 19:47:36');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-08-06 19:47:36', '2026-08-06 19:47:36');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_code` varchar(255) NOT NULL,
  `prefix` varchar(255) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `coupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount` double(8,2) DEFAULT NULL,
  `pick_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `pick_hour` varchar(255) DEFAULT NULL,
  `delivery_hour` varchar(255) DEFAULT NULL,
  `amount` double(8,2) NOT NULL,
  `total_amount` double(8,2) NOT NULL,
  `payment_status` enum('Pending','Paid','Unpaid') NOT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `address_id` bigint(20) UNSIGNED NOT NULL,
  `instruction` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_status` enum('Pending','Order confirmed','Picked your order','Processing','Cancelled','Delivered') NOT NULL,
  `is_show` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_charge` double(8,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_products`
--

CREATE TABLE `order_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` double(8,2) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_schedules`
--

CREATE TABLE `order_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `day` varchar(255) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `per_hour` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `type` enum('pickup','delivery') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `object` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `exp` date NOT NULL,
  `last_no` int(11) NOT NULL,
  `transaction` varchar(255) NOT NULL,
  `amount` double(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mode` varchar(255) NOT NULL DEFAULT 'test' COMMENT 'test or live',
  `alias` varchar(255) DEFAULT NULL COMMENT 'controller namespace',
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `shop_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(125) NOT NULL,
  `guard_name` varchar(125) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'order.edit', 'web', '2026-09-19 03:45:39', '2026-09-19 03:45:39'),
(2, 'order.edit.price', 'web', '2026-09-19 03:45:39', '2026-09-19 03:45:39'),
(3, 'order.delete', 'web', '2026-09-19 03:45:39', '2026-09-19 03:45:39'),
(4, 'order.payment-status', 'web', '2026-09-19 03:45:39', '2026-09-19 03:45:39'),
(5, 'service.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(6, 'service.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(7, 'service.status.toggle', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(8, 'additional.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(9, 'additional.create', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(10, 'additional.store', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(11, 'additional.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(12, 'additional.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(13, 'additional.status.toggle', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(14, 'variant.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(15, 'variant.create', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(16, 'variant.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(17, 'variant.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(18, 'variant.store', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(19, 'variant.products', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(20, 'notification.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(21, 'notification.send', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(22, 'customer.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(23, 'customer.show', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(24, 'customer.create', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(25, 'customer.store', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(26, 'customer.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(27, 'customer.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(28, 'product.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(29, 'product.create', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(30, 'product.store', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(31, 'product.show', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(32, 'product.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(33, 'product.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(34, 'product.status.toggle', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(35, 'banner.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(36, 'banner.promotional', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(37, 'banner.store', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(38, 'banner.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(39, 'banner.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(40, 'banner.destroy', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(41, 'banner.status.toggle', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(42, 'order.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(43, 'order.show', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(44, 'order.status.change', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(45, 'order.print.labels', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(46, 'order.print.invioce', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(47, 'orderIncomplete.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(48, 'orderIncomplete.paid', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(49, 'revenue.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(50, 'revenue.generate.pdf', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(51, 'report.generate.pdf', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(52, 'coupon.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(53, 'coupon.create', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(54, 'coupon.store', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(55, 'coupon.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(56, 'coupon.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(57, 'contact', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(58, 'driver.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(59, 'driver.create', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(60, 'driver.store', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(61, 'driverAssign', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(62, 'driver.details', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(63, 'profile.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(64, 'profile.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(65, 'profile.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(66, 'profile.change-password', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(67, 'schedule.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(68, 'toggole.status.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(69, 'schedule.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(70, 'dashboard.calculation', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(71, 'dashboard.revenue', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(72, 'dashboard.overview', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(73, 'setting.show', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(74, 'setting.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(75, 'setting.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(76, 'sms-gateway.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(77, 'sms-gateway.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(78, 'admin.index', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(79, 'admin.status-update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(80, 'admin.create', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(81, 'admin.store', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(82, 'admin.edit', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(83, 'admin.update', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(84, 'admin.show', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(85, 'admin.set-permission', 'web', '2026-08-06 19:46:13', '2026-08-06 19:46:13'),
(86, 'variant.delete', 'web', '2026-08-11 21:10:09', '2026-08-11 21:10:09'),
(87, 'service.delete', 'web', '2026-08-11 21:10:09', '2026-08-11 21:10:09'),
(88, 'product.delete', 'web', '2026-08-11 21:10:09', '2026-08-11 21:10:09'),
(93, 'order.update', 'web', '2026-08-20 21:23:39', '2026-08-20 21:23:39'),
(94, 'service.index', 'web', '2026-09-21 07:38:49', '2026-09-21 07:38:49'),
(95, 'service.create', 'web', '2026-09-21 07:38:49', '2026-09-21 07:38:49'),
(96, 'service.store', 'web', '2026-09-21 07:38:49', '2026-09-21 07:38:49');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `variant_id` bigint(20) UNSIGNED NOT NULL,
  `thumbnail_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `discount_price` double(8,2) DEFAULT NULL,
  `price` double(8,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name_bn` varchar(255) DEFAULT NULL,
  `order` bigint(20) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `content` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(125) NOT NULL,
  `guard_name` varchar(125) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'root', 'web', NULL, NULL),
(2, 'admin', 'web', NULL, NULL),
(3, 'customer', 'web', NULL, NULL),
(4, 'visitor', 'web', NULL, NULL),
(5, 'driver', 'web', NULL, NULL),
(6, 'vendor_admin', 'web', '2026-09-21 06:50:18', '2026-09-21 06:50:18');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
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
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(1, 2),
(1, 4),
(2, 4),
(3, 4),
(4, 4),
(5, 4),
(6, 4),
(7, 4),
(8, 4),
(9, 4),
(10, 4),
(11, 4),
(12, 4),
(14, 4),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(19, 4),
(20, 4),
(21, 4),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4),
(27, 4),
(28, 4),
(29, 4),
(30, 4),
(31, 4),
(32, 4),
(33, 4),
(35, 4),
(36, 4),
(37, 4),
(38, 4),
(39, 4),
(40, 4),
(42, 4),
(43, 4),
(44, 4),
(45, 4),
(46, 4),
(47, 4),
(49, 4),
(50, 4),
(51, 4),
(52, 4),
(53, 4),
(54, 4),
(55, 4),
(56, 4),
(57, 4),
(58, 4),
(59, 4),
(60, 4),
(61, 4),
(62, 4),
(63, 4),
(64, 4),
(65, 4),
(66, 4),
(67, 4),
(69, 4),
(70, 4),
(71, 4),
(72, 4),
(73, 4),
(74, 4),
(75, 4),
(76, 4),
(77, 4),
(78, 4),
(80, 4),
(82, 4),
(84, 4),
(89, 4),
(90, 4),
(92, 4),
(93, 4),
(1, 6),
(5, 6),
(6, 6),
(7, 6),
(8, 6),
(9, 6),
(10, 6),
(11, 6),
(12, 6),
(13, 6),
(14, 6),
(15, 6),
(16, 6),
(17, 6),
(18, 6),
(19, 6),
(22, 6),
(23, 6),
(28, 6),
(29, 6),
(30, 6),
(31, 6),
(32, 6),
(33, 6),
(34, 6),
(42, 6),
(43, 6),
(44, 6),
(49, 6),
(52, 6),
(53, 6),
(54, 6),
(55, 6),
(56, 6),
(58, 6),
(59, 6),
(60, 6),
(62, 6),
(70, 6),
(71, 6),
(72, 6),
(86, 6),
(87, 6),
(88, 6),
(93, 6),
(94, 6),
(95, 6),
(96, 6);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `name_bn` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `description_bn` longtext DEFAULT NULL,
  `thumbnail_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `vendor_id`, `name`, `name_bn`, `description`, `description_bn`, `thumbnail_id`, `is_active`, `created_at`, `updated_at`) VALUES
(9, NULL, 'غسيل عادي', 'غسيل', 'خدمة غسيل', 'خدمة', NULL, 1, '2026-09-21 05:57:55', '2026-09-21 05:57:55'),
(10, NULL, 'كوي', NULL, 'خدمة كوي', NULL, NULL, 1, '2026-09-21 05:57:55', '2026-09-21 05:57:55');

-- --------------------------------------------------------

--
-- Table structure for table `service_variants`
--

CREATE TABLE `service_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `variant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `title`, `slug`, `content`, `created_at`, `updated_at`) VALUES
(1, 'Privacy Policy', 'privacy-policy', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2026-08-06 19:47:16', '2026-08-06 19:47:16'),
(2, 'Terms of Service', 'trams-of-service', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2026-08-06 19:47:16', '2026-08-06 19:47:16'),
(3, 'Contact us', 'contact-us', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2026-08-06 19:47:16', '2026-08-06 19:47:16'),
(4, 'About Us', 'about-us', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2026-08-06 19:47:16', '2026-08-06 19:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stripe_keys`
--

CREATE TABLE `stripe_keys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `price` double(8,2) NOT NULL,
  `validity` int(11) NOT NULL,
  `validity_type` varchar(255) NOT NULL,
  `clothe` int(11) NOT NULL,
  `delivery` int(11) NOT NULL,
  `towel` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT 0,
  `amount` double(8,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) NOT NULL DEFAULT 'cash',
  `transaction_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `mobile_verified_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `profile_photo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gender` enum('Male','Female','Others') DEFAULT NULL,
  `alternative_phone` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `driving_lience` varchar(255) DEFAULT NULL,
  `date_of_birth` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `mobile`, `email`, `email_verified_at`, `mobile_verified_at`, `is_active`, `password`, `profile_photo_id`, `gender`, `alternative_phone`, `remember_token`, `created_at`, `updated_at`, `driving_lience`, `date_of_birth`) VALUES
(16, 'Admin', 'Mobile', '0592925567', '0592925567@admin.iyad', '2026-09-20 07:42:53', '2026-09-20 07:42:53', 1, '$2y$10$p9WwU4/l1UNfIewwOi9rgeMIIHZ5yHjv9dcEFpm9PvTcTWiZ4Tg8e', NULL, NULL, NULL, NULL, '2026-09-20 07:42:53', '2026-09-20 07:42:53', NULL, NULL),
(17, 'قيس عمرو', 'Vendor', '0595373460', '0595373460@vendor.local', '2026-09-21 06:40:23', '2026-09-21 06:40:23', 1, '$2y$10$CJD3rJqJnQFT204HttxTPeAgKjfQ6s44Rd2hGo/xt3gnGXII2bYka', NULL, NULL, NULL, NULL, '2026-09-21 06:40:23', '2026-09-21 06:40:23', NULL, NULL),
(18, 'قيس عمرو', 'Vendor', '0595373462', '0595373462@vendor.local', '2026-09-21 06:56:55', '2026-09-21 06:56:55', 1, '$2y$10$/7uMkB67Qv9xzjg/dGZQUe39KMwmVSXDJpzfYJrFQSW4LLEQaoSmm', NULL, NULL, NULL, NULL, '2026-09-21 06:56:55', '2026-09-21 06:56:55', NULL, NULL),
(19, 'hammam', NULL, '0568063667', NULL, NULL, '2026-09-22 22:19:03', 1, '$2y$10$/dUYqETKnNq0HwFNEkkzuOYlZmdZgzDpACPhh7cinvEgqw6Rn.Im6', NULL, NULL, NULL, NULL, '2026-09-22 22:19:02', '2026-09-22 22:19:03', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_has_subscriptions`
--

CREATE TABLE `user_has_subscriptions` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `expired_date` varchar(255) NOT NULL,
  `clothe` int(11) NOT NULL,
  `delivery` int(11) NOT NULL,
  `towel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variants`
--

CREATE TABLE `variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `name_bn` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo_media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Owner user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `slug`, `address`, `phone`, `email`, `logo_media_id`, `user_id`, `is_active`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(2, 'قيس عمرو', 'kys-aamro-6ab080d7132fe', 'دورا الخليل', '0595373462', '0595373462@vendor.local', 27, 18, 1, NULL, NULL, '2026-09-21 06:56:55', '2026-09-21 07:03:56');

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verification_codes`
--

INSERT INTO `verification_codes` (`id`, `contact`, `otp`, `token`, `created_at`, `updated_at`) VALUES
(1, 'playstore.review@elitecleanps.com', '7873', '67756ea3217a76161bfb5e6a76bcb60857eeaab9d87f36d08e983ad1124b6a41', '2026-08-30 04:04:03', '2026-08-30 04:04:03'),
(2, '0595373460', '7569', 'd87cd4293ad8b04541416c34838a2d4fe51bb622b441fab0b10ee8fb7f9dedc1', '2026-09-15 17:44:00', '2026-09-15 17:44:00'),
(3, '0595373463', '5019', '9e114f398df76743d6b79c5d9ac351a4bff755afa36e404f66bedac6e40c08f6', '2026-09-18 18:25:52', '2026-09-18 18:25:52'),
(4, '0568063667', '2981', '67b5ca7777c782f62172362c23d45bf7f47b5370dca13c704d2babd6cf6ace07', '2026-09-22 22:19:02', '2026-09-22 22:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE `website_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`value`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_settings`
--

CREATE TABLE `web_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `fav_icon` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `signature_id` bigint(20) UNSIGNED DEFAULT NULL,
  `currency_name` varchar(255) DEFAULT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `web_settings`
--

INSERT INTO `web_settings` (`id`, `name`, `title`, `logo`, `fav_icon`, `city`, `road`, `area`, `mobile`, `currency`, `created_at`, `updated_at`, `address`, `signature_id`, `currency_name`, `tax_rate`) VALUES
(1, 'Elite Cleaning', 'Elite Cleaning', 'logo.png', NULL, 'HEBRON - DURA', NULL, NULL, '0594045302', '₪', '2026-08-06 19:51:56', '2026-08-17 22:26:18', 'HEBRON - DURA', NULL, 'شيكل', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `additionals`
--
ALTER TABLE `additionals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `additional_orders`
--
ALTER TABLE `additional_orders`
  ADD KEY `additional_orders_order_id_foreign` (`order_id`),
  ADD KEY `additional_orders_additional_id_foreign` (`additional_id`);

--
-- Indexes for table `additional_services`
--
ALTER TABLE `additional_services`
  ADD KEY `additional_services_service_id_foreign` (`service_id`),
  ADD KEY `additional_services_additional_id_foreign` (`additional_id`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `admin_device_keys`
--
ALTER TABLE `admin_device_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_device_keys_user_id_foreign` (`user_id`);

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banners_thumbnail_id_foreign` (`thumbnail_id`),
  ADD KEY `banners_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `card_infos`
--
ALTER TABLE `card_infos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `card_infos_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupons_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `coupon_users`
--
ALTER TABLE `coupon_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_users_coupon_id_foreign` (`coupon_id`),
  ADD KEY `coupon_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_user_id_foreign` (`user_id`);

--
-- Indexes for table `delivery_costs`
--
ALTER TABLE `delivery_costs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_keys`
--
ALTER TABLE `device_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_keys_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drivers_user_id_foreign` (`user_id`);

--
-- Indexes for table `driver_device_keys`
--
ALTER TABLE `driver_device_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_device_keys_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `driver_histories`
--
ALTER TABLE `driver_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_histories_driver_id_foreign` (`driver_id`),
  ADD KEY `driver_histories_order_id_foreign` (`order_id`);

--
-- Indexes for table `driver_notifications`
--
ALTER TABLE `driver_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_notifications_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `driver_orders`
--
ALTER TABLE `driver_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_orders_order_id_foreign` (`order_id`),
  ADD KEY `driver_orders_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faqs_slug_unique` (`slug`),
  ADD KEY `faqs_faq_category_id_foreign` (`faq_category_id`);

--
-- Indexes for table `faq_categories`
--
ALTER TABLE `faq_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faq_categories_slug_unique` (`slug`);

--
-- Indexes for table `invoice_manages`
--
ALTER TABLE `invoice_manages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `languages_thumbnail_id_foreign` (`thumbnail_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mobile_app_urls`
--
ALTER TABLE `mobile_app_urls`
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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `notification_manages`
--
ALTER TABLE `notification_manages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`),
  ADD KEY `orders_coupon_id_foreign` (`coupon_id`),
  ADD KEY `orders_address_id_foreign` (`address_id`),
  ADD KEY `orders_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `order_products`
--
ALTER TABLE `order_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_products_order_id_foreign` (`order_id`),
  ADD KEY `order_products_product_id_foreign` (`product_id`);

--
-- Indexes for table `order_schedules`
--
ALTER TABLE `order_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Indexes for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_gateways_media_id_foreign` (`media_id`);

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
  ADD KEY `products_service_id_foreign` (`service_id`),
  ADD KEY `products_variant_id_foreign` (`variant_id`),
  ADD KEY `products_thumbnail_id_foreign` (`thumbnail_id`),
  ADD KEY `products_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ratings_order_id_foreign` (`order_id`),
  ADD KEY `ratings_customer_id_foreign` (`customer_id`);

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
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_thumbnail_id_foreign` (`thumbnail_id`),
  ADD KEY `services_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `service_variants`
--
ALTER TABLE `service_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_variants_service_id_foreign` (`service_id`),
  ADD KEY `service_variants_variant_id_foreign` (`variant_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `social_links_media_id_foreign` (`media_id`);

--
-- Indexes for table `stripe_keys`
--
ALTER TABLE `stripe_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_customer_id_foreign` (`customer_id`),
  ADD KEY `transactions_order_id_foreign` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_mobile_unique` (`mobile`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_profile_photo_id_foreign` (`profile_photo_id`);

--
-- Indexes for table `user_has_subscriptions`
--
ALTER TABLE `user_has_subscriptions`
  ADD KEY `user_has_subscriptions_user_id_foreign` (`user_id`),
  ADD KEY `user_has_subscriptions_subscription_id_foreign` (`subscription_id`);

--
-- Indexes for table `variants`
--
ALTER TABLE `variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_slug_unique` (`slug`),
  ADD KEY `vendors_logo_media_id_foreign` (`logo_media_id`),
  ADD KEY `vendors_user_id_foreign` (`user_id`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `verification_codes_otp_unique` (`otp`),
  ADD UNIQUE KEY `verification_codes_token_unique` (`token`);

--
-- Indexes for table `website_settings`
--
ALTER TABLE `website_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `website_settings_key_unique` (`key`);

--
-- Indexes for table `web_settings`
--
ALTER TABLE `web_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `additionals`
--
ALTER TABLE `additionals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_device_keys`
--
ALTER TABLE `admin_device_keys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `card_infos`
--
ALTER TABLE `card_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coupon_users`
--
ALTER TABLE `coupon_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `delivery_costs`
--
ALTER TABLE `delivery_costs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_keys`
--
ALTER TABLE `device_keys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `driver_device_keys`
--
ALTER TABLE `driver_device_keys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_histories`
--
ALTER TABLE `driver_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_notifications`
--
ALTER TABLE `driver_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_orders`
--
ALTER TABLE `driver_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faq_categories`
--
ALTER TABLE `faq_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_manages`
--
ALTER TABLE `invoice_manages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `mobile_app_urls`
--
ALTER TABLE `mobile_app_urls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notification_manages`
--
ALTER TABLE `notification_manages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_products`
--
ALTER TABLE `order_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_schedules`
--
ALTER TABLE `order_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `service_variants`
--
ALTER TABLE `service_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stripe_keys`
--
ALTER TABLE `stripe_keys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `variants`
--
ALTER TABLE `variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `website_settings`
--
ALTER TABLE `website_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_settings`
--
ALTER TABLE `web_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `additional_orders`
--
ALTER TABLE `additional_orders`
  ADD CONSTRAINT `additional_orders_additional_id_foreign` FOREIGN KEY (`additional_id`) REFERENCES `additionals` (`id`),
  ADD CONSTRAINT `additional_orders_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `additional_services`
--
ALTER TABLE `additional_services`
  ADD CONSTRAINT `additional_services_additional_id_foreign` FOREIGN KEY (`additional_id`) REFERENCES `additionals` (`id`),
  ADD CONSTRAINT `additional_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `admin_device_keys`
--
ALTER TABLE `admin_device_keys`
  ADD CONSTRAINT `admin_device_keys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `banners`
--
ALTER TABLE `banners`
  ADD CONSTRAINT `banners_thumbnail_id_foreign` FOREIGN KEY (`thumbnail_id`) REFERENCES `media` (`id`),
  ADD CONSTRAINT `banners_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `card_infos`
--
ALTER TABLE `card_infos`
  ADD CONSTRAINT `card_infos_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `coupon_users`
--
ALTER TABLE `coupon_users`
  ADD CONSTRAINT `coupon_users_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`),
  ADD CONSTRAINT `coupon_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `device_keys`
--
ALTER TABLE `device_keys`
  ADD CONSTRAINT `device_keys_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `driver_device_keys`
--
ALTER TABLE `driver_device_keys`
  ADD CONSTRAINT `driver_device_keys_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`);

--
-- Constraints for table `driver_histories`
--
ALTER TABLE `driver_histories`
  ADD CONSTRAINT `driver_histories_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`),
  ADD CONSTRAINT `driver_histories_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `driver_notifications`
--
ALTER TABLE `driver_notifications`
  ADD CONSTRAINT `driver_notifications_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`);

--
-- Constraints for table `driver_orders`
--
ALTER TABLE `driver_orders`
  ADD CONSTRAINT `driver_orders_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`),
  ADD CONSTRAINT `driver_orders_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `faqs_faq_category_id_foreign` FOREIGN KEY (`faq_category_id`) REFERENCES `faq_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `languages`
--
ALTER TABLE `languages`
  ADD CONSTRAINT `languages_thumbnail_id_foreign` FOREIGN KEY (`thumbnail_id`) REFERENCES `media` (`id`);

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
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`),
  ADD CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`),
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_products`
--
ALTER TABLE `order_products`
  ADD CONSTRAINT `order_products_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD CONSTRAINT `payment_gateways_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `products_thumbnail_id_foreign` FOREIGN KEY (`thumbnail_id`) REFERENCES `media` (`id`),
  ADD CONSTRAINT `products_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `variants` (`id`),
  ADD CONSTRAINT `products_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `ratings_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_thumbnail_id_foreign` FOREIGN KEY (`thumbnail_id`) REFERENCES `media` (`id`),
  ADD CONSTRAINT `services_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_variants`
--
ALTER TABLE `service_variants`
  ADD CONSTRAINT `service_variants_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `service_variants_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `variants` (`id`);

--
-- Constraints for table `social_links`
--
ALTER TABLE `social_links`
  ADD CONSTRAINT `social_links_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_profile_photo_id_foreign` FOREIGN KEY (`profile_photo_id`) REFERENCES `media` (`id`);

--
-- Constraints for table `user_has_subscriptions`
--
ALTER TABLE `user_has_subscriptions`
  ADD CONSTRAINT `user_has_subscriptions_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_has_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_logo_media_id_foreign` FOREIGN KEY (`logo_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
