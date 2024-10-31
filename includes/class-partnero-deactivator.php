<?php

/**
 * Defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Partnero
 * @subpackage Partnero/includes
 * @author     https://www.partnero.com/
 */

class Partnero_Deactivator {

    /**
     * Fired during plugin deactivation.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }

}
