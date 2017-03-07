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
                event.preventDefault();
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
            
            $.ajax({
                type: "POST",
                data: $('#frmLogin').serialize(),
                url: "<?php echo $ruta; ?>inicio/validar_login",
                success: function (msj) {
                    if (msj == 'ok') {
                        window.location.href = "<?php echo $ruta;?>principal/";
                    } else {
                     //   document.getElementById("error").innerHTML = "<a>Usuario o clave incorrecta, por favor vuelva a intentar</a>";
                        $("#light").html('Usuario o clave incorrecta, por favor vuelva a intentar')        
                        $("#light").delay("slow").fadeIn().delay(2000).fadeOut();
                    }
                }
            });
        }
    </script>
<style type="text/css">
    .btn_login {
  background: #7ed962;
  background-image: -webkit-linear-gradient(top, #7ed962, #4eb82b);
  background-image: -moz-linear-gradient(top, #7ed962, #4eb82b);
  background-image: -ms-linear-gradient(top, #7ed962, #4eb82b);
  background-image: -o-linear-gradient(top, #7ed962, #4eb82b);
  background-image: linear-gradient(to bottom, #7ed962, #4eb82b);
  -webkit-border-radius: 6;
  -moz-border-radius: 6;
  border-radius: 6px;
  text-shadow: 1px 1px 3px #666666;
  font-family: Arial;
  color: #ffffff;
  font-size: 16px;
  padding: 3% 24% 3% 24%;
  border: solid #44ad34 2px;
  text-decoration: none;
}

.btn_login:hover {
  background: #63b557;
  background-image: -webkit-linear-gradient(top, #63b557, #1bb52a);
  background-image: -moz-linear-gradient(top, #63b557, #1bb52a);
  background-image: -ms-linear-gradient(top, #63b557, #1bb52a);
  background-image: -o-linear-gradient(top, #63b557, #1bb52a);
  background-image: linear-gradient(to bottom, #63b557, #1bb52a);
  text-decoration: none;
}
.black_overlay{
    display: none;
    position: absolute;
    top: 0%;
    left: 0%;
    width: 100%;
    height: 100%;
    background-color: black;
    z-index:1001;
    -moz-opacity: 0.8;
    opacity:.80;
    filter: alpha(opacity=80);
}

.white_content {
    display: none;
    position: absolute;
    top: 45%;
    left: 22%;
    width: 270px;
    height: 70px;
    padding: 16px;
    border: 1px solid red;
    -webkit-border-radius: 10px 10px 10px 10px;
     border-radius: 10px 10px 10px 10px;
    background-color: rgba(215, 44, 44, 0.8);
background: rgba(215, 44, 44, 0.8);
color: white;

    z-index:1002;
    overflow: auto;
}
</style>
</head>
<body class='login_body'>

<img src="<?php echo $ruta; ?>recursos/img/placeholders/backgrounds/fondo_login.png" alt="Login Full Background"
     class="full-bg animation-pulseSlow">

<div id="login-container" class="animation-fadeIn" style="padding:4%;  position:relative; top:60px">

    <div class="login-title text-center" style="-webkit-border-radius: 18px 18px 18px 18px; border-radius: 18px 18px 18px 18px;
-webkit-box-shadow: 0 0 4px 4px #000000; box-shadow: 0 0 4px 4px #000000; background-color: rgba(102, 102, 102, 0.6); color: rgba(102, 102, 102, 0.6);">
            
        <div>
            
            <div class="sidebar-user-avatar" style="margin-left:0px; position: relative; left:10%; margin-top:-2%">
                    <img src="<?php echo base_url();?>recursos/img/octopus2.png" alt="avatar">
            </div>
    
                <h1>
                    <strong>Distribuci&oacute;n</strong><br>
                    <small><strong>Iniciar Sesi&oacute;n</strong></small> <!--or <strong>Register</strong></small>-->
                </h1>
            </div>


<div id="light" class="white_content"></div>

        <div id="error"></div>

<?php // echo md5('12345678'); ?>
        <!-- Login Form -->

        <hr style="border: 0; border-top: 1px solid #848484; border-bottom: 1px solid #333; height:0; margin-left: -2%;  width: 104%;"><br>
        <form method="post" id="frmLogin" class="form-horizontal">
            <div class="form-group" style=" padding-left: 16%;">
                <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon" style="color: white ; border-radius: 6px 0px 0px 6px ; border: 1px solid black; background-color: rgba(102, 102, 102, 0.8); color: white; font-size: 20px"><i class="gi gi-user"></i></span>
                        <input style="color: white; -webkit-border-radius: 0px 6px 6px 0px;border-radius: 0px 6px 6px 0px;background-color: rgba(102, 102, 102, 0.8); border: 1px solid black" type="text" id="user" name="user" value="" class="input-lg form-control " placeholder="Usuario">
                    </div>
                </div>
            </div>

            <div class="form-group" style=" padding-left: 16%;" >
                <div class="col-xs-10">
                    <div class="input-group">

                        <span class="input-group-addon" style="color: white ; border-radius: 6px 0px 0px 6px ; border: 1px solid black; background-color: rgba(102, 102, 102, 0.8); color: white; font-size: 20px"><i class="gi gi-asterisk"></i></span>
                        <input style="-webkit-border-radius: 0px 6px 6px 0px;border-radius: 0px 6px 6px 0px;background-color: rgba(102, 102, 102, 0.8);color: white; border: 1px solid black" type="password" id="pw" name="pw" value="" class="form-control  input-lg" placeholder="Contraseña">
                    </div>
                </div>
            </div>

    
            <div class="">
               
                <div class="">
                    <button id="btnlogin" type="button" class="btn_login"> Iniciar
                    </button>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 text-center">
                </div>
            </div>
            <br><br><br>
        </form>
        <!-- END Login Form -->


        <!-- END Register Form -->
    </div>

</div>
</body>
</html>
