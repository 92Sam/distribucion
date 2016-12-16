<?php


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
        case 3:{
            return 'Miercoles';
        }
        case 4:{
            return 'Jueves';
        }
        case 5:{
            return 'Viernes';
        }
        case 6:{
            return 'S&acute;bado';
        }
        case 7:{
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