<?php
/**
 * Settings wrapper start template
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2024 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $pagenow;

if ( 'term.php' === $pagenow || 'user-edit.php' === $pagenow ) {
	echo '</tbody></table>';
} else {
	echo '</div>';
}
