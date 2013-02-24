<?php
require_once '../File.php';

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-23 at 12:58:51.
 */
class Luki_FileTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Luki_File
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new Luki_File;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		
	}

	/**
	 * Generated from @assert (__FILE__) == 'text/x-php'.
	 *
	 * @covers Luki_File::getMimeType
	 */
	public function testGetMimeType()
	{
		$this->assertEquals(
			'text/x-c++', Luki_File::getMimeType(__FILE__)
		);
	}

	/**
	 * Generated from @assert ('abc') == NULL.
	 *
	 * @covers Luki_File::getMimeType
	 */
	public function testGetMimeType2()
	{
		$this->assertEquals(
			NULL, Luki_File::getMimeType('abc')
		);
	}

	/**
	 * Generated from @assert () == NULL.
	 *
	 * @covers Luki_File::getMimeType
	 */
	public function testGetMimeType3()
	{
		$this->assertEquals(
			NULL, Luki_File::getMimeType()
		);
	}

}
