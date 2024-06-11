<?php
if (!defined('WPINC')) {
    die('Closed');
}
?>
<!-- Legends -->
<div class="rm-sub-legends">    
    <div class="rm-subsection-heading"><?php echo RM_UI_Strings::get('LABEL_LEGEND'); ?></div>

    <div class="rm-sub-legends-row">
        <div class="rm-legend-wrap">
            <div class="rm-legend-img">   
                <img  class="rm_submission_icon" alt="" src="<?php echo RM_IMG_URL . 'pending_payment.png'; ?>"> <span class="rm-legend-text"><?php echo RM_UI_Strings::get('LABEL_LEGEND_PAYMENT_PENDING'); ?></span>
            </div>
        </div>

        <div class="rm-legend-wrap">
            <div class="rm-legend-img">
                <img  class="rm_submission_icon" alt="" src="<?php echo RM_IMG_URL . 'payment_completed.png'; ?>"> <span class="rm-legend-text"><?php echo RM_UI_Strings::get('LABEL_LEGEND_PAYMENT_COMPLETED'); ?></span>
            </div>
        </div>
        
        <div class="rm-legend-wrap">
            <div class="rm-legend-img">   
                <img  class="rm_submission_icon" alt="" src="<?php echo RM_IMG_URL . 'refunded_payment.png'; ?>"> <span class="rm-legend-text"><?php echo RM_UI_Strings::get('LABEL_LEGEND_PAYMENT_REFUNDED'); ?></span>
            </div>
        </div>
        
        <div class="rm-legend-wrap">
            <div class="rm-legend-img">   
                <img  class="rm_submission_icon" alt="" src="<?php echo RM_IMG_URL . 'canceled_payment.png'; ?>"> <span class="rm-legend-text"><?php echo RM_UI_Strings::get('LABEL_LEGEND_PAYMENT_CANCELED'); ?></span>
            </div>
        </div>
        
    </div>
    <div class="rm-sub-legends-row">
        
         <div class="rm-legend-wrap">
            <div class="rm-legend-img">   
                <img  class="rm_submission_icon" alt="" src="<?php echo RM_IMG_URL . 'blocked.png'; ?>"> <span class="rm-legend-text"><?php echo RM_UI_Strings::get('LABEL_LEGEND_USER_BLOCKED'); ?></span>
            </div>
         </div>
        
        <div class="rm-legend-wrap">
            <div class="rm-legend-img">   
                <img  class="rm_submission_icon" alt="" src="<?php echo RM_IMG_URL . 'note.png'; ?>"> <span class="rm-legend-text"><?php echo RM_UI_Strings::get('LABEL_LEGEND_NOTES'); ?></span>
            </div>
        </div>

        <div class="rm-legend-wrap">
            <div class="rm-legend-img">   
                <img  class="rm_submission_icon" alt="" src="<?php echo RM_IMG_URL . 'message.png'; ?>"> <span class="rm-legend-text"><?php echo RM_UI_Strings::get('LABEL_LEGEND_MESSAGE'); ?></span>
            </div>
        </div>

        <div class="rm-legend-wrap">
            <div class="rm-legend-img">   
                <img  class="rm_submission_icon" alt="" src="<?php echo RM_IMG_URL . 'attachment.png'; ?>"> <span class="rm-legend-text"><?php echo RM_UI_Strings::get('LABEL_LEGEND_ATTACHMENT'); ?></span>
            </div>
        </div>
        
    </div>
</div>
<!-- Legends End -->
