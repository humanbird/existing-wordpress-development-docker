<?php
if (!defined('WPINC')) {
    die('Closed');
}
wp_enqueue_media();
$enabled_invoice = isset($data['enable_invoice']) ? $data['enable_invoice'] : '';
$invoice_company_name = isset($data['invoice_company_name']) ? $data['invoice_company_name'] : '';
$invoice_company_address = isset($data['invoice_company_address']) ? $data['invoice_company_address'] : '';
$invoice_company_contact_no = isset($data['invoice_company_contact_no']) ? $data['invoice_company_contact_no'] : '';
$invoice_company_email = isset($data['invoice_company_email']) ? $data['invoice_company_email'] : '';
$invoice_company_vat = isset($data['invoice_company_vat']) ? $data['invoice_company_vat'] : '';
$invoice_enable_footer = isset($data['invoice_enable_footer']) ? $data['invoice_enable_footer'] : '';
$invoice_footer_text = isset($data['invoice_footer_text']) ? $data['invoice_footer_text'] : '';
$invoice_left_margin = isset($data['invoice_left_margin']) ? $data['invoice_left_margin'] : 15;
$invoice_top_margin = isset($data['invoice_top_margin']) ? $data['invoice_top_margin'] : 15;
$invoice_right_margin = isset($data['invoice_right_margin']) ? $data['invoice_right_margin'] : 15;
$enable_user_invoice = isset($data['enable_user_invoice']) ? $data['enable_user_invoice'] : '';
$enable_email_invoice = isset($data['enable_email_invoice']) ? $data['enable_email_invoice'] : '';
$invoice_logo_url = isset($data['invoice_company_logo']) ? wp_get_attachment_url($data['invoice_company_logo'] ) : '' ;
$invoice_logo_image = isset($data['invoice_company_logo']) ? $data['invoice_company_logo'] : null ;
$fonts = array('freeserif'=>'Freeserif','courier'=>'Courier','helvetica'=>'Helvetica','times'=>'Times');
$selected_font = isset($data['invoice_font']) ? $data['invoice_font'] :'helvetica';
?>
<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("options_manage_invoice");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));

        $form->addElement(new Element_HTML('<div class="rmheader">' . __( 'Invoice Configuration', 'registrationmagic-addon' ) . '</div>'));

        
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_ENABLE_INVOICE_SETTING'), "enable_invoice", array("yes" => ''),array("id" => "id_rm_admin_enable_invoice", "class" => "id_rm_admin_enable_invoice" , "value" => $enabled_invoice ,  "onclick" => "hide_show_invoice(this)" , "longDesc" => RM_UI_Strings::get('HELP_ENABLE_INVOICE_SETTING'))));

        if ($enabled_invoice == 'yes'){
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_admin_enable_invoice_childfieldsrow">'));
        }else{
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_admin_enable_invoice_childfieldsrow" style="display:none">'));
        }
        $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield"><label>'.RM_UI_Strings::get("LABEL_INVOICE_LOGO").'<sup class="required"> </sup></label></div><div class="rminput"><div class="rm_invoice_logo_selector"><div class="image-preview-wrapper">
            <img id="image-preview" src="'.$invoice_logo_url.'" width="200"></div><input id="rm_invoice_logo_add" class="rm_invoice_logo_upload_button button" type="button" value="Upload image" onclick="select_media_image(this)"/><input class="rm_invoice_logo_remove_button button" type="button" value="Remove image" id="rm_invoice_logo_remove" onclick="remove_media_image(this)"/><input type="hidden" name="invoice_company_logo" class="rm_invoice_logo_id" id="id_rm_invoice_company_logo" value="'.$invoice_logo_image.'"></div></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">'.RM_UI_Strings::get('HELP_INVOICE_LOGO').'</div></div></div>'));
        
        $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_INVOICE_COMPANY_NAME'), "invoice_company_name", array("id" => "id_rm_invoice_company_name", "value" =>$invoice_company_name, "required"=>1, "longDesc" => RM_UI_Strings::get('HELP_INVOICE_COMPANY_NAME'))));
        $form->addElement(new Element_Textarea(RM_UI_Strings::get('LABEL_INVOICE_ADDRESS'), "invoice_company_address", array("id" => "id_rm_invoice_company_address", "value" => $invoice_company_address, "longDesc" => RM_UI_Strings::get('HELP_INVOICE_ADDRESS'))));
        $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_INVOICE_CONTACT_NO'), "invoice_company_contact_no", array("id" => "id_rm_invoice_company_contact_no", "value" => $invoice_company_contact_no, "longDesc" => RM_UI_Strings::get('HELP_INVOICE_CONTACT_NO'))));
        $form->addElement(new Element_Email(RM_UI_Strings::get('LABEL_INVOICE_EMAIL'), "invoice_company_email", array("id" => "id_rm_invoice_company_email", "value" => $invoice_company_email, "longDesc" => RM_UI_Strings::get('HELP_INVOICE_EMAIL'))));
        $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_INVOICE_VAT'), "invoice_company_vat", array("id" => "id_rm_invoice_company_vat", "value" => $invoice_company_vat, "longDesc" => RM_UI_Strings::get('HELP_INVOICE_VAT'))));
        //Enable Footer Text
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_INVOICE_ENABLE_FOOTER'), "invoice_enable_footer", array("yes" => ''),array("id" => "id_rm_admin_invoice_enable_footer", "class" => "id_rm_admin_invoice_enable_footer" , "value" => $invoice_enable_footer ,  "onclick" => "hide_show(this)" , "longDesc" => RM_UI_Strings::get('HELP_INVOICE_ENABLE_FOOTER'))));
        if ($invoice_enable_footer == 'yes'){
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_admin_invoice_enable_footer_childfieldsrow">'));
        }else{
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_admin_invoice_enable_footer_childfieldsrow" style="display:none">'));
        }
        //$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_INVOICE_TEXT_FOOTER') . "</b>", "invoice_footer_text", array("id" => "id_rm_admin_invoice_text_footer", "class" => "id_rm_admin_invoice_text_footer", "value" => $invoice_footer_text, "longDesc"=>RM_UI_Strings::get('HELP_INVOICE_TEXT_FOOTER'))));
        $form->addElement(new Element_TinyMCEWP("<b>" . RM_UI_Strings::get('LABEL_INVOICE_TEXT_FOOTER')."</b>", $invoice_footer_text, "invoice_footer_text", array('editor_class' => 'rm_TinydMCE', 'editor_height' => '100px'), array("longDesc" => RM_UI_Strings::get('HELP_INVOICE_TEXT_FOOTER'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_INVOICE_LEFT_MARGIN') . "</b>", "invoice_left_margin", array("id" => "id_rm_admin_invoice_left_margin", "class" => "id_rm_admin_invoice_left_margin", "value" => $invoice_left_margin, "max"=>20, "longDesc"=>RM_UI_Strings::get('HELP_INVOICE_LEFT_MARGIN'))));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_INVOICE_TOP_MARGIN') . "</b>", "invoice_top_margin", array("id" => "id_rm_admin_invoice_top_margin", "class" => "id_rm_admin_invoice_top_margin", "value" => $invoice_top_margin, "max"=>20, "longDesc"=>RM_UI_Strings::get('HELP_INVOICE_TOP_MARGIN'))));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_INVOICE_RIGHT_MARGIN') . "</b>", "invoice_right_margin", array("id" => "id_rm_admin_invoice_right_margin", "class" => "id_rm_admin_invoice_right_margin", "value" => $invoice_right_margin, "max"=>20, "longDesc"=>RM_UI_Strings::get('HELP_INVOICE_RIGHT_MARGIN'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_INVOICE_FONT') ."</b>", "invoice_font", $fonts, array("value" => $selected_font, "longDesc" => RM_UI_Strings::get('HELP_INVOICE_FONT'))));
        
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_INVOICE_USER_DOWNLOAD'), "enable_user_invoice", array("yes" => ''),array("id" => "rm_enable_user_invoice", "class" => "rm_enable_user_invoice" , "value" => $enable_user_invoice ,  "longDesc" => RM_UI_Strings::get('HELP_INVOICE_USER_DOWNLOAD'))));
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_INVOICE_EMAIL_SENT'), "enable_email_invoice", array("yes" => ''),array("id" => "rm_enable_email_invoice", "class" => "rm_enable_email_invoice" , "value" => $enable_email_invoice ,  "longDesc" => RM_UI_Strings::get('HELP_INVOICE_EMAIL_SENT'))));

        
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','registrationmagic-addon'), '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));


        $form->render();
        ?>
    </div>
</div>
<script type='text/javascript'>
        function remove_media_image(selector){
            var selector_id = jQuery(selector).attr("id");
            var image_preview = jQuery('#'+selector_id).closest('.rm_invoice_logo_selector').find('img').attr('id');
            var image_attachment_id = jQuery('#'+selector_id).closest('.rm_invoice_logo_selector').find('.rm_invoice_logo_id').attr('id');
            jQuery( '#'+image_preview).attr( 'src', '' );
            jQuery( '#'+image_attachment_id).val('');
            console.log(selector_id);
            console.log(image_preview);
                    // Restore the main post ID
        }
        function select_media_image(selector){
            var selector_id = jQuery(selector).attr("id");
            var file_frame;
            var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
            var set_to_post_id = '';
            // If the media frame already exists, reopen it.
                if ( file_frame ) {
                    // Set the post ID to what we want
                    file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                    // Open frame
                    file_frame.open();
                    return;
                } else {
                    // Set the wp.media post id so the uploader grabs the ID we want when initialised
                    wp.media.model.settings.post.id = set_to_post_id;
                }
                // Create the media frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: 'Select a image to upload',
                    button: {
                        text: 'Use this image',
                    },
                    multiple: false // Set to true to allow multiple files to be selected
                });
                // When an image is selected, run a callback.
                file_frame.on( 'select', function() {
                    // We set multiple to false so only get one image from the uploader
                    attachment = file_frame.state().get('selection').first().toJSON();
                    // Do something with attachment.id and/or attachment.url here
                    var image_preview = jQuery('#'+selector_id).closest('.rm_invoice_logo_selector').find('img').attr('id');
                    var image_attachment_id = jQuery('#'+selector_id).closest('.rm_invoice_logo_selector').find('.rm_invoice_logo_id').attr('id');
                    jQuery( '#'+image_preview).attr( 'src', attachment.url );
                    jQuery( '#'+image_attachment_id).val( attachment.id );
                    // Restore the main post ID
                    wp.media.model.settings.post.id = wp_media_post_id;
                });
                    // Finally, open the modal
                    file_frame.open();
        }
        function hide_show_invoice(element)
        {
            var classname =jQuery(element).attr('class');
            var childclass=classname+'_childfieldsrow';
            if(jQuery(element).is(':checked')){
                jQuery('#'+childclass).slideDown();
                jQuery('#id_rm_invoice_company_name').attr('required', true);
            } else{
              jQuery('#'+childclass).slideUp();
              jQuery('#id_rm_invoice_company_name').attr('required', false);
            }  

        }
    </script>
<style>
.rm-theme-form-heading {
    color: #23282d;
    font-size: 16px;
    text-decoration: underline;
    margin-bottom: 25px;
    font-weight: 600;
    padding-left: 20px;
}
</style>