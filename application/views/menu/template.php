<?php
date_default_timezone_set('America/Lima');

// Base URL
$ruta = base_url();

/* Template variables */
$template = array(
    'name' => 'CD ARKAD',
    'version' => '2.0',
    'author' => 'Te Ayudo',
    'robots' => 'noindex, nofollow',
    'title' => 'CD ARKAD',
    'description' => 'Software de inventario, ventas y distribución',
    // true                     enable page preloader
    // false                    disable page preloader
    'page_preloader' => false,
    // true                     enable main menu auto scrolling when opening a submenu
    // false                    disable main menu auto scrolling when opening a submenu
    'menu_scroll' => true,
    // 'navbar-default'         for a light header
    // 'navbar-inverse'         for a dark header
    'header_navbar' => 'navbar-default',
    // ''                       empty for a static layout
    // 'navbar-fixed-top'       for a top fixed header / fixed sidebars
    // 'navbar-fixed-bottom'    for a bottom fixed header / fixed sidebars
    'header' => 'navbar-fixed-top',
    // ''                                               for a full main and alternative sidebar hidden by default (> 991px)
    // 'sidebar-visible-lg'                             for a full main sidebar visible by default (> 991px)
    // 'sidebar-partial'                                for a partial main sidebar which opens on mouse hover, hidden by default (> 991px)
    // 'sidebar-partial sidebar-visible-lg'             for a partial main sidebar which opens on mouse hover, visible by default (> 991px)
    // 'sidebar-alt-visible-lg'                         for a full alternative sidebar visible by default (> 991px)
    // 'sidebar-alt-partial'                            for a partial alternative sidebar which opens on mouse hover, hidden by default (> 991px)
    // 'sidebar-alt-partial sidebar-alt-visible-lg'     for a partial alternative sidebar which opens on mouse hover, visible by default (> 991px)
    // 'sidebar-partial sidebar-alt-partial'            for both sidebars partial which open on mouse hover, hidden by default (> 991px)
    // 'sidebar-no-animations'                          add this as extra for disabling sidebar animations on large screens (> 991px) - Better performance with heavy pages!
    'sidebar' => '',
    // ''                       empty for a static footer
    // 'footer-fixed'           for a fixed footer
    'footer' => '',
    // ''                       empty for default style
    // 'style-alt'              for an alternative main style (affects main page background as well as blocks style)
    'main_style' => '',
    // 'night', 'amethyst', 'modern', 'autumn', 'flatie', 'spring', 'fancy', 'fire' or '' leave empty for the Default Blue theme
    'theme' => 'flatie',
    // ''                       for default content in header
    // 'horizontal-menu'        for a horizontal menu in header
    // This option is just used for feature demostration and you can remove it if you like. You can keep or alter header's content in page_head.php
    'header_content' => 'horizontal-menu',
    'active_page' => basename($_SERVER['PHP_SELF'])
);

if ($this->session->userdata('tema')) {
    $template['theme'] = $this->session->userdata('tema');
}

/* Primary navigation array (the primary navigation will be created automatically based on this array, up to 3 levels deep) */
$primary_nav = array(
    // Menu Principal
    array(
        'name' => 'MENU PRINCIPAL',
        'url' => $ruta . 'principal',
        'icon' => 'fa fa-home',
        'slug' => 'home'
    ),

    // Inventario
    array(
        'name' => 'INVENTARIO',
        'slug' => 'inventario',
        'sub' => array(
            array(
                'name' => 'Productos',
                'url' => $ruta . 'producto',
                'icon' => 'gi gi-barcode',
                'slug' => 'productos'
            ),
            array(
                'name' => 'Categorias',
                'slug' => 'categorias',
                'icon' => 'fa fa-list',
                'sub' => array(
                    array(
                        'name' => 'Marcas',
                        'url' => $ruta . 'marca',
                        'icon' => 'fa fa-certificate',
                        'slug' => 'marcas'
                    ),
                    array(
                        'name' => 'Grupos',
                        'url' => $ruta . 'grupo',
                        'icon' => 'fa fa-cubes',
                        'slug' => 'gruposproductos'
                    ),
                    array(
                        'name' => 'Linea',
                        'url' => $ruta . 'subgrupo',
                        'icon' => 'fa fa-cubes',
                        'slug' => 'linea'
                    ),


                    array(
                        'name' => 'Sub-Linea',
                        'url' => $ruta . 'familia',
                        'icon' => 'fa fa-laptop',
                        'slug' => 'sublinea'
                    ),
                    array(
                        'name' => 'Familias',
                        'url' => $ruta . 'subfamilia',
                        'icon' => 'fa fa-laptop',
                        'slug' => 'familia'
                    ),

                    array(
                        'name' => 'Talla',
                        'url' => $ruta . 'linea',
                        'icon' => 'fa fa-th-large',
                        'slug' => 'talla'
                    ),


                )
            ),

        ),
    ),

    // Compras
    array(
        'name' => 'COMPRAS',
        'slug' => 'compras',
        'sub' => array(
            array(
                'name' => 'Registro de Compras',
                'url' => $ruta . 'ingresos?costos=true',
                'icon' => 'gi gi-cart_in',
                'slug' => 'registrarcompras'
            ),
            array(
                'name' => 'Registro de Existencia',
                'slug' => 'ingresoexistencia',
                'icon' => 'gi gi-cart_in',
                'url' => $ruta . 'ingresos?costos=false',
            ),
            array(
                'name' => 'Consultar Compras',
                'url' => $ruta . 'ingresos/consultar',
                'icon' => 'gi gi-history',
                'slug' => 'consultarcompras'
            ),
            array(
                'name' => 'Devolucion de Compras',
                'url' => $ruta . 'ingresos/devolucion',
                'icon' => 'gi gi-cart_out',
                'slug' => 'devolucioncompras'
            ),
        ),
    ),

    //Flujo de trabajo
    array(
        'name' => 'FLUJO DE TRABAJO',
        'slug' => 'flujodetrabajo',
        'sub' => array(
            array(
                'name' => 'Generar Pedido',
                'url' => $ruta . 'venta/pedidos',
                'icon' => 'fa fa-share',
                'slug' => 'generarpedidos'
            ),
            array(
                'name' => 'Bandeja de Pedidos',
                'url' => $ruta . 'venta/consultar?buscar=pedidos',
                'icon' => 'fa fa-list',
                'slug' => 'historialpedidos'
            ),
            array(
                'name' => 'Consolidado de Carga',
                'url' => $ruta . 'consolidadodecargas',
                'icon' => 'gi gi-notes_2',
                'slug' => 'consolidado'
            ),

            array(
                'name' => 'Liquidacion CGC',
                'url' => $ruta . 'consolidadodecargas/liquidacion',
                'icon' => 'fa  fa-truck',
                'slug' => 'liquidacioncdc'
            ),
        )
    ),

    // Cobranza
    array(
        'name' => 'COBRANZAS',
        'slug' => 'cobranzas',
        'sub' => array(
            array(
                'name' => 'Cuentas por Cobrar',
                'url' => $ruta . 'pago_pendiente/pagos',
                'icon' => 'gi gi-wallet',
                'slug' => 'pagospendientescobranzas'
            ),
            array(
                'name' => 'Confirmar Cobranzas',
                'url' => $ruta . 'pago_pendiente/confirmar_pago',
                'icon' => 'gi gi-ok',
                'slug' => 'liquidarcobranzas'
            )
        )
    ),

    // Caja y Bancos
    array(
        'name' => 'CAJA & BANCOS',
        'slug' => 'caja_bancos',
        'sub' => array(
            array(
                'name' => 'Caja',
                'url' => $ruta . 'cajas',
                'icon' => 'gi gi-nameplate',
                'slug' => 'cajas'
            ),
            array(
                'name' => 'Gastos',
                'url' => $ruta . 'gastos',
                'icon' => 'gi gi-parents',
                'slug' => 'gastos'
            ),
            array(
                'name' => 'Bancos',
                'url' => $ruta . 'banco',
                'icon' => 'gi gi-kiosk',
                'slug' => 'bancos'
            )
        )
    ),

    // Clientes
    array(
        'name' => 'CLIENTES',
        'slug' => 'clientespadre',
        'sub' => array(
            array(
                'name' => 'Gestión de Clientes',
                'url' => $ruta . 'cliente',
                'icon' => 'gi gi-parents',
                'slug' => 'clientes'
            ),
            array(
                'name' => 'Grupos de Clientes',
                'url' => $ruta . 'clientesgrupos',
                'icon' => 'fa fa-group',
                'slug' => 'gruposcliente'
            )
        )
    ),

    // Proveedores
    array(
        'name' => 'PROVEEDORES',
        'slug' => 'proveedores',
        'sub' => array(
            array(
                'name' => 'Gestión de Proveedores',
                'url' => $ruta . 'proveedor',
                'icon' => 'gi gi-vcard',
                'slug' => 'proveedor'
            ),
            array(
                'name' => 'Cuentas por Pagar',
                'url' => $ruta . 'proveedor/cuentas_por_pagar',
                'icon' => 'gi gi-wallet',
                'slug' => 'cuentasporpagar'
            ),
        )
    ),

    // Ventas
    array(
        'name' => 'VENTAS',
        'slug' => 'ventas',
        'sub' => array(
            array(
                'name' => 'Realizar Venta',
                'url' => $ruta . 'venta',
                'icon' => 'fa fa-share',
                'slug' => 'generarventa'
            ),

            array(
                'name' => 'Historial de Ventas',
                'url' => $ruta . 'venta/consultar',
                'icon' => 'fa fa-history',
                'slug' => 'historialventas'
            ),

            array(
                'name' => 'Anular Venta',
                'url' => $ruta . 'venta/cancelar',
                'icon' => 'gi gi-remove_2',
                'slug' => 'anularventa'
            ),

            array(
                'name' => 'Devoluciones',
                'url' => $ruta . 'venta/devolver',
                'icon' => 'fa fa-share',
                'slug' => 'devolucionventa'
            ),

            array(
                'name' => 'Reimprimir documentos',
                'url' => $ruta . 'venta/documentos',
                'icon' => 'fa fa-history',
                'slug' => 'reimprimir_documento'
            ),

            array(
                'name' => 'Promociones',
                'icon' => 'fa fa-gift',
                'sub' => array(

                    array(
                        'name' => 'Bonificaciones',
                        'url' => $ruta . 'bonificaciones',
                        'icon' => 'gi gi-parents',
                        'slug' => 'bonificaciones'
                    ),
                    array(
                        'name' => 'Descuentos',
                        'url' => $ruta . 'descuentos',
                        'icon' => 'gi gi-parents',
                        'slug' => 'descuentos'
                    ),
                ),
                'slug' => 'promociones'
            )
        )
    ),

    // Reportes
    array(
        'name' => 'REPORTES',
        'slug' => 'reportes',
        'sub' => array(
            array(
                'name' => 'Rep de Ventas',
                'icon' => 'fa fa-bar-chart',
                'slug' => 'reporteventas',
                'sub' => array(
                    array(
                        'name' => 'Resumen',
                        'icon' => 'fa fa-bar-chart',
                        'url' => $ruta . 'reporte/ventas',
                        'slug' => 'reporteventas'
                    ),
                    array(
                        'name' => 'Avance de Cobranzas',
                        'url' => $ruta . 'reporte/cobranzas',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteventas'
                    ),
                    array(
                        'name' => 'Estado de Cuenta',
                        'url' => $ruta . 'reporte/cliente_estado',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteventas'
                    ),
                    array(
                        'name' => 'Historial de Cobranzas',
                        'url' => $ruta . 'reporte/historial_cobranzas',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteventas'
                    ),
                    array(
                        'name' => 'Avance de Ventas',
                        'url' => $ruta . 'reporte/nota_entrega',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteventas'
                    ),
                    array(
                        'name' => 'Documentos',
                        'url' => $ruta . 'reporte/documentos',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteventas'
                    ),
                    array(
                        'name' => 'Clientes por atender',
                        'url' => $ruta . 'mapaVentas',
                        'icon' => 'fa fa-users',
                        'slug' => 'reporteventas'
                    ),
                    array(
                        'name' => 'Puntos de reparticion',
                        'url' => $ruta . 'puntosReparticion',
                        'icon' => 'fa  fa-map-marker',
                        'slug' => 'reporteventas'
                    ),
                    array(
                        'name' => 'Por productos',
                        'url' => $ruta . 'reporte/por_productos',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteventas'
                    )
                ),
            ),

            array(
                'name' => 'Rep de Compras',
                'icon' => 'fa fa-bar-chart',
                'slug' => 'reportecompras',
                'sub' => array(
                    array(
                        'name' => 'Resumen',
                        'icon' => 'fa fa-bar-chart',
                        'url' => $ruta . 'reporte_compra/compras',
                        'slug' => 'reportecompras'
                    ),
                    array(
                        'name' => 'Cuentas por Pagar',
                        'url' => $ruta . 'reporte_compra/cuentas',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reportecompras'
                    ),
                    array(
                        'name' => 'Avance de Pagos',
                        'url' => $ruta . 'reporte_compra/proveedor_estado',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reportecompras'
                    ),
                    array(
                        'name' => 'Por productos',
                        'url' => $ruta . 'reporte_compra/por_productos',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reportecompras'
                    )
                ),
            ),

            array(
                'name' => 'Rep de Inventario',
                'icon' => 'fa fa-bar-chart',
                'slug' => 'reporteinventario',
                'sub' => array(
                    array(
                        'name' => 'Stock de Producto',
                        'url' => $ruta . 'producto/stock',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteinventario'
                    ),
                    array(
                        'name' => 'Stock Comprometido',
                        'icon' => 'fa fa-bar-chart',
                        'url' => $ruta . 'reporte/stock_transito',
                        'slug' => 'reporteinventario'
                    ),
                    array(
                        'name' => 'Kardex',
                        'url' => $ruta . 'inventario/movimiento',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteinventario'
                    ),
                    array(
                        'name' => 'Estado del Producto',
                        'url' => $ruta . 'producto/reporteEstado',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteinventario'
                    ),
                    array(
                        'name' => 'Por productos',
                        'url' => $ruta . 'reporte_general/por_productos',
                        'icon' => 'fa fa-bar-chart',
                        'slug' => 'reporteinventario'
                    )
                ),
            ),

            /*Trabajar en este reporte con bencho
            array(
                'name' => 'Estado del Producto',
                'url' => $ruta . 'producto/reporteEstado',
                'icon' => 'fa fa-history',
                'slug' => 'reporteestado'
            ),*/
            array(
                'name' => 'Venta vs Compra',
                'url' => $ruta . 'reporte_general/ventas_compras',
                'icon' => 'fa fa-bar-chart',
                'slug' => 'ventas_v_compras'
            ),
            array(
                'name' => 'Rotación de Productos',
                'url' => $ruta . 'venta/reporteRotacionZona',
                'icon' => 'gi gi-heat',
                'slug' => 'rotacion_productos'
            ),
            array(
                'name' => 'Utilidad por Producto',
                'url' => $ruta . 'venta/reporteUtilidades',
                'icon' => 'fa fa-bar-chart',
                'slug' => 'utilidad_por_producto'
            )
        )

    ),

    //Configuraciones
    array(
        'name' => 'CONFIGURACIONES',
        'slug' => 'configuraciones',
        'sub' => array(
            array(
                'name' => 'Opciones',
                'url' => $ruta . 'opciones',
                'icon' => 'fa fa-cogs',
                'slug' => 'opcionesgenerales'
            ),
            array(
                'name' => 'Usuarios',
                'icon' => 'fa fa-users',
                'sub' => array(
                    array(
                        'name' => 'Trabajadores',
                        'url' => $ruta . 'usuario',
                        'icon' => 'fa fa-users',
                        'slug' => 'usuarios'
                    ),
                    array(
                        'name' => 'Cargos',
                        'url' => $ruta . 'usuariosgrupos',
                        'icon' => 'gi gi-parents',
                        'slug' => 'gruposusuarios'
                    ),
                ),
                'slug' => 'usuariospadre'
            ),

            array(
                'name' => 'Camiones',
                'url' => $ruta . 'camiones',
                'icon' => 'fa  fa-truck',
                'slug' => 'camiones'
            ),

            array(
                'name' => 'Unidades de Medida',
                'url' => $ruta . 'unidades',
                'icon' => 'fa fa-list-ol',
                'slug' => 'unidadesmedida'
            ),

            array(
                'name' => 'Ubigeo',
                'icon' => 'fa fa-globe',
                'sub' => array(
                    array(
                        'name' => 'Pais',
                        'url' => $ruta . 'pais',
                        'icon' => 'fa fa-users',
                        'slug' => 'pais'
                    ),
                    array(
                        'name' => 'Ciudad',
                        'url' => $ruta . 'estados',
                        'icon' => 'gi gi-shop_window',
                        'slug' => 'estado'
                    ),
                    array(
                        'name' => 'Distrito',
                        'url' => $ruta . 'ciudad',
                        'icon' => 'gi gi-shop_window',
                        'slug' => 'ciudad'
                    ),
                    array(
                        'name' => 'Zonas de Reparto',
                        'url' => $ruta . 'zona',
                        'icon' => 'gi gi-globe',
                        'slug' => 'zona'
                    )
                ),
                'slug' => 'ubigeo'

            ),

            array(
                'name' => 'Tipos de Gasto',
                'url' => $ruta . 'tiposdegasto',
                'icon' => 'fa fa-list-ol',
                'slug' => 'tiposgasto'
            ),

        )

    ),

    // Opciones
    array(
        'name' => 'OPCIONES',
        'slug' => 'opciones',
        'sub' => array(
            array(
                'name' => 'Metodos de Pago',
                'url' => $ruta . 'metodosdepago',
                'icon' => 'fa fa-money',
                'slug' => 'metodospago'
            ),

            array(
                'name' => 'Condiciones de Pago',
                'url' => $ruta . 'condicionespago',
                'icon' => 'fa fa-ticket',
                'slug' => 'condicionespago'
            ),
            array(
                'name' => 'Locales',
                'url' => $ruta . 'local',
                'icon' => 'gi gi-shop_window',
                'slug' => 'locales'
            ),
            array(
                'name' => 'Precios',
                'url' => $ruta . 'precio',
                'icon' => 'fa fa-money',
                'slug' => 'precios'
            ),
            array(
                'name' => 'Impuestos',
                'url' => $ruta . 'impuesto',
                'icon' => 'fa fa-money',
                'slug' => 'impuestos'
            ),
        )

    ),

);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SOFTWARE DE INVENTARIO, VENTAS Y DISTRIBUCIÓN</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <meta name="x-api-key" content="<?= $this->session->userdata('api_key'); ?>"/>

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="<?php echo $ruta; ?>recursos/img/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon57.png" sizes="57x57">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon72.png" sizes="72x72">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon76.png" sizes="76x76">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon114.png" sizes="114x114">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon120.png" sizes="120x120">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon144.png" sizes="144x144">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon152.png" sizes="152x152">

    <!-- END Icons -->
    <!-- Stylesheets -->
    <!-- Bootstrap is included in its original form, unaltered -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/jquery-ui.css">
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/bootstrap.min.css?v="<?php echo date('ims') ?>>

    <!-- Related styles of various icon packs and plugins -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/plugins.css">

    <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/main.css">

    <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->
    <link id="theme-link" rel="stylesheet"
          href="<?php if ($template['theme'] != '') {
              echo $template['theme'];
          } ?>">

    <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/themes.css">
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/bootstrap-select.min.css">

    <!-- END Stylesheets -->

    <!-- Modernizr (browser feature detection library) & Respond.js (Enable responsive CSS code on browsers that don't support it, eg IE8) -->
    <script src="<?php echo $ruta; ?>recursos/js/vendor/modernizr-2.7.1-respond-1.4.2.min.js"></script>

    <!-- Remember to include excanvas for IE8 chart support -->
    <!--[if IE 8]>
    <script src="<?php echo $ruta?>recursos/js/helpers/excanvas.min.js"></script><![endif]-->

    <!-- Include Jquery library from Google's CDN but if something goes wrong get Jquery from local file (Remove 'http:' if you have SSL) -->
    <script src="<?php echo $ruta; ?>recursos/js/vendor/jquery-1.11.1.min.js"></script>

    <!-- Bootstrap.js, Jquery plugins and Custom JS code -->
    <script>window.onerror = function () {
            return true;
        } </script>
    <script src="<?php echo base_url() ?>recursos/js/jquery-ui.js"></script>
    <script src="<?php echo $ruta ?>recursos/js/vendor/bootstrap.min.js"></script>
    <script src="<?php echo $ruta ?>recursos/js/pages/compMaps.js"></script>


    <script src="<?php echo $ruta ?>recursos/js/plugins.js"></script>

    <script src="<?php echo $ruta ?>recursos/js/app.js"></script>
    <!--<script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>-->
    <script src="<?php echo $ruta; ?>recursos/js/locationpicker.jquery.js"></script>
    <script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
    <script src="<?php echo $ruta ?>recursos/js/helpers/gmaps.min.js"></script>
    <script src="<?php echo $ruta; ?>recursos/js/bootstrap-select.min.js"></script>
    <script src="<?php echo $ruta; ?>recursos/js/common.js"></script>
    <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
    <script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>


    <script>
        var baseurl = '<?php echo base_url();?>';
    </script>
    <style>
        table th {
            font-size: 11px !important;
            padding: 6px 2px;
            text-align: center;
            vertical-align: middle;
        }

        table td {
            font-size: 10px !important;
        }

        .loading-icon {
            background: url("<?php echo $ruta ?>recursos/img/loading.gif") no-repeat;
            width: 192px;
            height: 24px;
            margin: 0 auto;
        }

        .btn, .btn-sm, .btn-xs {
            padding: 1px 5px !important;
            font-size: 12px !important;
            line-height: 1.5 !important;
            border-radius: 3px !important;
        }

        .form-control {
            font-size: 12px !important;
            padding: 2px 3px !important;
            margin: 1px 0 !important;
        }
    </style>

</head>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'x-api-key': $('meta[name="x-api-key"]').attr('content')
            }
        });
    });
</script>
<body>

<div id="page-wrapper"<?php if ($template['page_preloader']) {
    echo ' class="page-loading"';
} ?>>
    <!-- Preloader -->
    <!-- Preloader functionality (initialized in js/app.js) - pageLoading() -->
    <!-- Used only if page preloader is enabled from inc/config (PHP version) or the class 'page-loading' is added in #page-wrapper element (HTML version) -->
    <div class="preloader themed-background">
        <h1 class="push-top-bottom text-light text-center"><strong>Te</strong>Ayudo</h1>

        <div class="inner">
            <h3 class="text-light visible-lt-ie9 visible-lt-ie10"><strong>Cargando..</strong></h3>

            <div class="preloader-spinner hidden-lt-ie9 hidden-lt-ie10"></div>
        </div>
    </div>
    <!-- END Preloader -->

    <!-- Page Container -->
    <!-- In the PHP version you can set the following options from inc/config file -->
    <!--
        Available #page-container classes:

        '' (None)                                       for a full main and alternative sidebar hidden by default (> 991px)

        'sidebar-visible-lg'                            for a full main sidebar visible by default (> 991px)
        'sidebar-partial'                               for a partial main sidebar which opens on mouse hover, hidden by default (> 991px)
        'sidebar-partial sidebar-visible-lg'            for a partial main sidebar which opens on mouse hover, visible by default (> 991px)

        'sidebar-alt-visible-lg'                        for a full alternative sidebar visible by default (> 991px)
        'sidebar-alt-partial'                           for a partial alternative sidebar which opens on mouse hover, hidden by default (> 991px)
        'sidebar-alt-partial sidebar-alt-visible-lg'    for a partial alternative sidebar which opens on mouse hover, visible by default (> 991px)

        'sidebar-partial sidebar-alt-partial'           for both sidebars partial which open on mouse hover, hidden by default (> 991px)

        'sidebar-no-animations'                         add this as extra for disabling sidebar animations on large screens (> 991px) - Better performance with heavy pages!

        'style-alt'                                     for an alternative main style (without it: the default style)
        'footer-fixed'                                  for a fixed footer (without it: a static footer)

        'disable-menu-autoscroll'                       add this to disable the main menu auto scrolling when opening a submenu

        'header-fixed-top'                              has to be added only if the class 'navbar-fixed-top' was added on header.navbar
        'header-fixed-bottom'                           has to be added only if the class 'navbar-fixed-bottom' was added on header.navbar
    -->
    <?php
    $page_classes = '';

    if ($template['header'] == 'navbar-fixed-top') {
        $page_classes = 'header-fixed-top';
    } else if ($template['header'] == 'navbar-fixed-bottom') {
        $page_classes = 'header-fixed-bottom';
    }

    if ($template['sidebar']) {
        $page_classes .= (($page_classes == '') ? '' : ' ') . $template['sidebar'];
    }

    if ($template['main_style'] == 'style-alt') {
        $page_classes .= (($page_classes == '') ? '' : ' ') . 'style-alt';
    }

    if ($template['footer'] == 'footer-fixed') {
        $page_classes .= (($page_classes == '') ? '' : ' ') . 'footer-fixed';
    }

    if (!$template['menu_scroll']) {
        $page_classes .= (($page_classes == '') ? '' : ' ') . 'disable-menu-autoscroll';
    }
    ?>
    <div id="page-container"<?php if ($page_classes) {
        echo ' class="' . $page_classes . '"';
    } ?>>
        <!-- Alternative Sidebar -->
        <div id="sidebar-alt">
            <!-- Wrapper for scrolling functionality -->
            <div class="sidebar-scroll">
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <!-- Chat -->
                    <!-- Chat demo functionality initialized in js/app.js -> chatUi() -->

                    <!--  END Chat Talk -->
                    <!-- END Chat -->

                    <!-- Activity -->

                    <!-- END Messages -->
                </div>
                <!-- END Sidebar Content -->
            </div>
            <!-- END Wrapper for scrolling functionality -->
        </div>
        <!-- END Alternative Sidebar -->

        <!-- Main Sidebar -->
        <div id="sidebar">
            <!-- Wrapper for scrolling functionality -->
            <div class="sidebar-scroll">
                <!-- Sidebar Content -->
                <div class="sidebar-content">

                    <!-- Brand -->
                    <a href="<?= $ruta ?>principal" class="sidebar-brand">
                        <i class="gi gi-cart_out"></i><strong>CD ARKAD
                            <spam style="font-size: 10px"> V.01</spam>
                        </strong>
                    </a>
                    <!-- END Brand -->

                    <!-- User Info -->
                    <div class="sidebar-section sidebar-user clearfix">
                        <div class="sidebar-user-avatar">
                            <a href="<?= $ruta ?>principal">
                                <img src="<?php echo $ruta ?>recursos/img/logo.jpg"
                                     alt="avatar">
                            </a>
                        </div>
                        <div class="sidebar-user-name"><?= $this->session->userdata('nombre') ?></div>
                        <div class="sidebar-user-links">
                            <!-- <a href="page_ready_user_profile.php" data-toggle="tooltip" data-placement="bottom"
                                title="Profile"><i class="gi gi-user"></i></a>
                             <a href="page_ready_inbox.php" data-toggle="tooltip" data-placement="bottom"
                                title="Messages"><i class="gi gi-envelope"></i></a>
                             <!-- Opens the user settings modal that can be found at the bottom of each page (page_footer.php in PHP version) -->
                            <a href="#modal-user-settings" data-toggle="modal" class="enable-tooltip"
                               data-placement="bottom" title="Configuración"><i class="gi gi-user"></i></a>
                            <a href="logout" data-toggle="tooltip" data-placement="bottom" title="Cerrar Sesion"><i
                                        class="gi gi-exit"></i></a>
                        </div>
                    </div>
                    <!-- END User Info -->

                    <!-- Theme Colors -->
                    <!-- Change Color Theme functionality can be found in js/app.js - templateOptions() -->
                    <ul class="sidebar-section sidebar-themes clearfix">
                        <li class="active">
                            <a href="javascript:void(0)" class="themed-background-dark-default themed-border-default"
                               data-theme="default" data-toggle="tooltip" title="Default Blue"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="themed-background-dark-night themed-border-night"
                               data-theme="css/themes/night.css" data-toggle="tooltip" title="Night"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="themed-background-dark-amethyst themed-border-amethyst"
                               data-theme="css/themes/amethyst.css" data-toggle="tooltip" title="Amethyst"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="themed-background-dark-modern themed-border-modern"
                               data-theme="css/themes/modern.css" data-toggle="tooltip" title="Modern"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="themed-background-dark-autumn themed-border-autumn"
                               data-theme="css/themes/autumn.css" data-toggle="tooltip" title="Autumn"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="themed-background-dark-flatie themed-border-flatie"
                               data-theme="css/themes/flatie.css" data-toggle="tooltip" title="Flatie"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="themed-background-dark-spring themed-border-spring"
                               data-theme="css/themes/spring.css" data-toggle="tooltip" title="Spring"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="themed-background-dark-fancy themed-border-fancy"
                               data-theme="css/themes/fancy.css" data-toggle="tooltip" title="Fancy"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="themed-background-dark-fire themed-border-fire"
                               data-theme="css/themes/fire.css" data-toggle="tooltip" title="Fire"></a>
                        </li>
                    </ul>
                    <!-- END Theme Colors -->

                    <?php if ($primary_nav) { ?>
                        <!-- Sidebar Navigation -->
                        <ul class="sidebar-nav">
                            <?php foreach ($primary_nav as $key => $link) {
                                $link_class = '';
                                $li_active = '';
                                $menu_link = '';

                                // Get 1st level link's vital info
                                $url = (isset($link['url']) && $link['url']) ? $link['url'] : '#';
                                $active = (isset($link['url']) && ($template['active_page'] == $link['url'])) ? ' active' : '';
                                $icon = (isset($link['icon']) && $link['icon']) ? '<i class="' . $link['icon'] . ' sidebar-nav-icon"></i>' : '';
                                $slug = (isset($link['slug']) && $link['slug']) ? $link['slug'] : '';

                                // Check if the link has a submenu
                                if (isset($link['sub']) && $link['sub']) {
                                    // Since it has a submenu, we need to check if we have to add the class active
                                    // to its parent li element (only if a 2nd or 3rd level link is active)
                                    foreach ($link['sub'] as $sub_link) {
                                        if (in_array($template['active_page'], $sub_link)) {
                                            $li_active = ' class="active menulink"';
                                            break;
                                        }

                                        // 3rd level links
                                        if (isset($sub_link['sub']) && $sub_link['sub']) {
                                            foreach ($sub_link['sub'] as $sub2_link) {
                                                if (in_array($template['active_page'], $sub2_link)) {
                                                    $li_active = ' class="active menulink"';
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    $menu_link = 'sidebar-nav-menu';
                                }

                                // Create the class attribute for our link
                                if ($menu_link || $active) {
                                    $link_class = ' class="' . $menu_link . $active . '  "';
                                }
                                ?>
                                <?php if ($url == 'header') { // if it is a header and not a link

                                    if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), $slug)) { ?>

                                        <li class="sidebar-header">
                                            <?php if (isset($link['opt']) && $link['opt']) { // If the header has options set ?>
                                                <span
                                                        class="sidebar-header-options clearfix"><?php echo $link['opt']; ?></span>
                                            <?php } ?>
                                            <span class="sidebar-header-title"><?php echo $link['name']; ?></span>
                                        </li>
                                    <?php }
                                } else { // If it is a link
                                    if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), $slug) or $slug == 'home') {
                                        ?>
                                        <li <?php echo $li_active; ?>>
                                            <a href="<?php echo $url; ?>"<?php echo $link_class; ?> class="menulink">
                                                <?php if (isset($link['sub']) && $link['sub']) { // if the link has a submenu ?>
                                                    <i class="fa fa-angle-left sidebar-nav-indicator "></i>
                                                <?php }
                                                echo $icon . $link['name']; ?>
                                            </a>
                                            <?php if (isset($link['sub']) && $link['sub']) { // if the link has a submenu
                                                if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), $slug)) { ?>
                                                    <ul>
                                                        <?php
                                                        foreach ($link['sub'] as $sub_link) {
                                                            $link_class = '';
                                                            $li_active = '';
                                                            $submenu_link = '';

                                                            // Get 2nd level link's vital info
                                                            $url = (isset($sub_link['url']) && $sub_link['url']) ? $sub_link['url'] : '#';
                                                            $active = (isset($sub_link['url']) && ($template['active_page'] == $sub_link['url'])) ? ' active' : '';
                                                            $slug = $sub_link['slug'];
                                                            $icon = (isset($sub_link['icon']) && $sub_link['icon']) ? '<i class="' . $sub_link['icon'] . ' sidebar-nav-icon"></i>' : '';

                                                            // Check if the link has a submenu
                                                            if (isset($sub_link['sub']) && $sub_link['sub']) {
                                                                // Since it has a submenu, we need to check if we have to add the class active
                                                                // to its parent li element (only if a 3rd level link is active)
                                                                foreach ($sub_link['sub'] as $sub2_link) {
                                                                    if (in_array($template['active_page'], $sub2_link)) {
                                                                        $li_active = ' class="active menulink"';
                                                                        break;
                                                                    }
                                                                }

                                                                $submenu_link = 'sidebar-nav-submenu';
                                                            }

                                                            if ($submenu_link || $active) {
                                                                $link_class = ' class="' . $submenu_link . $active . '"';
                                                            }

                                                            if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), $slug)) { ?>
                                                                <li <?php echo $li_active; ?>>
                                                                    <a href="<?php echo $url; ?>"<?php echo $link_class; ?>
                                                                       class="menulink">
                                                                        <?php if (isset($sub_link['sub']) && $sub_link['sub']) { ?>
                                                                            <i class="fa fa-angle-left sidebar-nav-indicator"></i>
                                                                        <?php }
                                                                        echo $icon . $sub_link['name']; ?>
                                                                    </a>
                                                                    <?php if (isset($sub_link['sub']) && $sub_link['sub']) {
                                                                        if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), $slug)) { ?>
                                                                            <ul>
                                                                                <?php
                                                                                foreach ($sub_link['sub'] as $sub2_link) {
                                                                                    // Get 3rd level link's vital info
                                                                                    $url = (isset($sub2_link['url']) && $sub2_link['url']) ? $sub2_link['url'] : '#';
                                                                                    $active = (isset($sub2_link['url']) && ($template['active_page'] == $sub2_link['url'])) ? ' class="active"' : '';
                                                                                    if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), $sub2_link['slug'])) { ?>
                                                                                        <li>
                                                                                            <a href="<?php echo $url; ?>"<?php echo $active ?>
                                                                                               class="menulink"><?php echo $sub2_link['name']; ?></a>
                                                                                        </li>
                                                                                    <?php }
                                                                                } ?>
                                                                            </ul>
                                                                        <?php }
                                                                    } ?>
                                                                </li>
                                                            <?php }
                                                        } ?>
                                                    </ul>
                                                <?php }
                                            } ?>
                                        </li>
                                        <?php
                                    }
                                } ?>
                            <?php } ?>
                        </ul>
                        <!-- END Sidebar Navigation -->
                    <?php } ?>


                    <!-- END Sidebar Notifications -->
                </div>
                <!-- END Sidebar Content -->
            </div>
            <!-- END Wrapper for scrolling functionality -->
        </div>
        <!-- END Main Sidebar -->

        <!-- Main Container -->
        <div id="main-container">
            <!-- Header -->
            <!-- In the PHP version you can set the following options from inc/config file -->
            <!--
                Available header.navbar classes:

                'navbar-default'            for the default light header
                'navbar-inverse'            for an alternative dark header

                'navbar-fixed-top'          for a top fixed header (fixed sidebars with scroll will be auto initialized, functionality can be found in js/app.js - handleSidebar())
                    'header-fixed-top'      has to be added on #page-container only if the class 'navbar-fixed-top' was added

                'navbar-fixed-bottom'       for a bottom fixed header (fixed sidebars with scroll will be auto initialized, functionality can be found in js/app.js - handleSidebar()))
                    'header-fixed-bottom'   has to be added on #page-container only if the class 'navbar-fixed-bottom' was added
            -->
            <header class="navbar<?php if ($template['header_navbar']) {
                echo ' ' . $template['header_navbar'];
            } ?><?php if ($template['header']) {
                echo ' ' . $template['header'];
            } ?>">
                <?php if ($template['header_content'] == 'horizontal-menu') { // Horizontal Menu Header Content ?>
                    <!-- Navbar Header -->
                    <div class="navbar-header">
                        <!-- Horizontal Menu Toggle + Alternative Sidebar Toggle Button, Visible only in small screens (< 768px) -->
                        <ul class="nav navbar-nav-custom pull-right visible-xs">
                            <li>
                                <a href="javascript:void(0)" data-toggle="collapse"
                                   data-target="#horizontal-menu-collapse">Menu</a>
                            </li>


                        </ul>
                        <!-- END Horizontal Menu Toggle + Alternative Sidebar Toggle Button -->

                        <!-- Main Sidebar Toggle Button -->
                        <ul class="nav navbar-nav-custom">
                            <li>
                                <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar');">
                                    <i class="fa fa-bars fa-fw"></i>
                                </a>
                            </li>
                        </ul>
                        <!-- END Main Sidebar Toggle Button -->
                    </div>
                    <!-- END Navbar Header -->

                    <!-- Alternative Sidebar Toggle Button, Visible only in large screens (> 767px) -->
                    <!--<ul class="nav navbar-nav-custom pull-right hidden-xs">
                        <li>
                            <!-- If you do not want the main sidebar to open when the alternative sidebar is closed, just remove the second parameter: App.sidebar('toggle-sidebar-alt'); -->
                    <!--   <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar-alt', 'toggle-other');">
                           <i class="gi gi-share_alt"></i>
                           <span class="label label-primary label-indicator animation-floating">4</span>
                       </a>
                   </li>
               </ul>-->
                    <ul class="nav navbar-nav-custom pull-right">
                        <!-- Alternative Sidebar Toggle Button -->

                        <!-- END Alternative Sidebar Toggle Button -->

                        <!-- User Dropdown -->
                        <li class="dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?php echo $ruta; ?>recursos/img/logo.jpg"
                                     alt="avatar">
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-custom dropdown-menu-right">
                                <li>
                                    <!-- Opens the user settings modal that can be found at the bottom of each page (page_footer.php in PHP version) -->
                                    <a href="#modal-user-settings" data-toggle="modal">
                                        <i class="fa fa-user fa-fw pull-right"></i>
                                        Mi perfil
                                        <input type="hidden" value="<?= $ruta; ?>" id="ruta_base">
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?= $ruta; ?>logout"><i class="fa fa-ban fa-fw pull-right"></i> Cerrar
                                        Sesión</a>
                                </li>
                            </ul>
                        </li>
                        <!-- END User Dropdown -->
                    </ul>
                    <!-- END Alternative Sidebar Toggle Button -->

                    <!-- Horizontal Menu + Search -->
                    <div id="horizontal-menu-collapse" class="collapse navbar-collapse">
                        <ul class="nav navbar-nav">
                            <?php if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'ad_stock')) { ?>

                                <li>
                                    <a class="menulink" href="<?= $ruta ?>producto/stock">Stock Producto (F2)</a>
                                </li>
                            <?php } ?>

                            <?php if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'ad_avanceventas')) { ?>
                                <li>
                                <a class="menulink" href="<?= $ruta ?>reporte/nota_entrega">Avance de Ventas</a>
                                </li><?php } ?>

                            <?php if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'ad_avancecobranzas')) { ?>
                                <li>
                                <a class="menulink" href="<?= $ruta ?>reporte/cobranzas">Avance de Cobranzas</a>
                                </li><?php } ?>

                            <?php if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'ad_estadodecuenta')) { ?>
                                <li>
                                    <a class="menulink" href="<?= $ruta ?>reporte/cliente_estado">Estado de Cuentas
                                        Clientes</a>
                                </li>
                            <?php } ?>

                            <?php if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'ad_reporteventas')) { ?>
                                <li>
                                    <a class="menulink" href="<?= $ruta ?>reporte/historial_cobranzas">Historial de
                                        Cobranza</a>
                                </li>
                            <?php } ?>

                            <?php if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'ad_resumencompras')) { ?>
                                <li>
                                    <a class="menulink" href="<?= $ruta ?>reporte_compra/compras">Resumen de Compras</a>
                                </li>
                            <?php } ?>
                        </ul>
                        <!--  <form action="page_ready_search_results.php" class="navbar-form navbar-left" role="search">
                              <div class="form-group">
                                  <input type="text" class="form-control" placeholder="Search..">
                              </div>
                          </form>-->
                    </div>
                    <!-- END Horizontal Menu + Search -->
                <?php } else { // Default Header Content  ?>
                    <!-- Left Header Navigation -->
                    <ul class="nav navbar-nav-custom">
                        <!-- Main Sidebar Toggle Button -->
                        <li>
                            <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar');">
                                <i class="fa fa-bars fa-fw"></i>
                            </a>
                        </li>
                        <!-- END Main Sidebar Toggle Button -->

                        <!-- Template Options -->
                        <!-- Change Options functionality can be found in js/app.js - templateOptions() -->
                        <li class="dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="gi gi-settings"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-custom dropdown-options">
                                <li class="dropdown-header text-center">Header Style</li>
                                <li>
                                    <div class="btn-group btn-group-justified btn-group-sm">
                                        <a href="javascript:void(0)" class="btn btn-primary"
                                           id="options-header-default">Light</a>
                                        <a href="javascript:void(0)" class="btn btn-primary"
                                           id="options-header-inverse">Dark</a>
                                    </div>
                                </li>
                                <li class="dropdown-header text-center">Page Style</li>
                                <li>
                                    <div class="btn-group btn-group-justified btn-group-sm">
                                        <a href="javascript:void(0)" class="btn btn-primary" id="options-main-style">Default</a>
                                        <a href="javascript:void(0)" class="btn btn-primary"
                                           id="options-main-style-alt">Alternative</a>
                                    </div>
                                </li>
                                <li class="dropdown-header text-center">Main Layout</li>
                                <li>
                                    <button class="btn btn-sm btn-block btn-primary" id="options-header-top">Fixed
                                        Side/Header (Top)
                                    </button>
                                    <button class="btn btn-sm btn-block btn-primary" id="options-header-bottom">Fixed
                                        Side/Header (Bottom)
                                    </button>
                                </li>
                                <li class="dropdown-header text-center">Footer</li>
                                <li>
                                    <div class="btn-group btn-group-justified btn-group-sm">
                                        <a href="javascript:void(0)" class="btn btn-primary" id="options-footer-static">Default</a>
                                        <a href="javascript:void(0)" class="btn btn-primary" id="options-footer-fixed">Fixed</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- END Template Options -->
                    </ul>
                    <!-- END Left Header Navigation -->

                    <!-- Search Form -->
                    <form action="page_ready_search_results.php" method="post" class="navbar-form-custom" role="search">
                        <div class="form-group">
                            <input type="text" id="top-search" name="top-search" class="form-control"
                                   placeholder="Search..">
                        </div>
                    </form>
                    <!-- END Search Form -->

                    <!-- Right Header Navigation -->
                    <ul class="nav navbar-nav-custom pull-right">
                        <!-- Alternative Sidebar Toggle Button -->

                        <!-- END Alternative Sidebar Toggle Button -->


                    </ul>
                    <!-- END Right Header Navigation -->
                <?php } ?>
            </header>
            <!-- END Header -->


            <div id="page-content">
                <!-- Charts Header -->
                <?php echo $cuerpo ?>

            </div>
            <!-- END Page Content -->

            <!-- Footer -->
            <footer class="clearfix">
                <!--<div class="pull-right">
                    Crafted by <a href="http://teayudo.pe"
                                  target="_blank">Te Ayudo</a>
                </div>-->
                <div class="pull-left">
                    <span id="year-copy"></span> &copy; <a href="http://goo.gl/TDOSuC"
                                                           target="_blank"><?php echo $template['name'] . ' ' . $template['version']; ?></a>
                </div>
            </footer>
            <!-- END Footer -->
        </div>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->
</div>
<!-- END Page Wrapper -->

<!-- Scroll to top link, initialized in js/app.js - scrollToTop() -->
<a href="#" id="to-top"><i class="fa fa-angle-double-up"></i></a>

<!-- User Settings, modal which opens from Settings link (found in top right user menu) and the Cog link (found in sidebar user info) -->
<div id="modal-user-settings" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h2 class="modal-title"><i class="fa fa-pencil"></i> Mi Perfil</h2>
            </div>
            <!-- END Modal Header -->

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="<?= $ruta ?>/usuario/registrar" method="post" id="modal-user-settings-form"
                      enctype="multipart/form-data"
                      class="form-horizontal form-bordered" onsubmit="return false;">
                    <fieldset>
                        <legend>Informaci&oacute;n</legend>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Nombre de Usuario</label>


                            <input type="hidden" value="<?= $this->session->userdata('nUsuCodigo') ?>"
                                   name="nUsuCodigo">
                            <input type="hidden" value="<?= $this->session->userdata('username') ?>" name="username">

                            <div class="col-md-8">
                                <p class="form-control-static"><?= $this->session->userdata('username') ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="nombre">Nombre</label>

                            <div class="col-md-8">
                                <input type="text" id="nombre" name="nombre"
                                       class="form-control" value="<?= $this->session->userdata('nombre') ?>">
                            </div>
                        </div>

                    </fieldset>
                    <fieldset>
                        <legend>Cambio de password</legend>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="user-settings-password">Nuevo Password</label>

                            <div class="col-md-8">
                                <input type="password" id="user-settings-password" name="var_usuario_clave"
                                       class="form-control" placeholder="Ingrese un nuevo password">
                            </div>
                        </div>

                    </fieldset>
                    <div class="form-group form-actions">
                        <div class="col-xs-12 text-right">

                            <button type="button" id="" class="btn btn-primary" onclick="miperfil.guardar()">Confirmar
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- END Modal Body -->
        </div>
    </div>
</div>
<!-- END User Settings -->


<div id="cuadre_caja" class="modal fade" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Cuadre de Caja</h3>
            </div>
            <form id="frmCuadreCaja" class='validate form-horizontal' target="_blank" method="post"
                  action="<?php echo $ruta; ?>venta/toPDF_cuadre_caja">
                <div class="modal-body">
                    <fieldset>
                        <div class="control-group">
                            <label for="fecha" class="control-label">Fecha:</label>

                            <div class="controls">
                                <input type="text" name="fecha" id="fecha"
                                       class='input-small form-control'
                                >
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <input type="submit" value="Mostrar" class="btn btn-primary">
                    <a href="#" class="btn btn-danger" data-dismiss="modal">Salir</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true" id="barloadermodal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Cargando...
            </div>
            <div class="modal-body">
                <!-- <h3>Cargando Imagen, por favor espere...</h3>-->

                <div class="progress">
                    <div class="progress-bar  progress-bar-striped progress-bar-info active" role="progressbar"
                         aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"
                         style="width: 100%">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>

        </div>


    </div>
</div>
<input type="hidden" id="base_url" value="<?= base_url() ?>">
</body>
</html>
<script>

    $(document).ready(function () {
        checkSession();
        //checkTimeout = setTimeout(checkSession, 60000);

        $(document).ajaxComplete(function (event, xhr, settings) {
            //  $('#barloadermodal').modal('hide');
        });
        $(document).ajaxStop(function (event, xhr, settings) {
            //  $('#barloadermodal').modal('hide');
        });
        $(document).ajaxError(function (event, xhr, settings) {
            // $('#barloadermodal').modal('hide');
        });


        // $(document).ajaxSend(function (event, request, settings) {
        //   console.log(request);
        // console.log(event);
        // console.log(settings);
        //console.log(settings.url);


        /* $("#barloadermodal").modal({
         show: true,
         backdrop: 'static'
         });*/

        /*  if (settings.url != 'http://localhost/distribucion/inicio/renew_session' && settings.url != 'http://localhost/distribucion/inicio/very_sesion') {

         checkSession();
         }
         });*/

        $("body").mouseup(function () {
            //renewSession();
        });

        $("input").blur(function () {
            //renewSession();
        });

        $("input").focus(function () {
            // renewSession();
        });


        $('#fecha').datepicker({todayHighlight: true});

        $('body').on('keypress', function (e) {

            // console.log(e.keyCode);
            if (e.which == 13) // Enter key = keycode 13
            {
                e.preventDefault();
                e.stopPropagation();
                // $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
                return false;
            }
        });


        handleF();


    });


    // var checkTimeout;
    function checkSession() {
        $.ajax({
            url: '<?php echo base_url();?>inicio/very_sesion',
            type: "POST",
            success: function (result) {
                if (result === "false") {
                    alert('El tiempo de su sessión ha expirado');
                    window.location = "<?php echo base_url() ?>logout";
                }
            },
            complete: function () {
                //setTimeout(setupSessionTimeoutCheck(), 60000);
                console.log('verificando la session desde el home');
            }
        });
    }


    function setupSessionTimeoutCheck() {
        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(checkSession, 900000);
    }

    function renewSession() {
        $.ajax({
            url: "<?php echo base_url() ?>inicio/renew_sesion",
            type: "POST",
            data: {}
        });
    }

    function handleF() {
        $('body').on('keydown', function (e) {


            //console.log(e.keyCode);

            if (e.keyCode == 116) {
                e.preventDefault();
                e.stopPropagation();
                // $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
                return false;
            }


            if (e.keyCode == 113) {

                e.preventDefault();

                if ($(".modal").is(":visible")) {
                    return false;
                }
                $('#barloadermodal').modal('show');

                $.ajax({
                    url: '<?=$ruta?>producto/stock',
                    success: function (data) {

                        if (data.error == undefined) {

                            $('#page-content').html(data);


                        } else {

                            var growlType = 'warning';

                            $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });

                            $(this).prop('disabled', true);

                        }


                        $('#barloadermodal').modal('hide');

                    },
                    error: function (response) {
                        $('#barloadermodal').modal('hide');
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                    }
                })

            }
        });


    }


    var miperfil = {

        guardar: function () {
            if ($("#nombre").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar el nombre</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            $.ajax({
                url: '<?= base_url()?>usuario/guardarsession',
                dataType: 'json',
                data: $("#modal-user-settings-form").serialize(),
                type: 'post',
                success: function (data) {
                    if (data.error === 'undefined') {

                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                        return false;

                    } else {
                        $("#modal-user-settings").modal('hide');
                    }
                }

            })

        }
    }
</script>
