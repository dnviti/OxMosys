<?php namespace Oxmosys;

// Include all classes
chdir('../../');
include "php/config/requires.php";

use Oxmosys\InitDB;

try {
    $initdb = new InitDB();
    $initdb->initAll();
} catch (\Throwable $th) {
    $header = "HTTP/1.0 404 Code: " . $th->getCode();
    $header .= " - " . $th->getMessage();
    die(header($header));
}
