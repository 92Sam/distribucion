<?php $ruta = base_url(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DISTRIBUCION 0.1</title>
    <meta name="description" content="">

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1">
    <meta name="description" content="ProUI is a Responsive Bootstrap Admin.">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="noindex, nofollow">

    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="<?php echo $ruta; ?>recursos/img/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon57.png" sizes="57x57">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon72.png" sizes="72x72">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon76.png" sizes="76x76">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon114.png" sizes="114x114">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon120.png" sizes="120x120">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon144.png" sizes="144x144">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon152.png" sizes="152x152">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- Bootstrap is included in its original form, unaltered -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/bootstrap.min.css">

    <!-- Related styles of various icon packs and plugins -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/plugins.css">

    <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/main.css">

    <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->

    <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/themes.css">
    <!-- END Stylesheets -->

    <!-- Modernizr (browser feature detection library) & Respond.js (Enable responsive CSS code on browsers that don't support it, eg IE8) -->
    <script src="<?php echo $ruta; ?>recursos/js/vendor/modernizr-2.7.1-respond-1.4.2.min.js"></script>

    <script src="<?php echo $ruta; ?>recursos/js/vendor/jquery-1.11.1.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#btnlogin").click(function (event) {
                login();
            });


            $('body').on('keydown', function (e) {
                   // console.log(e.keyCode );
                    if (e.keyCode ==13) {

                        login();
                    }
                }
            )
        });

        function login() {
            <?php $mensaje = "<a></a>"; ?>
            event.preventDefault();
            $.ajax({
                type: "POST",
                data: $('#frmLogin').serialize(),
                url: "<?php echo $ruta; ?>inicio/validar_login",
                success: function (msj) {
                    if (msj == 'ok') {
                        window.location.href = "<?php echo $ruta;?>principal/";
                    } else {
                        document.getElementById("error").innerHTML = "<a>Usuario o clave incorrecta, por favor vuelva a intentar</a>";
                    }
                }
            });
        }
    </script>

</head>
<body class='login_body'>


<div id="login-container" class="animation-fadeIn">

    <div class="login-title text-center">
        <h1></i> <strong>Distribucion</strong><br>
            <small><strong>Inicio de Sesion</strong>
        </h1>
        <div id="error"></div>
    </div>

    <div class="block push-bit">
        <!-- Login Form -->
        <form method="post" id="frmLogin"
              class="form-horizontal form-bordered form-control-borderless">
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="gi gi-envelope"></i></span>
                        <input type="text" id="user" name="user" value="" class="form-control input-lg"
                               placeholder="Usuario">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="gi gi-asterisk"></i></span>
                        <input type="password" id="pw" name="pw" value="" class="form-control input-lg"
                               placeholder="Contraseña">
                    </div>
                </div>
            </div>

            <!-- <div class="form-group">
                <div class="col-xs-12">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="gi gi-shop"></i></span>
                        <select name="cboTienda" id="cboTienda" class="form-control input-lg">
                            <?php if (count($lstLocal) > 0): ?>
                                <?php foreach ($lstLocal as $l): ?>
                                    <option
                                        value="<?php echo $l['int_local_id']; ?>"><?php echo $l['local_nombre']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                    </div>
                </div>
            </div>-->
            <div class="form-group form-actions">
                <div class="col-xs-4">

                </div>
                <div class="col-md-12 ">
                    <button id="btnlogin" style="    border-color: #6ad2eb;  border-radius: 0px; height: 41px;   width: 100%; " type="button" class="btn btn-primary">Iniciar sesion</button>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 text-center">
                </div>
            </div>
        </form>
        <!-- END Login Form -->
    </div>
</div>
</body>
</html>