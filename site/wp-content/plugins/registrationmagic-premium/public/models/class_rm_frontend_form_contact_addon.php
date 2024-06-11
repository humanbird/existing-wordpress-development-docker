<?php

class RM_Frontend_Form_Contact_Addon
{

    public function post_sub_proc($request, $params, $parent_model)
    {
        if(isset($params['paystate']) && $params['paystate'] != 'post_payment')
            if ($parent_model->service->get_setting('enable_mailchimp') == 'yes' && (isset($parent_model->form_options->enable_mailchimp[0]) && $parent_model->form_options->enable_mailchimp[0]==1))
        {
            $form_options_mc=  $parent_model->form_options;
           
          if ($form_options_mc->form_is_opt_in_checkbox == 1 || (isset($form_options_mc->form_is_opt_in_checkbox[0]) && $form_options_mc->form_is_opt_in_checkbox[0] == 1))
        $should_subscribe = isset($request['rm_subscribe_mc']) && $request['rm_subscribe_mc'][0] == 1 ? 'yes' : 'no';
        else
        $should_subscribe = 'yes';
        if($should_subscribe == 'yes'){
             try
             {
           $parent_model->service->subscribe_to_mailchimp($request, $parent_model->get_form_options());
             }
             catch(Exception $e)
             {
                
             } 
                  
        }
             
        }
        /*
        if ($parent_model->service->get_setting('enable_ccontact') == 'yes' && $parent_model->form_options->enable_ccontact[0]==1)
        {
            $form_options_mc=  $parent_model->form_options;
           
          if ($form_options_mc->form_is_opt_in_checkbox_cc[0] == 1)
        $should_subscribe = isset($request['rm_subscribe_cc']) && $request['rm_subscribe_cc'][0] == 1 ? 'yes' : 'no';
        else
        $should_subscribe = 'yes';
      
        if($should_subscribe == 'yes'){
           
           
            try
             {
           $parent_model->service->subscribe_to_ccontact($request, $parent_model->get_form_options());
             }
             catch(Exception $e)
             {
                
             } 
        }
        }
        */
        
        do_action('rm_subscribe_newsletter',$parent_model->get_form_id(),$request);
        if ($parent_model->service->get_setting('enable_aweber') == 'yes' && (isset($parent_model->form_options->enable_aweber[0]) && $parent_model->form_options->enable_aweber[0]==1))
        {
            $form_options_mc=  $parent_model->form_options;
           
          if ($form_options_mc->form_is_opt_in_checkbox_aw[0] == 1)
        $should_subscribe = isset($request['rm_subscribe_aw']) && $request['rm_subscribe_aw'][0] == 1 ? 'yes' : 'no';
        else
        $should_subscribe = 'yes';
      
        if($should_subscribe == 'yes'){
           try
             {
           $parent_model->service->subscribe_to_aweber($request, $parent_model->get_form_options());
             }
             catch(Exception $e)
             {
                
             } 
           
        }
        }
        
        return null;
    }

    public function hook_post_field_addition_to_page($form, $page_no, $parent_model, $editing_sub=null)
    {
        //$last_page_no = max(array_keys($parent_model->form_pages))+1;
        $last_page_no = count($parent_model->form_pages);
        if ($last_page_no == $page_no)
        { 
            if ($parent_model->has_price_field() && !$editing_sub)
                $parent_model->add_payment_fields($form);
            
           $check_setting=null;
           if($parent_model->form_options->enable_captcha=='default')
            {
                $check_setting=get_option('rm_option_enable_captcha');
            }
            else
            {
                $check_setting=$parent_model->form_options->enable_captcha;
            }
          
            if ($check_setting == 'yes')   
                $form->addElement(new Element_Captcha());

         if ($parent_model->service->get_setting('enable_mailchimp') == 'yes' && $parent_model->form_options->form_is_opt_in_checkbox == 1 && $parent_model->form_options->enable_mailchimp[0] == 1 && !$editing_sub)
            {
                //This outer div is added so that the optin text can be made full width by CSS.
                $form->addElement(new Element_HTML('<div class="rm_optin_text">'));
                
                if($parent_model->form_options->form_opt_in_default_state == 'Checked')
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_mc', array(1 => $parent_model->form_options->form_opt_in_text ? : RM_UI_Strings::get('MSG_SUBSCRIBE')),array("value"=>1)));
                else 
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_mc', array(1 => $parent_model->form_options->form_opt_in_text ? : RM_UI_Strings::get('MSG_SUBSCRIBE'))));
            
                $form->addElement(new Element_HTML('</div>'));
            }
            
            do_action('rm_show_subscribe_opt',$parent_model->form_id,$form,$editing_sub);
            
            /*
            if ($parent_model->service->get_setting('enable_ccontact') == 'yes' && $parent_model->form_options->form_is_opt_in_checkbox_cc[0] == 1 && $parent_model->form_options->enable_ccontact[0] == 1 && !$editing_sub)
           {
                //This outer div is added so that the optin text can be made full width by CSS.
                $form->addElement(new Element_HTML('<div class="rm_optin_text">'));
                
                if($parent_model->form_options->form_opt_in_default_state_cc == 'Checked')
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_cc', array(1 => $parent_model->form_options->form_opt_in_text_cc ? : RM_UI_Strings::get('MSG_SUBSCRIBE')),array("value"=>1)));
                else 
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_cc', array(1 => $parent_model->form_options->form_opt_in_text_cc ? : RM_UI_Strings::get('MSG_SUBSCRIBE'))));
                
                $form->addElement(new Element_HTML('</div>'));
            }
            */
            if ($parent_model->service->get_setting('enable_aweber') == 'yes' && !empty($parent_model->form_options->enable_aweber) && $parent_model->form_options->form_is_opt_in_checkbox_aw[0] == 1 && $parent_model->form_options->enable_aweber[0] == 1 && !$editing_sub)
           {
                //This outer div is added so that the optin text can be made full width by CSS.
                $form->addElement(new Element_HTML('<div class="rm_optin_text">'));
                
                if($parent_model->form_options->form_opt_in_default_state_aw == 'Checked')
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_aw', array(1 => $parent_model->form_options->form_opt_in_text_aw ? : RM_UI_Strings::get('MSG_SUBSCRIBE')),array("value"=>1)));
                else 
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_aw', array(1 => $parent_model->form_options->form_opt_in_text_aw ? : RM_UI_Strings::get('MSG_SUBSCRIBE'))));
            
                $form->addElement(new Element_HTML('</div>'));
           }
            
            if($parent_model->form_options->show_total_price && $parent_model->has_price_field())
            {
                $gopts = new RM_Options;
                $total_price_localized_string = RM_UI_Strings::get('FE_FORM_TOTAL_PRICE');
                $curr_symbol = $gopts->get_currency_symbol();
                $curr_pos = $gopts->get_value_of('currency_symbol_position');
                $price_formatting_data = json_encode(array("loc_total_text" => $total_price_localized_string, "symbol" => $curr_symbol, "pos" => $curr_pos));
                $form->addElement(new Element_HTML("<div class='rmrow rm_total_price' style='{$parent_model->form_options->style_label}' data-rmpriceformat='$price_formatting_data'></div>"));
            }
       
        
        }
    }

}