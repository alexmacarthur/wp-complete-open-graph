<?php

namespace Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{

    public function testItExists()
    {
        $this->assertTrue(class_exists('\CompleteOpenGraph\App'));
    }
}
