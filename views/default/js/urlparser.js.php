/* 
 * Listener para identificar cuando el usuario inserta una url y la parsea para obtener titulo, imagen y descripcion de esta.
 */

$(document).ready(function () {	
        $('.fetched_data').hide(); // Hide div id by default
        $('.video_icon').hide();
        $('.link_icon').hide();
        $('.camera_icon_g').hide();
        $('.image_icon_g').hide();
        $('.file_icon_g').hide();
        $('#ajax_flag').val(0); // Initialize value to zero i.e  input tag with id='ajax_flag' will have a new attribute 'value=0'
        $("#wall").keyup(function() {// Listen to keyboard press event by user 
                var content = $(this).val(); // Get all the data in the textarea 
                var urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
                // Filtering URL from the content using regular expressions
                var url= content.match(urlRegex); 
                
                // regular expression that will allow us to extract url from the textarea
                if(url != null){
                    if (url.length > 0 && $('#ajax_flag').val() == 0) { // If there's atleast one url entered in the textarea
                        $('#parser_urls').show();
                        $('#atc_loading').show();
                        parse_link(url);
                        $('#ajax_flag').val(1); // Ensure that only once ajax will trigger if a url match is found in the textarea
                    }
                    if (url.length == 0 && $('#ajax_flag').val() == 1){
                        $('#ajax_flag').val(0);
                    }
                }
                else{
                    if ($('#ajax_flag').val() == 1){
                        $('#ajax_flag').val(0);
                        $('.video_icon').hide();
                        $('.link_icon').hide();
                        $('.camera_icon').show();
                        $('.camera_icon_g').hide();
                        $('.file_icon').show();
                        $('.file_icon_g').hide();
                        $('.image_icon').show();
                        $('.image_icon_g').hide();
                        $('#parser_urls').slideUp('slow');
                        $('#atc_images').html('');
                        $('#atc_title').html('');
                        $('#atc_desc').html('');
                        $('#atc_images').show();
                        $('#atc_url').html(''); 
                    }
                }
                return false;
        });
        
function parse_link(url)
{
        var url = String(url);
        if(!isValidURL(url))
        {
                alert('Please enter a valid url.');
                return false;
        }
        else
        {
                $('#parser_urls').slideDown('slow'); 
                             
                $.post("<?php echo elgg_get_site_url(); ?>mod/boxhome/actions/parserurl.php?url="+escape(url),{}, function(response){
                        if(response.type == 'youtube' || response.type == 'vimeo')
                            $('.video_icon').show()
                        else
                            $('.link_icon').show()
                        
                        $('.camera_icon').hide();
                        $('.camera_icon_g').show();
                        $('.file_icon').hide();
                        $('.file_icon_g').show();
                        $('.image_icon').hide();
                        $('.image_icon_g').show();
                           
                        //Set Content                        
                        $('#atc_title').html(response.title);
                        $('#atc_url').html(url);   
                        $('#atc_desc').html(response.description);
                        
                        //Set content in forms input hidden
                        $('#share_url_type').val(response.type);
                        $('#share_url_videoid').val(response.videoid);
                        $('#share_url_title').val(response.title);
                        $('#share_url_dir').val(url);   
                        $('#share_url_desc').val(response.description);
                        
                        if(response.total_images>0){
                           //alert('url: '+url);
                            $('#atc_images').html(' ');
                            $.each(response.images, function (a, b)
                            {
                                    $('#atc_images').append('<img src="'+b.img+'" width="100" id="'+(a+1)+'">');
                                    $('#share_url_image').val(b.img);
                                    return false; 
                            });
                            
                        }
                        else{
                            $('#atc_images').hide();
                            $('#atc_total_images').html('');
                            $('#next').hide();
                            $('#prev').hide();                                
                        }
                        

                        //Flip Viewable Content 
                        $('#attach_content').fadeIn('slow');
                        $('#atc_loading').hide();
                        
                        //Show first image
                        $('img#1').fadeIn();
                        $('#cur_image').val(1);
                        $('#cur_image_num').html(1);

                        
                });
        }
};	

});

function isValidURL(url)
{
        var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

        if(RegExp.test(url)){
                return true;
        }else{
                return false;
        }
}
