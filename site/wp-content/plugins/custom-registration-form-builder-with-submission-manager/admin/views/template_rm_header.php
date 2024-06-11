<?php if(defined('RM_ADDON_PLUGIN_VERSION') && version_compare(RM_ADDON_PLUGIN_VERSION, RM_PLUGIN_VERSION, '<')) { ?>
<div class="notice notice-warning rm-upgrade-issue-notice" style="position: relative;">
    <p>
        <strong><?php esc_html_e('You are using an older version of RegistrationMagic Premium', 'custom-registration-form-builder-with-submission-manager'); ?></strong><br/>
        <?php echo sprintf(wp_kses_post(__('To keep Premium up-to-date automatically, make sure you have a valid license key entered <a href="%s" target="blank">here</a>. You can also manually download and install the latest version from <a href="%s" target="blank"> here</a>.', 'custom-registration-form-builder-with-submission-manager')), "admin.php?page=rm_licensing", "https://registrationmagic.com/checkout/order-history/"); ?>
    </p>
</div>
<?php }