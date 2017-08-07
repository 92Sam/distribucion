ALTER TABLE `documento_detalle`
  ADD COLUMN `bono` INT(2) NOT NULL DEFAULT 0  AFTER `detalle_importe`;

  UPDATE `documento_detalle` SET `bono`= 1 WHERE `precio` = 0;
