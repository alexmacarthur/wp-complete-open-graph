<?php
/**
 * Contains code responsible for generating Open Graph markup on each page.
 *
 * @package CompleteOpenGraph
 */

namespace CompleteOpenGraph;

add_action('wp_head', 'CompleteOpenGraph\generate_open_graph_markup');
add_filter('language_attributes', 'CompleteOpenGraph\add_open_graph_prefix', 10, 2);

/**
 * Generate the Open Graph markup.
 *
 * @return void
 */
function generate_open_graph_markup()
{
    if (! apply_filters(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_maybe_enable', true)) {
        return;
    }

    echo "\n\n<!-- Open Graph data is managed by Alex MacArthur's Complete Open Graph plugin. (v" . App::getPluginData()['Version'] . ") -->\n";
    echo "<!-- https://wordpress.org/plugins/complete-open-graph/ -->\n";

    $start_time = microtime(true);

    foreach (Generator::getOpenGraphValues() as $key => $data) {
        if (empty($data['value'])) {
            continue;
        }

        // -- @todo Move this into process_content?
        $content = preg_replace("/\r|\n/", '', $data['value']);
        $content = htmlentities($content, ENT_QUOTES, 'UTF-8', false);

        if ($data['attribute'] === 'property') {
            echo "<meta property='$key' content='$content' />\n";
            continue;
        }

        if ($data['attribute'] === 'name') {
            echo "<meta name='$key' content='$content' />\n";
            continue;
        }
    }

    echo '<!-- End Complete Open Graph. | ' . (microtime(true) - $start_time) . "s -->\n\n";
}

/**
 * Add Open Graph prefixes to <html> tag.
 *
 * @param string $output A space-separated list of language attributes.
 * @param string $doctype The type of html document (xhtml|html).
 */
function add_open_graph_prefix($output, $doctype)
{
    if (! apply_filters(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_maybe_enable', true)) {
        return $output;
    }

    return $output . ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#"';
}
