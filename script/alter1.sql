ALTER TABLE `consolidado_detalle`
  CHANGE `liquidacion_monto_cobrado_caja` `confirmacion_monto_cobrado_caja` FLOAT NULL,
  CHANGE `liquidacion_monto_cobrado_bancos` `confirmacion_monto_cobrado_bancos` FLOAT NULL,
  CHANGE `liquidacion_caja_id` `confirmacion_caja_id` BIGINT(20) NULL,
  CHANGE `liquidacion_banco_id` `confirmacion_banco_id` BIGINT(20) NULL,
  CHANGE `liqudicion_usuario` `confirmacion_usuario` BIGINT(20) NULL,
  CHANGE `liquidacion_fecha` `confirmacion_fecha` DATETIME NULL;


ALTER TABLE `consolidado_detalle`
  ADD COLUMN `liquidacion_monto_cobrado` FLOAT(20) NULL AFTER `confirmacion_fecha`;
