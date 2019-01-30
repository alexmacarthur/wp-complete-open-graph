<?php

namespace CompleteOpenGraph;

require_once 'PostDecorator.php';

class Utilities
{
    public static function getFields()
    {
        return include 'fields.php';
    }

    public static function get_current_post_type()
    {
        global $post, $typenow, $current_screen;

        if ($post && $post->post_type) {
            return $post->post_type;
        } elseif ($typenow) {
            return $typenow;
        } elseif ($current_screen && $current_screen->post_type) {
            return $current_screen->post_type;
        } elseif (isset($_REQUEST['post_type'])) {
            return sanitize_key($_REQUEST['post_type']);
        } elseif (isset($_REQUEST['post'])) {
            return get_post_type($_REQUEST['post']);
        }

        return null;
    }

    /**
     * Gets serialized settings in options table.
     *
     * @return array
     */
    public static function get_options()
    {
        return get_option(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX);
    }

    /**
     * Gets specific option value.
     *
     * @param  string $key Option key
     * @return string
     */
    public static function get_option($key)
    {
        if (isset(self::get_options()[ $key ])) {
            return self::get_options()[ $key ];
        }

        return false;
    }

    /**
     * Returns instance of PostDecorator, creates one if not defined.
     *
     * @return obj
     */
    public static function get_post_decorator()
    {
        global $post;
        return new PostDecorator($post);
    }

    /**
     * Gets serialized options for individual post/page.
     *
     * @return array
     */
    public static function get_post_options()
    {
        $post_options = get_post_meta(self::get_post_decorator()->ID, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX);

        if (empty($post_options)) {
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
    public static function get_post_option($key)
    {
        $post_options = self::get_post_options();
        return ! empty($post_options[ $key ]) ? $post_options[ $key ] : false;
    }

    /**
     * Gets full name of particular field, with prefix appended.
     *
     * @param  string $name Name of field
     * @return string
     */
    public static function get_field_name($name)
    {
        return COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '[' . $name . ']';
    }

    /**
     * Gets the first image that appears on the post/page.
     *
     * @return string|bool
     */
    public static function get_first_image()
    {
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', self::get_post_decorator()->post_content, $matches);
        return ! empty($matches[1][0]) ? $matches[1][0] : false;
    }

    /**
     * Strips all tags from a string of text.
     *
     * @param  string text
     * @return string
     */
    public static function strip_all_tags($text)
    {
        if (! $text) {
            return $text;
        }

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
    public static function process_content($content)
    {
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
    public static function get_cascaded_value($progression)
    {
        if (empty($progression)) {
            return '';
        }

        foreach ($progression as $progressionValue) {
            if (! empty($progressionValue)) {
                return self::process_content($progressionValue);
            }
        }

        return '';
    }

    /**
     * Get a specific value for an Open Graph attribute.
     * If progression is given, it will assign the first value that exists.
     *
     * @param  string $field_name  Name of the attribute.
     * @param  array  $progression Array of possible values, in order of priority.
     * @param  array  $protectedKeys Prevents specified keys from being removed duing useGlobal.
     * @return string
     */
    public static function get_processed_value($field_name, $progression = array(), $protectedKeys = array())
    {

        // -- Check for explicit option to use global options, or if it's an archive page.
        $useGlobal =
            (
                self::get_option('force_all') === 'on' ||
                self::get_option($field_name . '_force') === 'on' ||
                (is_home() || is_archive())
            );

        if ($useGlobal) {
            $value = self::process_content(self::get_option($field_name));

            // -- Remove non-protected items before we tack on our global value.
            // -- This way, we can have a fallback system in place in case a global value is empty.
            $progression = self::get_only_protected_values($progression, $protectedKeys);

            array_unshift($progression, $value);

            return apply_filters(
                COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_processed_value',
                self::get_cascaded_value($progression),
                $field_name
            );
        }

        return apply_filters(
            COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_processed_value',
            self::get_cascaded_value($progression),
            $field_name
        );
    }

    /**
     * Ideally, this would be array_filter(), but was causing PHP fallback issues.
     *
     * @param array $progression
     * @param array $protectedKeys
     * @return array
     */
    public static function get_only_protected_values($progression, $protectedKeys)
    {
        $protectedValues = array();

        foreach ($protectedKeys as $key) {
            if (! isset($progression[ $key ])) {
                continue;
            }
            $protectedValues[] = $progression[ $key ];
        }

        return $protectedValues;
    }
}
