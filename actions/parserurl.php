<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/../../engine/start.php');

global $CONFIG; 



$url = urldecode(get_input("url"));

//echo $info->getUrlResponse;
//echo $url;
$url = checkValues($url);

$return_array = array();
     
$base_url = substr($url,0, strpos($url, "/",8));
$relative_url = substr($url,0, strrpos($url, "/")+1);

// Get Data
$cc = new cURL();
$string = $cc->get($url);
$string = str_replace(array("\n","\r","\t",'</span>','</div>'), '', $string);

$string = preg_replace('/(<(div|span)\s[^>]+\s?>)/',  '', $string);
if (mb_detect_encoding($string, "UTF-8") != "UTF-8") 
	$string = utf8_encode($string);


// Parse Title
$nodes = extract_tags( $string, 'title' );
$return_array['title'] = trim($nodes[0]['contents']);

// Parse Base
$base_override = false; 
$base_regex = '/<base[^>]*'.'href=[\"|\'](.*)[\"|\']/Ui';
preg_match_all($base_regex, $string, $base_match, PREG_PATTERN_ORDER);
if(strlen($base_match[1][0]) > 0)
{
	$base_url = $base_match[1][0];
	$base_override = true; 
}

// Parse Description
$text = '';
$return_array['description'] = '';
$nodes = extract_tags( $string, 'meta' );
foreach($nodes as $node)
{
	if (strtolower($node['attributes']['name']) == 'description')
		$text = trim($node['attributes']['content']);
}
$return_array['description'] = substr($text,0,170).'...';
$images_array=array();
// Parse Images
//$images_array = extract_tags( $string, 'img' );
$images = array();

$return_array['videoid'] = '';

if (preg_match('~^http://(?:www\.)?vimeo\.com/(?:clip:)?(\d+)~', $url, $matches)) {
    $vimeoid = $matches[1];
    
    $xml = simplexml_load_file("http://vimeo.com/api/v2/video/".$vimeoid.".xml");
    $xml = $xml->video; 
    $xml_pic = $xml->thumbnail_small.' ';
    //error_log('****VIMEO ID: '.$xml_pic);
    $return_array['type'] = 'vimeo';
    $return_array['videoid'] = $vimeoid;
    $images[] = array("img" => $xml_pic, "width" => 200, "height" => 90, 'area' =>  (120 * 90),'offset' => (120 * 90));
    
}
else{
    for ($i=0;$i<=sizeof($images_array);$i++)
    {
            $img = trim(@$images_array[$i]['attributes']['src']);
            $width = preg_replace("/[^0-9.]/", '', $images_array[$i]['attributes']['width']);
            $height = preg_replace("/[^0-9.]/", '', $images_array[$i]['attributes']['height']);

            $ext = trim(pathinfo($img, PATHINFO_EXTENSION));

            if($img && $ext != 'gif') 
            {
                    if (substr($img,0,7) == 'http://')
                            ;
                    else	if (substr($img,0,1) == '/' || $base_override)
                            $img = $base_url . $img;
                    else 
                            $img = $relative_url . $img;

                    if ($width == '' && $height == '')
                    {
                            $details = @getimagesize($img);

                            if(is_array($details))
                            {
                                    list($width, $height, $type, $attr) = $details;
                            } 
                    }
                    $width = intval($width);
                    $height = intval($height);


                    if ($width > 199 || $height > 199 )
                    {
                            if (
                                    (($width > 0 && $height > 0 && (($width / $height) < 3) && (($width / $height) > .2)) 
                                            || ($width > 0 && $height == 0 && $width < 700) 
                                            || ($width == 0 && $height > 0 && $height < 700)
                                    ) 
                                    && strpos($img, 'logo') === false )
                            {
                                    $return_array['type'] = 'web';
                                    $images[] = array("img" => $img, "width" => $width, "height" => $height, 'area' =>  ($width * $height),'offset' => $images_array[$i]['offset']);
                                    $i=sizeof($images_array);
                            }
                    }

            }
    }

}
if(count($images)==0){
    //Opcion para obtener el thumbnail de youtube
    if (preg_match('/(http:\/\/)([a-zA-Z]{2,3}\.)(youtube\.com\/)(.*)/', $url, $matches)) {
        $domain = $matches[2] . $matches[3];
        $path = $matches[4];

        if (preg_match('/^(watch\?v=)([a-zA-Z0-9_-]*)(&.*)?$/',$path, $matches)) {
            $image_youtube = "http://img.youtube.com/vi/".$matches[2]."/default.jpg";
            //echo $image_youtube;
            $return_array['type'] = 'youtube';
            $return_array['videoid'] = $matches[2];
            $images[] = array("img" => $image_youtube, "width" => 200, "height" => 90, 'area' =>  (120 * 90),'offset' => (120 * 90));
        }    
    }
    //Si el link es una imagen como tal, obtener la minuatura
    if (preg_match('/(http:\/\/)(.*)(\/.*)(\.(jpg|png|JPG|PNG|gif))/', $url, $matches)) {
        $images[] = array("img" => $url, "width" => 140, "height" => 90, 'area' =>  (140 * 140),'offset' => (140 * 140));
        $return_array['type'] = 'imagen';
        if($return_array['title']=='')
            $return_array['title']= str_replace ('-', ' ', str_replace ('/', '', $matches[3]));
    }
}

if($return_array['type']=='')
    $return_array['type'] = 'web';

//if($return_array['videoid']=='')
    

$return_array['images'] = array_values(($images));
$return_array['total_images'] = count($return_array['images']); 

header('Cache-Control: no-cache, must-revalidate');
//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

echo json_encode($return_array);
exit;


/**
 * FUNCTIONS
 * Feel Free to put these in another file
 */


function sort_array_by_field($original,$field,$descending = false)
{
	$sortArr = array();
	
	foreach ( $original as $key => $value )
	{
		$sortArr[ $key ] = $value[ $field ];
	}

	if ( $descending )
	{
		arsort( $sortArr );
	}
	else
	{
		asort( $sortArr );
	}
	
	$resultArr = array();
	foreach ( $sortArr as $key => $value )
	{
		$resultArr[ $key ] = $original[ $key ];
	}

	return $resultArr;
}  


function checkValues($value)
{
	$value = trim($value);
	if (get_magic_quotes_gpc())
	{
		$value = stripslashes($value);
	}
	$value = strtr($value, array_flip(get_html_translation_table(HTML_ENTITIES)));
	$value = strip_tags($value);
	$value = htmlspecialchars($value);
	return $value;
}


function extract_tags( $html, $tag, $selfclosing = null, $return_the_entire_tag = false, $charset = 'ISO-8859-1' ){
 
	if ( is_array($tag) ){
		$tag = implode('|', $tag);
	}
 
	//If the user didn't specify if $tag is a self-closing tag we try to auto-detect it
	//by checking against a list of known self-closing tags.
	$selfclosing_tags = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta', 'col', 'param' );
	if ( is_null($selfclosing) ){
		$selfclosing = in_array( $tag, $selfclosing_tags );
	}
 
	//The regexp is different for normal and self-closing tags because I can't figure out 
	//how to make a sufficiently robust unified one.
	if ( $selfclosing ){
		$tag_pattern = 
			'@<(?P<tag>'.$tag.')			# <tag
			(?P<attributes>\s[^>]+)?		# attributes, if any
			\s*/?>					# /> or just >, being lenient here 
			@xsi';
	} else {
		$tag_pattern = 
			'@<(?P<tag>'.$tag.')			# <tag
			(?P<attributes>\s[^>]+)?		# attributes, if any
			\s*>					# >
			(?P<contents>.*?)			# tag contents
			</(?P=tag)>				# the closing </tag>
			@xsi';
	}
 
	$attribute_pattern = 
		'@
		(?P<name>\w+)							# attribute name
		\s*=\s*
		(
			(?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)	# a quoted value
			|							# or
			(?P<value_unquoted>[^\s"\']+?)(?:\s+|$)			# an unquoted value (terminated by whitespace or EOF) 
		)
		@xsi';
 
	//Find all tags 
	if ( !preg_match_all($tag_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ){
		//Return an empty array if we didn't find anything
		return array();
	}
 
	$tags = array();
	foreach ($matches as $match){
 
		//Parse tag attributes, if any
		$attributes = array();
		if ( !empty($match['attributes'][0]) ){ 
 
			if ( preg_match_all( $attribute_pattern, $match['attributes'][0], $attribute_data, PREG_SET_ORDER ) ){
				//Turn the attribute data into a name->value array
				foreach($attribute_data as $attr){
					if( !empty($attr['value_quoted']) ){
						$value = $attr['value_quoted'];
					} else if( !empty($attr['value_unquoted']) ){
						$value = $attr['value_unquoted'];
					} else {
						$value = '';
					}
 
					//Passing the value through html_entity_decode is handy when you want
					//to extract link URLs or something like that. You might want to remove
					//or modify this call if it doesn't fit your situation.
					$value = html_entity_decode( $value, ENT_QUOTES, $charset );
 
					$attributes[$attr['name']] = $value;
				}
			}
 
		}
 
		$tag = array(
			'tag_name' => $match['tag'][0],
			'offset' => $match[0][1], 
			'contents' => !empty($match['contents'])?$match['contents'][0]:'', //empty for self-closing tags
			'attributes' => $attributes, 
		);
		if ( $return_the_entire_tag ){
			$tag['full_tag'] = $match[0][0]; 			
		}
 
		$tags[] = $tag;
	}
 
	return $tags;
}




class cURL
{

	var $headers;

	var $user_agent;

	var $compression;

	var $cookie_file;

	var $proxy;

	function cURL($cookies = TRUE, $cookie = '/tmp/cookies.txt', $compression = 'gzip', $proxy = '')
	{
		$this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
		$this->headers[] = 'Connection: Keep-Alive';
		$this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
		$this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
		$this->compression = $compression;
		$this->proxy = $proxy;
		$this->cookies = $cookies;
		if ($this->cookies == TRUE)
			$this->cookie($cookie);
	}

	function cookie($cookie_file)
	{
		if (file_exists($cookie_file))
		{
			$this->cookie_file = $cookie_file;
		}
		else
		{
			fopen($cookie_file, 'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
			$this->cookie_file = $cookie_file;
			fclose($this->cookie_file);
		}
	}

	function get($url)
	{
		$url = str_replace("&amp;", '&', $url);

		$process = curl_init($url);
		curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
		if ($this->cookies == TRUE)
			curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
		if ($this->cookies == TRUE)
			curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
		curl_setopt($process, CURLOPT_ENCODING, $this->compression);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_HTTPGET, true);
		
		
		if ($this->proxy)
			curl_setopt($process, CURLOPT_PROXY, $this->proxy);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		$return = curl_exec($process);
		curl_close($process);
		return $return;
	}

	function post($url, $data)
	{
		$process = curl_init($url);
		curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($process, CURLOPT_HEADER, 1);
		curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
		if ($this->cookies == TRUE)
			curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
		if ($this->cookies == TRUE)
			curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
		curl_setopt($process, CURLOPT_ENCODING, $this->compression);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		if ($this->proxy)
			curl_setopt($process, CURLOPT_PROXY, $this->proxy);
		curl_setopt($process, CURLOPT_POSTFIELDS, $data);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($process, CURLOPT_POST, 1);
		$return = curl_exec($process);
		curl_close($process);
		return $return;
	}

	function error($error)
	{
		die;
	}
}

?>
