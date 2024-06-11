<?php
if (!defined('WPINC')) {
    die('Closed');
}

/**
 * @internal Plugin Template File [Add Text Type Field]
 * 
 * This view generates the form for adding text type field to the form
 */
$price_field_type = array("fixed" => __("Fixed",'registrationmagic-addon'),
    "multisel" => __("Multi-Select", 'registrationmagic-addon'),
    "dropdown" => __("Dropdown", 'registrationmagic-addon'),
    "userdef" => __("User Defined", 'registrationmagic-addon'));

$fixed_class = "class = 'rm_hidden_element'";
$dd_class = "class = 'rm_hidden_element'";

if ($data->selected_field == 'fixed')
{
    $fixed_class = "";
    $dd_class = "class = 'rm_hidden_element'";
} else if ($data->selected_field == 'userdef')
{
    $fixed_class = "class = 'rm_hidden_element'";
    $dd_class = "class = 'rm_hidden_element'";
} else if ($data->selected_field == 'dropdown' || $data->selected_field == 'multisel')
{
    $fixed_class = "class = 'rm_hidden_element'";
    $dd_class = "";
}
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">

<?php
$form = new RM_PFBC_Form("add-paypal-field");

$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));

if (isset($data->model->field_id))
{
    $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_EDIT_PAYPAL_FIELD_PAGE")));
    $form->addElement(new Element_HTML('<div class="rm_payment_guide"><a target="_blank" href="https://registrationmagic.com/setup-payments-on-registrationmagic-form-using-products/"><span class="dashicons dashicons-book-alt"></span>'.RM_UI_Strings::get('LABEL_PAYMENTS_GUIDE'). '</a></div></div>'));
    $form->addElement(new Element_Hidden("field_id", $data->model->field_id));
} else{
    $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_PAYPAL_FIELD_PAGE")));
    $form->addElement(new Element_HTML('<div class="rm_payment_guide"><a target="_blank" href="https://registrationmagic.com/setup-payments-on-registrationmagic-form-using-products/">'.RM_UI_Strings::get('LABEL_PAYMENTS_GUIDE'). '</a></div></div>'));
}

$form->addElement(new Element_HTML('<div class="rmrow"><div class="rmnotice">'.RM_UI_Strings::get('MSG_RM_PRODUCT_NOTICE').'</div></div>'));
$form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_SELECT_PRICING_TYPE') . "</b>", "type", $price_field_type, array("value" => $data->selected_field, "id" => "id_paypal_field_type_dd", "class" => "rm-static-field", "required" => "1", "onchange" => "rm_toggle_visiblity_pricing_fields(this)", "longDesc" => RM_UI_Strings::get('HELP_ADD_PRICE_FIELD_SELECT_TYPE'))));
$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_PRODUCT_NAME') . "</b>", "name", array("id" => "id_paypal_field_name_tb", /* "required" => "0", */ "class" => "rm-static-field", "required" => "1", "value" => $data->model->name, "longDesc" => RM_UI_Strings::get('HELP_ADD_PRICE_FIELD_LABEL'))));

$form->addElement(new Element_HTML("<div id='id_block_fields_for_fixed' $fixed_class>"));
$form->addElement(new Element_Number("<b>" . sprintf(RM_UI_Strings::get('LABEL_PRICE'), $data->currency_symbol) . "</b>", "value", array("id" => "id_paypal_field_value_no", /* "required" => "0", */ "class" => "rm-static-field", "step" => "0.01", "min" => "0.01", "value" => $data->model->value, "longDesc" => RM_UI_Strings::get('HELP_PRICE_FIELD'))));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_SHOW_ON_FORM') . "</b>", "show_on_form", array(1 => ""), array("id" => "id_paypal_field_visible_cb", "class" => "rm-static-field", "value" => $data->show_on_form, "longDesc" => RM_UI_Strings::get('HELP_SHOW_ON_FORM'))));
$form->addElement(new Element_HTML("</div>"));

$form->addElement(new Element_HTML("<div id='id_block_fields_for_dd_multisel' $dd_class>"));

$multiple_prices = maybe_unserialize($data->model->get_option_price());
$multiple_labels = maybe_unserialize($data->model->get_option_label());

$form->addElement(new Element_HTML('<div class="rm_ul_sortable_container">'));
$form->addElement(new Element_Textboxsortable("<b>" . sprintf(RM_UI_Strings::get('LABEL_PRICE'), $data->currency_symbol) . "</b>", "multisel_name_value[]", array("id" => "id_placeholder_tb", "placeholder" => RM_UI_Strings::get('LABEL_LABEL'), "class" => "rm-static-field rm-mr-2", "required" => "1", "value" => $multiple_labels, "longDesc" => RM_UI_Strings::get('HELP_PRICE_FIELD')), array("type" => "number", "placeholder" => sprintf(RM_UI_Strings::get('LABEL_PRICE'), $data->currency_symbol), "name" => "multisel_price_value[]", "id" => "id_placeholder_no", "class" => "rm-static-field", "required" => "1", "step" => "0.01", "min" => "0.00", "value" => $multiple_prices)));
$form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_jqnotice_text"></div></div>'));
$form->addElement(new Element_HTML("</div></div>"));

$form->addElement(new Element_HTML("<div id='id_allow_quantity_container'>"));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_ALLOW_QUANTITY') . "</b>", "allow_quantity", array(1 => ""), array("id" => "id_allow_quantity", "class" => "rm-static-field", "value" => $data->allow_quantity, "longDesc" => RM_UI_Strings::get('HELP_PRICE_FIELD_ALLOW_QUANTITY'))));
$form->addElement(new Element_HTML("</div>"));

if(empty($data->allow_quantity))
    $form->addElement(new Element_HTML("<div id='id_min_max_quantity_container' class='childfieldsrow' style='display:none;'>"));
else
    $form->addElement(new Element_HTML("<div id='id_min_max_quantity_container' class='childfieldsrow'>"));
$form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_MIN_QUANTITY') . "</b>", "min_quantity", array("id"=>"id_paypal_field_min_quantity", /*"required" => "0",*/  "step"=>"1", "min"=>"0", "value" => $data->min_quantity, "longDesc" => RM_UI_Strings::get('HELP_PRICE_FIELD_MIN_QUANTITY'))));
$form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_MAX_QUANTITY') . "</b>", "max_quantity", array("id"=>"id_paypal_field_max_quantity", /*"required" => "0",*/  "step"=>"1", "min"=>$data->min_quantity, "value" => $data->max_quantity, "longDesc" => RM_UI_Strings::get('HELP_PRICE_FIELD_MAX_QUANTITY'))));
$form->addElement(new Element_HTML("</div>"));

$form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','registrationmagic-addon'), '?page=rm_paypal_field_manage', array('class' => 'cancel')));
$form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));

$form->render();
?>
</div>
</div>
<script type="text/javascript">
    //jQuery('input#id_allow_quantity-0').on('change', function(e) {
        //jQuery('div#id_min_max_quantity_container').toggle();
    //});
    
    
    
    jQuery("input#id_allow_quantity-0").click(function() {
    if(jQuery(this).is(":checked")) {
        jQuery("div#id_min_max_quantity_container").slideToggle( "slow" );
    } else {
        jQuery("div#id_min_max_quantity_container").slideUp();
    }
});
    
    
    jQuery('input#id_paypal_field_min_quantity').change(function(e) {
        var maxVal = jQuery('input#id_paypal_field_max_quantity');
        maxVal.attr('min', jQuery(this).val());
    });
</script>
<?php   
