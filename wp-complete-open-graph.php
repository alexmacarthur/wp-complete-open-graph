<?php
/**
* Plugin Name: Complete Open Graph
* Description: Simple, comprehensive, highly customizable Open Graph management.
* Version: 1.0.2
* Author: Alex MacArthur
* Author URI: http://macarthur.me
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( !defined( 'WPINC' ) ) {
  die;
}

if(!class_exists('CompleteOG')) {
  class CompleteOG {

    private static $instance;

    public static function generate_instance() {
      if(!isset(self::$instance) && !(self::$instance instanceof CompleteOG)) {
          self::$instance = new CompleteOG;
        }
    }

    public function __construct() {
      add_action( 'after_setup_theme', array($this, 'add_og_image_size' ));
      add_action( 'admin_enqueue_scripts', array($this, 'enqueue_styles_and_scripts' ));
      add_action( 'add_meta_boxes', array($this, 'add_open_graph_meta_box' ));
      add_action( 'save_post', array($this, 'open_graph_metabox_save' ));
      add_action( 'wp_head', array($this, 'open_graph_tag_generation'));
      add_action(	'admin_menu' , array($this, 'open_graph_settings_page'));
      add_action( 'admin_init', array($this, 'register_open_graph_settings') );
    }

    public function register_open_graph_settings () {
      register_setting( 'cog_global_settings', 'cog_global_type' );
      register_setting( 'cog_global_settings', 'cog_global_title' );
      register_setting( 'cog_global_settings', 'cog_global_image' );
      register_setting( 'cog_global_settings', 'cog_global_description' );
      register_setting( 'cog_global_settings', 'cog_global_fb_admin_ids' );
      register_setting( 'cog_global_settings', 'cog_global_fb_app_id' );
      register_setting( 'cog_global_settings', 'cog_global_twitter_description' );
    }

    public function open_graph_settings_page() {
      add_submenu_page('options-general.php', 'Open Graph Settings', 'Open Graph', 'edit_posts', 'open-graph', array($this, 'open_graph_settings_page_cb'));
    }

    public function open_graph_settings_page_cb() {
      $cog_global_type = get_option('cog_global_type');
      $cog_global_image = get_option('cog_global_image');
      $cog_global_title = get_option('cog_global_title');
      $cog_global_fb_admin_ids = get_option('cog_global_fb_admin_ids');
      $cog_global_fb_app_id = get_option('cog_global_fb_app_id');
      $cog_global_description = get_option('cog_global_description');
      $cog_global_twitter_description = get_option('cog_global_twitter_description');
    ?>
      <div id="cogSettingsBox" class="wrap <?php if(!$cog_global_image): ?>has-no-image<?php endif; ?>">
        <h1>Complete Open Graph Global Settings</h1>
        <p></p>
        <div class="SettingsWrapper">
          <form method="post" action="options.php">
            <?php wp_nonce_field('update-options'); ?>
            <?php settings_fields( 'cog_global_settings' ); ?>

            <fieldset class="SettingsWrapper-fieldset">
              <h3>Global Type</h3>
              <p>If left blank, 'website' will be used.</p>
              <input type="text" value="<?php echo $cog_global_type; ?>" name="cog_global_type" id="ogType" />
            </fieldset>

            <fieldset class="SettingsWrapper-fieldset">
              <h3>Global Title</h3>
              <p>If left blank, the site title will be used.</p>
              <input type="text" value="<?php echo $cog_global_title; ?>" name="cog_global_title" id="ogDescription" />
            </fieldset>

            <fieldset class="SettingsWrapper-fieldset">
              <h3>Global Description</h3>
              <p>If left blank, the site description will be used.</p>
              <input type="text" value="<?php echo $cog_global_description; ?>" name="cog_global_description" id="ogDescription" />
            </fieldset>

            <fieldset class="SettingsWrapper-fieldset">
              <h3>Global Twitter Description</h3>
              <p>If left blank, the description will be used.</p>
              <input type="text" value="<?php echo $cog_global_twitter_description; ?>" name="cog_global_twitter_description" id="ogDescription" />
            </fieldset>
            <fieldset class="SettingsWrapper-fieldset">
              <h3>Global Image</h3>
              <p>If left blank, the featured image on the home page will be used.</p>
              <div class="ImageHolder"
                id="cogImageHolder"
                style="background-image: url('<?php echo $cog_global_image; ?>')">
                <span class="ImageHolder-remove" id="ogRemoveImage">x</span>
              </div>
              <span class="howto ImageHolder-fileName" id="cogUploadedFileName"><?php echo basename($cog_global_image); ?></span>
              <div class="COG-buttonWrapper">
                <a class="button button-primary button-large" id="cogImageSelectButton">Choose File</a>
                <span>No image selected.</span>
              </div>
              <input id="cogImage" type="hidden" name="cog_global_image" value="<?php echo $cog_global_image; ?>" />
            </fieldset>

            <fieldset class="SettingsWrapper-fieldset">
              <h3>Facebook Admin ID(s)</h3>
              <p>Enter the user ID of a person you'd like to give admin access to view insights about this URL.</p>
              <input type="text" value="<?php echo $cog_global_fb_admin_ids; ?>" name="cog_global_fb_admin_ids" id="ogFbAdminIds" />
            </fieldset>

            <fieldset class="SettingsWrapper-fieldset">
              <h3>Facebook App ID</h3>
              <p>Enter the ID of the Facebook app you'd like to grant access to this URL.</p>
              <input type="text" value="<?php echo $cog_global_fb_app_id; ?>" name="cog_global_fb_app_id" id="ogFbAppId" />
            </fieldset>

            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="cog_global_image,cog_global_fb_admin_ids,cog_global_fb_app_id,cog_global_type,cog_global_title,cog_global_description,cog_global_twitter_description" />

            <p class="submit">
              <input type="submit" class="button-primary" value="Save Changes" />
            </p>

            </form>
          </div>
        </div>
      <?php
    }

    public function add_og_image_size() {
      add_image_size('open-graph', 1200, 1200);
    }

    public function enqueue_styles_and_scripts() {
      wp_enqueue_media();
      wp_print_scripts('media-upload');
      wp_enqueue_style( 'complete-open-graph', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), null);
      wp_enqueue_script( 'complete-open-graph', plugin_dir_url( __FILE__ ) . '/assets/js/scripts.js', array(), null, true );
    }

    public function add_open_graph_meta_box() {
      add_meta_box( 'open_graph_metabox', 'Open Graph Settings', array($this, 'open_graph_metabox_cb'), null, 'normal', 'low' );
    }

    public function open_graph_metabox_cb() {
      global $post;
      $post_type = get_post_type( $post->ID );
      $values = get_post_custom( $post->ID );
      $cog_title = isset($values['cog_title'][0]) ? $values['cog_title'][0] : false;
      $cog_type = isset($values['cog_type'][0]) ? $values['cog_type'][0] : false;
      $cog_image = isset($values['cog_image'][0]) ? $values['cog_image'][0] : false;
      $cog_description = isset($values['cog_description'][0]) ? $values['cog_description'][0] : false;
      $cog_twitter_description = isset($values['cog_twitter_description'][0]) ? $values['cog_twitter_description'][0] : false;

      wp_nonce_field( 'cog_nonce_verification', 'cog_nonce' );

      ?>
        <p class="main-description">These fields will allow you to customize the open graph data for the page or post.</p>
        <div id="cogMetaBox" class="COG-fieldsWrapper <?php if(!$cog_image): ?>has-no-image<?php endif; ?>">

          <fieldset class="SettingsWrapper-fieldset">
            <label for="cog_title">Title</label>
            <p>If left blank, the <strong><?php echo $post_type; ?> title</strong> will be used. If no <?php echo $post_type; ?> exists, the site title will be used.</p>
            <input type="text" value="<?php echo $cog_title; ?>" name="cog_title" id="ogTitle" />
          </fieldset>

          <fieldset class="SettingsWrapper-fieldset">
            <label for="cog_description">Description</label>
            <p>If left blank, the <strong><?php echo $post_type; ?> excerpt</strong> will be used. If no <?php echo $post_type; ?> excerpt exists, the <strong>site description</strong> will be used.</p>
            <input type="text" value="<?php echo $cog_description; ?>" name="cog_description" id="ogDescription" />
          </fieldset>

          <fieldset class="SettingsWrapper-fieldset">
            <label for="cog_twitter_description">Twitter Description</label>
            <p>If left blank, the <strong>description</strong> will be used.</p>
            <input type="text" value="<?php echo $cog_twitter_description; ?>" name="cog_twitter_description" id="ogTwitterDescription" />
          </fieldset>

          <fieldset class="SettingsWrapper-fieldset">
            <label for="cog_type">Type</label>
            <p>If left blank, the <strong>global 'type'</strong> value will be used. If you choose to override it, make sure it follows the correct <a href="https://developers.facebook.com/docs/reference/opengraph/" target="_blank">object type formatting</a>.</p>
            <input type="text" value="<?php echo $cog_type; ?>" name="cog_type" id="ogType" />
          </fieldset>

          <fieldset class="SettingsWrapper-fieldset">
            <span class="SettingsWrapper-spanLabel">Image</span>
            <p>If left empty, the <?php echo $post_type; ?>'s featured image will be used. If no featured image exists, the front page featured image will be used.</p>
            <div class="ImageHolder"
              id="cogImageHolder"
              style="background-image: url('<?php echo $cog_image; ?>')">
              <span class="ImageHolder-remove" id="ogRemoveImage">x</span>
            </div>
            <span class="howto ImageHolder-fileName" id="cogUploadedFileName"><?php echo basename($cog_image); ?></span>
            <div class="COG-buttonWrapper">
              <a class="button button-primary button-large" id="cogImageSelectButton">Choose File</a>
              <span>No image selected.</span>
            </div>
            <input type="hidden" value="<?php echo $cog_image; ?>" name="cog_image" id="cogImage">
          </fieldset>

        </div>
      <?php
    }

    public function open_graph_metabox_save( $post_id ){

      if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

      if( !isset( $_POST['cog_nonce'] ) || !wp_verify_nonce( $_POST['cog_nonce'], 'cog_nonce_verification' ) ) return;

      if( !current_user_can( 'edit_posts' ) ) return;

      if( isset( $_POST['cog_title'] ) )
          update_post_meta( $post_id, 'cog_title', esc_attr( $_POST['cog_title'] ) );

      if( isset( $_POST['cog_description'] ) )
          update_post_meta( $post_id, 'cog_description', esc_attr( $_POST['cog_description'] ) );

      if( isset( $_POST['cog_twitter_description'] ) )
          update_post_meta( $post_id, 'cog_twitter_description', esc_attr( $_POST['cog_twitter_description'] ) );

      if( isset( $_POST['cog_image'] ) )
          update_post_meta( $post_id, 'cog_image', esc_attr( $_POST['cog_image'] ) );

      if( isset( $_POST['cog_type'] ) )
          update_post_meta( $post_id, 'cog_type', esc_attr( $_POST['cog_type'] ) );
    }

    private function get_open_graph_values() {
      global $post;
      global $wp;

      $post_ID = isset($post) ? $post->ID : 0;

      $cog_site_name = get_bloginfo('name');
      $cog_url = get_permalink($post_ID);

      $post_cog_type = isset(get_post_meta( $post_ID, 'cog_type' )[0]) ? get_post_meta( $post_ID, 'cog_type' )[0] : false;
      if($post_cog_type) {
        $cog_type = $post_cog_type;
      } else if(get_option('cog_global_type')) {
        $cog_type = get_option('cog_global_type');
      } else {
        $cog_type = is_single() ? 'article' : 'website';
      }

      $post_cog_title = isset(get_post_meta( $post_ID, 'cog_title' )[0]) ? get_post_meta( $post_ID, 'cog_title' )[0] : false;
      if($post_cog_title) {
        $cog_title = $post_cog_title;
      } else if(get_the_title()) {
        $cog_title = get_the_title();
      } else if(get_option('cog_global_title')) {
        $cog_title = get_option('cog_global_title');
      } else {
        $cog_title = $cog_site_name;
      }

      $post_excerpt = isset(get_post($post_ID)->post_content) ? substr(strip_tags(get_post($post_ID)->post_content), 0, 300) : false;
      $post_cog_description = isset(get_post_meta( $post_ID, 'cog_description' )[0]) ? get_post_meta( $post_ID, 'cog_description' )[0] : false;
      if($post_cog_description) {
        $cog_description = $post_cog_description;
      } else if($post_excerpt) {
        $cog_description = $post_excerpt;
      } else if(get_option('cog_global_description')) {
        $cog_description = get_option('cog_global_description');
      } else {
        $cog_description = get_bloginfo('description');
      }

      $post_cog_twitter_description = isset(get_post_meta( $post_ID, 'cog_twitter_description' )[0]) ? get_post_meta( $post_ID, 'cog_twitter_description' )[0] : false;
      if($post_cog_twitter_description) {
        $cog_twitter_description = $post_cog_twitter_description;
      } else if($post_cog_description) {
        $cog_twitter_description = $post_cog_description;
      } else if($post_excerpt) {
        $cog_twitter_description = $post_excerpt;
      } else if(get_option('cog_global_twitter_description')) {
        $cog_twitter_description = get_option('cog_global_twitter_description');
      } else {
        $cog_twitter_description = $cog_description;
      }

      $post_cog_image = isset(get_post_meta( $post_ID, 'cog_image' )[0]) ? get_post_meta( $post_ID, 'cog_image' )[0] : false;
      if($post_cog_image) {
        $cog_image = $post_cog_image;
      } else if(has_post_thumbnail()) {
        $cog_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_ID ), 'open-graph')[0];
      } else if(get_option('cog_global_image')) {
        $cog_image = get_option('cog_global_image');
      } else if(has_post_thumbnail(get_option('page_on_front'))){
        $cog_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_option('page_on_front') ), 'open-graph')[0];
      } else {
        $cog_image = false;
      }

      $cog_data = array(
        'site_name' => array(
          'type' => 'open_graph',
          'value' => $cog_site_name
        ),

        'url' => array(
          'type' => 'open_graph',
          'value' => $cog_url
        ),

        'locale' => array(
          'type' => 'open_graph',
          'value' => 'en_us'
        ),

        'description' => array(
          'type' => 'open_graph',
          'value' => $cog_description
        ),

        'title' => array(
          'type' => 'open_graph',
          'value' => $cog_title
        ),

        'type' => array(
          'type' => 'open_graph',
          'value' => $cog_type
        ),

        'image' => array(
          'type' => 'open_graph',
          'value' => $cog_image
        ),

        'fb:admins' => array(
          'type' => 'open_graph',
          'value' => get_option('cog_global_fb_admin_ids') ? get_option('cog_global_fb_admin_ids') : false
        ),

        'fb:app_id' => array(
          'type' => 'open_graph',
          'value' => get_option('cog_global_fb_app_id') ? get_option('cog_global_fb_app_id') : false
        ),

        'twitter:creator' => array(
          'type' => 'standard',
          'value' => false
        ),

        'twitter:title' => array(
          'type' => 'standard',
          'value' => $cog_title
        ),

        'twitter:image' => array(
          'type' => 'standard',
          'value' => $cog_image
        ),

        'twitter:description' => array(
          'type' => 'standard',
          'value' => $cog_twitter_description
        )
      );

      return apply_filters('cog_open_graph_data', $cog_data);
    }

    public function open_graph_tag_generation() {
      $cog_data = $this->get_open_graph_values();

      foreach($cog_data as $key=>$data) {
        $content = preg_replace( "/\r|\n/", "", htmlentities($data['value']));

        if($data['value']) {
          if($data['type'] === 'open_graph') {
            ?><meta property="og:<?php echo $key; ?>" content="<?php echo $content; ?>" /><?php
            echo "\n";
          }

          if($data['type'] === 'standard') {
            ?><meta name="<?php echo $key; ?>" content="<?php echo $content; ?>" /><?php
            echo "\n";
          }
        }
      }
    }
  }

  CompleteOG::generate_instance();
}
