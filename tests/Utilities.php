<?php

namespace Tests;

use CompleteOpenGraph\Utilities;

class UtilitiesTest extends \Tests\TestCase
{
    public function test_get_only_protected_values_returnsOnlyProtectedValues()
    {
        $allItems      = [ 'value1', 'value2', 'value3' ];
        $protectedKeys = array( 0, 1 );
        $result        = Utilities::get_only_protected_values($allItems, $protectedKeys);

        $this->assertEquals(
            $result,
            array(
                'value1',
                'value2',
            )
        );
    }

    public function test_get_only_protected_values_shouldSkipNonExistentKeys()
    {
        $allItems      = [ 'value1', 'value2', 'value3' ];
        $protectedKeys = array( 1, 4, 5 );
        $result        = Utilities::get_only_protected_values($allItems, $protectedKeys);

        $this->assertEquals(
            $result,
            array(
                'value2',
            )
        );
    }

    public function test_get_processed_value_returnsFirstUnemptyValue()
    {
        update_option('complete_open_graph', array());

        $value = Utilities::get_processed_value(
            'og:description',
            array(
                'first',
                'second',
                'third',
            )
        );

        $this->assertEquals('first', $value);

        $value = Utilities::get_processed_value(
            'og:description',
            array(
                '',
                'second',
                'third',
            )
        );

        $this->assertEquals('second', $value);
    }

    public function test_get_processed_value_returnsGlobalValueIfForced()
    {
        update_option(
            'complete_open_graph',
            array(
                'og:description'       => 'global value',
                'og:description_force' => 'on',
            )
        );

        $value = Utilities::get_processed_value(
            'og:description',
            array(
                'first',
                'second',
                'third',
            )
        );

        $this->assertEquals('global value', $value);
    }

    public function test_get_processed_value_globalValueFallsBacktoProtectedValueIfEmpty()
    {
        update_option(
            'complete_open_graph',
            array(
                'og:description'       => '',
                'og:description_force' => 'on',
            )
        );

        $value = Utilities::get_processed_value(
            'og:description',
            array(
                'first',
                'second',
                'third',
            ),
            array( 2 )
        );

        $this->assertEquals('third', $value);
    }

    public function test_get_processed_value_returnsNothingIfGlobalValueIsEmptyAndNoProtectedFallbacksAreSet()
    {
        update_option(
            'complete_open_graph',
            array(
                'og:description'       => '',
                'og:description_force' => 'on',
            )
        );

        $value = Utilities::get_processed_value(
            'og:description',
            array(
                'first',
                'second',
                'third',
            )
        );

        $this->assertEquals('', $value);
    }
}
