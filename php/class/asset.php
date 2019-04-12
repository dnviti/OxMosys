<?php namespace Oxmosys;

use Oxmosys\Page;

class Asset extends Page
{
    function getCss($paths)
    {
        foreach ($paths as &$value) {
            echo '<link rel="stylesheet" href="' . $value . '">';
        }
    }
    function getJs($paths)
    {
        foreach ($paths as &$value) {
            echo '<script type="text/javascript" src="' . $value . '"></script>';
        }
    }
}
