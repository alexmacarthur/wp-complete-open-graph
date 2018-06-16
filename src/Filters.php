<?php
/**
* Mainly a set of filters used for one-off cases in which information needs to be modified.
*/

namespace CompleteOpenGraph;

class Filters extends App {

  public function __construct() {
    add_filter(self::$options_prefix . '_twitter:description', array($this, 'append_space_after_period'), 10, 2);
    add_filter(self::$options_prefix . '_og:description', array($this, 'append_space_after_period'), 10, 2);
    add_filter(self::$options_prefix . '_twitter:site', array($this, 'append_at_symbol'), 10, 2);
    add_filter(self::$options_prefix . '_twitter:creator', array($this, 'append_at_symbol'), 10, 2);
    add_filter(self::$options_prefix . '_og:image', array($this, 'process_image'), 10, 2);
    add_filter(self::$options_prefix . '_twitter:image', array($this, 'process_image'), 10, 2);
  }

  /**
   * If image is an attachment ID, construct value based on that. If a URL, use that.
	 *
   * @param  string|integer $value
   * @param  string $field_name
   * @return string
   */
  public function process_image($value, $field_name) {

    $width = '';
		$height = '';

		if(!is_numeric($value)) return $value;

		//-- If this attachment doesn't actually exist or isn't an image, just get out of here.
		if(!wp_attachment_is_image($value)) return '';

		$attachment_meta = wp_get_attachment_metadata($value);

		//-- For some weird reason, some images might not have a size key? It's apparently happened...
		if(empty($attachment_meta) || !isset($attachment_meta['sizes'])) return '';

		if(array_key_exists('complete_open_graph', $attachment_meta['sizes'])) {
			$meta = wp_get_attachment_image_src($value, 'complete_open_graph');
		} elseif(array_key_exists('large', $attachment_meta['sizes'])) {
			$meta = wp_get_attachment_image_src($value, 'large');
		} else {
			$meta = false;
		}

		//-- If, for some reason, no image is returned, just get out of here.
		if(!($meta)) return '';

		$value = $meta[0];
		$width = $meta[1];
		$height = $meta[2];

    //-- Set image sizes.
    add_filter(self::$options_prefix . '_og:image:width', function ($value, $key) use ($width) {
      return $width;
    }, 10, 2);

    add_filter(self::$options_prefix . '_og:image:height', function ($value, $key) use ($height) {
      return $height;
    }, 10, 2);

    return $value;
  }

  /**
   * Append the '@' symbol to the twitter:creator, twitter:site tag values.
   *
   * @param array Open Graph data
   * @return array
   */
  public function append_at_symbol($value, $key) {
    if(!$value) return $value;

    return '@' . str_replace('@', '', $value);
  }

  /**
   * Appends a space after the end of sentence if there isn't already one.
   *
   * @param  string $value
   * @return string
   */
  public static function append_space_after_period($value) {
    if(!$value) return $value;

    $value = preg_replace( '/\.([^, ])/', '. $1', $value);
    $value = preg_replace( '/\?([^, ])/', '? $1', $value);
    return preg_replace( '/\!([^, ])/', '! $1', $value);
  }

}
