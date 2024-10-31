<?php

/**
 * The public-facing functionality of the plugin.
 *
 * An instance of this class should be passed to the run() function
 * defined in Partnero_Loader as all of the hooks are defined
 * in that particular class.
 *
 * The Partnero_Loader will then create the relationship
 * between the defined hooks and the functions defined in this
 * class.
 *
 * @since      1.0.0
 * @package    Partnero
 * @subpackage Partnero/public
 * @author     https://www.partnero.com/
 */

class Partnero_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name
     * @param    string    $version
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Universal JS which is responsible for the tracking of clicks.
     *
     * @link     https://developers.partnero.com/guide/affiliate.html#tracking
     * @since    1.0.0
     * @param    string    $program_public_id      Public ID of the program created in partnero portal
     */
    public function attach_partnero_universal() {

        if( !Partnero_Util::has_option( 'program_public_id' ) ) {
            return;
        }

        $program_public_id = get_option( 'partnero' )['program_public_id'];

        echo "<!-- Partnero Universal -->
        <script>
            (function(p,t,n,e,r,o){ p['__partnerObject']=r;function f(){
            var c={ a:arguments,q:[]};var r=this.push(c);return \"number\"!=typeof r?r:f.bind(c.q);}
            f.q=f.q||[];p[r]=p[r]||f.bind(f.q);p[r].q=p[r].q||f.q;o=t.createElement(n);
            var _=t.getElementsByTagName(n)[0];o.async=1;o.src=e+'?v'+(~~(new Date().getTime()/1e6));
            _.parentNode.insertBefore(o,_);})(window, document, 'script', 'https://app.partnero.com/js/universal.js', 'po');
            po('settings', 'assets_host', 'https://assets.partnero.com');
            po('program', '{$program_public_id}', 'load');
        </script>
        <!-- End Partnero Universal -->";
    }

    /**
     * Handles signup tracking and sends customer call to partner api.
     *
     * @link     https://developers.partnero.com/guide/affiliate.html#sending-sign-up-data
     * @since    1.0.0
     * @param    string    $user_id      User ID of the customer created
     */
    public function signup_tracker_handle( $user_id ) {

        $user = get_userdata( $user_id );
        $partner_key = Partnero_Util::get_partner_key();

        if( empty( $user )                                                      // If wordpress user is empty
            || !in_array( 'customer', $user->roles )                            // Or user is not 'customer'
            || !Partnero_Util::has_option( 'api_key' )                          // Or api key is missing
            || !Partnero_Util::has_option( 'program_public_id' )                // Or program id is missing
            || empty($partner_key)                           // Or partner in cookie is missing
        ) {
            return;                                                             // Don't track the customer
        }

        $customer_key = Partnero_Util::get_customer_key( $user_id );
        $customer = Partnero_Api::customer_call( 'GET', [], $customer_key);
        if( !empty( $customer ) ) {
            return;
        }

        $first_name = sanitize_user($user->data->user_nicename);
        $last_name  = '';
        $email      = sanitize_email($user->data->user_email);

        // Retrieve $_POST values from WC signup form if available
        if ( isset( $_POST['sr_firstname'] ) ) {
            $first_name = sanitize_user( $_POST['sr_firstname'] );
        }

        if ( isset( $_POST['sr_lastname'] ) ) {
            $last_name = sanitize_user( $_POST['sr_lastname'] );
        }

        $request_body = [
            'partner' => [
                'key' => $partner_key,
            ],
            'customer' => [
                'key'     => $customer_key,
                'email'   => $email,
                'name'    => $first_name,
                'surname' => $last_name,
            ]
        ];

        Partnero_Api::customer_call( 'POST', $request_body );
    }

    /**
     * Add partner key to order meta data.
     *
     * @since    1.3.2
     * @param    string    $order_id      Order ID of the Order that is being tracked
     */
    public function attach_partner_key_to_order( $order_id ) {
        /**
         * https://developer.wordpress.org/reference/functions/is_admin/
         */
        if (is_admin()) {
            return;
        }

        $order = new WC_Order( $order_id );
        $partnerKey = Partnero_Util::get_partner_key() ?? Partnero_Util::get_partner_key_from_session();

        if(empty($partnerKey)) {
            return;
        }

        $order->add_meta_data('partnero_partner', $partnerKey, true);
        $order->save_meta_data();

        Partnero_Util::remove_partner_key_from_session();
    }

    /**
     * Store partner key into session if available in cookie
     * Remove from session if not available in cookie
     * @since    1.3.5
     */
    public function update_partner_key_into_session() {
        $partnerKey = Partnero_Util::get_partner_key();

        if ( !empty($partnerKey)) {
            Partnero_Util::set_partner_key_into_session($partnerKey);
        } else {
            Partnero_Util::remove_partner_key_from_session();
        }
    }

    /**
     * Handles woocommerce order tracking and sends transaction call to partner api.
     *
     * @link     https://developers.partnero.com/guide/affiliate.html#sending-sales-data
     * @since    1.2.0
     * @param    string    $order_id      Order ID of the Order that is being tracked
     */
    public function woocommerce_track_order( $order_id ) {

        $order = new WC_Order( $order_id );

        /**
         * Transaction can't be done if
         * Order not found
         * API key is missing
         * Program ID is missing (Meaning program is not attached)
         */
        if( empty( $order )
            || !Partnero_Util::has_option( 'api_key' )
            || !Partnero_Util::has_option( 'program_public_id' )
        ) {
            return;
        }

        $transaction_key = Partnero_Util::get_transaction_key( $order_id );
        $transaction = Partnero_Api::transaction_call( 'GET', [], $transaction_key);
        if( !empty( $transaction ) ) {
            return;
        }

        /**
         * Note: Woocommerce can have order without user logged in (guest order)
         *
         * If order have user associated (user logged in) with him
         * Customer key will be based on User ID
         * Else transaction key will be the customer key
         */
        $customer_key = !empty( $order->get_user_id() )
                       ? Partnero_Util::get_customer_key( $order->get_user_id() )
                       : $transaction_key;

        $customer = Partnero_Api::customer_call( 'GET', [], $customer_key);

        // If customer doesn't exist over partnero, we will create one
        if( empty( $customer ) ) {

            /* 'new_order' is core hook so order should have this meta set */
            $partner_key = $order->get_meta('partnero_partner', true);

            // Partner key is needed to create customer
            if( empty( $partner_key ) ) {
                return;
            }

            $customer = Partnero_Api::customer_call( 'POST', [
                'partner' => [
                    'key' => $partner_key,
                ],
                'customer' => [
                    'key'     => $customer_key,
                    'name'    => sanitize_user($order->get_billing_first_name()),
                    'surname' => sanitize_user($order->get_billing_last_name()),
                    'email'   => sanitize_email($order->get_billing_email()),
                ]
            ] );

            // If customer can't be created, we can't create transaction
            if( empty( $customer ) ) {
                return;
            }
        }

        $order->delete_meta_data('partnero_partner');
        $order->save_meta_data();

        /**
         * Final transaction amount after removing shipping and tax
         * @todo get_total_shipping() is deprecated this is used for older woocommerce support only, use get_shipping_total()
         */
        $total_amount = $order->get_total() - $order->get_total_shipping();

        // Tax deduction will be based on setting
        $tax_setting = Partnero_Util::has_option('tax_setting') ? get_option('partnero')['tax_setting'] : 'net';

        if($tax_setting === 'net') {
            $total_amount = $total_amount - $order->get_total_tax();
        }

        $product_ids = [];
        $product_types = [];
        foreach ( $order->get_items('line_item') ?? [] as $item ) {
            $product_id = $item->get_product_id();
            $product_ids[] = $product_id;

            $product_terms = get_the_terms ($product_id, 'product_cat') ?? [];
            foreach ( $product_terms as $term ) {
                $product_types[] = $term->term_id;
            }
        }
        $product_types = array_values(array_unique($product_types));

        Partnero_Api::transaction_call( 'POST', [
            'customer' => [
                'key' => $customer->key,
            ],
            'key'          => $transaction_key,
            'amount'       => round($total_amount, 2),
            'amount_units' => sanitize_text_field($order->get_currency()),
            'action'       => 'sale',
            'product_id'   => count($product_ids) === 1 ? $product_ids[0] : $product_ids,
            'product_type' => count($product_types) === 1 ? $product_types[0] : $product_types
        ] );
    }

    /**
     * Removes transaction at partnero for refunded or cancelled orders.
     *
     * @link     https://developers.partnero.com/guide/affiliate.html#recommendations
     * @since    1.2.0
     * @param    string    $order_id      Order ID of the Order that is being tracked
     */
    public function woocommerce_remove_order( $order_id ) {

        $order = new WC_Order( $order_id );

        /**
         * Transaction can't be done if
         * Order not found
         * API key is missing
         * Program ID is missing (Meaning program is not attached)
         */
        if( empty( $order )
            || !Partnero_Util::has_option( 'api_key' )
            || !Partnero_Util::has_option( 'program_public_id' )
        ) {
            return;
        }

        /**
         * Note: Woocommerce can have order without user logged in (guest order)
         *
         * If order have user associated (user logged in) with him
         * Customer key will be based on User ID
         * Else transaction key will be the customer key
         */
        $customer_key = !empty( $order->get_user_id() )
                       ? Partnero_Util::get_customer_key( $order->get_user_id() )
                       : Partnero_Util::get_transaction_key( $order_id );

        /**
         * @todo Passing ID in url is not working at the movement maybe update later
         */
        $is_order_deleted = Partnero_Api::transaction_call( 'DELETE', [
            'key' => Partnero_Util::get_transaction_key( $order_id )
        ] );

        // Remove customer only if it was guest order
        if( $is_order_deleted
            && $customer_key === Partnero_Util::get_transaction_key( $order_id )
        ) {
            Partnero_Api::customer_call( 'DELETE', [], $customer_key);
        }
    }
}
