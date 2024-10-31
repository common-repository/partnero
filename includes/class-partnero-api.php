<?php

/**
 * This class defines API calls to the partnero.
 * More information can be found at: https://developers.partnero.com/reference/general.html
 *
 * @since      1.0.0
 * @package    Partnero
 * @subpackage Partnero/includes
 * @author     https://www.partnero.com/
 */

class Partnero_Api {

    /**
     * Holds the api key to pass in calls
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $api_key
     */
    protected static $api_key;

    /**
     * Base url for api calls
     *
     * @link     https://developers.partnero.com/reference/general.html#base-url
     * @since    1.0.0
     * @access   protected
     * @var      string    $base_url
     */
    protected static $base_url = 'https://api.partnero.com/v1';

    /**
     * Set key in api_key variable.
     * Can be use to set api key from external class.
     *
     * @since    1.0.0
     * @param    string    $key
     */
    public static function set_api_key($key) {
        self::$api_key = $key;
    }

    /**
     * Get api key which is set or return from database.
     *
     * @since    1.0.0
     */
    public static function get_api_key() {

        if( empty( self::$api_key ) && Partnero_Util::has_option( 'api_key' ) ) {
            self::$api_key = get_option( 'partnero' )['api_key'];
        }

        return self::$api_key; // If not api key is found it will return null
    }

    /**
     * Test call to the api which will return general info of user & program.
     *
     * @since    1.0.0
     * @return   mixed
     */
    public static function test_call() {
        $url = self::$base_url . '/test';
        return self::common_request_call( $url, 'GET');
    }

    /**
     * Get program overview data.
     *
     * @since    1.0.0
     * @return   mixed
     */
    public static function program_overview_call() {
        $url = self::$base_url . '/program/overview';
        return self::common_request_call( $url, 'GET');
    }

    /**
     * Funcion call for CRUD operations of customers over REST API.
     *
     * @link     https://developers.partnero.com/reference/customers.html
     * @since    1.0.0
     * @param    $type         Type of call GET, POST, etc...
     * @param    $data         Data that will be passed in body of request
     * @param    $id           id / key to attach after url for the REST API
     * @return   mixed
     */
    public static function customer_call($type, $data = [], $id = '') {
        $url = self::$base_url . '/customers';
        if($id) {
            $url = $url . "/{$id}";
        }
        return self::common_request_call( $url, $type, $data );
    }

    /**
     * Funcion call for CRUD operations of transactions over REST API.
     *
     * @link     https://developers.partnero.com/reference/transactions.html
     * @since    1.0.0
     * @param    $type         Type of call GET, POST, etc...
     * @param    $data         Data that will be passed in body of request
     * @param    $id           id / key to attach after url for the REST API
     * @return   mixed
     */
    public static function transaction_call($type, $data = [], $id = '') {
        $url = self::$base_url . '/transactions';
        if($id) {
            $url = $url . "/{$id}";
        }
        return self::common_request_call( $url, $type, $data );
    }

    /**
     * Common function which will handle api call using curl.
     *
     * @since    1.0.0
     * @param    $url          URL that needs to be called
     * @param    $type         Type of call GET, POST, etc...
     * @param    $data         Data that will be passed in body of request
     * @return   mixed
     */
    public static function common_request_call( $url, $type = 'GET', $data = [] ) {

        $token = self::get_api_key();

        if( empty( $token ) ) {
            return;
        }

        $response = wp_remote_request( $url, [
            'method'  => $type,
            'timeout' => 30,
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => "application/json",
                'Accept'        => "application/json",
            ],
            'body' => !empty( $data ) ? json_encode( $data ) : null,
        ] );

        if( !empty( wp_remote_retrieve_body($response) ) ) {

            $body = json_decode( wp_remote_retrieve_body($response) );

            if( isset( $body->status ) ) {
                // Returns body data if available
                if( (bool)$body->status && !empty( $body->data ) ) {
                    return $body->data;
                }
                // Otherwise return body status
                return $body->status;
            }
        }
        // If there is error in request null will returned
        return null;
    }
}
