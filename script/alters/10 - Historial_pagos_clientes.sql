ALTER TABLE `historial_pagos_clientes`
CHANGE COLUMN `vendedor_id` `vendedor_id` BIGINT(20) NULL DEFAULT NULL  AFTER `historial_banco_id` ,
ADD COLUMN `fecha_documento` DATETIME NULL DEFAULT NULL  AFTER `vendedor_id` ;


