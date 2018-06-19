<?php
/**
* Plugin Name: Complete Open Graph
* Description: Simple, comprehensive, highly customizable Open Graph management.
* Version: 3.2.6
* Author: Alex MacArthur
* Author URI: https://macarthur.me
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CompleteOpenGraph;

if ( !defined( 'WPINC' ) ) {
  die;
}

require_once(trailingslashit(ABSPATH) . 'wp-admin/includes/plugin.php');

require_once 'src/Filters.php';
require_once 'src/Settings.php';
require_once 'src/Metabox.php';
require_once 'src/OpenGraph.php';

class App {

  private static $instance;
	public $controllers = array();
	protected static $plugin_data = null;
  protected static $options_prefix = 'complete_open_graph';
  protected static $admin_settings_page_slug = 'complete_open_graph';
  protected static $options_short_prefix = 'cog';
  protected static $options = null;
  protected static $post_options = null;
	protected static $post_decorator = null;

  public static function generate_instance() {

		if (!isset($GLOBALS[static::class]) || is_null($GLOBALS[static::class])) {
			$GLOBALS[static::class] = new static();
		}

  }

  /**
   * Instatiate necessary classes, enqueue admin scripts.
   */
  public function __construct() {
		self::$plugin_data = get_plugin_data(__DIR__ . '/complete-open-graph.php');

    $this->controllers['Settings'] = new Settings;
    $this->controllers['Metabox'] = new Metabox;
    $this->controllers['OpenGraph'] = new OpenGraph;
    $this->controllers['Filters'] = new Filters;

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
   * Enqueue global admin scripts & styles.
   *
   * @return void
   */
  public function enqueue_styles_and_scripts() {
    wp_enqueue_style( 'complete-open-graph', plugin_dir_url( __FILE__ ) . 'src/assets/css/style.css', array(), self::$plugin_data['Version']);
    wp_enqueue_script( 'complete-open-graph', plugin_dir_url( __FILE__ ) . 'src/assets/js/scripts.js', array('jquery'), self::$plugin_data['Version'], true );
  }
}

App::generate_instance();

//-- On uninstallation, delete plugin-related database stuffs.
register_uninstall_hook( __FILE__, array('\CompleteOG\App', 'delete_options_and_meta') );
