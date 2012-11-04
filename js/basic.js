jQuery(function ($) {
        if(mtf_form_in_modal == '1') {  
            $( "#basic-modal-content" ).dialog({
                    modal: true,
                    autoOpen: false,
                    //hide: {effect: "fadeOut", duration: 5000},
                    close: function(ev, ui) 
                    { 
                        $('.mail_f_fail').css("display",'none');
                        $('.mail_f_success').css("display",'none');
                    }
            });
            $('#basic-modal .basic').click(function (e) {
                    $( "#basic-modal-content" ).dialog( "open" );   
                    return false;
            });
        }
        $('#sendtofriendform').submit(function() {
            $.post(
                $(this).attr('action'), 
                $(this).serialize(),
                function(data) {
                   
                    if(data.response == 'fail') {
                        $('.mail_f_success').css("display",'none');
                        $('.mail_f_fail').css("display",'block');
                        if(mtf_captcha_enabled == '1') {
                            $("#txt_captcha").val('');
                            $(".captcha_image_container").html(data.img);
                            $("#mail_to_friend").val(data.prefix);
                        }
                        $('.mail_f_fail').html(data.error);
                        
                    }
                    else if (data.response == 'success') {
                        $('.mail_f_fail').css("display",'none');
                        $('.mail_f_success').css("display",'block');
                        if(mtf_captcha_enabled == '1') {
                            $("#txt_captcha").val('');
                            $(".captcha_image_container").html(data.img);
                            $("#mail_to_friend").val(data.prefix);
                        }
                        $(".gtextbox").val('');
                        $("#txt_friendmessage").val('');
                        //$( "#basic-modal-content" ).dialog( "close" );  
                    }
                    else{
                        if(mtf_captcha_enabled == '1') {
                            $("#txt_captcha").val('');
                            $(".captcha_image_container").html(data.img);
                            $("#mail_to_friend").val(data.prefix);
                        }
                    }
                }
            );
    return false;
    }); 
        
});