-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Фев 07 2026 г., 17:32
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cabletorg`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `sort_order`) VALUES
(1, 'ВЛ', NULL, 1),
(2, 'СИЛ', NULL, 2),
(3, 'Оптика', NULL, 3),
(4, 'КГ', NULL, 4),
(5, 'Витая пара', NULL, 5);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_phone` varchar(30) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('new','paid','shipped','closed') DEFAULT 'new',
  `created_at` datetime DEFAULT current_timestamp(),
  `delivery` varchar(50) DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `user_name`, `user_phone`, `user_email`, `total_price`, `status`, `created_at`, `delivery`, `items`) VALUES
(1, 1, 'Алексей', '+7 999 765 33 32', '228228aleop@gmail.com', 470.00, '', '2026-02-07 00:22:39', 'самовывоз', '[{\"id\":8,\"name\":\"Силовой кабель ВВГ 3x4\",\"price\":110,\"image\":\"assets\\/images\\/product_8.jpg\",\"article\":\"SIL-VVG-004\",\"quantity\":1},{\"id\":11,\"name\":\"Силовой кабель ВВГ 3x2.5\",\"price\":300,\"image\":\"assets\\/images\\/product_11.jpg\",\"article\":\"SIL-VVG-016\",\"quantity\":1},{\"id\":31,\"name\":\"Витая пара UTP CAT6a\",\"price\":60,\"image\":\"assets\\/images\\/product_31.jpg\",\"article\":\"UTP-CAT6A-004\",\"quantity\":1}]'),
(2, 1, 'Дарья', '+79756345325', 'belibichudez@hotmail.com', 840.00, '', '2026-02-07 19:25:43', 'курьер', '[{\"id\":4,\"name\":\"Кабель ВЛ Алюминиевый\",\"price\":80,\"image\":\"assets\\/images\\/product_4.jpg\",\"article\":\"VL-ALU-004\",\"quantity\":1},{\"id\":11,\"name\":\"Силовой кабель ВВГ 3x2.5\",\"price\":300,\"image\":\"assets\\/images\\/product_11.jpg\",\"article\":\"SIL-VVG-016\",\"quantity\":1},{\"id\":2,\"name\":\"Кабель ВЛ 6кВ\",\"price\":100,\"image\":\"assets\\/images\\/product_2.jpg\",\"article\":\"VL-6KV-002\",\"quantity\":1},{\"id\":23,\"name\":\"Кабель гибкий КГ 3х2.5\",\"price\":180,\"image\":\"assets\\/images\\/product_23.jpg\",\"article\":\"KG-3X2.5\",\"quantity\":2}]');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `article` varchar(100) NOT NULL,
  `short_desc` varchar(255) DEFAULT NULL,
  `full_desc` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `unit` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specs`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `article`, `short_desc`, `full_desc`, `price`, `old_price`, `stock`, `unit`, `image`, `specs`, `created_at`) VALUES
(1, 1, 'Кабель ВЛ 10кВ', 'VL-10KV-001', 'Высоковольтный кабель', 'Кабель для линий электропередач 10кВ', 120.00, 150.00, 500, 'м', 'product_1.jpg', '{\"сечение\":\"10\",\"материал\":\"алюминий\"}', '2026-02-06 23:10:34'),
(2, 1, 'Кабель ВЛ 6кВ', 'VL-6KV-002', 'Высоковольтный кабель', 'Кабель для линий электропередач 6кВ', 100.00, 130.00, 400, 'м', 'product_2.jpg', '{\"сечение\":\"6\",\"материал\":\"алюминий\"}', '2026-02-06 23:10:34'),
(3, 1, 'Кабель ВЛ 0.4кВ', 'VL-0.4KV-003', 'Низковольтный кабель', 'Кабель для ЛЭП 0.4кВ', 60.00, NULL, 600, 'м', 'product_3.jpg', '{\"сечение\":\"4\",\"материал\":\"алюминий\"}', '2026-02-06 23:10:34'),
(4, 1, 'Кабель ВЛ Алюминиевый', 'VL-ALU-004', 'Алюминиевый кабель', 'Для воздушных линий', 80.00, 95.00, 300, 'м', 'product_4.jpg', '{\"сечение\":\"16\",\"материал\":\"алюминий\"}', '2026-02-06 23:10:34'),
(5, 1, 'Кабель ВЛ Медь 25мм²', 'VL-CU-005', 'Медный кабель', 'Для ЛЭП с высокой нагрузкой', 150.00, 170.00, 200, 'м', 'product_5.jpg', '{\"сечение\":\"25\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(6, 1, 'Кабель ВЛ Медь 35мм²', 'VL-CU-006', 'Медный кабель', 'Для ЛЭП с максимальной нагрузкой', 200.00, 230.00, 150, 'м', 'product_6.jpg', '{\"сечение\":\"35\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(7, 2, 'Силовой кабель ВВГ 2x2.5', 'SIL-VVG-002', 'Силовой кабель ВВГ', 'Медный силовой кабель ВВГ 2x2.5мм²', 90.00, NULL, 1200, 'м', 'product_7.jpg', '{\"сечение\":\"2.5\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(8, 2, 'Силовой кабель ВВГ 3x4', 'SIL-VVG-004', 'Силовой кабель ВВГ', 'Медный силовой кабель ВВГ 3x4мм²', 110.00, 130.00, 800, 'м', 'product_8.jpg', '{\"сечение\":\"4\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(9, 2, 'Силовой кабель ВВГ 2x6', 'SIL-VVG-006', 'Силовой кабель ВВГ', 'Медный силовой кабель ВВГ 2x6мм²', 140.00, 160.00, 600, 'м', 'product_9.jpg', '{\"сечение\":\"6\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(10, 2, 'Силовой кабель ВВГ 2x10', 'SIL-VVG-010', 'Силовой кабель ВВГ', 'Медный силовой кабель ВВГ 2x10мм²', 200.00, 230.00, 400, 'м', 'product_10.jpg', '{\"сечение\":\"10\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(11, 2, 'Силовой кабель ВВГ 3x2.5', 'SIL-VVG-016', 'Силовой кабель ВВГ', 'Медный силовой кабель ВВГ 3x2.5мм²', 300.00, 350.00, 200, 'м', 'product_11.jpg', '{\"сечение\":\"2.5\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(12, 2, 'Силовой кабель ВВГ 3x25', 'SIL-VVG-025', 'Силовой кабель ВВГ', 'Медный силовой кабель ВВГ 3x25мм²', 450.00, 500.00, 100, 'м', 'product_12.jpg', '{\"сечение\":\"25\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(13, 2, 'Силовой кабель ВВГ 4x35', 'SIL-VVG-035', 'Силовой кабель ВВГ', 'Медный силовой кабель ВВГ 4x35мм²', 600.00, 650.00, 50, 'м', 'product_13.jpg', '{\"сечение\":\"35\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(14, 3, 'Оптоволоконный кабель 4 волокна', 'OPT-FO-004', 'Оптический кабель', 'Кабель для передачи данных, 4 волокна', 45.00, NULL, 3000, 'м', 'product_14.jpg', '{\"волокна\":\"4\",\"тип\":\"одномодовый\"}', '2026-02-06 23:10:34'),
(15, 3, 'Оптоволоконный кабель 8 волокон', 'OPT-FO-008', 'Оптический кабель', 'Кабель для передачи данных, 8 волокон', 60.00, NULL, 2500, 'м', 'product_15.jpg', '{\"волокна\":\"8\",\"тип\":\"одномодовый\"}', '2026-02-06 23:10:34'),
(16, 3, 'Оптоволоконный кабель 12 волокон', 'OPT-FO-012', 'Оптический кабель', 'Кабель для передачи данных, 12 волокон', 80.00, 90.00, 2000, 'м', 'product_16.jpg', '{\"волокна\":\"12\",\"тип\":\"одномодовый\"}', '2026-02-06 23:10:34'),
(17, 3, 'Оптоволоконный кабель 24 волокна', 'OPT-FO-024', 'Оптический кабель', 'Кабель для передачи данных, 24 волокна', 120.00, 140.00, 1500, 'м', 'product_17.jpg', '{\"волокна\":\"24\",\"тип\":\"одномодовый\"}', '2026-02-06 23:10:34'),
(18, 3, 'Оптоволоконный кабель 48 волокон', 'OPT-FO-048', 'Оптический кабель', 'Кабель для передачи данных, 48 волокон', 200.00, 220.00, 1000, 'м', 'product_18.jpg', '{\"волокна\":\"48\",\"тип\":\"многомодовый\"}', '2026-02-06 23:10:34'),
(19, 3, 'Оптоволоконный кабель 96 волокон', 'OPT-FO-096', 'Оптический кабель', 'Кабель для передачи данных, 96 волокон', 350.00, 380.00, 500, 'м', 'product_19.jpg', '{\"волокна\":\"96\",\"тип\":\"многомодовый\"}', '2026-02-06 23:10:34'),
(20, 3, 'Оптоволоконный кабель 144 волокна', 'OPT-FO-144', 'Оптический кабель', 'Кабель для передачи данных, 144 волокна', 500.00, 550.00, 300, 'м', 'product_20.jpg', '{\"волокна\":\"144\",\"тип\":\"многомодовый\"}', '2026-02-06 23:10:34'),
(22, 4, 'Кабель гибкий КГ 2х2.5', 'KG-2X2.5', 'Гибкий кабель', 'Кабель повышенной гибкости 2х2.5мм²', 120.00, NULL, 600, 'м', 'product_22.jpg', '{\"сечение\":\"2.5\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(23, 4, 'Кабель гибкий КГ 3х2.5', 'KG-3X2.5', 'Гибкий кабель', 'Кабель повышенной гибкости 3х2.5мм²', 180.00, NULL, 500, 'м', 'product_23.jpg', '{\"сечение\":\"2.5\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(24, 4, 'Кабель гибкий КГ 4х4', 'KG-4X4', 'Гибкий кабель', 'Кабель повышенной гибкости 4х4мм²', 250.00, 270.00, 400, 'м', 'product_24.jpg', '{\"сечение\":\"4\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(25, 4, 'Кабель гибкий КГ 5х4', 'KG-5X6', 'Гибкий кабель', 'Кабель повышенной гибкости 5х4мм²', 400.00, 450.00, 200, 'м', 'product_25.jpg', '{\"сечение\":\"4\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(27, 4, 'Кабель гибкий КГ 1х16', 'KG-10X16', 'Гибкий кабель', 'Кабель повышенной гибкости 1х16мм²', 900.00, 1000.00, 100, 'м', 'product_27.jpg', '{\"сечение\":\"16\",\"материал\":\"медь\"}', '2026-02-06 23:10:34'),
(28, 5, 'Витая пара UTP CAT5e', 'UTP-CAT5E-001', 'Сетевой кабель', 'Кабель витая пара CAT5e', 20.00, NULL, 1500, 'м', 'product_28.jpg', '{\"категория\":\"5e\",\"экранирование\":\"UTP\"}', '2026-02-06 23:10:34'),
(29, 5, 'Витая пара UTP CAT6', 'UTP-CAT6-002', 'Сетевой кабель', 'Кабель витая пара CAT6', 35.00, NULL, 2000, 'м', 'product_29.jpg', '{\"категория\":\"6\",\"экранирование\":\"UTP\"}', '2026-02-06 23:10:34'),
(31, 5, 'Витая пара UTP CAT6a', 'UTP-CAT6A-004', 'Сетевой кабель', 'Кабель витая пара CAT6a', 60.00, 65.00, 800, 'м', 'product_31.jpg', '{\"категория\":\"6a\",\"экранирование\":\"UTP\"}', '2026-02-06 23:10:34'),
(32, 5, 'Витая пара FTP CAT7', 'FTP-CAT7-005', 'Сетевой кабель', 'Кабель витая пара CAT7', 90.00, 100.00, 500, 'м', 'product_32.jpg', '{\"категория\":\"7\",\"экранирование\":\"FTP\"}', '2026-02-06 23:10:34'),
(33, 5, 'Витая пара S/FTP CAT7', 'SFTP-CAT7-006', 'Сетевой кабель', 'Кабель витая пара S/FTP CAT7', 120.00, 130.00, 300, 'м', 'product_33.jpg', '{\"категория\":\"7\",\"экранирование\":\"S\\/FTP\"}', '2026-02-06 23:10:34');

-- --------------------------------------------------------

--
-- Структура таблицы `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `filename`) VALUES
(1, 33, 'product_33_1770409333_0.jpg'),
(2, 33, 'product_33_1770409337_0.jpg'),
(3, 32, 'product_32_1770409594_0.jpg'),
(4, 32, 'product_32_1770409598_0.jpg'),
(5, 31, 'product_31_1770409637_0.jpg'),
(6, 31, 'product_31_1770409641_0.jpg'),
(7, 29, 'product_29_1770409695_0.jpg'),
(8, 29, 'product_29_1770409699_0.jpg'),
(9, 28, 'product_28_1770409916_0.jpg'),
(10, 28, 'product_28_1770409920_0.jpg'),
(11, 28, 'product_28_1770409925_0.jpg'),
(12, 27, 'product_27_1770410146_0.jpg'),
(13, 27, 'product_27_1770410149_0.jpg'),
(14, 25, 'product_25_1770410419_0.jpg'),
(15, 25, 'product_25_1770410421_0.jpg'),
(16, 25, 'product_25_1770410425_0.jpg'),
(17, 24, 'product_24_1770410638_0.jpg'),
(18, 24, 'product_24_1770410641_0.jpg'),
(19, 23, 'product_23_1770410713_0.jpg'),
(20, 23, 'product_23_1770410717_0.jpg'),
(21, 22, 'product_22_1770410821_0.jpg'),
(22, 22, 'product_22_1770410826_0.jpg'),
(23, 20, 'product_20_1770410983_0.jpg'),
(24, 20, 'product_20_1770410986_0.jpg'),
(25, 19, 'product_19_1770411098_0.jpg'),
(26, 19, 'product_19_1770411102_0.jpg'),
(27, 18, 'product_18_1770411226_0.jpg'),
(28, 18, 'product_18_1770411231_0.jpg'),
(29, 17, 'product_17_1770411330_0.jpg'),
(30, 17, 'product_17_1770411333_0.jpg'),
(31, 16, 'product_16_1770411422_0.jpg'),
(32, 16, 'product_16_1770411425_0.jpg'),
(33, 15, 'product_15_1770411538_0.jpg'),
(34, 15, 'product_15_1770411542_0.jpg'),
(35, 14, 'product_14_1770411748_0.jpg'),
(36, 14, 'product_14_1770411752_0.jpg'),
(37, 13, 'product_13_1770411985_0.jpg'),
(38, 13, 'product_13_1770411990_0.jpg'),
(39, 12, 'product_12_1770412117_0.jpg'),
(40, 12, 'product_12_1770412120_0.jpg'),
(41, 11, 'product_11_1770412265_0.jpg'),
(42, 11, 'product_11_1770412268_0.jpg'),
(43, 10, 'product_10_1770412391_0.jpg'),
(44, 10, 'product_10_1770412394_0.jpg'),
(45, 9, 'product_9_1770412473_0.jpg'),
(46, 9, 'product_9_1770412476_0.jpg'),
(47, 8, 'product_8_1770412547_0.jpg'),
(48, 8, 'product_8_1770412552_0.jpg'),
(49, 7, 'product_7_1770412610_0.jpg'),
(50, 7, 'product_7_1770412616_0.jpg'),
(51, 6, 'product_6_1770480870_0.jpg'),
(52, 6, 'product_6_1770480874_0.jpg'),
(53, 5, 'product_5_1770480915_0.jpg'),
(54, 5, 'product_5_1770480920_0.jpg'),
(55, 4, 'product_4_1770480957_0.jpg'),
(56, 4, 'product_4_1770480959_0.jpg'),
(57, 3, 'product_3_1770481097_0.jpg'),
(58, 3, 'product_3_1770481101_0.jpg'),
(59, 2, 'product_2_1770481271_0.jpg'),
(60, 2, 'product_2_1770481275_0.jpg'),
(61, 1, 'product_1_1770481471_0.jpg'),
(62, 1, 'product_1_1770481475_0.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `is_admin`, `created_at`) VALUES
(1, '', 'svtmingyu@mail.ru', '+7 (977) 658-80-90', '$2y$10$2h9/EAFjdDPnirWpckde/e3YsxftPDo03Hu255YbYTGyeHuqRrOXu', 1, '2026-02-06 23:11:09'),
(2, '', 'admin@admin.com', '', '$2y$10$kKCXdJXs9K/z597T6tHqWeODigLkgyW0CyV/Kjo7LF13GUHkZnxU.', 1, '2026-02-07 19:27:38');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article` (`article`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT для таблицы `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
