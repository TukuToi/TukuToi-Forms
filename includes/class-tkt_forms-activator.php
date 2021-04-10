<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tkt_forms
 * @subpackage Tkt_forms/includes
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_forms_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

	private $tkt_forms_db_version = '1.0';

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	private $plugin_name;

	public function __construct() {

	    global $tkt_forms_db_version;

	    $this->plugin_name = 'tkt_forms';
	    global $wpdb;
        $this->wpdb = $wpdb;
        $this->forms_table = $this->wpdb->prefix.$this->plugin_name;
        $this->notifications_table = $this->wpdb->prefix.$this->plugin_name.'_notifications';

	}

	private function database_install() {
		
		$charset_collate = $this->wpdb->get_charset_collate();

		$forms = "CREATE TABLE $this->forms_table (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			form_title text NOT NULL,
			form_name varchar(200) NOT NULL,
			creation_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			form_html longtext NOT NULL,
			form_js longtext NOT NULL,
			form_css longtext NOT NULL,
			form_type varchar(20) DEFAULT 'post_form' NOT NULL,
			PRIMARY KEY  (ID)
		) $charset_collate;";

		$notifications = "CREATE TABLE $this->notifications_table (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			notification_title text NOT NULL,
			notification_name varchar(200) NOT NULL,
			creation_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			notification_content longtext NOT NULL,
			PRIMARY KEY  (ID)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $forms );
		dbDelta( $notifications );

		add_option( $this->plugin_name .'_db_version', $tkt_forms_db_version );

	}

	public function activate() {

		$this->database_install();

	}

}
