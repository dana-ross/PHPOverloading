<?php

class TestOverloadedMethods extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers \DaveRoss\PHPOverloading\OverloadedMethods::_overridden_impl_fn_name
	 */
	function test_constructor_name() {

		// Valid methods
		$this->assertEquals( '__construct_string', TestOverloadedMethodsA::_overridden_impl_fn_name( '__construct', array( 'test' ) ) );
		$this->assertEquals( '__construct_integer', TestOverloadedMethodsA::_overridden_impl_fn_name( '__construct', array( 1 ) ) );
		$this->assertEquals( '__construct_double', TestOverloadedMethodsA::_overridden_impl_fn_name( '__construct', array( 1.0 ) ) );

		// Non-existent methods
		$this->assertEquals( false, TestOverloadedMethodsA::_overridden_impl_fn_name( '__construct', array(
			array(
				1,
				2,
				3
			)
		) ) );
		$this->assertEquals( false, TestOverloadedMethodsA::_overridden_impl_fn_name( '__construct', array( new stdClass() ) ) );

		$this->assertEquals( false, TestOverloadedMethodsA::_overridden_impl_fn_name( '__construct', array(
			'test',
			5
		) ) );
		$this->assertEquals( false, TestOverloadedMethodsA::_overridden_impl_fn_name( '__construct', array(
			'test',
			new stdClass()
		) ) );

	}

	/**
	 * @covers \DaveRoss\PHPOverloading\OverloadedMethods::__construct
	 */
	function test_string_constructor() {
		$instance = new TestOverloadedMethodsA( 'test' );
		$this->assertEquals( 'test', $instance->string );
	}

	/**
	 * @covers \DaveRoss\PHPOverloading\OverloadedMethods::__construct
	 */
	function test_integer_constructor() {
		$instance = new TestOverloadedMethodsA( 5 );
		$this->assertEquals( 5, $instance->integer );
	}

	/**
	 * @covers \DaveRoss\PHPOverloading\OverloadedMethods::__construct
	 */
	function test_double_constructor() {
		$instance = new TestOverloadedMethodsA( 5.0 );
		$this->assertEquals( 5.0, $instance->double );
	}

	/**
	 * @covers \DaveRoss\PHPOverloading\OverloadedMethods::__construct
	 */
	function test_invalid_constructor() {
		$this->setExpectedException( 'RuntimeException' );
		new TestOverloadedMethodsA( array() );
	}

	/**
	 * @covers \DaveRoss\PHPOverloading\OverloadedMethods::__construct
	 */
	function test_invalid_method() {
		$this->setExpectedException('RuntimeException');
		$testOverloadedMethodsA = new TestOverloadedMethodsA(5.0);
		$testOverloadedMethodsA->example(5.0);
	}

	/**
	 * @covers \DaveRoss\PHPOverloading\OverloadedMethods::__call
	 */
	function test_array_method() {
		$testOverloadedMethodsA = new TestOverloadedMethodsA(5.0);
		$testOverloadedMethodsA->example(array(5.0));
		$this->assertInternalType ('array', $testOverloadedMethodsA->array);
	}

	/**
	 * @covers \DaveRoss\PHPOverloading\OverloadedMethods::_overridden_impl_fn_name
	 */
	function test_overriden_impl_fn_name() {
		$this->assertEquals( false, TestOverloadedMethodsA::_overridden_impl_fn_name( 'example', array(
			5.5,
			5
		) ) );
		$this->assertEquals( false, TestOverloadedMethodsA::_overridden_impl_fn_name( 'example', array(
			new TestOverloadedMethodsA( 'test' ),
			5
		) ) );
	}

}

/**
 * Class TestOverloadedMethodsA
 * Test rig for TestOverloadedMethods
 */
class TestOverloadedMethodsA {

	use \DaveRoss\PHPOverloading\OverloadedMethods;

	public $double;
	public $string;
	public $integer;
	public $array;
	public $stdclass;

	public function __construct_string( $string ) {
		$this->string = $string;
	}

	public function __construct_integer( $integer ) {
		$this->integer = $integer;
	}

	public function __construct_double( $double ) {
		$this->double = $double;
	}

	public function example_array($array) {
		$this->array = $array;
	}

	public function example_stdclass(stdClass $stdclass) {
		$this->stdclass = $stdclass;
	}

}