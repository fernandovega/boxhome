<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/../../engine/start.php');
global $CONFIG; 

//$ajax = get_input('ajax'); //tipo de formulario enviado imagen o file
$user = get_user(get_loggedin_userid());
/*
	This file receives the JPEG snapshot
	from webcam.swf as a POST request.
date("m.d.y");
date("H:i:s");
*/

// We only need to handle POST requests:
if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
	exit;
}
$user_id = $user->guid;

$folder = dirname(__FILE__) . DIRECTORY_SEPARATOR ."uploads/";

$date = date("m-d-y_H:i:s");

$filename = $user->username.'_'.$date.'.jpg';

$original = $folder.$filename;

// The JPEG snapshot is sent as raw input:
$input = file_get_contents('php://input');

if(md5($input) == '7d4df9cc423720b7f1f3d672b89362be'){
	// Blank image. We don't need this one.
	exit;
}

$result = file_put_contents($original, $input);
if (!$result) {
	echo '{
		"error"		: 1,
		"message"	: "Failed save the image. Make sure you chmod the uploads folder and its subfolders to 777."
	}';
	exit;
}

$info = getimagesize($original);
if($info['mime'] != 'image/jpeg'){
	unlink($original);
	exit;
}


if(!isset ($_SESSION['camera_'.$user_id]))//si no existe la variable de sesion la creo
    $_SESSION['camera_'.$user_id]=array();

array_push($_SESSION['camera_'.$user_id],$filename); //almaceno las imagenes subidas
end($_SESSION['camera_'.$user_id]);         // move the internal pointer to the end of the array
$index = key($_SESSION['camera_'.$user_id]);

header("Content-type: application/json");
echo '{"status":1,"message":"Success!","filename":"'.$filename.'","index":"'.$index.'"}';

exit;


?>