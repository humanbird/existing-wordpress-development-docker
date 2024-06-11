<?php

class RM_Frontend_Form_Multipage_Addon
{

    public function prepare_fields_for_render($form,$parent_model,$editing_sub=null)
    {
        if(!empty($editing_sub))
            $form->addElement(new Element_Hidden("rm_tp_timezone","",array("id"=>"id_rm_tp_timezone")));
        $n = 1; //page no(ordinal no. not actual) maintained for js traversing through pages.
        
        if(count($parent_model->form_pages) > 1) {
            $form->addElement(new Element_HTML("<ul id=\"rmagic-progressbar\">"));
            foreach ($parent_model->ordered_form_pages as $index => $fp_no) {
                $page = $parent_model->form_pages[$fp_no];
                if($index == 0)
                    $form->addElement(new Element_HTML("<li class=\"active\"><span class=\"rm-progressbar-counter\"></span><span class=\"rm-form-page-name\">$page</span></li>"));
                else
                    $form->addElement(new Element_HTML("<li><span class=\"rm-progressbar-counter\"></span><span class=\"rm-form-page-name\">$page</span></li>"));
            }
            $form->addElement(new Element_HTML("</ul>"));
        }
        
        if(isset($_GET['form_prev'])) {
            if($parent_model->form_type == 0) {
                $form->addElement(new Element_HTML("<div class=\"rm-form-preview-notice\">".__("Since you are already logged in, Email Field cannot be edited.", 'registrationmagic-addon')."</div>"));
            } else {
                if($parent_model->form_options->hide_username == 1 && isset($parent_model->fields['pwd'])) {
                    $form->addElement(new Element_HTML("<div class=\"rm-form-preview-notice\">".__("Since you are already logged in, Email and Password Fields cannot be edited.", 'registrationmagic-addon')."</div>"));
                } elseif($parent_model->form_options->hide_username == 0 && isset($parent_model->fields['pwd'])) {
                    $form->addElement(new Element_HTML("<div class=\"rm-form-preview-notice\">".__("Since you are already logged in, Username, Email and Password Fields cannot be edited.", 'registrationmagic-addon')."</div>"));
                } elseif($parent_model->form_options->hide_username == 1 && !isset($parent_model->fields['pwd'])) {
                    $form->addElement(new Element_HTML("<div class=\"rm-form-preview-notice\">".__("Since you are already logged in, Email Field cannot be edited.", 'registrationmagic-addon')."</div>"));
                } elseif($parent_model->form_options->hide_username == 0 && !isset($parent_model->fields['pwd'])) {
                    $form->addElement(new Element_HTML("<div class=\"rm-form-preview-notice\">".__("Since you are already logged in, Username and Email Fields cannot be edited.", 'registrationmagic-addon')."</div>"));
                }
            }
        }
        
        foreach ($parent_model->ordered_form_pages as $fp_no) {
            $k = intval($fp_no);
            $page = $parent_model->form_pages[$fp_no];
            $i = $k+1;//actual page no.
            if ($n == 1) {
               $form->addElement(new Element_HTML("<div class=\"rm-multi-page-form rmformpage_form_".$parent_model->form_id."_".$parent_model->form_number."\" id=\"rm_form_page_form_".$parent_model->form_id ."_".$parent_model->form_number. "_".$n."\">"));
               $form->addElement(new Element_HTML("<fieldset class='rmfieldset'>"));
               
               //if(count($parent_model->form_pages) > 1)
                   //$form->addElement(new Element_HTML("<legend style='".$parent_model->form_options->style_section."'>".$page."</legend>"));
                
                $parent_model->hook_pre_field_addition_to_page($form, $n);
                if(!empty($parent_model->rows)) {
                    foreach($parent_model->rows as $row) {
                        if($row->page_no != $i) {
                            continue;
                        }
                        if(!empty($row->fields)) {
                            $class_attr = !empty($row->class) ? 'rmagic-row ' . $row->class : 'rmagic-row';
                            $style_attr = '';
                            if(!empty($row->bmargin))
                                $style_attr = $style_attr . 'margin-bottom:'. $row->bmargin . 'px;';
                            if(!empty($row->width))
                                $style_attr = $style_attr . 'max-width:'. $row->width . 'px;';
                            $form->addElement(new Element_HTML('<div class="'.$class_attr.'" style="'.$style_attr.'" data-bmargin="'. $row->bmargin . '">'));
                            if(!empty($row->heading))
                                $form->addElement(new Element_HTML('<div class="rmagic-heading">'.$row->heading.'</div>'));
                            if(!empty($row->subheading))
                                $form->addElement(new Element_HTML('<div class="rmagic-subheading">'.nl2br($row->subheading).'</div>'));
                            if($row->gutter == 10)
                                $form->addElement(new Element_HTML('<div class="rmagic-fields-wrap">'));
                            else
                                $form->addElement(new Element_HTML('<div class="rmagic-fields-wrap rm-gutter-'.$row->gutter.'">'));
                            switch($row->columns) {
                                case '1':
                                    $col_class = 'rmagic-col-12';
                                    break;
                                case '1:1':
                                    $col_class = 'rmagic-col-6';
                                    break;
                                case '2:1':
                                    $col_class = 'rmagic-col-4';
                                    break;
                                case '1:1:1':
                                    $col_class = 'rmagic-col-4';
                                    break;
                                case '1:1:1:1':
                                    $col_class = 'rmagic-col-3';
                                    break;
                                default:
                                    $col_class = 'rmagic-col-3';
                                    break;
                            }
                            $field_counter = 1;
                            foreach($row->fields as $field) {
                                if($row->columns == '2:1' && $field_counter == 1) {
                                    $form->addElement(new Element_HTML('<div class="rmagic-col rmagic-col-8">'));
                                } else if(isset($field->field_options['id']) && $field->field_options['id'] == 'rm_reg_form_pw_reentry' && $field->field_model->field_options->cnf_pass_position == 'below') {
                                    // Doing nothing
                                } else {
                                    $form->addElement(new Element_HTML('<div class="rmagic-col '.$col_class.'">'));
                                }
                                if(empty($field)) {
                                    $form->addElement(new Element_HTML("</div>"));
                                    $field_counter++;
                                    continue;
                                }
                                if(is_array($field)) {
                                   foreach($field as $single_field) {
                                       $pf = $single_field->get_pfbc_field();
                                        if ($pf === null || $single_field->get_page_no() != $i)
                                            continue;

                                        if (is_array($pf)) {
                                            foreach ($pf as $f) {
                                                if (!$f)
                                                    continue;
                                                $form->addElement($f);
                                            }
                                        } else
                                            $form->addElement($pf);
                                   }
                                   continue;
                                }
                                $pf = $field->get_pfbc_field();
                                if ($pf === null || $field->get_page_no() != $i)
                                    continue;

                                if (is_array($pf)) {
                                    foreach ($pf as $f) {
                                        if (!$f)
                                            continue;
                                        $form->addElement($f);
                                    }
                                } else
                                    $form->addElement($pf);
                                $form->addElement(new Element_HTML("</div>"));
                                $field_counter++;
                            }
                            $form->addElement(new Element_HTML("</div>"));
                            $form->addElement(new Element_HTML("</div>"));
                        }
                    }
                } else {
                    foreach($parent_model->fields as $field) {
                        if(is_array($field)) {
                           foreach($field as $single_field) {
                               $pf = $single_field->get_pfbc_field();
                                if ($pf === null || $single_field->get_page_no() != $i)
                                    continue;

                                if (is_array($pf)) {
                                    foreach ($pf as $f) {
                                        if (!$f)
                                            continue;
                                        $form->addElement($f);
                                    }
                                } else
                                    $form->addElement($pf);
                           }
                           continue;
                        }
                        $pf = $field->get_pfbc_field();                            
                        if ($pf === null || $field->get_page_no() != $i)
                            continue;

                        if (is_array($pf)) {
                            foreach ($pf as $f) {
                                if (!$f)
                                    continue;
                                $form->addElement($f);
                            }
                        } else
                            $form->addElement($pf);
                    }
                }
                $parent_model->hook_post_field_addition_to_page($form, $n, $editing_sub);
                $form->addElement(new Element_HTML("</fieldset>"));
                $form->addElement(new Element_HTML("</div>"));
            } else {
                $form->addElement(new Element_HTML("<div class=\"rm_form_page rmformpage_form_".$parent_model->form_id."_".$parent_model->form_number."\" id=\"rm_form_page_form_".$parent_model->form_id ."_".$parent_model->form_number. "_".$n."\" style=\"display:none\">"));
                $form->addElement(new Element_HTML("<fieldset class='rmfieldset'>"));
                //$form->addElement(new Element_HTML("<legend style='".$parent_model->form_options->style_section."'>".$page."</legend>"));
                
                $parent_model->hook_pre_field_addition_to_page($form, $n);
                if(!empty($parent_model->rows)) {
                    foreach($parent_model->rows as $row) {
                        if($row->page_no != $i) {
                            continue;
                        }
                        if(!empty($row->fields)) {
                            $class_attr = !empty($row->class) ? 'rmagic-row ' . $row->class : 'rmagic-row';
                            $style_attr = '';
                            if(!empty($row->bmargin))
                                $style_attr = $style_attr . 'margin-bottom:'. $row->bmargin . 'px;';
                            if(!empty($row->width))
                                $style_attr = $style_attr . 'max-width:'. $row->width . 'px;';
                            $form->addElement(new Element_HTML('<div class="'.$class_attr.'" style="'.$style_attr.'" data-bmargin="'. $row->bmargin . '">'));
                            if(!empty($row->heading))
                                $form->addElement(new Element_HTML('<div class="rmagic-heading">'.$row->heading.'</div>'));
                            if(!empty($row->subheading))
                                $form->addElement(new Element_HTML('<div class="rmagic-subheading">'.nl2br($row->subheading).'</div>'));
                            $form->addElement(new Element_HTML('<div class="rmagic-fields-wrap rm-gutter-'.$row->gutter.'">'));
                            switch($row->columns) {
                                case '1':
                                    $col_class = 'rmagic-col-12';
                                    break;
                                case '1:1':
                                    $col_class = 'rmagic-col-6';
                                    break;
                                case '2:1':
                                    $col_class = 'rmagic-col-4';
                                    break;
                                case '1:1:1':
                                    $col_class = 'rmagic-col-4';
                                    break;
                                case '1:1:1:1':
                                    $col_class = 'rmagic-col-3';
                                    break;
                                default:
                                    $col_class = 'rmagic-col-3';
                                    break;
                            }
                            $field_counter = 1;
                            foreach($row->fields as $field) {
                                if($row->columns == '2:1' && $field_counter == 1) {
                                    $form->addElement(new Element_HTML('<div class="rmagic-col rmagic-col-8">'));
                                } else if(isset($field->field_options['id']) && $field->field_options['id'] == 'rm_reg_form_pw_reentry' && $field->field_model->field_options->cnf_pass_position == 'below') {
                                    // Doing nothing
                                } else {
                                    $form->addElement(new Element_HTML('<div class="rmagic-col '.$col_class.'">'));
                                }
                                if(empty($field)) {
                                    $form->addElement(new Element_HTML("</div>"));
                                    $field_counter++;
                                    continue;
                                }
                                if(is_array($field)) {
                                   foreach($field as $single_field) {
                                       $pf = $single_field->get_pfbc_field();
                                        if ($pf === null || $single_field->get_page_no() != $i)
                                            continue;

                                        if (is_array($pf)) {
                                            foreach ($pf as $f) {
                                                if (!$f)
                                                    continue;
                                                $form->addElement($f);
                                            }
                                        } else
                                            $form->addElement($pf);
                                   }
                                   continue;
                                }
                                $pf = $field->get_pfbc_field();
                                if ($pf === null || $field->get_page_no() != $i)
                                    continue;

                                if (is_array($pf)) {
                                    foreach ($pf as $f) {
                                        if (!$f)
                                            continue;
                                        $form->addElement($f);
                                    }
                                } else
                                    $form->addElement($pf);
                                $form->addElement(new Element_HTML("</div>"));
                                $field_counter++;
                            }
                            $form->addElement(new Element_HTML("</div>"));
                            $form->addElement(new Element_HTML("</div>"));
                        }
                    }
                } else {
                    foreach ($parent_model->fields as $field) {
                        if(is_array($field)) {
                           foreach($field as $single_field) { 
                               $pf = $single_field->get_pfbc_field();
                                if ($pf === null || $single_field->get_page_no() != $i)
                                    continue;

                                if (is_array($pf)) {
                                    foreach ($pf as $f) {
                                        if (!$f)
                                            continue;
                                        $form->addElement($f);
                                    }
                                } else
                                    $form->addElement($pf);
                           }
                           continue;
                        }
                        $pf = $field->get_pfbc_field();

                        if ($pf === null || $field->get_page_no() != $i)
                            continue;

                        if (is_array($pf)) {
                            foreach ($pf as $f) {
                                if (!$f)
                                    continue;
                                $form->addElement($f);
                            }
                        } else
                            $form->addElement($pf);
                    }
                }
                $parent_model->hook_post_field_addition_to_page($form, $n, $editing_sub);
                $form->addElement(new Element_HTML("</fieldset>"));
                $form->addElement(new Element_HTML("</div>"));          
            }
            $n++;
        }
        
    }
    
    public function prepare_button_for_render($form,$parent_model,$editing_sub=null)
    {
        if ($parent_model->service->get_setting('theme') != 'matchmytheme')
        {
            if(isset($parent_model->form_options->style_btnfield))
                unset($parent_model->form_options->style_btnfield);
        }
        $sub_btn_label = $parent_model->form_options->form_submit_btn_label ? stripslashes((string)$parent_model->form_options->form_submit_btn_label) : __( 'Submit', 'registrationmagic-addon' );
        $prev_btn_label = $parent_model->form_options->form_prev_btn_label ? stripslashes((string)$parent_model->form_options->form_prev_btn_label) : RM_UI_Strings::get('LABEL_PREV_FORM_PAGE');
        $next_btn_label = $parent_model->form_options->form_next_btn_label ? stripslashes((string)$parent_model->form_options->form_next_btn_label) : __( 'Next', 'registrationmagic-addon' );
        $max_pages = count($parent_model->get_form_pages());
        
        $sub_btn_label = !empty($editing_sub) ? __('Update','registrationmagic-addon') : $sub_btn_label;
        $btn_label = ($max_pages > 1) ? $next_btn_label : $sub_btn_label;
        
        if($max_pages > 1 && !$parent_model->form_options->no_prev_button)
            $form->addElement(new Element_Button($prev_btn_label, "button", array("style" => isset($parent_model->form_options->style_btnfield)?$parent_model->form_options->style_btnfield:null,"name"=>"rm_prev_btn",'class'=>'rm_prev_btn', "id"=>"rm_prev_form_page_button_".$parent_model->form_id.'_'.$parent_model->form_number, "onclick"=>'gotoprev_form_'.$parent_model->form_id.'_'.$parent_model->form_number.'()', "disabled"=>"1")));
        
        
        $form->addElement(new Element_Button($btn_label, "submit", array("name"=>"rm_sb_btn","class"=>"rm_next_btn","data-label-next" => $next_btn_label,"data-label-sub" => $sub_btn_label,  "style" => isset($parent_model->form_options->style_btnfield)?$parent_model->form_options->style_btnfield:null)));
        $form->addElement(new Element_Button(stripslashes((string)$sub_btn_label), "submit", array(
 "style" => isset($parent_model->form_options->style_btnfield)?$parent_model->form_options->style_btnfield:null,"class"=>"rm_noscript_btn")));
        
        $parent_model->insert_JS($form);
    }
    
    public function get_jqvalidator_config_JS($parent_model)
    { $error= RM_Utilities::js_error_messages();
$str = <<<JSHD
        jQuery.extend(jQuery.validator.messages, {
            required:"{$error['required']}",
        });
        jQuery.validator.setDefaults({errorClass: 'rm-form-field-invalid-msg',
                                        ignore:':hidden,.ignore,:not(:visible),.rm_untouched', wrapper:'div',
                                        errorPlacement: function(error, element) { 

                                                            var elementId= element.attr('id');
                                                            if(elementId){
                                                                var target_element_id= elementId.replace('-error','');
                                                                var target_element= jQuery("#" + target_element_id);
                                                                if(target_element.length>0){
                                                                    if(target_element.hasClass('rm_untouched')){
                                                                        return true;
                                                                        }
                                                                }
                                                            }
                                                                
                                                            
                                                            error.appendTo(element.closest('div'));
                                                          }
                                    });
JSHD;
        return $str;
    }

    public function insert_JS($form,$parent_model)
    {
        if(isset($_GET['action']) && $_GET['action']=='registrationmagic_embedform') // Restricting front js loading in dashboard.
            return;
        $next_btn_label = $parent_model->form_options->form_next_btn_label ? stripslashes((string)$parent_model->form_options->form_next_btn_label) : __( 'Next', 'registrationmagic-addon' );
        $max_page_count = count($parent_model->get_form_pages());
        $form_identifier = "form_".$parent_model->get_form_id();
        $form_id = $parent_model->get_form_id();
        $validator_js = $parent_model->get_jqvalidator_config_JS();
        
      
        $jqvalidate = RM_Utilities::enqueue_external_scripts('rm_jquery_validate', RM_BASE_URL."public/js/jquery.validate.min.js", array('jquery'));
      
        $jqvalidate .= RM_Utilities::enqueue_external_scripts('rm_jquery_validate_add', RM_BASE_URL."public/js/additional-methods.min.js", array('jquery'));
        $jq_front_form_script = RM_Utilities::enqueue_external_scripts('rm_front_form_script', RM_BASE_URL."public/js/rm_front_form.js", array('jquery'));
        wp_enqueue_script('rm_front');
        wp_enqueue_script('rm_jquery_conditionalize');
        echo $jqvalidate.$jq_front_form_script;
        echo '<pre class=\'rm-pre-wrapper-for-script-tags\'><script>
        
   /*form specific onload functionality*/
jQuery(document).ready(function () {
if(jQuery("#'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' [name=\'rm_payment_method\']").length>0 && jQuery("#'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' [name=\'rm_payment_method\']:checked").val()==\'stripe\'){jQuery(\'#rm_stripe_fields_container_'.esc_html($form_id).'_'.esc_html($parent_model->form_number).'\').show();}

    jQuery(\'[data-rm-unique="1"]\').change(function(event) {
        rm_unique_field_check(jQuery(this));
    });
    
   });
                
if (typeof window[\'rm_multipage\'] == \'undefined\') {

    rm_multipage = {
        global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).': 1
    };

}
else
 rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' = 1;

function gotonext_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'(){
        /* Making sure action attribute is empty */
        jQuery("form.rmagic-form").attr("action","");
        var maxpage = '.esc_html($max_page_count).' ;
        '.$validator_js.'        
        
        var jq_prev_button = jQuery("#rm_prev_form_page_button_'.esc_html($form_id).'_'.esc_html($parent_model->form_number).'");
        var jq_next_button = jQuery("#rm_next_form_page_button_'.esc_html($form_id).'_'.esc_html($parent_model->form_number).'");
        
        var next_label = jq_next_button.data("label-next");
        var payment_method = jQuery(\'[name=rm_payment_method]:checked\').val();
        var form_object= jQuery("#rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).').closest("form");
        var submit_btn= form_object.find("[type=submit]:not(.rm_noscript_btn)");
        var sub_label = submit_btn.data("label-sub");
        form_object.find(\'input.rm_price_field_quantity\').each(function() {
            if(typeof jQuery(this).attr(\'min\') == \'undefined\') {
                jQuery(this).attr(\'min\', 0);
            }
        });
        if(form_object.find(\'.rm_privacy_cb\').is(\':visible\') && !form_object.find(\'.rm_privacy_cb\').prop(\'checked\')){
             //form_object.find(\'.rm_privacy_cb\').trigger(\'change\');
             form_object.find(\'.rm_privacy_cb\').focus();
             form_object.find(\'.rm_privacy_cb\').parent().parent().siblings(\'div.rm-form-error-message\').show();
             return false;
        } 
        if(typeof payment_method == \'undefined\' || payment_method != \'stripe\')
        {            
            elements_to_validate = jQuery("#rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'+" :input").not(\'#rm_stripe_fields_container_'.esc_html($form_id).'_'.esc_html($parent_model->form_number).' :input\');
        }
        else
            var elements_to_validate = jQuery("#rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'+" :input");
        
        if(elements_to_validate.length > 0)
        {
            var valid = elements_to_validate.valid();
            elements_to_validate.each(function(){
            var if_mobile= jQuery(this).attr(\'data-mobile-intel-field\');
                if(if_mobile){
                    var tel_error = rm_toggle_tel_error(this.dataset.validnumber,jQuery(this),jQuery(this).data(\'error-message\'));
                    if(tel_error){
                        valid= false;
                    }
                    else
                    {
                        jQuery(this).val(this.dataset.fullnumber);
                    }
                }
            });

            if(!valid)
            {   
                setTimeout(function(){ submit_btn.prop(\'disabled\',false); }, 1000);
                var error_element= jQuery(document).find("input.rm-form-field-invalid-msg")[0];
                if(error_element){
                    error_element.focus();
                }
                return false;
            }
            else{
                if(maxpage==rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'){
                    return true;
                }
            }
           
        } else{
            if(maxpage==rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'){
                    return true;
            }
        }
        
        /* Server validation for Username and Email field */
        for(var i=0;i<rm_validation_attr.length;i++){
            var validation_flag= true;
            jQuery("[" + rm_validation_attr[i] + "=0]").each(function(){
               validation_flag= false;
               return false;
            });
            
           
            if(!validation_flag)
              return;
        }
        
       
        rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'++;
        if(rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'>=maxpage){
            submit_btn.prop(\'value\',sub_label);
            submit_btn.addClass(\'rm-submit-btn-show\');
        }
        else{
            submit_btn.prop(\'value\',\''.esc_html($next_btn_label).'\');
            submit_btn.removeClass(\'rm-submit-btn-show\');
        }
       
        /*skip blank form pages*/
        /*while(jQuery("#rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'+" :input").length == 0)
        {
            if(maxpage <= rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).')
            {
                    if(jQuery("#rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'+" :input").length == 0){
                        jq_next_button.prop(\'type\',\'submit\');
                        jq_prev_button.prop(\'disabled\',true);
                        return;
                    }        
                    else
                        break;
            }    
           rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'++;		       
        }*/
          		
	if(rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' >= maxpage){
            jq_next_button.attr("value", sub_label);
        }
	if(maxpage < rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).')
	{
		rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' = maxpage;
		jq_next_button.prop(\'type\',\'submit\');
                jq_prev_button.prop(\'disabled\',true);
	}
        
	jQuery(".rmformpage_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'").each(function (){
        
		var visibledivid = "rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).';
		var current_page= jQuery(this);
                    if(jQuery(this).attr(\'id\') == visibledivid){
                        setTimeout(function(){ // Delaying field show to skip validation for untouched fields
                            current_page.show();
                            current_page.find(\':input\').addClass(\'rm_untouched\');
                            setTimeout(function(){ current_page.find(\':input\').removeClass(\'rm_untouched\'); }, 1000);
                        },100);
                }
		else
                    current_page.hide();  
        });         
        
        jQuery(\'.rmformpage_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'\').find(\':input\').filter(\':visible\').eq(0).focus();
        jQuery(\'#rmagic-progressbar\').animate({
            scrollTop: (jQuery(\'.rmformpage_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'\').first().offset().top)
        },500);
        
        jQuery(\'ul#rmagic-progressbar\').children(\'li\').each(function(index) {
            if(!jQuery(this).hasClass(\'active\')) {
                jQuery(this).addClass(\'active\');
                return false;
            }
        });
    
        jq_prev_button.prop(\'disabled\',false);
        rmInitGoogleApi();
        
        setTimeout(function(){ submit_btn.prop(\'disabled\',false); }, 1000);
        
        if(jq_prev_button.length > 0 && rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'>=1){
            jq_prev_button.show();
        }
        
        if(rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' == maxpage){
            return false;
        }
        
        if(maxpage==\''.esc_html($parent_model->form_number).'\'){
            return true;
        }
        return false;
           
}
    </script></pre>';

if(!$parent_model->form_options->no_prev_button) {
echo '<pre class=\'rm-pre-wrapper-for-script-tags\'><script>
function gotoprev_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'(){
	
	var maxpage = '.esc_html($max_page_count).' ;
        var jq_prev_button = jQuery("#rm_prev_form_page_button_'.esc_html($form_id).'_'.esc_html($parent_model->form_number).'");
        var jq_next_button = jQuery("#rm_next_form_page_button_'.esc_html($form_id).'_'.esc_html($parent_model->form_number).'");
        //var sub_label = jq_next_button.data("label-sub");
        var next_label = jq_next_button.data("label-next");
        var form_object= jQuery("#rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).').closest("form");
        var submit_btn= form_object.find("[type=submit]:not(.rm_noscript_btn)");
        var sub_label = submit_btn.data("label-sub");
        /*
        if(form_object.find(\'.rm_privacy_cb\').is(\':visible\') && !form_object.find(\'.rm_privacy_cb\').prop(\'checked\')){
            //form_object.find(\'.rm_privacy_cb\').trigger(\'change\');
            form_object.find(\'.rm_privacy_cb\').focus();
            form_object.find(\'.rm_privacy_cb\').parent().parent().siblings(\'div.rm-form-error-message\').show();
            return false;
        }
        */
	rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'--;
        jq_next_button.attr(\'type\',\'button\');        
        
        if(maxpage==rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'){
            submit_btn.prop(\'value\',sub_label);
            submit_btn.addClass(\'rm-submit-btn-show\');
        }
        else{
            submit_btn.prop(\'value\',\''.esc_html($next_btn_label).'\');
            submit_btn.removeClass(\'rm-submit-btn-show\');
        }
        /*skip blank form pages*/
        while(jQuery("#rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'+" :input,.rm-total-price ").length == 0)
        {
            if(1 >= rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).')
            {
                    if(jQuery("#rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'+" :input,.rm-total-price ").length == 0){
                        rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' = 1;
                        //jq_prev_button.prop(\'disabled\',true);
                        return;
                    }        
                    else
                        break;
            }
        
            rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'--;
        }
        
        if(rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' <= maxpage-1)
            jq_next_button.attr("value", next_label);
            
	jQuery(".rmformpage_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'").each(function (){
		var visibledivid = "rm_form_page_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'_"+rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).';
		if(jQuery(this).attr(\'id\') == visibledivid){
			jQuery(this).show();
                }
		else
			jQuery(this).hide();
	});
        jQuery(\'.rmformpage_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'\').find(\':input\').filter(\':visible\').eq(0).focus();
        if(rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' <= 1)
        {
            rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).' = 1;
           // jq_prev_button.prop(\'disabled\',true);
        }
        jQuery(\'#rmagic-progressbar\').animate({
            scrollTop: (jQuery(\'.rmformpage_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'\').first().offset().top)
        },500);
        
        jQuery(\'ul#rmagic-progressbar\').children(\'li\').each(function(index) {
            if(!jQuery(this).next().hasClass(\'active\')) {
                jQuery(this).removeClass(\'active\');
                return false;
            }
        });
        
        if(rm_multipage.global_page_no_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'==1){
            jq_prev_button.hide();
            submit_btn.prop(\'disabled\',false);
        }
}
         
</script>
    <script src="//www.youtube.com/player_api"></script>
    <script>
    var players = [];
    function onYouTubePlayerAPIReady() {
        // create the global player from the specific iframe (#video)
        var pre_id = \'\';
        jQuery(".allow-autoplay").each(function(){
            if(pre_id!=jQuery(this).attr("id")){
                players.push(new YT.Player(jQuery(this).attr("id")));
            }
            pre_id = jQuery(this).attr("id");
        });
    }
    jQuery(document).ready(function(){
        var videosArr = [];
        var pre_id = \'\';
        jQuery(".allow-autoplay").each(function(i){
            if(pre_id!=jQuery(this).attr("id")){
                videosArr[jQuery(this).attr("id")] = i;
            }
            pre_id = jQuery(this).attr("id");
        });
        jQuery(\'.buttonarea input\').click(function(){
            setTimeout(function(){
                jQuery(".rmformpage_'.esc_html($form_identifier).'_'.esc_html($parent_model->form_number).'").each(function(){
                    if(jQuery(this).css(\'display\')==\'block\'){
                        var page_video_id= jQuery(this).attr("id");
                        if(jQuery(\'#\'+page_video_id+\' .allow-autoplay\').length){
                            players[videosArr[jQuery(\'#\'+page_video_id+\' .allow-autoplay\').attr(\'id\')]].playVideo();
                        }
                    }
                });
            }, 300);
        });
    });
    </script> 
   </pre>';
}
        /*
        if($parent_model->form_options->no_prev_button)    
        $str = $jqvalidate.$jq_front_form_script.$mainstr;
        else
        $str = $jqvalidate.$jq_front_form_script.$mainstr.$prev_button_str;
        
        $form->addElement(new Element_HTML($str));
        */
    }

}
