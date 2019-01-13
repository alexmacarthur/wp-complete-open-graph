<?php

namespace Tests;

use CompleteOpenGraph\Field;

class FieldTest extends \Tests\TestCase {

	public function test_constructor_shouldReturnCorrectField() {
		$field = new Field( 'og:site_name' );
		$this->assertEquals( $field->field['label'], 'Site Name' );
	}

	public function test___get_shouldReturnCorrectInputName() {
		$field = new Field( 'og:site_name' );
		$this->assertEquals( $field->name, 'complete_open_graph_og:site_name' );
	}

	public function test___get_shouldReturnCorrectInputID() {
		$field = new Field( 'og:site_name' );
		$this->assertEquals( $field->id, 'cogOgSiteName' );

		$field = new Field( 'twitter:creator' );
		$this->assertEquals( $field->id, 'cogTwitterCreator' );
	}

	public function test___get_shouldReturnProperty() {
		$field = new Field( 'og:site_name' );
		$this->assertEquals( $field->attribute, 'property' );
	}

	public function test_getConfigurable_shouldReturnConfigurableFieldInstances() {
		$fields = [
			'og:title'       => [
				'is_configurable' => true,
			],
			'og:description' => [
				'is_configurable' => true,
			],
			'og:locale'      => [
				'is_configurable' => false,
			],
		];

		$result = Field::getConfigurable( $fields );

		$this->assertEquals( count( $result ), 2 );
	}

	public function test_getConfigurable_returnedInstancesShouldBeFields() {
		$fields = [
			'og:title' => [
				'is_configurable' => true,
			],
		];

		$result = Field::getConfigurable( $fields );

		$this->assertEquals( get_class( $result[0] ), 'CompleteOpenGraph\Field' );
	}

}
