<?php

namespace Oxmosys;
// Cambia la directory con la root del progetto
chdir('../../');

require_once("php/config/requires.php");

use Oxmosys\DML;
use Exception;

$v_uppercase = true;
$logtype = "";

try {

    if (!$_POST["UPPERCASE"]) {
        $v_uppercase = false;
    }

    switch ($_POST["OPERATION"]) {
        case "I":

            $logtype = "INSERT";

            $DML = new DML($_POST);

            $params = array();

            foreach ($DML->parentTable as $key => $value) {
                if ($value["COLUMN"] === 'PASSWORD') {
                    $v_uppercase = false;
                    $params[strtolower($value["COLUMN"])] = password_hash($value["VALUE"], PASSWORD_DEFAULT);
                } else {
                    $params[strtolower($value["COLUMN"])] = $value["VALUE"];
                }
            }
            $DML->tbname = strtolower($value["TABLE"]);

            if ($v_uppercase) {
                $lastId = $DML->insert($params, DML::UPPER_CASE);
            } else {
                $lastId = $DML->insert($params);
            }

            // cancello i vecchi parametri per far posto ai nuovi
            unset($params);

            $params = array();

            // Mi segno la tabella padre
            $DML->parentTable = $DML->tbname;

            // Tramite l'id della transazione in corso inserisco gli altri record
            foreach ($DML->childrenTables as $key => $value) {
                foreach ($DML->childrenTables[$key] as $key2 => $value2) {
                    if (strtolower($value2["COLUMN"]) === 'password') {
                        $v_uppercase = false;
                        $params[strtolower($value2["COLUMN"])] = password_hash($value2["VALUE"], PASSWORD_DEFAULT);
                    } else {
                        $params[strtolower($value2["COLUMN"])] = $value2["VALUE"];
                    }
                }
                $DML->tbname = strtolower($value2["TABLE"]);
                $params[strtolower($DML->parentTable . "_ID")] = $lastId;
                if ($v_uppercase) {
                    $DML->insert($params, DML::UPPER_CASE);
                } else {
                    $DML->insert($params);
                }
            }

            echo $lastId;

            break;
        case "U":

            $logtype = "UPDATE";

            $DML = new DML($_POST);

            $params = array();

            foreach ($DML->parentTable as $key => $value) {
                if (strtolower($value["COLUMN"]) === 'password') {
                    $v_uppercase = false;
                    $params[strtolower($value["COLUMN"])] = password_hash($value["VALUE"], PASSWORD_DEFAULT);
                } else {
                    $params[strtolower($value["COLUMN"])] = $value["VALUE"];
                }
            }
            $DML->tbname = strtolower($value["TABLE"]);

            if ($v_uppercase) {
                $lastId = $DML->update("id", $params, DML::UPPER_CASE);
            } else {
                $lastId = $DML->update("id", $params);
            }

            // cancello i vecchi parametri per far posto ai nuovi
            unset($params);

            $params = array();

            // Mi segno la tabella padre
            $DML->parentTable = $DML->tbname;

            // Tramite l'id della transazione in corso inserisco gli altri record
            foreach ($DML->childrenTables as $key => $value) {
                foreach ($DML->childrenTables[$key] as $key2 => $value2) {
                    if (strtolower($value2["COLUMN"]) === 'password') {
                        $v_uppercase = false;
                        $params[strtolower($value2["COLUMN"])] = password_hash($value2["VALUE"], PASSWORD_DEFAULT);
                    } else {
                        $params[strtolower($value2["COLUMN"])] = $value2["VALUE"];
                    }
                }
                $DML->tbname = strtolower($value2["TABLE"]);
                $params[$DML->parentTable . "_ID"] = $lastId;
                if ($v_uppercase) {
                    $DML->update(strtolower($DML->parentTable . "_ID"), $params, DML::UPPER_CASE);
                } else {
                    $DML->update(strtolower($DML->parentTable . "_ID"), $params);
                }
            }

            break;
        case "D":
            $logtype = "DELETE";

            $DML = new DML($_POST);

            $params = array();

            foreach ($DML->parentTable as $key => $value) {
                $params[strtolower($value["COLUMN"])] = $value["VALUE"];
            }
            $DML->tbname = strtolower($value["TABLE"]);

            $last_value = end($params);
            $last_key = key($params);

            $lastId = $DML->delete("id", $last_value);

            // cancello i vecchi parametri per far posto ai nuovi
            unset($params);

            $params = array();

            // Mi segno la tabella padre
            $DML->parentTable = $DML->tbname;

            // Tramite l'id della transazione in corso inserisco gli altri record
            foreach ($DML->childrenTables as $key => $value) {
                foreach ($DML->childrenTables[$key] as $key2 => $value2) {
                    $params[strtolower($value2["COLUMN"])] = $value2["VALUE"];
                }
                $DML->tbname = strtolower($value2["TABLE"]);
                $params[strtolower($DML->parentTable . "_ID")] = $lastId;
                $DML->delete(strtolower($DML->parentTable . "_ID"), $params);
            }

            break;
        case "R":

            $logtype = "RESTORE";
            $DML = new DML($_POST);

            $params = array();

            foreach ($DML->parentTable as $key => $value) {
                $params[strtolower($value["COLUMN"])] = $value["VALUE"];
            }
            $DML->tbname = strtolower($value["TABLE"]);

            $last_value = end($params);
            $last_key = key($params);

            $lastId = $DML->restore("id", $last_value);

            // cancello i vecchi parametri per far posto ai nuovi
            unset($params);

            $params = array();

            // Mi segno la tabella padre
            $DML->parentTable = $DML->tbname;

            // Tramite l'id della transazione in corso inserisco gli altri record
            foreach ($DML->childrenTables as $key => $value) {
                foreach ($DML->childrenTables[$key] as $key2 => $value2) {
                    $params[strtolower($value2["COLUMN"])] = $value2["VALUE"];
                }
                $DML->tbname = strtolower($value2["TABLE"]);
                $params[strtolower($DML->parentTable . "_ID")] = $lastId;
                $DML->restore(strtolower($DML->parentTable . "_ID"), $params);
            }

            break;
        default:
            throw new Exception("Operation not valid (I->Insert, U->Update, D->Delete, R->Restore)", 1);
            break;
    }
} catch (\Throwable $th) {
    echo "ERR";
    $header = "HTTP/1.0 404 Errore: " . $th->getCode();
    $header .= "<br>On " . $th->getFile();
    $header .= "<br>At Line " . $th->getLine();
    $header .= "<br>" . $th->getMessage();
    $DML->logdml($logtype, $DML->tbname, $params, null, $header);
    die(header($header));
}
