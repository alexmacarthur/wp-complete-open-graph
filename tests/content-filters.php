<?php

namespace Tests;

class ContentFiltersTest extends \Tests\TestCase {

	public function test_attach_image_dimensions_itShouldReturnTheString() {
		$this->assertEquals(
			\CompleteOpenGraph\attach_image_dimensions( 'https://www.google.com', 'og:image' ),
			'https://www.google.com'
		);
	}

	public function test_attach_image_dimensions_itShouldReturnEmptyStringIfInvalid() {
		$this->assertEquals(
			\CompleteOpenGraph\attach_image_dimensions( '999', 'og:image' ),
			''
		);
	}

	public function test_append_space_after_period_shouldAppendSpaceAfterEndingCharacter() {
		$this->assertEquals(
			\CompleteOpenGraph\append_space_after_period(
				'This is a sample value.'
			),
			'This is a sample value. '
		);

		$this->assertEquals(
			\CompleteOpenGraph\append_space_after_period(
				'This is a sample value?'
			),
			'This is a sample value? '
		);

		$this->assertEquals(
			\CompleteOpenGraph\append_space_after_period(
				'This is a sample value!'
			),
			'This is a sample value! '
		);
	}

	public function test_append_space_after_period_shouldAppendNoSpaceWhenItDoesnNotWithSpecifiedCharacter() {
		$this->assertEquals(
			\CompleteOpenGraph\append_space_after_period(
				'This is a sample value'
			),
			'This is a sample value'
		);

		$this->assertEquals(
			\CompleteOpenGraph\append_space_after_period(
				'This is a sample value/'
			),
			'This is a sample value/'
		);
	}

	public function test_append_at_symbol_shouldPrependAtSymbolIfNeeded() {
		$this->assertEquals(
			\CompleteOpenGraph\append_at_symbol(
				'something'
			),
			'@something'
		);
	}

	public function test_append_at_symbol_shouldPrependAtSymbolEvenIfStringStartsWithSpace() {
		$this->assertEquals(
			\CompleteOpenGraph\append_at_symbol(
				' somethingelse'
			),
			'@somethingelse'
		);
	}

	public function test_append_at_symbol_shouldNotPrependAtSymbolIfItAlreadyHasOne() {
		$this->assertEquals(
			\CompleteOpenGraph\append_at_symbol(
				'@somethingelseagain'
			),
			'@somethingelseagain'
		);
	}

}
