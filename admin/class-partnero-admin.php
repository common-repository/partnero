<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Partnero
 * @subpackage Partnero/admin
 * @author     https://www.partnero.com/
 */

class Partnero_Admin {

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
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * Quick solution to load admin css only for partnero page
         * Make sure to match the page name given in add_menu_page() hook
         */
        if(isset($_GET['page']) && $_GET['page'] == 'partnero-admin'){
            wp_enqueue_style(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'css/partnero-admin.css',
                array(),
                $this->version,
                'all'
            );
        }
    }

    /**
     * Attaches partnero menu in the sidebar.
     *
     * @since    1.0.0
     */
    public function attach_partnero_menu() {
        add_menu_page(
            'Partnero',                                           // Page title
            'Partnero',                                           // Menu title
            'manage_options',                                     // Capability
            'partnero-admin',                                     // Menu slug
            array($this, 'init_admin_page'),                      // Callback function
            Partnero_Util::get_image_url() . 'menu-icon.png',     // Icon
        );
    }

    /**
     * Handler function for the partnero page in admin.
     *
     * @since    1.0.0
     */
    public function init_admin_page() {

        // Remove partnero settings on program detach button click
        if( $_POST && array_key_exists( 'program_action', $_POST ) && $_POST['program_action'] === 'detach-program' ) {
            delete_option('partnero');
        }

        if( $_POST && array_key_exists( 'program_action', $_POST ) && $_POST['program_action'] === 'update-tax-setting' ) {
            $this->update_tax_setting((string)$_POST['tax_setting']);
        }

        // If partnero settings are set, show dashbord
        if( !empty( get_option( 'partnero' ) ) ) {
            $this->show_dashboard();
            return;
        }

        // If clicked on save button of api key form, call the handler function
        if( $_POST && array_key_exists( 'api_key', $_POST ) && !empty( $_POST['api_key'] ) ) {
            $this->api_key_form_handler();
            return;
        }

        // If api key is not set show form to enter it
        $this->api_key_form();
    }

    /**
     * Show api form.
     *
     * @since    1.0.0
     */
    private function api_key_form( $error = '' ) {
        require_once Partnero_Util::get_plugin_directory() . 'admin/template/api-key-form.php';
    }

    /**
     * Api key form handler function for save button click.
     *
     * @since    1.0.0
     */
    private function api_key_form_handler() {

        $api_key = sanitize_text_field( $_POST['api_key'] );
        Partnero_Api::set_api_key( $api_key );

        $result = Partnero_Api::test_call();

        if( !empty( $result ) ) {
            add_option( 'partnero', array(
                'api_key'           => $api_key,
                'program_public_id' => $result->program->pub_id,
                'tax_setting'       => 'net',
            ) );

            header("Refresh:0");
            return;
        }

        // If no result found show error in api key form
        $this->api_key_form( 'Error connecting to program!' );
    }

    /**
     * Shows admin dashboard page after api key is entered.
     *
     * @since    1.0.0
     */
    private function show_dashboard() {

        $result = Partnero_Api::program_overview_call();
        $tax_setting = Partnero_Util::has_option('tax_setting') ? get_option('partnero')['tax_setting'] : 'net';

        require_once Partnero_Util::get_plugin_directory() . 'admin/template/dashboard.php';
    }

    /**
     * Save tax setting to option
     *
     * @since 1.3.1
     * @param string
     */
    private function update_tax_setting($option) {

        $currentOptions = get_option( 'partnero' );
        $currentOptions['tax_setting'] = $option;

        update_option( 'partnero', $currentOptions );
    }
}
