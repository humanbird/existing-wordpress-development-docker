<?php

class RM_Frontend_Form_Reg_Addon {

    //Returning false here will prevent submission in form controller.
    public function pre_sub_proc($request, $params, $parent_model) {
        $form_name = 'form_' . $parent_model->form_id . "_" . $parent_model->form_number;
        $prime_data = $parent_model->get_prepared_data_primary($request);
        if (!is_user_logged_in()) {
            if (!isset($prime_data['user_email'], $prime_data['username']))
                return false;

            $email = $prime_data['user_email']->value;
            $username = $prime_data['username']->value;
            
            if(isset($prime_data['email_confirmation'])){
                $email_conf = trim((string)$prime_data['email_confirmation']->value);
                if($email !== $email_conf)
                {
                    RM_PFBC_Form::setError($form_name, RM_UI_Strings::get("ERR_EMAIL_MISMATCH"));
                    return false;
                }
            }

            RM_PFBC_Form::clearErrors($form_name);

            if (RM_Utilities::is_username_reserved($username)) {
                RM_PFBC_Form::setError($form_name, RM_UI_Strings::get("LABEL_BAN_USERNAME_MSG"));
                return false;
            }

            if (isset($prime_data['password'])) {

                $password = $prime_data['password']->value;
                if (isset($prime_data['password_confirmation'])) {
                    $password_conf = $prime_data['password_confirmation']->value;
                    if ($password !== $password_conf) {
                        RM_PFBC_Form::setError($form_name, RM_UI_Strings::get("ERR_PW_MISMATCH"));
                        return false;
                    }
                }
            }

            $valid_character_error = RM_Utilities::validate_username_characters($username, $parent_model->form_id);
            if (!empty($valid_character_error) && empty($parent_model->form_options->hide_username)) {
                RM_PFBC_Form::setError($form_name, $valid_character_error);
                return false;
            }

            $user = get_user_by('login', $username);
            if (!empty($user)) {
                $parent_model->user_exists = true;
                $username_field = RM_DBManager::get_field_by_type($parent_model->form_id, 'Username');
                $username_error = RM_UI_Strings::get("USERNAME_EXISTS");
                if (!empty($username_field)) {
                    $username_options = maybe_unserialize($username_field->field_options);
                    if (!empty($username_options->user_exists_error)) {
                        $username_error = $username_options->user_exists_error;
                    }
                }

                RM_PFBC_Form::setError($form_name, $username_error);
                return false;
            }

            $user = get_user_by('email', $email);

            if (!empty($user)) {
                $parent_model->user_exists = true;
                RM_PFBC_Form::setError($form_name, RM_UI_Strings::get("USEREMAIL_EXISTS"));
                return false;
            }


            RM_PFBC_Form::clearErrors($form_name);
            return true;
        }

        return true;
    }

    public function post_sub_proc($request, $params, $parent_model) {
        $prime_data = $parent_model->get_prepared_data_primary($request);
        $x = null;
        if (!is_user_logged_in()) {
            if (isset($params['paystate'])) {
                if ($params['paystate'] == 'pre_payment' || $params['paystate'] == 'na') {
                    if (!isset($prime_data['user_email'], $prime_data['username']))
                        return false;

                    $email = $prime_data['user_email']->value;
                    $username = $prime_data['username']->value;

                    $password_field = $parent_model->service->get_primary_field_options('userpassword', $parent_model->get_form_id());
                    if (empty($password_field))
                        $password = null;
                    else {
                        if (!isset($prime_data['password']))
                            return false;
                        $password = $prime_data['password']->value;
                    }
                    do_action('rm_subscribe_newsletter', $parent_model->get_form_id(), $request);
                    if ($params['paystate'] == 'pre_payment')
                        $user_id = $parent_model->service->register_user($username, $email, $password, $parent_model->form_id, false, $parent_model->form_options->user_auto_approval);
                    else
                        $user_id = $parent_model->service->register_user($username, $email, $password, $parent_model->form_id, true, $parent_model->form_options->user_auto_approval);

                    $parent_model->user_id = $user_id;
                    update_user_meta($user_id, 'RM_UMETA_FORM_ID', $parent_model->form_id);
                    update_user_meta($user_id, 'RM_UMETA_SUB_ID', $params['sub_detail']->submission_id);
                    update_user_meta($user_id, 'rm_activation_time', current_time('mysql'));
                    if ($parent_model->form_options->user_auto_approval == 'default') {
                        do_action('rm_user_registered', $user_id);
                    }

                    $x = array('user_id' => $user_id);
                    if (isset($request['role_as']) && !empty($request['role_as']) && in_array($request['role_as'], $parent_model->form_user_role)) {
                        $parent_model->service->get_user_service()->set_user_role($user_id, $request['role_as']);
                        $role_cost = $parent_model->get_role_cost($request['role_as']);
                        if (empty($role_cost)) {
                            $params['paystate'] = 'na';
                            $params['free_role'] = true;
                        }
                    } else {
                        if (!empty($parent_model->default_form_user_role)) {
                            $parent_model->service->get_user_service()->set_user_role($user_id, $parent_model->default_form_user_role);
                        }
                    }
                }
                $opt = new RM_Options;
                if ($params['paystate'] == 'post_payment' || $params['paystate'] == 'na') {
                    if ($parent_model->form_options->user_auto_approval == 'default') {
                        $check_setting = $opt->get_value_of('user_auto_approval');
                    } else {
                        $check_setting = $parent_model->form_options->user_auto_approval;
                    }

                    if ($check_setting == 'yes') {
                        if ($params['paystate'] == 'post_payment' && (isset($params['is_paid']) && $params['is_paid'] == true))
                            $user_id = $parent_model->service->get_user_service()->activate_user_by_id($parent_model->user_id);
                        else if ($params['paystate'] == 'na' && !empty($params['free_role'])) {
                            $user_id = $parent_model->service->get_user_service()->activate_user_by_id($parent_model->user_id);
                        }
                    }

                    if ($parent_model->form_options->auto_login) {
                        if ($check_setting == 'yes' || $check_setting == 'verify') {
                            $_SESSION['RM_SLI_UID'] = $parent_model->user_id;
                            $user = get_user_by('ID', $parent_model->user_id);
                            $login_service = new RM_Login_Service();
                            $login_service->insert_login_log(array('email' => $user->user_email, 'username_used' => $user->user_email, 'ip' => $_SERVER['REMOTE_ADDR'] === '::1' ? 'localhost' : $_SERVER['REMOTE_ADDR'], 'time' => current_time('timestamp'), 'status' => 1, 'type' => 'normal', 'result' => 'success', 'social_type' => ''));
                        }
                    }
                }
            }
        }

        if (isset($params['paystate']) && $params['paystate'] != 'post_payment') {
            if ($parent_model->service->get_setting('enable_mailchimp') == 'yes' && (!empty($parent_model->form_options->enable_mailchimp) && $parent_model->form_options->enable_mailchimp[0] == 1)) {
                $form_options_mc = $parent_model->form_options;

                if ($form_options_mc->form_is_opt_in_checkbox == 1 || (isset($form_options_mc->form_is_opt_in_checkbox[0]) && $form_options_mc->form_is_opt_in_checkbox[0] == 1))
                    $should_subscribe = isset($request['rm_subscribe_mc']) && $request['rm_subscribe_mc'][0] == 1 ? 'yes' : 'no';
                else
                    $should_subscribe = 'yes';
                if ($should_subscribe == 'yes') {
                    try {
                        $parent_model->service->subscribe_to_mailchimp($request, $parent_model->get_form_options());
                    } catch (Exception $e) {
                        
                    }
                }
            }
            /*
            if ($parent_model->service->get_setting('enable_ccontact') == 'yes' && $parent_model->form_options->enable_ccontact[0] == 1) {
                $form_options_mc = $parent_model->form_options;

                if ($form_options_mc->form_is_opt_in_checkbox_cc[0] == 1)
                    $should_subscribe = isset($request['rm_subscribe_cc']) && $request['rm_subscribe_cc'][0] == 1 ? 'yes' : 'no';
                else
                    $should_subscribe = 'yes';

                if ($should_subscribe == 'yes') {

                    try {
                        $parent_model->service->subscribe_to_ccontact($request, $parent_model->get_form_options());
                    } catch (Exception $e) {
                        
                    }
                }
            }
            */

            do_action('rm_subscribe_newsletter', $parent_model->get_form_id(), $request);

            if ($parent_model->service->get_setting('enable_aweber') == 'yes' && (!empty($parent_model->form_options->enable_aweber) && $parent_model->form_options->enable_aweber[0] == 1)) {
                $form_options_mc = $parent_model->form_options;

                if ($form_options_mc->form_is_opt_in_checkbox_aw[0] == 1)
                    $should_subscribe = isset($request['rm_subscribe_aw']) && $request['rm_subscribe_aw'][0] == 1 ? 'yes' : 'no';
                else
                    $should_subscribe = 'yes';

                if ($should_subscribe == 'yes') {

                    try {
                        $parent_model->service->subscribe_to_aweber($request, $parent_model->get_form_options());
                    } catch (Exception $e) {
                        
                    }
                }
            }
            return $x;
        }
    }

    public function hook_pre_field_addition_to_page($form, $page_no, $parent_model) {
        if (1 == $page_no) {
            if ($parent_model->preview || !is_user_logged_in()) {
                if (!empty($parent_model->form_options->form_should_user_pick) || !(isset($parent_model->form_user_role) && !empty($parent_model->form_user_role))) {
                    $role_pick = $parent_model->form_options->form_should_user_pick;
                    if ($role_pick) {
                        $role_as = empty($parent_model->form_options->form_user_field_label) ? RM_UI_Strings::get('LABEL_ROLE_AS') : $parent_model->form_options->form_user_field_label;
                        $form->addElement(new Element_Radio($role_as, "role_as", $parent_model->get_allowed_roles(), array("id" => "rm_role_selector_" . $parent_model->form_id . "_" . $parent_model->form_number, "class" => "rm_role_selector", "style" => $parent_model->form_options->style_textfield, "required" => "1", "labelStyle" => $parent_model->form_options->style_label,'data-rmfieldtype'=>"price")));
                    }
                }
            }
        }
    }

    public function hook_post_field_addition_to_page($form, $page_no, $parent_model, $editing_sub = null) {
        // Changing order of password and confirm password field
        if ($page_no == 1 && !is_user_logged_in() && (int) $parent_model->form_options->hide_username == 1):
            $elements = $form->getElements();
            $postions = array();
            if (is_array($elements)):
                foreach ($elements as $index => $element):

                    // Check for first occurence of Password type field
                    if ($element->getAttribute('name') == "password"):
                        $postions['password'] = $index;
                    endif;

                    // Get index of UserEmail element
                    if (get_class($element) == "Element_UserEmail"):
                        $postions['email'] = $index;
                    endif;

                    // Swaping password and UserEmail field order
                    if (isset($postions['email'], $postions['password']) && $postions['password']):
                        $tmpPass = $elements[$postions['password']];
                        $tmpEmail = $elements[$postions['email']];
                        $elements[$postions['email']] = $elements[$postions['password'] + 1];
                        $elements[$postions['password'] + 1] = $tmpPass;
                        $elements[$postions['password']] = $tmpEmail;

                        break;
                    endif;
                endforeach;
            endif;
            $form->setElements($elements);
        endif;
        // echo '<pre>';
        // print_r($elements);
        //$last_page_no = max(array_keys($parent_model->form_pages)) + 1;
        $last_page_no = count($parent_model->form_pages);
        if ($last_page_no == $page_no) {
            if ($parent_model->has_price_field() && !$editing_sub)
                $parent_model->add_payment_fields($form);
            
            if (!is_user_logged_in() && $parent_model->has_paid_role()) {
                $custom_role_data = $parent_model->service->get_setting('user_role_custom_data');
                if(isset($custom_role_data[$parent_model->default_form_user_role]))
                    $form->addElement(new Element_Hidden("paid_role" . $parent_model->form_id, $custom_role_data[$parent_model->default_form_user_role]->amount, array("id" => "paid_role_" . $parent_model->form_id . "_" . $parent_model->form_number, "data-rmdefrole" => $parent_model->default_form_user_role, "data-rmcustomroles" => json_encode($custom_role_data), 'data-rmfieldtype'=>"price", "data-rmfieldprice" => $custom_role_data[$parent_model->default_form_user_role]->amount)));
            }

            $check_setting = null;
            if ($parent_model->form_options->enable_captcha == 'default') {
                $check_setting = get_option('rm_option_enable_captcha');
            } else {
                $check_setting = $parent_model->form_options->enable_captcha;
            }

            if ($check_setting == 'yes')
                $form->addElement(new Element_Captcha());

            if ($parent_model->service->get_setting('enable_mailchimp') == 'yes' && $parent_model->form_options->form_is_opt_in_checkbox == 1 && (isset($parent_model->form_options->enable_mailchimp[0]) && $parent_model->form_options->enable_mailchimp[0] == 1) && !$editing_sub) {
                //This outer div is added so that the optin text can be made full width by CSS.
                $form->addElement(new Element_HTML('<div class="rm_optin_text">'));

                if ($parent_model->form_options->form_opt_in_default_state == 'Checked')
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_mc', array(1 => $parent_model->form_options->form_opt_in_text ?: RM_UI_Strings::get('MSG_SUBSCRIBE')), array("value" => 1, "class" => "rm_mc_optin_checkbox")));
                else
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_mc', array(1 => $parent_model->form_options->form_opt_in_text ?: RM_UI_Strings::get('MSG_SUBSCRIBE')), array("class" => "rm_mc_optin_checkbox")));

                $form->addElement(new Element_HTML('</div>'));
            }

            do_action('rm_show_subscribe_opt', $parent_model->form_id, $form, $editing_sub);

            /*
            if ($parent_model->service->get_setting('enable_ccontact') == 'yes' && $parent_model->form_options->form_is_opt_in_checkbox_cc[0] == 1 && $parent_model->form_options->enable_ccontact[0] == 1 && !$editing_sub) {
                //This outer div is added so that the optin text can be made full width by CSS.
                $form->addElement(new Element_HTML('<div class="rm_optin_text">'));

                if ($parent_model->form_options->form_opt_in_default_state_cc == 'Checked')
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_cc', array(1 => $parent_model->form_options->form_opt_in_text_cc ?: RM_UI_Strings::get('MSG_SUBSCRIBE')), array("value" => 1)));
                else
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_cc', array(1 => $parent_model->form_options->form_opt_in_text_cc ?: RM_UI_Strings::get('MSG_SUBSCRIBE'))));

                $form->addElement(new Element_HTML('</div>'));
            }
            */
            if ($parent_model->service->get_setting('enable_aweber') == 'yes' && !empty($parent_model->form_options->enable_aweber) && $parent_model->form_options->form_is_opt_in_checkbox_aw[0] == 1 && $parent_model->form_options->enable_aweber[0] == 1 && !$editing_sub) {
                //This outer div is added so that the optin text can be made full width by CSS.
                $form->addElement(new Element_HTML('<div class="rm_optin_text">'));

                if ($parent_model->form_options->form_opt_in_default_state_aw == 'Checked')
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_aw', array(1 => $parent_model->form_options->form_opt_in_text_aw ?: RM_UI_Strings::get('MSG_SUBSCRIBE')), array("value" => 1)));
                else
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_aw', array(1 => $parent_model->form_options->form_opt_in_text_aw ?: RM_UI_Strings::get('MSG_SUBSCRIBE'))));

                $form->addElement(new Element_HTML('</div>'));
            }

            if ($parent_model->form_options->show_total_price && $parent_model->has_price_field()) {
                $gopts = new RM_Options;
                $total_price_localized_string = RM_UI_Strings::get('FE_FORM_TOTAL_PRICE');
                $curr_symbol = $gopts->get_currency_symbol();
                $curr_pos = $gopts->get_value_of('currency_symbol_position');
                $price_formatting_data = json_encode(array("loc_total_text" => $total_price_localized_string, "symbol" => $curr_symbol, "pos" => $curr_pos));
                $form->addElement(new Element_HTML("<div class='rmrow rm_total_price' style='{$parent_model->form_options->style_label}' data-rmpriceformat='$price_formatting_data'></div>"));
            }
        }
    }

    public function get_allowed_roles($parent_model, $include_paid_roles = true) {
        global $wp_roles;
        $allowed_roles = array();
        $form_roles = array();
        $default_wp_roles = $wp_roles->get_names();
        $form_roles = $parent_model->form_user_role;
        if (is_array($form_roles)) {
            if (!empty($parent_model->default_form_user_role) && !in_array($parent_model->default_form_user_role, $form_roles))
                $form_roles[] = $parent_model->default_form_user_role;
        } else
            $form_roles[] = '';

        if (is_array($form_roles) && count($form_roles) > 0) {
            $gopts = new RM_Options;
            $custom_role_data = $parent_model->service->get_setting('user_role_custom_data');
            foreach ($form_roles as $val) {
                if (array_key_exists($val, $default_wp_roles)) {
                    $allowed_roles[$val] = $default_wp_roles[$val];
                    $paid_role_str = '';
                    if (isset($custom_role_data[$val]) && $custom_role_data[$val]->is_paid) {
                        $paid_role_str = ' (' . $gopts->get_formatted_amount($custom_role_data[$val]->amount) . ')';
                        if ($include_paid_roles)
                            $allowed_roles[$val] .= $paid_role_str;
                        else
                            unset($allowed_roles[$val]);
                    }
                }
            }
        }

        return $allowed_roles;
    }
    
    public function has_paid_role($parent_model) {
        $allowed_roles = $parent_model->get_allowed_roles();
        $custom_role_data = $parent_model->service->get_setting('user_role_custom_data');

        if (is_array($allowed_roles) && count($allowed_roles) > 0)
            foreach ($allowed_roles as $role => $disp_name) {
                if (isset($custom_role_data[$role]) && $custom_role_data[$role]->is_paid)
                    return true;
            }
        return false;
    }

    //get price of a paid role.
    public function get_role_cost($role, $parent_model) {
        $custom_role_data = $parent_model->service->get_setting('user_role_custom_data');
        if (isset($custom_role_data[$role]) && $custom_role_data[$role]->is_paid)
            return $custom_role_data[$role]->amount;
        else
            return null;
    }
    
    public function get_prepared_data_dbonly($request, $parent_model) {
        $data = array();

        foreach ($parent_model->fields as $field) {
            //if (in_array($field->get_field_type(),array('Spacing','Timer'))/*$field->get_field_type() == 'HTMLH' || $field->get_field_type() == 'HTMLP'|| $field->get_field_type() == 'HTML'|| $field->get_field_type() == 'HTMLCustomized'|| $field->get_field_type() == 'Spacing'*/)
            if(is_array($field)){
                $exit = false;
                foreach($field as $single_field){
                    if(!empty($single_field)){
                        if (in_array($single_field->get_field_type(), RM_Utilities::csv_excluded_widgets())) {
                            $exit = true;
                            continue;
                        } else {
                            $exit = false;
                        }
                        $field_data = $single_field->get_prepared_data($request);

                        if ($field_data === null)
                            continue;

                        $data[$field_data->field_id] = (object) array('label' => $field_data->label,
                                    'value' => $field_data->value,
                                    'type' => $field_data->type,
                                    'meta' => isset($field_data->meta) ? $field_data->meta : null);
                    }
                }
                if ($exit)
                    continue;
            } else {
                if(!empty($field)) {
                    if (in_array($field->get_field_type(), RM_Utilities::csv_excluded_widgets())) {
                        continue;
                    }
                    $field_data = $field->get_prepared_data($request);

                    /* if($field->get_field_type()=="HTMLCustomized"){
                      $html_field= new RM_Fields();
                      $html_field->load_from_db($field->get_field_id());
                      $field_data->value= $html_field->get_field_value();
                      if(strtolower($html_field->get_field_type())=="link")
                      {
                      $field_options=  $html_field->field_options;
                      $field_data->value= $html_field->field_options->link_type=="url" ? $html_field->field_options->link_href : get_permalink($html_field->field_options->link_page);
                      }
                      } */

                    if ($field_data === null)
                        continue;

                    $data[$field_data->field_id] = (object) array('label' => $field_data->label,
                                'value' => $field_data->value,
                                'type' => $field_data->type,
                                'meta' => isset($field_data->meta) ? $field_data->meta : null);
                }
            }
        } //die;
        return $data;
    }

}