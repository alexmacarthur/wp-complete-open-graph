=== Complete Open Graph ===

Contributors: alexmacarthur
Donate link: paypal.me/alexmacarthur
Tags: open graph, seo, open graph protocol, twitter, facebook, social media, google plus
Requires at least: 3.9
Requires PHP: 5.6
Tested up to: 5.0.3
Stable tag: 3.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple, comprehensive, customizable Open Graph management.

== Description ==

There's no shortage of plugins that promise to be THE all-in-one solution for all things SEO. Unfortunately, this often means lack of flexibility, confusing implementation, or just a big, bloated plugin that carries way too many features for your needs.

This plugin is built on an alternative philosophy: do one thing and one thing well. Complete Open Graph provides automatic, comprehensive, just-makes-sense Open Graph management, whether it's for a simple blog or a complex site with diverse sets of content.

Out of the box, Complete Open Graph generates all the basic tags your site should have, making it ready for social sharing on platforms including Twitter, Facebook, LinkedIn and Google+, and gives you full programmatic access to filter this data as you need.

TL;DR: This plugin does Open Graph. Freaking good Open Graph.

== Installation ==

1. Download the plugin and upload to your plugins directory, or install the plugin through the WordPress plugins page.
2. Activate the plugin on the 'Plugins' page.
3. (Optional) Use the Settings->Open Graph screen to set your default Open Graph data.

== Using the Plugin ==

Upon activation, Complete Open Graph is ready to generate Open Graph meta tags, with an intuitive set of fallbacks in place. Literally no configuration is required to begin making your site socially shareable.

= Available Fields =

On each page and post, the following fields are automatically generated, based on available page data. Many of these can be manually set at the page/post level.

* og:site_name
* og:locale
* og:type
* og:title
* og:url
* og:description
* og:image
* og:image:width
* og:image:height
* twitter:card
* twitter:creator
* twitter:title
* twitter:description
* twitter:image
* twitter:url
* twitter:site
* profile:first_name (Currently only displayed on individual author pages.)
* profile:last_name (Currently only displayed on individual author pages.)

= Default Settings =

As a fallback for values that aren't filled automatically by a page or post, you can set default values for Open Graph data. If desired, you can force these individual values to be used globally, overriding whatever is set at a page/post level. You're able to force individual fields, or force all of them at once.

* og:type
* og:title
* og:image
* og:image:width
* og:image:height
* og:description
* twitter:card
* twitter:description
* twitter:creator
* twitter:site
* fb:admins
* fb:app_id

== Filters ==

The `complete_open_graph_all_data` filter allows the customization of the entire set of Open Graph values, as well as the addition of new meta tags (including those that aren't Open Graph).

Example for customizing out-of-the-box Open Graph data:
`
function modify_open_graph_data($data) {
  $data['og:site_name']['value'] = 'whatevs';
  return $data;
}
add_filter('complete_open_graph_all_data', 'modify_open_graph_data');
`

Example for adding a standard, old meta tag:
`
function add_new_open_graph_fields($data) {
    $data['keywords']['attribute'] = 'name';
    $data['keywords']['value'] = 'keyword1,keyword2,keyword3';
    return $data;
}
add_filter('complete_open_graph_all_data', 'add_new_open_graph_fields');
`

The `complete_open_graph_processed_value` filter allows you to modify a single field after it's gone through the progression of priorities. For that reason, it will only be effective on the following fields:

* og:description
* og:title
* og:type
* og:image
* twitter:card
* twitter:title
* twitter:image
* twitter:description
* twitter:creator

Example for manipulating a processed value:
`
function manipulate_processed_value($value, $field_name) {
    if($field_name === 'og:description') {
        return 'WHATEVER I WANT.';
    }
    return $value;
}
add_filter('complete_open_graph_processed_value', 'manipulate_processed_value', 10, 2);
`

The `complete_open_graph_{$tagName}` filter allows you to modify a single field by identifying it by name and returning a modified value. These names are the "name" or "property" attributes on the meta tags. See "Available Fields" above for these names.

Example for manipulating a single value by name:
`
function modify_title($value, $field_name) {
    return 'My Newly Modified Title!'
}

add_filter('complete_open_graph_og:title', 'modify_title', 10, 2);
`

The `complete_open_graph_maybe_enable` filter allows you to disable tag generation altogether by returning a boolean.

Example for disabling generation altogether:
`
add_filter('complete_open_graph_maybe_enable', '__return_false');
`

Example for disabling generation on a specific page:
`
add_filter('complete_open_graph_maybe_enable', function ($maybeEnable) {
	global $post;

	if($post->post_name === 'my-page') {
		return false;
	}

	return $maybeEnable;
});
`

== Order of Priority ==

There's a fallback system set in place for you to effectively leverage this plugin. Below is the order of priority:

1. *Filters* - Any filters you apply in your code will take priority over any fields you have filled in the admin.
2. *Forced Global Settings* - If you've checked the box on these fields on the settings page, they'll override everything non-filtered.
3. *Post/Page COG Fields* - Filling out the meta box fields on a page or post in the WordPress Admin will give it priority over any default settings (unless they're forced).
4. *Post/Page Content*  - If no specific COG fields on the post/page are set, the post/page content itself will be used. For the Open Graph description, the excerpt will be respected if it's filled.
5. *Default COG Settings* - Next, the default COG global settings will used to populate Open Graph tags.
6. *Blog Info* - When nothing else is overriding them, Open Graph fields will default to your general WordPress site settings.

After flowing through this order of priority, if there is still no content to be pulled, those respective Open Graph tags will not be generated. So, don't worry about having extra, useless tags just sitting there in your markup.

== Screenshots ==

1. Shows the default settings page, where you can define global values for Open Graph tags, which serve as a fallback in case these values are not occupied on individual posts or pages.
2. Shows the form available to customize Open Graph information on individual posts and pages.

== Frequently Asked Questions ==

= For which social sites will this plugin make my content shareable? =
Your content will be shareable to any site that processes Open Graph meta tags, including Facebook, Twitter, Google+, and LinkedIn.

= Is any configuration needed out-of-the-box? =
No. You may customize any data you want, but once you activate the plugin, it'll immediately begin pulling data from your existing site content.

= Should I use this in conjunction with other SEO or Open Graph plugins? =
No, it's not recommended. COG does one thing and one thing well: Open Graph. If you're using another plugin to generate that markup, you'll end up with duplicate OG tags, which may throw flags in Facebook's debugger. If you are using another plugin to manage SEO, at least ensure that Open Graph generation is toggled off, to avoid duplicate tags.

= How do I make sure it's actually working? =
Your best option is to use Facebook's Sharing Debugger found here: https://developers.facebook.com/tools/debug/. Another option is to share your page to Facebook, Twitter, and see what's rendered.

== Changelog ==

= 1.0.1 =
* Initial public release.

= 1.0.2 =
* Improve documentation.
* Remove bits of logic that require at least PHP 7.

= 2.0.0 =
* Change `cog_open_graph_data` filter name to `complete_open_graph_all_data`.
* Add `complete_open_graph_single_value` filter.
* Add ability to force global values on all pages.
* Instead of storing global settings in individual option keys, all settings are serialized in the `complete_open_graph` key, making for a slightly tidier database.

= 2.1.0 =
* Add support for `twitter:card` (currently only supports "summary").
* Add support for `twitter:creator`.
* Add support for `twitter:site`.
* Add support for `og:image:width`.
* Add support for `og:image:height`.

= 2.1.1 =
* Add proper <html> prefix for Open Graph & Facebook parsing.

= 2.1.2 =
* Strip shortcodes from generated Open Graph content.

= 2.1.3 =
* Fix incorrectly rendered tags for `fb:app_id` and `fb:admins`.

= 2.1.4 =
* Fix bug producing errors when $post object is not set (like 404 pages).

= 3.0.0 =
* Display default data on pages where post object is not set (like 404 pages).
* Fix bug preventing image size tags from being rendered.
* Strip style and script tags from generated Open Graph descriptions.
* Rename `complete_open_graph_single_value` filter to `complete_open_graph_processed_value`.
* Add `complete_open_graph_{$tagName}` filter to target specific fields.
* Preserve HTML entities in generated content.
* Default to `large` image sizes for images uploaded before plugin was installed.

= 3.0.1 =
* Fix bug which was causing the failure to properly decode all quotation marks.

= 3.0.2 =
* Fix incorrect reference to `$GLOBALS` superglobal.
* Fix miscellaneous bugs caused when `post` object wasn't set.
* Fix bug involving specific use of `empty()` function which is unsupported in older versions of PHP and causing a few errors.
* Fix bug with generating `og:image` tags on blog list pages.
* Make performance improvements to option lookups.

= 3.0.3 =
* Fix warning with using `loadHTML()` for parsing certain types of content.
* Correctly add space after line breaks are removed from sentances ending in `?` and `!`.

= 3.1.0 =
* Fix styling issue causing strange layout of COG metabox.
* Add support for different Twitter card types.

= 3.1.1 =
* Properly encode special characters into HTML entities.

= 3.1.2 =
* Add checks for image existence to avoid potential errors.

= 3.2.0 =
* Respect post/page excerpt when generating Open Graph descriptions.
* Add feature to force all fallback settings at once.
* Improve image filtering to prevent potential bugs.

= 3.2.1 =
* Fix bug pulling Open Graph data from first post on any archive page; instead, it falls back to global settings.
* Slightly improve efficiency of generating Open Graph markup for each page.

= 3.2.2 =
* Uses the `get_locale()` method instead of hard-coding the value for the og:locale meta tag.

= 3.2.3 =
* Enqueue WP media scripts with better scope and more flexibility.
* Put `CompleteOpenGraph\App` into `$GLOBALS` to allow easier filtering and access within themes and plugins.

= 3.2.4 =
* Fix bug causing console error to be thrown on pages that didn't have the `media-upload` script enqueued.

= 3.2.5 =
* Fix bug causing errors when invalid attachment IDs were passed to Open Graph parser.
* Add version to assets URLs to break bust cache of outdated files.

= 3.2.6 =
* Fix incorrect reference to class property.

= 3.2.7 =
* Set up basic unit testing for improved code reliability.
* Improve handling of default values and how they're handled if left empty.

= 3.3.0 =
* Fix errors being thrown in PHP versions under 5.6.
* Add filter to disable Open Graph tags per page.

= 3.3.1 =
* Improve the logic (and respective efficiency) of determining how image meta tags are generated.

= 3.4.0 =
* Improve code organization under the hood.
* Display "profile:first_name" and "profile:last_name" meta tags on author pages.

= 3.4.1 =
* Fix constant-related bug for installations using older versions of PHP.

= 3.4.2 =
* Removes use of `array_filter` that relies on ARRAY_FILTER_USE_KEY constant for greater PHP backwards compatibility.

= 3.4.3 =
* Fixes the generation of the `og:type` tag, which was not displaying at all.
* On author archive pages, if author has an avatar image, use that as OG image.

= 3.4.4 =
* Fix sizing issue with uploaded images less than 1200px wide.
* Require that selected OG images be, at minimum, 200px x 200px.
* Ensure that OG URLs are never relative.

= 3.4.5 =
* Fixes the attribute used to set up `twitter:description` tags.
* If no explicit `twitter:description` exists on an archive page, use the `og:description`.

== Feedback ==

You like it? [Email](mailto:alex@macarthur.me) or [tweet](https://www.twitter.com/amacarthur) me. You hate it? [Email](mailto:alex@macarthur.me) or [tweet](https://www.twitter.com/amacarthur) me.

Regardless of how you feel, your review would be greatly appreciated!
