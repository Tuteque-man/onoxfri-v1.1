-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-10-2025 a las 04:26:28
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
-- Base de datos: `onoxfri`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'General', '2025-10-04 20:47:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configurations`
--

CREATE TABLE `configurations` (
  `key` varchar(190) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `custom_transactions`
--

CREATE TABLE `custom_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(40) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reference` varchar(190) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `import_jobs`
--

CREATE TABLE `import_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','running','done','failed') NOT NULL DEFAULT 'pending',
  `total` int(11) NOT NULL DEFAULT 0,
  `success` int(11) NOT NULL DEFAULT 0,
  `failed` int(11) NOT NULL DEFAULT 0,
  `report_url` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventory_movements`
--

CREATE TABLE `inventory_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('in','out','adjust') NOT NULL,
  `qty` int(11) NOT NULL,
  `reference` varchar(120) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `inventory_movements`
--
DELIMITER $$
CREATE TRIGGER `trg_inventory_movements_after_insert` AFTER INSERT ON `inventory_movements` FOR EACH ROW BEGIN
  INSERT INTO stock_levels (product_id, quantity_on_hand, reserved, updated_at)
  VALUES (NEW.product_id, 0, 0, NOW())
  ON DUPLICATE KEY UPDATE product_id = product_id;

  IF NEW.type = 'in' THEN
    UPDATE stock_levels SET quantity_on_hand = quantity_on_hand + NEW.qty WHERE product_id = NEW.product_id;
  ELSEIF NEW.type = 'out' THEN
    UPDATE stock_levels SET quantity_on_hand = GREATEST(quantity_on_hand - NEW.qty, 0) WHERE product_id = NEW.product_id;
  ELSEIF NEW.type = 'adjust' THEN
    UPDATE stock_levels SET quantity_on_hand = NEW.qty WHERE product_id = NEW.product_id;
  END IF;

  IF (SELECT quantity_on_hand FROM stock_levels WHERE product_id = NEW.product_id) <
     (SELECT COALESCE(min_stock,0) FROM products WHERE id = NEW.product_id) THEN
    INSERT INTO audit_logs (user_id, action, payload, ip, created_at)
    VALUES (
      NEW.created_by,
      @ACTION_LOW_STOCK,
      JSON_OBJECT('product_id', NEW.product_id, 'qty', (SELECT quantity_on_hand FROM stock_levels WHERE product_id = NEW.product_id)),
      NULL, NOW()
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `price_history`
--

CREATE TABLE `price_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `old_price` decimal(12,2) NOT NULL,
  `new_price` decimal(12,2) NOT NULL,
  `reason` varchar(190) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(64) NOT NULL,
  `name` varchar(190) NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_stock` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `category_id`, `price`, `cost`, `min_stock`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SKU-001', 'Producto demo', 1, 100.00, 60.00, 5, 1, '2025-10-04 20:47:05', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scope_type` enum('product','category') NOT NULL,
  `scope_id` bigint(20) UNSIGNED NOT NULL,
  `discount_type` enum('percent','amount') NOT NULL,
  `discount_value` decimal(12,2) NOT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 100,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`) VALUES
(1, 'Superusuario', '2025-10-04 20:47:05'),
(2, 'Gerente', '2025-10-04 20:47:05'),
(3, 'Asistente', '2025-10-04 20:47:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_levels`
--

CREATE TABLE `stock_levels` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_on_hand` int(11) NOT NULL DEFAULT 0,
  `reserved` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stock_levels`
--

INSERT INTO `stock_levels` (`product_id`, `quantity_on_hand`, `reserved`, `updated_at`) VALUES
(1, 10, 0, '2025-10-04 20:47:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$reemplazar_con_bcrypt', 1, '2025-10-04 20:47:05', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(1, 1, '2025-10-04 20:47:05');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_inventory_value`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_inventory_value` (
`product_id` bigint(20) unsigned
,`sku` varchar(64)
,`name` varchar(190)
,`category_id` bigint(20) unsigned
,`quantity_on_hand` int(11)
,`price` decimal(12,2)
,`cost` decimal(12,2)
,`total_value_price` decimal(22,2)
,`total_value_cost` decimal(22,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_low_stock`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_low_stock` (
`product_id` bigint(20) unsigned
,`sku` varchar(64)
,`name` varchar(190)
,`quantity_on_hand` int(11)
,`min_stock` int(11)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_inventory_value`
--
DROP TABLE IF EXISTS `v_inventory_value`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventory_value`  AS SELECT `p`.`id` AS `product_id`, `p`.`sku` AS `sku`, `p`.`name` AS `name`, `p`.`category_id` AS `category_id`, `s`.`quantity_on_hand` AS `quantity_on_hand`, `p`.`price` AS `price`, `p`.`cost` AS `cost`, `s`.`quantity_on_hand`* `p`.`price` AS `total_value_price`, `s`.`quantity_on_hand`* `p`.`cost` AS `total_value_cost` FROM (`products` `p` left join `stock_levels` `s` on(`s`.`product_id` = `p`.`id`)) WHERE `p`.`is_active` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_low_stock`
--
DROP TABLE IF EXISTS `v_low_stock`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_low_stock`  AS SELECT `p`.`id` AS `product_id`, `p`.`sku` AS `sku`, `p`.`name` AS `name`, `s`.`quantity_on_hand` AS `quantity_on_hand`, `p`.`min_stock` AS `min_stock` FROM (`products` `p` join `stock_levels` `s` on(`s`.`product_id` = `p`.`id`)) WHERE `p`.`is_active` = 1 AND `s`.`quantity_on_hand` < `p`.`min_stock` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user_created` (`user_id`,`created_at`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `configurations`
--
ALTER TABLE `configurations`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customers_name` (`name`);

--
-- Indices de la tabla `custom_transactions`
--
ALTER TABLE `custom_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `import_jobs`
--
ALTER TABLE `import_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_import_user` (`created_by`);

--
-- Indices de la tabla `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mov_product_created` (`product_id`,`created_at`),
  ADD KEY `fk_mov_user` (`created_by`);

--
-- Indices de la tabla `price_history`
--
ALTER TABLE `price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_price_product_created` (`product_id`,`created_at`),
  ADD KEY `fk_price_user` (`user_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_name` (`name`);

--
-- Indices de la tabla `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_promos_scope_active` (`scope_type`,`scope_id`,`is_active`,`starts_at`,`ends_at`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `stock_levels`
--
ALTER TABLE `stock_levels`
  ADD PRIMARY KEY (`product_id`);

--
-- Indices de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_suppliers_name` (`name`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_user_roles_role` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `custom_transactions`
--
ALTER TABLE `custom_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `import_jobs`
--
ALTER TABLE `import_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventory_movements`
--
ALTER TABLE `inventory_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `price_history`
--
ALTER TABLE `price_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `import_jobs`
--
ALTER TABLE `import_jobs`
  ADD CONSTRAINT `fk_import_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD CONSTRAINT `fk_mov_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mov_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `price_history`
--
ALTER TABLE `price_history`
  ADD CONSTRAINT `fk_price_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_price_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `stock_levels`
--
ALTER TABLE `stock_levels`
  ADD CONSTRAINT `fk_stock_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =========================
-- MÓDULO 1: AUTENTICACIÓN Y SEGURIDAD
-- =========================

-- Tabla de sesiones de usuario (historial de accesos)
CREATE TABLE IF NOT EXISTS user_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  login_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  logout_at DATETIME DEFAULT NULL,
  ip VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de permisos granulares (por funcionalidad)
CREATE TABLE IF NOT EXISTS permissions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255) DEFAULT NULL
);

-- Relación de roles-permisos (muchos a muchos)
CREATE TABLE IF NOT EXISTS role_permissions (
  role_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- =========================
-- MÓDULO 2: INVENTARIO CORE
-- =========================

-- Tabla de productos ya existente, pero se recomienda agregar:
ALTER TABLE products
  ADD COLUMN description TEXT DEFAULT NULL AFTER name,
  ADD COLUMN barcode VARCHAR(64) DEFAULT NULL AFTER sku;

-- Tabla de categorías ya existente.

-- Tabla de movimientos de inventario ya existente.

-- =========================
-- MÓDULO 3: PRECIOS Y PROMOCIONES
-- =========================

-- Tabla de historial de precios ya existente.

-- Tabla de promociones ya existente, pero se recomienda agregar:
ALTER TABLE promotions
  ADD COLUMN created_by BIGINT UNSIGNED DEFAULT NULL AFTER is_active,
  ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- =========================
-- MÓDULO 4: IMPORTACIÓN Y EXPORTACIÓN
-- =========================

-- Tabla de trabajos de importación ya existente, pero se recomienda agregar:
ALTER TABLE import_jobs
  ADD COLUMN type ENUM('import','export') NOT NULL DEFAULT 'import' AFTER id,
  ADD COLUMN file_name VARCHAR(255) DEFAULT NULL AFTER report_url;

-- =========================
-- MÓDULO 5: REPORTES Y MÉTRICAS (DASHBOARD)
-- =========================

-- Vista para métricas de valor total de inventario por categoría
CREATE OR REPLACE VIEW v_inventory_value_by_category AS
SELECT
  c.id AS category_id,
  c.name AS category_name,
  SUM(s.quantity_on_hand * p.price) AS total_value_price,
  SUM(s.quantity_on_hand * p.cost) AS total_value_cost,
  COUNT(p.id) AS total_products
FROM products p
JOIN categories c ON p.category_id = c.id
JOIN stock_levels s ON s.product_id = p.id
WHERE p.is_active = 1
GROUP BY c.id, c.name;

-- Vista para movimientos de stock por periodo
CREATE OR REPLACE VIEW v_stock_movements_by_period AS
SELECT
  im.product_id,
  p.name AS product_name,
  im.type,
  SUM(im.qty) AS total_qty,
  DATE(im.created_at) AS movement_date
FROM inventory_movements im
JOIN products p ON im.product_id = p.id
GROUP BY im.product_id, im.type, DATE(im.created_at);

-- =========================
-- SEGURIDAD Y RESPALDO
-- =========================

-- Tabla para registro de respaldos automáticos
CREATE TABLE IF NOT EXISTS backups (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  backup_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  file_path VARCHAR(255) NOT NULL,
  status ENUM('success','failed') NOT NULL DEFAULT 'success',
  created_by BIGINT UNSIGNED DEFAULT NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =========================
-- ÍNDICES RECOMENDADOS
-- =========================

ALTER TABLE products
  ADD INDEX idx_products_barcode (barcode);

ALTER TABLE inventory_movements
  ADD INDEX idx_movements_type_date (type, created_at);

-- =========================
-- USUARIOS Y ROLES POR DEFECTO
-- =========================

-- Usuarios por defecto
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$reemplazar_con_bcrypt', 1, NOW(), NULL),
(2, 'Gerente Demo', 'gerente@example.com', '$2y$10$reemplazar_con_bcrypt', 1, NOW(), NULL),
(3, 'Asistente Demo', 'asistente@example.com', '$2y$10$reemplazar_con_bcrypt', 1, NOW(), NULL);

-- Roles por defecto (ya insertados previamente)
-- 1: Superusuario, 2: Gerente, 3: Asistente

-- Asignación de roles a usuarios por defecto
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(1, 1, NOW()), -- Admin como Superusuario
(2, 2, NOW()), -- Gerente Demo como Gerente
(3, 3, NOW()); -- Asistente Demo como Asistente
