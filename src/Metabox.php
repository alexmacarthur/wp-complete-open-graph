<?php

namespace CompleteOpenGraph;

class Metabox extends App {

    /**
     * Add actions.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array($this, 'add_meta_box' ));
        add_action( 'save_post', array($this, 'save' ));
    }

    /**
     * Add meta box for the post. Only display on post types that are publicly queryable.
     */
    public function add_meta_box() {
        $currentPostType = Utilities::get_current_post_type();

        $queryablePostTypes = get_post_types(array(
            'publicly_queryable' => true
        ));

        //-- Oddly enough, pages aren't publicly queryable by default, so this ensures they're counted.
        $queryablePostTypes["page"] = "page";

        if(!in_array($currentPostType, $queryablePostTypes)) return;

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

        $imageURL = wp_get_attachment_image_src(Utilities::get_post_option('og:image'), 'medium')[0];

        ?>
            <p class="main-description">These fields will allow you to customize the open graph data for the page or post.</p>

            <div id="cogMetaBox" class="COG-fieldsWrapper <?php if(!Utilities::get_post_option('og:image')): ?>has-no-image<?php endif; ?>">

                <fieldset class="SK_Box">
                    <label for="complete_open_graph_title">Title</label>
                    <p>If left blank, the <strong><?php echo $post_type; ?> title</strong> will be used. If no <?php echo $post_type; ?> exists, the site title will be used.</p>
                    <input type="text" value="<?php echo Utilities::get_post_option('og:title'); ?>" name="complete_open_graph_title" id="ogTitle" />
                </fieldset>

                <fieldset class="SK_Box">
                    <label for="complete_open_graph_description">Description</label>
                    <p>If left blank, the <strong><?php echo $post_type; ?> excerpt</strong> will be used. If no <?php echo $post_type; ?> excerpt exists, the <strong>site description</strong> will be used.</p>
                    <input type="text" value="<?php echo Utilities::get_post_option('og:description'); ?>" name="complete_open_graph_description" id="ogDescription" />
                </fieldset>

                <fieldset class="SK_Box">
                    <label for="complete_open_graph_twitter_description">Twitter Description</label>
                    <p>If left blank, the <strong>description</strong> will be used.</p>
                    <input type="text" value="<?php echo Utilities::get_post_option('twitter:description'); ?>" name="complete_open_graph_twitter_description" id="ogTwitterDescription" />
                </fieldset>

                <fieldset class="SK_Box">
                    <label for="complete_open_graph_twitter_creator">Twitter Creator</label>
                    <p>If left blank, the global value will be used. It doesn't matter if you include the '@' symbol.</p>
                    <input type="text" value="<?php echo Utilities::get_post_option('twitter:creator'); ?>" name="complete_open_graph_twitter_creator" id="ogTwitterCreator" />
                </fieldset>

                <fieldset class="SK_Box">
                    <label for="complete_open_graph_twitter_card">Twitter Summary Card Type</label>
                    <p>The type of Twitter card that will be generated for Open Graph. To learn about what these types mean, see <a target="_blank" href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/abouts-cards">Twitter's documentation</a>.</p>

                    <?php $cardValue = Utilities::get_post_option('twitter:card'); ?>

                    <select name="complete_open_graph_twitter_card" id="ogTwitterCard">
                        <option <?php selected($cardValue, 'summary'); ?> value="summary">Summary</option>
                        <option <?php selected($cardValue, 'summary_large_image'); ?> value="summary_large_image">Large Summary</option>
                        <option <?php selected($cardValue, 'app'); ?> value="app">App</option>
                        <option <?php selected($cardValue, 'player'); ?> value="player">Player</option>
                    </select>

                </fieldset>

                <fieldset class="SK_Box">
                    <label for="complete_open_graph_type">Type</label>
                    <p>If left blank, the <strong>global 'type'</strong> value will be used. If you choose to override it, make sure it follows the correct <a href="https://developers.facebook.com/docs/reference/opengraph/" target="_blank">object type formatting</a>.</p>
                    <input type="text" value="<?php echo Utilities::get_post_option('og:type'); ?>" name="complete_open_graph_type" id="ogType" />
                </fieldset>

                <fieldset class="SK_Box">
                    <span class="SK_Box-label">Image</span>
                    <p>If left empty, the <?php echo $post_type; ?>'s featured image will be used. If no featured image exists, the front page featured image will be used.</p>
                    <div class="SK_ImageHolder"
                        id="cogImageHolder"
                        style="background-image: url('<?php echo $imageURL; ?>')">
                        <span class="SK_ImageHolder-remove" id="ogRemoveImage">x</span>
                    </div>
                    <span class="howto" id="cogUploadedFileName"><?php echo basename($imageURL); ?></span>
                    <div class="SK_Box-buttonWrapper">
                        <a class="button button-primary button-large" id="cogImageSelectButton">Choose Image</a>
                        <span>No image selected.</span>
                    </div>
                    <input type="hidden" value="<?php echo Utilities::get_post_option('og:image'); ?>" name="complete_open_graph_image" id="cogImage">
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
    public function save( $post_id ){

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if( !isset( $_POST[self::$options_prefix . '_nonce'] ) || !wp_verify_nonce( $_POST[self::$options_prefix . '_nonce'], self::$options_prefix . '_nonce_verification' ) ) return;

        if( !current_user_can( 'edit_posts' ) ) return;

        //-- Make sure the image we're saving actually returns a real string.
		if(isset($_POST['complete_open_graph_image'])) {
			$image = $_POST['complete_open_graph_image'];
			$imagePath = get_attached_file($image);

			if(is_numeric($image) && !file_exists($imagePath)) {
				$_POST['complete_open_graph_image'] = '';
			}
		}

        $newPostMeta = array(
            'og:title' => esc_attr($_POST['complete_open_graph_title']),
            'og:description' => esc_attr($_POST['complete_open_graph_description']),
            'og:image' => esc_attr($_POST['complete_open_graph_image']),
            'og:type' => esc_attr($_POST['complete_open_graph_type']),
            'twitter:card' => esc_attr($_POST['complete_open_graph_twitter_card']),
            'twitter:description' => esc_attr($_POST['complete_open_graph_twitter_description']),
            'twitter:creator' => esc_attr($_POST['complete_open_graph_twitter_creator'])
        );

        return update_post_meta( $post_id, self::$options_prefix, $newPostMeta );
    }
}
