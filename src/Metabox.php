<?php

namespace CompleteOG;
use CompleteOG\Utilities;

class Metabox extends App {

  /**
   * Add actions.
   */
  public function __construct() {
    add_action( 'add_meta_boxes', array($this, 'add_open_graph_meta_box' ));
    add_action( 'save_post', array($this, 'open_graph_meta_box_save' ));
  }

  /**
   * Add meta box for the post.
   */
  public function add_open_graph_meta_box() {
    add_meta_box( 'complete_open_graph_metabox', 'Open Graph Settings', array($this, 'open_graph_metabox_cb'), null, 'normal', 'low' );
  }

  /**
   * Generate markup for the meta box.
   *
   * @return void
   */
  public function open_graph_metabox_cb() {
    $post_type = get_post_type();
    wp_nonce_field( self::$options_prefix . '_nonce_verification', self::$options_prefix . '_nonce' );

    ?>
      <p class="main-description">These fields will allow you to customize the open graph data for the page or post.</p>
      <div id="cogMetaBox" class="COG-fieldsWrapper <?php if(!Utilities::get_post_option('image')): ?>has-no-image<?php endif; ?>">

        <fieldset class="SK_Box">
          <label for="complete_open_graph_title">Title</label>
          <p>If left blank, the <strong><?php echo $post_type; ?> title</strong> will be used. If no <?php echo $post_type; ?> exists, the site title will be used.</p>
          <input type="text" value="<?php echo Utilities::get_post_option('title'); ?>" name="complete_open_graph_title" id="ogTitle" />
        </fieldset>

        <fieldset class="SK_Box">
          <label for="complete_open_graph_description">Description</label>
          <p>If left blank, the <strong><?php echo $post_type; ?> excerpt</strong> will be used. If no <?php echo $post_type; ?> excerpt exists, the <strong>site description</strong> will be used.</p>
          <input type="text" value="<?php echo Utilities::get_post_option('description'); ?>" name="complete_open_graph_description" id="ogDescription" />
        </fieldset>

        <fieldset class="SK_Box">
          <label for="complete_open_graph_twitter_description">Twitter Description</label>
          <p>If left blank, the <strong>description</strong> will be used.</p>
          <input type="text" value="<?php echo Utilities::get_post_option('twitter_description'); ?>" name="complete_open_graph_twitter_description" id="ogTwitterDescription" />
        </fieldset>

        <fieldset class="SK_Box">
          <label for="complete_open_graph_twitter_creator">Twitter Creator</label>
          <p>If left blank, the global value will be used. It doesn't matter if you include the '@' symbol.</p>
          <input type="text" value="<?php echo Utilities::get_post_option('twitter_creator'); ?>" name="complete_open_graph_twitter_creator" id="ogTwitterCreator" />
        </fieldset>

        <fieldset class="SK_Box">
          <label for="complete_open_graph_type">Type</label>
          <p>If left blank, the <strong>global 'type'</strong> value will be used. If you choose to override it, make sure it follows the correct <a href="https://developers.facebook.com/docs/reference/opengraph/" target="_blank">object type formatting</a>.</p>
          <input type="text" value="<?php echo Utilities::get_post_option('type'); ?>" name="complete_open_graph_type" id="ogType" />
        </fieldset>

        <fieldset class="SK_Box">
          <span class="SK_Box-label">Image</span>
          <p>If left empty, the <?php echo $post_type; ?>'s featured image will be used. If no featured image exists, the front page featured image will be used.</p>
          <div class="SK_ImageHolder"
            id="cogImageHolder"
            style="background-image: url('<?php echo Utilities::get_post_option('image'); ?>')">
            <span class="SK_ImageHolder-remove" id="ogRemoveImage">x</span>
          </div>
          <span class="howto" id="cogUploadedFileName"><?php echo basename(Utilities::get_post_option('image')); ?></span>
          <div class="SK_Box-buttonWrapper">
            <a class="button button-primary button-large" id="cogImageSelectButton">Choose Image</a>
            <span>No image selected.</span>
          </div>
          <input type="hidden" value="<?php echo Utilities::get_post_option('image'); ?>" name="complete_open_graph_image" id="cogImage">
        </fieldset>

      </div>
    <?php
  }

  /**
   * Handle meta box saving.
   *
   * @param  integer $post_id The post/page ID.
   * @return mixed ID if the meta didn't exist, true/false if the update failed or succeeded.
   */
  public function open_graph_meta_box_save( $post_id ){

    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if( !isset( $_POST[self::$options_prefix . '_nonce'] ) || !wp_verify_nonce( $_POST[self::$options_prefix . '_nonce'], self::$options_prefix . '_nonce_verification' ) ) return;

    if( !current_user_can( 'edit_posts' ) ) return;

    $newPostMeta = array(
      'title' => esc_attr($_POST['complete_open_graph_title']),
      'description' => esc_attr($_POST['complete_open_graph_description']),
      'twitter_description' => esc_attr($_POST['complete_open_graph_twitter_description']),
      'twitter_creator' => esc_attr($_POST['complete_open_graph_twitter_creator']),
      'image' => esc_attr($_POST['complete_open_graph_image']),
      'type' => esc_attr($_POST['complete_open_graph_type'])
    );

    return update_post_meta( $post_id, self::$options_prefix, $newPostMeta );
  }
}
