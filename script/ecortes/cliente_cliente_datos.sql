



ALTER TABLE `cliente` ADD `agente_retencion` BOOLEAN NULL DEFAULT NULL AFTER `id_zona`, ADD `tipo_cliente` INT NOT NULL AFTER `id_zona`, ADD `linea_credito_valor` DECIMAL NULL DEFAULT NULL AFTER `retencion`, ADD `linea_libre` BOOLEAN NULL DEFAULT NULL AFTER `linea_credito_valor`, ADD `linea_libre_valor` DECIMAL NULL DEFAULT NULL AFTER `linea_libre`, ADD `importe_deuda` DECIMAL NULL DEFAULT NULL AFTER `linea_libre_valor`; 
