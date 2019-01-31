<?php
/**
 * Plugin Name: Complete Open Graph
 * Description: Simple, comprehensive, highly customizable Open Graph management.
 * Version: 3.4.5
 * Author: Alex MacArthur
 * Author URI: https://macarthur.me
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package CompleteOpenGraph
 */

namespace CompleteOpenGraph;

if (! defined('WPINC')) {
    die;
}

require_once(trailingslashit(ABSPATH) . 'wp-admin/includes/plugin.php');

define('COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX', 'complete_open_graph');
define('COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX', 'complete_open_graph');
define('COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG', 'complete_open_graph');
define('COMPLETE_OPEN_GRAPH_REAL_PATH', trailingslashit(realpath(dirname(__FILE__))));
define('COMPLETE_OPEN_GRAPH_IMAGE_WIDTH', 1200);
define('COMPLETE_OPEN_GRAPH_IMAGE_HEIGHT', 1200);

require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/Generator.php';
require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/Utilities.php';
require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/Field.php';

/**
 * Responsible for registering global assets and requiring code.
 */
class App
{

    /**
     * Initialize the plugin.
     *
     * @return object App Instance of class.
     */
    public static function go()
    {
        $GLOBALS[ __CLASS__ ] = new self;
        return $GLOBALS[ __CLASS__ ];
    }

    /**
     * Retrive array of plugin data.
     *
     * @return array
     */
    public static function getPluginData()
    {
        return get_plugin_data(__DIR__ . '/complete-open-graph.php');
    }

    /**
     * Instatiate necessary classes, enqueue admin scripts.
     */
    public function __construct()
    {
        require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/helpers.php';
        require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/hooks/generate-open-graph-markup.php';
        require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/hooks/content-filters.php';
        require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/hooks/settings.php';
        require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/hooks/metabox.php';
        require_once COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/hooks/support.php';
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ));
    }

    /**
     * Delete global and post/page data.
     *
     * @return void
     */
    public static function delete_options_and_meta()
    {
        global $wpdb;
        delete_option(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX);
        $wpdb->delete($wpdb->prefix . 'postmeta', array( 'meta_key' => COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX ));
    }

    /**
     * Enqueue global admin scripts & styles.
     *
     * @return void
     */
    public function enqueue_styles_and_scripts()
    {
        wp_enqueue_style('complete-open-graph', plugin_dir_url(__FILE__) . 'src/assets/css/style.css', array(), self::getPluginData()['Version']);
        wp_enqueue_script('complete-open-graph', plugin_dir_url(__FILE__) . 'src/assets/js/scripts.js', array( 'jquery' ), self::getPluginData()['Version'], true);
    }
}

App::go();

// -- On uninstallation, delete plugin-related database stuffs.
register_uninstall_hook(__FILE__, array( '\CompleteOG\App', 'delete_options_and_meta' ));
