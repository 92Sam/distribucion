ALTER TABLE `banco`
ADD COLUMN `cuenta_id` BIGINT(20) NOT NULL AFTER `banco_status`;

ALTER TABLE `historial_pagos_clientes`
ADD COLUMN `pago_data` VARCHAR(100) NULL AFTER `historial_banco_id`;

ALTER TABLE `historial_pagos_clientes`
ADD COLUMN `vendedor_id` BIGINT(20) NULL DEFAULT NULL AFTER `pago_data`;


