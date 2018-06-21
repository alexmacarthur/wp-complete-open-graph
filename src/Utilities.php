<?php

namespace CompleteOpenGraph;

require_once 'PostDecorator.php';

class Utilities extends App {

  public static function get_current_post_type() {
    global $post, $typenow, $current_screen;

    if ( $post && $post->post_type ) {
      return $post->post_type;
    }

    elseif ( $typenow ) {
      return $typenow;
    }

    elseif ( $current_screen && $current_screen->post_type ) {
      return $current_screen->post_type;
    }

    elseif ( isset( $_REQUEST['post_type'] ) ) {
      return sanitize_key( $_REQUEST['post_type'] );
    }

    elseif ( isset( $_REQUEST['post'] ) ) {
      return get_post_type( $_REQUEST['post'] );
    }

    return null;
  }

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
   * Returns instance of PostDecorator, creates one if not defined.
   *
   * @return obj
   */
  public static function get_post_decorator() {
    global $post;

    if(is_null(self::$post_decorator)) {
      self::$post_decorator = new PostDecorator($post);
    }

    return self::$post_decorator;
  }

  /**
   * Gets serialized options for individual post/page.
   *
   * @return array
   */
  public static function get_post_options() {
		$post_options = get_post_meta(self::get_post_decorator()->ID, self::$options_prefix);;

    if(empty($post_options)) {
      return array();
    }

    return $post_options[0];
  }

  /**
   * Gets value for specific post/page option.
   *
   * @param  string $key Field key
   * @return string|bool
   */
  public static function get_post_option($key) {
    $post_options = self::get_post_options();
    return !empty($post_options[$key]) ? $post_options[$key] : false;
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
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', self::get_post_decorator()->post_content, $matches);
    return !empty($matches[1][0]) ? $matches[1][0] : false;
  }

  /**
   * Strips all tags from a string of text.
   *
   * @param  string text
   * @return string
   */
  public static function strip_all_tags($text) {
    if(!$text) return $text;

    $text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $text);
    $text = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $text);

    return strip_tags($text);
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

	/**
	 * Cascades over progression of values and return the first value that isn't empty.
	 *
	 * @param [array] $progression
	 * @return string
	 */
	public static function get_cascaded_value($progression) {
		if(empty($progression)) return '';

		foreach ($progression as $progressionValue) {
			if(!empty($progressionValue)) {
				return Utilities::process_content($progressionValue);
			}
		}

		return '';
	}

}
