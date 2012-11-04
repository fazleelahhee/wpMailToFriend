<?php

class SendMailToFriend {
    private $to_mail ='';
    private $from_mail = 'fe@bondmedia.co.uk';
    private $sender_msg = '';
    private $sender_name = '';
    private $captcha_text = '';
    private $send_link = '';
    private $captcha_prefix = '';
    private $captca_tmp_dir = ''; 
    private $captca_img_url = '';
    private $error = array();
    private $captcha_instance = null;
    
    
    public function __construct() {
        $upload_dir = wp_upload_dir();
        $path = $upload_dir['basedir'].DIRECTORY_SEPARATOR.'wpcf7_captcha';

        if(!is_dir($path)){
            mkdir($path);
        }
        if(class_exists ('ReallySimpleCaptcha')) :
            $this->captcha_instance = new ReallySimpleCaptcha();
            $this->captca_tmp_dir = $path;
            $this->captca_img_url = $upload_dir['baseurl'].DIRECTORY_SEPARATOR.'wpcf7_captcha'.DIRECTORY_SEPARATOR;
        endif;
    }
    private function setTo_mail($value){
        $email = sanitize_email($value);
        if(is_email($email)) {
            $this->to_mail = $value;
        }
        else{
            $this->error['email'] = 'Invalid email address!'; 
        }
    }
    
    private function setSend_link($value) {
        $this->send_link = $url = esc_url($value);
    }
    private function setFrom_mail($value){
        $this->from_mail = $value;
    }
    private function setSender_msg($value){
        $allowed_html = array();
        $this->sender_msg = wp_kses(sanitize_text_field($value), $allowed_html);
    }
    private function setSender_name($value){
     $name = sanitize_text_field($value);
     
     if(trim($name) != '') {
      $this->sender_name = $value;   
     }
     else {
         $this->error['name'] = "Please enter name!";
     }
    }
    
    private function setCaptcha_text($value){
        $this->captcha_text = sanitize_text_field($value);
    }
    private function setCaptcha_prefix($value){
        $this->captcha_prefix = sanitize_text_field($value);
    }
    private function setCaptca_tmp_dir($value){
        $this->captca_tmp_dir($value);
    }
  
    public function setProperty($key, $value) {
        $func = "set".ucwords($key);
        $this->$func($value);
    }
    
    public function validate_captcha() {
        $this->captcha_instance->tmp_dir = $this->captca_tmp_dir.DIRECTORY_SEPARATOR;
        if(!$this->captcha_instance->check( $this->captcha_prefix, $this->captcha_text )) {
            $this->error['captcha'] = 'captcha does not matched';
        }
        
        $this->captcha_instance->remove( $this->captcha_prefix );
    }
    public function generate_captcha() {
        $word = $this->captcha_instance->generate_random_word();
        $this->captcha_instance->tmp_dir = $this->captca_tmp_dir.DIRECTORY_SEPARATOR;
        $prefix = mt_rand();
        $file_name = $this->captcha_instance->generate_image( $prefix, $word );
        $rtn_ARR = array('prefix'=>$prefix, 'img'=>"<img id='_captcha_image_' src='".$this->captca_img_url.$file_name."'>");
        return $rtn_ARR;
    }
    
    public function process() {
        $rtn = array();
        $mo = new MtfOption();
        $from_replace = array('{sender-name}','{site-url}','{current-site-link}', '{sender-message-to-friend}');
        $to_replace = array($this->sender_name,site_url(),$this->send_link,$this->sender_msg);
        
        if(count($this->error) == 0 ) {
            
            $headers = 'From: '.$mo->getProperty('from_name').' <'.$mo->getProperty('from_email').'>'. "\r\n";
            $subject = str_replace($from_replace,$to_replace , $mo->getProperty('mail_subject'));
            
            $message = str_replace($from_replace,$to_replace , $mo->getProperty('email_message'));
            if($mo->getProperty('html_content') == '1') {
                add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
            }
            wp_mail($this->to_mail,$subject,$message, $headers);
            //var_dump($message);
            $rtn =  array('response'=>'success');
        }
        else {
            ;
            $rtn = array('response'=>'fail');
           $rtn['error'] = implode("<br />",$this->error); 
        }
        if($mo->getProperty('captcha_enabled') == '1'):
            $captcha_ARR = $this->generate_captcha(); 
            $rtn['img'] = $captcha_ARR['img'];
            $rtn['prefix'] = $captcha_ARR['prefix'];
        endif;
        return $rtn;
    }
    
   
}// end of class