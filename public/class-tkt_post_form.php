<?php
/**
 * The file that defines the Post Form class
 *
 * A class definition that includes attributes and functions used for Post Forms.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/public
 */

/**
 * The Post Form class.
 *
 * This is used to build, sanitize, validate and handl the Post Forms.
 *
 * It inherits parent Class Tkt_Form which deliveres useful reusable methods
 *
 * @since      1.0.0
 * @package    Tkt_forms
 * @subpackage Tkt_forms/public
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_Post_Form extends Tkt_Form{

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
     * The errors array of the Form
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $errors    All errors registered during Form Submit.
     */
	protected $errors;

	/**
     * The FORM ID 
     *
     * @since    1.0.0
     * @access   private
     * @var      int   $form_id 	The unique ID of the FORM (NOT of the current object).
     */
	private $form_id;

	/**
     * The Object ID
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $ID    	The ID of the currently created or edited Object.
     */
	private $ID;

	/**
     * The Object Type
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $type    The Type of the currently created or edited Object.
     */
	private $type;

	/**
	 * Define the core settings of the Form.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Construct the parent, set the Form ID, nonce action and field,
	 * ID and Type of Object (if set thru an URL paramater, other wise uses setter)
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version, $human_plugin_name ) {

		parent::__construct( $plugin_name, $version, $human_plugin_name );
		
		$this->plugin_name 			= $plugin_name;
		$this->version 				= $version;
		$this->human_plugin_name 	= $human_plugin_name;

		$this->form_id 				= spl_object_hash($this);
		// $this->ID   				= !empty($_GET['post_id']) ? preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['post_id']) : '';
  //       $this->type 				= !empty($_GET['type']) ? preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['type']) : '';
		$this->tkt_form_action 		= $this->plugin_name .'_submit' . md5(__CLASS__);
		$this->tkt_nonce_field 		= $this->plugin_name .'_submit_nonce_' . md5(__CLASS__);

	}

	/**
	 * Check if nonce valid
	 * @return bool true false
	 * @access private	
	 */
	private function is_nonce_valid() {
        return isset( $_POST[$this->tkt_nonce_field] ) && wp_verify_nonce( $_POST[$this->tkt_nonce_field], $this->tkt_form_action );
    }

	/**
	 * Handle the Post form 
	 * Here we budle the single form actions
	 * Check if the form is submitted
	 * Check if the nonce is valid
	 * Check if required inputs are filled
	 * Check on errors
	 * Perform form actions (ig update/insert)
	 * @access public	
	 */
	public function handle_form(){

		//Is the form submitted
		if ( ! $this->is_form_submitted() )
            return false;

        //Is the nonce valid
        if ( ! $this->is_nonce_valid() )
            $this->errors['security_error'] = 'Security check failed, please try again.';

        /**
         * Sanitise $_POSTed data. See also Tkt_Form
         * takes array of [input_name, SANITIZATION_TYPE]
         * @todo apply_filter('','');
         */
        $this->sanitize_inputs($_POST, array(
			'postform_post_title'	=>    'SANITIZE_TEXT_FIELD',
			//'postform_post_content' =>    'SANITIZE_TITLE_FIELD',
			'postform_post_content' 	=>    'SANITIZE_RICH_FIELD',
			//'postform_post_title' 	=>    'SANITIZE_NUM_FIELD',
			//'postform_post_title' 	=>    'SANITIZE_URL_FIELD',
			//'postform_post_title' 	=>    'SANITIZE_IMG_FIELD',
			//'postform_post_title' 	=>    'SANITIZE_FILE_FIELD',
			//'postform_post_title' 	=>    'SANITIZE_USER_NAME',
        ));

        /**
         * Validate $_POSTed data. See also Tkt_Form
         * Takes array of [input_name, Input Label]
		 * @todo apply_filter('','');
         */
        $this->validate_required_inputs( array(
        	'postform_post_title' => 'Post Title',
        ));

        /**
         * The form was submitted
         * The Nonce is valid
         * Sanitizaation cleaned our inputs
         * There are no validation errors
         * 
         * Kickoff data insertion
         */
        if ( ! $this->errors ) {

        	/**
             * Clean $_POSTed data input names. See also Tkt_Form
             * Prepare to map to Object properties
             */
			$post_data = $this->prepare_data( $this->data, 'postform_' );
				
			if( empty( $post_data['post_type'] ) || !isset( $post_data['post_type'] ) ){//Post Type was not set in form
				$post_data['post_type'] = $this->type;//Hence set from URL or ShortCode attribute.
			}

            if ($this->ID == '') {//If we have no ID this is a "Create" Form

				/**
				 * All necessary $_POSTed data is prepared to insert a Post
				 * @return int $post_id The created Post ID
				 */
				$post_id = wp_insert_post( 
                    $post_data 
                );

            } 
            else {//We have an ID from either URL or ShortCode, thus is a "Edit" Form
                
				/**
				 * The Post ID is known
				 * However it will be missing from $post_data
				 * $this->ID is already safe to use (see $this __constructor and set_formdata())
				 * also assign the ID to $post_id
				 */        
                $post_data['ID'] = $this->ID;

			   	/**
				 * All necessary $_POSTed data is prepared to insert a Post
				 * @return int $post_id The updated Post ID
				 */
			   	$post_id = wp_update_post( 
			   		$post_data
                );

            }

            /**
             * Proceed updating newly inserted or updatd Post 
             * Send notifications
             * Redirect on successs
             * Abort on Failure
             */
            if ( $post_id ) {
            	
            	//do magic meta updating, taxonomies, repeating fields, etc here
            	//send eventual notifications
            	//apply eventual filters

            	/**
            	 * Redirect to Post or message
            	 */
                if( get_post_status( $post_id ) == 'publish' ){//The post was created and published

               		wp_redirect( get_permalink( $post_id ) );

				}
				elseif( get_post_status( $post_id ) != 'publish' ){//The post was created but is not published

					/**
					 * Build Query args for redirect
					 * Later we can use these to display messages
					 */
					$args = add_query_arg( array(
						'post_id' => $post_id,
						'success' => 'true',
					));

               		wp_redirect( $args );

				}
                
                exit;//After redirect, exit.

            }
            else {//There was a problem creating the post after all, error unknown

            	/**
            	 * @todo apply_filters('','')
            	 */
                $this->errors['unknown_error'] = 'This Article does not exist or is corrupt. This should not have happened. Please contact Administration.';

            }

        }

	}

	/**
	 * Get Form HTML 
	 * Here we build the form HTML and display errors
	 * @access public	
	 */
	public function get_form(){

		$form_fields = $this->form_fields($this->ID, $this->data);
		$form_fields = apply_filters( 'tkt_form_fields', $form_fields, $this->ID, $this->type);
		ob_start();

		

		?>
		<div class="<?php echo $this->plugin_name ?>_wrapper>" id="<?php echo $this->form_id ?>_wrapper">

			<div class="<?php echo $this->plugin_name ?>_errors>" id="<?php echo $this->form_id ?>_errors">
		        <?php
		        foreach ( $this->errors as $key => $error ) {
		        	?> 
		            <span class="<?php echo $this->plugin_name?>_error" id="<?php echo $this->form_id ?>_<?php echo $key ?>">
						<?php echo $error ?>
					</span>
		            <?php
		        };
				?>
			</div>

			<form id="<?php echo $this->form_id ?>" method="post" enctype="multipart/form-data">
				

	            <?php echo $form_fields; ?>
	            <button type="submit" name="submit_form"><?php _e( 'Submit Post Form',  $this->plugin_name ); ?></button>
	            <?php wp_nonce_field( $this->tkt_form_action, $this->tkt_nonce_field ) ?>
			</form>
		</div>
			<?php

		return ob_get_clean();
	}

	private function form_fields($id, $data){
		$html  = '<label for="postform_post_title">Post Title</label>';
	    $html .= '<input type="text" name="postform_post_title" id="postform_post_title" placeholder="Here Goes The New Post Title" value="'. get_post($id)->post_name .'"/>';
	    return $html;
	}

	/**
	 * Set Post ID and Type
	 * We can set this in URL or ShortCode. ShortCode wins over URL.
	 * @param $shortcode 
	 * @access public
	 */
	public function set_formdata( $shortcode_args = array() ){

		$this->ID 	= !empty($_GET['post_id']) ? preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['post_id']) : '';
        $this->type = !empty($_GET['type']) ? preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['type']) : '';
		
		if( empty($shortcode_args) || !isset($shortcode_args) ){
			return;
		}

		if( !empty($shortcode_args['id']) )
			$this->ID = $shortcode_args['id'];

		if( !empty($shortcode_args['type']) )
			$this->type = $shortcode_args['type'];

		// error_log( print_r( '$this->type setter', true) );
		// error_log( print_r( $this->type, true) );
		// error_log( print_r( $this->ID, true) );
	}

}