--
-- Table structure for table `documentos_logger`
--

CREATE TABLE IF NOT EXISTS `documentos_logger` (
  `id_doc_log` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `msg` varchar(50) NOT NULL,
  `params` varchar(1000) NOT NULL,
  `products` longtext NOT NULL,
  `result` longtext NULL,
  PRIMARY KEY (`id_doc_log`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;