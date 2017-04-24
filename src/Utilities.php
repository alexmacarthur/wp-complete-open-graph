<?php

namespace CompleteOG;

class Utilities extends App {

  /**
   * Gets serialized settings in options table.
   *
   * @return array
   */
  public static function get_options() {
    return get_option(self::$options_prefix);
  }

  /**
   * Gets specific option value.
   *
   * @param  string $key Option key
   * @return string
   */
  public static function get_option($key) {
    if(isset(self::get_options()[$key])) {
      return self::get_options()[$key];
    }

    // Fallback for previous option naming convention.
    if(isset(self::get_options()['global_' . $key])) {
      return self::get_options()['global_' . $key];
    }

    return false;
  }

  /**
   * Gets serialized options for individual post/page.
   *
   * @return array
   */
  public static function get_post_options() {
    global $post;

    return !empty(get_post_meta($post->ID, self::$options_prefix)) ? get_post_meta($post->ID, self::$options_prefix)[0] : array();
  }

  /**
   * Gets value for specific post/page option.
   *
   * @param  string $key Field key
   * @return string|bool
   */
  public static function get_post_option($key) {
    return !empty(self::get_post_options()[$key]) ? self::get_post_options()[$key] : false;
  }

  /**
   * Gets full name of particular field, with prefix appended.
   *
   * @param  string $name Name of field
   * @return string
   */
  public static function get_field_name($name) {
    return self::$options_prefix . '[' . $name . ']';
  }

  /**
   * Gets the first image that appears on the post/page.
   *
   * @return string|bool
   */
  public static function get_first_image() {
    global $post;
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    return !empty($matches[1][0]) ? $matches[1][0] : false;
  }

}
