<?php namespace Oxmosys;

chdir("../../");
include "php/config/requires.php";

use Oxmosys\Cookie;

session_start();
/*
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 1000);
        setcookie($name, '', time() - 1000, '/');
    }
}

 */
// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!

foreach (Cookie::get() as $key => $value) {
    Cookie::unset((string)$key);
}
// Finally, destroy the session.
session_destroy();
