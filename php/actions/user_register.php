<?php namespace Oxmosys;
// Cambia la directory con la root del progetto
chdir('../../');

require_once("php/config/requires.php");

use Oxmosys\User;

try {
    $_user = new User();

    if (!isset($_POST["ID"]) || $_POST["ID"] == "" || $_POST["ID"] == " ") {
        $params = array(
            "USERNAME" => $_POST["USERNAME"],
            "PASSWORD" => password_hash($_POST["PASSWORD"], PASSWORD_DEFAULT),
            "EMAIL" => $_POST["EMAIL"],
            "APP_USER_ROLE_ID" => $_POST["APP_USER_ROLE_ID"],
            "NAME" => $_POST["NAME"],
            "SURNAME" => $_POST["SURNAME"],
            "NOTES" => $_POST["NOTES"],
            "USERREG" => $_POST["USERREG"]
        );

        return ($_user->register($params));
    } else {
        $params = array(
            "USERNAME" => $_POST["USERNAME"],
            //"PASSWORD" => password_hash($_POST["PASSWORD"], PASSWORD_DEFAULT),
            "EMAIL" => $_POST["EMAIL"],
            "APP_USER_ROLE_ID" => $_POST["APP_USER_ROLE_ID"],
            "NAME" => $_POST["NAME"],
            "SURNAME" => $_POST["SURNAME"],
            "NOTES" => $_POST["NOTES"]
        );

        return ($_user->update($_POST["ID"], $params));
    }
} catch (\Throwable $th) {
    $header = "HTTP/1.0 404 Errore: " . $th->getCode();
    $header .= " - " . $th->getMessage();
    die(header($header));
}
