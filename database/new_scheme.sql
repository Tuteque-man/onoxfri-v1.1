-- Minimal schema: usuarios y roles
CREATE DATABASE IF NOT EXISTS `onoxfri` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `onoxfri`;

-- Limpiar tablas previas no necesarias (si existen)
DROP TABLE IF EXISTS `user_roles`;
DROP TABLE IF EXISTS `credencial`;
DROP TABLE IF EXISTS `usuario`;
DROP TABLE IF EXISTS `permisos_de_rol`;
DROP TABLE IF EXISTS `permiso`;
DROP TABLE IF EXISTS `users_pk_map`;
DROP TABLE IF EXISTS `empresas`;

-- Roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Empresas
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_empresa` VARCHAR(255) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_empresas_nombre` (`nombre_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Intersección Empresa-Rol (solo 3 roles por empresa)
CREATE TABLE IF NOT EXISTS `empresa_roles` (
  `empresa_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`empresa_id`, `role_id`),
  CONSTRAINT `fk_empr_rol_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_empr_rol_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Trigger para limitar a 3 roles por empresa
DELIMITER $$
CREATE TRIGGER `trg_empresa_roles_limit`
BEFORE INSERT ON `empresa_roles`
FOR EACH ROW
BEGIN
  DECLARE cnt INT;
  SELECT COUNT(*) INTO cnt FROM `empresa_roles` WHERE `empresa_id` = NEW.`empresa_id`;
  IF cnt >= 3 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Una empresa solo puede tener 3 roles';
  END IF;
END$$
DELIMITER ;

-- Usuarios
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(190) NOT NULL,
  `nombre_empresa` VARCHAR(255) DEFAULT NULL,
  `nombre` VARCHAR(120) NOT NULL,
  `apellido` VARCHAR(120) NOT NULL,
  `nombre_usuario` VARCHAR(190) NOT NULL,
  `direccion` VARCHAR(255) DEFAULT NULL,
  `telefono` VARCHAR(60) DEFAULT NULL,
  `telefono_secundario` VARCHAR(60) DEFAULT NULL,
  `empresa_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `descripcion_usuario` TEXT DEFAULT NULL,
  `descripcion_empresa` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `uq_users_empresa_role` (`empresa_id`, `role_id`), -- 1 usuario por rol por empresa
  KEY `idx_users_empresa` (`empresa_id`),
  KEY `idx_users_role` (`role_id`),
  CONSTRAINT `fk_users_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_users_empresa_role`
    FOREIGN KEY (`empresa_id`, `role_id`) REFERENCES `empresa_roles` (`empresa_id`, `role_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Roles por defecto
INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Superusuario'),
(2, 'Gerente'),
(3, 'Asistente')
ON DUPLICATE KEY UPDATE name = VALUES(name);
