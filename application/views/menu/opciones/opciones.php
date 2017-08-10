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
                <div class="col-md-4">
                    <label for="linea" class="control-label">Pais:</label>
                </div>
                <div class="col-md-8">
                    <select name="producto_marca" id="producto_marca" class='cho form-control'>
                        <option value="">Per&uacute;</option>

                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label for="linea" class="control-label">Idioma:</label>
                </div>
                <div class="col-md-8">
                    <select name="producto_marca" id="producto_marca" class='cho form-control'>
                        <option value="">Espa&nacute;ol</option>

                    </select>
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label class="control-label">Maximo de Items Por Pedidos:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="REFRESCAR_PEDIDOS" id="REFRESCAR_PEDIDOS"
                           class="form-control"
                           value="<?php echo $this->session->userdata('REFRESCAR_PEDIDOS'); ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Vender productos sin Stock:</label>
                </div>
                <div class="col-md-8">
                    <select name="VENTA_SIN_STOCK" id="VENTA_SIN_STOCK" class="form-control">
                        <option value="1" <?= valueOption('VENTA_SIN_STOCK', 0) == 1 ? 'selected' : '' ?>>SI</option>
                        <option value="0" <?= valueOption('VENTA_SIN_STOCK', 0) == 0 ? 'selected' : '' ?>>NO</option>
                    </select>
                </div>
            </div>

            <br>
            <h4>NOTA DE CREDITO CONFIGURACION</h4>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Nota de Credito Serie:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="NC_SERIE" id="NC_SERIE"
                           class="form-control"
                           value="<?= valueOption('NC_SERIE', 1) ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Nota de Credito Correlativo:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="NC_NEXT" id="NC_NEXT"
                           class="form-control"
                           value="<?= valueOption('NC_NEXT', 1) ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Maximo de Items Por Nota de Credito:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="NC_MAX" id="NC_MAX"
                           class="form-control"
                           value="<?= valueOption('NC_MAX', 12) ?>">
                </div>
            </div>

            <br>
            <h4>FACTURA CONFIGURACION</h4>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Factura Serie:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="FACTURA_SERIE" id="FACTURA_SERIE"
                           class="form-control"
                           value="<?= valueOption('FACTURA_SERIE', 1) ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Factura Correlativo:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="FACTURA_NEXT" id="FACTURA_NEXT"
                           class="form-control"
                           value="<?= valueOption('FACTURA_NEXT') ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Maximo de Items Por Factura:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="FACTURA_MAX" id="FACTURA_MAX"
                           class="form-control"
                           value="<?= valueOption('FACTURA_MAX', 15) ?>">
                </div>
            </div>

            <br>
            <h4>BOLETA CONFIGURACION</h4>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Boleta Serie:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="BOLETA_SERIE" id="BOLETA_SERIE"
                           class="form-control"
                           value="<?= valueOption('BOLETA_SERIE', 1) ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Boleta Correlativo:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="BOLETA_NEXT" id="BOLETA_NEXT"
                           class="form-control"
                           value="<?= valueOption('BOLETA_NEXT') ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Maximo de Items Por Boleta:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="BOLETA_MAX" id="BOLETA_MAX"
                           class="form-control"
                           value="<?= valueOption('BOLETA_MAX', 10) ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label">Monto Maximo Por Boleta:</label>
                </div>
                <div class="col-md-8">
                    <input type="number" size="10" name="MONTO_BOLETAS_VENTA" id="MONTO_BOLETAS_VENTA"
                           class="form-control"
                           value="<?php echo $this->session->userdata('MONTO_BOLETAS_VENTA'); ?>">
                </div>
            </div>


        </div>


    </div>


    <div class="form-group">
        <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>

    </div>


    <?= form_close() ?>

</div>
<script>
    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>opciones'

            })
        },
        guardar: function () {

            App.formSubmitAjax($("#formguardar").attr('action'), this.ajaxgrupo, null, 'formguardar');
        }
    }
</script>
