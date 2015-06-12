<?php

global $CONFIG; 


action_gatekeeper();

$user_id = get_loggedin_userid();
$share_delete = get_input('share_delete_id'); //id de index de arreglo d eliminar

if($share_delete!='') //si se envio el parametro de id a borrar se elimina la imagen del arreglo y el fomulario se envio solo con este campo
{   
    //echo "<script> alert('".$_SESSION['images_'.$user_id][$share_delete]."')</script>";

    //$array = $_SESSION['images_'.$user_id];
    $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'uploads/'.$_SESSION['camera_'.$user_id][$share_delete];
    if (file_exists($filename))
        unlink($filename);
    
    unset($_SESSION['camera_'.$user_id][$share_delete]); 

    //$array = array_values($array);

    //$_SESSION['images_'.$user_id] = $array;
    //var_dump($_SESSION['images_'.$user_id]);
    //$res = print_r($_SESSION['images_'.$user_id]);

    //verArregloImagenes($user_id, $CONFIG->url);//'{"status":2,"message":"Success!","filename":"'.$share_delete.'","index":"'.$share_delete.'"}';
    echo "<p>Se elimino la foto</p>";
    
}

exit;

?>
