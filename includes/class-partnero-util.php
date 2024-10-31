<?php

/**
 * This class covers helper and utility functions for the plugin
 *
 * @since      1.0.0
 * @package    Partnero
 * @subpackage Partnero/includes
 * @author     https://www.partnero.com/
 */

class Partnero_Util {

    /**
     * Return icon up or down based on postive or nagative growth
     * Reference in: /admin/template/dashboard.php
     *
     * @since    1.0.0
     * @param    float    $growth
     * @return   string
     */
    public static function get_growth_icon( $growth ) {
        if( $growth < 0 ) {
            return "dashicons-arrow-down-alt";
        }
        return "dashicons-arrow-up-alt";
    }

    /**
     * Return class for progress based on postive or nagative growth
     * Reference in: /admin/template/dashboard.php
     *
     * @since    1.0.0
     * @param    float    $growth
     * @return   string
     */
    public static function get_growth_class( $growth ) {
        if( $growth < 0 ) {
            return "nagative-progress";
        }
        return "positive-progress";
    }

    /**
     * Return directory path of partnero plugin.
     *
     * @since    1.0.0
     * @return   string
     */
    public static function get_plugin_directory() {
        return plugin_dir_path( dirname( __FILE__ ) );
    }

    /**
     * Return url for the partnero images folder.
     *
     * @since    1.0.0
     * @return   string
     */
    public static function get_image_url() {
        return plugin_dir_url( dirname( __FILE__ ) ) . '/images/';
    }

    /**
     * Checks if partnero data like api_key and program_id is stored and not empty in database.
     *
     * @since    1.0.0
     * @param    string    $option_name     api_key, program_public_id, etc..
     * @return   boolean
     */
    public static function has_option( $option_name ) {

        $options = get_option( 'partnero' );

        return (
            !empty( $options )
            && array_key_exists( $option_name, $options )
            && !empty( $options[$option_name] )
            && !empty( trim( $options[$option_name] ) )
        );
    }

    /**
     * Gets partner key from cookie stored by universal js.
     *
     * @since    1.0.0
     * @return   string
     */
    public static function get_partner_key() {
        if( empty($_COOKIE['partnero_partner']) ) {
            return;
        }
        return sanitize_text_field($_COOKIE['partnero_partner']);
    }

    /**
     * Gets partner key from session.
     *
     * @since    1.3.5
     * @return   string
     */
    public static function get_partner_key_from_session() {
        $session = WC()->session;

        if (is_null($session) || !method_exists($session, 'get')) {
            return;
        }

        $partnerKey = $session->get('partnero_partner');
        if( empty($partnerKey) ) {
            return;
        }

        return sanitize_text_field($partnerKey);
    }

    /**
     * Sets partner key into session.
     *
     * @param    string    $value
     * @since    1.3.5
     * @return   void
     */
    public static function set_partner_key_into_session($value) {
        $session = WC()->session;

        if (is_null($session) || !method_exists($session, 'set')) {
            return;
        }

        if( !empty($value)) {
            $session->set('partnero_partner', $value);
        }
    }

    /**
     * Removes partner key from session.
     *
     * @since    1.3.5
     * @return   void
     */
    public static function remove_partner_key_from_session() {
        $session = WC()->session;

        if (is_null($session) || !method_exists($session, '__unset')) {
            return;
        }

        $session->__unset('partnero_partner');
    }

    /**
     * Generates unique customer key to use in api from id provided.
     *
     * @since    1.0.0
     * @param    string    $id
     * @return   string
     */
    public static function get_customer_key( $id ) {
        if( empty( $id ) ) {
            return;
        }
        return sanitize_text_field('wpc_'. get_option( 'partnero' )['program_public_id'] ."_{$id}");
    }

    /**
     * Generates unique transaction key to use in api from id provided.
     *
     * @since    1.0.0
     * @param    string    $id
     * @return   string
     */
    public static function get_transaction_key( $id ) {
        if( empty( $id ) ) {
            return;
        }
        return sanitize_text_field('wpt_'. get_option( 'partnero' )['program_public_id'] ."_{$id}");
    }
}
