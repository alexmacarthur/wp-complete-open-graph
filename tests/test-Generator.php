<?php

namespace Tests;

require_once(realpath(dirname(__FILE__) . '/..') . '/src/Generator.php');

/**
 * Sample test case.
 */
class GeneratorTest extends \Tests\TestCase {

	private $generator;

	public function setUp() {
		$this->generator = new \CompleteOpenGraph\Generator();
	}

	public function testReturnsOnlyProtectedValues() {
		$allItems = array('value1', 'value2', 'value3');
		$protectedKeys = array(0,1);
		$result = $this->generator->get_only_protected_values($allItems, $protectedKeys);

		$this->assertEquals($result, array(
			'value1',
			'value2'
		));
	}

	public function testShouldSkipNonExistentKeys() {
		$allItems = array('value1', 'value2', 'value3');
		$protectedKeys = array(1, 4, 5);
		$result = $this->generator->get_only_protected_values($allItems, $protectedKeys);

		$this->assertEquals($result, array(
			'value2'
		));
	}

	public function testReturnsFirstUnemptyValue() {

		update_option('complete_open_graph', array());

		$value = $this->generator->get_processed_value(
			'og:description',
			array(
				'first',
				'second',
				'third'
			)
		);

		$this->assertEquals('first', $value);

		$value = $this->generator->get_processed_value(
			'og:description',
			array(
				'',
				'second',
				'third'
			)
		);

		$this->assertEquals('second', $value);
	}

	public function testReturnsGlobalValueIfForced() {

		update_option('complete_open_graph',
			array(
				'og:description' => 'global value',
				'og:description_force' => 'on'
			)
		);

		$value = $this->generator->get_processed_value(
			'og:description',
			array(
				'first',
				'second',
				'third'
			)
		);

		$this->assertEquals('global value', $value);
	}

	public function testGlobalValueFallsBacktoProtectedValueIfEmpty() {

		update_option('complete_open_graph',
			array(
				'og:description' => '',
				'og:description_force' => 'on'
			)
		);

		$value = $this->generator->get_processed_value(
			'og:description',
			array(
				'first',
				'second',
				'third'
			),
			array(2)
		);

		$this->assertEquals('third', $value);
	}

	public function testReturnsNothingIfGlobalValueIsEmptyAndNoProtectedFallbacksAreSet() {

		update_option('complete_open_graph',
			array(
				'og:description' => '',
				'og:description_force' => 'on'
			)
		);

		$value = $this->generator->get_processed_value(
			'og:description',
			array(
				'first',
				'second',
				'third'
			)
		);

		$this->assertEquals('', $value);
	}
}
