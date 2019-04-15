<?php namespace Oxmosys;
// Cambia la directory con la root del progetto
chdir('../../');

require_once("php/config/requires.php");

use Oxmosys\DML;
use Exception;

try {

    switch ($_POST["OPERATION"]) {
        case "I":

            $DML = new DML($_POST);

            $params = array();

            foreach ($DML->parentTable as $key => $value) {
                $params[$value["COLUMN"]] = $value["VALUE"];
            }
            $DML->tbname = $value["TABLE"];

            $lastId = $DML->insert($params, DML::UPPER_CASE);

            // cancello i vecchi parametri per far posto ai nuovi
            unset($params);

            $params = array();

            // Mi segno la tabella padre
            $DML->parentTable = $DML->tbname;

            // Tramite l'id della transazione in corso inserisco gli altri record
            foreach ($DML->childrenTables as $key => $value) {
                foreach ($DML->childrenTables[$key] as $key2 => $value2) {
                    $params[$value2["COLUMN"]] = $value2["VALUE"];
                }
                $DML->tbname = $value2["TABLE"];
                $params[$DML->parentTable . "_ID"] = $lastId;
                $DML->insert($params, DML::UPPER_CASE);
            }

            echo $lastId;

            break;
        case "U":

            $DML = new DML($_POST);

            $params = array();

            foreach ($DML->parentTable as $key => $value) {
                $params[$value["COLUMN"]] = $value["VALUE"];
            }
            $DML->tbname = $value["TABLE"];

            $lastId = $DML->update("ID", $params, DML::UPPER_CASE);

            // cancello i vecchi parametri per far posto ai nuovi
            unset($params);

            $params = array();

            // Mi segno la tabella padre
            $DML->parentTable = $DML->tbname;

            // Tramite l'id della transazione in corso inserisco gli altri record
            foreach ($DML->childrenTables as $key => $value) {
                foreach ($DML->childrenTables[$key] as $key2 => $value2) {
                    $params[$value2["COLUMN"]] = $value2["VALUE"];
                }
                $DML->tbname = $value2["TABLE"];
                $params[$DML->parentTable . "_ID"] = $lastId;
                $DML->update($DML->parentTable . "_ID", $params, DML::UPPER_CASE);
            }

            break;
        case "D":
            $DML = new DML($_POST);

            $params = array();

            foreach ($DML->parentTable as $key => $value) {
                $params[$value["COLUMN"]] = $value["VALUE"];
            }
            $DML->tbname = $value["TABLE"];

            $last_value = end($params);
            $last_key = key($params);

            $lastId = $DML->delete("ID", $last_value);

            // cancello i vecchi parametri per far posto ai nuovi
            unset($params);

            $params = array();

            // Mi segno la tabella padre
            $DML->parentTable = $DML->tbname;

            // Tramite l'id della transazione in corso inserisco gli altri record
            foreach ($DML->childrenTables as $key => $value) {
                foreach ($DML->childrenTables[$key] as $key2 => $value2) {
                    $params[$value2["COLUMN"]] = $value2["VALUE"];
                }
                $DML->tbname = $value2["TABLE"];
                $params[$DML->parentTable . "_ID"] = $lastId;
                $DML->delete($DML->parentTable . "_ID", $params);
            }

            break;
        default:
            throw new Exception("Operation not valid (I->Insert, U->Update, D->Delete)", 1);
            break;
    }
} catch (\Throwable $th) {
    echo "ERR";
    $header = "HTTP/1.0 404 Errore: " . $th->getCode();
    $header .= " - " . $th->getMessage();
    die(header($header));
}
