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
     * The Globla WP wpdb instance
     *
     * @since    1.0.0
     * @access   private
     * @var          $wpdb    WordPress wpdb instance.
     */
    private $wpdb;

    

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

        global $wpdb;

        $this->plugin_name          = $plugin_name;
        $this->human_plugin_name    = $human_plugin_name;
        $this->version              = $version;
        $this->common               = $common;
        $this->options              = $this->get_options();
        
        $this->wpdb                 = $wpdb;
        $this->tkt_forms_table      = $this->wpdb->prefix.$this->plugin_name;
        $this->notifications_table  = $this->wpdb->prefix.$this->plugin_name.'_notifications';
    }



    /**
     * Provide a method get all options of this plugin
     * 
     * @since 1.0.0
     * @return $options     array   The Plugin Options
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
     * @return $this->options[$this->plugin_name.$option] | $expected_output | ''    the output or a default
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
     * Provide a method to Insert or Udpate Forms to the Database 
     * 
     * @since 1.0.0
     * @param $form_title       string  The Title of our form
     * @param $form_html        string  The Form HTML section
     * @param $form_js          string  The Form JS section
     * @param $form_css         string  The Form CSS section
     * @param $form_type        string  The Form Type
     * @param $form_css         int     The Form ID (PRIMARY, AUTO INCREMENT)
     * @return $maybe_forms     int | false     The Number of rows affected or false
     * @see https://developer.wordpress.org/reference/classes/wpdb/replace/
     * @access private
     */
    private function db_save_form( $form_title, $form_html, $form_js, $form_css, $form_type, $form_id = null ) {

        //If it is an Update Form we have an ID
        $form_id = $form_id != null ? $form_id : null;
        //Sanitise the Title to make slug
        $form_name = sanitize_title( $form_title );
        //MYSQL Date Format
        $creation_date = current_time( 'mysql' );
        //Insert, Replace are already escpaped, no need for prepare
        $maybe_forms = $this->wpdb->replace( 
            $this->tkt_forms_table, 
            array( 
                'ID'            => $form_id,
                'form_title'    => $form_title, 
                'form_name'     => $form_name, 
                'creation_date' => $creation_date, 
                'form_html'     => $form_html, 
                'form_js'       => $form_js, 
                'form_css'      => $form_css, 
                'form_type'     => $form_type, 
            ) 
        );

        return $maybe_forms;

    }

    /**
     * Provide a method to Insert or Udpate Notifications to the Database 
     * 
     * @since 1.0.0
     * @param $notification_title       string  The Title of our Notification
     * @param $notification_content     string  The Notification Content
     * @param $notification_id          int     The Notification ID (PRIMARY, AUTO INCREMENT)
     * @return $maybe_forms     int | false     The Number of rows affected or false
     * @see https://developer.wordpress.org/reference/classes/wpdb/replace/
     * @access private
     */
    private function db_save_notification( $notification_title, $notification_content, $notification_id = null ) {

        //If it is an Update Notification we have an ID
        $notification_id = $notification_id != null ? $notification_id : null;
        //Sanitise the Title to make slug
        $notification_name = sanitize_title( $notification_title );
        //MYSQL Date Format
        $creation_date = current_time( 'mysql' );
        //Insert, Replace are already escpaped, no need for prepare
        $maybe_notifications = $this->wpdb->replace( 
            $this->notifications_table, 
            array( 
                'ID'                    => $notification_id,
                'notification_title'    => $notification_title, 
                'notification_name'     => $notification_name, 
                'creation_date'         => $creation_date, 
                'notification_content'  => $notification_content, 
            ) 
        );

        return $maybe_notifications;

    }

    /**
     * Provide a method to Get a single Form from database
     * 
     * @since 1.0.0
     * @param $form_id  int     The Form ID
     * @return $form    object  The Form Object
     * @access private
     */
    private function db_get_form($form_id){
        error_log( print_r( $_REQUEST, true) );
        /**
         * We have a query Variable, we must prepare, even if it is not a user variable
         */
        $form = $this->wpdb->get_row( 
            $this->wpdb->prepare( 
                "SELECT * FROM $this->tkt_forms_table WHERE ID = %d", 
                $form_id 
            ) 
        );

        if( empty($form) )
            echo "No Form with this ID was found";

        //We receive an Object with escaped property values. Unescape.
        foreach ( $form as $property => $value ){
                $form->$property = wp_unslash( $value );
            }

        return $form;

    }

    /**
     * Provide a method to Get a single Notification from database
     * 
     * @since 1.0.0
     * @param $notification_id  int     The Notification ID
     * @return $notification    object  The Notification Object
     * @access private
     */
    private function db_get_notification($notification_id){
        /**
         * We have a query Variable, we must prepare, even if it is not a user variable
         */
        $notification = $this->wpdb->get_row( 
            $this->wpdb->prepare( 
                "SELECT * FROM $this->notifications_table WHERE ID = %d", 
                $notification_id 
            ) 
        );

        if( empty($notification) )
            echo "No Notification with this ID was found";

        //We receive an Object with escaped property values. Unescape.
        foreach ( $notification as $property => $value ){
                $notification->$property = wp_unslash( $value );
            }

        return $notification;

    }

    /**
     * Provide a method to Get all Forms from database
     * 
     * @since 1.0.0
     * @return $forms   array  All forms found in an array of Objects
     * @access private
     */
    private function db_get_forms(){

        /**
         * We have no query Variable, we must not prepare
         * @return OBJECT_K Array with row objects
         */
        $forms = $this->wpdb->get_results( 
            "SELECT * FROM $this->tkt_forms_table" , 
            OBJECT_K 
        );

        if( !is_array( $forms ) || empty( $forms ) )
            echo "No Forms were found";

        //We receive an Array with Row Objects with escaped Object properties. Unescape
        foreach ( $forms as $row => $objects ){

                foreach ($objects as $property => $value) {
                    $objects->property = wp_unslash( $value );
                }

                $forms[$row] = $objects;

            }

        return $forms;

    }

    /**
     * Provide a method to Delete a Form from database
     * 
     * @since 1.0.0
     * @param $form_id int the form ID to delete
     * @access private
     */
    private function db_delete_form($form_id){

        /**
         * We have no query Variable, we must not prepare
         * @return OBJECT_K Array with row objects
         */
        $deleted = $this->wpdb->delete(
            $this->tkt_forms_table, // table to delete from
            array(
                'ID' => $form_id // value in column to target for deletion
            ),
            array(
                '%d' // format of value being targeted for deletion
            )
        );

        return $deleted;//either false or #of rows affected

    }

    /**
     * Provide a method to Get all Notifications from database
     * 
     * @since 1.0.0
     * @return $notifications   array  All notifications found in an array of Objects
     * @access private
     */
    private function db_get_notifications(){

        /**
         * We have no query Variable, we must not prepare
         * @return OBJECT_K Array with row objects
         */
        $notifications = $this->wpdb->get_results( 
            "SELECT * FROM $this->notifications_table" , 
            OBJECT_K 
        );

        if( !is_array( $notifications ) || empty( $notifications ) )
            echo "No Notifications were found";

        //We receive an Array with Row Objects with escaped Object properties. Unescape
        foreach ( $notifications as $row => $objects ){

                foreach ($objects as $property => $value) {
                    $objects->property = wp_unslash( $value );
                }

                $notifications[$row] = $objects;

            }

        return $notifications;

    }

    /**
     * Provide a method to Get a single Form from database with AJAX
     * 
     * @since 1.0.0
     * @return $form | error    string  The JSON Object of The form or error.
     * @access public
     */
    public function db_get_form_ajax(){

        /**
         * Security Check
         * wp_die() if false
         */
        check_ajax_referer( $this->plugin_name .'_admin_ajax_nonce', $this->plugin_name .'_admin_ajax_secure' );

        /**
         * We have a User Variable, but we prepare already in $this->db_get_form();
         */
        if ( isset( $_GET['form_id'] ) ) {

            $form = $this->db_get_form( $_GET['form_id'] );

            //Echo the Object into a JSON string for AJAX
            echo json_encode( $form, JSON_UNESCAPED_SLASHES );
            
            wp_die();

        }

        echo "No Form ID Provided";
        

    }

    /**
     * Provide a method to Get a single Notification from database with AJAX
     * 
     * @since 1.0.0
     * @return $notification | error    string  The JSON Object of The notification or error.
     * @access public
     */
    public function db_get_notification_ajax(){

        /**
         * Security Check
         * wp_die() if false
         */
        check_ajax_referer( $this->plugin_name .'_admin_ajax_nonce', $this->plugin_name .'_admin_ajax_secure' );

        /**
         * We have a User Variable, but we prepare already in $this->db_get_form();
         */
        if ( isset( $_GET['notification_id'] ) ) {

            $notification = $this->db_get_notification( $_GET['notification_id'] );

            //Echo the Object into a JSON string for AJAX
            echo json_encode( $notification, JSON_UNESCAPED_SLASHES );
            
            wp_die();

        }

        echo "No Notification ID Provided";
        

    }

    /**
     * Provide a method to Save Forms with AJAX
     * 
     * @since 1.0.0
     * @return $maybe_forms | error    string  Number of Rows affected or error
     * @access public
     */
    public function db_save_form_ajax(){

        /**
         * Security Check
         * wp_die() if false
         */
        check_ajax_referer( $this->plugin_name .'_admin_ajax_nonce', $this->plugin_name .'_admin_ajax_secure' );

        /**
         * Require at least a Form Name
         */
        if ( empty( $_POST["form_name"] || !isset( $_POST["form_name"] ) ) ) {
            echo "Insert a Form Name";
            wp_die();
        }
        /**
         * Get the Form ID from the form, null if empty
         */
        $form_id = null;
        if( isset( $_POST['form_id'] ) && !empty( $_POST['form_id'] ) ){
            $form_id = $_POST['form_id'];
        }

        /**
         * Gather Form data to save. May be empty, but is always set.
         */
        $form_title = $_POST["form_name"];
        $form_html  = $_POST['form_html'];
        $form_js    = $_POST['form_js'];
        $form_css   = $_POST['form_css'];
        $form_type  = $_POST['form_type'];

        $maybe_forms = $this->db_save_form( $form_title, $form_html, $form_js, $form_css, $form_type, $form_id );

        /**
         * False or (int) number of rows affected (generally should be one)
         * @debug $maybe_forms returns 1 on insert and at least 2 on upate (one delete, one isnert)
         */
        if( !empty( $maybe_forms ) )
            echo ' Form saved!';
        elseif( false == $maybe_forms )
            echo "There has been an error";

        wp_die();

    }

    public function db_delete_form_ajax($form_id){
        /**
         * Security Check
         * wp_die() if false
         */
        check_ajax_referer( $this->plugin_name .'_admin_ajax_nonce', $this->plugin_name .'_admin_ajax_secure' );

        /**
         * Get the Form ID from the form, null if empty
         */
        $form_id = null;
        if( isset( $_GET['form_id'] ) && !empty( $_GET['form_id'] ) ){
            $form_id = $_GET['form_id'];
        }
        error_log( print_r( $form_id, true) );
        if( $form_id != null ){
            $maybe_deleted = $this->db_delete_form($form_id);
            echo "Form was deleted!";
        }
        else{
            echo "There was a problem deleting the form";
        }

    }

    /**
     * Provide a method to Save Notifications with AJAX
     * 
     * @since 1.0.0
     * @return $maybe_notifications | error    string  Number of Rows affected or error
     * @access public
     */
    public function db_save_notification_ajax(){

        /**
         * Security Check
         * wp_die() if false
         */
        check_ajax_referer( $this->plugin_name .'_admin_ajax_nonce', $this->plugin_name .'_admin_ajax_secure' );

        /**
         * Require at least a Form Name
         */
        if ( empty( $_POST["notification_name"] || !isset( $_POST["notification_name"] ) ) ) {
            echo "Insert at least a Notification Name Please";
            wp_die();
        }
        /**
         * Get the Form ID from the form, null if empty
         */
        $notification_id = null;
        if( isset( $_POST['notification_id'] ) && !empty( $_POST['notification_id'] ) ){
            $notification_id = $_POST['notification_id'];
        }

        /**
         * Gather Form data to save. May be empty, but is always set.
         */
        $notification_title     = $_POST["notification_name"];
        $notification_content   = $_POST['notification_content'];

        $maybe_notifications = $this->db_save_notification( $notification_title, $notification_content, $notification_id );

        /**
         * False or (int) number of rows affected (generally should be one)
         * @debug $maybe_forms returns 1 on insert and at least 2 on upate (one delete, one isnert)
         */
        if( !empty( $maybe_notifications ) )
            echo ' Notification saved!';
        elseif( false == $maybe_notifications )
            echo "There has been an error";

        wp_die();

    }

    /**
     * Enqueue Styles in Settings page
     * (registered in Tkt_Forms_Admin)
     * @since    1.0.0
     * @access   public
     */
    public function enqueue_styles() {

        wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
        wp_enqueue_style( $this->common->get_common_name() . '-styles' );
        wp_enqueue_style( $this->plugin_name . '-styles' );
        wp_enqueue_style( $this->plugin_name . '-select2' );

    }

    /**
     * Enqueue Styles in Settings page
     * Localise a JS Object for AJAX methods 
     * (registered in Tkt_Forms_Admin)
     * @since    1.0.0
     * @access   public
     */
    public function enqueue_scripts() {

        wp_enqueue_script($this->plugin_name . '-scripts');
        wp_enqueue_script($this->plugin_name . '-select2');
        $data = array(
            'ajax_url'  => admin_url( "admin-ajax.php" ),
            'ajax_nonce'=> wp_create_nonce( $this->plugin_name .'_admin_ajax_nonce' ),
        );
        wp_localize_script( $this->plugin_name . '-scripts' , $this->plugin_name . '_ajax_data', $data );

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
        ob_start();
        include(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/tkt_forms-admin-display.php');
        $additional_html = ob_get_contents(); 
        ob_end_clean();
        $this->common->set_render_settings_page_content($active_tab = '', $this->plugin_name, $this->plugin_name, $this->plugin_name, 'Save Forms', $additional_html);
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
            __( 'Forms', $this->plugin_name ),
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
        $this->common->set_general_options_callback('Control the Forms of ', get_bloginfo('name'), ' centrally in one place', $this->plugin_name);
    }


}
