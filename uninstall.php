<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    Partnero
 * @author     https://www.partnero.com/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    exit;
}

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Important: Check if the file is the one that was registered during the uninstall hook.
if ( basename(__DIR__) . '/partnero.php' !== WP_UNINSTALL_PLUGIN )  {
    exit;
}

// Check user roles.
if ( ! current_user_can( 'activate_plugins' ) ) {
    exit;
}

// Safe to carry on
if ( !empty( get_option( 'partnero' ) ) ) {
    delete_option( 'partnero' );
}
// Maybe need to remove 'partner_key' meta_data that might linger
