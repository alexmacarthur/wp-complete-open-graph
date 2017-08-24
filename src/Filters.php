<?php
/**
* Mainly a set of filters used for one-off cases in which information needs to be modified.
*/

namespace CompleteOG;

class Filters extends App {

  public function __construct() {
    add_filter(self::$options_prefix . '_twitter:description', array($this, 'append_space_after_period'), 10, 2);
    add_filter(self::$options_prefix . '_og:description', array($this, 'append_space_after_period'), 10, 2);
    add_filter(self::$options_prefix . '_twitter:site', array($this, 'append_at_symbol'), 10, 2);
    add_filter(self::$options_prefix . '_twitter:creator', array($this, 'append_at_symbol'), 10, 2);
    add_filter(self::$options_prefix . '_processed_value', array($this, 'process_image'), 10, 2);
  }

  /**
   * If image is an attachment ID, construct value based on that. If a URL, use that.
   * @param  string|integer
   * @param  string $key
   * @return string
   */
  public function process_image($value, $field_name) {

    if($field_name !== 'og:image') return $value;

    $width = '';
    $height = '';

    //-- Get image data, including dimensions.
    if(is_numeric($value)) {

      //-- If this attachment doesn't actually exist or isn't an image, just get out of here.
      if(!wp_attachment_is_image($value)) return;

      $meta = array_key_exists('complete_open_graph', wp_get_attachment_metadata($value)['sizes']) ?
              wp_get_attachment_image_src($value, 'complete_open_graph') :
              wp_get_attachment_image_src($value, 'large');

      //-- If, for some reason, no image is returned, just get out of here.
      if(is_null($meta)) return;

      $value = $meta[0];
      $width = $meta[1];
      $height = $meta[2];
    } elseif ($imageData = @getimagesize($value)) {
      $width = $imageData[0];
      $height = $imageData[1];
    }

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
   * Add the image size attributes if an image exists.
   *
   * @todo Build to not rely on getimagesize() for improved efficiency.
   *
   * @param array Open Graph data
   * @return array
   */
  public function add_image_sizes($data) {

    if($image = @getimagesize($data['og:image']['value'])) {

      $data['og:image:width'] = array(
        'attribute' => 'property',
        'value' => $image[0]
      );

      $data['og:image:height'] = array(
        'attribute' => 'property',
        'value' => $image[1]
      );
    }

    return $data;
  }

  /**
   * Appends a space after the end of sentence if there isn't already one.
   *
   * @param  string $value
   * @return string
   */
  public static function append_space_after_period($value) {
    if(!$value) return $value;

    return preg_replace( '/\.([^, ])/', '. $1', $value);
  }

}
