<?php namespace Oxmosys;
// Cambia la directory con la root del progetto
chdir('../../');

require_once("php/config/requires.php");

use Oxmosys\DML;

try {
    $DML = new DML("APP_SUPPLIERS");

    switch ($_POST["OPERATION"]) {
        case "I":
            $params = array(
                "CODE" => $_POST["CODE"],
                "BUNAME" => $_POST["BUNAME"],
                "VATID" => $_POST["VATID"],
                "TELEPHONE" => $_POST["TELEPHONE"],
                "ADDRESS" => $_POST["ADDRESS"],
                "EMAIL" => $_POST["EMAIL"],
                "USERREG" => $_POST["USERREG"],
                "USERUPDATE" => $_POST["USERUPDATE"],
                "NOTES" => $_POST["NOTES"]
            );

            $DML->insert($params);
            break;
        case "U":
            $params = array(
                "CODE" => $_POST["CODE"],
                "BUNAME" => $_POST["BUNAME"],
                "VATID" => $_POST["VATID"],
                "TELEPHONE" => $_POST["TELEPHONE"],
                "ADDRESS" => $_POST["ADDRESS"],
                "EMAIL" => $_POST["EMAIL"],
                "USERUPDATE" => $_POST["USERUPDATE"],
                "LASTUPDATE" => date('Y-m-d H:i:s'),
                "NOTES" => $_POST["NOTES"]
            );

            $DML->update("ID", $_POST["ID"], $params);
            break;
        case "D":
            $DML->delete("ID", $_POST["ID"]);
            break;
        default:
            throw new Exception("Operation not valid (I->Insert, U->Update, D->Delete)", 1);
            break;
    }
} catch (\Throwable $th) {
    $header = "HTTP/1.0 404 Errore: " . $th->getCode();
    $header .= " - " . $th->getMessage();
    die(header($header));
}
