ALTER TABLE `usuario`
DROP FOREIGN KEY `usuario_ibfk_3`;
ALTER TABLE `usuario`
DROP INDEX `caja` ;MENT=13 ;

ALTER TABLE `historial_pagos_clientes`
DROP FOREIGN KEY `historial_pagos_clientes_ibfk_5`;
ALTER TABLE `historial_pagos_clientes`
DROP INDEX `historial_caja_id` ;

ALTER TABLE `venta`
DROP FOREIGN KEY `venta_ibfk_3`;
ALTER TABLE `venta`
DROP INDEX `confirmacion_caja` ;

DROP TABLE `caja`;

ALTER TABLE `distribucion_dev`.`usuario`
DROP COLUMN `caja`;

ALTER TABLE `distribucion_dev`.`historial_pagos_clientes`
DROP COLUMN `historial_caja_id`;

ALTER TABLE `distribucion_dev`.`venta`
DROP FOREIGN KEY `venta_ibfk_5`,
DROP FOREIGN KEY `venta_ibfk_4`;
ALTER TABLE `distribucion_dev`.`venta`
DROP COLUMN `confirmacion_usuario`,
DROP COLUMN `confirmacion_fecha`,
DROP COLUMN `confirmacion_banco`,
DROP COLUMN `confirmacion_caja`,
DROP INDEX `confirmacion_usuario` ,
DROP INDEX `confirmacion_banco` ;



CREATE TABLE `distribucion_dev`.`caja` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `local_id` BIGINT(20) NOT NULL,
  `moneda_id` BIGINT(20) NOT NULL,
  `estado` TINYINT(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`));

CREATE TABLE `distribucion_dev`.`caja_desglose` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `caja_id` INT NOT NULL,
  `responsable_id` BIGINT(20) NOT NULL,
  `descripcion` VARCHAR(45) NOT NULL,
  `saldo` FLOAT NOT NULL DEFAULT 0,
  `principal` TINYINT(2) NOT NULL DEFAULT 0,
  `estado` TINYINT(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`));

CREATE TABLE `distribucion_dev`.`caja_cuadre` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `caja_id` INT NOT NULL,
  `fecha_cierre` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  `saldo_cierre` FLOAT NOT NULL DEFAULT 0,
  `cerrado` VARCHAR(5) NOT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `distribucion_dev`.`caja_movimiento` (
  `id` BIGINT(20) NOT NULL,
  `caja_desglose_id` BIGINT(20) NOT NULL,
  `fecha_mov` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  `movimiento` VARCHAR(45) NOT NULL,
  `operacion` VARCHAR(45) NOT NULL,
  `medio_pago` VARCHAR(45) NOT NULL,
  `saldo` FLOAT NOT NULL,
  `ref_id` VARCHAR(50) NULL,
  `ref_val` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));

