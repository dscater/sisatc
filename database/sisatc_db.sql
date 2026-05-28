-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 28-05-2026 a las 20:17:27
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sisatc_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activos`
--

CREATE TABLE `activos` (
  `id` bigint UNSIGNED NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_activo_id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activo_pruebas`
--

CREATE TABLE `activo_pruebas` (
  `id` bigint UNSIGNED NOT NULL,
  `activo_id` bigint UNSIGNED NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `modulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prueba` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracions`
--

CREATE TABLE `configuracions` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre_sistema` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracions`
--

INSERT INTO `configuracions` (`id`, `nombre_sistema`, `alias`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'SISATC', 'SISATC', 'logo.png', '2026-05-28 20:03:02', '2026-05-28 20:03:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ejecucion_archivos`
--

CREATE TABLE `ejecucion_archivos` (
  `id` bigint UNSIGNED NOT NULL,
  `ejecucion_trazabilidad_id` bigint UNSIGNED NOT NULL,
  `archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ejecucion_trazabilidads`
--

CREATE TABLE `ejecucion_trazabilidads` (
  `id` bigint UNSIGNED NOT NULL,
  `activo_id` bigint UNSIGNED NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trazabilidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamientos`
--

CREATE TABLE `entrenamientos` (
  `id` bigint UNSIGNED NOT NULL,
  `activo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo_id` bigint UNSIGNED DEFAULT NULL,
  `modulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_falla` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prueba` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resultado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `res` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_accions`
--

CREATE TABLE `historial_accions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `accion` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_original` json DEFAULT NULL,
  `datos_nuevo` json DEFAULT NULL,
  `modulo` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_accions`
--

INSERT INTO `historial_accions` (`id`, `user_id`, `accion`, `descripcion`, `datos_original`, `datos_nuevo`, `modulo`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN USUARIO', '{\"id\": 2, \"fono\": \"67676767\", \"foto\": \"21779999163.jpg\", \"tipo\": \"JEFE DE AREA\", \"email\": \"juan@gmail.com\", \"acceso\": \"1\", \"nombre\": \"JUAN\", \"usuario\": \"juan@gmail.com\", \"apellido\": \"PERES\", \"created_at\": \"2026-05-28T20:12:43.000000Z\", \"updated_at\": \"2026-05-28T20:12:43.000000Z\", \"fecha_registro\": \"2026-05-28\"}', NULL, 'USUARIOS', '2026-05-28', '16:12:43', '2026-05-28 20:12:43', '2026-05-28 20:12:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencias`
--

CREATE TABLE `incidencias` (
  `id` bigint UNSIGNED NOT NULL,
  `activo_id` bigint UNSIGNED NOT NULL,
  `modulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_falla` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prueba` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resultado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_31_165641_create_configuracions_table', 1),
(2, '2024_11_02_153317_create_users_table', 1),
(3, '2024_11_02_153318_create_historial_accions_table', 1),
(4, '2026_05_28_153738_create_tipo_activos_table', 1),
(5, '2026_05_28_153739_create_activos_table', 1),
(6, '2026_05_28_153804_create_activo_pruebas_table', 1),
(7, '2026_05_28_153842_create_ejecucion_trazabilidads_table', 1),
(8, '2026_05_28_153848_create_incidencias_table', 1),
(9, '2026_05_28_154155_create_entrenamientos_table', 1),
(10, '2026_05_28_154327_create_ejecucion_archivos_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_activos`
--

CREATE TABLE `tipo_activos` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `usuario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fono` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acceso` int NOT NULL DEFAULT '1',
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `usuario`, `nombre`, `apellido`, `email`, `fono`, `password`, `foto`, `acceso`, `tipo`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'admin', 'admin', '0', '$2y$12$Y5wsEe2xcdQGhVLM0kkfiOolft.ptlxxb8hD/WsWACN91wNI9FwT.', NULL, 1, 'ADMINISTRADOR', '2026-05-28', 1, '2026-05-28 20:03:02', '2026-05-28 20:03:02'),
(2, 'juan@gmail.com', 'JUAN', 'PERES', 'juan@gmail.com', '67676767', '$2y$12$1BqsaAqAyEttoAV07g4CAey5p4MmRnpYgDHdkbR6/ru7lIlr6Gx0a', '21779999163.jpg', 1, 'JEFE DE AREA', '2026-05-28', 1, '2026-05-28 20:12:43', '2026-05-28 20:12:43');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activos`
--
ALTER TABLE `activos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `activos_codigo_unique` (`codigo`),
  ADD KEY `activos_tipo_activo_id_foreign` (`tipo_activo_id`),
  ADD KEY `activos_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `activo_pruebas`
--
ALTER TABLE `activo_pruebas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activo_pruebas_activo_id_foreign` (`activo_id`),
  ADD KEY `activo_pruebas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ejecucion_archivos`
--
ALTER TABLE `ejecucion_archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ejecucion_archivos_ejecucion_trazabilidad_id_foreign` (`ejecucion_trazabilidad_id`);

--
-- Indices de la tabla `ejecucion_trazabilidads`
--
ALTER TABLE `ejecucion_trazabilidads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ejecucion_trazabilidads_activo_id_foreign` (`activo_id`),
  ADD KEY `ejecucion_trazabilidads_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entrenamientos_activo_id_foreign` (`activo_id`),
  ADD KEY `entrenamientos_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historial_accions_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incidencias_activo_id_foreign` (`activo_id`),
  ADD KEY `incidencias_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_activos`
--
ALTER TABLE `tipo_activos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activos`
--
ALTER TABLE `activos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `activo_pruebas`
--
ALTER TABLE `activo_pruebas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ejecucion_archivos`
--
ALTER TABLE `ejecucion_archivos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ejecucion_trazabilidads`
--
ALTER TABLE `ejecucion_trazabilidads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipo_activos`
--
ALTER TABLE `tipo_activos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activos`
--
ALTER TABLE `activos`
  ADD CONSTRAINT `activos_tipo_activo_id_foreign` FOREIGN KEY (`tipo_activo_id`) REFERENCES `tipo_activos` (`id`),
  ADD CONSTRAINT `activos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `activo_pruebas`
--
ALTER TABLE `activo_pruebas`
  ADD CONSTRAINT `activo_pruebas_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`),
  ADD CONSTRAINT `activo_pruebas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `ejecucion_archivos`
--
ALTER TABLE `ejecucion_archivos`
  ADD CONSTRAINT `ejecucion_archivos_ejecucion_trazabilidad_id_foreign` FOREIGN KEY (`ejecucion_trazabilidad_id`) REFERENCES `ejecucion_trazabilidads` (`id`);

--
-- Filtros para la tabla `ejecucion_trazabilidads`
--
ALTER TABLE `ejecucion_trazabilidads`
  ADD CONSTRAINT `ejecucion_trazabilidads_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`),
  ADD CONSTRAINT `ejecucion_trazabilidads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD CONSTRAINT `entrenamientos_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`),
  ADD CONSTRAINT `entrenamientos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD CONSTRAINT `historial_accions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD CONSTRAINT `incidencias_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`),
  ADD CONSTRAINT `incidencias_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
