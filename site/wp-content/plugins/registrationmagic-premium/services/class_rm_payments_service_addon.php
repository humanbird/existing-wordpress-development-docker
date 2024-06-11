<?php

class RM_Payments_Service_Addon
{
    
    public function output_pdf_for_invoice(RM_Submissions $submission, $outputconf, $parent_service) {
        $data = new stdClass();
        $settings = new RM_Options;
        $data->submission = $submission;
        $data->payment = $parent_service->get('PAYPAL_LOGS', array('submission_id' => $submission->get_submission_id()), array('%d'), 'row', 0, 99999);
        
        if ($data->payment != null){
            $data->payment->total_amount = $settings->get_formatted_amount($data->payment->total_amount, $data->payment->currency);
        }
        $form = new RM_Forms();
        $form->load_from_db($submission->get_form_id());
        $form_type = $form->get_form_type() == "1" ? __('Registration','registrationmagic-addon') : __('Non WP Account','registrationmagic-addon');
        $data->form_type = $form_type;
        $data->form_type_status = $form->get_form_type();
        $data->form_name = $form->get_form_name();
        $data->form_is_unique_token = $form->get_form_is_unique_token();
        $service =  new RM_Setting_Service;
        $service->set_model(new RM_Options);
        $data->options = $service->get_options();
        if ($form->get_form_type() == "1") {
            $email = $submission->get_user_email();
            if ($email != "") {
                $user = get_user_by('email', $email);
                $data->user = $user;
            }
        }

        $mv_handler = new RM_Model_View_Handler;
        $view = $mv_handler->setView('payments_invoice');
        $html = $view->read($data);
        $header_data = $this->get_headerdata();
        $pdf_setting = $this->get_pdf_setting();
        RM_Utilities_Addon::create_invoice_pdf($header_data, $outputconf, $pdf_setting, $html);
    }
    public function get_headerdata() {
        $headerdata = array();
        $g_opt = new RM_Options();
        $logo = $g_opt->get_value_of('invoice_company_logo');
        if (is_numeric($logo)) {
            $logo_url = get_attached_file($logo);
            if ($logo_url)
                $headerdata['logo'] = $logo_url;
            else
                $headerdata['logo'] = null;
        }
        $title = $g_opt->get_value_of('sub_pdf_header_text');
        $headerdata['header_text'] = $title;
        $headerdata['title'] = __('Invoice','registrationmagic-addon');

        return $headerdata;
    }
    public function get_pdf_setting(){
        $pdf_setting = array();
        $g_opt = new RM_Options();
        $pdf_setting['left_margin'] = $g_opt->get_value_of('invoice_left_margin') !='' ? $g_opt->get_value_of('invoice_left_margin') : 5;
        $pdf_setting['top_margin'] = $g_opt->get_value_of('invoice_top_margin') !='' ? $g_opt->get_value_of('invoice_top_margin') : 5;
        $pdf_setting['right_margin'] = $g_opt->get_value_of('invoice_right_margin') !='' ? $g_opt->get_value_of('invoice_right_margin') : 5;
        $pdf_setting['enable_footer'] = $g_opt->get_value_of('invoice_enable_footer');
        $pdf_setting['footer_text'] = $g_opt->get_value_of('invoice_footer_text');
        $pdf_setting['font_family'] = $g_opt->get_value_of('invoice_font') != '' ? $g_opt->get_value_of('invoice_font') : 'helvetica';
        return $pdf_setting;
    }
    
}