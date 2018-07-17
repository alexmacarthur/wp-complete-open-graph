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
    add_filter(self::$options_prefix . '_og:image', array($this, 'attach_image_dimensions'), 10, 2);
    add_filter(self::$options_prefix . '_twitter:image', array($this, 'attach_image_dimensions'), 10, 2);
  }

  /**
   * If image is an attachment ID, construct value based on that. If a URL, use that.
	 *
   * @param  string|integer $value
   * @param  string $field_name
   * @return string
   */
  public function attach_image_dimensions($value, $field_name) {

		//-- This is probably a URL from an older version of the plugin. Just return it.
		if(!is_numeric($value)) return $value;

		$imageSizes = array(
			'complete_open_graph',
			'large',
			'medium_large',
			'medium',
			'full'
		);

		$data = false;
		$attachmentMeta = wp_get_attachment_metadata($value);
		$sizes = isset($attachmentMeta['sizes']) ? $attachmentMeta['sizes'] : array();

		//-- The 'full' size isn't included by default.
		$sizes['full'] = true;

		//-- Loop over each image size. Serves as a fallback mechanism if it doesn't exist at the ideal size.
		foreach($imageSizes as $size) {

			//-- We have an image!
			if(array_key_exists($size, $sizes)) {
				$data = wp_get_attachment_image_src($value, $size);
				break;
			}
		}

		//-- If, for some reason, no image is returned, exit. Should NEVER actually happen, but you know... #wordpress.
		if(empty($data)) return '';

		$value = $data[0];
		$width = $data[1];
		$height = $data[2];

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
