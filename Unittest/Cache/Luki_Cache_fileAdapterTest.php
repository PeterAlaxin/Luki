<?php
require_once('/var/projects/Luki/Cache/fileAdapter.php');

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-02-24 at 13:34:46.
 */
class Luki_Cache_fileAdapterTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Luki_Cache_fileAdapter
	 */
	protected $object;

	protected $key = 'savedKey';
	
	protected $value = '1234567890';
	
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new Luki_Cache_fileAdapter;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		
	}

	/**
	 * @covers Luki_Cache_fileAdapter::Set
	 * @covers Luki_Cache_fileAdapter::__construct
	 */
	public function testSet()
	{
		$this->assertTrue($this->object->Set($this->key, $this->value));
		
		$this->assertFileExists('/tmp/' . $this->key);
	}

	/**
	 * @covers Luki_Cache_fileAdapter::Get
	 */
	public function testGet()
	{
		$this->assertFalse($this->object->Get('abc'));
		
		$this->assertEquals($this->object->Get($this->key), $this->value);
	}

	/**
	 * @covers Luki_Cache_fileAdapter::Delete
	 */
	public function testDelete()
	{
		$this->assertFalse($this->object->Delete('abc'));
		
		$this->assertTrue($this->object->Delete($this->key));
	}

}
