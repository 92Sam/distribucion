<form name="formagregar" action="<?= base_url() ?>usuariosgrupos/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($usuariosgrupos['id_grupos_usuarios'])) echo $usuariosgrupos['id_grupos_usuarios']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Grupo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Nombre</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="nombre_grupos_usuarios" id="nombre_grupos_usuarios" required="true"
                                   class="form-control"
                                   value="<?php if (isset($usuariosgrupos['nombre_grupos_usuarios'])) echo $usuariosgrupos['nombre_grupos_usuarios']; ?>">
                        </div>

                    </div>


                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3>Lista Opciones a Ingresar</h3>
                            </div>
                            <div class="box-body">


                                <table class="dataTable table-striped" style="width: 100%">
                                    <th>Opcion</th>
                                    <th>Permiso</th>

                                    <?php
                                    foreach ($perm_list as $perm):
                                        //var_dump($perm);
                                        echo "<tr>";


                                        if (empty($perm->nOpcionClase)):

                                            ?> <td>
                                                            <b style="color: #0d70b7"><u><?php echo $perm->cOpcionNombre; ?></u></b>
                                        </td>

                                                       <td>
                                                            <input name="perms[]" type="checkbox"

                                                                   value="<?php echo $perm->nOpcion; ?>" <?php echo ($perm->var_opcion_usuario_estado == TRUE) ? 'checked="checked"' : NULL; ?>>

                                                            </input>
                                                        </td>

                                                        <?php foreach ($perm_list as $permhijo): if ($permhijo->nOpcionClase == $perm->nOpcion): ?>
                                            <tr>
                                                <td style="padding-left: 25px">
                                                    <?php echo $permhijo->cOpcionNombre; ?>

                                                </td>
                                                <td>
                                                    <input name="perms[]" type="checkbox"

                                                           value="<?php echo $permhijo->nOpcion; ?>" <?php echo ($permhijo->var_opcion_usuario_estado == TRUE) ? 'checked="checked"' : NULL; ?>>

                                                    </input>
                                                </td>

                                            </tr>
                                            <?php foreach ($perm_list as $permnieto): if ($permnieto->nOpcionClase == $permhijo->nOpcion): ?>
                                                <tr>

                                                    <td style="padding-left: 50px">
                                                        <?php echo $permnieto->cOpcionNombre; ?>
                                                    </td>
                                                    <td>
                                                        <input name="perms[]" type="checkbox"

                                                               value="<?php echo $permnieto->nOpcion; ?>" <?php echo ($permnieto->var_opcion_usuario_estado == TRUE) ? 'checked="checked"' : NULL; ?>>

                                                        </input>
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                        <?php endif; endforeach;?>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>
</form>