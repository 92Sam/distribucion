CREATE TABLE `caja_pendiente` (
  `id` BIGINT(20) NOT NULL,
  `caja_desglose_id` BIGINT(20) NOT NULL,
  `usuario_id` BIGINT(20) NOT NULL,
  `tipo` VARCHAR(45) NOT NULL,
  `monto` FLOAT NOT NULL,
  `estado` TINYINT NOT NULL,
  `ref_id` VARCHAR(45) NULL,
  `ref_val` VARCHAR(100) NULL,
  PRIMARY KEY (`id`));

  ALTER TABLE `caja_pendiente`
ADD COLUMN `IO` TINYINT NOT NULL AFTER `tipo`;

ALTER TABLE `caja_pendiente` 
CHANGE COLUMN `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT ;
