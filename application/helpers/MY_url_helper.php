<?php

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