<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');



class phpword
{
    function __construct()
    {
        require_once 'PHPWord/src/PhpWord/Autoloader.php';
        \PhpOffice\PhpWord\Autoloader::register();
    }
}

?>