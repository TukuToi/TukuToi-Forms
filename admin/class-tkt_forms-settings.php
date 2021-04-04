<?php

/**
 * The settings of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Forms
 * @subpackage Tkt_Forms/admin
 */

/**
 * Class Tkt_Forms_Admin_Settings
 *
 */
class Tkt_Forms_Admin_Settings {

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
     * TukuToi Common Code
     *
     * @since    1.0.0
     * @access   private
     * @var      TKT_Common    $common    TKT_Common instance.
     */
    private $common;

    

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string       $plugin_name       The name of this plugin.
     * @param      string       $version           The version of this plugin.
     * @param      string       $human_plugin_name The human name of this plugin.
     * @param      TKT_Common   $common            The TukuToi Common Code Class.
     */
    public function __construct( $plugin_name, $human_plugin_name, $version, $common ) {

        $this->plugin_name  = $plugin_name;
        $this->human_plugin_name = $human_plugin_name;
        $this->version      = $version;
        $this->common       = $common;
        $this->options      = $this->get_options();

    }



    /**
     * Provide a method get all options of this plugin
     * 
     * @since 1.0.0
     * @access private
     */
    private function get_options(){

        $options = get_option( $this->plugin_name );

        $defaults = array(
            // $this->plugin_name .'_style_handles_to_remove'  => array(),
            // $this->plugin_name .'_script_handles_to_remove' => array(),
            // $this->plugin_name .'_archives_to_exclude'      => array(),
            // $this->plugin_name .'_single_objects_to_exclude'=> array(), 
            // $this->plugin_name .'_script_styles_log'        => 0,
            // $this->plugin_name .'_load_frontend_menu'       => 0,
            // $this->plugin_name .'_remove_emojys'            => 0,
            // $this->plugin_name .'_disable_emojis'           => 0,
            // $this->plugin_name .'_fe_scripts'               => array(),
            // $this->plugin_name .'_fe_scripts_reg'           => array(),
            // $this->plugin_name .'_fe_styles'                => array(),
            // $this->plugin_name .'_fe_styles_reg'            => array(),
        );

        foreach ($defaults as $option => $default) {

            if( !array_key_exists($option, $options) ){

                $options[$option] = $default;
            }
        }
        
        return $options;

    }

    /**
     * Provide a method to ensure data we get from Options is as expected
     * 
     * @since 1.0.0
     * @param $option           string  the option suffix
     * @param $expected_output  mixed   the expected output type 
     * @access private
     */
    private function prepare_options($option, $expected_output){

        if( null !== $this->options[$this->plugin_name.$option] && !empty( $this->options[$this->plugin_name.$option] ) ){
            return  $this->options[$this->plugin_name.$option];
        }
        else{
            return $expected_output;
        }

        return '';

    }

    /**
     * Enqueue Styles in Settings page
     * (registered in Tkt_Forms_Admin)
     * @since    1.0.0
     * @access   public
     */
    public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name . '-styles' );
        wp_enqueue_style( $this->plugin_name . '-select2' );

    }

    /**
     * Enqueue Styles in Settings page
     * (registered in Tkt_Forms_Admin)
     * @since    1.0.0
     * @access   public
     */
    public function enqueue_scripts() {

        wp_enqueue_script($this->plugin_name . '-scripts');
        wp_enqueue_script($this->plugin_name . '-select2');

    }

    /**
     * Add Menu Page of this plugin
     *
     * @since 1.0.0
     * @access public
     */
    public function setup_plugin_menu() {

        $pages[] = add_submenu_page( 
            $this->common->get_common_name(), 
            $this->human_plugin_name, 
            'Forms', 
            'manage_options', 
            $this->plugin_name, 
            array($this,'render_settings_page_content'), 
            2 
        );

        foreach ($pages as $page) {
            add_action( "admin_print_styles-{$page}", array($this->common,'enqueue_styles') );
            add_action( "admin_print_styles-{$page}", array($this,'enqueue_styles') );
            add_action( "admin_print_scripts-{$page}", array($this,'enqueue_scripts') );
        }

    }

    /**
     * Render Settings Page
     *
     * @since 1.0.0
     * @access public
     */
    public function render_settings_page_content( $active_tab = '' ) {
        $this->common->set_render_settings_page_content($active_tab = '', $this->plugin_name, $this->plugin_name, $this->plugin_name);
    }

    /**
     * This Plugins Settings Options.
     *
     * @return array
     * @since 1.0.0
     * @access public
     */
    public function settings_options() {

        $options = array(
            // $this->plugin_name .'_style_handles_to_remove'      => "All styles to remove",
            // $this->plugin_name .'_script_handles_to_remove'     => "All scripts to remove",
            // $this->plugin_name .'_archives_to_exclude'          => "Archives to exclude from optimization (Post or tax type slug)",
            // $this->plugin_name .'_single_objects_to_exclude'    => "Pages or Posts or else single objects to exclude (Numeric ID)",
            // $this->plugin_name .'_script_styles_log'            => "Log Scripts and Styles",
            // $this->plugin_name .'_load_frontend_menu'           => "Load Frontend Admin Menu",
            // $this->plugin_name .'_remove_wp_emojis'             => "Remove WP Emojy Scripts and Styles",
            // $this->plugin_name .'_disable_emojis'               => "Remove Emojy even if Device supports it",
        );

        return $options;

    }

    /**
     * Provide Defaults for this Plugins Settings Options.
     *
     * @return array
     * @since 1.0.0
     * @access public
     */
    public function settings_options_defaults() {

        
        $defaults = array(
            // $this->plugin_name .'_style_handles_to_remove'      => "",
            // $this->plugin_name .'_script_handles_to_remove'     => "",
            // $this->plugin_name .'_archives_to_exclude'          => "",
            // $this->plugin_name .'_single_objects_to_exclude'    => "",
            // $this->plugin_name .'_script_styles_log'            => 0,
            // $this->plugin_name .'_load_frontend_menu'           => 0,
            // $this->plugin_name .'_remove_wp_emojis'             => 1,
            // $this->plugin_name .'_disable_emojis'               => 0,
        );

        return $defaults;

    }

    /**
     * Initialise all Option Settings
     *
     * @since 1.0.0
     * @access public
     */
    public function initialize_settings() {

        // If the options don't exist, create them.
        if( false == $this->options ) {
            $default_array = $this->settings_options_defaults();
            add_option( $this->plugin_name, $default_array );
        }

     
        // register a new section
        add_settings_section(
            $this->plugin_name,
            __( 'Form Options', $this->plugin_name ),
            array( $this, 'general_options_callback'),
            $this->plugin_name
        );

        //Why create as many functions as there are options? Just use foreach($settings_options) to create each settings field
        foreach ($this->settings_options() as $option => $name) {
            add_settings_field(
                $option,
                __( $name, $this->plugin_name ),
                array($this, $option . '_cb'),
                $this->plugin_name,
                $this->plugin_name,
                [
                    'label_for' => $option,
                    'class' => $this->plugin_name .'_row',
                    $this->plugin_name .'_custom_data' => 'custom',
                ]
            );

        }

        register_setting( $this->plugin_name, $this->plugin_name );

    }

    /**
     * General Options Callback API
     * @since 1.0.0
     * @access public
     */
    public function general_options_callback() {
        $this->common->set_general_options_callback('Control the Form Options of ', get_bloginfo('name'), ' centrally in one place', $this->plugin_name);
    }


}
