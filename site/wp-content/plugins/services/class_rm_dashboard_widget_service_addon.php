<?php

class RM_Dashboard_Widget_Service_Addon {
    
    public function get_latest_attachments(){
        $service = new RM_Attachment_Service;
        $data = new stdClass();
        $form_ids = RM_Utilities::get_forms_dropdown($service);
        $attachments_ids = array();
        if(count($form_ids)){
            foreach($form_ids as $form_id => $form_name):
            $attachments = $service->get_all_form_attachments($form_id, $return_type = 'ids', $limit = 5);
            //print_r($attachments);
            if(is_array($attachments)){
                if(count($attachments)){
                    foreach($attachments as $ids){
                        $attachments_ids[] = $ids;
                    }
                }
            }
            endforeach;
        }
        if(count($attachments_ids)){
            rsort($attachments_ids);
        
        }
        if(count($attachments_ids) > 5){
            $attachments_ids = array_slice($attachments_ids,0,5);
        }
        return $attachments_ids;
    }
    public function get_latest_payments(){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $payment_table_name = RM_Table_Tech::get_table_name_for('PAYPAL_LOGS');
        $forms_table_name = RM_Table_Tech::get_table_name_for('FORMS');
        $qry = "";
        $interval_string = "";
        $limit_string = "";
        $status_string = "";
        $results = new stdClass;
        $limit_string = "LIMIT 5";
        
        $interval_string = " ORDER BY $table_name.submission_id DESC ".$limit_string;
        $qry = "SELECT * FROM `$table_name` INNER JOIN `$forms_table_name` ON $forms_table_name.form_id = $table_name.form_id INNER JOIN `$payment_table_name` ON $payment_table_name.submission_id = $table_name.submission_id $interval_string";
        $payments = $wpdb->get_results($qry);
        return $payments; 
    }

}