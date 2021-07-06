-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 04, 2020 at 05:33 PM
-- Server version: 8.0.22-0ubuntu0.20.04.2
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `koma`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `owner_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `owner_id`, `created_at`, `updated_at`) VALUES
(7, NULL, 1, '2019-11-07 11:03:15', '2019-11-07 11:03:15'),
(8, 7, 1, '2019-11-07 11:03:15', '2019-11-07 11:03:15'),
(9, 7, 1, '2019-11-07 11:03:15', '2019-11-07 11:03:15'),
(10, NULL, 1, '2019-11-08 05:35:12', '2019-11-08 05:35:12');

-- --------------------------------------------------------

--
-- Table structure for table `encrypted_store`
--

CREATE TABLE `encrypted_store` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `resource_type` tinyint UNSIGNED NOT NULL,
  `resource_id` int UNSIGNED DEFAULT NULL,
  `data` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `encrypted_store`
--

INSERT INTO `encrypted_store` (`id`, `user_id`, `resource_type`, `resource_id`, `data`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 7, '-c05T1cU_iC7E--S_axrHfazyZK4-wo62_7rQzlUDm9sUqUa2e9uwolLBcGh9G0cxxVaUg_wuDffAlxs8vu0Y_TtzMNrd97uIzo3BOXBMEpG2vD3M3IEPYXgs2QMJS3lscrmEfmsoEFbivJuiupafL2AZ82dTKpIpz0h9Wq685RQYFTCDYxmb6HIAYtFW3EROs7NmHwie4NkhzQxOkukDHv9oD4OKLTu8VbR4muGZF5lAqBxt1gEDpfCTnZa9kUDVPCorjw=', NULL, NULL),
(2, 1, 1, 8, 'KjkrGPYe6KYK9OA3QSviIPQkqsGF41WdQ5TubDQh8X8tRbIXmgfR4IlLMC0QEGVvK343eFKGz9rMy7zM8adDMjLQj9u5lvd4GQ==', NULL, NULL),
(3, 1, 1, 9, 'KqNEIpJzhmVyQlMX1G8xl30cKpgZR28LxYTE3qQIjT737-r1jz2-Bb7Fjjj_eGlHjicwookALRwjhTqnY3Bwu7pe', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_user`
--

CREATE TABLE `group_user` (
  `id` int UNSIGNED NOT NULL,
  `group_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_categories`
--

CREATE TABLE `ip_categories` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort` smallint UNSIGNED NOT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `owner_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_fields`
--

CREATE TABLE `ip_fields` (
  `id` int UNSIGNED NOT NULL,
  `sort` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bindings` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_subnets`
--

CREATE TABLE `ip_subnets` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2016_01_13_110152_create_categories_table', 1),
(3, '2016_01_13_110200_create_items_table', 1),
(4, '2016_01_18_105945_create_ip_categories_table', 1),
(5, '2016_01_18_120626_create_ip_fields_table', 1),
(6, '2016_03_10_063952_create_groups_table', 1),
(7, '2016_03_10_150207_create_permissions_table', 1),
(8, '2019_06_11_162248_create_encrypted_store_table', 1),
(9, '2019_08_27_115543_create_ip_subnets_table', 1),
(10, '2019_09_06_122508_create_sessions_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `group_id` int UNSIGNED DEFAULT NULL,
  `resource_type` tinyint UNSIGNED NOT NULL,
  `resource_id` int UNSIGNED DEFAULT NULL,
  `grant_type` tinyint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `payload` text COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`, `expires_at`) VALUES
('6leSiIuA6FZRVmDdHD1i9nkRUDrXxffaw8ipJC2N', NULL, NULL, NULL, 'ZXlKcGRpSTZJbk5OYjFCV2RtTjBZMDB5ZGtFd1NXdFplRFJwUjFFOVBTSXNJblpoYkhWbElqb2lNbTFMUnpsdmVIUkVOV3N6ZUhOSWMzVTVhMjh5WkZOcE5EaDZUVWhzYTF3dlkzWXhWV0o2WEM5b09Wd3ZRalZwVW5sbmFtWkJaVWRWTlVGRlVGcGhibWxEWjI1NE5rOVBiVlZ4ZUVGbWExQTBlR2hYT1ZVM1VrNWlaaXRqVFU5NlRqTjZablpFWTJGMU5XOVFlV2huZUVnNWJGUm1PWFI2T0ZWMFdFUlBSVVpXTmpabWRIQlhTak5XTm5wcGVUZEVNV05aV1ROU2RFZFVhVEk1YVZKcGIyTnFXR3BSZUUxdGRYcEVXVXhjTHpKdWVEbE5iM2hUTW5GemRYZFRZakYyZVVoYVJYbEpRM0pCYmtKU09YWk9jbUZ2VjNKYVJFWnpTVFkwYUhaNWIzRXJiRUpOYzFSSlNuTkZUMDFjTDJWUE1FMTBUbXhUWldWdFluQkhOMlpHWlVneFYwOWFkbmxIVkhSVlFVaEJWMnhRYlVWY0wyRktUSHBZVkZnd2NWTkdTWGhVTTNaaWJsRnFXalpGVEZwU00wOVRiRzVYZDIxNmVXOVBVRk5rVFRkMmJuQjZRMGhHWjIxaGQwaFNiakJxYjFSSU9XdzBUblk1WW1vek1FRTFLMXBDVEROSFhDOUZObWR4TlhORVNYWk1iWFpTV2lzM2FFRjZRa3BSZVhCeFJGQjNNRW8xWjBwQ1dsd3ZWRmRuUmpjMmNVRmxlVkZKVDBKWFRtVk5jRmxRYUZFek5VUjJSVmxQWm1saVdGZHFSbnBJVUhKTWFXVmhjamQzWjJSUlVFazRWbkZDUkdGTk1HRlZhMUV4VW1KdWQxaE9TMDU1U0Z3dlQySmlSbmM5UFNJc0ltMWhZeUk2SWpsaVlqazVaVEkxT1RjMk5tRTVOelE0WkRNeU16azJOREZqTXpRMU5UaGlOMlEwTVdGaFpESmxZell3WVRBMllUZzBObUZoWTJJM05UZGxOMkUzT1RnaWZRPT0=', 1573570556, NULL),
('9KzF4qKMVNsgIlPzTA4onp8fKQzF6QYyp4gMbT7O', NULL, NULL, NULL, 'ZXlKcGRpSTZJalZ0VFhWWWFucExSM0JqVkdKaFVHOWphbU5NVm1jOVBTSXNJblpoYkhWbElqb2lZblIzVkdOTmRFMWhVQ3R6WkdkY0wzZFhZbVpJTm5OUmIzQktORzVxT0VWUE1UZHdRalZ5WkZGRllXbFRXVWRxV21Ga01FOUpaMkZ6VEhkVmNYbDRZV3RNUlVGbFQzcEpXVTF3Ym1ObE1rZEtRVGRaTjBoeGNHUmtNelZ3YTFNMmRFeDBXR3h6Vmt4aVhDOVBTV1JQTmxSclFWRk9VbGhPUzAxWVkzRnhaRE5JV0c1Vk1YVTRPRGhNU3pkMFFVNTNOSGwwTlVGS01FbE1lR1JYUTI1R2JsUmFSbWxZV0dGcFIwNXBNbkpVTlcwNU0wdDFTVlJqTTAxc1pYRnZVek5vTm14Mloxd3ZabEUxYVV4c00yZGNMM1ZtYTBkMFVtdGpSVUpJTTJGUllWd3ZhWFYzZFRacloyZFpiMnBTZVZkWVJIQlFkMkphVVZ3dk1qRnpOV3QzV0hkdWNHRnhVVU5VV0ZoTmRFSjJkbkZMU0hWWlVsTklORXR2YjNKd2NYYzJhMWxzTkhwV2JXOU9WVGhMWjFSUVVHUTVWRlZDVGtwblNGYzNXVTlEUXpOTVZYZGNMM2xjTDFoR1pETnRjallyVTBKSWRETmxSalZGVW1zMFQxUm1SVzVPY0VSRVJXNDRPVFpuYVhZclZuSjBNMGdyV1ZWek1GRnpiazV4UlZSWVkyRlFVMVV3VWxvMmNFMDRUWGRDY0RNME5uZGNMMDV4UnpjeWJtdE1PRUk0WTF3dk9FTTJiRkIwZWtsb1FsRktVa0oxYURsU1prRkNRbEZ2TVV0Q2RXWXlURGxTWVhwU1dGRmhhelpHT1VnMVVVMVpTVE54V2pOM1dEbE5iRUpuUFQwaUxDSnRZV01pT2lKaE5HUmpaalE1WlRKbE1XRXlOVFF6Wkdaa01EZGxPVGc1TkdVME1qQTFNams1TjJRMFpXRTROV1F5T1RSallqUTVNbVprWldReE16RXdOemhpTldGbEluMD0=', 1573458349, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `role` tinyint NOT NULL DEFAULT '1',
  `profile` text COLLATE utf8_unicode_ci,
  `public_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `recovery_string` text COLLATE utf8_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `profile`, `public_key`, `salt`, `recovery_string`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Tester Manole', 'tester@manole.ro', '$2y$10$Wrd7n/pCu8ZAcPtF.ZVJOOczm0t4MRRLnZmmFEgqKX6YmCzcVtSTa', 1, NULL, 'Le7b3VagOYyri7+6E5f4k1qrM1GYNYVpBDHmmKmRdW4=', '+8/OBmBWABbgsTM/tXNTJQ==', 'MUIEAJRV0YqwJV-mbKlatwSMdvyFk3dPU4PtqlkvnrWh8uHfJjjvEWmSWAJPNQP7gVgEy33p2C6weU4udrpfT9evNbR6ybysFtFRwsLkBIINYqfOqjCRU18MAisDMTRO8ajyJnCPSat96sMUfsS405lREjgfFcb4MThktKbAR2Cv', 'VwjYUUZQ1fkOFvlvKAVOx4kWjUJLHTeQWNJnz63vrrGeYghrA2N9HDKOGzgX', '2019-11-07 10:58:33', '2019-11-07 10:58:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_parent_id_index` (`parent_id`),
  ADD KEY `categories_owner_id_index` (`owner_id`);

--
-- Indexes for table `encrypted_store`
--
ALTER TABLE `encrypted_store`
  ADD PRIMARY KEY (`id`),
  ADD KEY `encrypted_store_user_id_index` (`user_id`),
  ADD KEY `encrypted_store_resource_type_index` (`resource_type`),
  ADD KEY `encrypted_store_resource_id_index` (`resource_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_user`
--
ALTER TABLE `group_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_user_group_id_index` (`group_id`),
  ADD KEY `group_user_user_id_index` (`user_id`);

--
-- Indexes for table `ip_categories`
--
ALTER TABLE `ip_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_categories_sort_index` (`sort`),
  ADD KEY `ip_categories_parent_id_index` (`parent_id`),
  ADD KEY `ip_categories_owner_id_index` (`owner_id`);

--
-- Indexes for table `ip_fields`
--
ALTER TABLE `ip_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ip_subnets`
--
ALTER TABLE `ip_subnets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_subnets_category_id_index` (`category_id`),
  ADD KEY `ip_subnets_created_by_index` (`created_by`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `items_category_id_index` (`category_id`),
  ADD KEY `items_created_by_index` (`created_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_user_id_index` (`user_id`),
  ADD KEY `permissions_group_id_index` (`group_id`),
  ADD KEY `permissions_resource_type_index` (`resource_type`),
  ADD KEY `permissions_resource_id_index` (`resource_id`),
  ADD KEY `permissions_grant_type_index` (`grant_type`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD UNIQUE KEY `sessions_id_unique` (`id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `encrypted_store`
--
ALTER TABLE `encrypted_store`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_user`
--
ALTER TABLE `group_user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_categories`
--
ALTER TABLE `ip_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_fields`
--
ALTER TABLE `ip_fields`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_subnets`
--
ALTER TABLE `ip_subnets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `encrypted_store`
--
ALTER TABLE `encrypted_store`
  ADD CONSTRAINT `encrypted_store_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_user`
--
ALTER TABLE `group_user`
  ADD CONSTRAINT `group_user_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_categories`
--
ALTER TABLE `ip_categories`
  ADD CONSTRAINT `ip_categories_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `ip_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_subnets`
--
ALTER TABLE `ip_subnets`
  ADD CONSTRAINT `ip_subnets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `ip_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_subnets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
