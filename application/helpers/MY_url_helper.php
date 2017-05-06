<?php
function divide_in($number, $parts)
{
    $max_div = 1;
    if ($number >= $parts)
        $max_div = intval($number / $parts);
    for ($i = 0; $i < $parts; $i++) {
        $number -= $max_div;
        $result[$i] = $max_div;
        if ($i == $parts - 1)
            $result[$i] += $number;
    }

    return $result;
}

function get_tipo_doc($cod)
{
    switch ($cod) {
        case 3: {
            return array('code' => $cod, 'value' => 'Boleta de Venta');
        }
        case 1: {
            return array('code' => $cod, 'value' => 'Factura');
        }
        case 7: {
            return array('code' => $cod, 'value' => 'Nota de Cr&eacute;dito');
        }
        case 8: {
            return array('code' => $cod, 'value' => 'Nota de D&eacute;bito');
        }
        case 20: {
            return array('code' => $cod, 'value' => 'Comprobante de Retenci&oacute;n');
        }
        case 31: {
            return array('code' => $cod, 'value' => 'Gu&iacute;a de Remisi&oacute;n - Transportista');
        }
        default: {
            return array('code' => 0, 'value' => 'Otros');
        }
    }
}


function get_tipo_operacion($cod)
{
    switch ($cod) {
        case 2: {
            return array('code' => $cod, 'value' => 'COMPRA');
        }
        case 1: {
            return array('code' => $cod, 'value' => 'VENTA');
        }
        case 5: {
            return array('code' => $cod, 'value' => 'DEVOLUCI&Oacute;N RECIBIDA');
        }
        case 6: {
            return array('code' => $cod, 'value' => 'DEVOLUCI&Oacute;N ENTREGADA');
        }
        case 7: {
            return array('code' => $cod, 'value' => 'PROMOCI&Oacute;N');
        }
        case 9: {
            return array('code' => $cod, 'value' => 'DONACI&Oacute;N');
        }
        case 11: {
            return array('code' => $cod, 'value' => 'TRANSFERENCIA ENTRE ALMACENES');
        }
        default: {
            return array('code' => 0, 'value' => 'Otros');
        }
    }
}

function valueOption($config_value, $default = '0')
{
    $CI =& get_instance();
    if ($CI->session->userdata($config_value) == NULL) {
        return $default;
    }
    return $CI->session->userdata($config_value);
}

function getCajaBanco($banco_id)
{
    $CI =& get_instance();
    $result = $CI->db->select('caja_desglose.*, caja.moneda_id')
        ->from('banco')
        ->join('caja_desglose', 'banco.cuenta_id = caja_desglose.id')
        ->join('caja', 'caja.id = caja_desglose.caja_id')
        ->where('banco_id', $banco_id)
        ->get()->row();

    $moneda = '';
    if ($result != NULL)
        $moneda = $result->moneda_id == 1 ? MONEDA : DOLAR;

    return $result != NULL ? $result->descripcion . ' (' . $moneda . ')' : 'No definido';
}

function sumCod($cod, $length = 4)
{
    $len = $length;

    if ($len < count(str_split($cod))) $len++;

    $temp = array_reverse(str_split($cod));
    $result = array();

    $n = 0;
    for ($i = $len - 1; $i >= 0; $i--) {
        if (isset($temp[$n]))
            $result[] = $temp[$n++];
        else
            $result[] = '0';
    }
    return implode(array_reverse($result));
}

function restCod($cod)
{

    $cod = (int)$cod;

    return $cod;
}

function last_day($year, $mes)
{
    return date("d", (mktime(0, 0, 0, $mes + 1, 1, $year) - 1));
}

function get_day_week($day)
{
    switch ($day) {
        case 1: {
            return 'Lunes';
        }
        case 2: {
            return 'Martes';
        }
        case 3: {
            return 'Miercoles';
        }
        case 4: {
            return 'Jueves';
        }
        case 5: {
            return 'Viernes';
        }
        case 6: {
            return 'S&aacute;bado';
        }
        case 7: {
            return 'Domingo';
        }
    }
}

function getMes($num)
{
    switch ($num) {
        case 1: {
            return 'Enero';
        }
        case 1: {
            return 'Enero';
        }
        case 2: {
            return 'Febrero';
        }
        case 3: {
            return 'Marzo';
        }
        case 4: {
            return 'Abril';
        }
        case 5: {
            return 'Mayo';
        }
        case 6: {
            return 'Junio';
        }
        case 7: {
            return 'Julio';
        }
        case 8: {
            return 'Agosto';
        }
        case 9: {
            return 'Septiembre';
        }
        case 10: {
            return 'Octubre';
        }
        case 11: {
            return 'Noviembre';
        }
        case 12: {
            return 'Diciembre';
        }
    }
}
