<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class consolidado_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    function get_all()
    {
        $this->db->select('camiones.*,SUM(liquidacion_monto_cobrado)as totalC,liquidacion_monto_cobrado,usuario.nombre,consolidado_detalle.consolidado_id as ConsolidadoDetalle,consolidado_carga.consolidado_id,
        fecha,camion,status,consolidado_carga.metros_cubicos as metrosc, fecha_creacion');
        $this->db->from('consolidado_carga');
        $this->db->join('camiones', 'camiones.camiones_id=consolidado_carga.camion', 'left');
        $this->db->join('usuario', 'usuario.nUsuCodigo=camiones.id_trabajadores', 'left');
        $this->db->join('consolidado_detalle', 'consolidado_detalle.consolidado_id=consolidado_carga.consolidado_id', 'left');
        $this->db->group_by('consolidado_id');
        $query = $this->db->get();
        return $query->result_array();

    }

    function getData($where = array())
    {
        $this->db->select('camiones.*,SUM(liquidacion_monto_cobrado)as totalC,
        liquidacion_monto_cobrado,usuario.nombre,
        consolidado_detalle.consolidado_id as ConsolidadoDetalle,
        consolidado_carga.consolidado_id,
        fecha,camion,status,consolidado_carga.metros_cubicos as metrosc
        , fecha_creacion');
        $this->db->from('consolidado_carga');
        $this->db->join('camiones', 'camiones.camiones_id=consolidado_carga.camion', 'left');
        $this->db->join('usuario', 'usuario.nUsuCodigo=camiones.id_trabajadores', 'left');
        $this->db->join('consolidado_detalle', 'consolidado_detalle.consolidado_id=consolidado_carga.consolidado_id', 'left');

        if (isset($where['estado']))
            $this->db->where('status', $where['estado']);

        if (isset($where['fecha_ini']) && isset($where['fecha_fin'])) {
            $this->db->where('fecha >=', date('Y-m-d H:i:s', strtotime($where['fecha_ini'] . " 00:00:00")));
            $this->db->where('fecha <=', date('Y-m-d H:i:s', strtotime($where['fecha_fin'] . " 23:59:59")));
        }
        $this->db->group_by('consolidado_id');
        $query = $this->db->get();
        return $query->result_array();

    }

    function get_all_estado($where)
    {
        $this->db->select('camiones.*,SUM(liquidacion_monto_cobrado)as totalC,liquidacion_monto_cobrado,usuario.nombre,consolidado_detalle.consolidado_id as ConsolidadoDetalle,consolidado_carga.consolidado_id,
        fecha,camion,status,consolidado_carga.metros_cubicos as metrosc, fecha_creacion');
        $this->db->from('consolidado_carga');
        $this->db->where($where);
        $this->db->join('camiones', 'camiones.camiones_id=consolidado_carga.camion', 'left');
        $this->db->join('usuario', 'usuario.nUsuCodigo=camiones.id_trabajadores', 'left');
        $this->db->join('consolidado_detalle', 'consolidado_detalle.consolidado_id=consolidado_carga.consolidado_id', 'left');
        $this->db->group_by('consolidado_id');
        $query = $this->db->get();

        return $query->result_array();

    }

    function getMap($where)
    {

        $query = $this->db->query("SELECT *,consolidado_carga.consolidado_id as cargaConsolidado FROM `consolidado_carga`
        LEFT JOIN consolidado_detalle ON consolidado_detalle.consolidado_id=consolidado_carga.consolidado_id
        LEFT JOIN venta ON venta.venta_id=consolidado_detalle.pedido_id
        LEFT JOIN cliente ON cliente.id_cliente=venta.id_cliente
        WHERE consolidado_carga.consolidado_id=" . $where . " GROUP BY venta.id_cliente ");
        return $query->result_array();

    }

    function mapClientesPorAtender()
    {

        $query = $this->db->query("SELECT *,consolidado_carga.consolidado_id as cargaConsolidado, consolidado_carga.fecha as fechaConsolidado
        FROM `consolidado_carga`
        LEFT JOIN consolidado_detalle ON consolidado_detalle.consolidado_id=consolidado_carga.consolidado_id
        LEFT JOIN venta ON venta.venta_id=consolidado_detalle.pedido_id
        LEFT JOIN cliente ON cliente.id_cliente=venta.id_cliente ");
        return $query->result_array();

    }

    function getMapaFecha($where)
    {

        $query = $this->db->query("SELECT *,consolidado_carga.consolidado_id as cargaConsolidado, consolidado_carga.fecha as fechaConsolidado
        FROM `consolidado_carga`
        LEFT JOIN consolidado_detalle ON consolidado_detalle.consolidado_id=consolidado_carga.consolidado_id
        LEFT JOIN venta ON venta.venta_id=consolidado_detalle.pedido_id
        LEFT JOIN cliente ON cliente.id_cliente=venta.id_cliente WHERE " . $where . " ");

        // echo $this->db->last_query();
        return $query->result_array();

    }

    function getReparticion($campo)
    {
        $this->db->where($campo);
        $this->db->join('camiones', 'camiones.camiones_id=consolidado_carga.camion', 'left');
        $this->db->join('usuario', 'usuario.nUsuCodigo=camiones.id_trabajadores', 'left');
        $query = $this->db->get('consolidado_carga');
        // echo $this->db->last_query();
        return $query->result_array();
    }


    function get_consolidado_by($where)
    {

        $this->db->select('*,usuarioCarga.nombre as userCarga, chofer.nombre as chofernombre');
        $this->db->from('consolidado_carga');
        $this->db->where($where);
        $this->db->join('camiones', 'camiones.camiones_id=consolidado_carga.camion', 'left');
        $this->db->join('usuario', 'usuario.nUsuCodigo=camiones.id_trabajadores', 'left');
        $this->db->join('usuario as usuarioCarga', 'usuarioCarga.nUsuCodigo=consolidado_carga.generado_por', 'left');
        $this->db->join('usuario as chofer', 'chofer.nUsuCodigo=camiones.id_trabajadores', 'left');
        $this->db->join('local', 'local.int_local_id=usuarioCarga.id_local', 'left');
        $this->db->order_by('consolidado_carga.consolidado_id desc');
        $query = $this->db->get();
        return $query->result_array();

    }


    function get_details_by($where)
    {


        $this->db->select('venta.*,consolidado_detalle.pedido_id,consolidado_detalle.detalle_id, consolidado_detalle.consolidado_id,
         consolidado_detalle.confirmacion_caja_id, consolidado_detalle.confirmacion_banco_id, consolidado_detalle.liquidacion_monto_cobrado,
         consolidado_detalle.confirmacion_monto_cobrado_bancos,consolidado_detalle.confirmacion_monto_cobrado_caja, cliente.*,documento_venta.*,banco.*,
         liquidacion_monto_cobrado as montocobradoliquidacion,venta_backup.total as totalbackup
        ,(select count(id_producto) from detalle_venta where id_venta = venta.venta_id ) as cantidad_prductos');
        $this->db->from('consolidado_detalle');
        $this->db->join('venta', 'venta.venta_id=consolidado_detalle.pedido_id', 'left');
        $this->db->join('venta_backup', 'venta_backup.venta_id=venta.venta_id', 'left');
        $this->db->join('cliente', 'venta.id_cliente=cliente.id_cliente', 'left');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento', 'left');
        $this->db->join('banco', 'banco.banco_id=consolidado_detalle.confirmacion_banco_id', 'left');

        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_detalle_by($where)
    {
        $this->db->select('distinct(documento_fiscal.venta_id), consolidado_detalle.*, carga.*, venta.*, documento_fiscal.documento_tipo');
        $this->db->from('consolidado_detalle');
        $this->db->join('consolidado_carga carga', 'carga.consolidado_id=consolidado_detalle.consolidado_id', 'left');
        $this->db->join('venta', 'venta.venta_id=consolidado_detalle.pedido_id', 'left');
        $this->db->join('documento_fiscal', 'documento_fiscal.venta_id=venta.venta_id', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }
    function get_detalle_bk_by($where)
    {
        $this->db->select('distinct(documento_fiscal.venta_id), consolidado_detalle.*, carga.*, venta.*, documento_fiscal.documento_tipo');
        $this->db->from('consolidado_detalle');
        $this->db->join('consolidado_carga carga', 'carga.consolidado_id=consolidado_detalle.consolidado_id', 'left');
        $this->db->join('venta_backup as venta', 'venta.venta_id=consolidado_detalle.pedido_id', 'left');
        $this->db->join('documento_fiscal', 'documento_fiscal.venta_id=venta.venta_id', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_detalle($campo)
    {
        $query = $this->db->query("SELECT *,zonas.*, usuarioCarga.nombre as userCarga, SUM(detalle_venta.cantidad) as cantidadTotal, chofer.nombre as chofernombre
      FROM `consolidado_detalle`
      LEFT JOIN venta ON venta.venta_id = consolidado_detalle.pedido_id
      LEFT JOIN consolidado_carga ON consolidado_carga.consolidado_id = consolidado_detalle.consolidado_id
      LEFT JOIN detalle_venta ON detalle_venta.id_venta = venta.venta_id
      LEFT JOIN producto ON producto.producto_id=detalle_venta.id_producto
      LEFT JOIN unidades ON unidades.id_unidad=detalle_venta.unidad_medida
      LEFT JOIN grupos ON grupos.id_grupo=producto.produto_grupo
      LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id
      LEFT JOIN  camiones ON camiones.camiones_id=consolidado_carga.camion
 LEFT JOIN usuario as chofer ON chofer.nUsuCodigo=camiones.id_trabajadores
     LEFT JOIN usuario as usuarioCarga ON usuarioCarga.nUsuCodigo=consolidado_carga.generado_por
     JOIN cliente ON cliente.id_cliente = venta.id_cliente
      JOIN zonas ON cliente.id_zona = zonas.zona_id
     LEFT JOIN local ON local.int_local_id=usuarioCarga.id_local
      WHERE consolidado_detalle.consolidado_id = " . $campo . " GROUP BY detalle_venta.id_producto  order BY nombre_grupo ");

        //echo $this->db->last_query();
        return $query->result_array();
    }


    function get_detalle_backup($campo)
    {
        $query = $this->db->query("SELECT *,zonas.*, usuarioCarga.nombre as userCarga, SUM(detalle_venta.cantidad) as cantidadTotal, chofer.nombre as chofernombre
      FROM `consolidado_detalle`
      LEFT JOIN venta_backup as venta ON venta.venta_id = consolidado_detalle.pedido_id
      LEFT JOIN consolidado_carga ON consolidado_carga.consolidado_id = consolidado_detalle.consolidado_id
      LEFT JOIN detalle_venta_backup as detalle_venta ON detalle_venta.id_venta = venta.venta_id
      LEFT JOIN producto ON producto.producto_id=detalle_venta.id_producto
      LEFT JOIN unidades ON unidades.id_unidad=detalle_venta.unidad_medida
      LEFT JOIN grupos ON grupos.id_grupo=producto.produto_grupo
      LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id
      LEFT JOIN  camiones ON camiones.camiones_id=consolidado_carga.camion
 LEFT JOIN usuario as chofer ON chofer.nUsuCodigo=camiones.id_trabajadores
     LEFT JOIN usuario as usuarioCarga ON usuarioCarga.nUsuCodigo=consolidado_carga.generado_por
     JOIN cliente ON cliente.id_cliente = venta.id_cliente
      JOIN zonas ON cliente.id_zona = zonas.zona_id
     LEFT JOIN local ON local.int_local_id=usuarioCarga.id_local
      WHERE consolidado_detalle.consolidado_id = " . $campo . " GROUP BY detalle_venta.id_producto  order BY nombre_grupo ");

        //echo $this->db->last_query();
        return $query->result_array();
    }

    function get_cantiad_vieja_by_product($product,$consolidado_id)
    {

        $q = "select sum(cantidad) as cantidadnueva from detalle_venta join consolidado_detalle on consolidado_detalle.pedido_id=detalle_venta.id_venta where id_producto=" . $product . " and consolidado_id=" . $consolidado_id . " GROUP by id_producto ";
        $query = $this->db->query($q);

        //echo $this->db->last_query();
        return $query->row_array();
    }

    function get_detalle_devueltos($campo, $in = false)
    {
        $q = "SELECT venta.*,consolidado_carga.*,detalle_venta_backup.*, zonas.*,
      producto.*,unidades.*,grupos.* ,unidades_has_producto.*,camiones.*,usuarioCarga.*, local.*, chofer.nombre as chofernombre,
      usuarioCarga.nombre as userCarga, SUM(detalle_venta_backup.cantidad) as cantidadTotal
      FROM `consolidado_detalle`
      LEFT JOIN venta ON venta.venta_id = consolidado_detalle.pedido_id
      LEFT JOIN consolidado_carga ON consolidado_carga.consolidado_id = consolidado_detalle.consolidado_id
      LEFT JOIN detalle_venta_backup ON detalle_venta_backup.id_venta = venta.venta_id

      LEFT JOIN producto ON producto.producto_id=detalle_venta_backup.id_producto
      LEFT JOIN unidades ON unidades.id_unidad=detalle_venta_backup.unidad_medida
      LEFT JOIN grupos ON grupos.id_grupo=producto.produto_grupo
      LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id
      LEFT JOIN  camiones ON camiones.camiones_id=consolidado_carga.camion
     LEFT JOIN usuario as usuarioCarga ON usuarioCarga.nUsuCodigo=consolidado_carga.generado_por
     LEFT JOIN usuario as chofer ON chofer.nUsuCodigo=camiones.id_trabajadores
     LEFT JOIN local ON local.int_local_id=usuarioCarga.id_local
      JOIN cliente ON cliente.id_cliente = venta.id_cliente
      JOIN zonas ON cliente.id_zona = zonas.zona_id
      WHERE consolidado_detalle.consolidado_id = " . $campo;
        if ($in != false) {
            $q = $q . " and venta_status IN " . $in;
        }
        $q = $q . " GROUP BY detalle_venta_backup.id_producto order BY nombre_grupo  ";
        $query = $this->db->query($q);

        //echo $this->db->last_query();
        return $query->result_array();
    }

    function get_documentoVenta_by_id($id, $in = false)
    {
        $q = "SELECT distinct(venta.venta_id),  consolidado_detalle.liquidacion_monto_cobrado, documento_venta.*, venta.venta_status, venta.total, credito.var_credito_estado,  (select SUM(total) FROM consolidado_carga
      LEFT JOIN consolidado_detalle ON consolidado_detalle.consolidado_id = consolidado_carga.consolidado_id
      LEFT JOIN venta ON venta.venta_id = consolidado_detalle.pedido_id
      WHERE consolidado_carga.consolidado_id = " . $id . ") as totalImporte
      FROM `venta`
      JOIN consolidado_detalle ON consolidado_detalle.pedido_id = venta.venta_id
      LEFT JOIN credito ON credito.id_venta = venta.venta_id
      JOIN documento_venta ON documento_venta.id_tipo_documento = venta.venta_id


      WHERE consolidado_detalle.consolidado_id = " . $id;

        if ($in != false) {
            $q = $q . " and venta_status IN " . $in;
        }

        $query = $this->db->query($q);


        return $query->result_array();

    }
    function get_documentoVentaBackup_by_id($id, $in = false)
    {
        $q = "SELECT distinct(venta_backup.venta_id),  consolidado_detalle.liquidacion_monto_cobrado, documento_venta.*, venta_backup.venta_status, venta_backup.total, credito.var_credito_estado,  (select SUM(total) FROM consolidado_carga
      LEFT JOIN consolidado_detalle ON consolidado_detalle.consolidado_id = consolidado_carga.consolidado_id
      LEFT JOIN venta_backup ON venta_backup.venta_id = consolidado_detalle.pedido_id
      WHERE consolidado_carga.consolidado_id = " . $id . ") as totalImporte
      FROM `venta_backup`
      JOIN consolidado_detalle ON consolidado_detalle.pedido_id = venta_backup.venta_id
      LEFT JOIN credito ON credito.id_venta = venta_backup.venta_id
      JOIN documento_venta ON documento_venta.id_tipo_documento = venta_backup.venta_id


      WHERE consolidado_detalle.consolidado_id = " . $id;

        if ($in != false) {
            $q = $q . " and venta_status IN " . $in;
        }

        $query = $this->db->query($q);


        return $query->result_array();

    }


    function get($id)
    {
        $this->db->where('consolidado_id', $id);
        $query = $this->db->get('consolidado_carga');
        return $query->row_array();

    }

    function set_consolidado($datos, $pedidos)
    {
        $this->db->trans_start(true);
        $this->db->trans_begin();

        $this->db->insert('consolidado_carga', $datos);

        $estatus = 'ENVIADO';
        $id_consolidado = $this->db->insert_id();
        $statusventa = array('venta_status' => $estatus);
        for ($i = 0; $i < count($pedidos); $i++) {
            if ($pedidos[$i] != 'on') {
                $insertar = array("consolidado_id" => $id_consolidado, "pedido_id" => $pedidos[$i]);
                $this->db->insert('consolidado_detalle', $insertar);

                $this->db->where('venta_id', $pedidos[$i]);
                $this->db->update('venta', $statusventa);

                $vendedor = $this->session->userdata('nUsuCodigo');
                $date = date('Y-m-d h:m:s');
                $data = array('venta_id' => $pedidos[$i], 'vendedor_id' => $vendedor, 'estatus' => $estatus,
                    'fecha' => $date);
                $this->db->insert('venta_estatus', $data);

            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function cambiarEstatus($id, $estatus)
    {
        $this->db->trans_start();
        $this->db->trans_begin();


        $estatusCons = array('status' => $estatus);

        $this->db->where('consolidado_id', $id);
        $this->db->update('consolidado_carga', $estatusCons);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }


    }


    function updateStatus($status)
    {
        $this->db->where('consolidado_id', $status['consolidado_id']);
        $this->db->update('consolidado_carga', $status);

    }

    function updateStatusVenta($statusVenta)
    {
        $this->db->where('venta_id', $statusVenta['venta_id']);
        $this->db->update('venta', $statusVenta);

    }

    function get_pedido($campo, $valor)
    {
        $this->db->select('*');
        $this->db->from('consolidado_detalle');
        $this->db->where($campo, $valor);
        $query = $this->db->get();
        return $query->result_array();
    }

    function updateDetalle($data)
    {
        $this->db->trans_start();
        $this->db->trans_begin();

        $this->db->where(array('pedido_id' => $data['pedido_id']));
        $this->db->update('consolidado_detalle', $data);


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function updateConsolidado($data, $pedidos)
    {
        $this->db->trans_start();
        $this->db->trans_begin();

        $this->db->where('consolidado_id', $data['consolidado_id']);
        $this->db->update('consolidado_carga', $data);

        $estatus = 'ENVIADO';
        $id_consolidado = $data['consolidado_id'];
        //VAlIDAR PEDIDOS EN ANTERIORES
        $query = $this->db->query("SELECT * FROM `consolidado_detalle` WHERE `consolidado_id`=" . $id_consolidado);
        $pedidosant = $query->result_array();

        for ($i = 0; $i < count($pedidosant); $i++) {
            if (in_array($pedidosant[$i]['pedido_id'], $pedidos, true) == FALSE) {
                $estatus = 'GENERADO';
                $statusventa = array('venta_status' => $estatus);

                $this->db->where('venta_id', $pedidosant[$i]['pedido_id']);
                $this->db->update('venta', $statusventa);

                $vendedor = $this->session->userdata('nUsuCodigo');
                $date = date('Y-m-d h:m:s');
                $data = array('venta_id' => $pedidosant[$i]['pedido_id'], 'vendedor_id' => $vendedor, 'estatus' => $estatus,
                    'fecha' => $date);
                $this->db->insert('venta_estatus', $data);
            }
        }
        //DELETE DE TABLA DETALLE
        $this->db->delete('consolidado_detalle', array('consolidado_id' => $id_consolidado));
        $estatus = 'ENVIADO';
        $statusventa = array('venta_status' => $estatus);
        for ($i = 0; $i < count($pedidos); $i++) {
            $insertar = array("consolidado_id" => $id_consolidado, "pedido_id" => $pedidos[$i]);
            $this->db->insert('consolidado_detalle', $insertar);

            $this->db->where('venta_id', $pedidos[$i]);
            $this->db->update('venta', $statusventa);

            $vendedor = $this->session->userdata('nUsuCodigo');
            $date = date('Y-m-d h:m:s');
            $data = array('venta_id' => $pedidos[$i], 'vendedor_id' => $vendedor, 'estatus' => $estatus,
                'fecha' => $date);
            $this->db->insert('venta_estatus', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function get_pedidos_by($condicion)
    {
        $this->db->select('venta.*, cliente.*, local.*, zonas.zona_nombre, condiciones_pago.*,documento_venta.*,usuario.*,
        (select SUM(metros_cubicos*detalle_venta.cantidad) from unidades_has_producto join detalle_venta
         on detalle_venta.id_producto=unidades_has_producto.producto_id where
         detalle_venta.id_venta=venta.venta_id and detalle_venta.unidad_medida=unidades_has_producto.id_unidad) as
         total_metos_cubicos,  (select count(id_detalle) from detalle_venta where id_venta=venta.venta_id and precio_sugerido>0) as preciosugerido');
        $this->db->from('consolidado_detalle');
        $this->db->join('venta', 'venta.venta_id=consolidado_detalle.pedido_id');
        $this->db->join('cliente', 'cliente.id_cliente=venta.id_cliente');
        $this->db->join('local', 'local.int_local_id=venta.local_id');
        $this->db->join('zonas', 'zonas.zona_id=cliente.id_zona');

        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->order_by('venta.venta_id', 'desc');
        $this->db->where($condicion);
        $query = $this->db->get();

        return $query->result();
    }

    function confirmacion_entregabanco($where)
    {
        $w = "DATE(consolidado_detalle.confirmacion_fecha) >= DATE('" . $where['fecha'] . "') AND DATE(consolidado_detalle.confirmacion_fecha) <= DATE('" . $where['fecha'] . "')
        AND consolidado_detalle.confirmacion_banco_id IS NOT NULL and consolidado_detalle.confirmacion_monto_cobrado_bancos is not null
        and consolidado_detalle.confirmacion_usuario is not null ";
        $this->db->select('SUM(confirmacion_monto_cobrado_bancos) as pago');
        $this->db->where($w);
        $this->db->from('consolidado_detalle');
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->row_array();
    }

    function confirmacion_entregacaja($where)
    {
        $w = "DATE(consolidado_detalle.confirmacion_fecha) >= DATE('" . $where['fecha'] . "') AND DATE(consolidado_detalle.confirmacion_fecha) <= DATE('" . $where['fecha'] . "')
        AND consolidado_detalle.confirmacion_caja_id IS NOT NULL and consolidado_detalle.confirmacion_monto_cobrado_caja is not null
        and consolidado_detalle.confirmacion_usuario is not null ";
        $this->db->select('SUM(confirmacion_monto_cobrado_caja) as pago');
        $this->db->where($w);
        $this->db->from('consolidado_detalle');
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->row_array();
    }

    function update_varios_detalles($data)
    {
        $this->db->trans_start();
        $this->db->trans_begin();
        //var_dump($data);
        $verificar_paso = false;
        if ($data['confirmar'] != false) {

            for ($i = 1; $i < count($data['confirmar']) + 1; $i++) {
                $campos = array();
                if ($data['confirmar'][$i] > 0) {
                    $verificar_paso = true;

                    $campos['confirmacion_usuario'] = $this->session->userdata('nUsuCodigo');
                    $campos['confirmacion_fecha'] = date('Y-m-d H:i:s');


                    if (isset($data['input_caja'][$i])) {

                        if ($data['input_caja'][$i] != "") {
                            $campos['confirmacion_monto_cobrado_caja'] = $data['input_caja'][$i];
                            $campos['confirmacion_caja_id'] = $this->session->userdata('caja');
                        }
                    }
                    if (isset($data['input_bancos'][$i])) {

                        if ($data['input_bancos'][$i] != "") {
                            $campos['confirmacion_monto_cobrado_bancos'] = $data['input_bancos'][$i];
                            $campos['confirmacion_banco_id'] = $data['bancos'][$i];
                        }
                    }

                    /****actualizo el credito con el monto confrmado mas lo que ya se habia pagado en pagos adelantados**/


                    $this->db->where(array('pedido_id' => $data['pedido_id'][$i], 'consolidado_id' => $data['consolidado_id']));
                    $this->db->update('consolidado_detalle', $campos);

                    $credito_actual = $this->venta_model->get_credito_by_venta($data['pedido_id'][$i]);

                    if (sizeof($credito_actual) > 0) {
                        $credito['dec_credito_montodebito'] = floatval($credito_actual[0]['dec_credito_montodebito']) + floatval($credito_actual[0]['confirmacion_monto_cobrado_bancos']) + $credito_actual[0]['confirmacion_monto_cobrado_caja'];

                        $credito['var_credito_estado'] = CREDITO_ACUENTA;
                        //  var_dump($credito_actual);
                        if ($credito['dec_credito_montodebito'] >= $credito_actual[0]['dec_credito_montodeuda']) {
                            $credito['var_credito_estado'] = CREDITO_CANCELADO;
                        }
                        $condition = array('id_venta' => $data['pedido_id'][$i]);
                        $this->venta_model->actualizarCredito($credito, $condition);
                    }


                }
            }

            if ($verificar_paso == true) {

                //esto lo saco del bucl porque deberia hacerse una sola vez
                $this->db->where(array('consolidado_id' => $data['consolidado_id']));
                $this->db->update('consolidado_carga', array('status' => "CONFIRMADO"));
            }

        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}