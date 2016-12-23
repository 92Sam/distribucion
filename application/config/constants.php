<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


define('COMPLETADO',		'COMPLETADO');
define('ESPERA',		'EN ESPERA');


define('INGRESO_PENDIENTE', 'PENDIENTE');
define('INGRESO_COMPLETADO', 'COMPLETADO');
define('PAGO_CANCELADO', 'PAGO CANCELADO');


define('INGRESO', 'INGRESO');
define('INGRESO_DEVOLUCION', 'DEVOLUCION DE INGRESO');
define('VENTA_DEVOLUCION', 'DEVOLUCION DE VENTA');
define('VENTA_EDICION', 'MODIFICACION DE VENTA');
define('PEDIDO_DEVUOLUCION', 'LIQUIDACION DE PEDIDO DEVUELTO PARCALMENTE');
define('PEDIDO_EDICION', 'MODIFICACION DE PEDIDO');
define('PEDIDO_RECHAZO', 'LIQUIDACION DE PEDIDO RECHAZADO');
define('AJUSTE_INVENTARIO', 'AJUSTE DE INVENTARIO');


define('VENTA', 'VENTA');
define('ENTRADA', 'ENTRADA');
define('SALIDA', 'SALIDA');

define('NOTAVENTA',		'NOTA DE PEDIDO');
define('BOLETAVENTA',		'BOLETA DE VENTA');
define('FACTURA',		'FACTURA');
define('NOTA_ENTREGA', 'NOTA DE ENTREGA');

define('DONACION',		'DONACION');
define('COMPRA',		'COMPRA');


define('PESABLE',		'PESABLE');
define('MEDIBLE',		'MEDIBLE');

define('MONEDA',		'S/.');
define('DOLAR',		'$');
/* End of file constants.php */
/* Location: ./application/config/constants.php */


/******CONFIGURACIONES************/

define('EMPRESA_NOMBRE',		'EMPRESA_NOMBRE');
define('EMPRESA_DIRECCION',		'EMPRESA_DIRECCION');
define('EMPRESA_TELEFONO',		'EMPRESA_TELEFONO');
define('MONTO_BOLETAS_VENTA', 'MONTO_BOLETAS_VENTA');
define('VENTA_SIN_STOCK', 'VENTA_SIN_STOCK');
define('DATABASE_IP',		'DATABASE_IP');
define('DATABASE_NAME',		'DATABASE_NAME');
define('DATABASE_USERNAME',		'DATABASE_USERNAME');
define('DATABASE_PASWORD',		'DATABASE_PASWORD');
define('MONEDA_OPCION', 'MONEDA');
define('REFRESCAR_PEDIDOS_OPCION', 'REFRESCAR_PEDIDOS');


define('NOMBRE_EXISTE',		'El nombre ingresado ya existe');
define('CEDULA_EXISTE',		'La identificacion ingresada ya existe');
define('USERNAME_EXISTE',		'El username ingresado ya existe');
define('CAMION_EXISTE', 'La placa ingresada ya existe');

/// constantes de estatus de las ventas a credito
define('CREDITO_DEBE', 'DEBE');
define('CREDITO_ACUENTA', 'A_CUENTA');
define('CREDITO_NOTACREDITO', 'NOTA_CREDITO');
define('CREDITO_CANCELADO', 'CANCELADA');

define('VENTA_CREDITO', 'Venta a credito');
define('VENTA_CONTADO', 'Venta al contado');


//////////////////////////////////////


///////// constantes de tipos de metodos de pago
define('METODO_BANCO', 'BANCO');
define('METODO_CAJA', 'CAJA');
////////////////////////////

define('REFRESCAR_PEDIDOS', '100000');

define('VENTA_ENTREGA', 'ENTREGA');
define('VENTA_CAJA', 'CAJA');

define('PEDIDO_ENTREGADO', 'ENTREGADO');
define('PEDIDO_ANULADO', 'ANULADO');
define('PEDIDO_ENVIADO', 'ENVIADO');
define('PEDIDO_RECHAZADO', 'RECHAZADO');
define('PEDIDO_GENERADO', 'GENERADO');
define('PEDIDO_DEVUELTO', 'DEVUELTO PARCIALMENTE');
define('INGRESO_DEVUELTO', 'DEVUELTO');

define('URL_CURL_GCM', 'http://teayudo.pe/distribucion/api/gcm_push/enviar');

define('PROCESO_GENERAR', 1);
define('PROCESO_MODIFICAR', 5);
define('PROCESO_ASIGNAR', 2);
define('PROCESO_IMPRIMIR', 3);
define('PROCESO_LIQUIDAR', 4);

//CONSTANTES DE DATOS CLIENTES
define('CDIRECCION', 1);
define('CTELEFONO', 2);
define('CCORREO', 3);
define('CWEB', 4);
define('CNOTA', 5);
define('CGERENTE_DNI', 6);
define('CCONTACTO_NOMBRE', 7);
define('CCONTACTO_DNI', 8);
