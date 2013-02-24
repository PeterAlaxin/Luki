<?php

require_once '../Cache.php';
require_once('../Cache/memoryAdapter.php');

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-02-24 at 14:20:37.
 */
class Luki_CacheTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Luki_Cache
	 */
	protected $object;
	
	protected $adapter;

	protected $key = 'savedKey';
	
	protected $value = '1234567890';
	
	protected $expiration = 100;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->adapter = new Luki_Cache_memoryAdapter;
		$this->object = new Luki_Cache($this->adapter);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		
	}

	/**
	 * @covers Luki_Cache::setExpiration
	 * @covers Luki_Cache::__construct
	 */
	public function testSetExpiration()
	{
		$this->assertFalse($this->object->setExpiration('abc'));
		$this->assertTrue($this->object->setExpiration($this->expiration));
	}

	/**
	 * @covers Luki_Cache::getExpiration
	 */
	public function testGetExpiration()
	{
		$this->object->setExpiration($this->expiration);
		$this->assertEquals($this->object->getExpiration(), $this->expiration);
	}

	/**
	 * @covers Luki_Cache::Set
	 */
	public function testSet()
	{
		$this->assertTrue($this->object->Set($this->key, $this->value));
		
		$this->assertTrue($this->object->Set(array($this->key => $this->value)));
	}

	/**
	 * @covers Luki_Cache::Get
	 * @todo   Implement testGet().
	 */
	public function testGet()
	{
		$this->assertFalse($this->object->Get('abc'));
		
		$this->assertEquals($this->object->Get($this->key), $this->value);
	}

	/**
	 * @covers Luki_Cache::Delete
	 * @todo   Implement testDelete().
	 */
	public function testDelete()
	{
		$this->assertFalse($this->object->Delete('abc'));
		
		$this->assertTrue($this->object->Delete($this->key));
	}

}
