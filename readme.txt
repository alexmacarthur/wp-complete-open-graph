=== Complete Open Graph ===
Contributors: alexmacarthur
Donate link: paypal.me/alexmacarthur
Tags: open graph, seo
Requires at least: 4.0.0
Tested up to: 4.5.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin for simple, comprehensive, customizable Open Graph management.

== Description ==

I've been hard-pressed to find a simple yet highly customizable plugin to easily manage Open Graph information, so I've made one. Out of the box, the plugin spins up all the essential Open Graph meta tags, but using filters, you have the ability to add more yourself -- including all types of standard meta tags, if you wish.

In terms of output, the plugin attaches via `wp_head` to output these meta tags and nothing else. No other assets are loaded to your page.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-complete-open-graph` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->Open Graph screen to set your global Open Graph data (if desired).

== Using the Plugin ==

Upon activation, Complete Open Graph is ready to generate Open Graph meta tags, as long as information exists to fill them.

= Page/Post Fields =

On each page and post, you have the ability to define Open Graph data, or allow it to be set automatically.

* og:site_name
* og:locale
* og:type
* og:title
* og:url
* og:description
* og:image
* twitter:card
* twitter:creator
* twitter:title
* twitter:description
* twitter:image
* twitter:url

= Global Fields =

As a fallback for values that aren't filled automatically by a page or post, you can set global values for Open Graph data.

* og:type
* og:title
* og:image
* og:description
* fb:admins
* fb:app_id
* twitter:description

== Filtering Meta Tags ==

The `cog_open_graph_data` filter exists to allow the customization of values, as well as the addition of new meta tags (including those that aren't Open Graph).

Example for customizing out-of-the-box Open Graph data:
```php
add_filter('cog_open_graph_data', 'modify_open_graph_data');
function modify_open_graph_data($cog_data) {
	$cog_data['site_name']['value'] = 'My Custom Site Name';
	return $cog_data;
}
```

Example for adding a standard, old meta tag:
```php
add_filter('cog_open_graph_data', 'add_new_open_graph_fields');
function add_new_open_graph_fields($cog_data) {
	$cog_data['keywords']['type'] = 'standard';
	$cog_data['keywords']['value'] = 'keyword1,keyword2,keyword3';
	return $cog_data;
}
```

== Order of Priority ==

There's an order of priority set in place for you to effectively leverage this plugin.

1. *Filters* - Any filters you apply will take priority over any fields you have filled in the admin.
2. *Post/Page Fields* - Filling out the fields on a page or post in the WordPress Admin will give it priority over any global settings.
3. *Global Settings* - These will take priority over any core WordPress settings in place (site name, description).
4. *Blog Info* - When nothing else is overriding them, Open Graph fields will default to your general WordPress site settings.

After flowing through this order of priority, if there is still no content to be pulled, those respective Open Graph will not be generated. So, don't worry about having extra, useless tags just sitting there in your markup.

== Screenshots ==

1. Shows the global settings page, where you can define global values for Open Graph tags, which serve as a fallback in case these values are not occupied on individual posts or pages.
2. Shows the form available to customize Open Graph information on individual posts and pages.

== Changelog ==

= 1.0.1 =
* Initial public release.

= 1.0.2 =
* Improve documentation.
* Remove bits of logic that require at least PHP 7.

== Feedback ==

You like it? [Email](mailto:alex@macarthur.me) or [tweet](http://www.twitter.com/amacarthur) me.

You hate it? [Email](mailto:alex@macarthur.me) or [tweet](http://www.twitter.com/amacarthur) me.
