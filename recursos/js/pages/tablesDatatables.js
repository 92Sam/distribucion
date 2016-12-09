/*
 *  Document   : tablesDatatables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables Datatables page
 */

//
// Pipelining function for DataTables. To be used to the `ajax` option of DataTables
//


// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register( 'clearPipeline()', function () {
    return this.iterator( 'table', function ( settings ) {
        settings.clearCache = true;
    } );
});


var TablesDatatables = function () {

    return {
        init: function (order, datatable) {
            var oder_col = order || 0;
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */

            if (datatable) {
                // console.log(datatable);
                datatable_elemnt = $('#' + datatable);
                //console.log(datatable_elemnt);
            }
            else {
                datatable_elemnt = $('.dataTable');
            }
            var table = datatable_elemnt.dataTable({
                retrieve: true,

               columnDefs: [
                    { width: '16%', targets: 0 }
                ],
                "iDisplayLength": 20,
                fixedColumns: true,
                "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "order": [[oder_col, "desc"]],
                "scrollY": "300px",

                "scrollCollapse": true,
                "dom": '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
                "language": {
                    "emptyTable": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 resultados",
                    "infoFiltered": "(filtrado de _MAX_ total resultados)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ resultados",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    // "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar ordenar columnas ascendente",
                        "sortDescending": ": activar ordenar columnas descendente"
                    }
                },
                "fnInitComplete": function () {

                    setTimeout(function () {
                        $('div.dataTables_filter input').focus();
                    }, 5);


                },



            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }
    };
}();


var TablesDatatablesBonos = function () {

    return {
        init: function (order, datatable) {
            var oder_col = order || 0;
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */

            if (datatable) {
                // console.log(datatable);
                datatable_elemnt = $('#' + datatable);
                //console.log(datatable_elemnt);
            }
            else {
                datatable_elemnt = $('.dataTable');
            }
            var table = datatable_elemnt.dataTable({
                retrieve: true,
                 columnDefs: [
                 { width: '16%', targets: 0 }
                 ],
                "iDisplayLength": 20,
                fixedColumns: true,
                "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "order": [[oder_col, "desc"]],
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "dom": '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
                "language": {
                    "emptyTable": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 resultados",
                    "infoFiltered": "(filtrado de _MAX_ total resultados)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ resultados",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    // "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar ordenar columnas ascendente",
                        "sortDescending": ": activar ordenar columnas descendente"
                    }
                },
                "fnInitComplete": function () {

                    setTimeout(function () {
                        $('div.dataTables_filter input').focus();
                    }, 5);


                },



            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }
    };
}();


var TablesDatatablesJson = function () {

    return {
        init: function (url,order,datatable) {
            var oder_col = order || 0;
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */

            if (datatable) {
                // console.log(datatable);
                datatable_elemnt = $('#' + datatable);
                //console.log(datatable_elemnt);
            }
            else {
                datatable_elemnt = $('.dataTable');
            }
            var table = datatable_elemnt.dataTable({
                "processing": true,
                "serverSide": true,
                "ajax":url,
                rowId: 'staffId',

                "columnDefs": [
                    {
                        // The `data` parameter refers to the data for the cell (defined by the
                        // `data` option, which defaults to the column being worked with, in
                        // this case `data: 0`.
                        "render": function ( data, type, row ) {
                            return '<div class="btn-group"> <a class="btn btn-default" data-toggle="tooltip"'+
                            'title="Editar" data-original-title="fa fa-comment-o"'+
                            'href="#" onclick="editar('+row[0]+ ');"> <i class="fa fa-edit"></i>'+
                            '</a><a class="btn btn-default" data-toggle="tooltip"  title="Eliminar" data-original-title="fa fa-comment-o"'+
                           'onclick="borrar(' +row[0]+ ',\'' +row[1]+ '\');">   <i class="fa fa-trash-o"></i> </a> </div>';
                        },
                        "targets": 9
                    }
                ],
                //"iDisplayLength": 20,
               // "pageLength": 20,
                //"aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "order": [[oder_col, "desc"]],
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "dom": '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
                "language": {
                    "emptyTable": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 resultados",
                    "infoFiltered": "(filtrado de _MAX_ total resultados)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ resultados",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    // "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar ordenar columnas ascendente",
                        "sortDescending": ": activar ordenar columnas descendente"
                    }
                },
                "fnInitComplete": function () {

                    setTimeout(function () {
                        $('div.dataTables_filter input').focus();
                    }, 5);

                }

            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }
    };
}();



var TablesDatatablesJsonProdcutos = function () {

    return {
        init: function (url,order,datatable, stock) {
            var oder_col = order || 0;
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */

            if (datatable) {
                // console.log(datatable);
                datatable_elemnt = $('#' + datatable);
                //console.log(datatable_elemnt);
            }
            else {
                datatable_elemnt = $('.dataTable');
            }
            var table = datatable_elemnt.dataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    "url":url,
                    "data": function ( d ) {
                         d.local = $('#locales').val();
                         d.stock=stock
                    }
                },
                retrieve: false,

                //"iDisplayLength": 20,
                // "pageLength": 20,
                //"aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "order": [[oder_col, "desc"]],
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "dom": '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
                "language": {
                    "emptyTable": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 resultados",
                    "infoFiltered": "(filtrado de _MAX_ total resultados)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ resultados",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    // "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar ordenar columnas ascendente",
                        "sortDescending": ": activar ordenar columnas descendente"
                    }
                },
                "fnInitComplete": function () {

                    setTimeout(function () {
                        $('div.dataTables_filter input').focus();
                    }, 5);

                }

            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }
    };
}();



var TablesDatatablesListaPrecios= function () {

    return {
        init: function (url,order,datatable, stock) {
            var oder_col = order || 0;
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */

            if (datatable) {
                // console.log(datatable);
                datatable_elemnt = $('#' + datatable);
                //console.log(datatable_elemnt);
            }
            else {
                datatable_elemnt = $('.dataTable');
            }
            var pago = 2;
            if ($('#pago1').is(':checked')){
                 pago = 1;
            }
            if($('#pago2').is(':checked')){
                 pago = 0;
            }

            var table = datatable_elemnt.dataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,

                "ajax":{
                    "url":url,
                    "data":  {
                        'pago' : pago

                    }


                },

                retrieve: false,


                //"iDisplayLength": 20,
                // "pageLength": 20,
                //"aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "order": [[oder_col, "desc"]],
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "dom": '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
                "language": {
                    "emptyTable": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 resultados",
                    "infoFiltered": "(filtrado de _MAX_ total resultados)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ resultados",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    // "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar ordenar columnas ascendente",
                        "sortDescending": ": activar ordenar columnas descendente"
                    }
                },
                "fnInitComplete": function () {

                    setTimeout(function () {
                        $('div.dataTables_filter input').focus();
                    }, 5);

                }

                });


            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }

    };

}();


var TablesDatatablesKardex = function () {

    return {
        init: function (url,order,datatable, stock) {
            var oder_col = order || 0;
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */

            if (datatable) {
                // console.log(datatable);
                datatable_elemnt = $('#' + datatable);
                //console.log(datatable_elemnt);
            }
            else {
                datatable_elemnt = $('.dataTable');
            }
            var table = datatable_elemnt.dataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    "url":url,
                    "data": function ( d ) {
                        d.local = $('#locales').val();
                        d.stock=stock
                    }
                },
                retrieve: false,
                "columnDefs": [
                    {
                        // The `data` parameter refers to the data for the cell (defined by the
                        // `data` option, which defaults to the column being worked with, in
                        // this case `data: 0`.
                        "render": function ( data, type, row ) {
                            console.log(row);
                            return '<div class="btn-group"> <a class="btn btn-default" data-toggle="tooltip"'+
                                'title="Kardex Interno" data-original-title="fa fa-comment-o"'+
                                'href="#" onclick="KARDEXINTERNO(\''+row[0]+'\')"> Kardex interno'+
                                '</a><a class="btn btn-default" data-toggle="tooltip"  title="Kardex Fiscal" data-original-title="fa fa-comment-o"'+
                                'onclick="KARDEXEXTERNO(\'' +row[0]+ '\');">   Kardex Fiscal </a> </div>';
                        },
                        "targets": 5
                    }
                ],
                //"iDisplayLength": 20,
                // "pageLength": 20,
                //"aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "order": [[oder_col, "desc"]],
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "dom": '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
                "language": {
                    "emptyTable": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 resultados",
                    "infoFiltered": "(filtrado de _MAX_ total resultados)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ resultados",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    // "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar ordenar columnas ascendente",
                        "sortDescending": ": activar ordenar columnas descendente"
                    }
                },
                "fnInitComplete": function () {

                    setTimeout(function () {
                        $('div.dataTables_filter input').focus();
                    }, 5);

                }

            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }
    };
}();




var TablesDatatablesPagosPendientes= function () {

    return {
        init: function (url,order,datatable, stock) {
            var oder_col = order || 0;
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */

            if (datatable) {
                // console.log(datatable);
                datatable_elemnt = $('#' + datatable);
                //console.log(datatable_elemnt);
            }
            else {
                datatable_elemnt = $('.dataTable');
            }
            var table = datatable_elemnt.dataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "ajax":{

                    "url":url,
                    "data": function (d) {
                        d.fecIni=$("#fecIni").val(),
                        d.fecFin=$("#fecFin").val(),
                        d.cboCliente=$("#cboCliente").val()
                    }
                },
                retrieve: false,

                //"iDisplayLength": 20,
                // "pageLength": 20,
                //"aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "order": [[oder_col, "desc"]],
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "dom": '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
                "language": {
                    "emptyTable": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 resultados",
                    "infoFiltered": "(filtrado de _MAX_ total resultados)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ resultados",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    // "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar ordenar columnas ascendente",
                        "sortDescending": ": activar ordenar columnas descendente"
                    }
                },
                "fnInitComplete": function() {

                    setTimeout(function() {
                        $('div.dataTables_filter input').focus();
                    }, 5);

                }

            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }
    };
}();

var TablesDatatablesCuentasPorPagar= function() {

    return {
        init: function (url,order,datatable, stock) {
            var oder_col = order || 0;
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */

            if (datatable) {
                // console.log(datatable);
                datatable_elemnt = $('#' + datatable);
                //console.log(datatable_elemnt);
            }
            else {
                datatable_elemnt = $('.dataTable');
            }
            var table = datatable_elemnt.dataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "ajax":{

                    "url":url,
                    "data": function (d) {
                            d.fecIni=$("#fecIni").val(),
                            d.fecFin=$("#fecFin").val(),
                            d.proveedor=$("#proveedor").val()
                    }
                },
                retrieve: false,

                //"iDisplayLength": 20,
                // "pageLength": 20,
                //"aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "order": [[oder_col, "desc"]],
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "dom": '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
                "language": {
                    "emptyTable": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 resultados",
                    "infoFiltered": "(filtrado de _MAX_ total resultados)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ resultados",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    // "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar ordenar columnas ascendente",
                        "sortDescending": ": activar ordenar columnas descendente"
                    }
                },
                "fnInitComplete": function () {

                    setTimeout(function () {
                        $('div.dataTables_filter input').focus();
                    }, 5);

                }

            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }
    };
}();
