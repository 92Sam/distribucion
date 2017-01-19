<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Movimiento de Inventario</a></li>
</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable"
             style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i>
            </button>
            <h4><i class="icon fa fa-ban"></i> Error</h4>
            <?php echo isset($error) ? $error : '' ?></div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i>
            </button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <?php echo isset($success) ? $success : '' ?>
        </div>
    </div>
</div>

<div class="block">


    <br>
    <div class="row">
        <div class="form-group">
            <div class="col-md-1" style="display: none">
                <label>Ubicaci&oacute;n Inventario</label>
            </div>
            <div class="col-md-3" style="display: none">
                <select class="form-control" id="locales" onchange="getproductosbylocal()">
                    <option value="TODOS">Todos</option>
                    <?php foreach($locales as $local){?>
                        <option value="<?= $local['int_local_id']?>"><?= $local['local_nombre']?></option>
                    <?php }?>
                </select>
            </div>


            <div class="col-md-1">
                <label class="control-label panel-admin-text">Mes</label>
            </div>
            <div class="col-md-2">
                <select class="form-control" id="mes">
                    <?php for($i = 1; $i < 13; $i++):?>
                        <option value="<?=$i?>" <?= date('n')==$i ? 'selected="selected"' : ''?>><?= getMes($i) ?></option>
                    <?php endfor;?>
                </select>
            </div>

            <div class="col-md-1">
                <label class="control-label panel-admin-text">A&ntilde;o</label>
            </div>
            <div class="col-md-1">
                <input type="number" id="year" name="year" value="<?=date('Y')?>" class="form-control">
            </div>

            <div class="col-md-7">
                <h4><strong>Recuerde que el kardex se muestra en función al año y mes seleccionado</strong> </h4>
            </div>
        </div>
    </div>
    <br>

    <div class="table-responsive">

        <table class='table table-striped dataTable table-bordered' id="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>UM</th>
                <th>Cantidad</th>
                <th>Fracci&oacute;n</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>


</div>


<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>




<script type="text/javascript">



    function KARDEXINTERNO(id) {
        $("#ver").html('');
        var local=$("#locales").val();
        var year=$("#year").val();
        var mes=$("#mes").val();
        $("#ver").load('<?= $ruta ?>inventario/kardex_interno/' + id + '/' + local + '/' + mes + '/' + year);
        $('#ver').modal('show');
    }

    function KARDEXEXTERNO(ELID) {
        $("#ver").html('');
        var documento_fiscal = true;
        var local=$("#locales").val();
        var mes=$("#mes").val();
        var year=$("#year").val();
        $("#ver").load('<?= $ruta ?>inventario/kardex/' + ELID + '/' + local + '/' + mes + '/' + year);
        $('#ver').modal('show');
    }



    function getproductosbylocal(){

        TablesDatatablesKardex.init('<?php echo base_url()?>inventario/getbyJson',0,false,false);


    }

</script>

<!-- Load and execute javascript code used only in this page -->

<script>$(function () {
        getproductosbylocal();

    });</script>
