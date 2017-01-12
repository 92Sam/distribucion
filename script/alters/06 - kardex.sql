CREATE TABLE `kardex` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `local_id` BIGINT(20) NOT NULL,
  `producto_id` BIGINT(20) NOT NULL,
  `unidad_id` BIGINT(20) NOT NULL,
  `fecha` DATETIME NOT NULL,
  `serie` VARCHAR(45) NOT NULL,
  `numero` VARCHAR(45) NOT NULL,
  `referencia` VARCHAR(100),
  `tipo_doc` BIGINT(20) NOT NULL,
  `tipo_operacion` BIGINT(20) NOT NULL,
  `IO` VARCHAR(2) NOT NULL,
  `cantidad` FLOAT NOT NULL,
  `costo_unitario` FLOAT NOT NULL,
  `total` FLOAT NOT NULL,
  `cantidad_final` FLOAT NOT NULL,
  `costo_unitario_final` FLOAT NOT NULL,
  `total_final` FLOAT NOT NULL,
  PRIMARY KEY (`id`));

  ALTER TABLE `kardex`
ADD COLUMN `ref_id` BIGINT(20) NULL AFTER `total_final`,
ADD COLUMN `ref_val` VARCHAR(100) NULL AFTER `ref_id`;

ALTER TABLE `documento_fiscal` 
ADD COLUMN `estado` TINYINT NOT NULL DEFAULT 1 AFTER `documento_numero`;
