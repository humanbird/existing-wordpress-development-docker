<?php

class RM_Email_Service_Addon
{
    /*
     * Sending submission details to admin
     */                    
    public static function notify_submission_to_admin($params,$token='')
    {
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        
        $notification_msg= RM_Email_Service::get_notification_message($params->form_id,'form_admin_ns_notification'); 
     
        $email_content='';
        $user_email = '';
        /*
         * Loop through serialized data for submission
         */
        $email_content .= "<div class='rm-email-content-wrap' style='display: table-row-group'>";
        if (is_array($params->sub_data)) {
            foreach ($params->sub_data as $field_id => $val) {
                if(is_null($val->value) || (is_string($val->value) && trim((string)$val->value) == '')) {
                    continue;
                }
                $email_content .= '<div class="rm-email-content-row-new" style="display: table-row;"><span class="key" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;"><strong>' . $val->label . ': </strong></span><br>';

                if (is_array($val->value)) {
                    $values = '';
                    // Check attachment type field
                    if (isset($val->value['rm_field_type']) && $val->value['rm_field_type'] == 'File') {
                        unset($val->value['rm_field_type']);

                        /*
                         * Grab all the attachments as links
                         */
                        foreach ($val->value as $attachment_id) {
                            $values .= wp_get_attachment_link($attachment_id) . '    ';
                        }

                        $email_content .= '<span class="key-val" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;">' . $values . '</span><br/>';
                    }elseif (isset($val->value['rm_field_type']) && $val->value['rm_field_type'] == 'Address'){
                        unset($val->value['rm_field_type']);
                        foreach($val->value as $in =>  $value){
                           if(empty($value))
                               unset($val->value[$in]);
                        }
                        $email_content .= '<span class="key-val" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;">' . implode(', ', $val->value) . '</span><br/>';
                    } elseif ($val->type == 'Checkbox') {   
                         $email_content .= '<span class="key-val" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;">' . implode(', ',RM_Utilities::get_lable_for_option($field_id, $val->value)) . '</span><br/>';
                    } elseif ($val->type == 'URL') {
                        $email_content .= '<span class="key-val" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;"><a href="' . $val->value['url'] . '">' . $val->value['url'] . '</a></span><br/>';
                    }
//                     elseif($val->type == 'Price' ){
//                     
//                    if (count($val->value) == 0)
//                        $email_content = null;
//                    else{
//                    $values = array();
//                        foreach ($val->value as $value){
//                            $tmp = array();
//                            $tmp = explode('&times;', $value);
//                            $values[] = implode('quantity',$tmp);
//                        }
//                        $email_content .= '<span class="key-val">'.implode(', ',$values) . '</span><br/>';
//                    }
//                }
                    
                    else {
                        $email_content .= '<span class="key-val" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;">' . implode(', ', $val->value) . '</span><br/>';
                    }
                } else {
                    $primary_fields= RM_DBManager::get_primary_fields_id($params->form_id,'email');
                    if ($val->type == 'Email' && $user_email=='' && in_array($field_id,$primary_fields)){
                        $user_email = $val->value;
                    }
                    $additional_fields = apply_filters('rm_additional_fields', array());
                    if(in_array($val->type, $additional_fields)){
                        $special_fields = apply_filters('rm_additional_fields_data_email',$val->value, $val->type);
                        $email_content .= '<span class="key-val" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;">' .$special_fields. '</span><br/>';
                        
                    }
                    elseif ($val->type == 'Radio' || $val->type == 'Select') {   
                       $email_content .= '<span class="key-val" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;">' . RM_Utilities::get_lable_for_option($field_id, $val->value). '</span><br/>';
                    }
                    else
                        $email_content .= '<span class="key-val" style="display: table-cell;border: 1px solid #e9e9e9;padding: 8px;line-height: 1.42857143;vertical-align: top;">' . nl2br($val->value) . '</span><br/>';
                }

                 $email_content .= "</div>";
            }
        }
        $email_content .= "</div>";
        /*
          Set unique token */
        if ($token) {
            $email_content .= '<div class="rm-email-content-row"><span class="key">' . RM_UI_Strings::get('LABEL_UNIQUE_TOKEN_EMAIL') . ':</span><br>';
            $email_content .= '<span class="key-val">' . $token . '</span><br/>';
            $email_content .= "</div>";
        }

        $notification_msg= str_replace('{{SUBMISSION_DATA}}', $email_content, $notification_msg);
        
        $history_content = '';
        $edd_user_content = '';
        $wc_user_content = '';
        $rm_user_content = '';
        if($user_email!=''){
            //Submission History Start
            $service = new RM_Services();
            $submissions = $service->get_recent_submissions_for_user($user_email);
            $history_content = '<h3>'.__('User Submission History','registrationmagic-addon').'</h3>';
            if(count($submissions)>1){
                $i=0;
                foreach ($submissions as $submission){
                    if($i>0 && $i<6 && $submission->child_id==0){
                        $submission_arr = unserialize($submission->data);
                        //echo '<pre>';print_r($submission_arr);echo '</pre>';
                        $email_history_content = '';
                        foreach ($submission_arr as $field_id => $val) {
                            $email_history_content .= '<div class="rm-email-content-row"> <span class="key">' . $val->label . ':</span>';

                            if (is_array($val->value)) {
                                $values = '';
                                if (isset($val->value['rm_field_type']) && $val->value['rm_field_type'] == 'Address'){
                                    unset($val->value['rm_field_type']);
                                    foreach($val->value as $in =>  $value){
                                       if(empty($value))
                                           unset($val->value[$in]);
                                    }
                                    $email_history_content .= '<span class="key-val">' . implode(', ', $val->value) . '</span><br/>';
                                } elseif ($val->type == 'Checkbox') {   
                                     $email_history_content .= '<span class="key-val">' . implode(', ',RM_Utilities::get_lable_for_option($field_id, $val->value)) . '</span><br/>';
                                } elseif ($val->type == 'URL') {
                                    $email_history_content .= '<span class="key-val"><a href="' . $val->value['url'] . '">' . $val->value['url'] . '</a></span><br/>';
                                } else {
                                    $email_history_content .= '<span class="key-val">' . implode(', ', $val->value) . '</span><br/>';
                                }
                            } else {
                                if ($val->type == 'Radio' || $val->type == 'Select') {   
                                   $email_history_content .= '<span class="key-val">' . RM_Utilities::get_lable_for_option($field_id, $val->value). '</span><br/>';
                                } else {
                                    $email_history_content .= '<span class="key-val">' . $val->value . '</span><br/>';
                                }

                            }
                            $email_history_content .= "</div>";
                        }
                        
                        $history_content .= "<span style='width: 350px; display: block;' ><strong>".__('Submitted on','registrationmagic-addon').": ".date('F j, Y', strtotime($submission->submitted_on))."</strong><br>".$email_history_content."<hr></span>";
                    }
                    $i++;
                }
            }else{
                $history_content .= "<p>".__('No previous submissions from this user.','registrationmagic-addon')."</p>";
            }
            //Submission History End
            
            //EDD History Start
            $edd_user_content = '';
            if ( class_exists( 'Easy_Digital_Downloads' ) ){
                $edd_user_details = $service->get_edd_user_details($user_email);
                $edd_user_content = '<h3>'.__("EDD Details",'registrationmagic-addon').'</h3>';
                $edd_user_content .= '<p><strong>'.__("Customer Details",'registrationmagic-addon').':</strong></p>';
                if(!empty($edd_user_details)){
                    $edd_payment_details = isset($edd_user_details->payment_ids) ? $service->get_recent_edd_orders_for_user($edd_user_details->payment_ids) : array();
                    $edd_payment_content = '<p><strong>'.__("Payment History",'registrationmagic-addon').':</strong></p>';
                    $edd_payment_content .= "<table border='1' cellpadding='10' cellspacing='0' max-width='600' width='100%' align='center' style='border: 1px solid #d0d0d0;border-collapse: collapse;'><tbody align='center'><tr><th>ID</th><th>".__("Details",'registrationmagic-addon')."</th><th>".__("Date",'registrationmagic-addon')."</th><th>".__("Amount",'registrationmagic-addon')."</th><th>".__("Status",'registrationmagic-addon')."</th></tr>";
                    foreach ($edd_payment_details as $edd_payment_detail){
                        $edd_payment_content .= "<tr><td>".$edd_payment_detail['ID']."</td><td><a href='".admin_url()."edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=".$edd_payment_detail['ID']."'>".__("View Order Details",'registrationmagic-addon')."</a></td><td>".date('F j, Y', strtotime($edd_payment_detail['date']))."</td><td>".$edd_payment_detail['currency'].' '.number_format($edd_payment_detail['amount'],2)."</td><td>".$edd_payment_detail['status']."</td></tr>";
                    }
                    $edd_payment_content .= "</tbody ></table>";

                    $edd_user_content .= "<table border='1' cellpadding='10' cellspacing='0' max-width='600' width='100%' align='center' style='border: 1px solid #d0d0d0;border-collapse: collapse;'><tbody align='center'><tr><th>".__("Name",'registrationmagic-addon')."</th><th>".__("Purchases",'registrationmagic-addon')."</th><th>".__("Total Spent",'registrationmagic-addon')."</th><th>".__("Date Created",'registrationmagic-addon')."</th></tr>";
                    $edd_user_content .= "<tr><td><a href='".admin_url()."edit.php?post_type=download&page=edd-customers&view=overview&id=".$edd_user_details->id."'>".$edd_user_details->name."</a></td><td>".$edd_user_details->purchase_count."</td><td>".edd_get_currency().' '.number_format($edd_user_details->purchase_value,2)."</td><td>".date('F j, Y', strtotime($edd_user_details->date_created))."</td></tr>";
                    $edd_user_content .= "</tbody></table>";
                    $edd_user_content .= "<p>".__("Note: The total value reflects default currency set in your dashboard. For earnings in other currencies, see the table below",'registrationmagic-addon')."</p>";

                    $edd_user_content .= $edd_payment_content;
                }else{
                    $edd_user_content .= "<p>".__("No customer record found for this user.",'registrationmagic-addon')."</p>";
                    $edd_user_content .= '<p><strong>'.__("Payment History",'registrationmagic-addon').':</strong></p>';
                    $edd_user_content .= "<p>".__("No payment records found for this user.",'registrationmagic-addon')."</p>";
                }
            }                
            //EDD History End
            
            //WooCommerce History Start
            //$customer = new WC_Customer( $user_details_by_email->ID );
            $user_details_by_email = get_user_by( 'email', $user_email );
            $wc_user_content = '';
            if ( class_exists( 'WooCommerce' ) && function_exists('wc_get_orders')){
                $wc_payment_details= wc_get_orders(array('email'=>$user_email));
                $wc_order_content = '<p><strong>'.__("Order History",'registrationmagic-addon').':</strong></p>';
                $wc_order_total = 0;
                $wc_item_total = 0;
                $skip_wc_email= false;
                if(!empty($wc_payment_details)){
                    $wc_order_content .= "<table border='1' cellpadding='10' cellspacing='0' max-width='600' width='100%' align='center' style='border: 1px solid #d0d0d0;border-collapse: collapse;'><tbody align='center'><tr><th>ID</th><th>".__("Date",'registrationmagic-addon')."</th><th>Total</th><th>".__("Status",'registrationmagic-addon')."</th></tr>";
                    foreach ($wc_payment_details as $wc_payment_detail){
                        if(!method_exists($wc_payment_detail,'get_date_created') || !method_exists($wc_payment_detail,'get_total') || !method_exists($wc_payment_detail,'get_items') || !method_exists($wc_payment_detail,'get_quantity')){
                            $skip_wc_email= true;
                            break;
                        }
                        $wc_date_object = $wc_payment_detail->get_date_created();
                        $wc_order_content .= "<tr><td><a href='".admin_url()."post.php?post=".$wc_payment_detail->get_id()."&action=edit'>".$wc_payment_detail->get_id()."</a></td><td>".$wc_date_object->date('F j, Y')."</td><td>".$wc_payment_detail->get_currency().' '.number_format($wc_payment_detail->get_total(),2)."</td><td>". ucfirst($wc_payment_detail->get_status())."</td></tr>";
                        if($wc_payment_detail->get_status()=='completed'){
                            $wc_order_total += $wc_payment_detail->get_total();

                            $wc_item_details = $wc_payment_detail->get_items();
                            foreach($wc_item_details as $wc_item_detail){
                                $wc_item_total += $wc_item_detail->get_quantity();
                            }
                        }
                    }
                    $wc_order_content .= "</tbody></table>";
                }else{
                    $wc_order_content .= "<p>".__("No previous orders found for this user.",'registrationmagic-addon')."</p>";
                }

                $wc_user_content = '<h3>'.__("Woocommerce Details",'registrationmagic-addon').'</h3>';
                $wc_user_content .= '<p><strong>'.__("Customer Details",'registrationmagic-addon').':</strong></p>';
                if(!empty($user_details_by_email)){
                    $wc_user_content .= "<table border='1' cellpadding='10' cellspacing='0' max-width='600' width='100%' align='center' style='border: 1px solid #d0d0d0;border-collapse: collapse;'><tbody align='center'><tr><th>".__("Name",'registrationmagic-addon')."</th><th>".__("Orders Placed",'registrationmagic-addon')."</th><th>".__("Products Purchased",'registrationmagic-addon')."</th><th>".__("Total Spent",'registrationmagic-addon')."</th></tr>";
                    $wc_user_content .= "<tr><td><a href='".admin_url()."admin.php?page=rm_user_view&user_id=".$user_details_by_email->ID."'>".$user_details_by_email->display_name."</a></td><td>".count($wc_payment_details)."</td><td>".$wc_item_total."</td><td>".get_woocommerce_currency_symbol().' '.number_format($wc_order_total,2)."</td></tr>";
                    $wc_user_content .= "</tbody></table>";
                    $wc_user_content .= "<p>Note: ".__("The total value reflects default currency set in your dashboard. For earnings in other currencies, see the table below",'registrationmagic-addon')."</p>";
                }else{
                    $wc_user_content .= "<p>".__("This user is not registered on the site.",'registrationmagic-addon')."</p>";
                }

                $wc_user_content .= $wc_order_content;
                if($skip_wc_email){
                    $wc_user_content='';                }
            }
            //echo '<pre>';print_r($wc_payment_details); echo '</pre>';
            //WooCommerce History End
            
            //RM History Start
            //echo '<pre>';print_r($submissions); echo '</pre>';
            $rm_submission_content = '<p><strong>'.__("Submission History",'registrationmagic-addon').':</strong></p>';
            $rm_submission_content .= "<table border='1' cellpadding='10' cellspacing='0' max-width='600' width='100%' align='center' style='border: 1px solid #d0d0d0;border-collapse: collapse;'><tbody align='center'><tr><th>".__("ID",'registrationmagic-addon')."</th><th>".__("Form Name",'registrationmagic-addon')."</th><th>".__("Submitted On",'registrationmagic-addon')."</th><th>".__("Details",'registrationmagic-addon')."</th></tr>";
            
            $rm_payment_str = '';
            $rm_order_total = 0;
            $submission_count = 0;
            foreach ($submissions as $submission){
                if($submission->child_id==0){
                    $form_details= new RM_Forms();
                    $form_results = $form_details->load_from_db($submission->form_id);
                    $rm_submission_content .= "<tr><td>".$submission->submission_id."</td><td><a href='".admin_url()."admin.php?page=rm_submission_manage&rm_form_id=".$submission->form_id."'>".$form_details->get_form_name()."</a></td><td>".date('F j, Y', strtotime($submission->submitted_on))."</td><td><a href='".admin_url()."admin.php?page=rm_submission_view&rm_submission_id=".$submission->submission_id."'>".__("View Details",'registrationmagic-addon')."</a></td></tr>";



                    $parent_sub_id = $service->get_oldest_submission_from_group($submission->submission_id);
                    $payment = $service->get('PAYPAL_LOGS', array('submission_id' => $parent_sub_id), array('%d'), 'row', 0, 99999);
                    if(!empty($payment)){
                        $bill = maybe_unserialize($payment->bill);
                        $rm_payment_status = ($params->sub_id==$payment->submission_id?($payment->status=='succeeded'?__("Succeeded",'registrationmagic-addon'):__("In Progress",'registrationmagic-addon')):ucfirst((string)$payment->status));
                        $rm_txn_id = ($payment->txn_id!='' && $payment->txn_id!=0)?$payment->txn_id:$payment->invoice;
                        if(isset($bill->tax)) {
                            $rm_payment_str .= "<tr><td>".$rm_txn_id."</td><td>".date('F j, Y', strtotime((string)$payment->posted_date))."</td><td>".$payment->currency.' '.number_format(intval($payment->total_amount),2)."</td><td>".$payment->currency.' '.number_format($bill->tax,2)."</td><td>".$rm_payment_status."</td></tr>";
                        } else {
                            $rm_payment_str .= "<tr><td>".$rm_txn_id."</td><td>".date('F j, Y', strtotime((string)$payment->posted_date))."</td><td>".$payment->currency.' '.number_format(intval($payment->total_amount),2)."</td><td>".$rm_payment_status."</td></tr>";
                        }
                        if(in_array(strtolower((string)$payment->status),array('completed','succeeded'))){
                            $rm_order_total += $payment->total_amount;
                        }
                    }
                    $submission_count++;
                }
            }
            $rm_submission_content .= "</tbody></table>";
            
            $rm_payment_content = '<p><strong>'.__("Payment History",'registrationmagic-addon').':</strong></p>';
            if($rm_payment_str!=''){
                $rm_payment_content .= "<table border='1' cellpadding='10' cellspacing='0' max-width='600' width='100%' align='center' style='border: 1px solid #d0d0d0;border-collapse: collapse;'><tbody align='center'><tr><th>".__("Transaction ID/ Invoice",'registrationmagic-addon')."</th><th>".__("Date",'registrationmagic-addon')."</th><th>".__("Amount",'registrationmagic-addon')."</th><th>".__("Status",'registrationmagic-addon')."</th></tr>";
                $rm_payment_content .= $rm_payment_str;
                $rm_payment_content .= "</tbody></table>";
            }else{
                $rm_payment_content .= "<p>".__("No payment history exists for this user.",'registrationmagic-addon')."</p>";
            }
            
            $rm_user_content = '<h3>'.__('User Details','registrationmagic-addon').'</h3>';
            $rm_user_content .= '<p><strong>'.__("Account Details",'registrationmagic-addon').':</strong></p>';
            if(!empty($user_details_by_email)){
                //echo '<pre>';print_r($user_details_by_email); echo '</pre>';
                $rm_user_content .= "<table border='1' cellpadding='10' cellspacing='0' max-width='600' width='100%' align='center' style='border: 1px solid #d0d0d0;border-collapse: collapse;'><tbody align='center'><tr><th>".__("Name",'registrationmagic-addon')."</th><th>".__("Registered On",'registrationmagic-addon')."</th><th>".__("Submissions",'registrationmagic-addon')."</th><th>".__("Total Spent",'registrationmagic-addon')."</th></tr>";
                $rm_user_content .= "<tr><td><a href='".admin_url()."admin.php?page=rm_user_view&user_id=".$user_details_by_email->ID."'>".$user_details_by_email->display_name."</a></td><td>".date('F j, Y', strtotime($user_details_by_email->user_registered))."</td><td>".$submission_count."</td><td>".$gopt->get_value_of('currency').' '.number_format($rm_order_total,2)."</td></tr>";
                $rm_user_content .= "</tbody></table>";
                $rm_user_content .= "<p>Note: ".__("The total value reflects default currency set in your dashboard. For earnings in other currencies, see the table below.",'registrationmagic-addon')."</p>";
            }else{
                $rm_user_content .= "<p>".__("The user is currently not registered on the site. No user account data is available.",'registrationmagic-addon')."</p>";
            }
            
            $rm_user_content = $rm_user_content.$rm_submission_content.$rm_payment_content;
            //echo '<pre>';print_r($rm_payment_details); echo '</pre>';
            //RM History End
        }
        $notification_msg= str_replace('{{SUBMISSION_HISTORY}}', $history_content, $notification_msg);
        $notification_msg= str_replace('{{RM_EDD_DETAILS}}', $edd_user_content, $notification_msg);
        $notification_msg= str_replace('{{RM_WOO_DETAILS}}', $wc_user_content, $notification_msg);
        $notification_msg= str_replace('{{RM_USERDATA}}', $rm_user_content, $notification_msg);
        $notification_msg = $notification_msg.'<style></style>';
        $notification_msg= wpautop($notification_msg);
        
        
        $is_wc_fields = 0;
        
        $service = new RM_Services();
        $fields = $service->get_all_form_fields($params->form_id);
        foreach($fields as $field){
            if($field->field_type=='WCBilling'){
                $is_wc_fields = 1;
            }else if($field->field_type=='WCShipping'){
                $is_wc_fields = 1;
            }else if($field->field_type=='WCBillingPhone'){
                $is_wc_fields = 1;
            }
        }
        
        $user_details_by_email = get_user_by( 'email', $user_email );
        
        $form = new RM_Forms();
        $form->load_from_db($params->form_id);
        
        if($is_wc_fields==1 && $form->get_form_type()!=1 && !$user_details_by_email){
            $notification_msg .= $notification_msg.'<br><br>Note: Billing/ Shipping/ Phone number field was not update in customer profile since this user is not registered on your site yet.';
        }
        
        $notification_msg= do_shortcode(wpautop($notification_msg));
        $rm_email->message($notification_msg);
        // Prepare recipients

        $to = array();
        $header = '';

       
        $admin_email= $form->form_options->admin_email;
        $notification_override= $form->form_options->admin_notification;
        if(!empty($admin_email) && !empty($notification_override)){
            $to = explode(',',(string)$admin_email);
        }
        else if ($gopt->get_value_of('admin_notification') == "yes") {
            $to = explode(',',(string)$gopt->get_value_of('admin_email'));
        }
        
        $subject= $form->form_options->form_admin_ns_notification_sub;
        if(empty($subject))
            $subject = $params->form_name . " " . RM_UI_Strings::get('LABEL_NEWFORM_NOTIFICATION') . " ";
        $rm_email->subject($subject);
        $rm_email->useAdminFrom= false; 
        
        $from_email= $gopt->get_value_of('an_senders_email');
        $from_email= trim((string)$from_email);
        if($from_email=="{{useremail}}"){
            $primary_fields= RM_DBManager::get_primary_fields_id($params->form_id,'email');
            if(count($primary_fields)){
                $from_email= isset($params->sub_data[$primary_fields[0]]) ? $params->sub_data[$primary_fields[0]]->value : '';
                $replyto_email= $from_email;
            }
        } else {
            $primary_fields= RM_DBManager::get_primary_fields_id($params->form_id,'email');
            if(count($primary_fields)){
                $replyto_email= isset($params->sub_data[$primary_fields[0]]) ? $params->sub_data[$primary_fields[0]]->value : '';
            }
        }
        $disp_name= $gopt->get_value_of('an_senders_display_name'); 
        $dname= '';
        if(stristr($disp_name, '{{user}}')){
            $sub_data= $params->sub_data;
            $first_name='';
            $last_name='';
            $user_email;
            if(!empty($sub_data)){
                foreach($sub_data as $fdata){
                     if($fdata->type=='Fname'){
                        $first_name=  $fdata->value;
                     } else if($fdata->type=='Lname'){
                         $last_name=  $fdata->value;
                     }  
                }
            }
            $dname= $first_name.' '.$last_name;
            if(trim((string)$dname)==''){
                $primary_fields= RM_DBManager::get_primary_fields_id($params->form_id,'email');
                $dname= isset($params->sub_data[$primary_fields[0]]) ? $params->sub_data[$primary_fields[0]]->value : '';
            }
        }
        $disp_name= str_replace('{{user}}', $dname, $disp_name);
        if(empty($disp_name))
        {
            $disp_name= get_bloginfo('name', 'display');
        }

       // $from_email = $disp_name . " <" . $from_email . ">";
        $rm_email->set_from_name($disp_name);
        $rm_email->from($from_email);
        $rm_email->reply_to($replyto_email, $disp_name);
        $rm_email->attach(array($params->attachment));
        
        foreach($to as $recepient)
        {
            $rm_email->to($recepient);
            if($rm_email->send())
                $params->sent_successfully = true;     
            else
                $params->sent_successfully = false;     
            
            RM_Email_Service::save_sent_emails($params,$rm_email,RM_EMAIL_POSTSUB_ADMIN);
            
        }
        
    }
    
    /*
     * Sending user activation link to admin
     */
    public static function notify_admin_to_activate_user($params)
    {
        // Check if it is disabled from custom filter
        $enabled = apply_filters('rm_user_activation_link_to_admin',true,$params);
        if(empty($enabled))
            return;
        
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        $user_email = $params->email;
        
        if(isset($params->form_id))        
            $notification_msg= RM_Email_Service::get_notification_message($params->form_id,'form_activate_user_notification'); 
        else    
            $notification_msg= RM_Email_Service::get_notification_message('social_media','form_activate_user_notification');
        
        
        $notification_msg = str_replace('{{SITE_NAME}}', get_bloginfo('name', 'display'), $notification_msg);
        $notification_msg = str_replace('%SITE_NAME%', get_bloginfo('name', 'display'), $notification_msg);
        
        if(isset($params->username)){
        $notification_msg = str_replace('{{USER_NAME}}', $params->username, $notification_msg);
        $notification_msg = str_replace('%USER_NAME%', $params->username, $notification_msg);
        }
        else{        
        $notification_msg = str_replace('{{USER_NAME}}', '', $notification_msg);
        $notification_msg = str_replace('%USER_NAME%','', $notification_msg);        
        }
       
        if(isset($params->email)){
        $notification_msg = str_replace('{{USER_EMAIL}}', $user_email, $notification_msg);
        $notification_msg = str_replace('%USER_EMAIL%', $user_email, $notification_msg);}
        else{
         $notification_msg = str_replace('{{USER_EMAIL}}', '', $notification_msg);
        $notification_msg = str_replace('%USER_EMAIL%', '', $notification_msg);
        }
         
        $notification_msg = str_replace('{{ACTIVATION_LINk}}', $params->link, $notification_msg);
        $notification_msg = str_replace('%ACTIVATION_LINk%', $params->link, $notification_msg);
        //Fix for lower case 'k'
        $notification_msg = str_replace('{{ACTIVATION_LINK}}', $params->link, $notification_msg);
        $notification_msg = str_replace('%ACTIVATION_LINK%', $params->link, $notification_msg);
        $notification_msg= do_shortcode(wpautop($notification_msg));
        $notification_msg = apply_filters('rm_user_activation_msg_to_admin',$notification_msg,$params);
        $rm_email->message($notification_msg);
        
        $form= new RM_Forms();
        $form->load_from_db($params->form_id);        
        $form_options= $form->form_options;
        
        $subject=$form_options->form_activate_user_notification_sub;
        if(empty($subject))
            RM_UI_Strings::get('MAIL_ACTIVATE_USER_DEF_SUB');
        $rm_email->subject($subject);
        //$rm_email->to(get_option('admin_email'));
        $rm_email->from($gopt->get_value_of('senders_email_formatted'));
        
        $to = array();
        $to = explode(',',(string)$gopt->get_value_of('admin_email'));

        foreach($to as $recepient)
        {
            $rm_email->to($recepient);
            if($rm_email->send())
                $params->sent_successfully = true;     
            else
                $params->sent_successfully = false;     

            RM_Email_Service::save_sent_emails($params,$rm_email,RM_EMAIL_USER_ACTIVATION_ADMIN);
        }
        
        /*
        if($rm_email->send())
            $params->sent_successfully = true;     
        else
            $params->sent_successfully = false;     
        
        RM_Email_Service::save_sent_emails($params,$rm_email,RM_EMAIL_USER_ACTIVATION_ADMIN);
        */
    }
    
    public static function send_activation_link($user_id){
        // Check if activation link is configured
        $gopts= new RM_Options();
        $user_auto_approval= $gopts->get_value_of('user_auto_approval');
        
        if($user_auto_approval!='verify')
            return;
        
        $user_status= get_user_meta($user_id,'rm_user_status',true);
        if(empty($user_status))
            return;
        
        $sub_page_id = $gopts->get_value_of('front_sub_page_id');   
        $sub_page_url= get_permalink($sub_page_id);
        $random_number= wp_rand(99999,99999999);
        $hash = md5( $random_number );
        $url= add_query_arg( array(
                    'rm_user' => $user_id,
                    'rm_hash' => $hash
               ), $sub_page_url );
        $url = '<a href="'.$url.'">'.RM_UI_Strings::get('LABEL_CLICK_HERE').'</a>';
        $form_id= absint(get_user_meta($user_id,'RM_UMETA_FORM_ID',true));
   
        if(empty($form_id))
            return;
        
        $form= new RM_Forms();
        $form->load_from_db($form_id);
        $form_options= $form->get_form_options();
        $act_link_message= $form_options->act_link_message;
        if(empty($act_link_message))
            $act_link_message= RM_UI_Strings::get('DEFAULT_ACT_LINK_MSG_VALUE');
        update_user_meta( $user_id, 'rm_activation_hash', $hash );
        update_user_meta( $user_id, 'rm_activation_time', current_time('mysql'));
        $user_info = get_userdata($user_id);   
        
        $subject= $form_options->act_link_sub;
        if(empty($subject))
            $subject = __("Email Verification",'registrationmagic-addon'); 
        $message= str_replace(array('{{EMAIL_VERIFICATION_LINK}}','{{EMAIL_VERIFICATION_CODE}}','{{EMAIL_VERIFICATION_EXPIRY}}'), array($url,$hash,$gopts->get_value_of('acc_act_link_expiry')), $act_link_message);
        //Fix for older spelling
        $message= str_replace('{{EMAIL_VERFICATION_LINK}}', $url, $message);
        $rm_email= new RM_Email();
        $message= do_shortcode(wpautop($message));
        $rm_email->message($message);
        $rm_email->subject($subject);
        $rm_email->to($user_info->user_email);
        $rm_email->from($gopts->get_value_of('senders_email_formatted'));
        $rm_email->send();
    }
    
    public static function send_2fa_otp($options){
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        $rm_email->message($options['message']);
        $rm_email->subject(__('OTP','registrationmagic-addon'));
        $user= get_user_by('login', $options['username']);
        $rm_email->to($user->user_email);
        $rm_email->from($gopt->get_value_of('senders_email_formatted'));
        $rm_email->send();
    }
    public static function notification_report_email($notification, $data){
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        $email_content = $notification->email_content;
        $email_subject = $notification->email_subject;
        $site_name = '<a href="'.$data->site_url.'">'.$data->site_name.'</a>';
        if($email_subject):
            $email_subject = str_replace('{{REPORT_NAME}}', $data->report_name, $email_subject);
        endif;
        if($email_content):
            $email_content = str_replace('{{REPORT_NAME}}', $data->report_name, $email_content);
            $email_content = str_replace('{{COUNT}}', $data->total_records, $email_content);
            $email_content = str_replace('{{DATE_RANGE}}', $data->date_range, $email_content);
            $email_content = str_replace('{{NEXT_EXECUTION}}', $data->next_exe, $email_content);
            $email_content = str_replace('{{SITE_NAME}}', $site_name, $email_content);
        endif;
        $result = false;
        if(is_array($data->receivers)):
            foreach($data->receivers as $receiver):
                $rm_email->subject($email_subject);
                $rm_email->message($email_content);
                $rm_email->attach(array($data->csv));
                $rm_email->to($receiver);
                $rm_email->from($gopt->get_value_of('senders_email_formatted'));
                $sent = $rm_email->send();
                if($sent){
                    $result = true;
                }
                else{
                    $result = false;
                }
            endforeach;
        endif;
        
    }
    public static function notify_payment_invoice_to_user($user_email, $form, $sub_id){
        $params = new stdClass();
        $gopt = new RM_Options();
        $rm_email = new RM_Email();
        
        $enabled_invoice = $gopt->get_value_of('enable_email_invoice');
        if(!$enabled_invoice) return false;
        $user = get_user_by('email', $user_email);
        $params->form_id = $form_id = $form->get_form_id();
        if(!empty($user) && !empty($user->first_name)) {
            $first_name = $user->first_name;
        } else {
            $first_name = $user_email;
        }
        $submission = new RM_Submissions;
        $submission->load_from_db($sub_id);
        $service = new RM_Services;
        $payments = $service->get('PAYPAL_LOGS', array('submission_id' => $submission->get_submission_id()), array('%d'), 'row', 0, 99999);      
        $payment_status = strtolower($payments->status);
        if(!in_array($payment_status, array('completed','succeeded'))) {
            return false;
        }
        $invoice_url = get_temp_dir().'Invoice-'.$payments->invoice.'.pdf';
        $payment_service = new RM_Payments_Service;
        $payment_service->output_pdf_for_invoice($submission,array('name' => $invoice_url, 'type' => 'F')); 
            
        $notification_msg = RM_Email_Service::get_notification_message($form_id,'form_user_payment_invoice');
        $notification_msg = str_replace('{{FORM_NAME}}',$form->get_form_name(),$notification_msg);
        $notification_msg = str_replace('{{FIRST_NAME}}',$first_name,$notification_msg);
        $notification_msg = do_shortcode(wpautop($notification_msg));
        $rm_email->message($notification_msg);
        $form_options = $form->form_options;
        $subject = $form_options->form_user_payment_invoice_sub;
        if(empty($subject))
            $subject = 'Invoice for {{FORM_NAME}}';
        $subject = str_replace('{{FORM_NAME}}',$form->get_form_name(),$subject);
        $rm_email->subject($subject);
        $rm_email->to($user_email);
        $rm_email->from($gopt->get_value_of('senders_email_formatted'));
        $rm_email->attach(array($invoice_url));
        if($rm_email->send())
            $params->sent_successfully = true;     
        else
            $params->sent_successfully = false;     
        
        RM_Email_Service::save_sent_emails($params,$rm_email,RM_EMAIL_USER_INVOICE);
        
        return $params->sent_successfully;
    }
}