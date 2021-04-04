<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/public
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_forms_Public {

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
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * The Options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $options    	The Options of this plugin.
	 */
	private $options;

	/**
	 * The TukuToi Forms
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $forms 	Array with Form Objects
	 */
	private $forms;

	/**
	 * The Current user
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Tkt_User_Form    $current_user 	The Cirrent user Object.
	 */
	private $current_user;



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $human_plugin_name ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->human_plugin_name = $human_plugin_name;

		$this->options = $this->get_options();

		$this->load_dependencies();
		$this->load_forms();

	}

	/**
	 * Load the required dependencies for the Forms.
	 *
	 * Include the following files that make up the forms:
	 *
	 * - Tkt_Form. Parent Class with reusable methods library for all Forms.
	 * - Tkt_Post_Form. Builds, Handles and Validates Post Forms.
	 * - Tkt_Term_Form. Builds, Handles and Validates Term Forms.
	 * - Tkt_User_Form. Builds, Handles and Validates User Forms.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies(){

		/**
		 * The class to generate and handle all forms 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt_form.php';

		/**
		 * The class generating the Post Form
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt_post_form.php';

		/**
		 * The class generating the Term Form
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt_term_form.php';

		/**
		 * The class generating the User Form
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt_user_form.php';

	}

	/**
	 * Load Plugin Options
	 * @return array $options The plugin Options.
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_options(){

        $options = get_option( $this->plugin_name );

        $defaults = array(
            // $this->plugin_name .'_style_handles_to_remove' 	=> array(),
            // $this->plugin_name .'_script_handles_to_remove' => array(),
            // $this->plugin_name .'_archives_to_exclude'		=> array(),
            // $this->plugin_name .'_single_objects_to_exclude'=> array(),	
            // $this->plugin_name .'_script_styles_log'		=> 0,
            // $this->plugin_name .'_load_frontend_menu'		=> 0,
            // $this->plugin_name .'_remove_emojys'			=> 0,
            // $this->plugin_name .'_fe_scripts'				=> array(),
            // $this->plugin_name .'_fe_scripts_reg'			=> array(),
            // $this->plugin_name .'_fe_styles'				=> array(),
            // $this->plugin_name .'_fe_styles_reg'			=> array(),
        );

        foreach ($defaults as $option => $default) {

            if( !array_key_exists($option, $options) ){

                $options[$option] = $default;
            }
        }
        
        return $options;

    }

    /**
	 * Define Current User Permission
	 * This does not only depend on the form but also on the object the form handles.
	 * @return bool wether user is allowed to use current form.
	 * @since    1.0.0
	 * @access   private
	 */
	private function current_user_can_use($allowlist, $edit_mode, $edit_others, $id){

		if ( array_intersect( $allowlist, $this->current_user->roles ) ) {//Wether the current user is allowed to handle forms.

			if ( 
				 $edit_mode != false //The Form ShortCode is declaring an Edit Form
				 &&
				 $edit_others == false //The Form ShortCode is disallowing to edit others content with this form
				 && 
				 !current_user_can( 'edit_post', $id ) //The Current user can not edit the current object
				) 
			{
			   return false;//The User is not allowed to see or handle this form
			}
		   	return true;//The User is allowed to see and handle this form
		}
		return false;//By Default the user is not allowed to handle this form.
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_styles() {

		wp_register_style( $this->plugin_name .'-fe-styles', plugin_dir_url( __FILE__ ) . 'css/tkt_forms-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts() {

		wp_register_script( $this->plugin_name .'-fe-scripts', plugin_dir_url( __FILE__ ) . 'js/tkt_forms-public.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Set Current User
	 * @since    1.0.0
	 */
	public function set_current_user(){
		$this->current_user = wp_get_current_user();
	}
	
	/**
	 * Register Post Form ShortCode
	 */
	public function add_or_edit_post_form_callback( $atts ){

		$a = shortcode_atts( 
			array(
				'roles_allowlist'	=> 'administrator',
				'edit_mode'			=> false,
				'edit_others'		=> false,
				'post_id'			=> get_the_ID(),
				'post_type'			=> '',
				'no_access_message' => 'No Access',

			), 
			$atts,
			'tkt_post_form'
		);

		$roles_allowlist = explode(',', $a['roles_allowlist']);
		$edit_mode 		 = $a['edit_mode'];
		$edit_allowrules = $a['edit_others'];
		$id 			 = $a['edit_mode'] == false ? '' : $a['post_id'];//edit mode is not set, post ID should not be set either.
		$message 		 = $a['no_access_message'];
		$type 			 = $a['post_type'];

		$current_user_can_use = $this->current_user_can_use( $roles_allowlist, $edit_mode, $edit_allowrules, $id);
		
		if( $current_user_can_use == true ){

			$this->forms['post_form']->set_formdata( array( 
				'type'	=>$type,
				'id'	=>$id, 
			));

			//$this->forms['post_form']->handle_form();
			
			return $this->forms['post_form']->get_form();

		}

		return $message;

	}

	/**
	 * Register Term Form ShortCode
	 */
	public function add_or_edit_term_form_callback( $atts ){

		$a = shortcode_atts( 
			array(
				'roles_allowlist'	=> 'administrator',
				'edit_mode'			=> false,
				'edit_others'		=> false,
				'term_id'			=> '',
				'taxonomy'			=> '',
				'no_access_message' => 'No Access',

			), 
			$atts,
			'tkt_term_form'
		);

		$roles_allowlist = explode(',', $a['roles_allowlist']);
		$edit_mode 		 = $a['edit_mode'];
		$edit_allowrules = false;
		$type 			 = $a['taxonomy'];
		$id 			 = $a['edit_mode'] == false ? '' : $a['term_id'];//edit mode is not set, post ID should not be set either.
		$message 		 = $a['no_access_message'];

		$current_user_can_use = $this->current_user_can_use( $roles_allowlist, $edit_mode, $edit_allowrules, $id);
		
		if( $current_user_can_use == true ){

			$this->forms['term_form']->set_formdata( array(
				'type'=>$type,
			));

			$this->forms['term_form']->handle_form();

			return $this->forms['term_form']->get_form();

		}

		return $message;

	}

	/**
	 * Register User Form ShortCode
	 */
	public function add_or_edit_user_form_callback( $atts ){

		$a = shortcode_atts( 
			array(
				'roles_allowlist'	=> 'administrator',
				'edit_mode'			=> false,
				'edit_others'		=> false,
				'user_id'			=> $this->current_user->ID,
				'no_access_message' => 'No Access',

			), 
			$atts,
			'tkt_user_form'
		);

		$roles_allowlist = explode(',', $a['roles_allowlist']);
		$edit_mode 		 = $a['edit_mode'];
		$edit_allowrules = $a['edit_others'];
		$id 			 = $a['edit_mode'] == false ? '' : $a['user_id'];//edit mode is not set, post ID should not be set either.
		$message 		 = $a['no_access_message'];

		$current_user_can_use = $this->current_user_can_use( $roles_allowlist, $edit_mode, $edit_allowrules, $id);
		
		if( $current_user_can_use == true ){

			$this->forms['user_form']->set_formdata( array(
				'type'=>$type,
			) );

			$this->forms['user_form']->handle_form();

			return $this->forms['user_form']->get_form();

		}

		return $message;

	}

	/**
	 * Register all ShortCodes
	 * @since 1.0.0
	 */
	public function register_shortcodes(){

		add_shortcode( 'tkt_post_form', array( $this, 'add_or_edit_post_form_callback' ) );
		add_shortcode( 'tkt_term_form', array( $this, 'add_or_edit_term_form_callback' ) );
		add_shortcode( 'tkt_user_form', array( $this, 'add_or_edit_user_form_callback' ) );

	}

	/**
	 * Instantiate all Forms
	 * @since 1.0.0
	 */
	public function load_forms(){
    	$this->forms['post_form'] = new Tkt_Post_Form($this->plugin_name, $this->version, $this->options);
    	//$this->forms['post_form'] = new Tkt_Post_Form($this->plugin_name, $this->version, array());
		add_action( 'template_redirect', array($this->forms['post_form'], 'handle_form') );
    	$this->forms['term_form'] = new Tkt_Term_Form($this->plugin_name, $this->version, $this->options);
    	$this->forms['user_form'] = new Tkt_User_Form($this->plugin_name, $this->version, $this->options);
    }

}
