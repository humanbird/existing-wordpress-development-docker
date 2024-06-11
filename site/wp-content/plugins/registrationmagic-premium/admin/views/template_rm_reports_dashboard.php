<?php
if (!defined('WPINC')) {
    die('Closed'); 
}
?>
<div class="rmagic">
    <div class="rmagic-reports rm-box-white-bg rm-box-border">
        <div class="rm-box-wrap rm-reports-title rm-box-mb-25">
                <div class="rm-box-row ">
                    <div class="rm-box-col-9">
                        <div class="rm-reports-page-title">
                            <?php _e('RegistrationMagic Reports', 'registrationmagic-addon'); ?>
                        </div>
                    </div>

                    <div class="rm-box-col-3 rm-box-text-right">
                        <a href="<?php echo admin_url('admin.php?page=rm_reports_notifications'); ?>" class="rm-email-report-action rm-box-border rm-box-white-bg"><?php _e('Schedule Email Reports','custom-registration-form-builder-with-submission-manage');?> <span class="material-icons"> mail_outline </span></a>
                    </div>
                </div> 
            </div>
        <div class="rm-reports-list rm-box-wrap rm-box-mb-25">
            <div class="rm-reports-card rm-box-white-bg rm-box-border">
                <a href="<?php echo admin_url('admin.php?page=rm_reports_submissions'); ?>">
                    <div class="rm-reports-card-icon rm-box-ptb"><img src="<?php echo RM_IMG_URL . 'svg/inbox.svg' ?>"></div>
                    <div class="rm-reports-card-content">
                        <h3 class=""><?php _e('Form Submissions', 'registrationmagic-addon'); ?></h3>
                        <p class=""><?php _e('Generates a report for submissions recorded within the selected time period. All field values, along with meta data like submission time are available.', 'registrationmagic-addon'); ?></p>
                    </div>
                </a>
            </div>
            
            <div class="rm-reports-card rm-box-white-bg rm-box-border">
                <a href="<?php echo admin_url('admin.php?page=rm_reports_login'); ?>">
                    <div class="rm-reports-card-icon rm-box-ptb"><img src="<?php echo RM_IMG_URL.'svg/login.svg'?>"></div>
                    <div class="rm-reports-card-content">
                        <h3 class=""><?php _e('Login Records','registrationmagic-addon');?></h3>
                        <p class=""><?php _e('Generates a report with login records for the selected time period. Both successful and failed attempts are available as filters. Data is recorded through RegistrationMagic login form.','registrationmagic-addon');?></p>
                    </div>
                </a>
            </div>
            
            <div class="rm-reports-card rm-box-white-bg rm-box-border">
                <a href="<?php echo admin_url('admin.php?page=rm_reports_attachments'); ?>">
                    <div class="rm-reports-card-icon rm-box-ptb"><img src="<?php echo RM_IMG_URL.'svg/attachment.svg'?>"></div>
                    <div class="rm-reports-card-content">
                            <h3 class=""><?php _e('Attachments','registrationmagic-addon');?></h3>
                            <p class=""><?php _e('Displays breakdown of file types received. An option to download all files attached to a form during selected time period, as a single zip is also available.','registrationmagic-addon');?></p>
                    </div>
                </a>
            </div>
            <div class="rm-reports-card rm-box-white-bg rm-box-border">
                <a href="<?php echo admin_url('admin.php?page=rm_reports_payments'); ?>">
                    <div class="rm-reports-card-icon rm-box-ptb"><img src="<?php echo RM_IMG_URL.'svg/payment.svg'?>"></div>
                    <div class="rm-reports-card-content">
                        <h3 class=""><?php _e('Payments','registrationmagic-addon');?></h3>
                        <p class=""><?php _e('Compiles payment records for all payments made from the selected form within selected time period. Includes additional filter for payment status.','registrationmagic-addon');?></p>
                    </div>
                </a>
            </div>
            <div class="rm-reports-card rm-box-white-bg rm-box-border">
                <a href="<?php echo admin_url('admin.php?page=rm_reports_form_compare'); ?>">
                    <div class="rm-reports-card-icon rm-box-ptb"><img src="<?php echo RM_IMG_URL.'svg/compare-forms.svg'?>"></div>
                    <div class="rm-reports-card-content">
                        <h3 class=""><?php _e('Form Comparison','registrationmagic-addon');?></h3>
                        <p class=""><?php _e('A side-by-side comparison table of two selected forms based on their different performance parameters.','registrationmagic-addon');?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
