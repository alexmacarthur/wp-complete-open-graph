<?php
/**
 * Contains miscellaneous filters used by the plugin.
 *
 * @package CompleteOpenGraph
 */

namespace CompleteOpenGraph;

add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:description', 'CompleteOpenGraph\append_space_after_period', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:description', 'CompleteOpenGraph\append_space_after_period', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:site', 'CompleteOpenGraph\append_at_symbol', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:creator', 'CompleteOpenGraph\append_at_symbol', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:image', 'CompleteOpenGraph\get_image_url_from_attachment_id', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:image', 'CompleteOpenGraph\get_image_url_from_attachment_id', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:image', 'CompleteOpenGraph\maybe_use_author_avatar', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:image', 'CompleteOpenGraph\maybe_use_author_avatar', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:image', 'CompleteOpenGraph\ensure_full_url', 10, 2);
add_filter(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter:image', 'CompleteOpenGraph\ensure_full_url', 10, 2);

/**
 * If, for some weird reason, we have an image URL that starts with a slash,
 * append the site URL so a full URL is actually generated.
 *
 * @param string $value
 * @param string $field_name
 * @return void
 */
function ensure_full_url($value, $field_name = '')
{
    if (substr($value, 0, 1) === '/') {
        return untrailingslashit(get_site_url()) . $value;
    }

    return $value;
}

/**
 * If we're on an author archive page and the user has an avatar,
 * set that as the OG image.
 *
 * @param string $value
 * @param string $field_name
 * @return void
 */
function maybe_use_author_avatar($value, $field_name = '')
{
    if (!is_author()) {
        return $value;
    }

    $userID = get_the_author_meta('ID');

    if (empty(get_avatar($userID))) {
        return $value;
    }

    return get_avatar_url($userID, ['size' => 1200]);
}

/**
 * If image is an attachment ID, construct value based on that. If a URL, use that.
 *
 * @param  string|integer $value Value of the field.
 * @param  string         $field_name Name of the field.
 * @return string
 */
function get_image_url_from_attachment_id($value, $field_name = '')
{
    // -- This is a URL. Just leave it be.
    // -- @todo: Require that it is an absolute URL.
    if (!is_numeric($value)) {
        return $value;
    }

    $data = wp_get_attachment_image_src($value, 'complete_open_graph');

    // -- If, for some reason, no image is returned, exit. Should NEVER actually happen, but you know... #WordPress.
    if (empty($data)) {
        return '';
    }

    $value  = $data[0];
    $width  = $data[1];
    $height = $data[2];

    // -- Set image sizes.
    add_filter(
        COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:image:width',
        function ($value, $key) use ($width) {
            return $width;
        },
        10,
        2
    );

    add_filter(
        COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_og:image:height',
        function ($value, $key) use ($height) {
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
function append_at_symbol($value, $field_name = '')
{
    return preg_replace('/^[^@]/', '@$0', trim($value));
}

/**
 * Appends a space after the end of sentence if there isn't already one.
 *
 * @param string|integer $value Value of the field.
 * @param string         $field_name Name of the field.
 * @return string
 */
function append_space_after_period($value, $field_name = '')
{
    return preg_replace('/(\.|\?|\!)$/', '$1 ', $value);
}
