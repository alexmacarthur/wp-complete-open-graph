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

  /**
   * Strips all tags from a string of text.
   *
   * @param  string text
   * @return string
   */
  public static function strip_all_tags($text) {
    $dom = new \DOMDocument('1.0','UTF-8');
    $dom->loadHTML($text);

    for ( $list = $dom->getElementsByTagName('script'), $i = $list->length; --$i >=0; ) {
      $node = $list->item($i);
      $node->parentNode->removeChild($node);
    }

    for ( $list = $dom->getElementsByTagName('style'), $i = $list->length; --$i >=0; ) {
      $node = $list->item($i);
      $node->parentNode->removeChild($node);
    }

    return strip_tags($dom->saveHTML());
  }

  /**
   * Do all the things for formatting a piece of OG content.
   *
   * @param  string $content
   * @return string
   */
  public static function process_content($content) {
    $value = strip_shortcodes($content);
    $value = self::strip_all_tags($value);
    $value = trim($value);
    $value = substr($value, 0, 300);
    return $value;
  }

}
