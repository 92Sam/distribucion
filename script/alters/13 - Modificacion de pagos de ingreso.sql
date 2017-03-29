ALTER TABLE `pagos_ingreso` 
	ADD COLUMN `medio_pago_id` BIGINT(20) NOT NULL  AFTER `pagoingreso_restante` , 
	ADD COLUMN `banco_id` BIGINT(20) NULL  AFTER `medio_pago_id` , 
	ADD COLUMN `operacion` VARCHAR(45) NULL  AFTER `banco_id` ;