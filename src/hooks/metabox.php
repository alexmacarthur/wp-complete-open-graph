<?php

namespace CompleteOpenGraph;

add_action('add_meta_boxes', 'CompleteOpenGraph\register_meta_box');
add_action('save_post', 'CompleteOpenGraph\save');

/**
 * Add meta box for the post. Only display on post types that are publicly queryable.
 */
function register_meta_box()
{
    $currentPostType = Utilities::get_current_post_type();

    $queryablePostTypes = get_post_types(
        array(
            'publicly_queryable' => true,
        )
    );

    // -- Oddly enough, pages aren't publicly queryable by default, so this ensures they're counted.
    $queryablePostTypes['page'] = 'page';

    if (! in_array($currentPostType, $queryablePostTypes)) {
        return;
    }

    add_meta_box('complete_open_graph_metabox', 'Open Graph Settings', 'CompleteOpenGraph\open_graph_metabox_cb', null, 'normal', 'low');
}

/**
 * Generate markup for the meta box.
 *
 * @return void
 */
function open_graph_metabox_cb()
{
    $post_type = get_post_type();
    wp_nonce_field(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_nonce_verification', COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_nonce');

    $imageURL = wp_get_attachment_image_src(Utilities::get_post_option('og:image'), 'medium')[0]; ?>
        <p class="main-description">These fields will allow you to customize the open graph data for the page or post.</p>

        <div id="cogMetaBox" class="COG-fieldsWrapper
        <?php
        if (! Utilities::get_post_option('og:image')) :
            ?>
        <?php endif; ?>">

            <?php foreach (Field::getConfigurable() as $field) : ?>
                <fieldset class="SK_Box">
                    <label for="<?php echo $field->id; ?>">
                        <?php echo $field->label; ?>
                    </label>
                    <p>
                        <?php echo $field->description; ?>
                    </p>

                    <?php

                    switch ($field->field_type) {
                        case 'text':
                            ?>
                                <input type="text" value="<?php echo Utilities::get_post_option($field->key); ?>" name="<?php echo $field->name; ?>" id="<?php echo $field->id; ?>" />
                            <?php
                            break;

                        case 'select':
                            $cardValue = Utilities::get_post_option($field->key);
                            ?>
                                <select name="<?php echo $field->name; ?>" id="<?php echo $field->id; ?>">
                                    <?php foreach ($field->field_options as $label => $value) : ?>
                                        <option <?php selected($cardValue, $value); ?> value="<?php echo $value; ?>">
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php
                            break;
                    } ?>
                </fieldset>

            <?php endforeach; ?>

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
                <input type="hidden" value="<?php echo Utilities::get_post_option('og:image'); ?>" name="complete_open_graph_og:image" id="cogImage">
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
function save($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (! isset($_POST[ COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_nonce' ]) ||
        ! wp_verify_nonce($_POST[ COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_nonce' ], COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_nonce_verification')
    ) {
        return;
    }

    unset($_POST[ COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_nonce' ]);

    if (! current_user_can('edit_posts')) {
        return;
    }

    // -- Get ony the keys that matter.
    $cogFields = getPrefixedValuesFrom($_POST);

    // -- Make sure the image we're saving actually returns a real string.
    if (isset($_POST['complete_open_graph_image'])) {
        $image     = $_POST['complete_open_graph_image'];
        $imagePath = get_attached_file($image);

        if (is_numeric($image) && ! file_exists($imagePath)) {
            $_POST['complete_open_graph_image'] = '';
        }
    }

    // -- Cleanse new post meta.
    // -- @todo: Move this to a method & TEST.
    $newPostMeta = [];
    foreach ($cogFields as $key => $value) {
        $key                 = str_replace(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_', '', $key);
        $value               = esc_attr($value);
        $newPostMeta[ $key ] = $value;
    }

    return update_post_meta($post_id, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX, $newPostMeta);
}
