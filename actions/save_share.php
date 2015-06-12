<?php
/*
 *Acción para guardar el fomulario final de share_plus y publicarlo, actualizamos el dashboard
 * 
 */
global $CONFIG;
action_gatekeeper();

$url_type = get_input('share_url_type');
$url_videoid = get_input('share_url_videoid');
$url_image = get_input('share_url_image'); //tipo de formulario enviado imagen o file
$url_desc = str_replace('|', '-', get_input('share_url_desc'));
$url_title =  str_replace('|', '-', get_input('share_url_title'));
$url_dir = get_input('share_url_dir');
$album_id = get_input('share_album_id');
$album_name = get_input('share_album_name');
$msj = get_input('share_msj');
$type = get_input('share_type');
$user_id = get_loggedin_userid();
$access = get_input('share_access',1);

$path = dirname(__FILE__) . DIRECTORY_SEPARATOR ."uploads/";

$parser_url = '';


if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
{
    
    if($url_dir!='' && $url_title!=''  && $url_type!=''){
        $parser_url = $url_type.'|'.$url_title.'|'.$url_dir.'|'.$url_image.'|'.$url_desc.'|'.$url_videoid;//tipo, titulo, url, url de imagen, descripcion,videoid
    }
    
    switch ($type){
        case 'text':
            if($msj!=''){
                if(strlen($msj)>=140){
                    
                        $blog = new ElggObject();
            // Tell the system it's a blog post
                        $blog->subtype = "blog";
                // Set its owner to the current user
                        $blog->owner_guid = $user_id;
                // Set it's container
                        $blog->container_guid = $user_id;
                // For now, set its access
                        $blog->access_id = $access;
                // Set its title and description appropriately
                        $blog->title = 'Entrada rapida de blog';
                        $blog->description =  $msj;
                // Now let's add tags. We can pass an array directly to the object property! Easy.

                //whether the user wants to allow comments or not on the blog post
                        $blog->comments_on = 'On';

                // Now save the object
                        if (!$blog->save()) {
                                echo '{"error":1,"message": "Hubo un error al crear la entrada de blog"}';
                                exit;
                        }

                    //Agregar integración con facebook y twitter
                    if($parser_url!=''){
                        $blog->annotate('url_parser', $parser_url, $blog->access_id, $user_id);
                    }                                

                    // tweet
                    $params = array(
                            'plugin' => 'blog',
                            'message' => elgg_get_excerpt($blog->description,139)
                    );

                    trigger_plugin_hook('tweet', 'twitter_service', $params);
                    trigger_plugin_hook('facebookstatus', 'facebook_service', $params);        

                    // add to river
                    add_to_river('river/object/blog/create', 'create', $user_id, $blog->guid);
                    
                    $patron = '/\@([A-Za-z0-9\_\.\-]*)/i';
                    preg_match_all ($patron, $blog->description, $matches);
                    $users_tagging = $matches[1];
                    $apariciones = array();
                    foreach($users_tagging as $i=>$value){
                        //Notificacion de mencion a usuario
                        if(!in_array($value, $apariciones)){
                            if($user_tag = get_user_by_username($value)){
                                new_notification(1,
                                               $user_tag->guid, 
                                               get_loggedin_userid(), 
                                               sprintf(" te ha mencionado en una entrada de Blog. <a href=\"{$blog->getURL()}\">Click para ver</a>.<br>
                                                <span id=\"notification_detail\">".elgg_get_excerpt($blog->description,140)."</span>")
                                               );
                            }
                            $apariciones[]=$value;  
                        }
                    }  
                    

                    echo '{"status":1,"message": "Se creo correctamente un post de Blog" }';    
                    exit;
    
                }
                else{
                    if (thewire_save_post($msj, $access, $user_id, 'site',$parser_url)) {
                       echo '{"status":1,"message": "Se creo correctamente un post de Microblogging" }';
                    }
                    else{
                       echo '{"error":1,"message": "Hubo un problema al crear el post de Microblogging" }';
                    }
                }   
            }
            else
                echo '{"error":1,"message": "Por favor escribe un texto" }';
            break;
        case 'album_img':            
            share_with_tidypics($user_id, $access, $msj, $album_id, $album_name, $_SESSION['images_'.$user_id]);
            break;
        case 'album_webcam':
            share_with_tidypics($user_id, $access, $msj, $album_id, $album_name, $_SESSION['camera_'.$user_id]);
            break;
        case 'file':
            if(isset ($_SESSION['file_'.$user_id])){
                $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'uploads/'.$_SESSION['file_'.$user_id];
                $file_new = array();
                if (file_exists($filename)){
                    $info = pathinfo($filename);
                    $file_new['type'] = mime_content_type($filename);                    
                    $file_new['ext'] =  $info['extension'];
                    $file_new['name'] =  $info['basename'];
                    $file_new['tmp_name'] = $filename;
                    
                    $file = new FilePluginFile();
                    $file->subtype = "file";
                    $file->title = $file_new['name'];
                    $file->description = $msj;
                    $file->access_id = $access;
                    $file->container_guid = $user_id;
                    $prefix = "file/";
                    $filestorename = elgg_strtolower(time().$file_new['name']);
                    $file->setFilename($prefix.$filestorename);
                    $mime_type = $file->detectMimeType($file_new['tmp_name'], $file_new['type']);
                    $file->setMimeType($mime_type);
                    $file->originalfilename = $file_new['name'];
                    $file->simpletype = get_general_file_type($mime_type);

                    // Open the file to guarantee the directory exists
                    $file->open("write");
                    $file->close();
                    // move using built in function to allow large files to be uploaded
                    rename($filename, $file->getFilenameOnFilestore());

                    $guid = $file->save();

                    // if image, we need to create thumbnails (this should be moved into a function)
                    if ($guid && $file->simpletype == "image") {
                            $thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),60,60, true);
                            if ($thumbnail) {
                                    $thumb = new ElggFile();
                                    $thumb->setMimeType($mime_type);

                                    $thumb->setFilename($prefix."thumb".$filestorename);
                                    $thumb->open("write");
                                    $thumb->write($thumbnail);
                                    $thumb->close();

                                    $file->thumbnail = $prefix."thumb".$filestorename;
                                    unset($thumbnail);
                            }

                            $thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),153,153, true);
                            if ($thumbsmall) {
                                    $thumb->setFilename($prefix."smallthumb".$filestorename);
                                    $thumb->open("write");
                                    $thumb->write($thumbsmall);
                                    $thumb->close();
                                    $file->smallthumb = $prefix."smallthumb".$filestorename;
                                    unset($thumbsmall);
                            }

                            $thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),600,600, false);
                            if ($thumblarge) {
                                    $thumb->setFilename($prefix."largethumb".$filestorename);
                                    $thumb->open("write");
                                    $thumb->write($thumblarge);
                                    $thumb->close();
                                    $file->largethumb = $prefix."largethumb".$filestorename;
                                    unset($thumblarge);
                            }
                    }
                    
                    if ($guid){
                        add_to_river('river/object/file/create', 'create', $user_id, $file->guid);
                        echo '{"status":1,"message": "Se publico correctamente tu archivo" }';
                    }
                    else
                        echo '{"error":1,"message": "Hubo un error al subir tu archivo, lo sentimos." }';
                }
            }  
                
            break;
        default:
            break;
    }
    exit;
}

//Funcion para leer e imprimir el arreglo de  imagenes
function share_with_tidypics($user_id, $access, $msj, $album_id, $album_name, $session_images)
{
    if($album_id!=''){
                $album = get_entity($album_id);
                if($album->description!='')
                    $album->description = $msj.'<br/>------------------------</br>'.$album->description;
                else
                    $album->description = $msj;
                
                $album->save();
            }
            else if($album_name!=''){
                $album = new TidypicsAlbum();
                $album->container_guid = $user_id;
                $album->owner_guid = $user_id;
                $album->access_id = $access;
                $album->title = $album_name;
                $album->description = $msj;

                $album->new_album = TP_NEW_ALBUM;
                if (!$album->save()) {
                    echo '{"error":1,"message": "No se pudo crear el nuevo album" }';
                }                
            }
            
            if($album){
                $image_lib = get_plugin_setting('image_lib', 'tidypics');
                if (!$image_lib) {
                        $image_lib = "GD";
                }

                $img_river_view = get_plugin_setting('img_river_view', 'tidypics');

                if ($album->new_album == TP_NEW_ALBUM) {
                        $new_album = true;
                } else {
                        $new_album = false;
                }
                
                //error_log(var_dump($_SESSION["images_".$user_id]));
                
                if(count($session_images)>0){
                    $sent_file = array();
                    $uploaded_images = array();
                    for($i=0;$i<count($session_images);$i++)
                    {   
                        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'uploads/'.$session_images[$i];
                        
                        $info = pathinfo($filename);
                        $sent_file['type'] = mime_content_type($filename);                    
                        $sent_file['ext'] =  $info['extension'];
                        $sent_file['name'] =  $info['basename'];
                        $sent_file['tmp_name'] = $filename;
                        $sent_file['size']= filesize($filename);
                    
                        $name = $sent_file['name'];
                        $mime = $sent_file['type'];
                        // must be an image
                                                
                        //this will save to user's folder in /image/ and organize by photo album
                        $file = new TidypicsImage();
                        $file->container_guid = $album->guid;
                        $file->setMimeType($mime);
                        $file->simpletype = "image";
                        $file->access_id = $access;
                        //$file->title = substr($name, 0, strrpos($name, '.'));

                        $result = $file->save();

                        if ($result) {
                             $file->setOriginalFilename($name);
                             $file->saveImageFile($sent_file['tmp_name'], $sent_file['size']);
                             $file->extractExifData();
                             $file->saveThumbnails($image_lib);

                             array_push($uploaded_images, $file->guid);

                             // plugins can register to be told when a new image has been uploaded
                             trigger_elgg_event('upload', 'tp_image', $file);

                                // successful upload so check if this is a new album and throw river event/notification if so
                             if ($album->new_album == TP_NEW_ALBUM) {
                                    $album->new_album = TP_OLD_ALBUM;

                                    // we throw the notification manually here so users are not told about the new album until there
                                    // is at least a few photos in it
                                    object_notifications('create', 'object', $album);

                                    add_to_river('river/object/album/create', 'create', $album->owner_guid, $album->guid);
                             }

                             if ($img_river_view == "all") {
                                        add_to_river('river/object/image/create', 'create', $file->getObjectOwnerGUID(), $file->getGUID());
                             }
                             
                             unset($file);
                             
                        }
                        
                        
                        

                    }//end of for loop
                    
                    if (count($uploaded_images)) {
                            // Create a new batch object to contain these photos
                            $batch = new ElggObject();
                            $batch->subtype = "tidypics_batch";
                            $batch->access_id = $access;
                            $batch->container_guid = $album->guid;

                            if ($batch->save()) {
                                    foreach ($uploaded_images as $uploaded_guid) {
                                            add_entity_relationship($uploaded_guid, "belongs_to_batch", $batch->getGUID());
                                    }
                                    if ($img_river_view == "batch" && $new_album == false) {
                                            add_to_river('river/object/tidypics_batch/create', 'create', $batch->getObjectOwnerGUID(), $batch->getGUID());
                                    }
                            }
                    }


                    if (count($uploaded_images) > 0) {
                            $album->prependImageList($uploaded_images);
                    }
                    
                    echo '{"status":1,"message": "Se publicaron correctamente las fotos en el album"}'; 
                    exit;
                }
                else{
                    echo '{"error":1,"message": "No se encontraron imagenes para publicar" }';
                }
                
            }
            else{
                echo '{"error":1,"message": "No se encontro el álbum de fotos" }';
            }
}





?>
