<?php

namespace CompleteOpenGraph;

class Generator extends App {

  public function __construct() {
    add_action('wp_head', array($this, 'generate_open_graph_markup'));
		add_filter('language_attributes', array($this, 'add_open_graph_prefix'), 10, 2);
	}

  /**
   * Add Open Graph prefixes to <html> tag.
   *
   * @param string $output A space-separated list of language attributes.
   * @param string $doctype The type of html document (xhtml|html).
   */
  public function add_open_graph_prefix( $output, $doctype ) {
		if(!apply_filters(self::$options_prefix . '_maybe_enable', true)) return $output;

    return $output . ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#"';
	}

  /**
   * Generate the Open Graph markup.
   *
   * @return void
   */
  public function generate_open_graph_markup() {
		if(!apply_filters(self::$options_prefix . '_maybe_enable', true)) return;

    echo "\n\n<!-- Open Graph data is managed by Alex MacArthur's Complete Open Graph plugin. (v" . self::$plugin_data['Version'] . ") -->\n";
    echo "<!-- https://wordpress.org/plugins/complete-open-graph/ -->\n";

		$startTime = microtime(true);

    foreach($this->get_open_graph_values() as $key => $data) {

			if(empty($data['value'])) continue;

			//-- @todo Move this into process_content?
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
   * @param  string $field_name  Name of the attribute.
   * @param  array  $progression Array of possible values, in order of priority.
	 * @param  array 	$protectedKeys Prevents specified keys from being removed duing useGlobal.
   * @return string
   */
  public function get_processed_value($field_name, $progression = array(), $protectedKeys = array()) {

		//-- Check for explicit option to use global options, or if it's an archive page.
		$useGlobal =
			(
				Utilities::get_option('force_all') === 'on' ||
				Utilities::get_option( $field_name . '_force' ) === 'on' ||
				(is_home() || is_archive())
			);

    if($useGlobal) {
			$value = Utilities::process_content(Utilities::get_option($field_name));

			//-- Remove non-protected items before we tack on our global value.
			//-- This way, we can have a fallback system in place in case a global value is empty.
			$progression = $this->get_only_protected_values($progression, $protectedKeys);

			array_unshift($progression, $value);

      return apply_filters(
				self::$options_prefix . '_processed_value',
				Utilities::get_cascaded_value($progression),
				$field_name);
		}

		return apply_filters(
			self::$options_prefix . '_processed_value',
			Utilities::get_cascaded_value($progression),
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
	public function get_only_protected_values($progression, $protectedKeys) {
		$protectedValues = array();

		foreach($protectedKeys as $key) {
			if(!isset($progression[$key])) continue;
			$protectedValues[] = $progression[$key];
		}

		return $protectedValues;
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
				'value' => $this->get_processed_value( 'og:url',
					array(
						get_permalink(Utilities::get_post_decorator()->ID),
						get_bloginfo('url')
					),
					array(1)
				)
      ),

      'og:locale' => array(
        'attribute' => 'property',
        'value' => get_locale()
      ),

      'og:description' => array(
        'attribute' => 'property',
        'value' => $description = $this->get_processed_value( 'og:description',
          array(
            Utilities::get_post_option('og:description'),
            Utilities::get_post_decorator()->post_excerpt,
						Utilities::get_post_decorator()->post_content,
            Utilities::get_option('og:description'),
						get_bloginfo('description')
					),
					array(3, 4)
        )
      ),

      'og:title' => array(
        'attribute' => 'property',
        'value' => $theTitle = $this->get_processed_value( 'og:title',
          array(
            Utilities::get_post_option('og:title'),
            get_the_title(),
            Utilities::get_option('og:title'),
            $site_name
					),
					array(2, 3)
        )
      ),

      'og:type' => array(
        'attribute' => 'property',
        'value' => $this->get_processed_value( 'og:type',
          array(
            Utilities::get_post_option('og:type'),
            is_single() ? 'article' : '',
						Utilities::get_option('og:type'),
						'website'
					),
					array(2, 3)
        )
      ),

      //-- Might be a string, might be an ID. Will be filtered to account for both.
      'og:image' => array(
        'attribute' => 'property',
        'value' => $image = $this->get_processed_value( 'og:image',
          array(
            Utilities::get_post_option('og:image'),
            get_post_thumbnail_id(Utilities::get_post_decorator()->ID),
            Utilities::get_first_image(),
            Utilities::get_option('og:image'),
						!empty($frontPageID) && has_post_thumbnail($frontPageID)
							? get_post_thumbnail_id( $frontPageID )
							: false
					),
					array(3, 4)
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
        'value' => $this->get_processed_value('twitter:card',
          array(
            Utilities::get_post_option('twitter:card'),
						Utilities::get_option('twitter:card'),
						'summary'
					),
					array(2)
        )
      ),

      'twitter:creator' => array(
        'attribute' => 'name',
        'value' => $this->get_processed_value('twitter:creator',
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
        'value' => $this->get_processed_value( 'twitter:description',
          array(
            Utilities::get_post_option('twitter:description'),
            Utilities::get_post_decorator()->post_excerpt,
            Utilities::get_post_decorator()->post_content,
            Utilities::get_option('twitter:description'),
            $description
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
