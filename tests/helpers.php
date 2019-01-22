<?php

namespace Tests;

class HelpersTest extends \Tests\TestCase
{

    public function test_getPrefixedValuesFrom_returnsValidValues()
    {
        $arr = [
            'key1' => 'key1value',
            'complete_open_graph_key2' => 'key2value',
            'key3' => 'key3value',
            'complete_open_graph_key4' => 'key4value',
        ];

        $this->assertEquals(
            \CompleteOpenGraph\getPrefixedValuesFrom($arr),
            [
                'complete_open_graph_key2' => 'key2value',
                'complete_open_graph_key4' => 'key4value'
            ]
        );
    }

    public function test_getPrefixedValuesFrom_doesNotChokeWhenEmptyValueIsPassed()
    {
        $this->assertEquals(
            \CompleteOpenGraph\getPrefixedValuesFrom(null),
            []
        );

        $this->assertEquals(
            \CompleteOpenGraph\getPrefixedValuesFrom([]),
            []
        );
    }
}
