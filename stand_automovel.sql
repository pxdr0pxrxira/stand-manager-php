-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2026 at 10:28 PM
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
-- Database: `stand_automovel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(2, 'admin', '$2y$10$Tl1y6E8iEJjkcRSFFAHste2qviK6lKOXV2q2B/dC.STpzAXZwhdt2', '2026-01-31 23:19:09');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(10) UNSIGNED NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `versao` varchar(100) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `ano` year(4) NOT NULL,
  `quilometros` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `combustivel` enum('Gasolina','Diesel','Híbrido','Elétrico','GPL') NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem_path` varchar(255) DEFAULT NULL,
  `vendido` tinyint(1) DEFAULT 0,
  `data_venda` timestamp NULL DEFAULT NULL,
  `reservado` tinyint(1) DEFAULT 0,
  `data_reserva` timestamp NULL DEFAULT NULL,
  `garantia` tinyint(1) DEFAULT 1,
  `iva_incluido` tinyint(1) DEFAULT 1,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  `matricula` varchar(20) DEFAULT NULL,
  `potencia` int(11) DEFAULT NULL COMMENT 'Potência em cavalos (cv)',
  `cilindrada` int(11) DEFAULT NULL COMMENT 'Cilindrada em cm³',
  `transmissao` varchar(50) DEFAULT NULL COMMENT 'Manual, Automática, Semi-automática',
  `tracao` varchar(50) DEFAULT NULL COMMENT 'Frente, Trás, 4x4',
  `portas` tinyint(4) DEFAULT NULL COMMENT 'Número de portas',
  `lugares` tinyint(4) DEFAULT NULL COMMENT 'Número de lugares',
  `cor` varchar(50) DEFAULT NULL COMMENT 'Cor exterior',
  `cor_interior` varchar(50) DEFAULT NULL COMMENT 'Cor interior',
  `consumo_medio` decimal(4,1) DEFAULT NULL COMMENT 'Consumo médio em L/100km',
  `emissoes_co2` int(11) DEFAULT NULL COMMENT 'Emissões CO2 em g/km',
  `segmento` varchar(50) DEFAULT NULL COMMENT 'Citadino, Berlina, SUV, etc'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `marca`, `modelo`, `versao`, `preco`, `ano`, `quilometros`, `combustivel`, `descricao`, `imagem_path`, `vendido`, `data_venda`, `reservado`, `data_reserva`, `garantia`, `iva_incluido`, `data_registo`, `matricula`, `potencia`, `cilindrada`, `transmissao`, `tracao`, `portas`, `lugares`, `cor`, `cor_interior`, `consumo_medio`, `emissoes_co2`, `segmento`) VALUES
(1, 'BMW', '320d', 'Sport Line', 28500.00, '2022', 45000, 'Diesel', 'Excelente estado, revisões na marca, único dono. Equipamento completo incluindo navegação, sensores de estacionamento e câmara traseira.', 'car_697fa03c596ad.jpg', 1, '2026-01-31 23:27:25', 0, NULL, 0, 0, '2026-01-31 22:39:43', '', NULL, NULL, '', '', NULL, NULL, '', '', NULL, NULL, ''),
(2, 'Mercedes-Benz', 'A180', 'AMG Line', 32000.00, '2023', 15000, 'Gasolina', 'Como novo, ainda com garantia de fábrica. Pack AMG exterior e interior, jantes 18\", LED Matrix.', 'car_697f90af83203.png', 0, NULL, 0, NULL, 0, 1, '2026-01-31 22:39:43', '22-44-KL', 100, 1600, 'Manual', 'Frente', 5, 4, 'Amarelo', 'Roxo', 10.0, 200, 'Citadino'),
(3, 'Volkswagen', 'Golf', 'GTI', 35000.00, '2023', 8000, 'Gasolina', 'Versão GTI com 245cv. Teto panorâmico, bancos desportivos, diferencial de deslizamento limitado.', 'car_697e8d810d5b4.jpg', 0, NULL, 0, NULL, 1, 1, '2026-01-31 22:39:43', '', 200, NULL, '', '', NULL, NULL, '', '', NULL, NULL, ''),
(4, 'Audi', 'A3 Sportback', 'S-Line', 29900.00, '2022', 32000, 'Diesel', 'Motor 2.0 TDI 150cv. Pack S-Line, Virtual Cockpit, CarPlay/Android Auto.', 'car_697f90bce5dad.jpg', 0, NULL, 0, NULL, 1, 0, '2026-01-31 22:39:43', '', NULL, NULL, '', '', NULL, NULL, '', '', NULL, NULL, ''),
(5, 'Tesla', 'Model 3', 'Long Range', 42000.00, '2023', 12000, 'Elétrico', 'Autonomia de 600km. Autopilot incluído, interior premium branco, jantes 19\".', 'car_697f90c5192ca.jpg', 1, '2026-02-01 19:54:08', 0, NULL, 1, 1, '2026-01-31 22:39:43', '', NULL, NULL, '', '', NULL, NULL, '', '', NULL, NULL, ''),
(6, 'Peugeot', '308', 'GT', 27500.00, '2022', 28000, 'Híbrido', 'Versão híbrida plug-in com 225cv. i-Cockpit 3D, Night Vision, suspensão adaptativa.', 'car_697f90cd5ee60.jpg', 0, NULL, 1, '2026-01-31 23:28:47', 1, 1, '2026-01-31 22:39:43', '', NULL, NULL, '', '', NULL, NULL, '', '', NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `car_images`
--

CREATE TABLE `car_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `car_id` int(10) UNSIGNED NOT NULL,
  `imagem_path` varchar(255) NOT NULL,
  `ordem` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_images`
--

INSERT INTO `car_images` (`id`, `car_id`, `imagem_path`, `ordem`, `created_at`) VALUES
(1, 3, 'car_697e8d810d313.jpg', 1, '2026-01-31 23:17:21'),
(2, 3, 'car_697e8d810d5b4.jpg', 2, '2026-01-31 23:17:21'),
(3, 3, 'car_697e8d810d81a.jpg', 3, '2026-01-31 23:17:21'),
(4, 3, 'car_697e8d810da46.jpg', 4, '2026-01-31 23:17:21'),
(6, 2, 'car_697f90af826d4.jpg', 2, '2026-02-01 17:43:11'),
(7, 2, 'car_697f90af82a92.jpg', 3, '2026-02-01 17:43:11'),
(8, 2, 'car_697f90af82e65.jpg', 4, '2026-02-01 17:43:11'),
(9, 2, 'car_697f90af83203.png', 5, '2026-02-01 17:43:11'),
(10, 2, 'car_697f90af83451.jpg', 6, '2026-02-01 17:43:11'),
(11, 2, 'car_697f90af83696.jpg', 7, '2026-02-01 17:43:11'),
(13, 4, 'car_697f90bce5dad.jpg', 1, '2026-02-01 17:43:24'),
(14, 4, 'car_697f90bce600d.jpg', 2, '2026-02-01 17:43:24'),
(15, 4, 'car_697f90bce6276.jpg', 3, '2026-02-01 17:43:24'),
(16, 4, 'car_697f90bce659d.png', 4, '2026-02-01 17:43:24'),
(17, 4, 'car_697f90bce6799.jpg', 5, '2026-02-01 17:43:24'),
(18, 4, 'car_697f90bce6d04.jpg', 6, '2026-02-01 17:43:24'),
(19, 5, 'car_697f90c5192ca.jpg', 1, '2026-02-01 17:43:33'),
(20, 5, 'car_697f90c519594.jpg', 2, '2026-02-01 17:43:33'),
(21, 5, 'car_697f90c5198a2.png', 3, '2026-02-01 17:43:33'),
(22, 5, 'car_697f90c519ad1.jpg', 4, '2026-02-01 17:43:33'),
(23, 5, 'car_697f90c519f88.jpg', 5, '2026-02-01 17:43:33'),
(24, 6, 'car_697f90cd5ee60.jpg', 1, '2026-02-01 17:43:41'),
(25, 6, 'car_697f90cd5f229.png', 2, '2026-02-01 17:43:41'),
(26, 6, 'car_697f90cd5f46c.jpg', 3, '2026-02-01 17:43:41'),
(27, 6, 'car_697f90cd5f6bd.jpg', 4, '2026-02-01 17:43:41'),
(28, 1, 'car_697fa03c593df.jpg', 1, '2026-02-01 18:49:32'),
(29, 1, 'car_697fa03c596ad.jpg', 2, '2026-02-01 18:49:32'),
(30, 1, 'car_697fa03c599f7.jpg', 3, '2026-02-01 18:49:32'),
(31, 2, 'car_697fa07ae719b.jpg', 8, '2026-02-01 18:50:34'),
(32, 2, 'car_697fa07ae7470.jpg', 9, '2026-02-01 18:50:34'),
(33, 2, 'car_697fa07aea835.jpg', 10, '2026-02-01 18:50:34'),
(34, 2, 'car_697fa07aeab82.jpg', 11, '2026-02-01 18:50:34'),
(35, 2, 'car_697fa07aeae08.jpg', 12, '2026-02-01 18:50:34'),
(36, 2, 'car_697fa07aeb15f.png', 13, '2026-02-01 18:50:34'),
(37, 2, 'car_697fa07aeb394.jpg', 14, '2026-02-01 18:50:34'),
(38, 2, 'car_697fa07aeb80d.jpg', 15, '2026-02-01 18:50:34'),
(39, 2, 'car_697fa07aec3f2.webp', 16, '2026-02-01 18:50:34'),
(40, 2, 'car_697fa0bd1f03b.jpg', 17, '2026-02-01 18:51:41'),
(41, 2, 'car_697fa0bd1f34d.jpg', 18, '2026-02-01 18:51:41'),
(42, 2, 'car_697fa0bd1f7a3.jpg', 19, '2026-02-01 18:51:41'),
(43, 2, 'car_697fa0bd1f9d3.jpg', 20, '2026-02-01 18:51:41'),
(44, 2, 'car_697fa0bd1fc70.jpeg', 21, '2026-02-01 18:51:41'),
(45, 2, 'car_697fa0bd1feca.jpg', 22, '2026-02-01 18:51:41'),
(46, 2, 'car_697fa0bd200dd.jpg', 23, '2026-02-01 18:51:41'),
(47, 2, 'car_697fa0bd20455.png', 24, '2026-02-01 18:51:41'),
(48, 2, 'car_697fa0bd20ae6.jpg', 25, '2026-02-01 18:51:41'),
(49, 2, 'car_697fa0bd20d2c.jpg', 26, '2026-02-01 18:51:41'),
(50, 2, 'car_697fa0bd21034.webp', 27, '2026-02-01 18:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `hero_images`
--

CREATE TABLE `hero_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_images`
--

INSERT INTO `hero_images` (`id`, `image_path`, `active`, `created_at`) VALUES
(7, 'hero_69810f3913399_0.jpeg', 1, '2026-02-02 20:55:21'),
(8, 'hero_69810fe9c2a8d_0.jpg', 1, '2026-02-02 20:58:17'),
(9, 'hero_69810fe9c31b6_1.jpg', 1, '2026-02-02 20:58:17'),
(10, 'hero_69810fe9c3840_2.jpg', 1, '2026-02-02 20:58:17'),
(11, 'hero_69810fe9c3f51_3.jpg', 1, '2026-02-02 20:58:17'),
(12, 'hero_69810fe9c441e_4.png', 1, '2026-02-02 20:58:17'),
(13, 'hero_69810fe9c48f7_5.jpeg', 1, '2026-02-02 20:58:17'),
(14, 'hero_69810fe9c4f57_6.jpg', 1, '2026-02-02 20:58:17');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_label` varchar(255) DEFAULT NULL,
  `setting_type` enum('text','email','tel','textarea','url') DEFAULT 'text',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_label`, `setting_type`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'Stand Automóvel', 'Nome da Empresa', 'text', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(2, 'company_email', 'stand.automovel@outlook.pt', 'Email da Empresa', 'email', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(3, 'company_phone', '+351 912 345 678', 'Telefone', 'tel', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(4, 'company_address', 'Rua Principal, 1231', 'Morada', 'text', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(5, 'company_city', '1000-001 Lisboa, Portugal', 'Cidade/Código Postal', 'text', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(6, 'company_hours', 'Segunda a Sexta: 9h - 19h\r\nSábado: 9h - 13h\r\nDomingo: 1h', 'Horário de Funcionamento', 'textarea', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(7, 'whatsapp_number', '351912345678', 'WhatsApp (com código país, sem +)', 'tel', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(8, 'facebook_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3014.7368951170743!2d-8.530861423578383!3d40.921514424762215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd2381aa0a154365%3A0xa99f6b2a8ae3b132!2sR.%20Casal%20de%20Matos!5e0!3m2!1spt-PT!2spt!4v1769969148311!5m2!1spt-PT!2spt\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade', 'Facebook URL', 'url', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(9, 'instagram_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3014.7368951170743!2d-8.530861423578383!3d40.921514424762215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd2381aa0a154365%3A0xa99f6b2a8ae3b132!2sR.%20Casal%20de%20Matos!5e0!3m2!1spt-PT!2spt!4v1769969148311!5m2!1spt-PT!2spt\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade', 'Instagram URL', 'url', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(10, 'maps_latitude', '38.7223', 'Latitude Google Maps', 'text', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(11, 'maps_longitude', '-9.1393', 'Longitude Google Maps', 'text', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(12, 'maps_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d40752.58917358124!2d-9.199706175645389!3d38.69637987794952!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd193477b40ec39b%3A0xb4c0704199e433d7!2sCastelo%20de%20S.%20Jorge!5e0!3m2!1spt-PT!2spt!4v1770067653604!5m2!1spt-PT!2spt\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade', 'Google Maps Embed URL', 'url', '2026-02-01 18:04:36', '2026-02-02 21:27:56'),
(32, 'semi_new_max_km', '20000', 'Máximo de Quilómetros para Semi-novo', '', '2026-02-01 19:21:13', '2026-02-02 21:27:56'),
(33, 'home_hero_subtitle', 'O seu stand de confiança com mais de 20 anos de experiência no mercado automóvel. Qualidade e transparência em primeiro lugar.1', NULL, 'text', '2026-02-02 21:19:40', '2026-02-02 21:27:56'),
(34, 'home_stats_years', '201+', NULL, 'text', '2026-02-02 21:19:40', '2026-02-02 21:27:56'),
(35, 'home_stats_customers', '20001+', NULL, 'text', '2026-02-02 21:19:40', '2026-02-02 21:27:56'),
(36, 'home_stats_warranty', '102%', NULL, 'text', '2026-02-02 21:19:40', '2026-02-02 21:27:56'),
(37, 'footer_text', 'O seu stand de confiança com mais de 20 anos de experiência no mercado automóvel. Qualidade e transparência em primeiro lugar.12', NULL, 'text', '2026-02-02 21:21:34', '2026-02-02 21:27:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_marca` (`marca`),
  ADD KEY `idx_preco` (`preco`),
  ADD KEY `idx_ano` (`ano`),
  ADD KEY `idx_vendido` (`vendido`),
  ADD KEY `idx_data_registo` (`data_registo`);

--
-- Indexes for table `car_images`
--
ALTER TABLE `car_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_car_id` (`car_id`),
  ADD KEY `idx_ordem` (`ordem`);

--
-- Indexes for table `hero_images`
--
ALTER TABLE `hero_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `car_images`
--
ALTER TABLE `car_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `hero_images`
--
ALTER TABLE `hero_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car_images`
--
ALTER TABLE `car_images`
  ADD CONSTRAINT `car_images_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
