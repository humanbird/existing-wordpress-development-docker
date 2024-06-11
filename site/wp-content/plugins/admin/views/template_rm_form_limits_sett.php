<?php
if (!defined('WPINC')) {
    die('Closed');
}

       $other_status_list= array(''=>'Select Status');
       foreach($data->forms as $f){
           $fopt= maybe_unserialize($f->form_options);
           if(isset($fopt->custom_status) && is_array($fopt->custom_status))
           {
                foreach($fopt->custom_status as $key=>$value){
                    $key = $f->form_id.':'.$key;
                    $other_status_list[$key]=$value['label'];
                }
           }
       }
       
?>
<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("form_sett_limits");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        
        if (isset($data->model->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->model->form_name . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsettingtitle">' . RM_UI_Strings::get('LABEL_F_LIM_SETT') . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->model->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
        }
        $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmnotice">You can set to display Limits above the form in <a target="_blank" href="'.admin_url("admin.php?page=rm_options_general").'">Global Settings</a>.</div></div>'));   
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_AUTO_EXPIRE') . "</b>", "form_should_auto_expire", array(1 => ""), array("id" => "rm_", "class" => "rm_a", "value" => $data->model->form_should_auto_expire, "onclick" => "hide_show(this);", "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXPIRE'))));
        if ($data->model->form_should_auto_expire == '1')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_a_childfieldsrow" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_a_childfieldsrow" style="display:none">'));

        $expired_by = $data->model->form_options->form_expired_by ? $data->model->form_options->form_expired_by : 'submissions';
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_EXPIRY') . "</b>", "form_expired_by", array('submissions' => __('By Submissions','registrationmagic-addon'), 'date' => __('By Date','registrationmagic-addon'), 'both' => __('Set Both (Whichever is earlier)','registrationmagic-addon'),'status'=>__('By Custom Status','registrationmagic-addon')), array("id" => "rm_", "value" => $expired_by, "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_EXPIRE_BY'))));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_SUB_LIMIT') . "</b>", "form_submissions_limit", array("id" => "rm_form_name", "value" => $data->model->form_options->form_submissions_limit, "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXP_SUB_LIMIT'))));
        $form->addElement(new Element_jQueryUIDateTime("<b>" . RM_UI_Strings::get('LABEL_EXPIRY_DATE') . "</b>", 'form_expiry_date', array('class' => 'rm_dateelement', "value" => $data->model->form_options->form_expiry_date, "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXP_TIME_LIMIT'), "date_format" => "mm/dd/yy HH:mm")));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_STATUSES') . "</b>", "form_limit_by_cs", $other_status_list, array("id"=>"limit_by_status", "multiple"=>"multiple", "value" => $data->model->form_options->form_limit_by_cs, "class" => "rm_static_field rm_status_form_sett_limits", "longDesc"=>RM_UI_Strings::get('HELP_CLEAR_STATUSES'))));
        ////////POST EXP SWITCH
        $post_exp_action = $data->model->form_options->post_expiry_action ? $data->model->form_options->post_expiry_action : 'display_message';
        
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_POST_EXP_ACTION') . "</b>", "post_expiry_action", array('display_message' => RM_UI_Strings::get('LABEL_DISPLAY_MSG'), 'switch_to_another_form' => RM_UI_Strings::get('LABEL_SWITCH_FORM')), array("value" => $post_exp_action, "longDesc" => RM_UI_Strings::get('HELP_POST_EXP_ACTION'))));
        
        $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_post_exp_msg" >'));
        
        $exp_msg = isset($data->model->form_options->form_message_after_expiry)?$data->model->form_options->form_message_after_expiry:'';
        $form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_EXPIRY_MSG') . "</b>", "form_message_after_expiry", array("class" => "rm_form_description",  "value" => $exp_msg,  "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXP_MSG'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_post_exp_form" >'));
        
        //Remove current form from the list of selectable forms.
        $selectable_forms = $data->form_dropdown;
        if(isset($selectable_forms[$data->model->form_id]))
            unset($selectable_forms[$data->model->form_id]);
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_SELECT_FORM') . "</b>", "post_expiry_form_id", $selectable_forms, array("class" => "rm_form_description",  "value" => $data->model->form_options->post_expiry_form_id,  "longDesc" => RM_UI_Strings::get('HELP_POST_EXP_FORM'))));
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','registrationmagic-addon'), '?page='.$data->next_page.'&rm_form_id='.$data->model->form_id, array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field.','registrationmagic-addon') ."')")));
        $form->render();
        ?>
    </div>
<!-- Plugin Custom Solution Banner-->
<?php include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php'; ?> 
    
</div>

<pre class='rm-pre-wrapper-for-script-tags'><script>
jQuery(document).ready(function(){
    jQuery('input[name=form_expired_by]').click(function(e) {
        var value = jQuery(this).val();
        if(value == 'submissions') {
            jQuery('input[name=form_submissions_limit]').parents('div.rmrow').show();
            jQuery('input[name=form_expiry_date]').parents('div.rmrow').hide();
            jQuery('select#limit_by_status').parents('div.rmrow').hide();
            jQuery('input[name=post_expiry_action]').parents('div.rmrow').show();
            jQuery('input[name=post_expiry_action]').trigger("change");
        } else if(value == 'date') {
            jQuery('input[name=form_submissions_limit]').parents('div.rmrow').hide();
            jQuery('select#limit_by_status').parents('div.rmrow').hide();
            jQuery('input[name=form_expiry_date]').parents('div.rmrow').show();
            jQuery('input[name=post_expiry_action]').parents('div.rmrow').show();
            jQuery('input[name=post_expiry_action]').trigger("change");
        } else if(value == 'status') {
            jQuery('select#limit_by_status').parents('div.rmrow').show();
            jQuery('input[name=form_submissions_limit]').parents('div.rmrow').hide();
            jQuery('input[name=form_expiry_date]').parents('div.rmrow').hide();
            jQuery('input[name=post_expiry_action]').parents('div.rmrow').show();
            jQuery('input[name=post_expiry_action]').trigger("change");
        } else {
            jQuery('select#limit_by_status').parents('div.rmrow').hide();
            jQuery('input[name=form_submissions_limit]').parents('div.rmrow').show();
            jQuery('input[name=form_expiry_date]').parents('div.rmrow').show();
            jQuery('input[name=post_expiry_action]').parents('div.rmrow').show();
            jQuery('input[name=post_expiry_action]').trigger("change");
        }
    });

    jQuery('input[name=post_expiry_action]').click(function(e) {
        var value = jQuery(this).val();
        if(value == 'display_message') {
            jQuery('#rm_post_exp_form').hide();
            jQuery('#rm_post_exp_msg').show();
            jQuery('#rm_post_exp_msg :input').attr('disabled',false);
            jQuery('#rm_post_exp_form :input').attr('disabled',true);
        } else if(value == 'switch_to_another_form'){
            jQuery('#rm_post_exp_msg').hide();
            jQuery('#rm_post_exp_form').show();
            jQuery('#rm_post_exp_form :input').attr('disabled',false);
            jQuery('#rm_post_exp_msg :input').attr('disabled',true);
        }
    });

    jQuery('input[value=<?php echo $expired_by; ?>]').trigger('click');
    jQuery('input[value=<?php echo $post_exp_action; ?>]').trigger('click');
});
</script></pre>
<?php