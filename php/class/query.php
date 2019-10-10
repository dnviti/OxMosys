<?php namespace Oxmosys;

class Query
{

    function getQueryValue($sql)
    {

        $user = $_SESSION["username"];
        $pass = $_SESSION["password"];

        if ($conn->query($sql) === true) {
            return true;
        } else {
            return false;
        }
    }
}

$_query = new Query();
