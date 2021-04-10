<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tkt_forms
 * @subpackage Tkt_forms/includes
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_forms {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tkt_forms_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
     * The human readable name of this plugin
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $human_plugin_name    The String used as Human Readable Name for the plugin.
     */
    protected $human_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TKT_FORMS_VERSION' ) ) {
			$this->version = TKT_FORMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'tkt_forms';
		$this->human_plugin_name = 'TukuToi Forms';
		$this->tkt_forms_db_version = '1.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tkt_forms_Loader. Orchestrates the hooks of the plugin.
	 * - Tkt_forms_i18n. Defines internationalization functionality.
	 * - Tkt_forms_Admin. Defines all hooks for the admin area.
	 * - Tkt_forms_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tkt_forms-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tkt_forms-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tkt_forms-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt_forms-public.php';

		/**
         * TukuToi Common Code
         */
        if( !defined( 'TKT_COMMON_LOADED' ) ){
            require_once( plugin_dir_path( dirname( __FILE__ ) ).'includes/common/class-tkt-common.php' );

        }
        $this->common = TKT_Common::getInstance();

		$this->loader = new Tkt_forms_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tkt_forms_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tkt_forms_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tkt_forms_Admin( $this->get_plugin_name(), $this->human_plugin_name, $this->get_version() );
		$plugin_settings    = new Tkt_Forms_Admin_Settings( $this->get_plugin_name(), $this->get_human_plugin_name(), $this->get_version(), $this->common );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'register_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'register_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_settings, 'setup_plugin_menu', 11 );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_settings' );
        
		$this->loader->add_action( 'init', $this->common, 'load' );

		$this->loader->add_action('wp_ajax_db_save_form_ajax', $plugin_settings, 'db_save_form_ajax');
		$this->loader->add_action('wp_ajax_db_get_form_ajax', $plugin_settings, 'db_get_form_ajax' );
		$this->loader->add_action('wp_ajax_db_save_notification_ajax', $plugin_settings, 'db_save_notification_ajax');
		$this->loader->add_action('wp_ajax_db_get_notification_ajax', $plugin_settings, 'db_get_notification_ajax' );
		$this->loader->add_action('wp_ajax_db_delete_form_ajax', $plugin_settings, 'db_delete_form_ajax' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Tkt_forms_Public( $this->get_plugin_name(), $this->get_version(), $this->get_human_plugin_name() );
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_scripts' );

		//$this->loader->add_action( 'init', $plugin_public, 'load_forms' );
		
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_action( 'plugins_loaded', $plugin_public, 'set_current_user' );

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
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
     * The Human name of the plugin used to give the plugin a name
     *
     * @since     1.0.0
     * @return    string    The Human name of the plugin.
     */
    public function get_human_plugin_name() {
        return $this->human_plugin_name;
    }

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tkt_forms_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
