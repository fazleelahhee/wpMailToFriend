<?php

/*
 * Plugin Name: Mail to freind
 * Author: Fazle Elahee
 * Description: This is a simple mail to friend wordpress plugns
 * version: 1.0
 */

if(!defined('ABSPATH')) {
    header("Location: /");
    exit;
}

include_once 'mail-to-send.php';
include_once 'class-options-mail-to-send.php';
// create custom plugin settings menu

function mail_to_friend_activate() {
$mo = new MtfOption();
$current_user = wp_get_current_user();
$mo->save(
        array(
                'form_title'=>'Email to friend',
                'from_email'=>$current_user->user_email,
                'from_name'=>$current_user->display_name,
                'mail_subject'=>'{sender-name} has recomanded to visit.',
                'email_message'=>'<p>Hi,</p>
<p>{sender-name} saw  this on the {site-url} website and thought you would be interested.</p>

<p>You can visit: {current-site-link}</p>
<p>{sender-message-to-friend}</p>
<p>Thanks</p>
<p>{site-url}</p>',
                 'modal_form'=>1,
                 'html_content'=>1
            )
        );
}

register_activation_hook( __FILE__, 'mail_to_friend_activate' );

add_action('admin_menu', 'mail_to_freind_create_menu');

function mail_to_freind_create_menu() {
	//create new top-level menu
        add_options_page('Mail to freind settings',  'Mail to freind', 'manage_options', 'mail-to-freind', 'mail_to_freind_settings_page');
        add_action('admin_mail_friend', 'mail_to_friend_js_admin_scripts');
}

function mail_to_freind_settings_page() {
    	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
        
        echo '<div class="wrap">';
        include_once 'options-page.php';
        echo '</div>';
}
function mail_to_friend_js_admin_scripts() {
        wp_enqueue_script( 'jquery');
}
function mail_to_friend_modal_css() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'custom_jquery_ui_1_9_1', 
            plugins_url( '/css/ui-lightness/jquery-ui-1.9.1.custom.min.css' , __FILE__ ) );
    wp_enqueue_style( 'custom_jquery_ui_1_9_1' );
}
add_action( 'wp_enqueue_scripts', 'mail_to_friend_modal_css' );

function mail_to_friend_modal_js_jquery() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('jquery-ui-dialog');
}
add_action( 'wp_enqueue_scripts', 'mail_to_friend_modal_js_jquery' );

function mail_to_friend_modal_js() {
?>
<script defer='defer' src="<?php echo plugins_url( '/js/basic.js' , __FILE__ ); ?>"></script>
<?php
}
add_action('wp_footer','mail_to_friend_modal_js');

function mail_ro_friend_with_modal() {
    global $post;
    $mo = new MtfOption();
    ?>
                <style>
                    .mail_f_fail {color: red;}
                    .mail_f_success {color: green;}
                </style>
                <script>
                    var mtf_captcha_enabled ='<?php echo $mo->getProperty('captcha_enabled'); ?>';
                    var mtf_form_in_modal = '<?php echo $mo->getProperty('modal_form'); ?>';
                </script>
                <?php if($mo->getProperty('modal_form') == '1'): ?>
                <div id='basic-modal'>
			<a href='#' class='basic'><?php echo $mo->getProperty('form_title'); ?></a>
		</div>
		<?php endif ?>
		<!-- modal content -->
		<div id="basic-modal-content" title="Email to a friend">
                            <div id="send__to_form">
                               <?php if($mo->getProperty('modal_form') != '1'): ?>
                                    <h3 class="mtf-form-title"><?php echo $mo->getProperty('form_title'); ?></h3>
                               <?php endif; ?>  
                                <form action="<?php echo site_url(); ?>" method="post" name="sendtofriendform" id="sendtofriendform">
                                   
                                    <div style='padding-top:4px;'><span id="send-link-to-friend-result"></span></div>
                                    <div class="gtitle" style='padding-top:4px;'>Your Name</div>
                                    <div><input name="txt_yourname" class="gtextbox" type="text" id="txt_yourname" maxlength="120" /></div>
                                    
                                    <div class="gtitle" style='padding-top:4px;'>Friend email</div>
                                    <div><input name="txt_friendemail" class="gtextbox" type="text" id="txt_friendemail" maxlength="120" /></div>
                                    <div class="gtitle" style='padding-top:4px;'>Enter your message</div>
                                    <div><textarea name="txt_friendmessage" class="gtextarea" rows="3" id="txt_friendmessage"></textarea></div>
                                    <?php
                                    $captcha_data = array('prefix'=>'no captcha'); 
                                    if($mo->getProperty('captcha_enabled') == '1'):
                                    $captcha = new SendMailToFriend();
                                    $captcha_data = $captcha->generate_captcha(); 
                                    ?>
                                    <div class="gtitle" style='padding-top:4px;'> Enter below security code</div>
                                    
                                    <div style='padding-top:4px;' class="captcha_image_container"> <?php echo @$captcha_data['img']; ?></div>
                                    <div> <input name="txt_captcha" class="gtextbox" type="text" id="txt_captcha" maxlength="6" /></div>
                                    <?php endif ?>
                                    <div style="padding-top:4px;" >
                                    <input type="submit" value="Send" class="submit_mail_to_freind" /></div> 
                                    <input name="mail_to_friend" id="mail_to_friend" type="hidden" value="<?php echo @$captcha_data['prefix']; ?>" />
                                    
                                    <input type="hidden" name="sendlink" id="sendlink" value="<?php echo get_permalink($post->ID); ?> "/>
                                </form>
                                 <div class="mail_f_success hide_me_close" style="display:none">Message has been sent successfully.</div>
                                 <div class="mail_f_fail hide_me_close" style="display:none"></div>
                            </div>
		</div>
    <?php
}

add_shortcode('mail_to_freind', 'mail_ro_friend_with_modal');

add_action('init', 'mail_to_friend_init');

function mail_to_friend_init(){
    if(isset($_POST['mail_to_friend']) && $_POST['mail_to_friend'] != '') {
        $captcha = new SendMailToFriend();  
        $captcha->setProperty('to_mail', $_POST['txt_friendemail']);
        $captcha->setProperty('sender_msg', $_POST['txt_friendmessage']);
        $captcha->setProperty('sender_name', $_POST['txt_yourname']);

        $captcha->setProperty('send_link', $_POST['sendlink']);
        $mo = new MtfOption();
        if($mo->getProperty('captcha_enabled') == '1'):
            $captcha->setProperty('captcha_text', $_POST['txt_captcha']);
            $captcha->setProperty('captcha_prefix', $_POST['mail_to_friend']);    
            $captcha->validate_captcha();
        endif;
        $response = $captcha->process();
        $response = json_encode($response);
        header('Content-Type: application/json');
        echo $response;
        exit;

    }
}

add_action('init', 'save_option_mail_to_friend_init');

function save_option_mail_to_friend_init(){
    if(isset($_POST['mail_to_friend_option']) && $_POST['mail_to_friend_option'] == 'mail-to-friend-options'):
        if (!current_user_can('manage_options'))  {
                    echo 'You do not have sufficient permissions to access this page.';
            }
        else {

               $email = sanitize_email($_POST['from_email']);
               header('Content-Type: application/json');
               if(!is_email($email)) {
                   
                   echo json_encode(array('response'=>'fail','from_email'=>'Please enter correct email address!'));
                   exit;
               
               }
               
               $mo = new MtfOption();
               $mo->save($_POST);
               echo json_encode(array('response'=>'success'));
        }
    
        exit;
    endif;
}