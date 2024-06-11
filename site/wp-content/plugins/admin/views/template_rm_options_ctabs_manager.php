<?php
if (!defined('WPINC')) {
    die('Closed');
}
wp_enqueue_style( 'rm_material_icons', RM_BASE_URL . 'admin/css/material-icons.css' );
?>
    <div class="rmagic">

    <!-----Operations bar Starts----->

    <div class="operationsbar">
        <div class="rmtitle"><?php echo RM_UI_Strings::get("TITLE_CTABS_MANAGER"); ?></div>
        <div class="icons">
            <a href="?page=rm_options_manage"><img alt="" src="<?php echo RM_IMG_URL . 'global-settings.png'; ?>"></a>
        </div>
        <div class="nav">
            <ul>
                <li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
                
                <li><a href="<?php echo admin_url('admin.php?page=rm_options_add_ctabs');?>"><?php echo RM_UI_Strings::get("LABEL_NEW_TAB"); ?></a></li>
                
                <li id="rm-delete-ctabs" class="rm_deactivated" onclick="jQuery.rm_do_action('rm_ctabs_manager_form', 'rm_options_ctabs_remove')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_DELETE"); ?></a></li>
                <li class="rm-form-toggle rm-tabs-reorder"><a href="<?php echo admin_url('admin.php?page=rm_options_tabs');?>"><span class="rm-profile-tab-reorder-info"></span> <?php _e('Reorder Tabs','registrationmagic-addon');?></a></li>
            </ul>
        </div>

    </div>
    <!--  Operations bar Ends----->


    <!-------Content area Starts----->

    <?php
    if(empty($data->ctabs)){
        ?><div class="rmnotice-container">
            <div class="rmnotice">
        <?php echo RM_UI_Strings::get('MSG_NO_FORM_CTABS'); ?>
            </div>
        </div><?php
    }
   else
    {?>
    <div class="rm-custom-tabs-table-wrapper">
        <form action="" method="post" id="rm_ctabs_manager_form">
        <table class="rm-form-ctabs rmagic-table">
            <tr>
                <th><input type="checkbox" id="rm_ctabs_select_all" onchange="selectAll(this)" /></th>
                <th><?php _e('Icon','registrationmagic-addon');?></th>
                <th><?php _e('Label','registrationmagic-addon');?></th>
                <th><?php _e('Edit','registrationmagic-addon');?></th>
            </tr>
            <?php foreach($data->ctabs as $key=>$ctabs): ?>
            <tr>
                <td><input class="rm-ctabs-index" type="checkbox" name="rm_ctabs_index[]" value="<?php echo $ctabs->tab_id; ?>"/></td>
                <td><i class="material-icons"><?php echo $ctabs->tab_icon;?></i></td>
                <td><?php echo $ctabs->tab_label; ?></td>
                <td><a href="<?php echo admin_url('admin.php?page=rm_options_add_ctabs&tab_id='.$ctabs->tab_id); ?>"><?php echo RM_UI_Strings::get('LABEL_EDIT'); ?></a></td>
            </tr>     
            <?php endforeach; ?>
        </table>
            <input type="hidden" id="rm_slug_input_field" name="remove_ctabs" />    
        </form>    
    </div>
    <?php 
    }
    ?>        
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css"/>
</div>

<script>
function selectAll(obj){
    if(jQuery(obj).is(':checked')){
        jQuery('.rm-ctabs-index').attr('checked',true);
        jQuery('#rm-delete-ctabs').removeClass('rm_deactivated');
    }
    else{
        jQuery('.rm-ctabs-index').attr('checked',false);
        jQuery('#rm-delete-ctabs').addClass('rm_deactivated');
    }
}
jQuery(document).ready(function(){
    jQuery('.rm-ctabs-index').change(function(){
        if(jQuery('.rm-ctabs-index:checked').length>0){
           jQuery('#rm-delete-ctabs').removeClass('rm_deactivated');
           return;
        }
        jQuery('#rm-delete-ctabs').addClass('rm_deactivated');
        jQuery('#rm_ctabs_select_all').attr('checked',false);
    });
});
</script>
