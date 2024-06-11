<?php
if (!defined('WPINC')) {
    die('Closed');
}
wp_enqueue_script( 'jquery-ui-dialog', '', 'jquery' );
wp_enqueue_style( 'rm_material_icons', RM_BASE_URL . 'admin/css/material-icons.css' );
wp_enqueue_style('jquery-ui', RM_BASE_URL . 'admin/css/jquery-ui.min.css');
?>

<div class="rmagic">
    <!--Dialogue Box Starts-->
    <div class="rmcontent">
        <?php
        require_once RM_EXTERNAL_DIR.'icons/icons_list.php';
        if(isset($data->tab_content)){
            $tab_content = $data->tab_content;
        }
        else{
            $tab_content = "Write Something";
        }
        if(isset($data->tab_icon)){
            $tab_icon = $data->tab_icon;
        }
        else{
            $tab_icon ='';
        }
        if(isset($data->tab_label)){
            $tab_label = $data->tab_label;
        }
        else{
            $tab_label ='';
        }
        $form = new RM_PFBC_Form("add-ctabs");

        $form->configure(array(
        "prevent" => array("bootstrap", "jQuery"),
        "action" => ""
        ));
        
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("CTAB_LABEL") . '</div>'));
        if(isset($data->tab_id)){
            $form->addElement(new Element_Hidden("tab_id", $data->tab_id));
        }
            
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('CTAB_LABEL_NAME') . "</b>", "ctab_label", array("id" => "ctab_label", "required" => "1", "value" => $tab_label, "longDesc" => RM_UI_Strings::get('HELP_ADD_CTAB_TITLE'))));
        $form->addElement(new Element_TinyMCEWP("<b>" . RM_UI_Strings::get('CTAB_LABEL_CONTENT') . "</b>", $tab_content , "ctab_desc", array('editor_class' => 'rm_TinydMCE',  "required" => "1", 'editor_height' => '100px'), array("longDesc" => RM_UI_Strings::get('CTAB_HELP_RT_CONTENT'))));
        $form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row_date_type"><div class="rmfield" for="rm_field_value_options_textarea"><label>'.RM_UI_Strings_Addon::get('LABEL_FIELD_ICON').'</label></div><div class="rminput" id="rm_field_icon_chosen"><i class="material-icons" id="id_show_selected_icon">'.$tab_icon.'</i><div class="rm-icon-action"><div class="rm_show_icons" onclick="show_icon_reservoir()"><a>'.RM_UI_Strings::get('LABEL_FIELD_ICON_CHANGE').'</a></div> <div class="rm_remove_icon" onclick="rm_remove_icon()"><a>'.RM_UI_Strings::get('LABEL_REMOVE').'</a></div></div></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">'.RM_UI_Strings::get('CTAB_LABEL_ICON_CONTENT').'</div></div></div>'));
        $form->addElement(new Element_Hidden('ctab_icon', $tab_icon, array('id'=>'ctab_icon', 'class' => 'rm_customize_user_area_tab_icon')));
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','registrationmagic-addon'), '?page=rm_options_manage_ctabs', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field.','registrationmagic-addon')."')")));
        $form->render();
        ?>  
    </div>
</div>
<?php
$ico_arr = rm_get_icons_array();
?>
<div class='rm_field_icon_res_container' id='id_rm_field_icon_reservoir' style='display:none'>    
<div class='rm_field_icon_reservoir'>
<?php
foreach( $ico_arr as $icon_name => $icon_codepoint):
    //var_dump($icon_codepoint);var_dump($f_icon->codepoint);
    if('&#x'.$icon_codepoint == $tab_icon) {
    ?>
    <i class="material-icons rm-icons-get-ready rm_active_icon" onclick="rm_select_icon(this)" id="rm-icon_<?php echo esc_attr($icon_codepoint); ?>"><?php echo '&#x'.esc_html($icon_codepoint); ?></i>
    <?php }
    else {
        ?>
    <i class="material-icons rm-icons-get-ready" onclick="rm_select_icon(this)" id="rm-icon_<?php echo esc_attr($icon_codepoint); ?>"><?php echo '&#x'.esc_html($icon_codepoint); ?></i>
    <?php }
    
endforeach;
?>
</div>
</div>
<pre class='rm-pre-wrapper-for-script-tags'><script>
function show_icon_reservoir(){
    jQuery('#id_rm_field_icon_reservoir').show();
    jQuery(".rm_field_icon_reservoir").dialog();
    jQuery (".ui-dialog.ui-widget").addClass("rmdialog");
}

function close_icon_reservoir(){
    jQuery('#id_rm_field_icon_reservoir').hide();
}

function rm_remove_icon(){    
        //Get old icon
        var oic = jQuery('#ctab_icon').val();
        if(typeof oic != 'undefined')
        {
            if(oic)
            {
                var oicid =  'rm-icon_'+ (oic.slice(3));
                jQuery('#'+oicid).removeClass('rm_active_icon');
            }
        }
            
        //jQuery('#rm-icon_'+ico_cp).addClass('rm_active_icon');
        jQuery('#id_show_selected_icon').html('');
        jQuery('#ctab_icon').val('');
}

function rm_select_icon(e){
    var icid = jQuery(e).attr('id'); id_show_selected_icon;
    if(typeof icid != 'undefined')
    {
        var x = icid.split('_');
        var ico_cp = x[1];
        
        //Get old icon
        var oic = jQuery('#ctab_icon').val();
        if(typeof oic != 'undefined')
            {
               var oicid =  'rm-icon_'+ (oic.slice(3));
               jQuery('#'+oicid).removeClass('rm_active_icon');
            }
            
        jQuery('#rm-icon_'+ico_cp).addClass('rm_active_icon');
        jQuery('#id_show_selected_icon').html('&#x'+ico_cp);
        jQuery('#ctab_icon').val('&#x'+ico_cp);
    }
}
</script>
</pre>