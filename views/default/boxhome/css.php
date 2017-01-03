<?php
/**
 * Custom Index CSS
 *
 */
?>

/*******************************
	Custom Index
********************************/
/*<style type="text/css" media="screen">*/
/*Shared plus*/

.elgg-river-layout .elgg-heading-main{
  display: none
}


#boxhome {
    background: #FFFFFF;
    margin: 0px auto;
    padding: 20px;
    text-align: left;
    border: 1px solid #dcdcdc;
    border-top: 0px;
    border-bottom: 0px;
}

#boxhome form{
  width: 97%;
}

#boxhome textarea, #boxhome input{
  margin: 10px 0;
  width: 100%;
}

.boxhome_info{
  color: #666;
  float: right;
  font-size: 80%;
  margin-top: 10px;
  text-align: left;
  width: 50%;
}

#boxhome_save{
  max-width: 100px;
}


#parser_urls {
	margin:0;
}

#enviar {
    border: 1px solid #E8E8E8;
    background: #F4F4F4;
    text-align: right;
    padding: 0 5px;
}

#fetched_data {
	height:125px;
	margin-bottom:10px;
}

.boxhome_tool_bar{
    float: left;
    height: 24px;
    padding: 5px;
    width: 100%;
}


.boxhome_tools{
    float: right;
    height: 24px;
    padding: 3px;
}

.boxhome_tools a{
    cursor:pointer;
}

.boxhome_msj_tools{
    float: left;
    margin-top: 10px;
    width: 120px;
    font-weight: normal;
}

.close_tool{
    margin-top: 8px;
    width:16px;
    height: 16px;
    float: right;
    background:url('<?php echo elgg_get_site_url(); ?>mod/boxhome/graphics/close.png') no-repeat center center;
    cursor:pointer;
}

#boxhome select{
  background: url("<?php echo elgg_get_site_url();?>mod/boxhome/graphics/config.png") no-repeat scroll right center #fff;
  border: 0 solid #dcdcdc;
  font-size: 90%;
  margin: 15px 0;
  overflow: hidden;
  padding: 5px 30px 0 0;
  text-align: right;
  width: 170px;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/*Imagen upload*/

#upload_imagen input{
    max-width: 60%;
}

#upload_file input{
    max-width: 60%;
}

.upload_media{
    border: 1px solid #CCCCCC;
    padding: 10px;
    display: none;
    
}

.upload_media form{
    text-aling:left;
    color: #666666;
}

.form_table{
    max-width: 450px;
    min-width:450px
}

.form_table tr td.label{
    min-width: 130px;
    max-width: 130px;
    padding: 10px;    
}

.upload_media input, .upload_media select{
    border: 1px solid #E8E8E8;
    color: #666666;
    max-width: 60%;
    min-width: 60%;
    padding: 0px;
}

.preview
{
    height: 70px;
    width: 72px;
    border: solid 1px #dedede;
    margin: 10px;
    position: relative;
    overflow: hidden; 
    float: left;
}

.preview img
{
   height:72px;
}

.preview a
{
   position: absolute; 
   left: 1px; 
   top: 1px;
   color: #000;
   font-size: 8pt;
   background: #FFF;
   padding: 2px;
    -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";
    filter: alpha(opacity=70);
    -moz-opacity:0.7;
    -khtml-opacity: 0.7;
    opacity: 0.7;   
}

.preview a img
{
   height:24px;
}

#preview_image
{
    color:#cc0000;
    font-size:12px
}

/*WebCamara*/

#camera{
    border: 1px solid #CCCCCC;
    padding: 10px;
    display: none;
}

#screen{
    float: left;
    width: 320px;
}

#buttons_camera{
    float: right;
    width: 120px;
    margin-top: 30px;
}

.buttons_action{
    padding: 3px;
    margin: 5px;
    width: 50px;
}


#photos{
    background: none repeat scroll 0 0 #DDDDDD;
    float: left;
    margin-top: 10px;
    width: 100%;
}

/*Parser URL*/

#atc_bar{width:100%;}
#attach_content{}
#atc_images {width:100px;height:100px;overflow:hidden;float:left;margin-top: 15px;}
#atc_info {width:360px;float:left;height:100px;text-align:left; padding:10px 0 10px 10px;}
#atc_title {font-size:12px;display:block;font-weight: bold}
#atc_url {font-size:9px;display:block;color: #888}
#atc_desc {font-size:11px;}
#atc_total_image_nav{float:left;padding-left:20px}
#atc_total_images_info{float:left;padding:4px 10px;font-size:10px;}
  
.cboxIframe{
    overflow-x: hidden; 
}
 
/*TopBar and header*/

#layout_header{
    
    height: 61px;
    margin-top: 45px;
}


/**tooltip tipsy**/

.tipsy { font-size: 10px; position: absolute; padding: 5px; z-index: 100000; }
  .tipsy-inner { background-color: #000; color: #FFF; max-width: 200px; padding: 5px 8px 4px 8px; text-align: center; }

  /* Rounded corners */
  .tipsy-inner { border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px; }
  
  /* Uncomment for shadow */
  /*.tipsy-inner { box-shadow: 0 0 5px #000000; -webkit-box-shadow: 0 0 5px #000000; -moz-box-shadow: 0 0 5px #000000; }*/
  
  .tipsy-arrow { position: absolute; width: 0; height: 0; line-height: 0; border: 5px dashed #000; }
  
  /* Rules to colour arrows */
  .tipsy-arrow-n { border-bottom-color: #000; }
  .tipsy-arrow-s { border-top-color: #000; }
  .tipsy-arrow-e { border-left-color: #000; }
  .tipsy-arrow-w { border-right-color: #000; }
  
    .tipsy-n .tipsy-arrow { top: 0px; left: 50%; margin-left: -5px; border-bottom-style: solid; border-top: none; border-left-color: transparent; border-right-color: transparent; }
    .tipsy-nw .tipsy-arrow { top: 0; left: 10px; border-bottom-style: solid; border-top: none; border-left-color: transparent; border-right-color: transparent;}
    .tipsy-ne .tipsy-arrow { top: 0; right: 10px; border-bottom-style: solid; border-top: none;  border-left-color: transparent; border-right-color: transparent;}
  .tipsy-s .tipsy-arrow { bottom: 0; left: 50%; margin-left: -5px; border-top-style: solid; border-bottom: none;  border-left-color: transparent; border-right-color: transparent; }
    .tipsy-sw .tipsy-arrow { bottom: 0; left: 10px; border-top-style: solid; border-bottom: none;  border-left-color: transparent; border-right-color: transparent; }
    .tipsy-se .tipsy-arrow { bottom: 0; right: 10px; border-top-style: solid; border-bottom: none; border-left-color: transparent; border-right-color: transparent; }
  .tipsy-e .tipsy-arrow { right: 0; top: 50%; margin-top: -5px; border-left-style: solid; border-right: none; border-top-color: transparent; border-bottom-color: transparent; }
  .tipsy-w .tipsy-arrow { left: 0; top: 50%; margin-top: -5px; border-right-style: solid; border-left: none; border-top-color: transparent; border-bottom-color: transparent; }