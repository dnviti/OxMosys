<?php namespace Oxmosys;

session_start();
// Cambia la directory con la root del progetto
chdir('../../');

include "php/config/requires.php";

use Oxmosys\OxMosysMail;

$mail = new OxMosysMail();

if ($_POST["DEBUG"] == "1") {
    $mail->SendDebug($_POST["SUBJ"], $_POST["MESS"]);
}
