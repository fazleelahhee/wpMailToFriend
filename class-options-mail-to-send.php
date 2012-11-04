<?php
class MtfOption{
    private $options;
    private $options_name = 'mail_to_friend_option';
    public function __construct() {
        $this->options = unserialize(get_option( $this->options_name ));
        if(!$this->options) {
            $this->options = array();
        }
        
    }
    
    public function save($obj) {
        $new_value = serialize($obj);

        if ( get_option( $this->options_name) != $new_value ) {
            update_option( $this->options_name, $new_value );
        } else {
            $deprecated = ' ';
            $autoload = 'no';
            add_option( $this->options_name, $new_value, $deprecated, $autoload );
        }
    }
    
    public function getProperty($key){
        if($key == 'captcha_enabled' && class_exists ('ReallySimpleCaptcha') == false) {
            return '';
        }
        if(array_key_exists($key, $this->options)) {
            return html_entity_decode(stripcslashes($this->options[$key]));
        }

        return '';
    }
}