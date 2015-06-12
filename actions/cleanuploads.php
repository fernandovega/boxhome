<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/../../engine/start.php');
global $CONFIG; 

$user_id = get_loggedin_userid();

//Imagenes subidas por la camara web
if(isset($_SESSION['camera_'.$user_id])){
    for($i=0;$i<count($_SESSION['camera_'.$user_id]);$i++)
    {        
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'uploads/'.$_SESSION['camera_'.$user_id][$i];
        if (file_exists($filename))
            unlink($filename);
    }
    unset ($_SESSION['camera_'.$user_id]);
}

//Imagenes subidas de la computadora
if(isset ($_SESSION['images_'.$user_id])){
    for($i=0;$i<count($_SESSION['images_'.$user_id]);$i++)
    {        
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'uploads/'.$_SESSION['images_'.$user_id][$i];
        if (file_exists($filename))
            unlink($filename);
    }
    unset ($_SESSION['images_'.$user_id]);
}


//Archivo subido
if(isset ($_SESSION['file_'.$user_id])){
    $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'uploads/'.$_SESSION['file_'.$user_id];
    if (file_exists($filename))
            unlink($filename);
    
    unset ($_SESSION['file_'.$user_id]);
}
 exit;

?>
