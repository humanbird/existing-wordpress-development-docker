<?php
if (!defined('WPINC')) {
    die('Closed');
}
?>
<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $wp_pages = RM_Utilities::wp_pages_dropdown();
        $form = new RM_PFBC_Form("rm_default_pages");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        $form->addElement(new Element_HTML('<div class="rmheader">'.__('Default Pages', 'registrationmagic-addon').'</div>'));
        $selected = ($data['default_registration_url'] !== null) ? $data['default_registration_url'] : 0;
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_DEFAULT_REGISTER_URL'), "default_registration_url", $wp_pages, array("value" => $selected, "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_REG_URL'))));
        
        $options= new RM_Options();
        $front_sub_page= $options->get_value_of('front_sub_page_id');
        $disable_pg_profile= $options->get_value_of('disable_pg_profile');
        $form->addElement(new Element_Select(__('Default User Account Page', 'registrationmagic-addon'), "default_user_acc_page", $wp_pages, array("value" => $front_sub_page,"longDesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_ACCOUNT_URL'))));
        if(class_exists('Profile_Magic')) {
            $form->addElement(new Element_Checkbox(__('Disable ProfileGrid Frontend Profile', 'registrationmagic-addon'), "disable_pg_profile", array('yes' => ''), array("value" => $disable_pg_profile, "longDesc" => __("Use this option if you don't need the ProfileGrid frontend profile to display instead of the RegistrationMagic User Account page.",'registrationmagic-addon'))));
        }
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','registrationmagic-addon'), '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field.','registrationmagic-addon') ."')")));
        $form->render();
        ?>
    </div>
</div>

<?php

