<?php namespace Oxmosys;

chdir("../../");
include "php/config/requires.php";

use Oxmosys\Cookie;
use Oxmosys\DML;

session_start();

$ck = Cookie::get("USER")["USERNAME"];

DML::logdml("LOGOUT", "APP_USERS", Cookie::get("USER"));

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!

foreach (Cookie::get() as $key => $value) {
    Cookie::unset((string)$key);
}
// Finally, destroy the session.
session_destroy();
