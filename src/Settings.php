<?php

namespace CompleteOG;
use CompleteOG\Utilities;

class Settings extends App {

  public $options;
  public $github_url = 'https://github.com/alexmacarthur/complete-open-graph';
  public $wordpress_url = 'https://wordpress.org/support/plugin/complete-open-graph/reviews/';
  public $twitter_url = 'https://twitter.com/home?status=I%20highly%20recommend%20the%20Complete%20Open%20Graph%20%23WordPress%20plugin%20from%20%40amacarthur!%20https%3A//wordpress.org/plugins/complete-open-graph/';
  public $website_url = 'http://macarthur.me/#contact';

  /**
   * Add actions, set up stuffs.
   */
  public function __construct() {
    $this->options = Utilities::get_options();

    add_action( 'admin_init', array($this, 'register_main_setting'));
    add_action( 'admin_init', array($this, 'register_settings'));
    add_action( 'admin_init', array($this, 'register_facebook_settings'));
    add_action( 'admin_init', array($this, 'register_twitter_settings'));
    add_action(	'admin_menu', array($this, 'open_graph_settings_page'));
  }

  /**
   * Add submenu page for settings.
   *
   * @return void
   */
  public function open_graph_settings_page() {
    add_submenu_page('options-general.php', 'Open Graph Settings', 'Open Graph', 'edit_posts', 'complete-open-graph', array($this, 'open_graph_settings_page_cb'));
  }

  /**
   * Generate markup for settings page.
   *
   * @return void
   */
  public function open_graph_settings_page_cb() {
  ?>
    <div id="cogSettingsPage" class="wrap <?php if(!Utilities::get_option('image')): ?>has-no-image<?php endif; ?>">
      <h1>Complete Open Graph Settings</h1>

      <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
          <div id="post-body-content" class="postbox-container">
            <form method="post" action="options.php">
              <?php wp_nonce_field('update-options'); ?>
              <?php
                settings_fields( 'complete_open_graph_settings' );
                do_settings_sections( self::$admin_settings_page_slug );
                submit_button();
              ?>
            </form>
          </div>

          <div id="postbox-container-1" class="postbox-container">

            <h2>Has COG Made Your Life Better?</h2>

            <ul class="SK_FeedbackList">
              <li class="SK_FeedbackList-item SK_FeedbackItem">
                <a class="SK_FeedbackItem-link SK_FeedbackItem-link--github" href="<?php echo $this->github_url; ?>" target="_blank"><?php echo file_get_contents(plugin_dir_url( __FILE__ ) . 'assets/img/github.svg'); ?></a>
                <span class="SK_FeedbackItem-text"><p><a href="<?php echo $this->github_url; ?>">Star</a> it.</p></span>
              </li>
              <li class="SK_FeedbackList-item SK_FeedbackItem">
                <a class="SK_FeedbackItem-link" href="<?php echo $this->wordpress_url; ?>" target="_blank"><?php echo file_get_contents(plugin_dir_url( __FILE__ ) . 'assets/img/wordpress.svg'); ?></a>
                <span class="SK_FeedbackItem-text"><p><a href="<?php echo $this->wordpress_url; ?>">Review</a> it.</p></span>
              </li>
              <li class="SK_FeedbackList-item SK_FeedbackItem">
                <a class="SK_FeedbackItem-link" href="<?php echo $this->twitter_url; ?>" target="_blank"><?php echo file_get_contents(plugin_dir_url( __FILE__ ) . 'assets/img/twitter.svg'); ?></a>
                <span class="SK_FeedbackItem-text"><p><a href="<?php echo $this->twitter_url; ?>">Tweet</a> about it.</p></span>
              </li>
              <li class="SK_FeedbackList-item SK_FeedbackItem">
                <a class="SK_FeedbackItem-link SK_FeedbackItem-link--mail" href="<?php echo $this->website_url; ?>" target="_blank"><?php echo file_get_contents(plugin_dir_url( __FILE__ ) . 'assets/img/envelope.svg'); ?></a>
                <span class="SK_FeedbackItem-text"><p><a href="<?php echo $this->website_url; ?>">Email</a> me.</p></span>
              </li>
            </ul>

          </div>
        </div>
      </div>
    </div>

  <?php
  }

  /**
   * Register the main setting for storing settings.
   *
   * @return void
   */
  public function register_main_setting() {
    register_setting( 'complete_open_graph_settings', self::$options_prefix);
  }

  /**
   * Register settings specific to Facebook.
   *
   * @return void
   */
  public function register_facebook_settings() {
    add_settings_section( self::$options_prefix . '_facebook_parameters', 'Facebook Parameters', array( $this, 'cb_facebook_parameters_section' ), self::$admin_settings_page_slug );

    add_settings_field( self::$options_short_prefix . '_facebook_admin_ids', 'Facebook Admin IDs', array( $this, 'cb_field_facebook_admin_ids' ), self::$admin_settings_page_slug, self::$options_prefix . '_facebook_parameters' );

    add_settings_field( self::$options_short_prefix . '_facebook_app_id', 'Facebook App ID', array( $this, 'cb_field_facebook_app_id' ), self::$admin_settings_page_slug, self::$options_prefix . '_facebook_parameters' );
  }


  /**
   * Register settings specific to Twitter.
   *
   * @return void
   */
  public function register_twitter_settings() {
    add_settings_section( self::$options_prefix . '_twitter_parameters', 'Twitter Parameters', array( $this, 'cb_twitter_parameters_section' ), self::$admin_settings_page_slug );

    add_settings_field( self::$options_short_prefix . '_twitter_description', 'Default Twitter Description', array( $this, 'cb_field_twitter_description' ), self::$admin_settings_page_slug, self::$options_prefix . '_twitter_parameters' );

    add_settings_field( self::$options_short_prefix . '_twitter_creator', 'Default Twitter Creator', array( $this, 'cb_field_twitter_creator' ), self::$admin_settings_page_slug, self::$options_prefix . '_twitter_parameters' );

    add_settings_field( self::$options_short_prefix . '_twitter_site', 'Default Twitter Site', array( $this, 'cb_field_twitter_site' ), self::$admin_settings_page_slug, self::$options_prefix . '_twitter_parameters' );
  }

  /**
   * Register Default fallback and force settings.
   *
   * @return void
   */
  public function register_settings () {

    add_settings_section( self::$options_prefix . '_fallbacks', 'Default Fallbacks', array( $this, 'cb_fallbacks_section' ), self::$admin_settings_page_slug );

    add_settings_field( self::$options_short_prefix . '_type', 'Default Type', array( $this, 'cb_field_type' ), self::$admin_settings_page_slug, self::$options_prefix . '_fallbacks' );

    add_settings_field( self::$options_short_prefix . '_title', 'Default Title', array( $this, 'cb_field_title' ), self::$admin_settings_page_slug, self::$options_prefix . '_fallbacks' );

    add_settings_field( self::$options_short_prefix . '_description', 'Default Description', array( $this, 'cb_field_description' ), self::$admin_settings_page_slug, self::$options_prefix . '_fallbacks' );

    add_settings_field( self::$options_short_prefix . '_image', 'Default Image', array( $this, 'cb_field_image' ), self::$admin_settings_page_slug, self::$options_prefix . '_fallbacks' );
  }

  /**
   * Returns whether a certain input is checked.
   *
   * @param  string $key The field key
   * @return string
   */
  public function checked($key) {
    return Utilities::get_option($key) === 'on' ? 'checked' : '';
  }

  /**
   * Outputs some basic instructions.
   *
   * @return void
   */
  public function cb_fallbacks_section() {
    echo '<p>These settings will serve as fallbacks in case individual pages/posts do not have information supplied. If you wish to force these values to always override individual posts/pages, check the box under each option.</p>';
  }

  /**
   * Outputs some more basic instructions.
   *
   * @return void
   */
  public function cb_facebook_parameters_section() {
    echo '<p>Optionally modify Default / fallback settings for Facebook-related tags, including those that tie your site to a partiular Facebook page or account.</p>';
  }

  /**
   * Outputs some more basic instructions.
   *
   * @return void
   */
  public function cb_twitter_parameters_section() {
    echo '<p>Optionally modify global/fallback settings for Twitter-related Open Graph tags.</p>';
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_type() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>If left blank, 'website' will be used.</p>
      <input type="text" value="<?php echo Utilities::get_option('type'); ?>" name="<?php echo Utilities::get_field_name('type'); ?>" id="ogType" />

      <div class="SK_Box-checkboxGroup">
        <input type="checkbox" <?php echo $this->checked('type_force'); ?> name="<?php echo Utilities::get_field_name('type_force'); ?>" id="ogTypeForce">
        <label for="ogTypeForce">Force Default Type</label>
        <span class="SK_Box-disclaimer">Checking this will force this value, no matter what.</span>
      </div>

    </fieldset>
    <?php
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_title() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>If left blank, the site title will be used.</p>
      <input type="text" value="<?php echo Utilities::get_option('title'); ?>" name="<?php echo Utilities::get_field_name('title'); ?>" id="ogDescription" />

      <div class="SK_Box-checkboxGroup">
        <input type="checkbox" <?php echo $this->checked('title_force'); ?> name="<?php echo Utilities::get_field_name('title_force'); ?>" id="ogTitleForce">
        <label for="ogTitleForce">Force Default Title</label>
        <span class="SK_Box-disclaimer">Checking this will force this value, no matter what.</span>
      </div>
    </fieldset>
    <?php
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_description() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>If left blank, the site description will be used.</p>
      <input type="text" value="<?php echo Utilities::get_option('description'); ?>" name="<?php echo Utilities::get_field_name('description'); ?>" id="ogDescription" />

      <div class="SK_Box-checkboxGroup">
        <input type="checkbox" <?php echo $this->checked('description_force'); ?> name="<?php echo Utilities::get_field_name('description_force'); ?>" id="ogDescriptionForce">
        <label for="ogDescriptionForce">Force Default Description</label>
        <span class="SK_Box-disclaimer">Checking this will force this value, no matter what.</span>
      </div>
    </fieldset>
    <?php
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_twitter_description() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>If left blank, the description will be used.</p>
      <input type="text" value="<?php echo Utilities::get_option('twitter_description'); ?>" name="<?php echo Utilities::get_field_name('twitter_description'); ?>" id="ogTwitterDescription" />

      <div class="SK_Box-checkboxGroup">
        <input type="checkbox" <?php echo $this->checked('twitter_description_force'); ?> name="<?php echo Utilities::get_field_name('twitter_description_force'); ?>" id="ogTwitterDescriptionForce">
        <label for="ogTwitterDescriptionForce">Force Default Twitter Description
        </label>
        <span class="SK_Box-disclaimer">Checking this will force this value, no matter what.</span>
      </div>
    </fieldset>
    <?php
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_twitter_creator() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>Enter the Twitter handle for the primary author. If left blank, the tag will be omitted, unless it's set at the post/page level.</p>
      <input type="text" value="<?php echo Utilities::get_option('twitter_creator'); ?>" name="<?php echo Utilities::get_field_name('twitter_creator'); ?>" id="ogTwitterCreator" />

      <div class="SK_Box-checkboxGroup">
        <input type="checkbox" <?php echo $this->checked('twitter_creator_force'); ?> name="<?php echo Utilities::get_field_name('twitter_creator_force'); ?>" id="ogTwitterCreatorForce">
        <label for="ogTwitterCreatorForce">Force Default Twitter Creator
        </label>
        <span class="SK_Box-disclaimer">Checking this will force this value, no matter what.</span>
      </div>
    </fieldset>
    <?php
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_twitter_site() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>Enter the Twitter handle for the site itself. It doesn't matter if the '@' symbol is included. If left blank, the tag will be omitted.</p>
      <input type="text" value="<?php echo Utilities::get_option('twitter_site'); ?>" name="<?php echo Utilities::get_field_name('twitter_site'); ?>" id="ogTwitterSite" />
    </fieldset>
    <?php
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_image() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>If left blank, the featured image on the home page will be used.</p>
      <div class="SK_ImageHolder"
        id="cogImageHolder"
        style="background-image: url('<?php echo Utilities::get_option('image'); ?>')">
        <span class="SK_ImageHolder-remove" id="ogRemoveImage">x</span>
      </div>
      <span class="howto" id="cogUploadedFileName"><?php echo basename(Utilities::get_option('image')); ?></span>
      <div class="SK_Box-buttonWrapper">
        <a class="button button-primary button-large" id="cogImageSelectButton">Choose File</a>
        <span>No image selected.</span>
      </div>
      <input id="cogImage" type="hidden" name="<?php echo Utilities::get_field_name('image'); ?>" value="<?php echo Utilities::get_option('image'); ?>" />

      <div class="SK_Box-checkboxGroup">
        <input type="checkbox" <?php echo $this->checked('image_force'); ?> name="<?php echo Utilities::get_field_name('image_force'); ?>" id="ogImageForce">
        <label for="ogImageForce">Force Default Image</label>
        <span class="SK_Box-disclaimer">Checking this will force this value, no matter what.</span>
      </div>
    </fieldset>
    <?php
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_facebook_app_id() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>Enter the ID of the Facebook app you'd like to grant access to this URL.</p>
      <input type="text" value="<?php echo Utilities::get_option('facebook_app_id'); ?>" name="<?php echo Utilities::get_field_name('facebook_app_id'); ?>" id="ogFacebookAppID" />
    </fieldset>
    <?php
  }

  /**
   * Outputs field markup.
   *
   * @return void
   */
  public function cb_field_facebook_admin_ids() {
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
      <p>Enter the user ID of a person you'd like to give admin access to view insights about this URL.</p>
      <input type="text" value="<?php echo Utilities::get_option('facebook_admin_ids'); ?>" name="<?php echo Utilities::get_field_name('facebook_admin_ids'); ?>" id="ogFacebookAdminIDs" />
    </fieldset>
    <?php
  }
}
