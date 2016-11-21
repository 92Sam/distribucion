
ALTER TABLE `cliente` DROP `codigo_postal`, DROP `direccion`, DROP `direccion2`, DROP `email`, DROP `pagina_web`, DROP `telefono1`, DROP `telefono2`, DROP `nota`;


ALTER TABLE `cliente` ADD `agente_retencion` BOOLEAN NULL DEFAULT NULL AFTER `id_zona`, ADD `tipo_cliente` INT NOT NULL AFTER `id_zona`, ADD `linea_credito_valor` DECIMAL NULL DEFAULT NULL AFTER `agente_retencion`, ADD `linea_libre` BOOLEAN NULL DEFAULT NULL AFTER `linea_credito_valor`, ADD `linea_libre_valor` DECIMAL NULL DEFAULT NULL AFTER `linea_libre`, ADD `importe_deuda` DECIMAL NULL DEFAULT NULL AFTER `linea_libre_valor`; 
