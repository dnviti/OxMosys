<?php namespace Oxmosys;
// Cambia la directory con la root del progetto
chdir('../../');
include "php/config/requires.php";

session_start();

use Oxmosys\AppConfig;
use Oxmosys\Page;

$app = new AppConfig();

$_component = new Component($app, $_SESSION["PAGE"]["ID"]);

$filepath = $_POST["QUERY"];

$params = [];

if ($_POST["PARAMS"]) {

    parse_str($_POST["PARAMS"], $params);

    $params = array_values($params);
    /**
     * 'inidate' => string '2019-04-01' (length=10)
     * 'findate' => string '2019-04-30' (length=10)
     * 
     */
}


$jsonRes = $_component->valueFromQueryFile($filepath, $params);

// var_dump($jsonRes);
//$return = $_POST;

//$return = $jsonRes;
echo $jsonRes;

// return $jsonRes;
