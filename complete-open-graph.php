<?php
/**
* Plugin Name: Complete Open Graph
* Description: Simple, comprehensive, highly customizable Open Graph management.
* Version: 3.1.1
* Author: Alex MacArthur
* Author URI: https://macarthur.me
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CompleteOpenGraph;

if ( !defined( 'WPINC' ) ) {
  die;
}

require_once 'src/Filters.php';
require_once 'src/Settings.php';
require_once 'src/Metabox.php';
require_once 'src/OpenGraph.php';

class App {

  private static $instance;
  public $version = '3.1.2';
  protected static $options_prefix = 'complete_open_graph';
  protected static $admin_settings_page_slug = 'complete_open_graph';
  protected static $options_short_prefix = 'cog';
  protected static $options = null;
  protected static $post_options = null;
  protected static $post_decorator = null;

  public static function generate_instance() {
    if(!isset(self::$instance) && !(self::$instance instanceof App)) {
        self::$instance = new App;
      }
  }

  /**
   * Instatiate necessary classes, enqueue admin scripts.
   */
  public function __construct() {
    new Settings;
    new Metabox;
    new OpenGraph;
    new Filters;

    add_action( 'admin_enqueue_scripts', array($this, 'enqueue_styles_and_scripts' ));
  }

  /**
   * Delete global and post/page data.
   *
   * @return void
   */
  public static function delete_options_and_meta() {
    global $wpdb;
    delete_option(self::$options_prefix);
    $wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => self::$options_prefix) );
  }

  /**
   * Enqueue necessary admin scripts & styles.
   *
   * @return void
   */
  public function enqueue_styles_and_scripts() {
    wp_enqueue_media();
    wp_print_scripts('media-upload');
    wp_enqueue_style( 'complete-open-graph', plugin_dir_url( __FILE__ ) . 'src/assets/css/style.css', array(), null);
    wp_enqueue_script( 'complete-open-graph', plugin_dir_url( __FILE__ ) . 'src/assets/js/scripts.js', array('jquery'), null, true );
  }
}

App::generate_instance();

//-- On uninstallation, delete plugin-related database stuffs.
register_uninstall_hook( __FILE__, array('\CompleteOG\App', 'delete_options_and_meta') );
