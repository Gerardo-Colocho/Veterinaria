-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2026 a las 20:41:01
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vet_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `mascota_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `estado` varchar(50) DEFAULT 'pendiente',
  `motivo_cancelacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `user_id`, `mascota_id`, `fecha_hora`, `motivo`, `estado`, `motivo_cancelacion`, `created_at`, `updated_at`) VALUES
(9, 4, 7, '2026-10-20 11:00:00', 'Chequeo General', 'Completada', NULL, '2026-10-20 05:54:28', '2026-10-20 05:55:52'),
(10, 4, 8, '2026-10-20 13:00:00', 'necesita una vacuna', 'Pendiente', NULL, '2026-10-20 06:08:20', '2026-10-20 06:10:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
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
-- Estructura de tabla para la tabla `historial_medicos`
--

CREATE TABLE `historial_medicos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mascota_id` bigint(20) UNSIGNED NOT NULL,
  `diagnostico` text NOT NULL,
  `tratamiento` text NOT NULL,
  `fecha_atencion` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_medicos`
--

INSERT INTO `historial_medicos` (`id`, `mascota_id`, `diagnostico`, `tratamiento`, `fecha_atencion`, `observaciones`, `created_at`, `updated_at`) VALUES
(4, 7, 'tiene alergia', 'aceite de coco', '2026-10-19', NULL, '2026-10-20 05:58:22', '2026-10-20 05:58:22'),
(5, 7, 'Chequeo General', 'aceite de coco cada 12 horas', '2026-10-20', NULL, '2026-10-20 06:06:17', '2026-10-20 06:06:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `especie` varchar(255) NOT NULL,
  `raza` varchar(255) DEFAULT NULL,
  `edad_meses` int(11) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id`, `user_id`, `nombre`, `especie`, `raza`, `edad_meses`, `peso`, `foto_url`, `created_at`, `updated_at`) VALUES
(7, 4, 'Copito', 'Perro', 'Golden', 20, 10.00, NULL, '2026-10-20 05:53:45', '2026-10-20 05:53:45'),
(8, 4, 'Carlos', 'Ave', 'Loro', 20, 5.00, NULL, '2026-10-20 06:07:45', '2026-10-20 06:07:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_17_042954_create_personal_access_tokens_table', 1),
(5, '2026_10_17_043021_create_mascotas_table', 1),
(6, '2026_10_17_043022_create_citas_table', 1),
(7, '2026_10_17_043022_create_historial_medicos_table', 1),
(8, '2026_10_19_212035_add_motivo_cancelacion_to_citas_table', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'vettoken', '3377c2a1ef9433dfe9058c02da842afcf690fe07b382133fd2c786e6c056bf56', '[\"*\"]', '2026-10-19 09:19:13', NULL, '2026-10-17 10:47:42', '2026-10-19 09:19:13'),
(2, 'App\\Models\\User', 1, 'vettoken', 'be0a90b9ff544e1b247564649ca5ccde989793f4d1eaa267a7cc2c44256f8629', '[\"*\"]', NULL, NULL, '2026-10-17 11:36:38', '2026-10-17 11:36:38'),
(3, 'App\\Models\\User', 1, 'vettoken', '7274a06e6e366a07c2b3419f580493169c90b5c30195358000f7bbd69ad590bd', '[\"*\"]', NULL, NULL, '2026-10-17 11:36:47', '2026-10-17 11:36:47'),
(4, 'App\\Models\\User', 1, 'vettoken', '4751dcb17de291f39490cd0bb684601900fb18c058d87c5c1dd17edd4cc62222', '[\"*\"]', NULL, NULL, '2026-10-17 11:37:41', '2026-10-17 11:37:41'),
(5, 'App\\Models\\User', 1, 'vettoken', '0bcc8c844f834adbdc117ed0eade345fd06d873cf2af7497718e67d2ca202efa', '[\"*\"]', '2026-10-17 11:50:59', NULL, '2026-10-17 11:38:47', '2026-10-17 11:50:59'),
(6, 'App\\Models\\User', 1, 'vettoken', '4b490c14ef87247b468aa047dcc378da95fc2d465919d21653184ad0d5c17e94', '[\"*\"]', '2026-10-17 12:31:49', NULL, '2026-10-17 11:54:28', '2026-10-17 12:31:49'),
(7, 'App\\Models\\User', 2, 'vettoken', '1716c4e9db7c33c171b8a7b9ff9acd9a705294b166970b9de597af85951113b8', '[\"*\"]', NULL, NULL, '2026-10-17 12:39:58', '2026-10-17 12:39:58'),
(8, 'App\\Models\\User', 2, 'vettoken', '4168b40efdc508a335ed2b611bfaf013434e8945d9e1394003ffad863ffd3225', '[\"*\"]', '2026-10-17 12:47:05', NULL, '2026-10-17 12:40:25', '2026-10-17 12:47:05'),
(9, 'App\\Models\\User', 1, 'vettoken', '0010ce5c17159bf36be187416893a62deaac3eaecf5e621a2f4912e83a5913b4', '[\"*\"]', '2026-10-19 08:58:44', NULL, '2026-10-19 08:56:34', '2026-10-19 08:58:44'),
(10, 'App\\Models\\User', 3, 'vettoken', 'a28972019d20094792887fdb936eb7c4338319ef2f1860f25cbc9ca3375ab27a', '[\"*\"]', NULL, NULL, '2026-10-19 08:59:13', '2026-10-19 08:59:13'),
(11, 'App\\Models\\User', 3, 'vettoken', 'eaa4a441cf65e546ed2f78bea88765151debf2cf79c5ee1afd552417913a38e1', '[\"*\"]', '2026-10-19 09:20:30', NULL, '2026-10-19 08:59:30', '2026-10-19 09:20:30'),
(12, 'App\\Models\\User', 1, 'vettoken', 'c328be4a8e62ceb0d65da3b8ad8f937053ef607aebe4dc69a832fadecc3fca59', '[\"*\"]', '2026-10-20 05:21:35', NULL, '2026-10-20 03:18:11', '2026-10-20 05:21:35'),
(13, 'App\\Models\\User', 1, 'vettoken', '55b36cbf863560f2fad620066f3a90bc4e0873339ab7c026bf99a93eb8e23fbe', '[\"*\"]', '2026-10-20 05:16:47', NULL, '2026-10-20 04:11:58', '2026-10-20 05:16:47'),
(14, 'App\\Models\\User', 1, 'vettoken', '183cea64e78c562667299891098b1c2835487a3826ef58d958c7c96f02183b3a', '[\"*\"]', NULL, NULL, '2026-10-20 05:21:54', '2026-10-20 05:21:54'),
(15, 'App\\Models\\User', 1, 'vettoken', '44209b9da0d6270a35336f08bb24148fd82c5a0cf5009a6822d04078f600e976', '[\"*\"]', NULL, NULL, '2026-10-20 05:22:13', '2026-10-20 05:22:13'),
(16, 'App\\Models\\User', 1, 'vettoken', '4a95348ee85a03954f72e1142319b87f033fb5567050a4cc0324264b0a6f0db2', '[\"*\"]', '2026-10-20 05:22:46', NULL, '2026-10-20 05:22:39', '2026-10-20 05:22:46'),
(17, 'App\\Models\\User', 4, 'vettoken', 'f345c350b3277df258f4f9cd76befd9bc85e3ba91ef9662964a49a67d5e1954a', '[\"*\"]', '2026-10-20 05:31:50', NULL, '2026-10-20 05:23:15', '2026-10-20 05:31:50'),
(18, 'App\\Models\\User', 1, 'vettoken', 'cd0c74d060fd14332c6e02c3ee00f259aa9d139b2adece6cc211630ce8c32c76', '[\"*\"]', NULL, NULL, '2026-10-20 05:23:49', '2026-10-20 05:23:49'),
(19, 'App\\Models\\User', 1, 'vettoken', 'e7dff1e6179cbf4d6d0be59f3fb5dd25fe1a00468619a6c2adc7778b03223d42', '[\"*\"]', NULL, NULL, '2026-10-20 05:24:46', '2026-10-20 05:24:46'),
(20, 'App\\Models\\User', 1, 'vettoken', '7501c9e14bf28a63c710c0a87e39cebc3d8299247af6b6b3e73d5b0d1aac8fb1', '[\"*\"]', NULL, NULL, '2026-10-20 05:25:34', '2026-10-20 05:25:34'),
(21, 'App\\Models\\User', 1, 'vettoken', 'd27ae7eabc7e5871b018289ec191885d5ad0c441096fe74a691e407bdd3e5db2', '[\"*\"]', NULL, NULL, '2026-10-20 05:25:51', '2026-10-20 05:25:51'),
(22, 'App\\Models\\User', 1, 'vettoken', '619c3748c6f5e5d6a4ff071b52dc05af0aedecf7e2bcfa15a7cfbe35d85febee', '[\"*\"]', '2026-10-20 06:11:38', NULL, '2026-10-20 05:29:51', '2026-10-20 06:11:38'),
(23, 'App\\Models\\User', 4, 'vettoken', '0f971757c68ac96a71d90396366d4cea5e7fcffb5e79988958cc7b81c04112b5', '[\"*\"]', '2026-10-20 05:37:20', NULL, '2026-10-20 05:37:11', '2026-10-20 05:37:20'),
(24, 'App\\Models\\User', 4, 'vettoken', 'f79ed419dd7ab48e1a8a51dac1c5d29ff7ea7133e8e6020c6a291e6e8eda2515', '[\"*\"]', '2026-10-20 06:11:49', NULL, '2026-10-20 05:46:13', '2026-10-20 06:11:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1yQVAdpiiYs3bNiBYBkXidsZM4ylF3O1XtvRVIpm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1VIaGtmWlRCWXZBT0Zwem5SMGJzUERGVTV4WmdBa1NvaDVGRlNJVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7czo1OiJyb3V0ZSI7czoxNToiYWRtaW4uZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1792447125),
('uIM7QFOxtpnSuVVOslqPDS7X3qgckSa8kPr0sqKZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieXQ2UGxNOEY1RHlVS0RLOE91dWFLVThwRkNiSmZqRTlzR3lZQ0dURyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbiI7czo1OiJyb3V0ZSI7czoxNToiYWRtaW4uZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2FkbWluIjt9fQ==', 1792455107);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL DEFAULT 'cliente',
  `telefono` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `rol`, `telefono`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador VetCare', 'admin@veterinaria.com', NULL, '$2y$12$2gdmnvitIcnSeGmzw3vJPuzwfso912t2NJP2SsgUjWxvEb3JP0UNm', 'admin', '77778888', NULL, '2026-10-17 10:47:42', '2026-10-20 05:25:09'),
(4, 'Miki', 'miki26@veterinaria.com', NULL, '$2y$12$3GDUbKTW.G2MjJKwFp5gH.iuNkgbVTp55UORp26/119toP8/v7aH.', 'cliente', NULL, NULL, '2026-10-20 05:23:15', '2026-10-20 05:23:15');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citas_user_id_foreign` (`user_id`),
  ADD KEY `citas_mascota_id_foreign` (`mascota_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `historial_medicos`
--
ALTER TABLE `historial_medicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historial_medicos_mascota_id_foreign` (`mascota_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mascotas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_medicos`
--
ALTER TABLE `historial_medicos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_mascota_id_foreign` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_medicos`
--
ALTER TABLE `historial_medicos`
  ADD CONSTRAINT `historial_medicos_mascota_id_foreign` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
