<?php

namespace Tests;

require_once realpath(dirname(__FILE__) . '/..') . '/src/Filters.php';

/**
 * Sample test case.
 */
class FiltersTest extends \Tests\TestCase
{

    private $filters;

    public function setUp()
    {
        $this->filters = new \CompleteOpenGraph\Filters();
	}

	public function testItShouldReturnTheString() {
		$this->assertEquals(
			$this->filters->attach_image_dimensions('https://www.google.com', 'og:image'),
			'https://www.google.com'
		);
	}

	//-- Ensure this ID actually exists!
	public function testItShouldReturnAttachmentData() {
		$this->assertTrue(
			strlen($this->filters->attach_image_dimensions(13, 'og:image')) > 0
		);

		$this->assertTrue(
			strlen($this->filters->attach_image_dimensions('13', 'og:image')) > 0
		);
	}

	public function testItShouldReturnEmptyStringIfInvalid() {
		$this->assertEquals(
			$this->filters->attach_image_dimensions('999', 'og:image'),
			''
		);
	}

}
