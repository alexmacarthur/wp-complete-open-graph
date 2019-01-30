<?php

namespace Tests;

class FieldsTest extends \Tests\TestCase
{
    public function setUp()
    {
        $this->fields = include __DIR__ . "/../src/fields.php";
    }

    public function test_eachFieldShouldHaveValidAttribute()
    {
        $onlyValidAttributes = array_filter($this->fields, function ($item) {
            return in_array($item['attribute'], ['property', 'name']);
        });

        $this->assertEquals(count($onlyValidAttributes), count($this->fields));
    }
}
