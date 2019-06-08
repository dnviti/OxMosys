<?php namespace Oxmosys;

session_start();
// Cambia la directory con la root del progetto
chdir('../../');

include "php/config/requires.php";

use Oxmosys\User;
use Oxmosys\Cookie;
use Oxmosys\DML;

try {
    $_user = new User();
    if ($_user->login($_POST["USERNAME"], $_POST["PASSWORD"], 'a')) {
        $_user->username = $_POST["USERNAME"];
        Cookie::set("USER", $_user->getProperties());
    }
    $params = $_user->getProperties();
    DML::logdml("LOGIN", "APP_USERS", $_user->getProperties());
} catch (\Throwable $th) {
    session_abort();
    $header = "HTTP/1.0 404 Errore: " . $th->getCode();
    $header .= " - " . $th->getMessage();
    die(header($header));
}
