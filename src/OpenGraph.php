<?php

namespace CompleteOG;
use CompleteOG\Utilities;

class OpenGraph extends App{

  /**
   * Add actions.
   */
  public function __construct() {
    add_action('plugins_loaded', array($this, 'add_og_image_size'));
    add_action('wp_head', array($this, 'open_graph_tag_generation'));
    add_filter('image_size_names_choose', array($this, 'add_og_image_size_to_uploader'));
    add_filter('language_attributes', array($this, 'add_open_graph_prefix'), 10, 2 );
  }

  /**
   * Add Open Graph prefixes to <html> tag.
   *
   * @param string $output A space-separated list of language attributes.
   * @param string $doctype The type of html document (xhtml|html).
   */
  public function add_open_graph_prefix( $output, $doctype ) {
    return $output . ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#"';
  }

  /**
   * Add WordPress image size for Open Graph images.
   *
   * @return void
   */
  public function add_og_image_size() {
    add_theme_support('post-thumbnails');
    add_image_size('complete_open_graph', 1200, 1200, false);
  }

  /**
   * Add our custom image size to the media uploader.
   *
   * @param arr $sizes Image sizes
   * @return arr
   */
  public function add_og_image_size_to_uploader($sizes) {
    $sizes['complete_open_graph'] = __( 'Open Graph');
    return $sizes;
  }

  /**
   * Generate the Open Graph markup.
   *
   * @return void
   */
  public function open_graph_tag_generation() {
    global $post;

    if(empty($post)) return;

    echo "\n<!-- Open Graph managed (and managed freaking well) by Alex MacArthur's Complete Open Graph plugin. (v" . $this->version . "). -->\n";
    echo "<!-- https://wordpress.org/plugins/complete-open-graph/ -->\n";

    $startTime = microtime(true);

    foreach($this->get_open_graph_values() as $key=>$data) {
      $content = preg_replace( "/\r|\n/", "", htmlentities($data['value']));

      if($data['value']) {
        if($data['attribute'] === 'property') {
          ?><meta property="<?php echo $key; ?>" content="<?php echo $content; ?>" /><?php
          echo "\n";
        }

        if($data['attribute'] === 'name') {
          ?><meta name="<?php echo $key; ?>" content="<?php echo $content; ?>" /><?php
          echo "\n";
        }
      }
    }

    echo "<!-- End Complete Open Graph. | " . (microtime(true) - $startTime) . "s -->\n\n";
  }

  /**
   * Get a specific value for an Open Graph attribute.
   * If progression is given, it will assign the first value that exists.
   *
   * @todo Deprecate the single_value filter. Much better ways to implement this.
   *
   * @param  string $field_name  Name of the attribute
   * @param  array  $progression Array of possible values, in order of priority.
   * @return string The value
   */
  public function get_open_graph_value($field_name, $progression = array()) {

    if(
      !empty(Utilities::get_option( $field_name . '_force' )) &&
      !empty(Utilities::get_option( $field_name )) &&
      Utilities::get_option( $field_name . '_force' ) === 'on'
    ) {
      $value = substr(trim(strip_tags(strip_shortcodes(Utilities::get_option($field_name)))), 0, 300);
      return apply_filters(self::$options_prefix . '_single_value', $value, $field_name);
    }

    if(!empty($progression)) {
      foreach ($progression as $default) {
        if(!empty($default)) {
          $value = substr(trim(strip_shortcodes(strip_tags($default))), 0, 300);
          return apply_filters(self::$options_prefix . '_single_value', $value, $field_name);
        }
      }
    }

    return apply_filters(self::$options_prefix . '_single_value', '', $field_name);
  }

  /**
   * Get the values for each OG attribute, based on priority & existence of values.
   *
   * @return array Open Graph values
   */
  public function get_open_graph_values() {
    global $post;

    $site_name = get_bloginfo('name');
    $url = get_permalink($post->ID);

    $data = array(
      'og:site_name' => array(
        'attribute' => 'property',
        'value' => $site_name
      ),

      'og:url' => array(
        'attribute' => 'property',
        'value' => $url
      ),

      'og:locale' => array(
        'attribute' => 'property',
        'value' => 'en_us'
      ),

      'og:description' => array(
        'attribute' => 'property',
        'value' => $this->get_open_graph_value( 'description',
          array(
            Utilities::get_post_option('description'),
            $post->post_content,
            Utilities::get_option('description'),
            get_bloginfo('description')
          )
        )
      ),

      'og:title' => array(
        'attribute' => 'property',
        'value' => $this->get_open_graph_value( 'title',
          array(
            Utilities::get_post_option('title'),
            get_the_title(),
            Utilities::get_option('title'),
            $site_name
          )
        )
      ),

      'og:type' => array(
        'attribute' => 'property',
        'value' => $this->get_open_graph_value( 'type',
          array(
            Utilities::get_post_option('type'),
            is_single() ? 'article' : 'website',
            Utilities::get_option('type')
          )
        )
      ),

      'og:image' => array(
        'attribute' => 'property',
        'value' => $this->get_open_graph_value( 'image',
          array(
            Utilities::get_post_option('image'),
            wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'complete_open_graph')[0],
            Utilities::get_first_image(),
            Utilities::get_option('image'),
            !empty(get_option('page_on_front')) && has_post_thumbnail(get_option('page_on_front')) ?
            wp_get_attachment_image_src( get_post_thumbnail_id( get_option('page_on_front' )), 'complete_open_graph')[0] :
            false
          )
        )
      ),

      'fb:admins' => array(
        'attribute' => 'property',
        'value' => !empty(Utilities::get_option('facebook_admin_ids')) ? Utilities::get_option('facebook_admin_ids') : false
      ),

      'fb:app_id' => array(
        'attribute' => 'property',
        'value' => !empty(Utilities::get_option('facebook_app_id')) ? Utilities::get_option('facebook_app_id') : false
      ),

      //-- TWITTER

      'twitter:card' => array(
        'attribute' => 'name',
        'value' => 'summary'
      ),

      'twitter:creator' => array(
        'attribute' => 'name',
        'value' => $this->get_open_graph_value('twitter_creator',
          array(
            Utilities::get_post_option('twitter_creator'),
            Utilities::get_option('twitter_creator')
          )
        )
      ),

      'twitter:site' => array(
        'attribute' => 'name',
        'value' => Utilities::get_option('twitter_site')
      ),

      'twitter:title' => array(
        'attribute' => 'name',
        'value' => $this->get_open_graph_value( 'title',
          array(
            Utilities::get_post_option('title'),
            get_the_title(),
            Utilities::get_option('title'),
            $site_name
          )
        )
      ),

      'twitter:image' => array(
        'attribute' => 'name',
        'value' => $this->get_open_graph_value( 'image',
          array(
            Utilities::get_post_option('image'),

            wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'complete_open_graph')[0],

            Utilities::get_first_image(),

            Utilities::get_option('image'),

            !empty(get_option('page_on_front')) && has_post_thumbnail(get_option('page_on_front')) ?
            wp_get_attachment_image_src( get_post_thumbnail_id( get_option('page_on_front' )), 'complete_open_graph')[0] :
            false
          )
        )
      ),

      'twitter:description' => array(
        'attribute' => 'name',
        'value' => $this->get_open_graph_value( 'twitter_description',
          array(
            Utilities::get_post_option('twitter_description'),
            $post->post_content,
            Utilities::get_option('twitter_description'),
            $this->get_open_graph_value( 'description',
              array(
                $post->post_content, get_bloginfo('description')
              )
            )
          )
        )
      )
    );

    //-- Loop over values to check if 'force' is in effect.
    foreach($data as $key=>$item) {
      if(Utilities::get_option($key . '_force') === 'on') {
        $data[$key]['value'] = Utilities::get_option($key);
      }
    }

    return apply_filters(self::$options_prefix . '_all_data', $data);
  }
}
