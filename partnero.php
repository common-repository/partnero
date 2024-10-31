<?php

/**
 * @package Parntero
 *
 * Plugin Name: Partnero
 * Description: Grow your e-commerce business revenue and customer base with a bespoke affiliate program. Automatically track visits, signups and sales, monitor program performance, and automate payouts.
 * Version: 1.3.7
 * Author: Partnero
 * Author URI: https://www.partnero.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: partnero
**/

// If called from external site or directly, abort.
if (!defined('ABSPATH')) {
    die;
}

// Current plugin version.
define('PARTNERO_VERSION', '1.3.7');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-partnero-activator.php
 */
function activate_partnero() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-partnero-activator.php';
    Partnero_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-partnero-deactivator.php
 */
function deactivate_partnero() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-partnero-deactivator.php';
    Partnero_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_partnero' );
register_deactivation_hook( __FILE__, 'deactivate_partnero' );

/**
 * The core plugin class that is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-partnero.php';

/**
 * Plugin execution will start here
 *
 * @since 1.0.0
 */
function run_partnero()
{
    if(class_exists('Partnero')) {
        (new Partnero())->run();
    }
}

run_partnero();
