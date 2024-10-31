<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 *
 * @since      1.0.0
 * @package    Partnero
 * @subpackage Partnero/includes
 * @author     https://www.partnero.com/
 */

class Partnero
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Partnero_Loader    $loader
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version
     */
    protected $version;

    /**
     * Sets up core functionality of plugin.
     *
     * @since 1.0.0
     */
    public function __construct() {

        if (defined('PARTNERO_VERSION')) {
            $this->version = PARTNERO_VERSION;
        }
        else {
            $this->version = '1.0.0';
        }

        $this->plugin_name = 'partnero';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_universal_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-partnero-loader.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-partnero-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-partnero-public.php';

        /**
         * The class responsible for defining all api calls to partnero.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-partnero-api.php';

        /**
         * The class that contains helper and utility functions for plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-partnero-util.php';

        $this->loader = new Partnero_Loader();

    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Partnero_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_menu', $plugin_admin, 'attach_partnero_menu' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Partnero_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_head', $plugin_public, 'attach_partnero_universal' );
        $this->loader->add_action( 'user_register', $plugin_public, 'signup_tracker_handle' );
        $this->loader->add_action( 'woocommerce_new_order', $plugin_public, 'attach_partner_key_to_order' );

        /**
         * Extra hook to be compatible with SVEA checkout. Because during SVEA checkout, partnero cookies are not available
         * This is the last hook, on which partnero cookies are available before the checkout is progressed.
         * @since 1.3.5
         */
        $this->loader->add_action( 'woocommerce_sco_session_data', $plugin_public, 'update_partner_key_into_session' );
    }

    /**
     * Register all of the hooks that are universal and will effect public and admin both.
     *
     * @since    1.2.0
     * @access   private
     */
    private function define_universal_hooks() {

        $plugin_public = new Partnero_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'woocommerce_order_status_processing', $plugin_public, 'woocommerce_track_order' );
        $this->loader->add_action( 'woocommerce_order_status_completed', $plugin_public, 'woocommerce_track_order' );

        $this->loader->add_action( 'woocommerce_order_status_cancelled', $plugin_public, 'woocommerce_remove_order' );
        $this->loader->add_action( 'woocommerce_order_status_refunded', $plugin_public, 'woocommerce_remove_order' );

        /**
         * Extra hook in case payment is done but order status change has any issues.
         * This hook will not depend on order status and will be fired once payment is complete and but order is unchanged.
         * Note depending on 3rd party plugin this hook may not even fire.
         */
        $this->loader->add_action( 'woocommerce_pre_payment_complete', $plugin_public, 'woocommerce_track_order' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Partnero_Loader
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of WordPress
     *
     * @since  1.0.0
     * @return string
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrive the version number of the plugin
     *
     * @since  1.0.0
     * @return string
     */
    public function get_version() {
        return $this->version;
    }
}
