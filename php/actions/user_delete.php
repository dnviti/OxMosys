<?php namespace Oxmosys;
// Cambia la directory con la root del progetto
chdir('../../');
require_once("php/config/requires.php");

$_user = new User();

try {
    $_user->delete("APP_USERS", $_POST["ID"]);
} catch (\Throwable $th) {
    $header = "HTTP/1.0 404 ";
    $header .= $th->getMessage();
    die(header($header));
}
