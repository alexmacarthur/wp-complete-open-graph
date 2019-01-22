<?php

namespace CompleteOpenGraph;

class Generator
{

    /**
     * Get the values for each OG attribute, based on priority & existence of values.
     *
     * @return array Open Graph values
     */
    public static function getOpenGraphValues()
    {
        $frontPageID = (int) get_option('page_on_front');
        $fields      = Utilities::getFields();

        // -- Filter for filtering specific fields.
        foreach ($fields as $key => $item) {
            $value = isset($fields[ $key ]['get_value']) ? $fields[ $key ]['get_value']() : "";
            $fields[ $key ]['value'] = apply_filters(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_' . $key, $value, $key);
        }

        return apply_filters(COMPLETE_OPEN_GRAPH_OPTIONS_PREFIX . '_all_data', $fields);
    }
}
