<?php

namespace CompleteOpenGraph;

class OpenGraph extends App {

	/**
	 * Cache whether the 'force all' value has been checked.
	 *
	 * @var boolean
	 */
	protected $forceAll = false;

	/**
	 * Cache whether the current page lists multiple posts,
	 * like an archive page.
	 *
	 * @var boolean
	 */
	protected $isPostListingPage = false;

  /**
   * Add actions.
   */
  public function __construct() {
		add_action('wp', array($this, 'set_useful_variables'));
    add_action('plugins_loaded', array($this, 'add_og_image_size'));
    add_action('wp_head', array($this, 'open_graph_tag_generation'));
    add_filter('image_size_names_choose', array($this, 'add_og_image_size_to_uploader'));
		add_filter('language_attributes', array($this, 'add_open_graph_prefix'), 10, 2 );
	}

	public function set_useful_variables() {
		$this->forceAll = Utilities::get_option('force_all');
		$this->isPostListingPage = is_home() || is_archive();
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

    echo "\n<!-- This Open Graph data is managed by Alex MacArthur's Complete Open Graph plugin. (v" . self::$plugin_data['Version'] . "). -->\n";
    echo "<!-- https://wordpress.org/plugins/complete-open-graph/ -->\n";

		$startTime = microtime(true);

    foreach($this->get_open_graph_values() as $key => $data) {

			if(empty($data['value'])) continue;

      $content = preg_replace( "/\r|\n/", "", $data['value']);
			$content = htmlentities($content, ENT_QUOTES, 'UTF-8', false);

			if($data['attribute'] === 'property') {
				?><meta property="<?php echo $key; ?>" content="<?php echo $content; ?>" /><?php
				echo "\n";
				continue;
			}

			if($data['attribute'] === 'name') {
				?><meta name="<?php echo $key; ?>" content="<?php echo $content; ?>" /><?php
				echo "\n";
				continue;
			}
    }

    echo "<!-- End Complete Open Graph. | " . (microtime(true) - $startTime) . "s -->\n\n";
  }

  /**
   * Get a specific value for an Open Graph attribute.
   * If progression is given, it will assign the first value that exists.
   *
   * @param  string $field_name  Name of the attribute
   * @param  array  $progression Array of possible values, in order of priority.
   * @return string The value
   */
  public function get_open_graph_processed_value($field_name, $progression = array()) {

		$option = Utilities::get_option( $field_name );

		//-- Check for explicit option to use global options, or if it's an archive page.
		$useGlobal = $this->forceAll === 'on'
			|| $this->isPostListingPage
			|| Utilities::get_option( $field_name . '_force' ) === 'on';

    if( $useGlobal ) {
			$value = Utilities::process_content(Utilities::get_option($field_name));
      return apply_filters(self::$options_prefix . '_processed_value', $value, $field_name);
    }

    if(!empty($progression)) {
      foreach ($progression as $progressionValue) {
        if(!empty($progressionValue)) {
          $value = Utilities::process_content($progressionValue);
          return apply_filters(self::$options_prefix . '_processed_value', $value, $field_name);
        }
      }
    }

    return '';
  }

  /**
   * Get the values for each OG attribute, based on priority & existence of values.
   *
   * @return array Open Graph values
   */
  public function get_open_graph_values() {
    $frontPageID = (int) get_option('page_on_front');

    $data = array(
      'og:site_name' => array(
        'attribute' => 'property',
        'value' => $site_name = get_bloginfo('name')
      ),

      'og:url' => array(
        'attribute' => 'property',
        'value' => $url = get_permalink(Utilities::get_post_decorator()->ID)
      ),

      'og:locale' => array(
        'attribute' => 'property',
        'value' => get_locale()
      ),

      'og:description' => array(
        'attribute' => 'property',
        'value' => $this->get_open_graph_processed_value( 'og:description',
          array(
            Utilities::get_post_option('og:description'),
            Utilities::get_post_decorator()->post_excerpt,
            Utilities::get_post_decorator()->post_content,
            Utilities::get_option('og:description'),
            get_bloginfo('og:description')
          )
        )
      ),

      'og:title' => array(
        'attribute' => 'property',
        'value' => $theTitle = $this->get_open_graph_processed_value( 'og:title',
          array(
            Utilities::get_post_option('og:title'),
            get_the_title(),
            Utilities::get_option('og:title'),
            $site_name
          )
        )
      ),

      'og:type' => array(
        'attribute' => 'property',
        'value' => $this->get_open_graph_processed_value( 'og:type',
          array(
            Utilities::get_post_option('og:type'),
            is_single() ? 'article' : 'website',
            Utilities::get_option('og:type')
          )
        )
      ),

      //-- Might be a string, might be an ID. Will be filtered to account for both.
      'og:image' => array(
        'attribute' => 'property',
        'value' => $image = $this->get_open_graph_processed_value( 'og:image',
          array(
            Utilities::get_post_option('og:image'),
            get_post_thumbnail_id(Utilities::get_post_decorator()->ID),
            Utilities::get_first_image(),
            Utilities::get_option('og:image'),
            !empty($frontPageID) && has_post_thumbnail($frontPageID) ?
            get_post_thumbnail_id( $frontPageID ) :
            false
          )
        )
      ),

      'og:image:width' => array(
        'attribute' => 'property',
        'value' => ''
      ),

      'og:image:height' => array(
        'attribute' => 'property',
        'value' => ''
      ),

      'fb:admins' => array(
        'attribute' => 'property',
        'value' => Utilities::get_option('fb:admins')
      ),

      'fb:app_id' => array(
        'attribute' => 'property',
        'value' => Utilities::get_option('fb:app_id')
      ),

      'twitter:card' => array(
        'attribute' => 'name',
        'value' => $this->get_open_graph_processed_value('twitter:card',
          array(
            Utilities::get_post_option('twitter:card'),
            Utilities::get_option('twitter:card')
          )
        )
      ),

      'twitter:creator' => array(
        'attribute' => 'name',
        'value' => $this->get_open_graph_processed_value('twitter:creator',
          array(
            Utilities::get_post_option('twitter:creator'),
            Utilities::get_option('twitter:creator')
          )
        )
      ),

      'twitter:site' => array(
        'attribute' => 'name',
        'value' => Utilities::get_option('twitter:site')
      ),

      'twitter:title' => array(
        'attribute' => 'name',
        'value' => $theTitle
      ),

      'twitter:image' => array(
        'attribute' => 'name',
        'value' => $image
      ),

      'twitter:description' => array(
        'attribute' => 'name',
        'value' => $this->get_open_graph_processed_value( 'twitter:description',
          array(
            Utilities::get_post_option('twitter:description'),
            Utilities::get_post_decorator()->post_excerpt,
            Utilities::get_post_decorator()->post_content,
            Utilities::get_option('twitter:description'),
            $this->get_open_graph_processed_value( 'og:description',
              array(
                Utilities::get_post_decorator()->post_content,
                get_bloginfo('og:description')
              )
            )
          )
        )
      )
    );

    //-- Filter for filtering specific fields.
    foreach($data as $key=>$item) {
      $data[$key]['value'] = apply_filters(self::$options_prefix . '_' . $key, $data[$key]['value'], $key);
    }

    return apply_filters(self::$options_prefix . '_all_data', $data);
  }
}
