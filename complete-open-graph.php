<?php
/**
* Plugin Name: Complete Open Graph
* Description: Simple, comprehensive, highly customizable Open Graph management.
* Version: 2.1.4
* Author: Alex MacArthur
* Author URI: http://macarthur.me
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CompleteOG;

if ( !defined( 'WPINC' ) ) {
  die;
}

require __DIR__ . '/vendor/autoload.php';

use CompleteOG\Metabox;
use CompleteOG\OpenGraph;

class App {

  private static $instance;
  public $version = '2.1.4';
  protected static $options_prefix = 'complete_open_graph';
  protected static $admin_settings_page_slug = 'complete_open_graph';
  protected static $options_short_prefix = 'cog';

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
    wp_enqueue_script( 'complete-open-graph', plugin_dir_url( __FILE__ ) . 'src/assets/js/scripts.js', array(), null, true );
  }
}

App::generate_instance();

//-- On uninstallation, delete plugin-related database stuffs.
register_uninstall_hook( __FILE__, array('\CompleteOG\App', 'delete_options_and_meta') );
