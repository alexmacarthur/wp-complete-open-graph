<?php
/**
* Mainly a set of filters used for one-off cases in which information needs to be modified.
*/

namespace CompleteOG;

class Filters extends App {

  public function __construct() {
    add_filter(self::$options_prefix . '_all_data', array($this, 'append_at_symbol'), 10, 2);
    add_filter(self::$options_prefix . '_all_data', array($this, 'add_image_sizes'), 10, 2);
  }

  /**
   * Append the '@' symbol to the twitter:creator, twitter:site tag values.
   *
   * @param array Open Graph data
   * @return array
   */
  public function append_at_symbol($data) {

    if(isset($data['twitter:site'])) {
      if($value = $data['twitter:site']['value']) {
        $data['twitter:site']['value'] = '@' . str_replace('@', '', $value);
      }
    }

    if(isset($data['twitter:creator'])) {
      if($value = $data['twitter:creator']['value']) {
        $data['twitter:creator']['value'] = '@' . str_replace('@', '', $value);
      }
    }

    return $data;
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

    if($image = @getimagesize($data['image']['value'])) {
      $data['image:width'] = array(
        'attribute' => 'property',
        'value' => $image[0]
      );

      $data['image:height'] = array(
        'attribute' => 'property',
        'value' => $image[1]
      );
    }

    return $data;
  }

}
