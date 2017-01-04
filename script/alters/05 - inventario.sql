ALTER TABLE `inventario`
CHANGE COLUMN `cantidad` `cantidad` FLOAT UNSIGNED NULL DEFAULT NULL ,
CHANGE COLUMN `fraccion` `fraccion` FLOAT UNSIGNED NULL DEFAULT NULL ;

INSERT INTO `configuraciones` (`config_key`, `config_value`) VALUES ('FACTURA_NEXT', '1');
INSERT INTO `configuraciones` (`config_key`, `config_value`) VALUES ('BOLETA_NEXT', '1');
INSERT INTO `configuraciones` (`config_key`, `config_value`) VALUES ('FACTURA_SERIE', '1');
INSERT INTO `configuraciones` (`config_key`, `config_value`) VALUES ('BOLETA_SERIE', '1');
INSERT INTO `configuraciones` (`config_key`, `config_value`) VALUES ('FACTURA_MAX', '15');
INSERT INTO `configuraciones` (`config_key`, `config_value`) VALUES ('BOLETA_MAX', '10');
