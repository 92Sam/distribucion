<?php
/**
 * Created by PhpStorm.
 * User: Jhainey
 * Date: 31/05/2015
 * Time: 15:29
 */

/**
 * Class main_controller
 *  * @property ajusteinventario_model $ajusteinventario_model
 *  * @property ajustedetalle_model $ajustedetalle_model
 *  * @property inventario_model $inventario_model
 *  * @property producto_model $producto_model
 *  * @property unidades_model $unidades_model
 *  * @property local_model $local_model
 *  * @property clientes_grupos_model $clientes_grupos_model
 *  * @property precios_model $precios_model
 *  * @property marcas_model $marcas_model
 *  * @property lineas_model $lineas_model
 *  * @property familias_model $familias_model
 *  * @property grupos_model $grupos_model
 *  * @property proveedor_model $proveedor_model
 *  * @property impuestos_model $impuestos_model
 *  * @property columnas_model $columnas_model
 *  * @property estado_model $estado_model
 *  * @property ciudad_model $ciudad_model
 *  * @property cliente_model $cliente_model
 *  * @property condiciones_pago_model $condiciones_pago_model
 *  * @property venta_model $venta_model
 *  * @property caja_model $caja_model
 *  * @property gastos_model $gastos_model
 *  * @property usuarios_grupos_model $usuarios_grupos_model
 *  * @property usuario_model $usuario_model
 *  * @property opciones_model $opciones_model
 *  * @property ingreso_model $ingreso_model
 *  * @property tipos_gasto_model $tipos_gasto_model
 *  * @property metodos_pago_model $metodos_pago_model
 *  * @property pais_model $pais_model
 *  * @property camiones_model $camiones_model
 *  * @property zona_model $zona_model
 *  * @property consolidado_model $consolidado_model
 *  * @property venta_estatus_model $venta_estatus_model
 *  * @property historial_pagos_clientes_model $historial_pagos_clientes_model
 *  * @property bonificaciones_model $bonificaciones_model
 *  * @property descuentos_model $descuentos_model
 *  * @property liquidacion_cobranza_model $liquidacion_cobranza_model
 *  * @property kardex_model $kardex_model
 */
class MY_Controller extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('usuariosgrupos/usuarios_grupos_model');
        /*con esto valido la sesion*/
        $this->load->model('login/login_model');
        $this->load->library('session');
        $this->login_model->very_session();
      /* if (!$this->session->userdata('nUsuCodigo')) {
            redirect(base_url() . 'inicio');
        }*/
    }
}