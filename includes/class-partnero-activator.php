<?php

/**
 * Defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Partnero
 * @subpackage Partnero/includes
 * @author     https://www.partnero.com/
 */

class Partnero_Activator {

    /**
     * Fired during plugin activation.
     *
     * @since    1.0.0
     */
    public static function activate() {
        flush_rewrite_rules();
    }
}
