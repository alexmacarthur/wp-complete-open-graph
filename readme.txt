=== Complete Open Graph ===

Contributors: alexmacarthur
Donate link: paypal.me/alexmacarthur
Tags: open graph, seo, open graph protocol, twitter, facebook, social media, google plus
Requires at least: 3.9
Tested up to: 4.8.1
Stable tag: 3.0.1
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
* twitter:card
* twitter:site

= Default Settings =

As a fallback for values that aren't filled automatically by a page or post, you can set default values for Open Graph data. If desired, you can force these individual values to be used globally, overriding whatever is set at a page/post level. 

* og:type
* og:title
* og:image
* og:image:width
* og:image:height
* og:description
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
  $data['site_name']['value'] = 'whatevs';
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
* twitter:title
* twitter:image
* twitter:description
* twitter:creator

Example for manipulating a processed value:
`
function manipulate_processed_value($value, $field_name) {
    if($field_name === 'description') {
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

== Order of Priority ==

There's a fallback system set in place for you to effectively leverage this plugin. Below is the order of priority: 

1. *Filters* - Any filters you apply in your code will take priority over any fields you have filled in the admin.
2. *Forced Global Settings* - If you've checked the box on these fields on the settings page, they'll override everything non-filtered. 
2. *Post/Page Fields* - Filling out the metabox fields on a page or post in the WordPress Admin will give it priority over any default settings (unless they're forced).
3. *Default Settings* - These will take priority over any core WordPress settings in place (site name, description).
4. *Blog Info* - When nothing else is overriding them, Open Graph fields will default to your general WordPress site settings.

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

== Upgrade Notice ==

= 3.0.0 =
Significant update. Changes how some data is stored, modifies and adds filters, fixes miscellaneous bugs, adds other improvements. If you're using the Complete Open Graph metabox fields on your posts/pages or if you use the filters in your code, this may require you to resave posts/pages and update your code.

= 3.0.1 =
Minor update. Fixes bug that was causing the failure to properly decode all quotation marks.

== Feedback ==

You like it? [Email](mailto:alex@macarthur.me) or [tweet](https://www.twitter.com/amacarthur) me. You hate it? [Email](mailto:alex@macarthur.me) or [tweet](https://www.twitter.com/amacarthur) me.

Regardless of how you feel, your review would be greatly appreciated!

