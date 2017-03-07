ALTER TABLE `venta`
ADD COLUMN `retencion` FLOAT NULL DEFAULT 0 AFTER `venta_tipo`;

ALTER TABLE `venta_backup`
ADD COLUMN `retencion` FLOAT NULL DEFAULT 0 AFTER `venta_tipo`;

