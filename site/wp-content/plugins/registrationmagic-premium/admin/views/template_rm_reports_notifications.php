<?php
if (!defined('WPINC')) {
    die('Closed');
}
wp_enqueue_style( 'rm_material_icons', RM_BASE_URL . 'admin/css/material-icons.css' );

?>
    <div class="rmagic">

    <!-----Operations bar Starts----->

    <div class="operationsbar">
        <div class="rmtitle"><?php echo RM_UI_Strings::get("REPORT_TITLE_EMAIL_REMINDER"); ?></div>
        <div class="icons">
            <a href="?page=rm_options_manage"><img alt="" src="<?php echo RM_IMG_URL . 'global-settings.png'; ?>"></a>
        </div>
        <div class="nav">
            <ul>
                <li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
                
                <li><a href="<?php echo admin_url('admin.php?page=rm_reports_notification_add');?>"><?php echo RM_UI_Strings::get("REPORT_ADD_EMAIL_REMINDER"); ?></a></li>
                
                <li id="rm-delete-notification" class="rm_deactivated" onclick="jQuery.rm_do_action('rm_notification_manager_form', 'rm_reports_notification_remove')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_DELETE"); ?></a></li>
                <li id="rm-enable-notification" class="rm_deactivated" onclick="rm_set_enable_disable_reports('enable')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("REPORT_ENABLE_LABEL"); ?></a></li>
                <li id="rm-disable-notification" class="rm_deactivated" onclick="rm_set_enable_disable_reports('disable')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("REPORT_DISABLE_LABEL"); ?></a></li>
            </ul>
        </div>

    </div>
    <!--  Operations bar Ends----->


    <!-------Content area Starts----->

    <?php
    if(empty($data->notifications)){
        ?><div class="rmnotice-container">
            <div class="rmnotice">
                <?php _e('You have not created any email reports yet. Start creating by clicking "New Report" button above.','registrationmagic-addon');?>
            </div>
        </div><?php
    }
   else
    {?>
    <div class="rm-custom-tabs-table-wrapper">
        <form action="" method="post" id="rm_notification_manager_form">
        <table class="rm-form-ctabs rmagic-table">
            <tr>
                <th><input type="checkbox" id="rm_notification_select_all" onchange="selectAll(this)" /></th>
                <th><?php _e('Name','registrationmagic-addon');?></th>
                <th><?php _e('Type','registrationmagic-addon');?></th>
                <th><?php _e('Frequency','registrationmagic-addon');?></th>
                <th><?php _e('Last Execution','registrationmagic-addon');?></th>
                <th><?php _e('Status','registrationmagic-addon');?></th>
                <th><?php _e('Action','registrationmagic-addon');?></th>
            </tr>
            <?php foreach($data->notifications as $key=>$notification): ?>
            <tr>
                <td><input class="rm-notification-index" type="checkbox" name="rm_notification_index[]" value="<?php echo $notification->id; ?>"/></td>
                <td><?php echo wp_kses_post((string)$notification->notification_title);?></td>
                <td><?php echo wp_kses_post((string)ucfirst($notification->notification_type)); ?></td>
                <td><?php 
                    if($notification->cron_type == 'rm_monthly'){
                        _e('Monthly','registrationmagic-addon');
                    }else{
                        echo wp_kses_post((string)ucfirst($notification->cron_type));
                    }
                    
                    ?>
                </td>
                <td>
                    <?php 
                    if($notification->last_exe != ''){
                        $last_exe = date( 'd-m-y, H:ia', $notification->last_exe);
                        echo esc_html(RM_Utilities::localize_time($last_exe,'d-m-y, H:ia')); 
                    }else{
                        echo '-:-';
                    }
                    ?>
                </td>
                <td>
                    <?php 
                    if($notification->enable):
                        echo esc_html(RM_UI_Strings::get("REPORT_ENABLE_LABEL"));
                    else:
                        echo esc_html(RM_UI_Strings::get("REPORT_DISABLE_LABEL"));
                    endif;?>
                </td>
                <td><a href="<?php echo admin_url('admin.php?page=rm_reports_notification_add&rm_notification_id='.$notification->id); ?>"><?php echo RM_UI_Strings::get('LABEL_EDIT'); ?></a></td>
            </tr>     
            <?php endforeach; ?>
        </table>
            <input type="hidden" id="rm_slug_input_field" name="notification_remove" />    
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
        jQuery('.rm-notification-index').attr('checked',true);
        jQuery('#rm-delete-notification').removeClass('rm_deactivated');
        jQuery('#rm-enable-notification').removeClass('rm_deactivated');
        jQuery('#rm-disable-notification').removeClass('rm_deactivated');
    }
    else{
        jQuery('.rm-notification-index').attr('checked',false);
        jQuery('#rm-delete-notification').addClass('rm_deactivated');
        jQuery('#rm-enable-notification').addClass('rm_deactivated');
        jQuery('#rm-disable-notification').addClass('rm_deactivated');
    }
}
jQuery(document).ready(function(){
    jQuery('.rm-notification-index').change(function(){
        if(jQuery('.rm-notification-index:checked').length>0){
           jQuery('#rm-delete-notification').removeClass('rm_deactivated');
           jQuery('#rm-enable-notification').removeClass('rm_deactivated');
           jQuery('#rm-disable-notification').removeClass('rm_deactivated');
           return;
        }
        jQuery('#rm-delete-notification').addClass('rm_deactivated');
        jQuery('#rm-enable-notification').addClass('rm_deactivated');
        jQuery('#rm-disable-notification').addClass('rm_deactivated');
        jQuery('#rm_notification_select_all').attr('checked',false);
    });
});
function rm_set_enable_disable_reports(state) {
    var tasks_jq = jQuery("input[name='rm_notification_index[]']:checked");
    var task_ids = tasks_jq.map(function() {return this.value;}).get();
    if(task_ids.length > 0) {
        var data = {
            action: 'rm_update_reports_enable_disable',
            task_ids: task_ids,
            state: state
        };
        jQuery("#rm-enable-task, #rm-disable-task").addClass("rm_deactivated"); 
        jQuery.post(ajaxurl, data,function(resp){
            window.location.reload();
        });
    }
}
</script>