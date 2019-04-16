<?php namespace Oxmosys;
// Cambia la directory con la root del progetto
chdir('../../');

require_once("php/config/requires.php");

use Oxmosys\DML;
use Exception;

try {
    $app = new AppConfig();

    $valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp', 'pdf', 'doc', 'ppt'); // valid extensions
    $path = 'assets/img/upd/'; // upload directory
    if (!empty($_POST['name']) || !empty($_POST['email']) || $_FILES['image']) {
        $img = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        // get uploaded file's extension
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        // can upload same image using rand function
        $final_image = $_POST["APP_WAREHOUSE_ITEMS-ID"] . '.' . explode('.', $img)[1];
        // check's valid format
        if (in_array($ext, $valid_extensions)) {
            $path = $path . strtolower($final_image);
            if (move_uploaded_file($tmp, $path)) {
                echo $path;
                //insert form data in the database
                $insert = $app::$qb->run("UPDATE app_custom_warehouse_items SET imagepath = '$path' WHERE APP_WAREHOUSE_ITEMS_ID = " . $_POST["APP_WAREHOUSE_ITEMS-ID"]);
                //echo $insert?'ok':'err';
            }
        } else {
            echo 'invalid';
        }
    }
} catch (\Throwable $th) {
    echo "ERR";
    $header = "HTTP/1.0 404 Errore: " . $th->getCode();
    $header .= " - " . $th->getMessage();
    die(header($header));
}
