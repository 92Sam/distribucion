--
-- Table structure for table `zona_dias`
--

CREATE TABLE IF NOT EXISTS `zona_dias` (
  `id_zud` int(11) NOT NULL AUTO_INCREMENT,
  `id_zona` int(11) NOT NULL,
  `dia_semana` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_zud`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;