<?php
if (!defined('WPINC')) {
    die('Closed');
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//$data [] = 
$curr_arr = array('USD' => __("US Dollars",'registrationmagic-addon'),
    'EUR' => __("Euros",'registrationmagic-addon'),
    'GBP' => __("Pounds Sterling",'registrationmagic-addon'),
    'AUD' => __("Australian Dollars",'registrationmagic-addon'),
    'BRL' => __("Brazilian Real",'registrationmagic-addon'),
    'CAD' => __("Canadian Dollars",'registrationmagic-addon'),
    'HRK' => __("Croatian Kuna",'registrationmagic-addon'),
    'CZK' => __("Czech Koruna",'registrationmagic-addon'),
    'DKK' => __("Danish Krone",'registrationmagic-addon'),
    'HKD' => __("Hong Kong Dollar",'registrationmagic-addon'),
    'HUF' => __("Hungarian Forint",'registrationmagic-addon'),
    'IDR' => __("Indonesian Rupiah",'registrationmagic-addon'),
    'ILS' => __("Israeli Shekel",'registrationmagic-addon'),
    'JPY' => __("Japanese Yen",'registrationmagic-addon'),
    'MYR' => __("Malaysian Ringgits",'registrationmagic-addon'),
    'MXN' => __("Mexican Peso",'registrationmagic-addon'),
    'NZD' => __("New Zealand Dollar",'registrationmagic-addon'),
    'NOK' => __("Norwegian Krone",'registrationmagic-addon'),
    'PHP' => __("Philippine Pesos",'registrationmagic-addon'),
    'PLN' => __("Polish Zloty",'registrationmagic-addon'),
    'SGD' => __("Singapore Dollar",'registrationmagic-addon'),
    'SEK' => __("Swedish Krona",'registrationmagic-addon'),
    'CHF' => __("Swiss Franc",'registrationmagic-addon'),
    'TWD' => __("Taiwan New Dollars",'registrationmagic-addon'),
    'THB' => __("Thai Baht",'registrationmagic-addon'),
    'INR' => __("Indian Rupee",'registrationmagic-addon'),
    'TRY' => __("Turkish Lira",'registrationmagic-addon'),
    'RIAL' => __("Iranian Rial",'registrationmagic-addon'),
    'RON' => __("Romanian Leu",'registrationmagic-addon'),
    'RUB' => __("Russian Rubles",'registrationmagic-addon'),
    'NGN' => __("Nigerian Naira",'registrationmagic-addon'),
    'ZAR' => __("South African Rand",'registrationmagic-addon'),
    'ZMW' => __("Zambian Kwacha",'registrationmagic-addon'),
    'GHS' => __("Ghanaian cedi",'registrationmagic-addon'),
    'KES' => __("Kenyan Shilling",'registrationmagic-addon'),
    'UGX' => __("Ugandan Shilling",'registrationmagic-addon'),
    'TZS' => __("Tanzanian Shilling",'registrationmagic-addon'),
    'OMR' => __("Omani Riyal",'registrationmagic-addon')
    );
    
    $selected_default_payment = isset($data['default_payment_method']) ? $data['default_payment_method'] : '';
    $enabled_payments = isset($data['payment_gateway']) && !empty($data['payment_gateway']) ? $data['payment_gateway'] : array('paypal');
    if(count($enabled_payments)){
        if(!in_array($selected_default_payment,$enabled_payments)){
           $selected_default_payment = $enabled_payments[0]; 
        }
    }
$ssl_available = RM_Utilities::is_ssl();
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $gopts = new RM_Options;
        $include_stripe= $gopts->get_value_of('include_stripe');
//PFBC form
        $form = new RM_PFBC_Form("options_payment");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));

        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get('GLOBAL_SETTINGS_PAYMENT')));
        $form->addElement(new Element_HTML('<div class="rm_payment_guide"><a target="_blank" href="https://registrationmagic.com/setup-payments-on-registrationmagic-form-using-products/"><span class="dashicons dashicons-book-alt"></span>'.RM_UI_Strings::get('LABEL_PAYMENTS_GUIDE'). '</a></div></div>'));
        $config_field = new Element_HTML('<a onclick="rm_open_payproc_config(this)" class="rm-payment-setting"><span class="material-icons">settings</span></a><a class="rm_default_list" onclick="rm_make_default_payment(this)">'.RM_UI_Strings::get('LABEL_MAKE_DEFAULT').'</a>');
        
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_PAYMENT_PROCESSOR'), "payment_gateway", $data['pay_procs_options'], array("value" => $data['payment_gateway'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_PROCESSOR')), array('exclass_row'=>'rm_pricefield_checkbox','sub_element'=>$config_field)));
        if(!$ssl_available){
            $form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqntice_row"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_jqnotice_text">'.__('SSL encryption is not available on server! Can not use Stripe and Authorize.net.','registrationmagic-addon').'</div></div>'));
        }
        else
        {
            if($include_stripe!='yes')
                $form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqntice_row"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_jqnotice_text">'.__('To enable Stripe payments you must include Stripe library from Global Settings --> Advanced Options.','registrationmagic-addon').'</div></div>'));
        }
            
        
        ////////////////// Payment Processor configuration popup /////////////////
        $form->addElement(new Element_HTML('<div id="rm_pproc_config_parent_backdrop" style="display:none" class="rm_config_pop_wrap">'));
        $form->addElement(new Element_HTML('<div class="rm_pproc_config_overlay"  onclick="hide_payment_config_modal();"></div>'));
        $form->addElement(new Element_HTML('<div id="rm_pproc_config_parent" style="display:block" class="rm_config_pop">'));
        foreach($data['pay_procs_configs'] as $pproc_name => $form_elems):
            $form->addElement(new Element_HTML('<div class="rm_pproc_config_single" id="rm_pproc_config_'.$pproc_name.'" style="display:none">'));
                $form->addElement(new Element_HTML("<div class='rm_pproc_config_single_titlebar'><div class='rm_pproc_title'>{$data['pay_procs_options'][$pproc_name]}</div><span onclick='hide_payment_config_modal();' class='rm-popup-close'>&times;</span></div>"));
                $form->addElement(new Element_HTML('<div class="rm_pproc_config_single_elems">'));
            foreach($form_elems as $elem):
                $form->addElement($elem);
            endforeach;
                $form->addElement(new Element_HTML('</div>'));
            $form->addElement(new Element_HTML('</div>'));
        endforeach;
        
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_Hidden('default_payment_method', $selected_default_payment, array("id" => 'rm_default_payment_method_field')));
        
        ////////////////// End: Payment Processor configuration popup ////////////
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_HIDE_PAYMENT_SELECTOR'), "hide_pay_selector", array("yes" => ''),array("id" => "rm_payments_hide_pay_selector", "class" => "rm_payments_hide_pay_selector" , "value" => $data['hide_pay_selector'] , "longDesc" => RM_UI_Strings::get('LABEL_ENABLE_HIDE_PAYMENT_SELECTOR'))));
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_CURRENCY'), "currency", $curr_arr, array("value" => $data['currency'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_CURRENCY'))));
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_CURRENCY_SYMBOL'), "currency_symbol_position", array("before" => __("Before amount (Eg.: $10)",'registrationmagic-addon'), "after" => __("After amount (Eg.: 10$)",'registrationmagic-addon')), array("value" => $data['currency_symbol_position'], "longDesc" => RM_UI_Strings::get("LABEL_CURRENCY_SYMBOL_HELP"))));

        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_ENABLE_TAX'), "enable_tax", array("yes" => ''),array("id" => "rm_payments_enable_tax", "class" => "rm_payments_enable_tax" , "value" => $data['enable_tax'],  "onclick" => "hide_show(this)" , "longDesc" => RM_UI_Strings::get('LABEL_ENABLE_TAX_HELP'))));
        
        if ($data['enable_tax'] == 'yes')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_payments_enable_tax_childfieldsrow" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_payments_enable_tax_childfieldsrow" style="display:none">'));
        $form->addElement(new Element_Radio("<b>".RM_UI_Strings::get('LABEL_TAX_TYPE')."</b>", "tax_type", array('fixed' => RM_UI_Strings::get('LABEL_TAX_TYPE_FIXED'), 'percentage' => RM_UI_Strings::get('LABEL_TAX_TYPE_PERCENTAGE')), array("id" => "rm_tax_type", "class" => "rm_tax_type", "value" => $data['tax_type'] == 'fixed' ? 'fixed' : 'percentage', "onclick" => "hide_show_tax_values(this)", "longDesc" => '')));
        $form->addElement(new Element_Number("<b>".RM_UI_Strings::get('LABEL_TAX_FIXED')."</b>", "tax_fixed", array("id" => "rm_tax_fixed", "value" => $data['tax_fixed'], "min" => 0, "step" => "0.01", "longDesc" => '')));
        $form->addElement(new Element_Number("<b>".RM_UI_Strings::get('LABEL_TAX_PERCENTAGE')."</b>", "tax_percentage", array("id" => "rm_tax_percentage", "value" => $data['tax_percentage'], "min" => 0, "max" => 100, "step" => "0.01", "longDesc" => '')));
        $form->addElement(new Element_Textbox("<b>".RM_UI_Strings::get('LABEL_TAX_RENAME')."</b>", "tax_rename", array("id" => "rm_tax_rename", "value" => $data['tax_rename'], "longDesc" => '')));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','registrationmagic-addon'), '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));

        $form->render();
        ?>

    </div>
</div>
<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
    
    function rm_open_payproc_config(ele) {
        var jqele = jQuery(ele);
        
        if(jqele.closest(".rmrow").hasClass("rm_deactivated"))
            return;
        
        var jq_pproc = jqele.parents("li").children('span.rm-pricefield-wrap').children().val();
        
        if(typeof jq_pproc == 'undefined')
            return;
        
        jQuery("#rm_pproc_config_parent").children().hide();
        jQuery("#rm_pproc_config_parent").children("#rm_pproc_config_"+jq_pproc).show();
        jQuery("#rm_pproc_config_parent_backdrop").show();
    }
    function enable_paypal_modern_popup(element){
        var modernContainer = document.getElementById('rm_pp_modern_enable_childfieldsrow');
        if(element.checked){
            modernContainer.style.display = 'block';
        }else{
            modernContainer.style.display = 'none';
        }
    }
    function hide_payment_config_modal(element) {
        if(
            jQuery("div#rm_pproc_config_parent_backdrop").is(':visible')
            && jQuery("input#rm_pp_modern_enable-0").is(':checked')
            && jQuery("input#rm_pp_modern_client_id").val().trim() == ''
        ) {
            jQuery("input#rm_pp_modern_client_id").focus();
            jQuery('span#rm_pp_modern_client_error_msg').show();
            
            var rmErrorMsg = jQuery('span#rm_pp_modern_client_error_msg');
            rmErrorMsg.insertAfter('#rm_pp_modern_client_id');
            
            return;
        }
        jQuery("#rm_pproc_config_parent_backdrop").hide();
    }
    jQuery(document).mouseup(function (e) {
        var container = jQuery("#rm_pproc_config_parent");
        if (!container.is(e.target) // if the target of the click isn't the container... 
                && container.has(e.target).length === 0 && container.is(":visible")) // ... nor a descendant of the container 
        {
            //jQuery("#rm_pproc_config_parent_backdrop").hide();
        }
    });

    function hide_show_tax_values(element) {
        var value = jQuery(element).val();
        if(value == 'fixed') {
            jQuery('input[name=tax_fixed]').parent().parent().show();
            jQuery('input[name=tax_percentage]').parent().parent().hide();
        } else {
            jQuery('input[name=tax_percentage]').parent().parent().show();
            jQuery('input[name=tax_fixed]').parent().parent().hide();
        }
    }
    function disbaled_default_payment_method(){
        var selectedMethod = '<?php echo $selected_default_payment ;?>';
        var selected_label = '<?php echo RM_UI_Strings::get('LABEL_SELECTED_DEFAULT');?>';
        const elements = document.querySelectorAll('.rm_default_list');
        Array.from(elements).forEach( (el) => {
           var current_payment =  jQuery(el).closest('li').find("input[name='payment_gateway[]']").val();
            if(selectedMethod == current_payment){
               jQuery(el).addClass('rm_default_active_method');
               jQuery(el).text(selected_label);
           }
        });
    }
    function rm_make_default_payment(element) {
        var default_label = '<?php echo RM_UI_Strings::get('LABEL_MAKE_DEFAULT');?>';
        var selected_label = '<?php echo RM_UI_Strings::get('LABEL_SELECTED_DEFAULT');?>';
        var paymentProcessor = jQuery(element).closest('li').find("input[name='payment_gateway[]']").val();
        var list_payment = document.querySelectorAll('.rm_default_list');
        Array.from(list_payment).forEach( (el) => {
                        jQuery(el).removeClass('rm_default_active_method');
                        jQuery(element).addClass('rm_default_active_method');
                        jQuery(el).text(default_label);
                        jQuery(element).text(selected_label);
                        
                    }); 
        if(paymentProcessor) {
            var data = {
                action: 'rm_options_default_payment_method',
                rm_sec_nonce: '<?php echo wp_create_nonce('rm_ajax_secure'); ?>',
                payment_method: paymentProcessor
            };
            jQuery.post(ajaxurl, data,function(resp){
                if(resp == 'success'){
                    jQuery('#rm_default_payment_method_field').val(paymentProcessor);
                    Array.from(list_payment).forEach( (el) => {
                        jQuery(el).removeClass('rm_default_active_method');
                        jQuery(element).addClass('rm_default_active_method');
                        jQuery(el).text(default_label);
                        jQuery(element).text(selected_label);
                        
                    });    
                }
            });
        }
    }
    jQuery(document).ready(function () {
        jQuery('#options_payment-element-1-0').click(function () {
            checkbox_disable_elements(this, 'rm_pp_test_cb-0,rm_pp_email_tb,rm_pp_style_tb', 0);
        });
        jQuery('#options_payment-element-1-1').click(function () {
            checkbox_disable_elements(this, 'rm_s_api_key_tb,rm_s_publish_key_tb', 0);
        });
        <?php if(!$ssl_available){?>
            jQuery('#options_payment-element-1-1').attr('checked', false);
            jQuery('#options_payment-element-1-1').attr('disabled', true);
        <?php ;} ?>               
        
        var pgws_jqel = jQuery("input[name='payment_gateway[]']");
        
        pgws_jqel.each(function(){
            var cbox_jqel = jQuery(this);
            if(cbox_jqel.prop('checked'))
                cbox_jqel.parents("li").children('.rmrow').removeClass("rm_deactivated");
            else
                cbox_jqel.parents("li").children('.rmrow').addClass("rm_deactivated");
        });
        
        pgws_jqel.change(function(){
            var cbox_jqel = jQuery(this);
            if(cbox_jqel.prop('checked'))
                cbox_jqel.parents("li").children('.rmrow').removeClass("rm_deactivated");
            else
                cbox_jqel.parents("li").children('.rmrow').addClass("rm_deactivated");
        });
        
        hide_show_tax_values(document.querySelector('input[name="tax_type"]:checked'));
        disbaled_default_payment_method();
        
        jQuery("input#rm_pp_modern_client_id").on('keyup', function() {
            if(jQuery(this).val() != '') {
                jQuery('span#rm_pp_modern_client_error_msg').hide();
            }
        });
    });
</script></pre>

<?php   
