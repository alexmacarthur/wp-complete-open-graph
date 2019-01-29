<?php

namespace CompleteOpenGraph;

add_action('plugins_loaded', 'CompleteOpenGraph\add_og_image_size');
add_filter('image_size_names_choose', 'CompleteOpenGraph\add_og_image_size_to_uploader');

/**
 * Add WordPress image size for Open Graph images.
 *
 * @return void
 */
function add_og_image_size()
{
    add_theme_support('post-thumbnails');
    add_image_size('complete_open_graph', COMPLETE_OPEN_GRAPH_IMAGE_WIDTH, COMPLETE_OPEN_GRAPH_IMAGE_HEIGHT, false);
}

/**
 * Add our custom image size to the media uploader.
 *
 * @param array $sizes Image sizes
 * @return array
 */
function add_og_image_size_to_uploader($sizes)
{
    $sizes['complete_open_graph'] = __('Open Graph');
    return $sizes;
}
