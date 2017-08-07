ALTER TABLE `pagos_ingreso`
ADD COLUMN `fecha_operacion` DATETIME NULL  AFTER `operacion` ,
ADD COLUMN `motivo` VARCHAR(255) NULL  AFTER `fecha_operacion` ;
