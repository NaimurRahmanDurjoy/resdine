-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 04, 2026 at 09:28 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `resdine`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `drop_columns_if_exist` ()   BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE t_name VARCHAR(255);
  DECLARE c_name VARCHAR(255);
  DECLARE cur CURSOR FOR
    SELECT TABLE_NAME, COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'resdine'
      AND COLUMN_NAME IN ('created_by_type', 'updated_by_type', 'deleted_by_type');
      
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  
  OPEN cur;
  
  read_loop: LOOP
    FETCH cur INTO t_name, c_name;
    IF done THEN
      LEAVE read_loop;
    END IF;
    SET @s = CONCAT('ALTER TABLE `', t_name, '` DROP COLUMN `', c_name, '`;');
    PREPARE stmt FROM @s;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END LOOP;
  
  CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `payload`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'POST', 'login', '{\"email\": \"admin@resdine.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 00:33:45', '2026-03-16 00:33:45'),
(2, 1, 'POST', 'system-config', '{\"structure\": [{\"id\": 1, \"order\": 1, \"children\": []}, {\"id\": 2, \"order\": 2, \"children\": []}, {\"id\": 3, \"order\": 3, \"children\": []}, {\"id\": 5, \"order\": 4, \"children\": []}, {\"id\": 34, \"order\": 5, \"children\": []}, {\"id\": 6, \"order\": 6, \"children\": []}, {\"id\": 35, \"order\": 7, \"children\": []}, {\"id\": 7, \"order\": 8, \"children\": []}, {\"id\": 8, \"order\": 9, \"children\": []}, {\"id\": 9, \"order\": 10, \"children\": []}, {\"id\": 38, \"order\": 11, \"children\": []}, {\"id\": 10, \"order\": 12, \"children\": []}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 00:36:50', '2026-03-16 00:36:50'),
(3, 1, 'PUT', 'system-config', '{\"icon\": \"person\", \"name\": \"Customers\", \"order\": 1, \"route\": \"admin.customers.index\", \"is_active\": true, \"parent_id\": 35}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 00:37:52', '2026-03-16 00:37:52'),
(4, 1, 'PUT', 'system-config', '{\"icon\": \"card_membership\", \"name\": \"Membership Tiers\", \"order\": 2, \"route\": \"admin.memberships.index\", \"is_active\": true, \"parent_id\": 35}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 00:38:18', '2026-03-16 00:38:18'),
(5, 1, 'DELETE', 'system-config', '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 00:38:42', '2026-03-16 00:38:42'),
(6, 1, 'DELETE', 'system-config', '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 00:38:46', '2026-03-16 00:38:46'),
(7, 1, 'POST', 'login', '{\"email\": \"support@resdine.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 00:40:37', '2026-03-16 00:40:37'),
(8, 1, 'POST', 'login', '{\"email\": \"admin@resdine.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-01 05:45:36', '2026-04-01 05:45:36'),
(9, 1, 'POST', 'login', '{\"email\": \"admin@resdine.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 23:29:49', '2026-04-03 23:29:49'),
(10, 1, 'POST', 'system-config', '{\"structure\": [{\"id\": 1, \"order\": 1, \"children\": []}, {\"id\": 2, \"order\": 2, \"children\": []}, {\"id\": 3, \"order\": 3, \"children\": []}, {\"id\": 5, \"order\": 4, \"children\": []}, {\"id\": 34, \"order\": 5, \"children\": []}, {\"id\": 44, \"order\": 6, \"children\": []}, {\"id\": 6, \"order\": 7, \"children\": []}, {\"id\": 35, \"order\": 8, \"children\": []}, {\"id\": 9, \"order\": 9, \"children\": []}, {\"id\": 38, \"order\": 10, \"children\": []}, {\"id\": 10, \"order\": 11, \"children\": []}, {\"id\": 42, \"order\": 12, \"children\": []}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 23:32:10', '2026-04-03 23:32:10'),
(11, 1, 'PUT', 'system-config', '{\"icon\": \"business\", \"name\": \"Business Config\", \"order\": 10, \"route\": \"admin.settings.business-config.index\", \"is_active\": true, \"parent_id\": 10}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 01:00:13', '2026-04-04 01:00:13'),
(12, 1, 'PUT', 'system-config', '{\"icon\": \"table_restaurant\", \"name\": \"Table Bookings\", \"order\": 1, \"route\": \"admin.reservations.index\", \"is_active\": true, \"parent_id\": 38}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 01:02:53', '2026-04-04 01:02:53'),
(13, 1, 'PUT', 'system-config', '{\"icon\": \"event\", \"name\": \"Company Events\", \"order\": 2, \"route\": \"admin.events.index\", \"is_active\": true, \"parent_id\": 38}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 01:03:15', '2026-04-04 01:03:15'),
(14, 1, 'POST', 'system-config', '{\"icon\": \"query_stats\", \"name\": \"Reports & Analytics\", \"order\": 0, \"route\": null, \"is_active\": true, \"parent_id\": null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 01:06:15', '2026-04-04 01:06:15'),
(15, 1, 'POST', 'system-config', '{\"icon\": \"finance_mode\", \"name\": \"Reports\", \"order\": 0, \"route\": \"admin.reports.index\", \"is_active\": true, \"parent_id\": 45}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 01:07:45', '2026-04-04 01:07:45'),
(16, 1, 'POST', 'system-config', '{\"structure\": [{\"id\": 1, \"order\": 1, \"children\": []}, {\"id\": 2, \"order\": 2, \"children\": []}, {\"id\": 3, \"order\": 3, \"children\": []}, {\"id\": 5, \"order\": 4, \"children\": []}, {\"id\": 34, \"order\": 5, \"children\": []}, {\"id\": 44, \"order\": 6, \"children\": []}, {\"id\": 6, \"order\": 7, \"children\": []}, {\"id\": 35, \"order\": 8, \"children\": []}, {\"id\": 38, \"order\": 9, \"children\": []}, {\"id\": 42, \"order\": 10, \"children\": []}, {\"id\": 45, \"order\": 11, \"children\": []}, {\"id\": 10, \"order\": 12, \"children\": []}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 01:22:34', '2026-04-04 01:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'developer',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `user_name`, `password`, `role`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 'Admin', 'admin@resdine.com', 'admin', '$2y$12$rk/bat02PtG.mHplU9ad4u40Qh3bDvMkBkyTcJsHKPEP/DC3Ht9d6', 'developer', NULL, NULL, NULL, '2025-10-15 02:44:40', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_menus`
--

CREATE TABLE `admin_menus` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_menus`
--

INSERT INTO `admin_menus` (`id`, `name`, `route`, `icon`, `parent_id`, `order`, `is_active`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 'Dashboard', 'devAdmin.dashboard', 'dashboard', NULL, 2, 1, '2025-10-30 09:37:12', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(2, 'User Management', NULL, 'group', NULL, 3, 1, '2025-10-30 09:37:12', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(3, 'System Tools', NULL, 'build', NULL, 4, 1, '2025-10-30 09:37:12', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(4, 'System Config', NULL, 'tune', NULL, 5, 1, '2025-10-30 09:37:12', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(5, 'Database', NULL, 'database', NULL, 6, 1, '2025-10-30 09:37:12', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(6, 'Settings', NULL, 'settings', NULL, 7, 1, '2025-10-30 09:37:12', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(7, 'All Users', 'devAdmin.users.index', 'list', 2, 1, 1, '2025-10-30 09:37:12', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(9, 'Logs', NULL, 'description', 3, 1, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(10, 'Queue Monitor', NULL, 'task_alt', 3, 2, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(11, 'Cache Management', NULL, 'bolt', 3, 3, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(12, 'Admin Panel', NULL, 'admin_panel_settings', 4, 1, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(13, 'Menu', 'devAdmin.systemConfig.adminPanel.menu.index', 'menu', 12, 1, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(14, 'Internal Link', 'devAdmin.systemConfig.adminPanel.internalLink', 'link', 12, 2, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(15, 'Menu Sorting', 'devAdmin.systemConfig.adminPanel.menuSorting', 'sort', 12, 3, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(16, 'Software', NULL, 'terminal', 4, 2, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(17, 'Menu', 'devAdmin.systemConfig.software.menu.index', 'menu', 16, 1, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(18, 'Internal Link', 'devAdmin.systemConfig.software.internalLink', 'link', 16, 2, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(19, 'Menu Sorting', 'devAdmin.systemConfig.software.menuSorting', 'sort', 16, 2, 1, '2025-10-30 09:37:13', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL),
(22, 'Test', NULL, 'labs', NULL, 1, 1, '2025-11-03 23:26:33', '2025-11-03 23:38:58', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu_access`
--

CREATE TABLE `admin_menu_access` (
  `id` bigint UNSIGNED NOT NULL,
  `menu_id` bigint UNSIGNED NOT NULL,
  `admin_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_menu_access`
--

INSERT INTO `admin_menu_access` (`id`, `menu_id`, `admin_id`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(3, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 2, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 4, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 5, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 6, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 7, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 10, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 11, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 12, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 13, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 14, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 15, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 16, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 17, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 18, 1, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `location`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 'Head Office', NULL, 1, '2025-09-22 09:49:46', '2025-09-22 09:49:46', NULL, NULL, NULL, NULL),
(2, 'Branch Office', NULL, 1, '2025-09-22 09:49:46', '2025-09-22 09:49:46', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `business_configs`
--

CREATE TABLE `business_configs` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('resdine-cache-admin_menu_4', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:12:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:1;s:4:\"name\";s:9:\"Dashboard\";s:5:\"route\";s:15:\"admin.dashboard\";s:4:\"icon\";s:9:\"dashboard\";s:9:\"parent_id\";N;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:1;s:4:\"name\";s:9:\"Dashboard\";s:5:\"route\";s:15:\"admin.dashboard\";s:4:\"icon\";s:9:\"dashboard\";s:9:\"parent_id\";N;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:2;s:4:\"name\";s:5:\"Users\";s:5:\"route\";N;s:4:\"icon\";s:5:\"group\";s:9:\"parent_id\";N;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:2;s:4:\"name\";s:5:\"Users\";s:5:\"route\";N;s:4:\"icon\";s:5:\"group\";s:9:\"parent_id\";N;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:11;s:4:\"name\";s:12:\"Manage Users\";s:5:\"route\";s:17:\"admin.users.index\";s:4:\"icon\";s:15:\"manage_accounts\";s:9:\"parent_id\";i:2;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:11;s:4:\"name\";s:12:\"Manage Users\";s:5:\"route\";s:17:\"admin.users.index\";s:4:\"icon\";s:15:\"manage_accounts\";s:9:\"parent_id\";i:2;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:2;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:3;s:4:\"name\";s:15:\"Menu Management\";s:5:\"route\";N;s:4:\"icon\";s:15:\"restaurant_menu\";s:9:\"parent_id\";N;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:3;s:4:\"name\";s:15:\"Menu Management\";s:5:\"route\";N;s:4:\"icon\";s:15:\"restaurant_menu\";s:9:\"parent_id\";N;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:13;s:4:\"name\";s:10:\"Categories\";s:5:\"route\";s:30:\"admin.product.categories.index\";s:4:\"icon\";s:8:\"category\";s:9:\"parent_id\";i:3;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:13;s:4:\"name\";s:10:\"Categories\";s:5:\"route\";s:30:\"admin.product.categories.index\";s:4:\"icon\";s:8:\"category\";s:9:\"parent_id\";i:3;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:14;s:4:\"name\";s:5:\"Items\";s:5:\"route\";s:25:\"admin.product.items.index\";s:4:\"icon\";s:10:\"restaurant\";s:9:\"parent_id\";i:3;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:14;s:4:\"name\";s:5:\"Items\";s:5:\"route\";s:25:\"admin.product.items.index\";s:4:\"icon\";s:10:\"restaurant\";s:9:\"parent_id\";i:3;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:2;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:15;s:4:\"name\";s:8:\"Variants\";s:5:\"route\";s:28:\"admin.product.variants.index\";s:4:\"icon\";s:8:\"list_alt\";s:9:\"parent_id\";i:3;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:15;s:4:\"name\";s:8:\"Variants\";s:5:\"route\";s:28:\"admin.product.variants.index\";s:4:\"icon\";s:8:\"list_alt\";s:9:\"parent_id\";i:3;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:3;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:5;s:4:\"name\";s:9:\"Inventory\";s:5:\"route\";N;s:4:\"icon\";s:13:\"shopping_cart\";s:9:\"parent_id\";N;s:5:\"order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:5;s:4:\"name\";s:9:\"Inventory\";s:5:\"route\";N;s:4:\"icon\";s:13:\"shopping_cart\";s:9:\"parent_id\";N;s:5:\"order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:30;s:4:\"name\";s:5:\"Units\";s:5:\"route\";s:16:\"admin.unit.index\";s:4:\"icon\";s:7:\"balance\";s:9:\"parent_id\";i:5;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 05:08:43\";s:10:\"updated_at\";s:19:\"2026-03-11 06:34:16\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:30;s:4:\"name\";s:5:\"Units\";s:5:\"route\";s:16:\"admin.unit.index\";s:4:\"icon\";s:7:\"balance\";s:9:\"parent_id\";i:5;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 05:08:43\";s:10:\"updated_at\";s:19:\"2026-03-11 06:34:16\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:31;s:4:\"name\";s:11:\"Ingredients\";s:5:\"route\";s:23:\"admin.ingredients.index\";s:4:\"icon\";s:7:\"grocery\";s:9:\"parent_id\";i:5;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 05:10:53\";s:10:\"updated_at\";s:19:\"2026-03-11 06:34:16\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:31;s:4:\"name\";s:11:\"Ingredients\";s:5:\"route\";s:23:\"admin.ingredients.index\";s:4:\"icon\";s:7:\"grocery\";s:9:\"parent_id\";i:5;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 05:10:53\";s:10:\"updated_at\";s:19:\"2026-03-11 06:34:16\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:2;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:33;s:4:\"name\";s:5:\"Stock\";s:5:\"route\";s:17:\"admin.stock.index\";s:4:\"icon\";s:9:\"inventory\";s:9:\"parent_id\";i:5;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 05:25:21\";s:10:\"updated_at\";s:19:\"2026-03-11 06:34:16\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:33;s:4:\"name\";s:5:\"Stock\";s:5:\"route\";s:17:\"admin.stock.index\";s:4:\"icon\";s:9:\"inventory\";s:9:\"parent_id\";i:5;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 05:25:21\";s:10:\"updated_at\";s:19:\"2026-03-11 06:34:16\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:4;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:34;s:4:\"name\";s:8:\"Purchase\";s:5:\"route\";N;s:4:\"icon\";s:17:\"add_shopping_cart\";s:9:\"parent_id\";N;s:5:\"order\";i:5;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 06:32:34\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:34;s:4:\"name\";s:8:\"Purchase\";s:5:\"route\";N;s:4:\"icon\";s:17:\"add_shopping_cart\";s:9:\"parent_id\";N;s:5:\"order\";i:5;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 06:32:34\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:32;s:4:\"name\";s:9:\"Suppliers\";s:5:\"route\";s:21:\"admin.suppliers.index\";s:4:\"icon\";s:21:\"deployed_code_account\";s:9:\"parent_id\";i:34;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 05:23:40\";s:10:\"updated_at\";s:19:\"2026-03-11 06:35:17\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:32;s:4:\"name\";s:9:\"Suppliers\";s:5:\"route\";s:21:\"admin.suppliers.index\";s:4:\"icon\";s:21:\"deployed_code_account\";s:9:\"parent_id\";i:34;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-11 05:23:40\";s:10:\"updated_at\";s:19:\"2026-03-11 06:35:17\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:17;s:4:\"name\";s:13:\"All Purchases\";s:5:\"route\";s:20:\"admin.purchase.index\";s:4:\"icon\";s:12:\"shopping_bag\";s:9:\"parent_id\";i:34;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-03-11 06:35:17\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:17;s:4:\"name\";s:13:\"All Purchases\";s:5:\"route\";s:20:\"admin.purchase.index\";s:4:\"icon\";s:12:\"shopping_bag\";s:9:\"parent_id\";i:34;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-03-11 06:35:17\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:5;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:44;s:4:\"name\";s:13:\"Point of Sale\";s:5:\"route\";s:15:\"admin.pos.index\";s:4:\"icon\";s:13:\"point_of_sale\";s:9:\"parent_id\";N;s:5:\"order\";i:6;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 05:25:29\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:44;s:4:\"name\";s:13:\"Point of Sale\";s:5:\"route\";s:15:\"admin.pos.index\";s:4:\"icon\";s:13:\"point_of_sale\";s:9:\"parent_id\";N;s:5:\"order\";i:6;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 05:25:29\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:6;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:6;s:4:\"name\";s:6:\"Orders\";s:5:\"route\";s:18:\"admin.orders.index\";s:4:\"icon\";s:12:\"receipt_long\";s:9:\"parent_id\";N;s:5:\"order\";i:7;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:6;s:4:\"name\";s:6:\"Orders\";s:5:\"route\";s:18:\"admin.orders.index\";s:4:\"icon\";s:12:\"receipt_long\";s:9:\"parent_id\";N;s:5:\"order\";i:7;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:18;s:4:\"name\";s:10:\"All Orders\";s:5:\"route\";s:18:\"admin.orders.index\";s:4:\"icon\";s:7:\"receipt\";s:9:\"parent_id\";i:6;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:18;s:4:\"name\";s:10:\"All Orders\";s:5:\"route\";s:18:\"admin.orders.index\";s:4:\"icon\";s:7:\"receipt\";s:9:\"parent_id\";i:6;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-11-03 10:50:04\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:7;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:35;s:4:\"name\";s:19:\"Customers & Loyalty\";s:5:\"route\";N;s:4:\"icon\";s:6:\"groups\";s:9:\"parent_id\";N;s:5:\"order\";i:8;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:35;s:4:\"name\";s:19:\"Customers & Loyalty\";s:5:\"route\";N;s:4:\"icon\";s:6:\"groups\";s:9:\"parent_id\";N;s:5:\"order\";i:8;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:36;s:4:\"name\";s:9:\"Customers\";s:5:\"route\";s:21:\"admin.customers.index\";s:4:\"icon\";s:6:\"person\";s:9:\"parent_id\";i:35;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-03-16 06:37:52\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:36;s:4:\"name\";s:9:\"Customers\";s:5:\"route\";s:21:\"admin.customers.index\";s:4:\"icon\";s:6:\"person\";s:9:\"parent_id\";i:35;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-03-16 06:37:52\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:37;s:4:\"name\";s:16:\"Membership Tiers\";s:5:\"route\";s:23:\"admin.memberships.index\";s:4:\"icon\";s:15:\"card_membership\";s:9:\"parent_id\";i:35;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-03-16 06:38:18\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:37;s:4:\"name\";s:16:\"Membership Tiers\";s:5:\"route\";s:23:\"admin.memberships.index\";s:4:\"icon\";s:15:\"card_membership\";s:9:\"parent_id\";i:35;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-03-16 06:38:18\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:2;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:9;s:4:\"name\";s:10:\"Promotions\";s:5:\"route\";s:22:\"admin.promotions.index\";s:4:\"icon\";s:8:\"campaign\";s:9:\"parent_id\";i:35;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 05:32:10\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:9;s:4:\"name\";s:10:\"Promotions\";s:5:\"route\";s:22:\"admin.promotions.index\";s:4:\"icon\";s:8:\"campaign\";s:9:\"parent_id\";i:35;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 05:32:10\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:8;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:38;s:4:\"name\";s:12:\"Reservations\";s:5:\"route\";N;s:4:\"icon\";s:11:\"book_online\";s:9:\"parent_id\";N;s:5:\"order\";i:9;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:38;s:4:\"name\";s:12:\"Reservations\";s:5:\"route\";N;s:4:\"icon\";s:11:\"book_online\";s:9:\"parent_id\";N;s:5:\"order\";i:9;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:39;s:4:\"name\";s:14:\"Table Bookings\";s:5:\"route\";s:24:\"admin.reservations.index\";s:4:\"icon\";s:16:\"table_restaurant\";s:9:\"parent_id\";i:38;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:02:53\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:39;s:4:\"name\";s:14:\"Table Bookings\";s:5:\"route\";s:24:\"admin.reservations.index\";s:4:\"icon\";s:16:\"table_restaurant\";s:9:\"parent_id\";i:38;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:02:53\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:40;s:4:\"name\";s:14:\"Company Events\";s:5:\"route\";s:18:\"admin.events.index\";s:4:\"icon\";s:5:\"event\";s:9:\"parent_id\";i:38;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:03:15\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:40;s:4:\"name\";s:14:\"Company Events\";s:5:\"route\";s:18:\"admin.events.index\";s:4:\"icon\";s:5:\"event\";s:9:\"parent_id\";i:38;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:03:15\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:9;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:42;s:4:\"name\";s:10:\"Operations\";s:5:\"route\";N;s:4:\"icon\";s:21:\"settings_applications\";s:9:\"parent_id\";N;s:5:\"order\";i:10;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 05:24:31\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:42;s:4:\"name\";s:10:\"Operations\";s:5:\"route\";N;s:4:\"icon\";s:21:\"settings_applications\";s:9:\"parent_id\";N;s:5:\"order\";i:10;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 05:24:31\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:43;s:4:\"name\";s:21:\"Kitchen Display (KDS)\";s:5:\"route\";s:15:\"admin.kds.index\";s:4:\"icon\";s:10:\"restaurant\";s:9:\"parent_id\";i:42;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 05:24:32\";s:10:\"updated_at\";s:19:\"2026-04-04 05:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:43;s:4:\"name\";s:21:\"Kitchen Display (KDS)\";s:5:\"route\";s:15:\"admin.kds.index\";s:4:\"icon\";s:10:\"restaurant\";s:9:\"parent_id\";i:42;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 05:24:32\";s:10:\"updated_at\";s:19:\"2026-04-04 05:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:10;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:45;s:4:\"name\";s:19:\"Reports & Analytics\";s:5:\"route\";N;s:4:\"icon\";s:11:\"query_stats\";s:9:\"parent_id\";N;s:5:\"order\";i:11;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 07:06:15\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";i:1;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:45;s:4:\"name\";s:19:\"Reports & Analytics\";s:5:\"route\";N;s:4:\"icon\";s:11:\"query_stats\";s:9:\"parent_id\";N;s:5:\"order\";i:11;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 07:06:15\";s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";i:1;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:46;s:4:\"name\";s:7:\"Reports\";s:5:\"route\";s:19:\"admin.reports.index\";s:4:\"icon\";s:12:\"finance_mode\";s:9:\"parent_id\";i:45;s:5:\"order\";i:0;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 07:07:45\";s:10:\"updated_at\";s:19:\"2026-04-04 07:07:45\";s:10:\"created_by\";i:1;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:46;s:4:\"name\";s:7:\"Reports\";s:5:\"route\";s:19:\"admin.reports.index\";s:4:\"icon\";s:12:\"finance_mode\";s:9:\"parent_id\";i:45;s:5:\"order\";i:0;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-04-04 07:07:45\";s:10:\"updated_at\";s:19:\"2026-04-04 07:07:45\";s:10:\"created_by\";i:1;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:11;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:10;s:4:\"name\";s:8:\"Settings\";s:5:\"route\";s:20:\"admin.settings.index\";s:4:\"icon\";s:8:\"settings\";s:9:\"parent_id\";N;s:5:\"order\";i:12;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:10;s:4:\"name\";s:8:\"Settings\";s:5:\"route\";s:20:\"admin.settings.index\";s:4:\"icon\";s:8:\"settings\";s:9:\"parent_id\";N;s:5:\"order\";i:12;s:9:\"is_active\";i:1;s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2026-04-04 07:22:34\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:25;s:4:\"name\";s:16:\"Restaurant Setup\";s:5:\"route\";N;s:4:\"icon\";s:16:\"settings_suggest\";s:9:\"parent_id\";i:10;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:25;s:4:\"name\";s:16:\"Restaurant Setup\";s:5:\"route\";N;s:4:\"icon\";s:16:\"settings_suggest\";s:9:\"parent_id\";i:10;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:26;s:4:\"name\";s:8:\"Branches\";s:5:\"route\";s:46:\"admin.settings.restaurant-setup.branches.index\";s:4:\"icon\";s:5:\"store\";s:9:\"parent_id\";i:25;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:26;s:4:\"name\";s:8:\"Branches\";s:5:\"route\";s:46:\"admin.settings.restaurant-setup.branches.index\";s:4:\"icon\";s:5:\"store\";s:9:\"parent_id\";i:25;s:5:\"order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:27;s:4:\"name\";s:16:\"Kitchen/Res Dept\";s:5:\"route\";s:53:\"admin.settings.restaurant-setup.res-departments.index\";s:4:\"icon\";s:7:\"kitchen\";s:9:\"parent_id\";i:25;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:27;s:4:\"name\";s:16:\"Kitchen/Res Dept\";s:5:\"route\";s:53:\"admin.settings.restaurant-setup.res-departments.index\";s:4:\"icon\";s:7:\"kitchen\";s:9:\"parent_id\";i:25;s:5:\"order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:2;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:28;s:4:\"name\";s:10:\"Staff Dept\";s:5:\"route\";s:55:\"admin.settings.restaurant-setup.staff-departments.index\";s:4:\"icon\";s:6:\"groups\";s:9:\"parent_id\";i:25;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:28;s:4:\"name\";s:10:\"Staff Dept\";s:5:\"route\";s:55:\"admin.settings.restaurant-setup.staff-departments.index\";s:4:\"icon\";s:6:\"groups\";s:9:\"parent_id\";i:25;s:5:\"order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:3;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:29;s:4:\"name\";s:17:\"Tables & Sections\";s:5:\"route\";s:44:\"admin.settings.restaurant-setup.tables.index\";s:4:\"icon\";s:16:\"table_restaurant\";s:9:\"parent_id\";i:25;s:5:\"order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:29;s:4:\"name\";s:17:\"Tables & Sections\";s:5:\"route\";s:44:\"admin.settings.restaurant-setup.tables.index\";s:4:\"icon\";s:16:\"table_restaurant\";s:9:\"parent_id\";i:25;s:5:\"order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"updated_at\";s:19:\"2026-03-08 09:24:32\";s:10:\"created_by\";N;s:10:\"updated_by\";N;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}i:1;O:23:\"App\\Models\\SoftwareMenu\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"software_menus\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:41;s:4:\"name\";s:15:\"Business Config\";s:5:\"route\";s:36:\"admin.settings.business-config.index\";s:4:\"icon\";s:8:\"business\";s:9:\"parent_id\";i:10;s:5:\"order\";i:10;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:00:13\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:41;s:4:\"name\";s:15:\"Business Config\";s:5:\"route\";s:36:\"admin.settings.business-config.index\";s:4:\"icon\";s:8:\"business\";s:9:\"parent_id\";i:10;s:5:\"order\";i:10;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-03-16 05:48:05\";s:10:\"updated_at\";s:19:\"2026-04-04 07:00:13\";s:10:\"created_by\";N;s:10:\"updated_by\";i:1;s:10:\"deleted_by\";N;s:10:\"deleted_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:5:\"route\";i:2;s:4:\"icon\";i:3;s:9:\"parent_id\";i:4;s:5:\"order\";i:5;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:0:{}s:16:\"\0*\0forceDeleting\";b:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1775297086, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `combo_item_details`
--

CREATE TABLE `combo_item_details` (
  `id` bigint UNSIGNED NOT NULL,
  `combo_id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `combo_item_details`
--

INSERT INTO `combo_item_details` (`id`, `combo_id`, `item_id`, `quantity`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(7, 5, 2, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 5, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_memberships`
--

CREATE TABLE `customer_memberships` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `membership_id` bigint UNSIGNED NOT NULL,
  `card_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `estimated_guests` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `admin_approval` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `min_stock` decimal(10,2) NOT NULL DEFAULT '0.00',
  `has_expiry` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=expiry item,0=non-expiry',
  `expiry_days` int DEFAULT NULL COMMENT 'shelf life in days',
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_master`
--

CREATE TABLE `invoice_master` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `promotion_id` bigint UNSIGNED DEFAULT NULL,
  `sub_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `collect_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1=pending,2=paid,3=cancelled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_points`
--

CREATE TABLE `loyalty_points` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `points_earned` decimal(10,2) NOT NULL DEFAULT '0.00',
  `points_redeemed` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `loyalty_multiplier` int NOT NULL DEFAULT '1',
  `min_points` int NOT NULL DEFAULT '0',
  `max_points` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '0001_01_01_000002_create_jobs_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '2025_09_17_000001_create_roles_and_permission_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '2025_09_17_000002_create_branches_and_departments_and_tables_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '2025_09_17_000002_create_users_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '2025_09_17_000003_create_menu_categories_and_units_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '2025_09_17_000004_create_menu_items_and_combo_item_details_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(8, '2025_09_17_000005_create_ingredients_and_menu_recipes_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(9, '2025_09_17_000006_create_supplier_purchase_master_and_purchase_details_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(10, '2025_09_17_000007_create_customers_membership_and_membership_relation_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(11, '2025_09_17_000008_create_promotions_and_loyality_points_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(12, '2025_09_17_000009_create_orders_master_and_order_details_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(13, '2025_09_17_000010_create_invoices_master_and_invoice_details_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(14, '2025_09_17_000011_create_stocks_ledger_and_stock_summary_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(16, '2025_09_17_105726_create_order_payments_table', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(17, '2025_09_28_100002_create_software_menu_and_menu_access_table', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(18, '2025_09_17_100001_create_admins_table', 3, NULL, NULL, NULL, NULL, NULL, NULL),
(19, '2025_09_28_100003_create_admin_menu_and_menu_access_table copy', 4, NULL, NULL, NULL, NULL, NULL, NULL),
(20, '2026_03_08_151500_seed_restaurant_setup_menus', 5, NULL, NULL, NULL, NULL, NULL, NULL),
(21, '2026_03_08_150829_create_staff_departments_table', 6, NULL, NULL, NULL, NULL, NULL, NULL),
(22, '2026_03_11_150000_add_audit_columns_to_tables', 7, NULL, NULL, NULL, NULL, NULL, NULL),
(23, '2026_03_16_000001_create_reservations_and_events_table', 8, NULL, NULL, NULL, NULL, NULL, NULL),
(24, '2026_03_16_000002_create_system_configs_and_logs_table', 9, NULL, NULL, NULL, NULL, NULL, NULL),
(25, '2026_03_16_000003_seed_new_modules_menus', 10, NULL, NULL, NULL, NULL, NULL, NULL),
(26, '2026_04_01_000002_create_user_profiles_table', 11, NULL, NULL, NULL, NULL, NULL, NULL),
(27, '2026_04_03_000003_add_kds_menu', 12, NULL, NULL, NULL, NULL, NULL, NULL),
(28, '2026_04_03_000004_add_pos_menu', 13, NULL, NULL, NULL, NULL, NULL, NULL),
(29, '2026_04_03_000002_add_status_to_order_details', 14, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `preparation_status` enum('pending','preparing','ready','served') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_masters`
--

CREATE TABLE `order_masters` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED DEFAULT NULL,
  `member_id` bigint UNSIGNED DEFAULT NULL,
  `table_id` bigint UNSIGNED DEFAULT NULL COMMENT 'null if takeaway/delivery',
  `order_type` tinyint NOT NULL DEFAULT '1' COMMENT '1=dine-in,2=takeaway,3=delivery,4=party/corporate',
  `order_status` tinyint NOT NULL DEFAULT '0' COMMENT '0=pending,1=preparing,2=ready,3=out_for_delivery,4=completed,5=cancelled',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `collect_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'for partial payments',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `deleted_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_payments`
--

CREATE TABLE `order_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_master_id` bigint UNSIGNED NOT NULL,
  `method` tinyint NOT NULL COMMENT '1=cash,2=card,3=mobile banking,4=wallet',
  `cash_amount` decimal(10,2) NOT NULL,
  `card_amount` decimal(10,2) NOT NULL,
  `mfs_amount` decimal(10,2) NOT NULL,
  `collect_amount` decimal(10,2) NOT NULL,
  `due_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `image`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(8, 'Pizza', 'uploads/menu_category_img/68dbace2cc71c.jpg', 1, '2025-09-30 04:11:46', '2025-09-30 04:11:46', NULL, NULL, NULL, NULL),
(9, 'Burger', 'uploads/menu_category_img/68dbad14c7017.jpg', 1, '2025-09-30 04:12:36', '2025-09-30 04:12:36', NULL, NULL, NULL, NULL),
(10, 'Beverages', 'uploads/menu_category_img/68e49b73e6ee0.jpg', 1, '2025-10-06 22:47:47', '2025-10-06 22:47:47', NULL, NULL, NULL, NULL),
(11, 'Pasta', 'uploads/menu_category_img/68e49bb5c6a03.jpeg', 1, '2025-10-06 22:48:53', '2025-10-06 22:48:53', NULL, NULL, NULL, NULL),
(12, 'Main dish', 'uploads/menu_category_img/68e49bf46a6ff.jpg', 1, '2025-10-06 22:49:56', '2025-10-06 22:49:56', NULL, NULL, NULL, NULL),
(13, 'Chicken', 'uploads/menu_category_img/68e49c160db42.jpg', 1, '2025-10-06 22:50:30', '2025-10-06 22:50:30', NULL, NULL, NULL, NULL),
(14, 'Appetizers', 'menu_categories/d38571fa-1edf-4619-a512-9bb9396fd6ea.jpg', 1, '2025-10-06 22:52:33', '2026-03-14 02:58:40', NULL, 4, NULL, NULL),
(15, 'Desserts', 'uploads/menu_category_img/68e4a23231b39.jpg', 1, '2025-10-06 22:52:41', '2025-10-06 23:16:34', NULL, NULL, NULL, NULL),
(16, 'Specials', 'uploads/menu_category_img/68e4a2ea71ca2.jpg', 1, '2025-10-06 22:53:14', '2025-10-06 23:19:38', NULL, NULL, NULL, NULL),
(17, 'Salads', 'uploads/menu_category_img/68e4a21ad8de6.jpg', 1, '2025-10-06 22:53:21', '2025-10-06 23:16:10', NULL, NULL, NULL, NULL),
(18, 'Kids\' Menu', 'uploads/menu_category_img/68e4a1f5b4e12.jpg', 1, '2025-10-06 22:54:07', '2025-10-06 23:15:33', NULL, NULL, NULL, NULL),
(19, 'Burger', 'uploads/menu_category_img/68ea28677577c.jpg', 1, '2025-10-06 23:20:21', '2025-10-11 03:50:31', NULL, NULL, NULL, NULL),
(21, 'add-on', NULL, 1, '2025-10-11 03:50:48', '2025-10-11 03:50:48', NULL, NULL, NULL, NULL),
(22, '11', NULL, 1, '2025-11-30 04:40:35', '2025-11-30 04:40:35', NULL, NULL, NULL, NULL),
(23, 'Salad', 'menu_categories/ReYcGejk4Z7hC6L64QuX5WXrqs7RArTkfdP5zvug.jpg', 1, '2026-03-07 03:53:44', '2026-03-07 03:53:44', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_items`
--

CREATE TABLE `product_items` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `menu_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `is_featured` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1=available,0=not available',
  `type` tinyint NOT NULL DEFAULT '1' COMMENT '1=regular,2=combo,3=complementary',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_items`
--

INSERT INTO `product_items` (`id`, `category_id`, `name`, `description`, `price`, `menu_img`, `unit_id`, `department_id`, `is_featured`, `status`, `type`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 8, 'Chicken Pizza', NULL, 400.00, 'menu-images/7XWTh8hFnl8L9WtfYSxsIGhwICLb0t3HrUx3U2ku.jpg', 1, 1, 0, 1, 1, '2025-10-07 05:11:47', '2025-10-07 05:11:47', NULL, NULL, NULL, NULL),
(2, 9, 'Burger2', NULL, 200.00, 'menu-images/coZJzbueTcDDotsNcDperVKbxm42Yj2nEfGwY7I8.jpg', 1, 1, 0, 1, 1, '2025-10-07 05:37:31', '2025-10-11 03:06:33', NULL, NULL, NULL, NULL),
(3, 10, 'Sprite', NULL, 100.00, 'menu-images/U1ZHZAmZNaSwE6QH2oXeuVUmUyjS84veBk9tj0wI.jpg', 2, 2, 0, 1, 1, '2025-10-07 05:38:05', '2025-10-07 05:38:05', NULL, NULL, NULL, NULL),
(5, 12, 'C2', NULL, 400.00, 'menu-images/A5TYgUPWD9anaDv42vzIPCp7v3tlbqG5XfeE4xyG.jpg', 1, 1, 0, 1, 2, '2025-10-11 00:00:59', '2025-10-11 00:00:59', NULL, NULL, NULL, NULL),
(7, 10, 'Lemon Juice', NULL, 0.00, 'menu-images/pwTu6KvkyCb8uqAME8EAeivGXLOLi5XoxeW2XC9K.jpg', 2, 2, 0, 1, 3, '2025-10-11 03:05:18', '2025-10-11 03:05:18', NULL, NULL, NULL, NULL),
(8, 21, 'Water', NULL, 0.00, 'menu-images/cae82087-27df-40cd-8026-3fcd654a9b76.jpg', 2, 2, 0, 1, 3, '2026-03-14 03:05:17', '2026-03-14 03:06:54', 4, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `item_id`, `name`, `price`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(2, 2, 'Large', 400.00, '2025-10-11 03:21:32', '2025-10-11 03:30:43', NULL, NULL, NULL, NULL),
(3, 1, 'Extra Cheese', 250.00, '2025-10-11 03:25:16', '2025-10-11 03:30:35', NULL, NULL, NULL, NULL),
(4, 1, 'Extra Sauces', 220.00, '2025-10-11 03:39:31', '2025-10-11 03:40:09', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion_items`
--

CREATE TABLE `promotion_items` (
  `id` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_details`
--

CREATE TABLE `purchase_details` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `ingredients_id` bigint UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_master`
--

CREATE TABLE `purchase_master` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `purchase_date` date NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0=pending,1=approved,2=received,3=cancelled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `ingredient_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `table_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `reservation_time` datetime NOT NULL,
  `guests_count` int NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_requests` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `capacity` int NOT NULL DEFAULT '4',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`id`, `name`, `section`, `branch_id`, `capacity`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 'T01', NULL, 1, 4, 1, '2026-03-31 03:49:19', '2026-03-31 03:49:19', 4, NULL, NULL, NULL),
(2, 'T02', NULL, 1, 4, 1, '2026-03-31 03:49:23', '2026-03-31 03:49:53', 4, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `res_departments`
--

CREATE TABLE `res_departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `res_departments`
--

INSERT INTO `res_departments` (`id`, `name`, `branch_id`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 'Kitchen', 1, '2025-10-07 06:05:28', '2025-10-07 06:05:28', NULL, NULL, NULL, NULL),
(2, 'Juice Bar', 1, '2025-10-07 06:05:28', '2025-10-07 06:05:28', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1=active,0=inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `deleted_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(1, 'admin', 'Access All', 1, '2025-09-22 09:44:56', '2025-09-22 09:44:56', NULL, 0, 0, 0),
(2, 'manager', 'Access few', 1, '2025-09-22 09:45:22', '2025-09-22 09:45:22', NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('zDRIQcYHzhwq4cMX2uRxBM3qiSczbvchHMuOcky6', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidkZ3YkdzaGluM285OFR2Y0U0aEg5cWE4UjY2NjJub2NEZmVWWXJNcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vcmVzZGluZS50ZXN0L2FkbWluL3JlcG9ydHMiO3M6NToicm91dGUiO3M6MTk6ImFkbWluLnJlcG9ydHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O3M6NTI6ImxvZ2luX2FkbWluXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1775293521, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `software_menus`
--

CREATE TABLE `software_menus` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `software_menus`
--

INSERT INTO `software_menus` (`id`, `name`, `route`, `icon`, `parent_id`, `order`, `is_active`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 'Dashboard', 'admin.dashboard', 'dashboard', NULL, 1, 1, NULL, '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(2, 'Users', NULL, 'group', NULL, 2, 1, NULL, '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(3, 'Menu Management', NULL, 'restaurant_menu', NULL, 3, 1, NULL, '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(5, 'Inventory', NULL, 'shopping_cart', NULL, 4, 1, NULL, '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(6, 'Orders', 'admin.orders.index', 'receipt_long', NULL, 7, 1, NULL, '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(9, 'Promotions', 'admin.promotions.index', 'campaign', 35, 3, 1, NULL, '2026-04-03 23:32:10', NULL, NULL, NULL, NULL),
(10, 'Settings', 'admin.settings.index', 'settings', NULL, 12, 1, NULL, '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(11, 'Manage Users', 'admin.users.index', 'manage_accounts', 2, 1, 1, NULL, '2025-11-03 04:50:04', NULL, NULL, NULL, NULL),
(13, 'Categories', 'admin.product.categories.index', 'category', 3, 1, 1, NULL, '2025-11-03 04:50:04', NULL, NULL, NULL, NULL),
(14, 'Items', 'admin.product.items.index', 'restaurant', 3, 2, 1, NULL, '2025-11-03 04:50:04', NULL, NULL, NULL, NULL),
(15, 'Variants', 'admin.product.variants.index', 'list_alt', 3, 3, 1, NULL, '2025-11-03 04:50:04', NULL, NULL, NULL, NULL),
(17, 'All Purchases', 'admin.purchase.index', 'shopping_bag', 34, 2, 1, NULL, '2026-03-11 00:35:17', NULL, NULL, NULL, NULL),
(18, 'All Orders', 'admin.orders.index', 'receipt', 6, 1, 1, NULL, '2025-11-03 04:50:04', NULL, NULL, NULL, NULL),
(25, 'Restaurant Setup', NULL, 'settings_suggest', 10, 1, 1, '2026-03-08 03:24:32', '2026-03-08 03:24:32', NULL, NULL, NULL, NULL),
(26, 'Branches', 'admin.settings.restaurant-setup.branches.index', 'store', 25, 1, 1, '2026-03-08 03:24:32', '2026-03-08 03:24:32', NULL, NULL, NULL, NULL),
(27, 'Kitchen/Res Dept', 'admin.settings.restaurant-setup.res-departments.index', 'kitchen', 25, 2, 1, '2026-03-08 03:24:32', '2026-03-08 03:24:32', NULL, NULL, NULL, NULL),
(28, 'Staff Dept', 'admin.settings.restaurant-setup.staff-departments.index', 'groups', 25, 3, 1, '2026-03-08 03:24:32', '2026-03-08 03:24:32', NULL, NULL, NULL, NULL),
(29, 'Tables & Sections', 'admin.settings.restaurant-setup.tables.index', 'table_restaurant', 25, 4, 1, '2026-03-08 03:24:32', '2026-03-08 03:24:32', NULL, NULL, NULL, NULL),
(30, 'Units', 'admin.unit.index', 'balance', 5, 1, 1, '2026-03-10 23:08:43', '2026-03-11 00:34:16', NULL, NULL, NULL, NULL),
(31, 'Ingredients', 'admin.ingredients.index', 'grocery', 5, 2, 1, '2026-03-10 23:10:53', '2026-03-11 00:34:16', NULL, NULL, NULL, NULL),
(32, 'Suppliers', 'admin.suppliers.index', 'deployed_code_account', 34, 1, 1, '2026-03-10 23:23:40', '2026-03-11 00:35:17', NULL, NULL, NULL, NULL),
(33, 'Stock', 'admin.stock.index', 'inventory', 5, 3, 1, '2026-03-10 23:25:21', '2026-03-11 00:34:16', NULL, NULL, NULL, NULL),
(34, 'Purchase', NULL, 'add_shopping_cart', NULL, 5, 1, '2026-03-11 00:32:34', '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(35, 'Customers & Loyalty', NULL, 'groups', NULL, 8, 1, '2026-03-15 23:48:05', '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(36, 'Customers', 'admin.customers.index', 'person', 35, 1, 1, '2026-03-15 23:48:05', '2026-03-16 00:37:52', NULL, 1, NULL, NULL),
(37, 'Membership Tiers', 'admin.memberships.index', 'card_membership', 35, 2, 1, '2026-03-15 23:48:05', '2026-03-16 00:38:18', NULL, 1, NULL, NULL),
(38, 'Reservations', NULL, 'book_online', NULL, 9, 1, '2026-03-15 23:48:05', '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(39, 'Table Bookings', 'admin.reservations.index', 'table_restaurant', 38, 1, 1, '2026-03-15 23:48:05', '2026-04-04 01:02:53', NULL, 1, NULL, NULL),
(40, 'Company Events', 'admin.events.index', 'event', 38, 2, 1, '2026-03-15 23:48:05', '2026-04-04 01:03:15', NULL, 1, NULL, NULL),
(41, 'Business Config', 'admin.settings.business-config.index', 'business', 10, 10, 1, '2026-03-15 23:48:05', '2026-04-04 01:00:13', NULL, 1, NULL, NULL),
(42, 'Operations', NULL, 'settings_applications', NULL, 10, 1, '2026-04-03 23:24:31', '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(43, 'Kitchen Display (KDS)', 'admin.kds.index', 'restaurant', 42, 1, 1, '2026-04-03 23:24:32', '2026-04-03 23:24:32', NULL, NULL, NULL, NULL),
(44, 'Point of Sale', 'admin.pos.index', 'point_of_sale', NULL, 6, 1, '2026-04-03 23:25:29', '2026-04-04 01:22:34', NULL, NULL, NULL, NULL),
(45, 'Reports & Analytics', NULL, 'query_stats', NULL, 11, 1, '2026-04-04 01:06:15', '2026-04-04 01:22:34', 1, NULL, NULL, NULL),
(46, 'Reports', 'admin.reports.index', 'finance_mode', 45, 0, 1, '2026-04-04 01:07:45', '2026-04-04 01:07:45', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `software_menu_access`
--

CREATE TABLE `software_menu_access` (
  `id` bigint UNSIGNED NOT NULL,
  `menu_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `software_menu_access`
--

INSERT INTO `software_menu_access` (`id`, `menu_id`, `user_id`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(6, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 2, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 3, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 5, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 6, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 9, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 10, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 10, 4, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_departments`
--

CREATE TABLE `staff_departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_ledger`
--

CREATE TABLE `stock_ledger` (
  `id` bigint UNSIGNED NOT NULL,
  `ingredient_id` bigint UNSIGNED NOT NULL,
  `unit_id` int NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `transaction_type` tinyint UNSIGNED NOT NULL COMMENT '1=purchase, 2=sale, 3=return_in, 4=return_out, 5=adjustment_in, 6=adjustment_out, 7=transfer_in, 8=transfer_out',
  `reference_id` bigint UNSIGNED DEFAULT NULL COMMENT 'invoice_id, purchase_id, adjustment_id etc.',
  `reference_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'purchase, invoice, adjustment',
  `qty_in` decimal(12,2) NOT NULL DEFAULT '0.00',
  `qty_out` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `batch_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Batch/Lot number if applicable',
  `expiry_date` date DEFAULT NULL COMMENT 'Expiry date for perishable stock',
  `transaction_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_summary`
--

CREATE TABLE `stock_summary` (
  `id` bigint UNSIGNED NOT NULL,
  `ingredient_id` bigint UNSIGNED NOT NULL,
  `unit_id` int NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `current_stock` decimal(12,2) NOT NULL DEFAULT '0.00',
  `average_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `batch_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Batch/Lot number if applicable',
  `expiry_date` date DEFAULT NULL COMMENT 'Expiry date for perishable stock',
  `last_transaction_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 'gm', 1, '2025-10-07 06:07:15', '2025-10-07 06:07:15', NULL, NULL, NULL, NULL),
(2, 'ml', 1, '2025-10-07 06:05:28', '2025-10-07 06:05:28', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role_id`, `branch_id`, `status`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, 'Admin User', 'admin@resdine.com', '01710000001', '$2y$12$HWzpYmih9BRkaQnZhsCxJeR1ljvBQV0p.yYiRcUNcCbKCZHtscSvC', 1, 1, 1, '2025-01-01 04:00:00', 'JCLgymDMls99Z6Nkc5scNNoMaMTSiilKDByMFL1x5jpvOrHn0L0VSWVLYPvz', '2025-01-01 04:00:00', '2025-01-01 04:00:00', NULL, NULL, NULL, NULL),
(2, 'Branch Manager', 'manager@resdine.com', '01710000002', '$2y$10$abcdefghijklmnopqrstuv', 2, 1, 1, '2025-01-05 05:00:00', 'token456', '2025-01-05 05:00:00', '2025-01-05 05:00:00', NULL, NULL, NULL, NULL),
(3, 'Sales Staff', 'staff@resdine.com', '01710000003', '$2y$10$abcdefghijklmnopqrstuv', 1, 2, 1, '2025-02-01 03:30:00', 'token789', '2025-02-01 03:30:00', '2025-02-01 03:30:00', NULL, NULL, NULL, NULL),
(4, 'Support Staff', 'support@resdine.com', '01710000004', '$2y$12$rk/bat02PtG.mHplU9ad4u40Qh3bDvMkBkyTcJsHKPEP/DC3Ht9d6', 1, 1, 0, NULL, NULL, '2025-02-10 02:00:00', '2025-02-10 02:00:00', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `profile_photo`, `address`, `dob`, `gender`, `created_at`, `updated_at`) VALUES
(1, 4, 'users/f4d0a83c-e6ab-4bed-a873-952b93fd97c7.jpg', 'Mirpur, Dhaka, Bangladesh', '2026-04-04', 'male', '2026-04-02 06:02:44', '2026-04-02 06:03:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`),
  ADD UNIQUE KEY `admins_user_name_unique` (`user_name`);

--
-- Indexes for table `admin_menus`
--
ALTER TABLE `admin_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_menus_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `admin_menu_access`
--
ALTER TABLE `admin_menu_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_menu_access_menu_id_foreign` (`menu_id`),
  ADD KEY `admin_menu_access_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_configs`
--
ALTER TABLE `business_configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_configs_key_unique` (`key`);

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
-- Indexes for table `combo_item_details`
--
ALTER TABLE `combo_item_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `combo_item_details_combo_id_foreign` (`combo_id`),
  ADD KEY `combo_item_details_item_id_foreign` (`item_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_memberships`
--
ALTER TABLE `customer_memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_memberships_customer_id_foreign` (`customer_id`),
  ADD KEY `customer_memberships_membership_id_foreign` (`membership_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingredients_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_details_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_details_item_id_foreign` (`item_id`);

--
-- Indexes for table `invoice_master`
--
ALTER TABLE `invoice_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_master_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoice_master_order_id_foreign` (`order_id`),
  ADD KEY `invoice_master_customer_id_foreign` (`customer_id`),
  ADD KEY `invoice_master_promotion_id_foreign` (`promotion_id`);

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
-- Indexes for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loyalty_points_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_order_id_foreign` (`order_id`),
  ADD KEY `order_details_item_id_foreign` (`item_id`);

--
-- Indexes for table `order_masters`
--
ALTER TABLE `order_masters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_master_user_id_foreign` (`user_id`),
  ADD KEY `order_master_branch_id_foreign` (`branch_id`),
  ADD KEY `order_master_member_id_foreign` (`member_id`),
  ADD KEY `order_master_table_id_foreign` (`table_id`),
  ADD KEY `order_master_promotion_id_foreign` (`promotion_id`);

--
-- Indexes for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_payments_order_master_id_foreign` (`order_master_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_items`
--
ALTER TABLE `product_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_category_id_foreign` (`category_id`),
  ADD KEY `menu_items_unit_id_foreign` (`unit_id`),
  ADD KEY `menu_items_department_id_foreign` (`department_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_variants_item_id_foreign` (`item_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotion_items`
--
ALTER TABLE `promotion_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_items_promotion_id_foreign` (`promotion_id`),
  ADD KEY `promotion_items_item_id_foreign` (`item_id`);

--
-- Indexes for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_details_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_details_ingredients_id_foreign` (`ingredients_id`);

--
-- Indexes for table `purchase_master`
--
ALTER TABLE `purchase_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_master_invoice_number_unique` (`invoice_number`),
  ADD KEY `purchase_master_supplier_id_foreign` (`supplier_id`),
  ADD KEY `purchase_master_branch_id_foreign` (`branch_id`),
  ADD KEY `purchase_master_user_id_foreign` (`user_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipes_unit_id_foreign` (`unit_id`),
  ADD KEY `recipes_ingredient_id_foreign` (`ingredient_id`),
  ADD KEY `recipes_item_id_foreign` (`item_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_customer_id_foreign` (`customer_id`),
  ADD KEY `reservations_table_id_foreign` (`table_id`),
  ADD KEY `reservations_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_tables_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `res_departments`
--
ALTER TABLE `res_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `res_departments_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_permissions_role_id_foreign` (`role_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `software_menus`
--
ALTER TABLE `software_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `software_menus_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `software_menu_access`
--
ALTER TABLE `software_menu_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `software_menu_access_menu_id_foreign` (`menu_id`),
  ADD KEY `software_menu_access_user_id_foreign` (`user_id`);

--
-- Indexes for table `staff_departments`
--
ALTER TABLE `staff_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_departments_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_ledger_ingredient_id_index` (`ingredient_id`),
  ADD KEY `stock_ledger_branch_id_index` (`branch_id`),
  ADD KEY `stock_ledger_transaction_type_index` (`transaction_type`),
  ADD KEY `stock_ledger_transaction_date_index` (`transaction_date`),
  ADD KEY `stock_ledger_batch_no_index` (`batch_no`),
  ADD KEY `stock_ledger_expiry_date_index` (`expiry_date`);

--
-- Indexes for table `stock_summary`
--
ALTER TABLE `stock_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_summary_ingredient_id_branch_id_batch_no_unique` (`ingredient_id`,`branch_id`,`batch_no`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `users_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_profiles_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_menus`
--
ALTER TABLE `admin_menus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `admin_menu_access`
--
ALTER TABLE `admin_menu_access`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `business_configs`
--
ALTER TABLE `business_configs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `combo_item_details`
--
ALTER TABLE `combo_item_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_memberships`
--
ALTER TABLE `customer_memberships`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_master`
--
ALTER TABLE `invoice_master`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_masters`
--
ALTER TABLE `order_masters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_payments`
--
ALTER TABLE `order_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `product_items`
--
ALTER TABLE `product_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotion_items`
--
ALTER TABLE `promotion_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_master`
--
ALTER TABLE `purchase_master`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `res_departments`
--
ALTER TABLE `res_departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `software_menus`
--
ALTER TABLE `software_menus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `software_menu_access`
--
ALTER TABLE `software_menu_access`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `staff_departments`
--
ALTER TABLE `staff_departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_ledger`
--
ALTER TABLE `stock_ledger`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_summary`
--
ALTER TABLE `stock_summary`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_menus`
--
ALTER TABLE `admin_menus`
  ADD CONSTRAINT `admin_menus_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `admin_menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_menu_access`
--
ALTER TABLE `admin_menu_access`
  ADD CONSTRAINT `admin_menu_access_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_menu_access_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `admin_menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `combo_item_details`
--
ALTER TABLE `combo_item_details`
  ADD CONSTRAINT `combo_item_details_combo_id_foreign` FOREIGN KEY (`combo_id`) REFERENCES `product_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `combo_item_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `product_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_memberships`
--
ALTER TABLE `customer_memberships`
  ADD CONSTRAINT `customer_memberships_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_memberships_membership_id_foreign` FOREIGN KEY (`membership_id`) REFERENCES `memberships` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD CONSTRAINT `invoice_details_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoice_master` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `product_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_master`
--
ALTER TABLE `invoice_master`
  ADD CONSTRAINT `invoice_master_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoice_master_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `order_masters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoice_master_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD CONSTRAINT `loyalty_points_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `product_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `order_masters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_masters`
--
ALTER TABLE `order_masters`
  ADD CONSTRAINT `order_master_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_master_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_master_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_master_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_master_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD CONSTRAINT `order_payments_order_master_id_foreign` FOREIGN KEY (`order_master_id`) REFERENCES `order_masters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_items`
--
ALTER TABLE `product_items`
  ADD CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `menu_items_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `res_departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `menu_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `menu_variants_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `product_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotion_items`
--
ALTER TABLE `promotion_items`
  ADD CONSTRAINT `promotion_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `product_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_items_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `purchase_details_ingredients_id_foreign` FOREIGN KEY (`ingredients_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_details_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchase_master` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_master`
--
ALTER TABLE `purchase_master`
  ADD CONSTRAINT `purchase_master_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_master_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_master_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `product_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservations_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD CONSTRAINT `restaurant_tables_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `res_departments`
--
ALTER TABLE `res_departments`
  ADD CONSTRAINT `res_departments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `software_menus`
--
ALTER TABLE `software_menus`
  ADD CONSTRAINT `software_menus_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `software_menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `software_menu_access`
--
ALTER TABLE `software_menu_access`
  ADD CONSTRAINT `software_menu_access_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `software_menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `software_menu_access_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_departments`
--
ALTER TABLE `staff_departments`
  ADD CONSTRAINT `staff_departments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
