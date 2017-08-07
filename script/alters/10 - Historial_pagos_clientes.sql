ALTER TABLE `historial_pagos_clientes`
ADD COLUMN `fecha_documento` DATETIME NULL DEFAULT NULL  AFTER `vendedor_id` ;


