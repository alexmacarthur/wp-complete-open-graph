# Complete Open Graph
A WordPress plugin for simple, comprehensive, customizable Open Graph management.

## Description
There's a wide variety of plugins available to manage Open Graph data for your site, but none appear to be excellent at balancing simplicity and comprehensive, customizable configuration. Here's a plugin that, with no hassle, allow you to easily manage Open Graph data for your site, whether it's a simple blog or a complex site with diverse sets of data. 

Out of the box, Complete Open Graph generates all the basic tags your site should have, making it ready for effective social sharing on platforms including Twitter and Facebook, and give you full programmatic access to filter this data as you need. In fact, Complete Open Graph is prepared to generate all types of meta tags -- not just Open Graph.

No gimmicks -- just simple, complete Open Graph management. Simple as that.

## Setup
1. Install the plugin.
2. Activate the plugin.

## Using the Plugin

### Out-of-the-Box Tags

#### Page/Post Fields
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

#### Global Fields
As a fallback for values that aren't filled automatically by a page or post, you can set global values for Open Graph data.

* og:type
* og:title
* og:image
* og:description
* fb:admins
* fb:app_id
* twitter:description

### Filtering Meta Tags
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

### Order of Priority
There's an order of priority set in place for you to effectively leverage this plugin.

1. *Filters* - Any filters you apply will take priority over any fields you have filled in the admin.
2. *Post/Page Fields* - Filling out the fields on a page or post in the WordPress Admin will give it priority over any global settings.
3. *Global Settings* - These will take priority over any core WordPress settings in place (site name, description).
4. *Blog Info* - When nothing else is overriding them, Open Graph fields will default to your general WordPress site settings.

After flowing through this order of priority, if there is still no content to be pulled, those respective Open Graph will not be generated. So, don't worry about having extra, useless tags just sitting there in your markup.

## Feedback
You like it? [Email](mailto:alex@macarthur.me) or [tweet](http://www.twitter.com/amacarthur) me.

You hate it? [Email](mailto:alex@macarthur.me) or [tweet](http://www.twitter.com/amacarthur) me.
