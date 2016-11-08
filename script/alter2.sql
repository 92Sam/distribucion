ALTER TABLE `venta`
  ADD COLUMN `confirmacion_caja` BIGINT(20) NULL AFTER `venta_tipo`,
  ADD COLUMN `confirmacion_banco` BIGINT(20) NULL AFTER `confirmacion_caja`,
  ADD COLUMN `confirmacion_fecha` BIGINT(20) NULL AFTER `confirmacion_banco`,
  ADD COLUMN `confirmacion_usuario` BIGINT(20) NULL AFTER `confirmacion_fecha`;
ALTER TABLE `venta`
  CHANGE `confirmacion_fecha` `confirmacion_fecha` DATETIME NULL,
  ADD FOREIGN KEY (`confirmacion_caja`) REFERENCES `caja`(`caja_id`),
  ADD FOREIGN KEY (`confirmacion_banco`) REFERENCES `banco`(`banco_id`),
  ADD FOREIGN KEY (`confirmacion_usuario`) REFERENCES `usuario`(`nUsuCodigo`);
