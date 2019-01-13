<?php
/**
 * Contains miscellaneous filters used by the plugin.
 *
 * @package CompleteOpenGraph
 */

namespace CompleteOpenGraph;

add_filter( COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:description', 'CompleteOpenGraph\append_space_after_period', 10, 2 );
add_filter( COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:description', 'CompleteOpenGraph\append_space_after_period', 10, 2 );
add_filter( COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:site', 'CompleteOpenGraph\append_at_symbol', 10, 2 );
add_filter( COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:creator', 'CompleteOpenGraph\append_at_symbol', 10, 2 );
add_filter( COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:image', 'CompleteOpenGraph\attach_image_dimensions', 10, 2 );
add_filter( COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:image', 'CompleteOpenGraph\attach_image_dimensions', 10, 2 );

/**
 * If image is an attachment ID, construct value based on that. If a URL, use that.
 *
 * @param  string|integer $value Value of the field.
 * @param  string         $field_name Name of the field.
 * @return string
 */
function attach_image_dimensions( $value, $field_name ) {

	// -- This is probably a URL from an older version of the plugin. Just return it.
	if ( ! is_numeric( $value ) ) {
		return $value;
	}

	$image_sizes = array(
		'complete_open_graph',
		'large',
		'medium_large',
		'medium',
		'full',
	);

	$data            = false;
	$attachment_meta = wp_get_attachment_metadata( $value );
	$sizes           = isset( $attachment_meta['sizes'] ) ? $attachment_meta['sizes'] : array();

	// -- The 'full' size isn't included by default.
	$sizes['full'] = true;

	// -- Loop over each image size. Serves as a fallback mechanism if it doesn't exist at the ideal size.
	foreach ( $image_sizes as $size ) {

		// -- We have an image!
		if ( array_key_exists( $size, $sizes ) ) {
			$data = wp_get_attachment_image_src( $value, $size );
			break;
		}
	}

	// -- If, for some reason, no image is returned, exit. Should NEVER actually happen, but you know... #WordPress.
	if ( empty( $data ) ) {
		return '';
	}

	$value  = $data[0];
	$width  = $data[1];
	$height = $data[2];

	// -- Set image sizes.
	add_filter(
		COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:image:width',
		function ( $value, $key ) use ( $width ) {
			return $width;
		},
		10,
		2
	);

	add_filter(
		COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:image:height',
		function ( $value, $key ) use ( $height ) {
			return $height;
		},
		10,
		2
	);

	return $value;
}

/**
 * Append the '@' symbol to the twitter:creator, twitter:site tag values.
 *
 * @param string|integer $value Value of the field.
 * @param string         $field_name Name of the field.
 * @return  array
 */
function append_at_symbol( $value, $field_name = '' ) {
	return preg_replace( '/^[^@]/', '@$0', trim( $value ) );
}

/**
 * Appends a space after the end of sentence if there isn't already one.
 *
 * @param string|integer $value Value of the field.
 * @param string         $field_name Name of the field.
 * @return string
 */
function append_space_after_period( $value, $field_name = '' ) {
	return preg_replace( '/(\.|\?|\!)$/', '$1 ', $value );
}

