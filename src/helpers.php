<?php

namespace CompleteOpenGraph;

/**
 * Retrieves the keys that are prefixed
 * with COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX.
 *
 * @return void
 */
function getPrefixedValuesFrom($arr)
{
    if (empty($arr)) {
        return [];
    }

    $validKeys = [];

    foreach ($arr as $key => $value) {
        if (substr($key, 0, 19) === COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX) {
            $validKeys[$key] = $value;
        }
    }

    return $validKeys;
}
