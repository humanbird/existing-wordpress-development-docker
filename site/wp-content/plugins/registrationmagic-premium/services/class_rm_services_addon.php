<?php

class RM_Services_Addon {
    
    public function manage_form_page($action, $form_id, $parent_service, $page_no = null, $new_page_name = null) {
        $form = new RM_Forms;
        $form->load_from_db($form_id);
        $fopts = $form->get_form_options();
        $form_pages = $fopts->form_pages;
        $ordered_form_pages = $fopts->ordered_form_pages;

        switch ($action) {
            case 'add_page':
                if ($form_pages == null) {
                    $form_pages = array('Page 1', 'Page 2');
                    $ordered_form_pages = array(0, 1);
                } else {
                    $total_page = count($form_pages);

                    $new_page_no = 0;
                    foreach ($form_pages as $index => $fpage) {
                        if ($index >= $new_page_name)
                            $new_page_no = $index;
                    }
                    $new_page_no += 2;
                    $last_added_page_no = count($form_pages);
                    $form_pages[] = 'Page ' . $new_page_no;
                    if (!$ordered_form_pages) {
                        $ordered_form_pages = array_keys($form_pages);
                    } else {
                        //$ordered_form_pages[] = $last_added_page_no;
                        array_push($ordered_form_pages, strval($new_page_no - 1));
					}
                }
                break;

            case 'delete_page':
                if ($form_pages == null || !$page_no) {
                    return;
                } else {
                    if ($page_no == 1)
                        return; //can't delete first page.
                    if (isset($form_pages[$page_no - 1])) {
                        RM_DBManager::remove_rows_for_page($page_no, $form_id);
                        RM_DBManager::remove_fields_for_page($page_no, $form_id);
                        unset($form_pages[$page_no - 1]);
                        $page_to_be_removed = array_search($page_no - 1, $ordered_form_pages);
                        if (isset($ordered_form_pages[$page_to_be_removed]))
                            unset($ordered_form_pages[$page_to_be_removed]);
                        //remove holes
                        $ordered_form_pages = array_values($ordered_form_pages);
                    }
                }
                break;

            case 'rename_page':
                if ($form_pages == null) {
                    $form_pages = array('Page 1');
                }
                if (!$page_no || !$new_page_name) {
                    return;
                } else {
                    if (isset($form_pages[$page_no - 1]))
                        $form_pages[$page_no - 1] = $new_page_name;
                }

                break;
        }

        $x = (object) array('form_pages' => $form_pages, 'ordered_form_pages' => $ordered_form_pages);
        $form->set_form_options($x);
        $form->update_into_db();
        return count($form_pages);
    }

    public function get_custom_fields($user_email, $parent_service) {
        $field_ids = array();
        $forms = array();
        $custom_fields = array();

        $submissions = $parent_service->get('SUBMISSIONS', array('user_email' => $user_email), array('%s'), 'results', 0, 999999, '*', null, false);

        if (!$submissions)
            return false;

        if (is_array($submissions) || is_object($submissions))
            foreach ($submissions as $submission) {
                if (!in_array($submission, $forms)) {
                    $forms[] = $submission->form_id;
                    $result = $parent_service->get('FIELDS', array('form_id' => $submission->form_id, 'field_show_on_user_page' => 1), array('%s', '%d'), 'results', 0, 999999, '*', null, false);
                    if ($result){
                        $data = maybe_unserialize($submission->data);
                        $field_ids[$submission->submission_id] = (object)array('field' => $result, 'sub_data' => $data);                                            
                    }
                }
            }

        foreach ($field_ids as $submission_id => $res) {
            foreach ($res->field as $f_row) {

                $result = $parent_service->get('SUBMISSION_FIELDS', array('submission_id' => $submission_id, 'field_id' => $f_row->field_id), array('%d', '%d'), 'var', 0, 999999, 'value', null, false);

                if ($result) {
                    $custom_fields[$f_row->field_id] = new stdClass();
                    $custom_fields[$f_row->field_id]->label = $f_row->field_label;
                    $custom_fields[$f_row->field_id]->value = $result;
                    $custom_fields[$f_row->field_id]->type = $f_row->field_type;
                    $custom_fields[$f_row->field_id]->is_editable = $f_row->field_is_editable;
                    $custom_fields[$f_row->field_id]->form_id = $f_row->form_id;
                    $custom_fields[$f_row->field_id]->meta = isset($res->sub_data[$f_row->field_id], $res->sub_data[$f_row->field_id]->meta) ? $res->sub_data[$f_row->field_id]->meta: null;//$f_row->form_id;
                }
            }
        }

        if (count($custom_fields) == 0)
            return null;

        return $custom_fields;
    }
    
    public function get_editable_fields_for_admin($form_id, $parent_service)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'rm_fields';
        if ((int) $form_id) {
            $db_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$table_name` WHERE `form_id` = %d AND `field_type` != 'Price' ORDER BY `page_no` ASC, `field_order` ASC",$form_id));
 
            if (!$db_fields || !is_array($db_fields))
                return null;
 
            $fields = array();
            foreach ($db_fields as $db_field) {
                $fields[$db_field->field_id] = $db_field;
            }
            return $fields;
        }
        return false;
    }

    public function output_pdf_for_submission(RM_Submissions $submission, $parent_service, $outputconf = array('name' => 'rm_submission.pdf', 'type' => 'D')) {
        
        $data = new stdClass();

        $settings = new RM_Options;

        $data->submission = $submission;

        $data->payment = $parent_service->get('PAYPAL_LOGS', array('submission_id' => $submission->get_submission_id()), array('%d'), 'row', 0, 99999);

        if ($data->payment != null) {
            $data->payment->total_amount = $settings->get_formatted_amount($data->payment->total_amount, $data->payment->currency);
            $bill = maybe_unserialize($data->payment->bill);
            if(isset($bill->tax)) {
                $data->payment->tax = $settings->get_formatted_amount($bill->tax, $data->payment->currency);
            }
        }

        /*
         * Check submission type
         */
        $form = new RM_Forms();
        $form->load_from_db($submission->get_form_id());
        $form_type = $form->get_form_type() == "1" ? __('Registration','registrationmagic-addon') : __('Non WP Account','registrationmagic-addon');
        $data->form_type = $form_type;
        $data->form_type_status = $form->get_form_type();
        $data->form_name = $form->get_form_name();
        $data->form_is_unique_token = $form->get_form_is_unique_token();
        $data->tax_label = $settings->get_value_of('tax_rename');

        /*
         * User details if form is registration type
         */
        if ($form->get_form_type() == "1") {
            $email = $submission->get_user_email();
            if ($email != "") {
                $user = get_user_by('email', $email);
                $data->user = $user;
            }
        }

        $mv_handler = new RM_Model_View_Handler;
        $view = $mv_handler->setView('print_submission');
        $html = $view->read($data);
        $header_data = $parent_service->get_headerdata();
        RM_Utilities::create_pdf($html, $header_data, $outputconf);
    }

    public function get_headerdata($parent_service) {
        $headerdata = array();
        $g_opt = new RM_Options();
        $logo = $g_opt->get_value_of('sub_pdf_header_img');
        if (is_numeric($logo)) {
            $logo_url = get_attached_file($logo);
            if ($logo_url)
                $headerdata['logo'] = $logo_url;
            else
                $headerdata['logo'] = null;
        }
        $title = $g_opt->get_value_of('sub_pdf_header_text');
        $headerdata['header_text'] = $title;
        $headerdata['title'] = __('Submission','registrationmagic-addon');

        return $headerdata;
    }
    
    public function get_recent_submissions_for_user($user_email, $parent_service, $form_ids = array()) {
        return RM_DBManager::get_recent_submissions_for_user($user_email, $form_ids);
    }
    
    public function get_edd_user_details($user_email, $parent_service) {
        return RM_DBManager::get_edd_user_details($user_email);
    }
    public function get_recent_edd_orders_for_user($payment_ids, $parent_service) {
        return RM_DBManager::get_recent_edd_orders_for_user($payment_ids);
    }
    
    public function get_custom_statuses($submission_id,$form_id, $parent_service) {
        return RM_DBManager::get_custom_statuses($submission_id,$form_id);
    }
    
    public function update_custom_statuses($status_index,$submission_id,$form_id,$action,$parent_service,$clear_index=array()) {
        return RM_DBManager::update_custom_statuses($status_index,$submission_id,$form_id,$action,$clear_index);
    }
    
    //Update submit field config for specified form
    public function update_submit_field_config($form_id, $config, $parent_service){ 
        $form = new RM_Forms;
        if($form->load_from_db($form_id)) {
            $form->form_options->no_prev_button = ($config['hide_prev_btn'] === "true") ? 1: 0;
            $form->form_options->form_btn_align = $config['btn_align'];
            
            $sub_btn = trim((string)$config['submit_btn_label']);
            $next_btn = trim((string)$config['next_btn_label']);
            $prev_btn = trim((string)$config['prev_btn_label']);
            
            if($next_btn)
                $form->form_options->form_next_btn_label = $next_btn;
            
            if($prev_btn)
                $form->form_options->form_prev_btn_label = $prev_btn;            
            
            if($sub_btn)
                $form->form_options->form_submit_btn_label = $sub_btn;
            
            $form->update_into_db();
        }
    }
    
    public function get_editable_rows($form_id, $parent_service, $is_admin = false) {
        global $wpdb;
        $table_name = $wpdb->prefix.'rm_fields';
        $rows = $parent_service->get_all_form_rows($form_id);
        $editable_rows = array();
        if(!empty($rows)) {
            foreach($rows as $row) {
                if(!empty($row->field_ids)) {
                    if($is_admin) {
                        $db_fields = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `field_id` IN (".implode(",",maybe_unserialize($row->field_ids)).") AND `field_type` != 'Price' ORDER BY `page_no` ASC");
                    } else {
                        $db_fields = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `field_id` IN (".implode(",",maybe_unserialize($row->field_ids)).") AND (`field_is_editable` = 1 OR `is_field_primary` = 1) AND `field_type` != 'Price' ORDER BY `page_no` ASC");
                    }
                
                    if (!empty($db_fields))
                        array_push($editable_rows, $row);
                    else
                        continue;
                }
            }
        }
        
        return $editable_rows;
    }
    
    public function create_premium_form($model, $service, $request, $params, $template){
        if($template=='cp1'){
            $model->form_options->form_pages = array('Basic Details', 'Select Product');
        }elseif($template=='cp2'){
            $model->form_options->form_pages = array('Basic Details');
        }elseif($template=='cp4'){
            $model->form_options->form_pages = array('Child Details','Parent Details');
        }elseif($template=='rp1'){
            $model->form_options->form_pages = array('Basic Details', 'Contact Details', 'Address');
        }
        $form_id = $model->insert_into_db();
        $form = new RM_Forms();
        $form->load_from_db($form_id);
        $form_type = $form->get_form_type();

        $service->add_template_form_fields($form_id, $form_type, $template);

        return $form_id;
    }
    
}