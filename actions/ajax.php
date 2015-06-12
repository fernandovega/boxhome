<?php
/*
 *Acción para subir la imagenes y archivos por ajax del fomulario share_plus
 * 
 */
global $CONFIG; 


action_gatekeeper();

$ajax = get_input('ajax'); //tipo de formulario enviado imagen o file
$user_id = get_loggedin_userid();
$share_delete = get_input('share_delete_id'); //id de index de arreglo d eliminar
$path = dirname(__FILE__) . DIRECTORY_SEPARATOR ."uploads/";


//echo $ajax;
if($ajax=='file')
    $valid_formats = array("zip", "doc", "odt","pdf","ppt","xls","jpg", "jpeg", "png", "JPG", "PNG","gif");
else
    $valid_formats = array("jpg", "jpeg", "png", "JPG", "PNG","gif");


if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
{
        $name = $_FILES['share_file']['name'];
        $size = $_FILES['share_file']['size'];
        //var_dump($_FILES['share_file']);
        
        //echo filesize($_FILES['share_file']['tmp_name']);
        
        if(strlen($name))//cuando se cumple esta condicion es porque se recibio un archivo
        {       
                $info = pathinfo($name);
                $ext = $info['extension'];
                $txt = $info['filename'];
               
                if(in_array($ext,$valid_formats))
                {
                    if($size<(1024*1024*10))//Limite de 3Mb
                    {
                            $name = str_replace(" ", "_", $txt).'_'.$user_id.".".$ext;
                            $tmp = $_FILES['share_file']['tmp_name'];
                       if(!isset ($_SESSION['images_'.$user_id]) || !in_array($name, $_SESSION['images_'.$user_id]))
                       {
                            if(move_uploaded_file($tmp, $path.$name))//Subir archivo a la carpeta upload
                            {   
                                           
                                if($ajax=='file'){//si es un archivo muestro su nombre                                   
                                    $_SESSION['file_'.$user_id] = $name; //almaceno el archivo subido
                                    echo $txt.".".$ext;
                                    exit;
                                }
                                else{ 
                                    if(!isset ($_SESSION['images_'.$user_id]))//si no existe la variable de sesion la creo
                                      $_SESSION['images_'.$user_id]=array();
                                    
                                    array_push($_SESSION['images_'.$user_id],$name);
                                    end($_SESSION['images_'.$user_id]);         // move the internal pointer to the end of the array
                                    $index = key($_SESSION['images_'.$user_id]);
                                    //header("Content-type: application/json");
                                    echo '{"status":1,"message":"Success!","filename":"'.$name.'","index":"'.$index.'"}';
                                    exit;
                                }
                            }
                            else{                                
                                echo '{
                                        "error"		: 1,
                                        "message"	: "Error al subir el archivo."
                                }';
                                exit;
                            }
                       }
                       else{                            
                            echo '{
                                        "error"		: 1,
                                        "message"	: "Ya existe un archivo con el mismo nombre"
                                }';
                                exit;
                       }
                    }
                    else{			
                        echo '{
                                        "error"		: 1,
                                        "message"	: "Tamaño maximo de 10 MB"
                                }';
                        exit;
                    }
                }
                else{	
                    echo '{
                                    "error"		: 1,
                                    "message"	: "Formato no soportado"
                            }';
                    exit;
                }
        }
        else if($share_delete!='') //si se envio el parametro de id a borrar se elimina la imagen del arreglo y el fomulario se envio solo con este campo
        {   
            //echo "<script> alert('".$_SESSION['images_'.$user_id][$share_delete]."')</script>";
            
            //$array = $_SESSION['images_'.$user_id];
            $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'uploads/'.$_SESSION['images_'.$user_id][$share_delete];
            if (file_exists($filename))
                unlink($filename);
            
            //echo $_SESSION['images_'.$user_id][$share_delete];
            unset($_SESSION['images_'.$user_id][$share_delete]); 
            
            //$array = array_values($array);
            
            //$_SESSION['images_'.$user_id] = $array;
            //var_dump($_SESSION['images_'.$user_id]);
            //$res = print_r($_SESSION['images_'.$user_id]);
            
            //verArregloImagenes($user_id, $CONFIG->url);//'{"status":2,"message":"Success!","filename":"'.$share_delete.'","index":"'.$share_delete.'"}';
            echo "<p>Se elimino la imagen </p>";
            exit;
        }
        else
           echo '';
}

//Funcion para leer e imprimir el arreglo de  imagenes
function verArregloImagenes($user_id, $url)
{
    if(isset ($_SESSION['images_'.$user_id])){
        for($i=0;$i<count($_SESSION['images_'.$user_id]);$i++)
        {        
        echo '<div class="preview"><a href="#" original-title="Eliminar Imagen" id="'.$i.'" class="share_delete_imagen msjbox"><img src="'.$url.'/mod/plusriver/graphics/Bin.png" align ="left" hspace="1px" /></a><img src="'.$url.'/mod/plusriver/actions/share_plus/uploads/'.$_SESSION['images_'.$user_id][$i].'" /></div>';
        }
    }
}


?>
