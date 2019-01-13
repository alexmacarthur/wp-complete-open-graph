<?php

namespace CompleteOpenGraph;

add_action( 'plugins_loaded', 'CompleteOpenGraph\add_og_image_size' );
add_filter( 'image_size_names_choose', 'CompleteOpenGraph\add_og_image_size_to_uploader' );

/**
 * Add WordPress image size for Open Graph images.
 *
 * @return void
 */
function add_og_image_size() {
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'complete_open_graph', 1200, 1200, false );
}

/**
 * Add our custom image size to the media uploader.
 *
 * @param array $sizes Image sizes
 * @return array
 */
function add_og_image_size_to_uploader( $sizes ) {
	$sizes['complete_open_graph'] = __( 'Open Graph' );
	return $sizes;
}
