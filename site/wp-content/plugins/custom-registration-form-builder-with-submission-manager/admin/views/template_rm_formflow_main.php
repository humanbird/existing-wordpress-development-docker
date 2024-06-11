<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_formflow_main.php'); else {

$build_page_style = $config_page_style = $publish_page_style = 'style="display:none;"';
$build_step_class = $config_step_class = $publish_step_class = '';

$normalized_form_name = function_exists("mb_strimwidth")? mb_strimwidth((string)$data->form_name, 0, 22, "..."): $data->form_name;

switch($data->active_step) {
    
    case 'config':
        $config_page_style = "";
        $config_step_class = "rm-wizard-activated";
        break;
    
    case 'publish':
        $publish_page_style = "";
        $publish_step_class = "rm-wizard-activated";
        break;
    
    default:
        $build_page_style = "";
        $build_step_class = "rm-wizard-activated";
        break;
}

wp_enqueue_style('rm_form_dashboard_css', RM_BASE_URL . 'admin/css/style_rm_form_dashboard.css');
wp_enqueue_style('rm_formflow_css', RM_BASE_URL . 'admin/css/style_rm_formflow.css');
if(defined('REGMAGIC_ADDON')) {
    wp_enqueue_style('rm_addon_form_dashboard_css', RM_ADDON_BASE_URL . 'admin/css/style_rm_form_dashboard.css');
    wp_enqueue_style('rm_addon_formflow_css', RM_ADDON_BASE_URL . 'admin/css/style_rm_formflow.css');
}
wp_enqueue_script('rm-formflow');
wp_enqueue_style( 'rm_material_icons', RM_BASE_URL . 'admin/css/material-icons.css' );
?>
<div class="rm-formflow-top-bar">
 
     <!-- Step 1 -->
     <div class="rm-formflow-top-section" style="text-align: left">
         <div class="rm-formflow-top-action" >
             <span class="rm-formflow-top-left"><a href="<?php echo admin_url( 'admin.php?page=rm_form_manage'); ?>"><i class="material-icons">keyboard_arrow_left</i><?php _e('All Forms','custom-registration-form-builder-with-submission-manager'); ?></a></span>
         </div>
     </div>
     <!-- Step 1 -->
 
     <!-- Step 2 -->
     <div class="rm-formflow-top-section" style="text-align: center">
         <div class="rm-formflow-top-action rm-form-design-wrap rm-formflow-top-action-center" >
             <ul class="rm-di-flex rm-d-flex-v-center rm-form-design-wrap">
                    <?php
                    $design_link_class = $design_link_tooltip = "";
                    if($data->theme == 'classic') {
                        $design_link_class = "class='rm_deactivated'";
                        $design_link_tooltip = __('Form design customization is not applicable for Classic theme. To enable please change theme in Global Settings >> General Settings.', 'custom-registration-form-builder-with-submission-manager');
                    }
                    ?>
                    <li title="<?php echo esc_attr($design_link_tooltip); ?>"><a <?php echo wp_kses_post((string)$design_link_class); ?> href="?page=rm_form_sett_view&rdrto=rm_field_manage&rm_form_id=<?php echo esc_attr($data->form_id); ?>"><?php _e('Design','custom-registration-form-builder-with-submission-manager'); ?></a></li>
                   <!-- <li><a id="rm_form_preview_action" class="thickbox rm_form_preview_btn" href="<?php echo add_query_arg(array('form_prev' => '1','form_id' => $data->form_id), get_permalink($data->prev_page)); ?>&TB_iframe=true&width=900&height=600"><?php esc_html_e('Preview ','custom-registration-form-builder-with-submission-manager'); ?></a></li>-->
                   <li><a id="rm_form_preview_action" class="rm_form_preview_btn" href="javascript:void(0)" data-form-id="<?php echo add_query_arg(array('form_prev' => '1','form_id' => $data->form_id), get_permalink($data->prev_page)); ?>"><?php esc_html_e('Preview ','custom-registration-form-builder-with-submission-manager'); ?></a></li>
                </ul>
         </div>
     </div>
     <!-- Step 2 -->
 
     <!-- Step 3 -->
     <div class="rm-formflow-top-section" style="text-align: right">
         <div class="rm-formflow-top-action rm-formflow-top-action-right" >
             
             <span class="rm-formflow-top-right"><a href="<?php echo admin_url( 'admin.php?page=rm_form_sett_manage&rm_form_id='.$data->form_id); ?>"><?php _e('Form Dashboard','custom-registration-form-builder-with-submission-manager'); ?> <i class="material-icons">keyboard_arrow_right</i></a></span>
         </div>
     </div>
 
 </div>

<div id="rm_formflow_build" class="rm_formflow_page" <?php echo wp_kses_post((string)$build_page_style); ?> >
<?php if($data->row_eligible) include RM_ADMIN_DIR."views/template_rm_field_manager_new.php"; else include RM_ADMIN_DIR."views/template_rm_field_manager.php"; ?>
</div>

<div class="rm-formflow-top-bar">

    <!-- Step 1 -->
    <div class="rm-formflow-top-section" style="text-align: left">
        <div class="rm-formflow-top-action" >
            <span class="rm-formflow-top-left"><a href="<?php echo admin_url('admin.php?page=rm_form_manage'); ?>"><i class="material-icons">keyboard_arrow_left</i> <?php _e('All Forms', 'custom-registration-form-builder-with-submission-manager'); ?></a></span>
        </div>
    </div>
    <!-- Step 1 -->

    <!-- Step 2 -->
    <div class="rm-formflow-top-section" style="text-align: center">
        <div class="rm-formflow-top-action  rm-formflow-top-action-center" >

            <span >&nbsp;</span>
        </div>
    </div>
    <!-- Step 2 -->

    <!-- Step 3 -->
    <div class="rm-formflow-top-section" style="text-align: right">
        <div class="rm-formflow-top-action rm-formflow-top-action-right" >

            <span class="rm-formflow-top-right"><a href="<?php echo admin_url('admin.php?page=rm_form_sett_manage&rm_form_id=' . $data->form_id); ?>"><?php _e('Form Dashboard', 'custom-registration-form-builder-with-submission-manager'); ?> <i class="material-icons">keyboard_arrow_right</i></a></span>
        </div>
    </div>

</div>

<?php $current_page= isset($_GET['page']) ? sanitize_text_field($_GET['page']) : ''; ?>
<?php if($current_page!='rm_field_manage') : ?>
    <div id="rm_formflow_publish" class="rm_formflow_page" <?php echo wp_kses_post((string)$publish_page_style); ?> >
    <?php include RM_ADMIN_DIR."views/template_rm_formflow_publish.php"; ?>
    </div>
<?php endif; } ?>



<!-- The modal -->

<div class="rmagic">
    <div id="rm-form-preview-modal" class="rm-modal-view modal" style="display: none">
        <div class="rm-modal-overlay rm-field-popup-overlay-fade-in"></div>
        <div class="rm_field_row_setting_wrap rm-select-row-setting rm-field-popup-out">
            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title">
                    <?php esc_html_e('Form Preview', 'custom-registration-form-builder-with-submission-manager'); ?>
                </div>
                <span class="rm-modal-close rm-text-center">×</span>
            </div>
            <div id="rm-iframe-loader" class="rm-loader"></div>
            
            <iframe src="admin.php?page=rm_form_preview&rm_form_id=<?php echo esc_attr($data->form_id); ?>" name="iframe_a" id="rm-form-preview-frame">
                <p><?php esc_html_e('Your browser does not support iframes.', 'custom-registration-form-builder-with-submission-manager'); ?></p>
            </iframe>
        </div>
    </div>
</div>

<style>
    #rm-form-preview-modal iframe#rm-form-preview-frame{
        width: 100%;
        min-height: 600px;
        height: 100%;
    }
    
    .rmagic .rm_field_row_setting_wrap.rm-select-row-setting{
      min-height: auto;
    }
    
    #rm-iframe-loader {
        display: none;
        margin-top: 18%;
        margin-bottom: 200px
    }
    
    #rm-form-preview-modal .rm-preview-loading #rm-form-preview-frame{
        display: none;
    }
    
    #rm-form-preview-modal .rm-preview-loaded #rm-form-preview-frame{
        display: block;
    }
    
    .rm-preview-loading #rm-iframe-loader{
        display: block !important;
    }
    
    #rm-form-preview-modal .rm_field_row_setting_wrap{
        max-width: 800px;
        left: calc(50% - 400px);
    }
    

    
</style>
<script>

// When the user clicks on the link, open the modal
jQuery(document).ready(function() {
    jQuery(".rm_form_preview_btn").click(function(event) {
        event.preventDefault(); // Prevent default action of link
        var formId = jQuery(this).data("form-id");
        var iframe = document.getElementById('rm-form-preview-frame');
        showModalWithData(formId);
        
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-out');
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-in');
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-preview-loaded');
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-preview-loading');
        jQuery('.rmagic .rm-modal-view').addClass('rm-form-popup-show').removeClass('rm-form-popup-hide');

        iframe.addEventListener('load', function() {
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-preview-loading');
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-preview-loaded');
            iframe.contentWindow.document.querySelector('input[name=rm_sb_btn]').addEventListener('click', function() {
                if(this.classList.contains("rm-submit-btn-show")) {
                    // Add jQuery code here to close the modal
                     jQuery("#rm-form-preview-modal").hide();
                
                }
            });
        });
        
        iframe.contentDocument.location.reload(true);
    });
});

// When the user clicks on <span> (x), close the modal

jQuery(".rm-modal-close").click(function() {
    jQuery("#rm-form-preview-modal").hide();
    
    jQuery('.rmagic .rm-modal-view').addClass('rm-form-popup-hide').removeClass('rm-form-popup-show'); 
});

// Function to show modal with data according to form ID
function showModalWithData(formId) {
    // Here you can fetch data based on the formId and populate the modalContent accordingly
    var modalContent = jQuery("#rm-fields-data-content");
    //modalContent.html("<p>Modal content for form ID: " + formId + "</p>");
    
    // Display the modal
    jQuery("#rm-form-preview-modal").show();
}
</script>