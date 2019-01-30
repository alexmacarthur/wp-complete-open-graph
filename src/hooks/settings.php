<?php

namespace CompleteOpenGraph;

add_action('admin_init', 'CompleteOpenGraph\ensure_attachment_exists', 1);
add_action('admin_init', 'CompleteOpenGraph\register_main_setting');
add_action('admin_init', 'CompleteOpenGraph\register_settings');
add_action('admin_init', 'CompleteOpenGraph\register_facebook_settings');
add_action('admin_init', 'CompleteOpenGraph\register_twitter_settings');
add_action('admin_menu', 'CompleteOpenGraph\open_graph_settings_page');
add_action('admin_enqueue_scripts', 'CompleteOpenGraph\enqueue_settings_page_media');

/**
 * Enqueue media scripts only on COG settings page.
 *
 * @return void
 */
function enqueue_settings_page_media()
{
    if (! isset($_REQUEST['page']) || $_REQUEST['page'] !== 'complete-open-graph') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script('media-upload');
}

function ensure_attachment_exists()
{
    if (! isset($_POST['complete_open_graph']) || ! isset($_POST['complete_open_graph']['og:image'])) {
        return;
    }

    $image     = $_POST['complete_open_graph']['og:image'];
    $imagePath = get_attached_file($image);

    if (is_numeric($image) && ! file_exists($imagePath)) {
        unset($_POST['complete_open_graph']['og:image']);
    }
}

/**
 * Add submenu page for settings.
 *
 * @return void
 */
function open_graph_settings_page()
{
    add_submenu_page('options-general.php', 'Open Graph Settings', 'Open Graph', 'edit_posts', 'complete-open-graph', 'CompleteOpenGraph\open_graph_settings_page_cb');
}

/**
 * Generate markup for settings page.
 *
 * @return void
 */
function open_graph_settings_page_cb()
{
    $github_url    = 'https://github.com/alexmacarthur/wp-complete-open-graph';
    $wordpress_url = 'https://wordpress.org/support/plugin/complete-open-graph/reviews/?rate=5#new-post';
    $twitter_url   = 'https://twitter.com/intent/tweet?text=I%20highly%20recommend%20the%20Complete%20Open%20Graph%20%23WordPress%20plugin%20from%20%40amacarthur!%20https%3A//wordpress.org/plugins/complete-open-graph/'; ?>
    <div
        id="cogSettingsPage"
        class="
            wrap
            <?php echo Utilities::get_option('og:image') ? '' : 'has-no-image '; ?>
            <?php echo Utilities::get_option('force_all') ? 'is-forcing-all ' : ''; ?>"
        >
        <h1>Complete Open Graph Settings</h1>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" class="postbox-container">
                    <form method="post" action="options.php">
                        <?php wp_nonce_field('update-options'); ?>
                        <?php
                            settings_fields('complete_open_graph_settings');
    do_settings_sections(COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG);
    submit_button(); ?>
                    </form>
                </div>

                <div id="postbox-container-1" class="postbox-container">

                    <aside class="SK_SidebarBlock">

                        <h2>Support Complete Open Graph!</h2>

                        <ul class="SK_FeedbackList">
                            <li class="SK_FeedbackList-item SK_FeedbackItem">
                                <a class="SK_FeedbackItem-link" title="Review on WordPress" href="<?php echo $wordpress_url; ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo file_get_contents(COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/assets/img/wordpress.svg.php'); ?>
                                </a>
                                <span class="SK_FeedbackItem-text"><p><a href="<?php echo $wordpress_url; ?>">Leave a 5 Star Review</a></p></span>
                            </li>
                            <li class="SK_FeedbackList-item SK_FeedbackItem">
                                <a class="SK_FeedbackItem-link SK_FeedbackItem-link--github" title="Star on Github" href="<?php echo $github_url; ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo file_get_contents(COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/assets/img/github.svg.php'); ?>
                                </a>
                                <span class="SK_FeedbackItem-text"><p><a href="<?php echo $github_url; ?>">Star on GitHub</a></p></span>
                            </li>
                            <li class="SK_FeedbackList-item SK_FeedbackItem">
                                <a class="SK_FeedbackItem-link" title="Tweet About It" href="<?php echo $twitter_url; ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo file_get_contents(COMPLETE_OPEN_GRAPH_REAL_PATH . 'src/assets/img/twitter.svg.php'); ?>
                                </a>
                                <span class="SK_FeedbackItem-text"><p><a href="<?php echo $twitter_url; ?>">Tweet About It</a></p></span>
                            </li>
                        </ul>
                    </aside>
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
function register_main_setting()
{
    register_setting('complete_open_graph_settings', COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX);
}

/**
 * Register settings specific to Facebook.
 *
 * @return void
 */
function register_facebook_settings()
{
    add_settings_section(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_facebook_parameters', 'Facebook Parameters', 'CompleteOpenGraph\cb_facebook_parameters_section', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG);
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_facebook_admin_ids', 'Facebook Admin IDs', 'CompleteOpenGraph\cb_field_facebook_admin_ids', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_facebook_parameters');
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_facebook_app_id', 'Facebook App ID', 'CompleteOpenGraph\cb_field_facebook_app_id', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_facebook_parameters');
}


/**
 * Register settings specific to Twitter.
 *
 * @return void
 */
function register_twitter_settings()
{
    add_settings_section(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter_parameters', 'Twitter Parameters', 'CompleteOpenGraph\cb_twitter_parameters_section', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG);
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_twitter_card', 'Default Twitter Card', 'CompleteOpenGraph\cb_field_twitter_card', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter_parameters');
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_twitter_description', 'Default Twitter Description', 'CompleteOpenGraph\cb_field_twitter_description', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter_parameters');
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_twitter_creator', 'Default Twitter Creator', 'CompleteOpenGraph\cb_field_twitter_creator', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter_parameters');
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_twitter_site', 'Default Twitter Site', 'CompleteOpenGraph\cb_field_twitter_site', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_twitter_parameters');
}

/**
 * Register Default fallback and force settings.
 *
 * @return void
 */
function register_settings()
{
    add_settings_section(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_fallbacks', 'Default Fallbacks', 'CompleteOpenGraph\cb_fallbacks_section', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG);
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_force_all', 'Force All', 'CompleteOpenGraph\cb_field_force_all', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_fallbacks');
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_type', 'Default Type', 'CompleteOpenGraph\cb_field_type', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_fallbacks');
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_title', 'Default Title', 'CompleteOpenGraph\cb_field_title', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_fallbacks');
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_description', 'Default Description', 'CompleteOpenGraph\cb_field_description', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_fallbacks');
    add_settings_field(COMPLETE_OPEN_GRAPH_OPTIONS_SHORT_PREFIX . '_image', 'Default Image', 'CompleteOpenGraph\cb_field_image', COMPLETE_OPEN_GRAPH_ADMIN_SETTINGS_PAGE_SLUG, COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_fallbacks');
}

/**
 * Returns whether a certain input is checked.
 *
 * @param  string $key The field key
 * @return string
 */
function checked($key)
{
    return Utilities::get_option($key) === 'on' ? 'checked' : '';
}

/**
 * Outputs some basic instructions.
 *
 * @return void
 */
function cb_fallbacks_section()
{
    echo '<p>These settings will serve as fallbacks in case individual pages/posts do not have information supplied. If you wish to force these values to always override individual posts/pages, check the box under each option.</p>';
}

/**
 * Outputs some more basic instructions.
 *
 * @return void
 */
function cb_facebook_parameters_section()
{
    echo '<p>Optionally modify Default / fallback settings for Facebook-related tags, including those that tie your site to a partiular Facebook page or account.</p>';
}

/**
 * Outputs some more basic instructions.
 *
 * @return void
 */
function cb_twitter_parameters_section()
{
    echo '<p>Optionally modify global/fallback settings for Twitter-related Open Graph tags.</p>';
}

/**
 * Outputs field markup.
 *
 * @return void
 */
function cb_field_type()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>If left blank, 'website' will be used.</p>
        <input type="text" value="<?php echo Utilities::get_option('og:type'); ?>" name="<?php echo Utilities::get_field_name('og:type'); ?>" id="ogType" />

        <div class="SK_Box-checkboxGroup">
            <input type="checkbox" <?php echo \CompleteOpenGraph\checked('og:type_force'); ?> name="<?php echo Utilities::get_field_name('og:type_force'); ?>" id="ogTypeForce">
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
function cb_field_force_all()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut SK_Box--important">
        <p><strong>Pay attention!</strong> Checking this box will force all of the values from the fields here to be applied universally. Only check this if you want your Open Graph data to be completely uniform throughout your site.</p>

        <div class="SK_Box-checkboxGroup is-immune">
            <input type="checkbox" <?php echo \CompleteOpenGraph\checked('force_all'); ?> name="<?php echo Utilities::get_field_name('force_all'); ?>" id="ogForceAll">
            <label for="ogForceAll"><strong>Force All Fallback Settings</strong></label>
            <span class="SK_Box-disclaimer">Checking this will force the values on this page to be used on very page, no matter what.</span>
        </div>

    </fieldset>
    <?php
}

/**
 * Outputs field markup.
 *
 * @return void
 */
function cb_field_title()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>If left blank, the site title will be used.</p>
        <input type="text" value="<?php echo Utilities::get_option('og:title'); ?>" name="<?php echo Utilities::get_field_name('og:title'); ?>" id="ogDescription" />

        <div class="SK_Box-checkboxGroup">
            <input type="checkbox" <?php echo \CompleteOpenGraph\checked('og:title_force'); ?> name="<?php echo Utilities::get_field_name('og:title_force'); ?>" id="ogTitleForce">
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
function cb_field_description()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>If left blank, the site description will be used.</p>
        <input type="text" value="<?php echo Utilities::get_option('og:description'); ?>" name="<?php echo Utilities::get_field_name('og:description'); ?>" id="ogDescription" />

        <div class="SK_Box-checkboxGroup">
            <input type="checkbox" <?php echo \CompleteOpenGraph\checked('og:description_force'); ?> name="<?php echo Utilities::get_field_name('og:description_force'); ?>" id="ogDescriptionForce">
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
function cb_field_twitter_card()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>The type of Twitter card that will be generated for Open Graph. To learn about what these types mean, see <a target="_blank" href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/abouts-cards">Twitter's documentation</a>.</p>

        <?php $cardValue = Utilities::get_option('twitter:card'); ?>

        <select name="<?php echo Utilities::get_field_name('twitter:card'); ?>" id="ogTwitterCard">
            <option <?php selected($cardValue, 'summary'); ?> value="summary">Summary</option>
            <option <?php selected($cardValue, 'summary_large_image'); ?> value="summary_large_image">Large Summary</option>
            <option <?php selected($cardValue, 'app'); ?> value="app">App</option>
            <option <?php selected($cardValue, 'player'); ?> value="player">Player</option>
        </select>

        <div class="SK_Box-checkboxGroup">
            <input type="checkbox" <?php echo \CompleteOpenGraph\checked('twitter:card_force'); ?> name="<?php echo Utilities::get_field_name('twitter:card_force'); ?>" id="ogTwitterCardForce">
            <label for="ogTwitterCardForce">Force Default Twitter Card</label>
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
function cb_field_twitter_description()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>If left blank, the description will be used.</p>
        <input type="text" value="<?php echo Utilities::get_option('twitter:description'); ?>" name="<?php echo Utilities::get_field_name('twitter:description'); ?>" id="ogTwitterDescription" />

        <div class="SK_Box-checkboxGroup">
            <input type="checkbox" <?php echo \CompleteOpenGraph\checked('twitter:description_force'); ?> name="<?php echo Utilities::get_field_name('twitter:description_force'); ?>" id="ogTwitterDescriptionForce">
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
function cb_field_twitter_creator()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>Enter the Twitter handle for the primary author. If left blank, the tag will be omitted, unless it's set at the post/page level.</p>
        <input type="text" value="<?php echo Utilities::get_option('twitter:creator'); ?>" name="<?php echo Utilities::get_field_name('twitter:creator'); ?>" id="ogTwitterCreator" />

        <div class="SK_Box-checkboxGroup">
            <input type="checkbox" <?php echo \CompleteOpenGraph\checked('twitter:creator_force'); ?> name="<?php echo Utilities::get_field_name('twitter:creator_force'); ?>" id="ogTwitterCreatorForce">
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
function cb_field_twitter_site()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>Enter the Twitter handle for the site itself. It doesn't matter if the '@' symbol is included. If left blank, the tag will be omitted.</p>
        <input type="text" value="<?php echo Utilities::get_option('twitter:site'); ?>" name="<?php echo Utilities::get_field_name('twitter:site'); ?>" id="ogTwitterSite" />
    </fieldset>
    <?php
}

/**
 * Outputs field markup.
 *
 * @return void
 */
function cb_field_image()
{
    $imageID = Utilities::get_option('og:image');

    // -- Ensure the image exists before getting the URL.
    if (empty($imageID)) {
        $imageURL = '';
    } else {
        $imageAttachment = wp_get_attachment_image_src($imageID, 'medium');
        $imageURL        = isset($imageAttachment[0]) ? $imageAttachment[0] : '';
    } ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>If left blank, the featured image on the home page will be used.</p>
        <div class="SK_ImageHolder"
            id="cogImageHolder"
            style="background-image: url('<?php echo $imageURL; ?>')">
            <span class="SK_ImageHolder-remove" id="ogRemoveImage">x</span>
        </div>
        <span class="howto" id="cogUploadedFileName"><?php echo basename($imageURL); ?></span>
        <div class="SK_Box-buttonWrapper">
            <a class="button button-primary button-large" id="cogImageSelectButton">Choose File</a>
            <span>No image selected.</span>
        </div>
        <input id="cogImage" type="hidden" name="<?php echo Utilities::get_field_name('og:image'); ?>" value="<?php echo Utilities::get_option('og:image'); ?>" />

        <div class="SK_Box-checkboxGroup">
            <input type="checkbox" <?php echo \CompleteOpenGraph\checked('og:image_force'); ?> name="<?php echo Utilities::get_field_name('og:image_force'); ?>" id="ogImageForce">
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
function cb_field_facebook_app_id()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>Enter the ID of the Facebook app you'd like to grant access to this URL.</p>
        <input type="text" value="<?php echo Utilities::get_option('fb:app_id'); ?>" name="<?php echo Utilities::get_field_name('fb:app_id'); ?>" id="ogFacebookAppID" />
    </fieldset>
    <?php
}

/**
 * Outputs field markup.
 *
 * @return void
 */
function cb_field_facebook_admin_ids()
{
    ?>
    <fieldset class="SK_Box SK_Box--standOut">
        <p>Enter the user ID of a person you'd like to give admin access to view insights about this URL.</p>
        <input type="text" value="<?php echo Utilities::get_option('fb:admins'); ?>" name="<?php echo Utilities::get_field_name('fb:admins'); ?>" id="ogFacebookAdminIDs" />
    </fieldset>
    <?php
}
