<?php

/*
 Mail to friend wordpress plugins
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

$mo = new MtfOption();
?>
<script>
jQuery(document).ready(function ($){     
    $("#mail_to_friend_settings_form").submit(function(e) {
    e.preventDefault();  
    tinyMCE.triggerSave(true,true); 
    $.post(
        $(this).attr('action'), 
        $(this).serialize(),
        function(data) {
            if(data.response == 'fail'){
                $(".response_message").html(data.from_email); 
            }
            else {
                $(".response_message").html('Successfully saved'); 
            }
        });
    });  
    
});
</script>
<h2>Mail to friend setting page</h2>
<div class="response_message"></div>
<form name="mail_to_friend_settings_form" id="mail_to_friend_settings_form" action="<?php echo site_url();?>" method="post">
    <table>
        <tr> <td><strong>Title</strong></td><td>&nbsp;<input name="form_title" type="textbox" value="<?php echo $mo->getProperty('form_title'); ?>" style="width:300px;"/></td></tr>
        <tr> <td><strong>From Email</strong></td><td>&nbsp;<input name="from_email" type="textbox" value="<?php echo $mo->getProperty('from_email'); ?>" style="width:300px;"/></td></tr>
        <tr> <td><strong>From Name</strong></td><td>&nbsp;<input name="from_name" type="textbox" value="<?php echo $mo->getProperty('from_name'); ?>" style="width:300px;"/></td></tr>
        <tr> <td><strong>Email Subject</strong> </td><td>&nbsp;</td></tr>
        <tr> <td> &nbsp; </td><td>   <textarea name="mail_subject" cols="70" rows="2"><?php echo $mo->getProperty('mail_subject'); ?></textarea> </td></tr>

        <tr> <td><strong>Email Messages</strong> </td><td>&nbsp;</td></tr>
        <tr> <td> &nbsp; </td><td>     <?php the_editor($mo->getProperty('email_message'),'email_message')?></td></tr>
        <tr> <td><strong>Email body in HTML</strong></td><td>&nbsp;<input name="html_content" type="checkbox" value="1" <?php echo $mo->getProperty('html_content') == '1'? "checked='checked'": ''; ?> /></td></tr>
        <tr> <td> <strong>Form on click popup</strong></td><td>&nbsp;<input name="modal_form" type="checkbox" value="1" <?php echo $mo->getProperty('modal_form') == '1'? "checked='checked'": ''; ?> /></td></tr>
        <tr> <td> <strong>Captcha</strong></td><td>
                <?php if(class_exists ('ReallySimpleCaptcha')) :?>
                &nbsp;<input name="captcha_enabled" type="checkbox" value="1" <?php echo $mo->getProperty('captcha_enabled') == '1'? "checked='checked'": ''; ?> />
                <?php else: ?>
                Note: To use CAPTCHA, you need Really Simple CAPTCHA plugin installed. <br />
                http://wordpress.org/extend/plugins/really-simple-captcha/
                <?php endif; ?>
            </td></tr>
        <input type="hidden" value="mail-to-friend-options" name="mail_to_friend_option" />
        <tr> <td> &nbsp; </td><td> <input name="submit" type="submit" value="Update" class="button-primary"/> </td></tr>
    </table>
</form>