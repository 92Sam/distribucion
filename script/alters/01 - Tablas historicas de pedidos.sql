--
-- Table structure for table `historial_pedido_detalle`
--

DROP TABLE IF EXISTS `historial_pedido_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_pedido_detalle` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `historial_pedido_proceso_id` int(11) NOT NULL,
  `producto_id` bigint(20) NOT NULL,
  `unidad_id` bigint(20) NOT NULL,
  `stock` float NOT NULL DEFAULT '0',
  `costo_unitario` float DEFAULT '0',
  `precio_unitario` float DEFAULT NULL,
  `bonificacion` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hist_pedido_index` (`historial_pedido_proceso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `historial_pedido_proceso`
--

DROP TABLE IF EXISTS `historial_pedido_proceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_pedido_proceso` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `proceso_id` int(11) NOT NULL,
  `pedido_id` bigint(20) NOT NULL,
  `responsable_id` bigint(20) NOT NULL,
  `fecha_plan` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `actual` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pedido_unique` (`pedido_id`,`proceso_id`),
  KEY `pedido_index` (`pedido_id`),
  KEY `create_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `procesos`
--

DROP TABLE IF EXISTS `procesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `procesos` (
  `historial_pedido_proceso_id` int(10) unsigned NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `orden` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`historial_pedido_proceso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO
    `procesos` VALUES (1,'Generar Pedido',NULL,1),
    (2,'Asignar Pedido',NULL,10),
    (3,'Imprimir Pedido',NULL,20),
    (4,'Liquidar Pedido',NULL,30),
    (5,'Modificar Pedido',NULL,5);