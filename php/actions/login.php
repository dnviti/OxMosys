<?php namespace Oxmosys;

session_start();
// Cambia la directory con la root del progetto
chdir('../../');

include "php/config/requires.php";

use Oxmosys\User;

$_user = new User(session_id());

// die(header(var_dump($_user)));

if ($_user->login($_user->dbConnection, $_POST["USERNAME"], $_POST["PASSWORD"], 'a')) {
    $_user->username = $_POST["USERNAME"];
    $_SESSION["USER"] = $_user->getProperties();
} else {
    session_abort();
    die(header("HTTP/1.0 404 Dati Errati"));
}
