<?php $ruta = base_url(); ?>

<?php
/* Template variables */
$template = array(
    'name' => 'ProUI',
    'version' => '2.2',
    'author' => 'pixelcave',
    'robots' => 'noindex, nofollow',
    'title' => 'ProUI - Responsive Bootstrap Admin Template',
    'description' => 'ProUI is a Responsive Bootstrap Admin Template created by pixelcave and published on Themeforest.',
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
    'header' => '',
    // ''                                               for a full main and alternative sidebar hidden by default (> 991px)
    // 'sidebar-visible-lg'                             for a full main sidebar visible by default (> 991px)
    // 'sidebar-partial'                                for a partial main sidebar which opens on mouse hover, hidden by default (> 991px)
    // 'sidebar-partial sidebar-visible-lg'             for a partial main sidebar which opens on mouse hover, visible by default (> 991px)
    // 'sidebar-alt-visible-lg'                         for a full alternative sidebar visible by default (> 991px)
    // 'sidebar-alt-partial'                            for a partial alternative sidebar which opens on mouse hover, hidden by default (> 991px)
    // 'sidebar-alt-partial sidebar-alt-visible-lg'     for a partial alternative sidebar which opens on mouse hover, visible by default (> 991px)
    // 'sidebar-partial sidebar-alt-partial'            for both sidebars partial which open on mouse hover, hidden by default (> 991px)
    // 'sidebar-no-animations'                          add this as extra for disabling sidebar animations on large screens (> 991px) - Better performance with heavy pages!
    'sidebar' => 'sidebar-partial sidebar-visible-lg sidebar-no-animations',
    // ''                       empty for a static footer
    // 'footer-fixed'           for a fixed footer
    'footer' => '',
    // ''                       empty for default style
    // 'style-alt'              for an alternative main style (affects main page background as well as blocks style)
    'main_style' => '',
    // 'night', 'amethyst', 'modern', 'autumn', 'flatie', 'spring', 'fancy', 'fire' or '' leave empty for the Default Blue theme
    'theme' => '',
    // ''                       for default content in header
    // 'horizontal-menu'        for a horizontal menu in header
    // This option is just used for feature demostration and you can remove it if you like. You can keep or alter header's content in page_head.php
    'header_content' => 'horizontal-menu',
    'active_page' => basename($_SERVER['PHP_SELF'])
);

/* Primary navigation array (the primary navigation will be created automatically based on this array, up to 3 levels deep) */
$primary_nav = array(
    array(
        'name' => 'MENU PRINCIPAL',
        'url' => $ruta . 'principal',
        'icon' => 'fa fa-home'
    ),
    array(
        'name' => 'INVENTARIO',
        'sub' => array()
    ),

    array(
        'name' => 'Productos',
        'url' => $ruta . 'producto',
        'icon' => 'gi gi-barcode'
    ),

    array(
        'name' => 'Grupos',
        'url' => $ruta . 'grupo',
        'icon' => 'fa fa-cubes'
    ),
    array(
        'name' => 'Marcas',
        'url' => $ruta . 'marca',
        'icon' => 'fa fa-certificate'
    ),

    array(
        'name' => 'Lineas',
        'url' => $ruta . 'linea',
        'icon' => 'fa fa-th-large'
    ),
    array(
        'name' => 'Familias',
        'url' => $ruta . 'familia',
        'icon' => 'fa fa-th-large'
    ),
    array(
        'name' => 'Lista de Precios',
        'url' => $ruta . 'producto/listaprecios',
        'icon' => 'fa fa-money'
    ),
    array(
        'name' => 'Ajustar Inventario',
        'url' => $ruta . 'inventario/ajuste',
        'icon' => 'fa fa-exchange'
    ),
    array(
        'name' => 'Productos con existencia minima',
        'url' => $ruta . 'inventario/existencia_minima',
        'icon' => 'fa fa-minus-square-o'
    ),
    array(
        'name' => 'Reporte de Existencias bajas',
        'url' => $ruta . 'inventario/existencia_baja',
        'icon' => 'fa fa-search-minus'
    ),
    array(
        'name' => 'Reporte de Existencias altas',
        'url' => $ruta . 'inventario/existencia_alta',
        'icon' => 'fa fa-search-plus'
    ),
    array(
        'name' => 'VENTAS',
        'url' => 'header',
    ),
    array(
        'name' => 'Generar Venta',
        'url' => $ruta . 'venta',
        'icon' => 'fa fa-share'
    ),
    array(
        'name' => 'Historial de Ventas',
        'url' => $ruta . 'venta/consultar',
        'icon' => 'fa fa-list'
    ),
    array(
        'name' => 'Anular Venta',
        'url' => $ruta . 'venta/cancelar',
        'icon' => 'gi gi-remove_2'
    ),
    array(
        'name' => 'Ventas por Cliente',
        'url' => $ruta . 'venta/ventas_by_cliente',
        'icon' => 'fa fa-bar-chart'
    ),

    array(
        'name' => 'Gastos',
        'icon' => 'fa fa-calculator',
        'sub' => array(
            array(
                'name' => 'Gastos',
                'url' => $ruta . 'gastos',
                'icon' => 'gi gi-parents'
            ),
            array(
                'name' => 'Tipos de gasto',
                'url' => $ruta . 'tiposdegasto',
                'icon' => 'gi gi-parents'
            ),
        )

    ),

    array(
        'name' => 'CLIENTES',
        'url' => 'header',
    ),
    array(
        'name' => 'Clientes',
        'url' => $ruta . 'cliente',
        'icon' => 'gi gi-parents'
    ),
    array(
        'name' => 'Grupos',
        'url' => $ruta . 'clientesgrupos',
        'icon' => 'fa fa-group'
    ),
    array(
        'name' => 'Metodos de Pago',
        'url' => $ruta . 'metodosdepago',
        'icon' => 'fa fa-credit-card'
    ),
    array(
        'name' => 'Cronograma Pago',
        'url' => $ruta . 'venta/cronograma_pago',
        'icon' => 'gi gi-calendar'
    ),
    array(
        'name' => 'INGRESOS',
        'url' => 'header',
    ),
    array(
        'name' => 'Registrar Ingreso',
        'url' => $ruta . 'ingresos',
        'icon' => 'gi gi-edit'
    ),
    array(
        'name' => 'Consultar Ingresos',
        'url' => $ruta . 'ingresos/consultar',
        'icon' => 'gi gi-edit'
    ),
    array(
        'name' => 'Devolucion De Ingreso',
        'url' => $ruta . 'ingresos/devolucion',
        'icon' => 'gi gi-edit'
    ),
   /* array(
        'name' => 'Registro Movimiento',
        'url' => $ruta . 'caja',
        'icon' => 'gi gi-file_import'
    ),*/
    array(
        'name' => 'PROVEEDORES',
        'url' => 'header',
    ),
    array(
        'name' => 'Proveedor',
        'url' => $ruta . 'proveedor',
        'icon' => 'gi gi-shop'
    ),

    array(
        'name' => 'REPORTES',
        'url' => 'header',
    ),
    array(
        'name' => 'OPCIONES',
        'url' => 'header'
    ),
    array(
        'name' => 'Impuestos',
        'url' => $ruta . 'impuesto',
        'icon' => 'fa fa-caret-square-o-up'
    ),
    array(
        'name' => 'Condiciones de Pago',
        'url' => $ruta . 'condicionespago',
        'icon' => 'fa fa-ticket'
    ),
    array(
        'name' => 'Personal',
        'url' => $ruta . 'personal',
        'icon' => 'gi gi-group'
    ),


    array(
        'name' => 'Usuario',
        'url' => $ruta . 'usuario',
        'icon' => 'gi gi-user'
    ),
    array(
        'name' => 'Locales',
        'url' => $ruta . 'local',
        'icon' => 'gi gi-shop_window'
    ),
    array(
        'name' => 'Paises',
        'url' => $ruta . 'pais',
        'icon' => 'gi gi-shop_window'
    ),
    array(
        'name' => 'Estados',
        'url' => $ruta . 'estados',
        'icon' => 'gi gi-shop_window'
    ),
    array(
        'name' => 'Ciudades',
        'url' => $ruta . 'ciudad',
        'icon' => 'gi gi-shop_window'
    ),
    array(
        'name' => 'Usuarios',
        'icon' => 'fa fa-users',
        'sub' => array(
            array(
                'name' => 'Usuarios',
                'url' => $ruta . 'usuarios',
                'icon' => 'fa fa-users'
            ),
            array(
                'name' => 'Grupos',
                'url' => $ruta . 'usuariosgrupos',
                'icon' => 'gi gi-parents'
            ),
        )

    ),

    array(
        'name' => 'Precios',
        'url' => $ruta . 'precio',
        'icon' => 'fa fa-money'
    ),
    array(
        'name' => 'Unidades de Medida',
        'url' => $ruta . 'unidades',
        'icon' => 'fa fa-list-ol'
    )


);
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SISTEMA INVENTARIO</title>
    <meta name="description" content="">

    <meta name="viewport" content="width=device-width">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="<?php echo $ruta ?>recursos/img/favicon.ico">
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
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/bootstrap.min.css">

    <!-- Related styles of various icon packs and plugins -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/plugins.css">

    <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/main.css">

    <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->
    <?php if ($template['theme']) { ?>
        <link id="theme-link" rel="stylesheet"
              href="<?php echo $ruta; ?>recursos/css/themes/<?php echo $template['theme']; ?>.css"><?php } ?>

    <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/themes.css">
    <!-- END Stylesheets -->

    <!-- Modernizr (browser feature detection library) & Respond.js (Enable responsive CSS code on browsers that don't support it, eg IE8) -->
    <script src="<?php echo $ruta; ?>recursos/js/vendor/modernizr-2.7.1-respond-1.4.2.min.js"></script>

    <!-- Remember to include excanvas for IE8 chart support -->
    <!--[if IE 8]>
    <script src="<?php echo $ruta?>recursos/js/helpers/excanvas.min.js"></script><![endif]-->

    <!-- Include Jquery library from Google's CDN but if something goes wrong get Jquery from local file (Remove 'http:' if you have SSL) -->
    <script src="<?php echo $ruta ?>recursos/js/vendor/jquery-1.11.1.min.js"></script>

    <!-- Bootstrap.js, Jquery plugins and Custom JS code -->
    <script src="<?php echo $ruta ?>recursos/js/vendor/bootstrap.min.js"></script>
    <script src="<?php echo $ruta ?>recursos/js/plugins.js"></script>
    <script src="<?php echo $ruta ?>recursos/js/app.js"></script>


</head>

<body>

<div id="page-wrapper"<?php if ($template['page_preloader']) {
    echo ' class="page-loading"';
} ?>>
    <!-- Preloader -->
    <!-- Preloader functionality (initialized in js/app.js) - pageLoading() -->
    <!-- Used only if page preloader is enabled from inc/config (PHP version) or the class 'page-loading' is added in #page-wrapper element (HTML version) -->
    <div class="preloader themed-background">
        <h1 class="push-top-bottom text-light text-center"><strong>Pro</strong>UI</h1>

        <div class="inner">
            <h3 class="text-light visible-lt-ie9 visible-lt-ie10"><strong>Loading..</strong></h3>

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
                    <a href="index.php" class="sidebar-brand">
                        <i class="gi gi-cart_out"></i><strong>Punto</strong> de Ventas
                    </a>
                    <!-- END Brand -->

                    <!-- User Info -->
                    <div class="sidebar-section sidebar-user clearfix">
                        <div class="sidebar-user-avatar">
                            <a href="page_ready_user_profile.php">
                                <img src="<?php echo $ruta ?>recursos/img/placeholders/avatars/avatar2.jpg"
                                     alt="avatar">
                            </a>
                        </div>
                        <div class="sidebar-user-name">John Doe</div>
                        <div class="sidebar-user-links">
                            <a href="page_ready_user_profile.php" data-toggle="tooltip" data-placement="bottom"
                               title="Profile"><i class="gi gi-user"></i></a>
                            <a href="page_ready_inbox.php" data-toggle="tooltip" data-placement="bottom"
                               title="Messages"><i class="gi gi-envelope"></i></a>
                            <!-- Opens the user settings modal that can be found at the bottom of each page (page_footer.php in PHP version) -->
                            <a href="#modal-user-settings" data-toggle="modal" class="enable-tooltip"
                               data-placement="bottom" title="Settings"><i class="gi gi-cogwheel"></i></a>
                            <a href="logout" data-toggle="tooltip" data-placement="bottom" title="Logout"><i
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

                                // Check if the link has a submenu
                                if (isset($link['sub']) && $link['sub']) {
                                    // Since it has a submenu, we need to check if we have to add the class active
                                    // to its parent li element (only if a 2nd or 3rd level link is active)
                                    foreach ($link['sub'] as $sub_link) {
                                        if (in_array($template['active_page'], $sub_link)) {
                                            $li_active = ' class="active"';
                                            break;
                                        }

                                        // 3rd level links
                                        if (isset($sub_link['sub']) && $sub_link['sub']) {
                                            foreach ($sub_link['sub'] as $sub2_link) {
                                                if (in_array($template['active_page'], $sub2_link)) {
                                                    $li_active = ' class="active"';
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    $menu_link = 'sidebar-nav-menu';
                                }

                                // Create the class attribute for our link
                                if ($menu_link || $active) {
                                    $link_class = ' class="' . $menu_link . $active . '"';
                                }
                                ?>
                                <?php if ($url == 'header') { // if it is a header and not a link ?>
                                    <li class="sidebar-header">
                                        <?php if (isset($link['opt']) && $link['opt']) { // If the header has options set ?>
                                            <span
                                                class="sidebar-header-options clearfix"><?php echo $link['opt']; ?></span>
                                        <?php } ?>
                                        <span class="sidebar-header-title"><?php echo $link['name']; ?></span>
                                    </li>
                                <?php } else { // If it is a link ?>
                                    <li<?php echo $li_active; ?>>
                                        <a href="<?php echo $url; ?>"<?php echo $link_class; ?>><?php if (isset($link['sub']) && $link['sub']) { // if the link has a submenu ?>
                                                <i class="fa fa-angle-left sidebar-nav-indicator"></i><?php }
                                            echo $icon . $link['name']; ?></a>
                                        <?php if (isset($link['sub']) && $link['sub']) { // if the link has a submenu ?>
                                            <ul>
                                                <?php foreach ($link['sub'] as $sub_link) {
                                                    $link_class = '';
                                                    $li_active = '';
                                                    $submenu_link = '';

                                                    // Get 2nd level link's vital info
                                                    $url = (isset($sub_link['url']) && $sub_link['url']) ? $sub_link['url'] : '#';
                                                    $active = (isset($sub_link['url']) && ($template['active_page'] == $sub_link['url'])) ? ' active' : '';

                                                    // Check if the link has a submenu
                                                    if (isset($sub_link['sub']) && $sub_link['sub']) {
                                                        // Since it has a submenu, we need to check if we have to add the class active
                                                        // to its parent li element (only if a 3rd level link is active)
                                                        foreach ($sub_link['sub'] as $sub2_link) {
                                                            if (in_array($template['active_page'], $sub2_link)) {
                                                                $li_active = ' class="active"';
                                                                break;
                                                            }
                                                        }

                                                        $submenu_link = 'sidebar-nav-submenu';
                                                    }

                                                    if ($submenu_link || $active) {
                                                        $link_class = ' class="' . $submenu_link . $active . '"';
                                                    }
                                                    ?>
                                                    <li<?php echo $li_active; ?>>
                                                        <a href="<?php echo $url; ?>"<?php echo $link_class; ?>><?php if (isset($sub_link['sub']) && $sub_link['sub']) { ?>
                                                                <i class="fa fa-angle-left sidebar-nav-indicator"></i><?php }
                                                            echo $sub_link['name']; ?></a>
                                                        <?php if (isset($sub_link['sub']) && $sub_link['sub']) { ?>
                                                            <ul>
                                                                <?php foreach ($sub_link['sub'] as $sub2_link) {
                                                                    // Get 3rd level link's vital info
                                                                    $url = (isset($sub2_link['url']) && $sub2_link['url']) ? $sub2_link['url'] : '#';
                                                                    $active = (isset($sub2_link['url']) && ($template['active_page'] == $sub2_link['url'])) ? ' class="active"' : '';
                                                                    ?>
                                                                    <li>
                                                                        <a href="<?php echo $url; ?>"<?php echo $active ?>><?php echo $sub2_link['name']; ?></a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        <?php } ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
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
                            <li>
                                <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar-alt');">
                                    <i class="gi gi-share_alt"></i>
                                    <span class="label label-primary label-indicator animation-floating">4</span>
                                </a>
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
                                <img src="<?php echo $ruta ?>recursos/img/placeholders/avatars/avatar2.jpg"
                                     alt="avatar"> <i
                                    class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-custom dropdown-menu-right">
                                <li class="dropdown-header text-center">Account</li>
                                <li>
                                    <a href="page_ready_timeline.php">
                                        <i class="fa fa-clock-o fa-fw pull-right"></i>
                                        <span class="badge pull-right">10</span>
                                        Updates
                                    </a>
                                    <a href="page_ready_inbox.php">
                                        <i class="fa fa-envelope-o fa-fw pull-right"></i>
                                        <span class="badge pull-right">5</span>
                                        Messages
                                    </a>
                                    <a href="page_ready_pricing_tables.php"><i
                                            class="fa fa-magnet fa-fw pull-right"></i>
                                        <span class="badge pull-right">3</span>
                                        Subscriptions
                                    </a>
                                    <a href="page_ready_faq.php"><i class="fa fa-question fa-fw pull-right"></i>
                                        <span class="badge pull-right">11</span>
                                        FAQ
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="page_ready_user_profile.php">
                                        <i class="fa fa-user fa-fw pull-right"></i>
                                        Profile
                                    </a>
                                    <!-- Opens the user settings modal that can be found at the bottom of each page (page_footer.php in PHP version) -->
                                    <a href="#modal-user-settings" data-toggle="modal">
                                        <i class="fa fa-cog fa-fw pull-right"></i>
                                        Settings
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="page_ready_lock_screen.php"><i class="fa fa-lock fa-fw pull-right"></i>
                                        Lock Account</a>
                                    <a href="<?= $ruta ?>logout"><i class="fa fa-ban fa-fw pull-right"></i> Logout</a>
                                </li>
                                <li class="dropdown-header text-center">Activity</li>
                                <li>
                                    <div class="alert alert-success alert-alt">
                                        <small>5 min ago</small>
                                        <br>
                                        <i class="fa fa-thumbs-up fa-fw"></i> You had a new sale ($10)
                                    </div>
                                    <div class="alert alert-info alert-alt">
                                        <small>10 min ago</small>
                                        <br>
                                        <i class="fa fa-arrow-up fa-fw"></i> Upgraded to Pro plan
                                    </div>
                                    <div class="alert alert-warning alert-alt">
                                        <small>3 hours ago</small>
                                        <br>
                                        <i class="fa fa-exclamation fa-fw"></i> Running low on space<br><strong>18GB in
                                            use</strong> 2GB left
                                    </div>
                                    <div class="alert alert-danger alert-alt">
                                        <small>Yesterday</small>
                                        <br>
                                        <i class="fa fa-bug fa-fw"></i> <a href="javascript:void(0)" class="alert-link">New
                                            bug submitted</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- END User Dropdown -->
                    </ul>
                    <!-- END Alternative Sidebar Toggle Button -->

                    <!-- Horizontal Menu + Search -->
                    <div id="horizontal-menu-collapse" class="collapse navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="<?= $ruta ?>producto/productos">Stock Producto</a>
                            </li>
                            <li>
                                <a href="<?= $ruta ?>venta/pagospendientes">Pagos Pendientes</a>
                            </li>

                            <li>
                                <a href="<?= $ruta ?>venta/estadocuenta">Estado de cuentas</a>
                            </li>
                            <li>
                                <a href="#cuadre_caja" data-toggle="modal">Cuadre caja</a>
                            </li>

                        </ul>
                        <form action="page_ready_search_results.php" class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search..">
                            </div>
                        </form>
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

                        <!-- User Dropdown -->
                        <li class="dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?php echo $ruta ?>recursos/img/placeholders/avatars/avatar2.jpg"
                                     alt="avatar"> <i
                                    class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-custom dropdown-menu-right">
                                <li class="dropdown-header text-center">Account</li>
                                <li>
                                    <a href="page_ready_timeline.php">
                                        <i class="fa fa-clock-o fa-fw pull-right"></i>
                                        <span class="badge pull-right">10</span>
                                        Updates
                                    </a>
                                    <a href="page_ready_inbox.php">
                                        <i class="fa fa-envelope-o fa-fw pull-right"></i>
                                        <span class="badge pull-right">5</span>
                                        Messages
                                    </a>
                                    <a href="page_ready_pricing_tables.php"><i
                                            class="fa fa-magnet fa-fw pull-right"></i>
                                        <span class="badge pull-right">3</span>
                                        Subscriptions
                                    </a>
                                    <a href="page_ready_faq.php"><i class="fa fa-question fa-fw pull-right"></i>
                                        <span class="badge pull-right">11</span>
                                        FAQ
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="page_ready_user_profile.php">
                                        <i class="fa fa-user fa-fw pull-right"></i>
                                        Profile
                                    </a>
                                    <!-- Opens the user settings modal that can be found at the bottom of each page (page_footer.php in PHP version) -->
                                    <a href="#modal-user-settings" data-toggle="modal">
                                        <i class="fa fa-cog fa-fw pull-right"></i>
                                        Settings
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="page_ready_lock_screen.php"><i class="fa fa-lock fa-fw pull-right"></i>
                                        Lock Account</a>
                                    <a href="login.php"><i class="fa fa-ban fa-fw pull-right"></i> Logout</a>
                                </li>
                                <li class="dropdown-header text-center">Activity</li>
                                <li>
                                    <div class="alert alert-success alert-alt">
                                        <small>5 min ago</small>
                                        <br>
                                        <i class="fa fa-thumbs-up fa-fw"></i> You had a new sale ($10)
                                    </div>
                                    <div class="alert alert-info alert-alt">
                                        <small>10 min ago</small>
                                        <br>
                                        <i class="fa fa-arrow-up fa-fw"></i> Upgraded to Pro plan
                                    </div>
                                    <div class="alert alert-warning alert-alt">
                                        <small>3 hours ago</small>
                                        <br>
                                        <i class="fa fa-exclamation fa-fw"></i> Running low on space<br><strong>18GB in
                                            use</strong> 2GB left
                                    </div>
                                    <div class="alert alert-danger alert-alt">
                                        <small>Yesterday</small>
                                        <br>
                                        <i class="fa fa-bug fa-fw"></i> <a href="javascript:void(0)" class="alert-link">New
                                            bug submitted</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- END User Dropdown -->
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
                <div class="pull-right">
                    Crafted with <i class="fa fa-heart text-danger"></i> by <a href="http://goo.gl/vNS3I"
                                                                               target="_blank">pixelcave</a>
                </div>
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
                <h2 class="modal-title"><i class="fa fa-pencil"></i> Settings</h2>
            </div>
            <!-- END Modal Header -->

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="index.php" method="post" enctype="multipart/form-data"
                      class="form-horizontal form-bordered" onsubmit="return false;">
                    <fieldset>
                        <legend>Vital Info</legend>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Username</label>

                            <div class="col-md-8">
                                <p class="form-control-static">Admin</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="user-settings-email">Email</label>

                            <div class="col-md-8">
                                <input type="email" id="user-settings-email" name="user-settings-email"
                                       class="form-control" value="admin@example.com">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="user-settings-notifications">Email
                                Notifications</label>

                            <div class="col-md-8">
                                <label class="switch switch-primary">
                                    <input type="checkbox" id="user-settings-notifications"
                                           name="user-settings-notifications" value="1" checked>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Password Update</legend>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="user-settings-password">New Password</label>

                            <div class="col-md-8">
                                <input type="password" id="user-settings-password" name="user-settings-password"
                                       class="form-control" placeholder="Please choose a complex one..">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="user-settings-repassword">Confirm New
                                Password</label>

                            <div class="col-md-8">
                                <input type="password" id="user-settings-repassword" name="user-settings-repassword"
                                       class="form-control" placeholder="..and confirm it!">
                            </div>
                        </div>
                    </fieldset>
                    <div class="form-group form-actions">
                        <div class="col-xs-12 text-right">
                            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
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
                  action="<?php echo $ruta; ?>exportar/toPDF_cuadre_caja">
                <div class="modal-body">
                    <fieldset>
                        <div class="control-group">
                            <label for="fecha" class="control-label">Fecha:</label>

                            <div class="controls">
                                <input type="text" name="fecha" id="fecha" class='input-small input-datepicker form-control'
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


</body>
</html>