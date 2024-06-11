<?php
/**
 * Contains code copied from and/or based on Extra Theme by Elegant Themes
 * See the license.txt file in the root directory for more information and licenses
 *
 */
// phpcs:disable -- all code in this file from line 7 onward is a direct copy from the Extra theme with minimal automated change(s); assuming all escaping, etc., has already been done where needed
?>

<?php $id_attr = '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : ''; ?>
<div <?php echo $id_attr ?> class="module post-module et_pb_extra_module <?php echo esc_attr( $module_class ); ?>" style="border-top-color:<?php echo esc_attr( $border_top_color ); ?>">
	<div class="module-head">
		<h1 style="color:<?php echo esc_attr( $module_title_color ); ?>"><?php echo esc_html( $title ); ?></h1>
		<span class="module-filter"><?php echo esc_html( $sub_title ); ?></span>
	</div>
	<?php require ($postsContentTemplate = locate_template('module-posts-content.php')) ? $postsContentTemplate : dirname(__FILE__).'/module-posts-content.php'; ?>
</div>
