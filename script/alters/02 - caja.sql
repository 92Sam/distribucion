ALTER TABLE `usuario`
DROP FOREIGN KEY `usuario_ibfk_3`;
ALTER TABLE `usuario`
DROP INDEX `caja` ;MENT=13 ;

ALTER TABLE `historial_pagos_clientes`
DROP FOREIGN KEY `historial_pagos_clientes_ibfk_5`;
ALTER TABLE `historial_pagos_clientes`
DROP INDEX `historial_caja_id` ;

ALTER TABLE `venta`
DROP FOREIGN KEY `venta_ibfk_3`;
ALTER TABLE `venta`
DROP INDEX `confirmacion_caja` ;

DROP TABLE `caja`;

ALTER TABLE `usuario`
DROP COLUMN `caja`;

ALTER TABLE `historial_pagos_clientes`
DROP COLUMN `historial_caja_id`;


--
-- Table structure for table `caja`
--

DROP TABLE IF EXISTS `caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `local_id` bigint(20) NOT NULL,
  `moneda_id` bigint(20) NOT NULL,
  `responsable_id` bigint(20) NOT NULL,
  `estado` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caja_desglose`
--

DROP TABLE IF EXISTS `caja_desglose`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja_desglose` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caja_id` int(11) NOT NULL,
  `responsable_id` bigint(20) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `saldo` float NOT NULL DEFAULT '0',
  `principal` tinyint(2) NOT NULL DEFAULT '0',
  `estado` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caja_movimiento`
--

DROP TABLE IF EXISTS `caja_movimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja_movimiento` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `caja_desglose_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `fecha_mov` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `movimiento` varchar(45) NOT NULL,
  `operacion` varchar(45) NOT NULL,
  `medio_pago` varchar(45) NOT NULL,
  `saldo` float NOT NULL,
  `saldo_old` float NOT NULL,
  `ref_id` varchar(50) DEFAULT NULL,
  `ref_val` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;