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