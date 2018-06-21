<?php

namespace Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{

	public function testItExists() {
		$this->assertTrue(class_exists('\CompleteOpenGraph\App'));
	}

    /**
     * Allows non-public class methods to be tested by PHPUnit.
     *
     * @param [object] $object
     * @param [string] $methodName
     * @param array $parameters
     * @return mixed
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
