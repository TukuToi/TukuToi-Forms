<?php

/**
 * Instantiate all TukuToi Forms
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/public
 */

/**
 * Instantiate all Forms
 *
 * Maintain a list of all Forms Classe here
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/includes
 * @author     TukuToi <hello@tukutoi.com>
 */

class Tkt_Form {

	protected $errors;
	protected $data;
	private $plugin_name;
	private $version;
	private $form_id;


	public function __construct( $plugin_name, $version, $human_plugin_name ) {

		$this->plugin_name 			= $plugin_name;
		$this->version 				= $version;
		$this->human_plugin_name 	= $human_plugin_name;

		$this->tkt_form_action 		= $this->plugin_name .'_submit' . md5(__CLASS__);
		$this->tkt_nonce_field 		= $this->plugin_name .'_submit_nonce_' . md5(__CLASS__);

		$this->errors 				= array();
		$this->data 				= array();
		$this->form_id 				= spl_object_hash($this);

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
	 * Check if form submitted
	 * @return bool true false
	 * @access protected	
	 */
    protected function is_form_submitted() {
        return isset( $_POST['submit_form'] );
    }

    /**
	 * Sanitize Image Inputs
	 * @return string $output the mime type or nothing
	 * @param $input  string the escaped form field input, ig url
	 * @access protected	
	 */
    protected function sanitize_img_inputs($input){
	    
	    $output = '';
	
	    $filetype = wp_check_filetype( $input );
	    $mime_type = $filetype['type'];
	 
	    if ( strpos( $mime_type, 'image' ) !== false ){
	        $output = $input;
	    }
	 
	    return $output;

    }

    /**
	 * WP Data Sanitization
	 * Possible Filter Types
	 * @see https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
	 * @return array 	$data The sanitized form data used to update objects later
	 * @param $inputs 	array [input_key,input_value]
	 * @param $filters 	array [input_key,FILTER_TYPE]
	 * @access protected	
	 */
    protected function sanitize_inputs( $inputs = array(), $filters = array() ){

		/**
		 * Remove array key value pairs from $inputs that are not part of the public form
		 */
		foreach ($inputs as $input_name => $value) {
			if( !array_key_exists($input_name, $filters) )
				unset($inputs[$input_name]);
		}

		/**
		 * Sanitize each of the inputs according to $filters map
		 */
		foreach ($inputs as $input_name => $value) {
			if( $filters[$input_name] == 'SANITIZE_TEXT_FIELD' )
				$sanitized_values[$input_name] = sanitize_text_field( $value );
			elseif( $filters[$input_name] == 'SANITIZE_TITLE_FIELD')
				$sanitized_values[$input_name] = sanitize_title( $value );
			elseif( $filters[$input_name] == 'SANITIZE_RICH_FIELD')
				$sanitized_values[$input_name] = wp_filter_post_kses( $value );
			elseif( $filters[$input_name] == 'SANITIZE_NUM_FIELD')
				$sanitized_values[$input_name] = absint( $value );
			elseif( $filters[$input_name] == 'SANITIZE_URL_FIELD')
				$sanitized_values[$input_name] = esc_url( $value );
			elseif( $filters[$input_name] == 'SANITIZE_IMG_FIELD')
				$sanitized_values[$input_name] = $this->sanitize_img_inputs( esc_url( $value ) );
			elseif( $filters[$input_name] == 'SANITIZE_FILE_FIELD')
				$sanitized_values[$input_name] = sanitize_file_name( $value );
			elseif( $filters[$input_name] == 'SANITIZE_USER_NAME')
				$sanitized_values[$input_name] = sanitize_user( $value );
			else
				$sanitized_values[$input_name] = sanitize_text_field( $value );
		}
		
        /**
         * Remove trailing slahes
         */
        $sanitized_values = wp_unslash( $sanitized_values );

        /**
         * Remove start and end whitespace
         */
        $sanitized_values = array_map( 'trim', $sanitized_values );
        
        /**
         * Build data.
         */
        $this->data = $sanitized_values;

	}

	/**
	 * Validate required Inputs
	 * @return string message
	 * @param $inputs array [input_key,input_label]
	 * @access protected	
	 */
    protected function validate_required_inputs( $inputs = array() ){
		foreach ($inputs as $key => $name) {
			if( !$_POST[$key] || $_POST[$key] == '' ){
				$this->errors[$key] = 'The '. $name .' field must be filled';
			}
		}
	}

	protected function prepare_data( $data = array(), $prefix ){
		foreach ( $data as $name => $value ) {
			$post_data_prefix = substr( $name, 0, strlen( $prefix ) ) === $prefix;
			if( $post_data_prefix == true){
				$name = str_replace($prefix, '', $name);
				$data[$name] = $data[$prefix.$name];
				unset($data[$prefix.$name]);
			}
			else{
				unset($data[$name]);
			}
		}
		return $data;
	}

	/**
	 * Handle the form Prototype
	 * Here we budle the single form actions
	 * Check if the form is submitted
	 * Check if the nonce is valid
	 * Check if required inputs are filled
	 * Check on errors
	 * Perform form actions (ig update/insert)
	 * @access public	
	 */
	public function handle_form(){

		if ( ! $this->is_form_submitted() )
            return false;

        //sec

        if ( ! $this->is_nonce_valid() )
            $this->errors['security_error'] = 'Security check failed, please try again.';

        $this->validate_required_inputs();

        if ( ! $this->errors ) {
        	//handle
        }

	}

	/**
	 * Get Form HTML Prototype
	 * Here we build the form HTML and display errors
	 * @access public	
	 */
	public function get_form(){

		ob_start();


        foreach ( $this->errors as $error ) { 
            echo $error;
        };

		?>
		<form id="<?php echo $this->form_id ?>" method="post" enctype="multipart/form-data">
			<label for="<?php echo $this->form_id ?>_default_input">Default Input Label</label>
            <input type="text" name="default_input" id="<?php echo $this->form_id ?>_default_input" value="" placeholder="This is a Default Form" />
            <button type="submit" name="submit_form"><?php _e( 'Submit Form',  $this->plugin_name ); ?>"</button>
            <?php echo apply_filters( 'tkt_form_fields', '' ); ?>
            <?php wp_nonce_field( $this->tkt_form_action, $this->tkt_nonce_field ) ?>
		</form>
		<?php

		return ob_get_clean();

	}

}
