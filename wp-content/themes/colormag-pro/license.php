<?php


/**
 * Handles Theme license and automatic updates.
 *
 * @package ThemeGrill
 * @subpackage Colormag Pro
 * @since Colormag 1.0
 */

/**
 * WooCommerce API Manager code integration
 *
 * Localization for translation is not provided in this example
 */

/**
 * Displays an inactive message if the API License Key has not yet been activated
 */
if ( get_option( 'api_manager_theme_colormag_activated' ) != 'Activated' ) {
	add_action( 'admin_notices', 'API_Manager_theme_Colormag::am_theme_colormag_inactive_notice' );
}

class API_Manager_theme_Colormag {

	/**
	 * Self Upgrade Values
	 */
	// Base URL to the remote upgrade API server
	public $upgrade_url = 'http://themegrill.com/'; // URL to access the Update API Manager.

	/**
	 * @var string
	 * This version is saved after an upgrade to compare this db version to $version
	 */
	public $api_manager_theme_colormag_version_name = 'api_manager_theme_colormag_version';

	/**
	 * @var string
	 */
	public $theme_url;

	/**
	 * @var string
	 */
	public $version;

	/**
	 * @var ojb
	 */
	private $my_theme;

	/**
	 * @var string
	 * used to defined localization for translation, but a string literal is preferred
	 *
	 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/issues/59
	 * http://markjaquith.wordpress.com/2011/10/06/translating-wordpress-plugins-and-themes-dont-get-clever/
	 * http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
	 */
	public $text_domain = 'colormag';

	/**
	 * Data defaults
	 * @var mixed
	 */
	private $ame_software_product_id;

	public $ame_data_key;
	public $ame_api_key;
	public $ame_activation_email;
	public $ame_product_id_key;
	public $ame_instance_key;
	public $ame_deactivate_checkbox_key;
	public $ame_activated_key;

	public $ame_deactivate_checkbox;
	public $ame_activation_tab_key;
	public $ame_deactivation_tab_key;
	public $ame_settings_menu_title;
	public $ame_settings_title;
	public $ame_menu_tab_activation_title;
	public $ame_menu_tab_deactivation_title;

	public $ame_options;
	public $ame_plugin_name;
	public $ame_product_id;
	public $ame_renew_license_url;
	public $ame_instance_id;
	public $ame_domain;
	public $ame_software_version;
	public $ame_plugin_or_theme;

	public $ame_update_version;

	public $ame_update_check = 'am_colormag_theme_update_check';

	public $api_manager_theme_colormag_key;

	/**
	 * Used to send any extra information.
	 * @var mixed array, object, string, etc.
	 */
	public $ame_extra;

    /**
     * @var The single instance of the class
     */
    protected static $_instance = null;

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
        	self::$_instance = new self();
        }

        return self::$_instance;
    }

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.2
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'colormag' ), '1.2' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.2
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'colormag' ), '1.2' );
	}

	public function __construct() {

		if ( is_admin() ) {

			// Check for external connection blocking
			add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );

			add_action( 'admin_init', array( $this, 'activation' ) );

			$this->my_theme = wp_get_theme(); // Get theme data

			// Set the version based on this plugin's header Version
			$this->version = $this->my_theme->get( 'Version' );

			/**
			 * Set all data defaults here
			 */
			$this->ame_data_key 				= 'api_manager_theme_colormag';
			$this->ame_api_key 					= 'api_key';
			$this->ame_activation_email 		= 'activation_email';
			$this->ame_product_id_key 			= 'api_manager_theme_colormag_product_id';
			$this->ame_instance_key 			= 'api_manager_theme_colormag_instance';
			$this->ame_deactivate_checkbox_key 	= 'api_manager_theme_colormag_deactivate_checkbox';
			$this->ame_activated_key 			= 'api_manager_theme_colormag_activated';

			/**
			 * Set all admin menu data
			 */
			$this->ame_deactivate_checkbox 			= 'am_deactivate_colormag_checkbox';
			$this->ame_activation_tab_key 			= 'api_manager_theme_colormag_dashboard';
			$this->ame_deactivation_tab_key 		= 'api_manager_theme_colormag_deactivation';
			$this->ame_settings_menu_title 			= 'Colormag License Manager';
			$this->ame_settings_title 				= 'Colormag License Manager';
			$this->ame_menu_tab_activation_title 	= __( 'License Activation', 'colormag' );
			$this->ame_menu_tab_deactivation_title 	= __( 'License Deactivation', 'colormag' );

			/**
			 * Set all software update data here
			 */
			$this->ame_options 				= get_option( $this->ame_data_key );
			$this->ame_plugin_name 			= get_stylesheet();
			// Gets the Theme Name from the stylesheet
			$this->ame_product_id 			= trim( $this->my_theme->get( 'Name' ) ); // Software Title
			$this->ame_renew_license_url 	= 'http://themegrill.com/my-account'; // URL to renew a license
			$this->ame_instance_id 			= get_option( $this->ame_instance_key ); // Instance ID (unique to each blog activation)
			/**
			 * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
			 * so only the host portion of the URL can be sent. For example the host portion might be
			 * www.example.com or example.com. http://www.example.com includes the scheme http,
			 * and the host www.example.com.
			 * Sending only the host also eliminates issues when a client site changes from http to https,
			 * but their activation still uses the original scheme.
			 * To send only the host, use a line like the one below:
			 *
			 * $this->ame_domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
			 */
			$this->ame_domain 				= str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
			$this->ame_software_version 	= $this->version; // The software version
			$this->ame_plugin_or_theme 		= 'theme'; // 'theme' or 'plugin'

			/**
			 * Software Product ID is the product title string
			 * This value must be unique, and it must match the API tab for the product in WooCommerce
			 * Gets the theme name from the stylesheet Theme Name:
			 * For example Theme Name: Testtheme
			 * Testtheme would then need to be the Software Title in the product edit API tab data field
			 */
			$this->ame_software_product_id = __( $this->ame_product_id, 'colormag' );

			// Performs activations and deactivations of API License Keys
			require_once( 'am/classes/class-wc-key-api.php' );
			$this->api_manager_theme_colormag_key = new Api_Manager_theme_Colormag_Key();

			// Checks for software updatess
			require_once( 'am/classes/class-wc-plugin-update.php' );

			// Admin menu with the license key and license email form
			require_once( 'am/admin/class-wc-api-manager-menu.php' );

			$options = get_option( $this->ame_data_key );

			/**
			 * Check for software updates
			 */
			if ( ! empty( $options ) && $options !== false ) {

				$this->update_check(
					$this->upgrade_url,
					$this->ame_plugin_name,
					$this->ame_product_id,
					$this->ame_options[$this->ame_api_key],
					$this->ame_options[$this->ame_activation_email],
					$this->ame_renew_license_url,
					$this->ame_instance_id,
					$this->ame_domain,
					$this->ame_software_version,
					$this->ame_plugin_or_theme,
					$this->text_domain
					);

			}

			// remotely deactivate license upon switching away from this theme
			add_action( 'switch_theme', array( $this, 'uninstall' ) );

		}

	}

	/** Load Shared Classes as on-demand Instances **********************************************/

	/**
	 * Update Check Class.
	 *
	 * @return API_Manager_Colormag_theme_Update_API_Check
	 */
	public function update_check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '' ) {

		return API_Manager_Colormag_theme_Update_API_Check::instance( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra );
	}

	/**
	 * Gets a properly formed URL, even for child themes
	 * @return string
	 */
	public function theme_url() {
		if ( isset( $this->theme_url ) ) {
			return $this->theme_url;
		}

		return $this->theme_url = get_stylesheet_directory_uri() . '/';
	}

	/**
	 * Generate the default data arrays
	 */
	public function activation() {

		if ( get_option( $this->ame_data_key ) === false || get_option( $this->ame_instance_key ) === false ) {

			global $wpdb;

			$global_options = array(
				$this->ame_api_key 				=> '',
				$this->ame_activation_email 	=> '',
						);

			update_option( $this->ame_data_key, $global_options );

			require_once( 'am/classes/class-wc-api-manager-passwords.php' );

			$api_manager_theme_colormag_password_management = new API_Manager_Colormag_theme_Password_Management();

			// Generate a unique installation $instance id
			$instance = $api_manager_theme_colormag_password_management->generate_password( 12, false );

			$single_options = array(
				$this->ame_product_id_key 			=> $this->ame_software_product_id,
				$this->ame_instance_key 			=> $instance,
				$this->ame_deactivate_checkbox_key 	=> 'on',
				$this->ame_activated_key 			=> 'Deactivated',
				);

			foreach ( $single_options as $key => $value ) {
				update_option( $key, $value );
			}

			$curr_ver = get_option( $this->api_manager_theme_colormag_version_name );

			// checks if the current plugin version is lower than the version being installed
			if ( version_compare( $this->version, $curr_ver, '>' ) ) {
				// update the version
				update_option( $this->api_manager_theme_colormag_version_name, $this->version );
			}

		}

	}

	/**
	 * Deletes all data using the Deactivate API License Key checkbox
	 * @return void
	 */
	public function uninstall() {
		global $wpdb, $blog_id;

		$this->license_key_deactivation();

		// Remove options
		if ( is_multisite() ) {

			switch_to_blog( $blog_id );

			foreach ( array(
					$this->ame_data_key,
					$this->ame_product_id_key,
					$this->ame_instance_key,
					$this->ame_deactivate_checkbox_key,
					$this->ame_activated_key,
					) as $option) {

					delete_option( $option );

					}

			restore_current_blog();

		} else {

			foreach ( array(
					$this->ame_data_key,
					$this->ame_product_id_key,
					$this->ame_instance_key,
					$this->ame_deactivate_checkbox_key,
					$this->ame_activated_key
					) as $option) {

					delete_option( $option );

					}

		}

	}

	/**
	 * Deactivates the license on the API server
	 * @return void
	 */
	public function license_key_deactivation() {

		$activation_status = get_option( $this->ame_activated_key );

		$api_email = $this->ame_options[$this->ame_activation_email];
		$api_key = $this->ame_options[$this->ame_api_key];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);

		if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
			$this->api_manager_theme_colormag_key->deactivate( $args ); // reset license key activation
		}
	}

    /**
     * Displays an inactive notice when the software is inactive.
     */
	public static function am_theme_colormag_inactive_notice() { ?>
		<?php if ( ! current_user_can( 'manage_options' ) ) return; ?>
		<?php if ( isset( $_GET['page'] ) && 'api_manager_theme_colormag_dashboard' == $_GET['page'] ) return; ?>
		<div id="message" class="error">
			<p><?php printf( __( 'The License Key for Theme Colormag has not been activated, %sClick here%s to activate the license key.', 'colormag' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=api_manager_theme_colormag_dashboard' ) ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check for external blocking contstant
	 * @return string
	 */
	public function check_external_blocking() {
		// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
		if( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

			// check if our API endpoint is in the allowed hosts
			$host = parse_url( $this->upgrade_url, PHP_URL_HOST );

			if( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
				?>
				<div class="error">
					<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', 'colormag' ), $this->ame_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>'); ?></p>
				</div>
				<?php
			}

		}
	}

} // End of class

function AMET() {
    return API_Manager_theme_Colormag::instance();
}

// Initialize the class instance only once
AMET();
