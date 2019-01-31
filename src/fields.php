<?php

namespace CompleteOpenGraph;

$frontPageID = (int) get_option('page_on_front');

return [
    'og:site_name'        => [
        'label'     => 'Site Name',
        'attribute' => 'property',
        'get_value' => function () {
            return get_bloginfo('name');
        },
    ],
    'og:url'              => [
        'attribute' => 'property',
        'get_value' => function () {
            return Utilities::get_processed_value(
                'og:url',
                array(
                    get_permalink(Utilities::get_post_decorator()->ID),
                    get_bloginfo('url'),
                ),
                array( 1 )
            );
        },
    ],
    'og:locale'           => [
        'attribute' => 'property',
        'get_value' => function () {
            return get_locale();
        },
    ],
    'og:description'      => $description = [
        'is_configurable' => true,
        'field_type'      => 'text',
        'attribute'       => 'property',
        'label'           => 'Description',
        'description'     => 'If left blank, the excerpt will be used. If no excerpt exists, the site description will be used.',
        'get_value'       => function () {
            return Utilities::get_processed_value(
                'og:description',
                array(
                    Utilities::get_post_option('og:description'),
                    Utilities::get_post_decorator()->post_excerpt,
                    Utilities::get_post_decorator()->post_content,
                    Utilities::get_option('og:description'),
                    get_bloginfo('description'),
                ),
                array( 3, 4 )
            );
        },
    ],
    'og:title'            => $title       = [
        'is_configurable' => true,
        'field_type'      => 'text',
        'attribute'       => 'property',
        'label'           => 'Title',
        'description'     => 'If left blank, the post title will be used. If that does not exist, the site title will be used.',
        'get_value'       => function () {
            return Utilities::get_processed_value(
                'og:title',
                array(
                    Utilities::get_post_option('og:title'),
                    get_the_title(),
                    Utilities::get_option('og:title'),
                    get_bloginfo('name'),
                ),
                array( 2, 3 )
            );
        },
    ],
    'og:type'             => [
        'is_configurable' => true,
        'field_type'      => 'text',
        'attribute'       => 'property',
        'label'           => 'Type',
        'description'     => 'If left blank, the global \'type\' will be used. If you choose to override it, make sure it follows the correct <a href="https://developers.facebook.com/docs/reference/opengraph/" target="_blank">object type formatting</a>.',
        'get_value'       => function () {
            return Utilities::get_processed_value(
                'og:type',
                array(
                    Utilities::get_post_option('og:type'),
                    is_single() ? 'article' : '',
                    Utilities::get_option('og:type'),
                    'website',
                ),
                array( 2, 3 )
            );
        },
    ],
    'og:image'            => $image       = [
        'is_configurable' => false,
        'attribute'       => 'property',
        'label'           => 'Image',
        'description'     => 'If left empty, the post\'s featured image will be used. If no featured image exists, the front page featured image will be used.',
        'get_value'       => function () use ($frontPageID) {
            return Utilities::get_processed_value(
                'og:image',
                array(
                    Utilities::get_post_option('og:image'),
                    get_post_thumbnail_id(Utilities::get_post_decorator()->ID),
                    Utilities::get_first_image(),
                    Utilities::get_option('og:image'),
                    ! empty($frontPageID) && has_post_thumbnail($frontPageID)
                        ? get_post_thumbnail_id($frontPageID)
                        : false,
                ),
                array( 3, 4 )
            );
        },
    ],
    'og:image:width'      => [
        'attribute' => 'property',
    ],
    'og:image:height'     => [
        'attribute' => 'property',
    ],
    'twitter:card'        => [
        'label'           => 'Twitter Summary Card Type',
        'attribute'       => 'name',
        'is_configurable' => true,
        'field_type'      => 'select',
        'field_options'   => [
            'Summary'       => 'summary',
            'Large Summary' => 'summary_large_image',
            'App'           => 'app',
            'Player'        => 'player',
        ],
        'description'     => 'The type of Twitter card that will be generated for Open Graph. To learn about what these types mean, see <a target="_blank" href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/abouts-cards">Twitter\'s documentation</a>.',
        'get_value'       => function () {
            return Utilities::get_processed_value(
                'twitter:card',
                array(
                    Utilities::get_post_option('twitter:card'),
                    Utilities::get_option('twitter:card'),
                    'summary',
                ),
                array( 2 )
            );
        },
    ],
    'twitter:creator'     => [
        'label'           => 'Twitter Creator',
        'description'     => 'If left blank, the global value will be used. It doesn\'t matter if you include the \'@\' symbol.',
        'is_configurable' => true,
        'field_type'      => 'text',
        'attribute'       => 'name',
        'get_value'       => function () {
            return Utilities::get_processed_value(
                'twitter:creator',
                array(
                    Utilities::get_post_option('twitter:creator'),
                    Utilities::get_option('twitter:creator'),
                )
            );
        },
    ],
    'twitter:site'        => [
        'attribute' => 'name',
        'get_value' => function () {
            return Utilities::get_option('twitter:site');
        },
    ],
    'twitter:title'       => [
        'attribute' => 'name',
        'get_value' => $title['get_value'],
    ],
    'twitter:image'       => [
        'attribute' => 'name',
        'get_value' => $image['get_value'],
    ],
    'twitter:description' => [
        'is_configurable' => true,
        'label'           => 'Twitter Description',
        'field_type'      => 'text',
        'description'     => 'If left blank, the description will be used.',
        'attribute'       => 'name',
        'get_value'       => function () use ($description) {
            return Utilities::get_processed_value(
                'twitter:description',
                array(
                    Utilities::get_post_option('twitter:description'),
                    Utilities::get_post_decorator()->post_excerpt,
                    Utilities::get_post_decorator()->post_content,
                    Utilities::get_option('twitter:description'),
                    $description['get_value'](),
                ),
                [4]
            );
        },
    ],
    'fb:admins'           => [
        'attribute' => 'property',
        'get_value' => function () {
            return Utilities::get_option('fb:admins');
        },
    ],
    'fb:app_id'           => [
        'attribute' => 'property',
        'get_value' => function () {
            return Utilities::get_option('fb:app_id');
        },
    ],
    'profile:first_name' => [
        'attribute' => 'property',
        'get_value' => function () {
            return is_author() ? get_the_author_meta('first_name') : "";
        }
    ],
    'profile:last_name' => [
        'attribute' => 'property',
        'get_value' => function () {
            return is_author() ? get_the_author_meta('last_name') : "";
        }
    ]
];
