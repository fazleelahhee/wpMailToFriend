<?php
/*
 *  Mail to friend wordpress plugins
    Copyright (C) 2012  Fazle Elahee

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
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