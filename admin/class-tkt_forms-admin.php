<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/admin
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_forms_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
     * The Human Name of the plugin
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $human_plugin_name    The humanly readable plugin name
     */
    private $human_plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $human_plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->human_plugin_name = $human_plugin_name;
		$this->version = $version;

		$this->load_dependencies();

	}

	/**
     * Include file with Settings Class
     * @since 1.0.0
     * @access private
     */
    private function load_dependencies() {

        /**
         * The class responsible for defining and instantiating all Setttings in the Plugins options page.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-tkt_forms-settings.php';

    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function register_styles() {

		wp_register_style( $this->plugin_name . '-styles', plugin_dir_url( __FILE__ ) . 'css/tkt_forms-admin.css', array(), $this->version, 'all' );
        wp_register_style( $this->plugin_name . '-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts() {

		wp_register_script( $this->plugin_name . '-scripts', plugin_dir_url( __FILE__ ) . 'js/tkt_forms-admin.js', array( 'jquery' ), $this->version, true );
        wp_register_script( $this->plugin_name . '-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), $this->version, true );

	}

}
