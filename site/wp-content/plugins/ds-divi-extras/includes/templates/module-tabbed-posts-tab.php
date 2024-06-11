<?php
/**
 * Contains code copied from and/or based on Extra Theme by Elegant Themes
 * See the license.txt file in the root directory for more information and licenses
 *
 */
// phpcs:disable -- all code in this file from line 7 onward is a direct copy from the Extra theme with minimal automated change(s); assuming all escaping, etc., has already been done where needed
?>

<?php if ( empty( $module_posts ) ) return; ?>
<div class="tab-content tab-content-<?php echo esc_attr( $tab_id ); ?> <?php esc_attr_e( $module_class ); ?>">
	<?php require ($postsContentTemplate = locate_template('module-posts-content.php')) ? $postsContentTemplate : dirname(__FILE__).'/module-posts-content.php'; ?>
</div>
