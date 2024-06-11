<?php
if (!defined('WPINC')) {
    die('Closed');
}?>
<div class="rmagic">
    <!--Dialogue Box Starts-->
    <div class="rmcontent">
        
        <div class="rmheader"><?php echo _e('Scheduled Email Report', 'registrationmagic-addon'); ?></div>
        
        <?php
        
        $notification_type = isset($data->notification->notification_type) ? $data->notification->notification_type : 'submissions';
        $sent_to = isset($data->notification->sent_to) && $data->notification->sent_to!= null ? $data->notification->sent_to : 'me';
        $email_body =  isset($data->notification->email_content) && $data->notification->email_content!= null ? $data->notification->email_content : RM_UI_Strings::get('REPORT_MAIL_BODY');
        $email_subject =  isset($data->notification->email_subject) && $data->notification->email_subject!= null ? $data->notification->email_subject : RM_UI_Strings::get('REPORT_MAIL_SUBJECT');
        
        $disable_form_id = '';
        $disable_login_status = '';
        $disable_payment_status = '';
        if($notification_type=='submissions'){
            $disable_form_id = '';
            $disable_login_status = ' rm_disabled_field';
            $disable_payment_status = ' rm_disabled_field';
        }elseif($notification_type=='logins'){
            $disable_form_id = ' rm_disabled_field';
            $disable_login_status = '';
            $disable_payment_status = ' rm_disabled_field';
        
        }elseif($notification_type=='payments'){
            $disable_form_id = '';
            $disable_login_status =' rm_disabled_field';
            $disable_payment_status = '';
        }
        $first_exe = '';
        if(isset($data->notification->first_exe)) { 
            $first_exe = str_replace(' ', '', RM_Utilities::localize_time($data->notification->first_exe, 'Y-m-d\T\ H:i', false, true));

        }
        $form = new RM_PFBC_Form("reports_notification");

        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        if(isset($data->notification->id)):
            $form->addElement(new Element_Hidden("id", $data->notification->id));
            $form->addElement(new Element_Hidden("last_exe", $data->notification->last_exe));
            $form->addElement(new Element_Hidden("enable", $data->notification->enable));
        endif;
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('REPORT_NOT_TITLE_LABEL') . "</b>", "notification_title", array("id" => "rm_notification_title", "required"=>1, "value" => isset($data->notification->notification_title) ? $data->notification->notification_title : '', "longDesc"=>RM_UI_Strings::get('REPORT_NOT_TITLE_HELP'))));
              
        $form->addElement(new Element_Radio("<b>".RM_UI_Strings::get('REPORT_TITLE_LABEL')."</b>", "notification_type", array('submissions'=>__('Form Submissions', 'registrationmagic-addon'),'logins'=>__('Logins', 'registrationmagic-addon'),'payments'=>__('Form Payments', 'registrationmagic-addon')), array("value" => $notification_type, "required"=>1, "longDesc"=>RM_UI_Strings::get('REPORT_TITLE_HELP'))));
            // Submissions Reports
          
            $form->addElement(new Element_HTML('<div id="rm_submissions_reports" class="childfieldsrow">'));
                $form->addElement(new Element_HTML('<div id="rm_form_field" class="'.$disable_form_id.'">'));
                    $form->addElement(new Element_Select("<b>" .RM_UI_Strings::get('REPORT_FORM_TITLE_LABEL'). "</b>", "form_id",$data->forms, array("value" => isset($data->notification->form_id) ? $data->notification->form_id : '', "class" => "rm_form_id ", "id" => "rm_form_id","longDesc" => RM_UI_Strings::get('REPORT_SENT_TO_HELP'))));
                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('<div id="rm_login_field" class="'.$disable_login_status.'">'));
                    $form->addElement(new Element_Select("<b>" .RM_UI_Strings::get('REPORT_LOGIN_STATUS_LABEL'). "</b>", "login_status",$data->login_status, array("value" => isset($data->notification->login_status) ? $data->notification->login_status : '', "class" => "rm_login_status".$disable_login_status,"id" => "rm_login_status", "longDesc" => RM_UI_Strings::get('REPORT_LOGIN_STATUS_HELP'))));
                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('<div id="rm_payment_field" class="'.$disable_payment_status.'">'));
                    $form->addElement(new Element_Select("<b>" .RM_UI_Strings::get('REPORT_PAYMENT_STATUS_LABEL'). "</b>", "payment_status",$data->payment_status, array("value" => isset($data->notification->payment_status) ? $data->notification->payment_status : '', "class" => "rm_payment_status ".$disable_payment_status,"id" => "rm_payment_status", "longDesc" => RM_UI_Strings::get('REPORT_PAYMENT_STATUS_HELP'))));
                $form->addElement(new Element_HTML('</div>'));
            $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_Radio("<b>".RM_UI_Strings::get('REPORT_SENT_TO_LABEL')."</b>", "sent_to", array('me'=>__('Only me', 'registrationmagic-addon'),'admins'=>__('All Admin Users', 'registrationmagic-addon'),'individual'=>__('Other Users', 'registrationmagic-addon')), array("value" => isset($sent_to) ? $sent_to : 'me', "longDesc"=>RM_UI_Strings::get('REPORT_INDIVIDUALS_EMAIL_LABEL'))));    
            if($sent_to == 'individual'):
            $form->addElement(new Element_HTML('<div id="rm_sent_reports" class="childfieldsrow">'));
            else:
                $form->addElement(new Element_HTML('<div id="rm_sent_reports" class="childfieldsrow" style="display:none;">'));
            endif;
                $form->addElement(new Element_Textarea("<b>".RM_UI_Strings::get('REPORT_INDIVIDUALS_EMAIL_LABEL')."</b>", "individual_receiver", array("id" => "rm_individual_receiver", "class" => "rm_individual_receiver", "value" => isset($data->notification->receivers) ? $data->notification->receivers : '', "longDesc"=>RM_UI_Strings::get('REPORT_INDIVIDUALS_EMAIL_HELP'))));
            $form->addElement(new Element_HTML('</div>'));
            $form->addElement(new Element_Select("<b>" .RM_UI_Strings::get('REPORT_CRON_TYPE_LABEL'). "</b>", "cron_type",$data->schedule, array("value" => isset($data->notification->cron_type) ? $data->notification->cron_type : '', "class" => "rm_cron_type","id" => "rm_cron_type", "required"=>1, "longDesc" => RM_UI_Strings::get('REPORT_CRON_TYPE_HELP'))));
            $form->addElement (new Element_DateTimeLocal("<b>" .RM_UI_Strings::get('REPORT_FIRST_EXE_LABEL'). "</b>", "first_exe", array("value" => $first_exe, "required"=>1, "class" => "rm_first_exe","id" => "rm_first_exe", "longDesc" => RM_UI_Strings::get('REPORT_FIRST_EXE_HELP'))));
            $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('REPORT_SUBJECT_LABEL') . "</b>", "email_subject", array("id" => "rm_email_subject","required"=>1, "value" => $email_subject, "longDesc"=>RM_UI_Strings::get('REPORT_SUBJECT_HELP'))));
            $form->addElement(new Element_TinyMCEWP("<b>" . RM_UI_Strings::get('REPORT_EMAIL_LABEL') . "</b>", $email_body, "email_content", array('editor_class' => 'rm_TinydMCE', 'editor_height' => '100px'), array("id"=>"rm_email_content", "required"=>1, "longDesc" => RM_UI_Strings::get('REPORT_EMAIL_HELP'))));
            
            $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','registrationmagic-addon'), '?page=rm_reports_notifications', array('class' => 'cancel')));
            $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit")));
        $form->render();
        ?>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery("input[name=notification_type]").change(function(){
            var report_type= jQuery(this).val();
            if(report_type=='submissions' && jQuery(this).is(':checked')){
                
                jQuery('#rm_payment_field').addClass('rm_disabled_field').slideUp();
                jQuery('#rm_login_field').addClass('rm_disabled_field').slideUp();
                jQuery('#rm_form_field').removeClass('rm_disabled_field').slideDown();
                //jQuery("#rm_login_status").addClass('rm_disabled_field').attr('disabled',true);
                //jQuery("#rm_payment_status").addClass('rm_disabled_field').attr('disabled',true);
                //jQuery("#rm_form_id").removeClass('rm_disabled_field').attr('disabled',false);
            }
            else if(report_type=='logins' && jQuery(this).is(':checked')){
                //
                jQuery('#rm_form_field').addClass('rm_disabled_field').slideUp();
                jQuery('#rm_payment_field').addClass('rm_disabled_field').slideUp();
                jQuery('#rm_login_field').removeClass('rm_disabled_field').slideDown();
                //jQuery("#rm_login_status").removeClass('rm_disabled_field').attr('disabled',false);
                //jQuery("#rm_payment_status").addClass('rm_disabled_field').attr('disabled',true);
                //jQuery("#rm_form_id").addClass('rm_disabled_field').attr('disabled',true);
            }else if(report_type=='payments' && jQuery(this).is(':checked')){
                jQuery('#rm_login_field').addClass('rm_disabled_field').slideUp();
                jQuery('#rm_form_field').removeClass('rm_disabled_field').slideDown();
                jQuery('#rm_payment_field').removeClass('rm_disabled_field').slideDown();
                //jQuery("#rm_login_field").addClass('rm_disabled_field').attr('disabled',true);
                //jQuery("#rm_payment_status").removeClass('rm_disabled_field').attr('disabled',false);
                //jQuery("#rm_form_id").removeClass('rm_disabled_field').attr('disabled',false);
            }
        });
        
        jQuery("input[name=sent_to]").change(function(){
            var sent_to= jQuery(this).val();
            if((sent_to=='me' || sent_to=='admins') && jQuery(this).is(':checked')){
                jQuery("#rm_sent_reports").slideUp();
                jQuery('#rm_individual_receiver').attr('required',false);
            }
            else if(sent_to=='individual' && jQuery(this).is(':checked')){
                jQuery('#rm_individual_receiver').attr('required',true);
                jQuery("#rm_sent_reports").slideDown();
            }
        });
        
    });
    
    
    
</script>    
<style>
.rm_disabled_field {
    display:none;
}

</style>