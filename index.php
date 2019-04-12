<?php namespace Oxmosys;

// Settaggi programma
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include all classes
include "php/config/requires.php";

// Use
use Oxmosys\Page;

// Stampa la pagina compilata
new Page(isset($_GET["p"]) ? (int)$_GET["p"] : -1);
