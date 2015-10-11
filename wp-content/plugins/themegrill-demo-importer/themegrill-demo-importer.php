<?php
/**
 * Plugin Name: ThemeGrill Demo Importer
 * Plugin URI: http://themegrill.com/demo-importer/
 * Description: ThemeGrill Demo Importer plugin is used for uploading demo data of themes by ThemeGrill.
 * Author: ThemeGrill
 * Author URI: http://themegrill.com
 * Version: 1.0
 *
 * ThemeGrill Demo Importer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * ThemeGrill Demo Importer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Maintenance Page. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Themegrill_Demo_Importer
 * @author ThemeGrill <themegrill@gmail.com>
 * @version 1.0
 */

/**
 * If this file is attempted to be accessed directly, we'll exit.
 *
 * The following check provides a level of security from other files
 * that request data directly.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Themegrill_Demo_Importer' ) ) :
/**
 * Main Class
 *
 * @since 1.0
 */
class Themegrill_Demo_Importer {
   /**
    * Stores the singleton instance.
    *
    * @access private
    *
    * @var object
    */
   private static $instance;

   /**
    * The attachment ID.
    *
    * @access private
    *
    * @var int
    */
   private $file_id_data;
   private $file_id_setting;

   /**
    * The transient key template used to store the options after upload.
    *
    * @access private
    *
    * @var string
    */
   private $transient_key = 'options-import-%d';

   /**
    * Stores the import data from the uploaded file.
    *
    * @access public
    *
    * @var array
    */
   public $import_setting;

   public $import_data;


   /**
    * Private constructor prevents construction outside this class.
    */
   Private function __construct() {
   }

   /**
    * Throw error on object clone
    *
    * The whole idea of the singleton design pattern is that there is a single
    * object therefore, we don't want the object to be cloned.
    *
    * @since 1.0
    * @access protected
    * @return void
    */
   public function __clone() { wp_die( "Please don't __clone Themegrill_Demo_Importer" ); }

   public function __wakeup() { wp_die( "Please don't __wakeup Themegrill_Demo_Importer" ); }


   /**
    * ThemeGrill Demo Importer Instance
    *
    * Insures that only one instance of ThemeGrill_Demo_Importer exists in memory at any one
    * time. In singleton class you cannot create a second instance
    *
    * @since 1.0
    * @static
    * @static var array $instance
    * @return The one true ThemeGrill_Demo_Importer
    */
   public static function instance() {
      if ( !isset( self::$instance ) ) {
         self::$instance = new self();
         self::$instance->setup_constants();
         self::$instance->setup();

      }
      return self::$instance;
   }

   /**
    * Setup plugin constants
    *
    * @access private
    * @since 1.0
    * @return void
    */
   private function setup_constants() {
      // Plugin version
      $this->version    = '1.0';

      // Setup some base path and URL information
      $this->file       = __FILE__; // Plugin Root File
      $this->plugin_dir = apply_filters( 'tg_plugin_dir_path',  plugin_dir_path( $this->file ) ); // Plugin Folder Path
      $this->plugin_url = apply_filters( 'tg_plugin_dir_url',   plugin_dir_url ( $this->file ) ); // Plugin Folder URL
      $this->basename   = apply_filters( 'tg_plugin_basenname', plugin_basename( $this->file ) ); //path relative to plugin folder
   }

   public function setup() {
      add_action( 'admin_init', array( $this, 'register_importer' ) );
      add_action( 'admin_enqueue_scripts', array( $this ,'tg_importer_style' ) );
   }

   /**
    * Register our importer.
    *
    * @return void
    */
   public function register_importer() {
      if ( function_exists( 'register_importer' ) ) {
         register_importer( 'tg-demo-import', __( 'ThemeGrill Demo Data', 'themegrill-demo-importer' ), __( 'Import Demo Data of themes by ThemeGrill', 'themegrill-demo-importer' ), array( $this, 'dispatch' ) );
      }
   }

   /**
    * Enqueue style and scripts necessary
    */
   public function tg_importer_style( $hook ) {
      if ( 'admin.php' != $hook ) {
        return;
      }
      if( $_GET['import'] == 'tg-demo-import' ) {
         wp_enqueue_style( 'tg_custom_style', $this->plugin_url . 'assets/css/tg-importer.css' );
      }
   }

   /**
    * Registered callback function for the ThemeGrill Demo Data
    *
    * Manages the three separate stages of the import process.
    *
    * @return void
    */
   public function dispatch() {
      $this->header();

      if ( empty( $_GET['step'] ) ) {
         $_GET['step'] = 0;
      }

      switch ( intval( $_GET['step'] ) ) {
         case 0:
            $this->greet();
         break;

         case 1:
            check_admin_referer( 'import-upload' );
            if ( $this->handle_upload_data() ) {

               $this->greetagain();
            } else {
               echo '<p><a href="' . esc_url( admin_url( 'admin.php?import=tg-demo-import' ) ) . '">' . __( 'Return to File Upload', 'themegrill-demo-importer' ) . '</a></p>';
            }
         break;

         case 2:
            //check_admin_referer( 'import-upload' );
            if ( $this->handle_upload_setting() ) {

               $this->import_data = get_transient( 'demo-data' );
               $this->import_setting = get_transient( $this->transient_key() );
               $this->import();
            } else {

               echo '<p><a href="' . esc_url( admin_url( 'admin.php?import=tg-demo-import' ) ) . '">' . __( 'Return to File Upload', 'themegrill-demo-importer' ) . '</a></p>';
            }
         break;
      }

      $this->footer();
   }

   /**
    * Start the options import page HTML.
    *
    * @return void
    */
   private function header() {
      echo '<div class="tg-importer-wrap">';
      echo '<h1>' . __( 'ThemeGrill Demo Data Importer', 'themegrill-demo-importer' ) . '</h1>';
   }


   /**
    * End the options import page HTML.
    *
    * @return void
    */
   private function footer() {
      echo '</div>';
   }

   /**
    * Display introductory text and file upload form.
    *
    * @return void
    */
   private function greet() {
      echo '<div class="narrow">';
      echo '<h2>'.__( "Step 1:").'</h2>';
      printf( '<p>For detail instruction go through this <a href="%s" target="_blank" title="importing-demo-content">Importing Demo Content</a></p>',esc_url( "http://themegrill.com/theme-instruction/importing-demo-content/" ) );
      echo '<div class="alert alert-warning"><p>'.__( '<stromg>WARNING:</strong> Uploading demo data will replace your current theme options, sliders and widgets. It will take few minutes to complete. Importing data is recommended on fresh installs only. Importing on sites with content or importing twice will duplicate menus, pages and all posts.', 'themegrill-demo-importer' ).'</p></div>';
      echo '<p>'.__( 'Choose XML (.xml) file to upload, then click Upload file and import.', 'themegrill-demo-importer' ).'</p>';

      wp_import_upload_form( 'admin.php?import=tg-demo-import&amp;step=1' );
      echo '</div>';
   }

   private function greetagain() {
      echo '<div class="narrow">';
      echo '<h2>'.__( "Step 2:").'</h2>';
      echo '<p>'.__( 'Choose a JSON (.json) file to upload, then click Upload file and import.', 'themegrill-demo-importer' ).'</p>';
      echo '<div class="alert alert-info"><p>'.__( 'It will take some time to load demo data. Please wait for a moment.', 'themegrill-demo-importer' ).'</p></div>';
      wp_import_upload_form( 'admin.php?import=tg-demo-import&amp;step=2' );
      echo '</div>';
   }

   /**
    * Handles the XML upload
    */
   private function handle_upload_data() {
      $file = wp_import_handle_upload();

      if ( isset( $file['error'] ) ) {
         return $this->error_message(
            __( 'Sorry, there has been an error.', 'themegrill-demo-importer' ),
            esc_html( $file['error'] )
         );
      }

      if ( ! isset( $file['file'], $file['id'] ) ) {
         return $this->error_message(
            __( 'Sorry, there has been an error.', 'themegrill-demo-importer' ),
            __( 'The file did not upload properly. Please try again.', 'themegrill-demo-importer' )
         );
      }

      $this->file_id_data = intval( $file['id'] );

      if ( ! file_exists( $file['file'] ) ) {
         wp_import_cleanup( $this->file_id_data );
         return $this->error_message(
            __( 'Sorry, there has been an error.', 'themegrill-demo-importer' ),
            sprintf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'themegrill-demo-importer' ), esc_html( $file['file'] ) )
         );
      }

      if ( ! is_file( $file['file'] ) ) {
         wp_import_cleanup( $this->file_id_data );
         return $this->error_message(
            __( 'Sorry, there has been an error.', 'themegrill-demo-importer' ),
            __( 'The path is not a file, please try again.', 'themegrill-demo-importer' )
         );
      }

      $this->import_data =  $file['file'];
      set_transient( 'demo-data', $this->import_data, HOUR_IN_SECONDS );

      //wp_import_cleanup( $this->file_id_data );

      return true;
   }

   /**
    * Handles the JSON upload and initial parsing of the file to prepare for
    * displaying author import options
    *
    * @return bool False if error uploading or invalid file, true otherwise
    */
   private function handle_upload_setting() {
      $file = wp_import_handle_upload();

      if ( isset( $file['error'] ) ) {
         return $this->error_message(
            __( 'Sorry, there has been an error.', 'themegrill-demo-importer' ),
            esc_html( $file['error'] )
         );
      }

      if ( ! isset( $file['file'], $file['id'] ) ) {
         return $this->error_message(
            __( 'Sorry, there has been an error.', 'themegrill-demo-importer' ),
            __( 'The file did not upload properly. Please try again.', 'themegrill-demo-importer' )
         );
      }

      $this->file_id_setting = intval( $file['id'] );

      if ( ! file_exists( $file['file'] ) ) {
         wp_import_cleanup( $this->file_id_setting );
         return $this->error_message(
            __( 'Sorry, there has been an error.', 'themegrill-demo-importer' ),
            sprintf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'themegrill-demo-importer' ), esc_html( $file['file'] ) )
         );
      }

      if ( ! is_file( $file['file'] ) ) {
         wp_import_cleanup( $this->file_id_setting );
         return $this->error_message(
            __( 'Sorry, there has been an error.', 'themegrill-demo-importer' ),
            __( 'The path is not a file, please try again.', 'themegrill-demo-importer' )
         );
      }
      $this->import_setting =  $file['file'];
      $file_contents = file_get_contents( $file['file'] );
      $this->import_setting = json_decode( $file_contents, true );
      set_transient( $this->transient_key(), $this->import_setting, DAY_IN_SECONDS );
      wp_import_cleanup( $this->file_id_setting );

      return $this->run_data_check();
   }

   /**
    * The main controller for the actual import stage.
    *
    * @return void
    */
   private function import() {
      if ( current_user_can( 'manage_options' ) ) {
         if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true); // we are loading importers

         if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
            $wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
            include $wp_importer;
         }

         if ( ! class_exists('WP_Import') ) { // if WP importer doesn't exist
            $wp_import =  dirname( __FILE__ ) . '/wordpress-importer/wordpress-importer.php';
            include $wp_import;
         }

         if ( class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) { // check for main import class and wp import class

            $importer = new WP_Import();
            /* Import Posts, Pages, Portfolio Content, FAQ, Images, Menus */
            $importer->fetch_attachments = true;
            ob_start();
            $importer->import( $this->import_data );
            ob_end_clean();
         }
         delete_transient( 'demo-data' );
         //wp_import_cleanup( $this->file_id_data );
      }

      if ( $this->run_data_check() ) {

         $options_to_import = array();
         $options_to_import = array_keys( $this->import_setting['options'] );

         foreach ( (array) $options_to_import as $option_name ) {
            if ( isset( $this->import_setting['options'][ $option_name ] ) ) {

               $option_value = maybe_unserialize( $this->import_setting['options'][ $option_name ] );
               if ( in_array( $option_name, $this->import_setting['no_autoload'] ) ) {
                  delete_option( $option_name );
                  add_option( $option_name, $option_value, '', 'no' );
               } else {
                  update_option( $option_name, $option_value );
               }
            }
         }

         $this->clean_up();
         echo '<p>' . __( 'All done. That was easy.', 'themegrill-demo-importer' ) . ' <a href="' . admin_url() . '">' . __( 'Have fun!', 'themegrill-demo-importer' ) . '</a>' . '</p>';
      }
   }

   /**
    * Run a series of checks to ensure we're working with a valid JSON export.
    *
    * @return bool true if the file and data appear valid, false otherwise.
    */
   private function run_data_check() {
      if ( empty( $this->import_setting['version'] ) ) {
         $this->clean_up();
         return $this->error_message( __( 'Sorry, there has been an error. This file may not contain data or is corrupt.', 'themegrill-demo-importer' ) );
      }

      if ( empty( $this->import_setting['options'] ) ) {
         $this->clean_up();
         return $this->error_message( __( 'Sorry, there has been an error. This file appears valid, but does not seem to have any options.', 'themegrill-demo-importer' ) );
      }

      return true;
   }


   private function transient_key() {
      return sprintf( $this->transient_key, $this->file_id_setting );
   }

   private function clean_up() {
      delete_transient( $this->transient_key() );
   }

   /**
    * A helper method to keep DRY with our error messages. Note that the error messages
    * must be escaped prior to being passed to this method (this allows us to send HTML).
    *
    * @param  string $message The main message to output.
    * @param  string $details Optional. Additional details.
    * @return bool false
    */
   private function error_message( $message, $details = '' ) {
      echo '<div class="error"><p><strong>' . $message . '</strong>';
      if ( ! empty( $details ) ) {
         echo '<br />' . $details;
      }
      echo '</p></div>';
      return false;
   }

}
endif;
Themegrill_Demo_Importer::instance();