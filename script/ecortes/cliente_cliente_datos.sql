ALTER TABLE `cliente` DROP `codigo_postal`, DROP `direccion`, DROP `direccion2`, DROP `email`, DROP `pagina_web`, DROP `telefono1`, DROP `telefono2`, DROP `nota`;



ALTER TABLE `cliente` ADD `tipo_cliente` INT NOT NULL AFTER `id_zona`, ADD `agente_retencion` BOOLEAN NOT NULL AFTER `tipo_cliente`, ADD `linea_credito_valor` DECIMAL NOT NULL AFTER `agente_retencion`, ADD `linea_libre` BOOLEAN NOT NULL AFTER `linea_credito_valor`, ADD `linea_libre_valor` DECIMAL NOT NULL AFTER `linea_libre`;



ALTER TABLE `cliente` CHANGE `linea_credito_valor` `linea_credito_valor` DECIMAL(10,0) NULL DEFAULT NULL, CHANGE `linea_libre` `linea_libre` TINYINT(1) NULL DEFAULT NULL, CHANGE `linea_libre_valor` `linea_libre_valor` DECIMAL(10,0) NULL DEFAULT NULL, CHANGE `importe_deuda` `importe_deuda` DECIMAL(10,0) NULL DEFAULT NULL;
