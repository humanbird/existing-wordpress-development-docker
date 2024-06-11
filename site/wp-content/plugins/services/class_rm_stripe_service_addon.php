<?php

class RM_Stripe_Service_Addon
{
    
    public function convert_price_into_lowest_unit($price, $currency, $parent_service)
    {
        switch(strtoupper($currency))
        {
            case 'BIF':
            case 'DJF':
            case 'JPY':
            case 'KRW':
            case 'PYG':
            case 'VND':
            case 'XAF':
            case 'XPF':
            case 'CLP':
            case 'GNF':
            case 'KMF':
            case 'MGA':
            case 'RWF':
            case 'VUV':
            case 'XOF':
                return $price;
                
            default:
                return $price*100;
        }
    }
    
    public function create_payment_intent($parent_service,$data=null) {
        $submission_id= isset($_POST['sub_id']) ? absint($_POST['sub_id']) : 0;
        empty($submission_id) ? wp_send_json_error(array('msg'=>__('Submission not valid.','registrationmagic-addon'))) : '';
        $submission = new RM_Submissions();
        if(!$submission->load_from_db($submission_id)){
            wp_send_json_error(array('msg'=>__('Submission not valid.','registrationmagic-addon')));
        }
        $amount = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
        $intent_error = '';
        if(empty($amount)){
            wp_send_json_error(array('msg'=>__('Amount is not valid.','registrationmagic-addon')));
        }
        if(!class_exists('Stripe\Stripe'))
          require_once RM_ADDON_EXTERNAL_DIR . 'stripe/init.php';
        try {
            \Stripe\Stripe::setApiKey($parent_service->options->get_value_of('stripe_api_key'));
            $intent = \Stripe\PaymentIntent::create(array(
                'amount' => $parent_service->convert_price_into_lowest_unit($amount,$parent_service->currency),
                'currency' => $parent_service->currency,
                'description' => sanitize_text_field($_POST['description']),
                'automatic_payment_methods' => [
                    'enabled' => true,
                ]
            ));
        }
        catch(\Stripe\Error\RateLimit $e){
            $intent_error= array('msg'=>__('Stripe API request limit exceeded.','registrationmagic-addon'));
        }
        catch(\Stripe\Error\Authentication $e){
            $intent_error= array('msg'=>__('Authentication failed.','registrationmagic-addon'));
        }
        catch(Exception $e){
             $intent_error= array('msg'=>$e->getMessage());
        }
        if(!empty($intent_error)) {
            wp_send_json_error($intent_error);
        }
        wp_send_json_success(array('client_secret' => $intent->client_secret, 'intent_json' => $intent->jsonSerialize()));
    }

    public function after_intent_process($parent_service) {
        $submission_id= isset($_POST['sub_id']) ? absint($_POST['sub_id']) : 0;
        empty($submission_id) ? wp_send_json_error(array('msg'=>__('Submission not valid.','registrationmagic-addon'))) : '';
        $submission = new RM_Submissions();
        if(!$submission->load_from_db($submission_id)){
            wp_send_json_error(array('msg'=>__('Submission not valid.','registrationmagic-addon')));
        }
        $amount = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
        $log_id = isset($_POST['log_id']) ? absint($_POST['log_id']) : 0;
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $intent = isset($_POST['intent']) ? $_POST['intent'] : '';
        $intent_status = isset($_POST['intent_status']) ? $_POST['intent_status'] : '';
        
        $log_data= array(
            'submission_id' => $submission_id,
            'form_id' => $submission->get_form_id(),
            'txn_id' => '',
            'status' => $intent_status,
            'invoice' => (string) date("His") . rand(1234, 9632),
            'total_amount' => $amount,
            'currency' => $parent_service->currency,
            'log' => maybe_serialize($intent),
            'posted_date' => RM_Utilities::get_current_time(),
            'pay_proc' => 'stripe'
        );
        $log_entry_id = RM_DBManager::update_row('PAYPAL_LOGS', $log_id, $log_data, array('%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s'));
             
        $payment_status = $intent_status == 'succeeded' ? true : false;
        $response= apply_filters('rm_payment_completed_response', array('msg'=>'','redirect'=>''), $submission, $submission->get_form_id(), $payment_status);
        if(!empty($log_id)){
            $response['log_id']= $log_id;
        }
        
        $form = new RM_Forms();
        $form->load_from_db($submission->get_form_id());
        do_action('rm_payment_completed', $submission->get_user_email(), $form, $submission_id);
        
        wp_send_json_success($response);
    }

    public function charge($parent_service,$data=null,$pricing_details=null) {
        $amount = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
        if(empty($amount)){
            wp_send_json_error(array('msg'=>__('Amount is not valid.','registrationmagic-addon')));
        }
        $pm_id= isset($_POST['payment_method_id']) ? $_POST['payment_method_id'] : '';
        $pi_id= isset($_POST['payment_intent_id']) ? $_POST['payment_intent_id'] : '';
        empty($pm_id) && empty($pi_id) ? wp_send_json_error(array('msg'=>__('Payment request is invalid.','registrationmagic-addon'))) : '';
        $submission_id= isset($_POST['sub_id']) ? absint($_POST['sub_id']) : 0;
        empty($submission_id) ? wp_send_json_error(array('msg'=>__('Submission not valid.','registrationmagic-addon'))) : '';
        $submission = new RM_Submissions();
        if(!$submission->load_from_db($submission_id)){
            wp_send_json_error(array('msg'=>__('Submission not valid.','registrationmagic-addon')));
        }
        $log_id = isset($_POST['log_id']) ? absint($_POST['log_id']) : 0;
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        if(!class_exists('Stripe\Stripe'))
          require_once RM_ADDON_EXTERNAL_DIR . 'stripe/init.php';
        \Stripe\Stripe::setApiKey($parent_service->options->get_value_of('stripe_api_key'));
        $charge_error= '';
        try{
            if(!empty($pm_id)){
                $charge_details= array('payment_method'=>$pm_id,'confirmation_method'=>'manual','confirm'=>true, 
                                       'amount' => $parent_service->convert_price_into_lowest_unit($amount,$parent_service->currency), 'currency' => $parent_service->currency, 'description' => $description, 'metadata'=>array('Submission ID'=>$submission_id));
                $intent = \Stripe\PaymentIntent::create($charge_details);
            }
            if(!empty($pi_id)){
                $intent = \Stripe\PaymentIntent::retrieve($pi_id);
                $intent->confirm();
            }
        } 
        catch(\Stripe\Error\RateLimit $e){
            $charge_error= array('msg'=>__('Stripe API request limit exceeded.','registrationmagic-addon'));
        }
        catch(\Stripe\Error\Authentication $e){
            $charge_error= array('msg'=>__('Authentication failed.','registrationmagic-addon'));
        }
        catch(Exception $e){
             $charge_error= array('msg'=>$e->getMessage());
        }
        
        if(!empty($charge_error)){
            wp_send_json_error($charge_error);
        }
        
        if ($intent->status == 'requires_source_action' && $intent->next_action->type == 'use_stripe_sdk') {
            wp_send_json_success(array('requires_action' => true,'payment_intent_client_secret' => $intent->client_secret,'sub_id'=>$submission_id));
        } 
        
            $log_data= array('submission_id' =>$submission_id,
            'form_id' => $submission->get_form_id(),
            'txn_id' => '',
            'status' => $intent->status,
            'invoice' => (string) date("His") . rand(1234, 9632),
            'total_amount' => $amount,
            'currency' => $parent_service->currency,
            'log' => maybe_serialize($intent->jsonSerialize()),
            'posted_date' => RM_Utilities::get_current_time(),
            'pay_proc' => 'stripe');
             $log_entry_id = RM_DBManager::update_row('PAYPAL_LOGS',$log_id,$log_data , array('%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s'));
             
         $payment_status = $intent->status=='succeeded' ? true: false;
         $response= apply_filters('rm_payment_completed_response',array('msg'=>'','redirect'=>''),$submission,$submission->get_form_id(),$payment_status);
         if(!empty($log_id)){
             $response['log_id']= $log_id;
         }
         wp_send_json_success($response);
    }
    
    public function show_card_elements($details, $pricing, $parent_service){
        $gopts = new RM_Options;
        $curr_date = RM_Utilities::get_current_time();
        $invoice = (string) date("His") . rand(1234, 9632);
        //Checking if payment was of 0 value
        if($pricing->total_price <= 0.0) {
            $log_entry_id = RM_DBManager::insert_row(
                'PAYPAL_LOGS', array(
                    'submission_id' => $details->submission_id,
                    'form_id' => $details->form_id,
                    'invoice' => $invoice,
                    'status' => 'succeeded',
                    'total_amount' => $pricing->total_price,
                    'currency' => $parent_service->currency,
                    'posted_date' => $curr_date,
                    'pay_proc' => 'stripe',
                    'bill' => maybe_serialize($pricing)), array('%d', '%d', '%s', '%s', '%f', '%s', '%s', '%s', '%s')
            );

            return true;
        }
        $data=array();
        echo RM_Utilities::enqueue_external_scripts('stripe_script', esc_url_raw('https://js.stripe.com/v3/'), array());
        echo RM_Utilities::enqueue_external_scripts('rm_stripe_checkout_utility',RM_ADDON_BASE_URL. 'public/js/stripe_checkout_utility.js', array());
        $rm_admin_vars = array('nonce'=>wp_create_nonce('rm_ajax_secure'));
        wp_localize_script('rm_stripe_checkout_utility','rm_admin_vars',$rm_admin_vars);
        wp_enqueue_style('rm_stripe_checkout_style');
        if(isset($details->user_id) && !empty($details->user_id)) {
            $user_details = get_userdata($details->user_id);
            $description = "$details->form_name - $user_details->user_login - $details->user_email";
        } else {
            $description = "$details->form_name - $details->user_email";
        }
        $log_entry_id = RM_DBManager::insert_row('PAYPAL_LOGS', array('submission_id' => $details->submission_id,
                        'form_id' => $details->form_id,
                        'invoice' => $invoice,
                        'status' => $pricing->total_price<=0.0 ? 'Completed' : 'Pending',
                        'total_amount' => $pricing->total_price,
                        'currency' => $parent_service->currency,
                        'posted_date' => $curr_date,
                        'pay_proc' => 'stripe',
                        'bill' => maybe_serialize($pricing)), array('%d', '%d', '%s', '%s', '%f', '%s', '%s', '%s', '%s'));
         
        $stripe_product_title = __('Product Details','registrationmagic-addon');
        $stripe_total_price_label = __('Total :','registrationmagic-addon');
        $label = __('Please enter the details to complete the payment:','registrationmagic-addon');
        $btn_label = __('Pay','registrationmagic-addon').' '.RM_Utilities::get_formatted_price(number_format((float)$pricing->total_price, 2, '.', ''));
        $email_placeholder = __('Enter email address','registrationmagic-addon');
        $pricing_html = '';
        foreach($pricing->billing as $product) {
            $pricing_html .= "<div class=\"rm-stripe-product-info-row\"><div class=\"rm-stripe-product-name\"><span class=\"rm-stripe-product-quantity\">$product->qty x</span>$product->label </div><div class=\"rm-stripe-product-price\">".RM_Utilities::get_formatted_price(number_format((float)$product->price*(float)$product->qty, 2, '.', ''))."</div></div>";
        }
        
        if(get_site_option('rm_option_enable_tax', null) == 'yes' && $pricing->total_price > 0) {
            $product_sub_total = '<div class="rm-stripe-product-info-row rm-subtotal"><div class="rm-stripe-product-total-price">'.__('Sub Total :','registrationmagic-addon').' <span>'.RM_Utilities::get_formatted_price(number_format((float)$pricing->total_price-$pricing->tax, 2, '.', '')).'</span></div></div>';
            $product_tax_info = '<div class="rm-stripe-product-info-row rm-subtotal"><div class="rm-stripe-product-total-price">'.$gopts->get_value_of('tax_rename').' <span>'.RM_Utilities::get_formatted_price(number_format((float)$pricing->tax, 2, '.', '')).'</span></div></div>';
        } else {
            $product_sub_total = '';
            $product_tax_info = '';
        }
        
        $data['html']= "<div class=\"rm-stripe-product-info-box\"><div class=\"rm-stripe-product-info-box-wrap\">
                            <div class=\"rm-stripe-product-title\"><span><svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 0 24 24' width='24px' fill='#000000'><path d='M0 0h24v24H0V0z' fill='none'/><path d='M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z'/></svg></span>$stripe_product_title</div>"
             
                . $pricing_html . $product_sub_total. $product_tax_info. "<div class=\"rm-stripe-product-info-row\"><div class=\"rm-stripe-product-total-price\">$stripe_total_price_label <span>".RM_Utilities::get_formatted_price(number_format((float)$pricing->total_price, 2, '.', ''))."</span></div></div>
                    
                          </div>
                        </div>
                         <div class=\"rm_stripe_fields\">
                            <div class=\"rm_stripe_label\">$label</div>
                            <form id=\"rm-stripe-payment-form\" data-log-id=\"$log_entry_id\" data-total-price=\"$pricing->total_price\" data-submission-id=\"$details->submission_id\" data-description=\"$description\">                         
                            <div class=\"rm-stripe-email-label\">$email_placeholder</div>
                              <input type=\"text\" id=\"rm-stripe-email\" placeholder=\"$email_placeholder\" required/>                                  
                              <div class='rm-stripe-loader'>
                              <div class='rm-stripe-loader-overlay'></div>
                                <div class='rm-stripe-loader-dots rm-stripe-loader-1'></div>
                                <div class='rm-stripe-loader-dots rm-stripe-loader-2'></div>
                                <div class='rm-stripe-loader-dots rm-stripe-loader-3'></div>
                              </div>
                              <div id=\"rm-stripe-payment-element\">
                                <!--Stripe.js injects the Payment Element-->
                              </div>
                              <button id=\"rm-stripe-submit\" type=\"submit\">
                                <div class=\"rm-stripe-spinner rm-stripe-hidden\" id=\"rm-stripe-spinner\"></div>
                                <span id=\"rm-stripe-button-text\">$btn_label</span>
                              </button>
                              <div id=\"rm-stripe-payment-message\" class=\"rm-stripe-hidden\"></div>
                            </form>
                        </div>";
        $data['status']='do_not_redirect';
        return $data;
    }
    
    public function localize_data($parent_service){
        return array(
            'public' => $parent_service->options->get_value_of('stripe_publish_key'),
            'locale' => substr(get_locale(), 0, 2),
        );
    }
    
    public function localize_data_json($parent_service){
        $data= $parent_service->localize_data();
        wp_send_json($data);
    }
}