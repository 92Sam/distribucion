ALTER TABLE `bonificaciones`
  ADD COLUMN `id_grupos_cliente` BIGINT(20) NULL AFTER `subgrupo_id`;

ALTER TABLE `descuentos`
  ADD COLUMN `id_grupos_cliente` BIGINT(20) NULL AFTER `status`;