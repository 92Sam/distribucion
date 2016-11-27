<ul class="breadcrumb breadcrumb-top">
    <li>Opciones</li>

</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable" id="error"
             style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Error</h4>
            <span id="errorspan"><?php //echo isset($error) ? $error : '' ?></div>
    </div>
</div>
<div class="block">

    <div class="block-title">
        <h2><strong>Opciones</strong></h2>
    </div>



    <?= form_open_multipart(base_url() . 'opciones/save', array('id' => 'formguardar')) ?>



    <input type="hidden" name="id" id="id"
           class='form-control' autofocus="autofocus" maxlength="15"
           value="<?php if (isset($producto['producto_id'])) echo $producto['producto_id'] ?>"
        >

    <div id="mensaje"></div>


    <ul class="nav nav-tabs" role="tablist">
        <li class='active' role="presentation">
            <a href="#lista" data-toggle="tab">Generales</a>
        </li>

        <li role="presentation">
            <a href="#precios" data-toggle="tab">Configuraciones</a>
        </li>

    </ul>


    <div class="tab-content row" style="height: auto">

        <div class="tab-pane active" role="tabpanel" id="lista" role="tabpanel">


            <div class="form-group">
                <div class="col-md-3">
                    <label for="linea" class="control-label">Pais:</label>
                </div>
                <div class="col-md-8">
                    <select name="producto_marca" id="producto_marca" class='cho form-control'>
                        <option value="">Per&uacute;</option>

                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <label for="linea" class="control-label">Idioma:</label>
                </div>
                <div class="col-md-8">
                    <select name="producto_marca" id="producto_marca" class='cho form-control'>
                        <option value="">Espa&nacute;ol</option>

                    </select>
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label">Nombre de la empresa:</label>
                </div>

                <div class="col-md-8">
                    <input type="text" name="EMPRESA_NOMBRE" required="true" id="EMPRESA_NOMBRE"
                           class='form-control'
                           maxlength="100"
                           value="<?php echo $this->session->userdata('EMPRESA_NOMBRE'); ?>">
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label">Direcci&oacute;n:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="EMPRESA_DIRECCION" id="EMPRESA_DIRECCION"
                           class='form-control'
                           maxlength="500"
                           value="<?php echo $this->session->userdata('EMPRESA_DIRECCION'); ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label">Tel&eacute;fono:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="EMPRESA_TELEFONO" id="EMPRESA_TELEFONO"
                           class='form-control'
                           maxlength="500"
                           value="<?php echo $this->session->userdata('EMPRESA_TELEFONO'); ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label">Moneda:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="MONEDA" id="MONEDA"
                           class='form-control'
                           maxlength="500"
                           value="<?php echo $this->session->userdata('MONEDA'); ?>">
                </div>
            </div>

        </div>

        <div class="tab-pane" role="tabpanel" id="precios" role="tabpanel">
            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label">Monto de boleta:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="MONTO_BOLETAS_VENTA" id="MONTO_BOLETAS_VENTA"
                           class="form-control"
                           value="<?php echo $this->session->userdata('MONTO_BOLETAS_VENTA'); ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-3" >
                    <label class="control-label">Pedidos:</label>
                </div>
                <div class="col-md-8" >
                    <input type="number" size="10" name="REFRESCAR_PEDIDOS" id="REFRESCAR_PEDIDOS"
                           class="form-control"
                           value="<?php echo $this->session->userdata('REFRESCAR_PEDIDOS'); ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-3" >
                    <label class="control-label">Vender productos sin Stock:</label>
                </div>
                <div class="col-md-8">
                    <input type="checkbox" name="VENTA_SIN_STOCK" id="VENTA_SIN_STOCK"
                           value="1" <?php echo ($this->session->userdata('VENTA_SIN_STOCK') == TRUE) ? 'checked="checked"' : NULL; ?>>
                </div>
            </div>


        </div>



    </div>


    <div class="form-group">
        <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>

    </div>


    <?= form_close() ?>

</div>
<script>
    var grupo = {
        ajaxgrupo : function(){
            return  $.ajax({
                url:'<?= base_url()?>opciones'

            })
        },
        guardar : function () {

            App.formSubmitAjax($("#formguardar").attr('action'), this.ajaxgrupo, null, 'formguardar');
        }
    }
</script>
