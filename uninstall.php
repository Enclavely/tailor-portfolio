<?php

/**
 * Uninstalls the Tailor Portfolio plugin.
 *
 * @package Tailor Portfolio
 * @since 1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check user permissions
if ( ! current_user_can( 'activate_plugins' ) ) {
	return;
}