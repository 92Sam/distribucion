/*
 *  Document   : uiDraggable.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Draggable Blocks page
 */

var UiDraggable = function() {

    return {
        init: function() {
            /* Initialize draggable and sortable blocks, check out more examples at https://jqueryui.com/sortable/ */
            $('.draggable-blocks').sortable({
                connectWith: '.block',
                items: '.block',
                opacity: 0.75,
                handle: '.block-title',
                placeholder: 'draggable-placeholder',
                tolerance: 'pointer',
                start: function(e, ui){
                    ui.placeholder.css('height', ui.item.outerHeight());
                }
            });

            $('.draggable-tbody').sortable({
                tolerance: 'pointer',
                start: function(e, ui){
                    ui.placeholder.css('height', ui.item.outerHeight());
                },
               // handle: '.fa-arrows-v',
                update: function( event, ui ) {
                    var count = 0;
                   $("tr[id^='trunidad']").each(function(){
                       $(this).attr('id', 'trunidad'+count);

                       $("#trunidad"+count+" select[name^='medida']").attr('name', 'medida['+count+']');
                       $("#trunidad"+count+" select[name^='medida']").attr('id', 'medida'+count+'');

                       $("#trunidad"+count+" input[name^='unidad']").attr('name', 'unidad['+count+']');
                       $("#trunidad"+count+" input[name^='unidad']").attr('id', 'unidad'+count+'');

                       var countprecio=0;
                       $("#trunidad"+count+" input[name^='precio_id_']").each(function(){
                          $(this).attr('name', 'precio_id_'+count+'['+countprecio+']');
                           countprecio++;
                       });
                       $("#trunidad"+count+" input[name^='precio_id_']").attr('id', 'precio_id'+count);

                       var countprecio=0;
                       $("#trunidad"+count+" input[name^='precio_valor_']").each(function(){
                           $(this).attr('name', 'precio_valor_'+count+'['+countprecio+']');
                           countprecio++;
                       })
                       $("#trunidad"+count+" input[name^='precio_valor_']").attr('id', 'precio_valor'+count);


                       $("#trunidad"+count+" a[id^='eliminar']").attr('id', 'eliminar'+count);
                       $("#trunidad"+count+" a[id^='eliminar']").attr('onclick', 'eliminarunidad('+count+')');

                       count++;
                   })
                }
            });
        }
    };
}();