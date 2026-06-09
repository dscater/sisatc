-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-06-2026 a las 17:02:12
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

--
-- Volcado de datos para la tabla `activos`
--

INSERT INTO `activos` (`id`, `codigo`, `nombre`, `descripcion`, `tipo_activo_id`, `version`, `user_id`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 'AC001', 'ACTIVO 1', 'DESC', 1, '1.0', 1, '2026-06-08', '2026-06-08 14:35:46', '2026-06-08 14:35:46'),
(2, 'A002', 'SISTEMA 2', 'DESC', 3, '1', 1, '2026-06-09', '2026-06-09 15:03:51', '2026-06-09 15:03:51');

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

--
-- Volcado de datos para la tabla `activo_pruebas`
--

INSERT INTO `activo_pruebas` (`id`, `activo_id`, `descripcion`, `modulo`, `prueba`, `user_id`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 1, 'DESCRIPCION', 'MODULO 1', 'PRUEBA', 1, '2026-06-08', '11:15:24', '2026-06-08 15:15:24', '2026-06-08 15:18:51');

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
  `archivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ejecucion_archivos`
--

INSERT INTO `ejecucion_archivos` (`id`, `ejecucion_trazabilidad_id`, `archivo`, `created_at`, `updated_at`) VALUES
(1, 2, '11781019019.pdf', '2026-06-09 15:30:19', '2026-06-09 15:30:19'),
(2, 2, '121781019019.pdf', '2026-06-09 15:30:19', '2026-06-09 15:30:19'),
(3, 3, '31781019045.pdf', '2026-06-09 15:30:45', '2026-06-09 15:30:45'),
(4, 3, '141781019045.pdf', '2026-06-09 15:30:45', '2026-06-09 15:30:45');

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

--
-- Volcado de datos para la tabla `ejecucion_trazabilidads`
--

INSERT INTO `ejecucion_trazabilidads` (`id`, `activo_id`, `estado`, `trazabilidad`, `user_id`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(2, 1, 'INICIO', '0', 1, '2026-06-09', '11:30:19', '2026-06-09 15:30:19', '2026-06-09 15:30:19'),
(3, 2, 'INICIO', '0', 1, '2026-06-09', '11:30:45', '2026-06-09 15:30:45', '2026-06-09 15:48:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamientos`
--

CREATE TABLE `entrenamientos` (
  `id` bigint UNSIGNED NOT NULL,
  `tipo_activo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_activo_id` bigint UNSIGNED DEFAULT NULL,
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

--
-- Volcado de datos para la tabla `entrenamientos`
--

INSERT INTO `entrenamientos` (`id`, `tipo_activo`, `tipo_activo_id`, `modulo`, `tipo_falla`, `severidad`, `prueba`, `resultado`, `bug`, `estado`, `res`, `user_id`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 'api de pagos', 3, 'Pagos', 'Timeout', 'Critica', 'Api Payments', 'FAIL', 'SI', '1', 'IMPORTADO', 1, '2026-06-09', '10:45:57', '2026-06-09 14:45:57', '2026-06-09 14:45:57'),
(2, 'api de pagos', 3, 'Pagos', 'TimeOut', 'Critica', 'Api Payments', 'PASS', 'NO', '1', 'IMPORTADO', 1, '2026-06-09', '10:46:00', '2026-06-09 14:46:00', '2026-06-09 14:46:00'),
(3, 'api de pagos', 3, 'Pagos', 'Timeout', 'Critica', 'Api Payments', 'FAIL', 'SI', '1', 'IMPORTADO', 1, '2026-06-09', '10:46:03', '2026-06-09 14:46:03', '2026-06-09 14:46:03'),
(4, 'Api autenticacion', 4, 'Clientes', 'Validacion', 'Media', 'Form Validation', 'FAIL', 'SI', '1', 'IMPORTADO', 1, '2026-06-09', '10:46:06', '2026-06-09 14:46:06', '2026-06-09 14:46:06'),
(5, 'api de pagos', 3, 'Pagos', 'Timeout', 'Critica', 'Api Payments', 'FAIL', 'SI', '1', 'IMPORTADO', 1, '2026-06-09', '10:46:57', '2026-06-09 14:46:57', '2026-06-09 14:46:57'),
(6, 'api de pagos', 3, 'Pagos', 'TimeOut', 'Critica', 'Api Payments', 'PASS', 'NO', '1', 'IMPORTADO', 1, '2026-06-09', '10:47:00', '2026-06-09 14:47:00', '2026-06-09 14:47:00'),
(7, 'api de pagos', 3, 'Pagos', 'Timeout', 'Critica', 'Api Payments', 'FAIL', 'SI', '1', 'IMPORTADO', 1, '2026-06-09', '10:47:03', '2026-06-09 14:47:03', '2026-06-09 14:47:03'),
(8, 'Api autenticacion', 4, 'Clientes', 'Validacion', 'Media', 'Form Validation', 'FAIL', 'SI', '1', 'IMPORTADO', 1, '2026-06-09', '10:47:06', '2026-06-09 14:47:06', '2026-06-09 14:47:06');

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
(1, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN USUARIO', '{\"id\": 2, \"fono\": \"67676767\", \"foto\": \"21779999163.jpg\", \"tipo\": \"JEFE DE AREA\", \"email\": \"juan@gmail.com\", \"acceso\": \"1\", \"nombre\": \"JUAN\", \"usuario\": \"juan@gmail.com\", \"apellido\": \"PERES\", \"created_at\": \"2026-05-28T20:12:43.000000Z\", \"updated_at\": \"2026-05-28T20:12:43.000000Z\", \"fecha_registro\": \"2026-05-28\"}', NULL, 'USUARIOS', '2026-05-28', '16:12:43', '2026-05-28 20:12:43', '2026-05-28 20:12:43'),
(2, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN TIPO DE ACTIVO', '{\"id\": 1, \"nombre\": \"Tipo activo 1\", \"created_at\": \"2026-06-08T14:18:27.000000Z\", \"updated_at\": \"2026-06-08T14:18:27.000000Z\"}', NULL, 'TIPO DE ACTIVOS', '2026-06-08', '10:18:27', '2026-06-08 14:18:27', '2026-06-08 14:18:27'),
(3, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN TIPO DE ACTIVO', '{\"id\": 1, \"nombre\": \"Tipo activo 1\", \"created_at\": \"2026-06-08T14:18:27.000000Z\", \"updated_at\": \"2026-06-08T14:18:27.000000Z\"}', '{\"id\": 1, \"nombre\": \"Tipo activo 1 asd\", \"created_at\": \"2026-06-08T14:18:27.000000Z\", \"updated_at\": \"2026-06-08T14:18:31.000000Z\"}', 'TIPO DE ACTIVOS', '2026-06-08', '10:18:31', '2026-06-08 14:18:31', '2026-06-08 14:18:31'),
(4, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN TIPO DE ACTIVO', '{\"id\": 1, \"nombre\": \"tipo de activo 1\", \"created_at\": \"2026-06-08T14:18:49.000000Z\", \"updated_at\": \"2026-06-08T14:18:49.000000Z\"}', NULL, 'TIPO DE ACTIVOS', '2026-06-08', '10:18:49', '2026-06-08 14:18:49', '2026-06-08 14:18:49'),
(5, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN TIPO DE ACTIVO', '{\"id\": 2, \"nombre\": \"tipo activo 2\", \"created_at\": \"2026-06-08T14:18:54.000000Z\", \"updated_at\": \"2026-06-08T14:18:54.000000Z\"}', NULL, 'TIPO DE ACTIVOS', '2026-06-08', '10:18:54', '2026-06-08 14:18:54', '2026-06-08 14:18:54'),
(6, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN TIPO DE ACTIVO', '{\"id\": 1, \"codigo\": \"AC001\", \"nombre\": \"ACTIVO 1\", \"user_id\": 1, \"version\": \"1.0\", \"created_at\": \"2026-06-08T14:32:34.000000Z\", \"updated_at\": \"2026-06-08T14:32:34.000000Z\", \"descripcion\": \"DESCRIPCION\", \"fecha_registro\": \"2026-06-08\", \"tipo_activo_id\": \"1\"}', NULL, 'TIPO DE ACTIVOS', '2026-06-08', '10:32:34', '2026-06-08 14:32:34', '2026-06-08 14:32:34'),
(7, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN TIPO DE ACTIVO', '{\"id\": 1, \"codigo\": \"AC001\", \"nombre\": \"ACTIVO 1\", \"user_id\": 1, \"version\": \"1.0\", \"created_at\": \"2026-06-08T14:32:34.000000Z\", \"updated_at\": \"2026-06-08T14:32:34.000000Z\", \"descripcion\": \"DESCRIPCION\", \"fecha_registro\": \"2026-06-08\", \"tipo_activo_id\": 1}', '{\"id\": 1, \"codigo\": \"AC001\", \"nombre\": \"ACTIVO 1\", \"user_id\": 1, \"version\": \"1.0\", \"created_at\": \"2026-06-08T14:32:34.000000Z\", \"updated_at\": \"2026-06-08T14:35:21.000000Z\", \"descripcion\": \"DESCRIPCION\", \"fecha_registro\": \"2026-06-08\", \"tipo_activo_id\": \"2\"}', 'TIPO DE ACTIVOS', '2026-06-08', '10:35:21', '2026-06-08 14:35:21', '2026-06-08 14:35:21'),
(8, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN TIPO DE ACTIVO', '{\"id\": 1, \"codigo\": \"AC001\", \"nombre\": \"ACTIVO 1\", \"user_id\": 1, \"version\": \"1.0\", \"created_at\": \"2026-06-08T14:35:46.000000Z\", \"updated_at\": \"2026-06-08T14:35:46.000000Z\", \"descripcion\": \"DESC\", \"fecha_registro\": \"2026-06-08\", \"tipo_activo_id\": \"1\"}', NULL, 'TIPO DE ACTIVOS', '2026-06-08', '10:35:46', '2026-06-08 14:35:46', '2026-06-08 14:35:46'),
(9, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 1, \"hora\": \"11:15:24\", \"fecha\": \"2026-06-08\", \"modulo\": \"MODULO 1\", \"prueba\": \"PRUEBA\", \"user_id\": 1, \"activo_id\": 1, \"created_at\": \"2026-06-08T15:15:24.000000Z\", \"updated_at\": \"2026-06-08T15:15:24.000000Z\", \"descripcion\": \"DESCRIPCION\"}', NULL, 'GUIONES DE PRUEBAS', '2026-06-08', '11:15:24', '2026-06-08 15:15:24', '2026-06-08 15:15:24'),
(10, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 2, \"hora\": \"11:15:50\", \"fecha\": \"2026-06-08\", \"modulo\": \"MODULO 2\", \"prueba\": \"PRUEBA 2\", \"user_id\": 1, \"activo_id\": 1, \"created_at\": \"2026-06-08T15:15:50.000000Z\", \"updated_at\": \"2026-06-08T15:15:50.000000Z\", \"descripcion\": \"DESC 2\"}', NULL, 'GUIONES DE PRUEBAS', '2026-06-08', '11:15:50', '2026-06-08 15:15:50', '2026-06-08 15:15:50'),
(11, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN GUIÓN DE PRUEBA', '{\"id\": 1, \"hora\": \"11:15:24\", \"fecha\": \"2026-06-08\", \"modulo\": \"MODULO 1\", \"prueba\": \"PRUEBA\", \"user_id\": 1, \"activo_id\": 1, \"created_at\": \"2026-06-08T15:15:24.000000Z\", \"updated_at\": \"2026-06-08T15:15:24.000000Z\", \"descripcion\": \"DESCRIPCION\"}', '{\"id\": 1, \"hora\": \"11:15:24\", \"fecha\": \"2026-06-08\", \"modulo\": \"MODULO 1\", \"prueba\": \"PRUEBA\", \"user_id\": 1, \"activo_id\": 1, \"created_at\": \"2026-06-08T15:15:24.000000Z\", \"updated_at\": \"2026-06-08T15:18:46.000000Z\", \"descripcion\": \"DESCRIPCIONASD\"}', 'GUIONES DE PRUEBAS', '2026-06-08', '11:18:46', '2026-06-08 15:18:46', '2026-06-08 15:18:46'),
(12, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN GUIÓN DE PRUEBA', '{\"id\": 1, \"hora\": \"11:15:24\", \"fecha\": \"2026-06-08\", \"modulo\": \"MODULO 1\", \"prueba\": \"PRUEBA\", \"user_id\": 1, \"activo_id\": 1, \"created_at\": \"2026-06-08T15:15:24.000000Z\", \"updated_at\": \"2026-06-08T15:18:46.000000Z\", \"descripcion\": \"DESCRIPCIONASD\"}', '{\"id\": 1, \"hora\": \"11:15:24\", \"fecha\": \"2026-06-08\", \"modulo\": \"MODULO 1\", \"prueba\": \"PRUEBA\", \"user_id\": 1, \"activo_id\": 1, \"created_at\": \"2026-06-08T15:15:24.000000Z\", \"updated_at\": \"2026-06-08T15:18:51.000000Z\", \"descripcion\": \"DESCRIPCION\"}', 'GUIONES DE PRUEBAS', '2026-06-08', '11:18:51', '2026-06-08 15:18:51', '2026-06-08 15:18:51'),
(13, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 1, \"bug\": \"SI\", \"res\": \"IMPORTADO\", \"hora\": \"10:45:57\", \"fecha\": \"2026-06-09\", \"estado\": 1, \"modulo\": \"Pagos\", \"prueba\": \"Api Payments\", \"user_id\": 1, \"resultado\": \"FAIL\", \"severidad\": \"Critica\", \"created_at\": \"2026-06-09T14:45:57.000000Z\", \"tipo_falla\": \"Timeout\", \"updated_at\": \"2026-06-09T14:45:57.000000Z\", \"tipo_activo\": \"api de pagos\", \"tipo_activo_id\": 3}', NULL, 'ENTRENAMIENTOS', '2026-06-09', '10:46:00', '2026-06-09 14:46:00', '2026-06-09 14:46:00'),
(14, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 2, \"bug\": \"NO\", \"res\": \"IMPORTADO\", \"hora\": \"10:46:00\", \"fecha\": \"2026-06-09\", \"estado\": 1, \"modulo\": \"Pagos\", \"prueba\": \"Api Payments\", \"user_id\": 1, \"resultado\": \"PASS\", \"severidad\": \"Critica\", \"created_at\": \"2026-06-09T14:46:00.000000Z\", \"tipo_falla\": \"TimeOut\", \"updated_at\": \"2026-06-09T14:46:00.000000Z\", \"tipo_activo\": \"api de pagos\", \"tipo_activo_id\": 3}', NULL, 'ENTRENAMIENTOS', '2026-06-09', '10:46:03', '2026-06-09 14:46:03', '2026-06-09 14:46:03'),
(15, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 3, \"bug\": \"SI\", \"res\": \"IMPORTADO\", \"hora\": \"10:46:03\", \"fecha\": \"2026-06-09\", \"estado\": 1, \"modulo\": \"Pagos\", \"prueba\": \"Api Payments\", \"user_id\": 1, \"resultado\": \"FAIL\", \"severidad\": \"Critica\", \"created_at\": \"2026-06-09T14:46:03.000000Z\", \"tipo_falla\": \"Timeout\", \"updated_at\": \"2026-06-09T14:46:03.000000Z\", \"tipo_activo\": \"api de pagos\", \"tipo_activo_id\": 3}', NULL, 'ENTRENAMIENTOS', '2026-06-09', '10:46:06', '2026-06-09 14:46:06', '2026-06-09 14:46:06'),
(16, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 4, \"bug\": \"SI\", \"res\": \"IMPORTADO\", \"hora\": \"10:46:06\", \"fecha\": \"2026-06-09\", \"estado\": 1, \"modulo\": \"Clientes\", \"prueba\": \"Form Validation\", \"user_id\": 1, \"resultado\": \"FAIL\", \"severidad\": \"Media\", \"created_at\": \"2026-06-09T14:46:06.000000Z\", \"tipo_falla\": \"Validacion\", \"updated_at\": \"2026-06-09T14:46:06.000000Z\", \"tipo_activo\": \"Api autenticacion\", \"tipo_activo_id\": 4}', NULL, 'ENTRENAMIENTOS', '2026-06-09', '10:46:09', '2026-06-09 14:46:09', '2026-06-09 14:46:09'),
(17, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 5, \"bug\": \"SI\", \"res\": \"IMPORTADO\", \"hora\": \"10:46:57\", \"fecha\": \"2026-06-09\", \"estado\": 1, \"modulo\": \"Pagos\", \"prueba\": \"Api Payments\", \"user_id\": 1, \"resultado\": \"FAIL\", \"severidad\": \"Critica\", \"created_at\": \"2026-06-09T14:46:57.000000Z\", \"tipo_falla\": \"Timeout\", \"updated_at\": \"2026-06-09T14:46:57.000000Z\", \"tipo_activo\": \"api de pagos\", \"tipo_activo_id\": 3}', NULL, 'ENTRENAMIENTOS', '2026-06-09', '10:47:00', '2026-06-09 14:47:00', '2026-06-09 14:47:00'),
(18, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 6, \"bug\": \"NO\", \"res\": \"IMPORTADO\", \"hora\": \"10:47:00\", \"fecha\": \"2026-06-09\", \"estado\": 1, \"modulo\": \"Pagos\", \"prueba\": \"Api Payments\", \"user_id\": 1, \"resultado\": \"PASS\", \"severidad\": \"Critica\", \"created_at\": \"2026-06-09T14:47:00.000000Z\", \"tipo_falla\": \"TimeOut\", \"updated_at\": \"2026-06-09T14:47:00.000000Z\", \"tipo_activo\": \"api de pagos\", \"tipo_activo_id\": 3}', NULL, 'ENTRENAMIENTOS', '2026-06-09', '10:47:03', '2026-06-09 14:47:03', '2026-06-09 14:47:03'),
(19, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 7, \"bug\": \"SI\", \"res\": \"IMPORTADO\", \"hora\": \"10:47:03\", \"fecha\": \"2026-06-09\", \"estado\": 1, \"modulo\": \"Pagos\", \"prueba\": \"Api Payments\", \"user_id\": 1, \"resultado\": \"FAIL\", \"severidad\": \"Critica\", \"created_at\": \"2026-06-09T14:47:03.000000Z\", \"tipo_falla\": \"Timeout\", \"updated_at\": \"2026-06-09T14:47:03.000000Z\", \"tipo_activo\": \"api de pagos\", \"tipo_activo_id\": 3}', NULL, 'ENTRENAMIENTOS', '2026-06-09', '10:47:06', '2026-06-09 14:47:06', '2026-06-09 14:47:06'),
(20, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBAadmin REGISTRO UN GUIÓN DE PRUEBA', '{\"id\": 8, \"bug\": \"SI\", \"res\": \"IMPORTADO\", \"hora\": \"10:47:06\", \"fecha\": \"2026-06-09\", \"estado\": 1, \"modulo\": \"Clientes\", \"prueba\": \"Form Validation\", \"user_id\": 1, \"resultado\": \"FAIL\", \"severidad\": \"Media\", \"created_at\": \"2026-06-09T14:47:06.000000Z\", \"tipo_falla\": \"Validacion\", \"updated_at\": \"2026-06-09T14:47:06.000000Z\", \"tipo_activo\": \"Api autenticacion\", \"tipo_activo_id\": 4}', NULL, 'ENTRENAMIENTOS', '2026-06-09', '10:47:09', '2026-06-09 14:47:09', '2026-06-09 14:47:09'),
(21, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA INCIDENCIA', '{\"id\": 1, \"bug\": \"SI\", \"hora\": \"11:00:21\", \"fecha\": \"2026-06-09\", \"estado\": \"ABIERTO\", \"modulo\": \"tipo 1\", \"prueba\": \"prueba\", \"user_id\": 1, \"resultado\": \"fail\", \"severidad\": \"severidad 1\", \"created_at\": \"2026-06-09T15:00:21.000000Z\", \"tipo_falla\": \"falla 1\", \"updated_at\": \"2026-06-09T15:00:21.000000Z\", \"tipo_activo_id\": \"1\"}', NULL, 'INCIDENCIAS', '2026-06-09', '11:00:21', '2026-06-09 15:00:21', '2026-06-09 15:00:21'),
(22, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA INCIDENCIA', '{\"id\": 1, \"bug\": \"SI\", \"hora\": \"11:00:21\", \"fecha\": \"2026-06-09\", \"estado\": \"ABIERTO\", \"modulo\": \"tipo 1\", \"prueba\": \"prueba\", \"user_id\": 1, \"resultado\": \"fail\", \"severidad\": \"severidad 1\", \"created_at\": \"2026-06-09T15:00:21.000000Z\", \"tipo_falla\": \"falla 1\", \"updated_at\": \"2026-06-09T15:00:21.000000Z\", \"tipo_activo_id\": 1}', '{\"id\": 1, \"bug\": \"SI\", \"hora\": \"11:00:21\", \"fecha\": \"2026-06-09\", \"estado\": \"EN CORRECCIÓN\", \"modulo\": \"tipo 1\", \"prueba\": \"prueba\", \"user_id\": 1, \"resultado\": \"fail\", \"severidad\": \"severidad 1\", \"created_at\": \"2026-06-09T15:00:21.000000Z\", \"tipo_falla\": \"falla 1\", \"updated_at\": \"2026-06-09T15:02:38.000000Z\", \"tipo_activo_id\": \"1\"}', 'INCIDENCIAS', '2026-06-09', '11:02:38', '2026-06-09 15:02:38', '2026-06-09 15:02:38'),
(23, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA INCIDENCIA', '{\"id\": 1, \"bug\": \"SI\", \"hora\": \"11:03:25\", \"fecha\": \"2026-06-09\", \"estado\": \"ABIERTO\", \"modulo\": \"modulo 1\", \"prueba\": \"prueba\", \"user_id\": 1, \"resultado\": \"res\", \"severidad\": \"severidad 1\", \"created_at\": \"2026-06-09T15:03:25.000000Z\", \"tipo_falla\": \"falla 1\", \"updated_at\": \"2026-06-09T15:03:25.000000Z\", \"tipo_activo_id\": \"1\"}', NULL, 'INCIDENCIAS', '2026-06-09', '11:03:25', '2026-06-09 15:03:25', '2026-06-09 15:03:25'),
(24, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN ACTIVO Y CONFIGURACIÓN', '{\"id\": 2, \"codigo\": \"A002\", \"nombre\": \"SISTEMA 2\", \"user_id\": 1, \"version\": \"1\", \"created_at\": \"2026-06-09T15:03:51.000000Z\", \"updated_at\": \"2026-06-09T15:03:51.000000Z\", \"descripcion\": \"DESC\", \"fecha_registro\": \"2026-06-09\", \"tipo_activo_id\": \"3\"}', NULL, 'ACTIVOS Y CONFIGURACIÓN', '2026-06-09', '11:03:51', '2026-06-09 15:03:51', '2026-06-09 15:03:51'),
(25, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA INCIDENCIA', '{\"id\": 2, \"hora\": \"11:30:19\", \"fecha\": \"2026-06-09\", \"estado\": \"INICIO\", \"user_id\": 1, \"activo_id\": \"1\", \"created_at\": \"2026-06-09T15:30:19.000000Z\", \"updated_at\": \"2026-06-09T15:30:19.000000Z\", \"trazabilidad\": 0}', NULL, 'INCIDENCIAS', '2026-06-09', '11:30:19', '2026-06-09 15:30:19', '2026-06-09 15:30:19'),
(26, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA INCIDENCIA', '{\"id\": 3, \"hora\": \"11:30:45\", \"fecha\": \"2026-06-09\", \"estado\": \"INICIO\", \"user_id\": 1, \"activo_id\": \"1\", \"created_at\": \"2026-06-09T15:30:45.000000Z\", \"updated_at\": \"2026-06-09T15:30:45.000000Z\", \"trazabilidad\": 0}', NULL, 'INCIDENCIAS', '2026-06-09', '11:30:45', '2026-06-09 15:30:45', '2026-06-09 15:30:45'),
(27, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA INCIDENCIA', '{\"id\": 3, \"hora\": \"11:30:45\", \"fecha\": \"2026-06-09\", \"estado\": \"INICIO\", \"user_id\": 1, \"activo_id\": 1, \"created_at\": \"2026-06-09T15:30:45.000000Z\", \"updated_at\": \"2026-06-09T15:30:45.000000Z\", \"trazabilidad\": \"0\"}', '{\"id\": 3, \"hora\": \"11:30:45\", \"fecha\": \"2026-06-09\", \"estado\": \"INICIO\", \"user_id\": 1, \"activo_id\": \"2\", \"created_at\": \"2026-06-09T15:30:45.000000Z\", \"updated_at\": \"2026-06-09T15:48:18.000000Z\", \"trazabilidad\": \"0\"}', 'INCIDENCIAS', '2026-06-09', '11:48:18', '2026-06-09 15:48:18', '2026-06-09 15:48:18'),
(28, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA INCIDENCIA', '{\"id\": 2, \"bug\": \"SI\", \"hora\": \"11:48:57\", \"fecha\": \"2026-06-09\", \"estado\": \"ABIERTO\", \"modulo\": \"modulo prueba\", \"prueba\": \"prueba\", \"user_id\": 1, \"resultado\": \"res\", \"severidad\": \"media\", \"created_at\": \"2026-06-09T15:48:57.000000Z\", \"tipo_falla\": \"falla\", \"updated_at\": \"2026-06-09T15:48:57.000000Z\", \"tipo_activo_id\": \"3\"}', NULL, 'INCIDENCIAS', '2026-06-09', '11:48:57', '2026-06-09 15:48:57', '2026-06-09 15:48:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencias`
--

CREATE TABLE `incidencias` (
  `id` bigint UNSIGNED NOT NULL,
  `tipo_activo_id` bigint UNSIGNED NOT NULL,
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

--
-- Volcado de datos para la tabla `incidencias`
--

INSERT INTO `incidencias` (`id`, `tipo_activo_id`, `modulo`, `tipo_falla`, `severidad`, `prueba`, `resultado`, `bug`, `estado`, `user_id`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 1, 'modulo 1', 'falla 1', 'severidad 1', 'prueba', 'res', 'SI', 'ABIERTO', 1, '2026-06-09', '11:03:25', '2026-06-09 15:03:25', '2026-06-09 15:03:25'),
(2, 3, 'modulo prueba', 'falla', 'media', 'prueba', 'res', 'SI', 'ABIERTO', 1, '2026-06-09', '11:48:57', '2026-06-09 15:48:57', '2026-06-09 15:48:57');

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

--
-- Volcado de datos para la tabla `tipo_activos`
--

INSERT INTO `tipo_activos` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'tipo de activo 1', '2026-06-08 14:18:49', '2026-06-08 14:18:49'),
(2, 'tipo activo 2', '2026-06-08 14:18:54', '2026-06-08 14:18:54'),
(3, 'api de pagos', '2026-06-09 14:45:57', '2026-06-09 14:45:57'),
(4, 'Api autenticacion', '2026-06-09 14:46:06', '2026-06-09 14:46:06');

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `activo_pruebas`
--
ALTER TABLE `activo_pruebas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ejecucion_archivos`
--
ALTER TABLE `ejecucion_archivos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ejecucion_trazabilidads`
--
ALTER TABLE `ejecucion_trazabilidads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipo_activos`
--
ALTER TABLE `tipo_activos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  ADD CONSTRAINT `incidencias_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
