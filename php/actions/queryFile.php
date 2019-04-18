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

// try {
//     $post_data = unserialize($_POST["SERIAL"]);
//     $where = '';

//     if ($post_data) {
//         // costruisco la where

//     }
// } catch (\Throwable $th) {
//     $where = '';
// }


$jsonRes = $_component->valueFromQueryFile($filepath);

// var_dump($jsonRes);
//$return = $_POST;

//$return = $jsonRes;
echo $jsonRes;

// return $jsonRes;
