<?php namespace Oxmosys;
// Cambia la directory con la root del progetto
chdir('../../');
include "php/config/requires.php";

session_start();

use Oxmosys\AppConfig;
use Oxmosys\Page;

$app = new AppConfig();

$_component = new Component($app, $_SESSION["PAGE"]["ID"]);

$sql = $_POST["QUERY"];

$jsonRes = $_component->valueFromQuery($sql);

// var_dump($jsonRes);
//$return = $_POST;

//$return = $jsonRes;
echo $jsonRes;

// return $jsonRes;
