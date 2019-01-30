<?php

namespace CompleteOpenGraph;

/**
 * A class to help access field configuration data.
 */
class Field
{
    public function __construct($key)
    {
        $this->fields = Utilities::getFields();
        $this->key    = $key;
        $this->field  = $this->fields[ $key ];
    }

    public function __get($property)
    {
        if ($property === 'name') {
            return 'complete_open_graph_' . $this->key;
        }

        if ($property === 'id') {
            $result = preg_split('/(_|:)/', $this->key);

            $result = array_map(
                function ($item) {
                    return ucfirst($item);
                },
                $result
            );

            return 'cog' . implode('', $result);
        }

        if (isset($this->field[ $property ])) {
            return $this->field[ $property ];
        }

        return '';
    }

    /**
     * Return only the fields that are configurable within the admin.
     */
    public static function getConfigurable($fields = null)
    {
        $fields = is_null($fields) ? Utilities::getFields() : $fields;

        $configurableFields = array_filter(
            $fields,
            function ($item) {
                return isset($item['is_configurable']) && $item['is_configurable'];
            }
        );

        $fields = [];

        foreach ($configurableFields as $k => $v) {
            $fields[] = new self($k);
        }

        return $fields;
    }
}
