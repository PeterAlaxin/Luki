<?php

require_once('/var/projects/Luki/Config/xmlAdapter.php');

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-02-23 at 19:18:57.
 */
class Luki_Config_xmlAdapterTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Luki_Config_xmlAdapter
	 */
	protected $object;
	
	protected $file = '/var/projects/demo/data/config/config.xml';
	
	protected $fileTemp = '/var/projects/demo/data/config/configTemp.xml';
	
	protected $configuration;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new Luki_Config_xmlAdapter($this->file);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		if(is_file($this->fileTemp)) {
			unlink($this->fileTemp);
		}
	}

	/**
	 * @covers Luki_Config_xmlAdapter::getConfiguration
	 * @todo   Implement testGetConfiguration().
	 */
	public function testGetConfiguration()
	{
		$this->configuration = $this->object->getConfiguration();
		$this->assertEquals(is_array($this->configuration), TRUE);
	}

	/**
	 * @covers Luki_Config_xmlAdapter::saveConfiguration
	 * @todo   Implement testSaveConfiguration().
	 */
	public function testSaveConfiguration()
	{
		$bSet = $this->object->setFilename($this->fileTemp);
		$bSaved = FALSE;
		if($bSet) {
			$bSaved = $this->object->saveConfiguration();
		}

		$this->assertEquals($bSaved, TRUE);
	}

	/**
	 * @covers Luki_Config_xmlAdapter::getFilename
	 * @todo   Implement testGetFilename().
	 */
	public function testGetFilename()
	{
		$this->assertEquals($this->object->getFilename(), $this->file);
	}

	/**
	 * @covers Luki_Config_xmlAdapter::setConfiguration
	 * @covers Luki_Config_xmlAdapter::getConfiguration
	 * @todo   Implement testSetConfiguration().
	 */
	public function testSetConfiguration()
	{
		$bSet = $this->object->setConfiguration($this->object->getConfiguration());
		
		$this->assertEquals($bSet, TRUE);
	}

	/**
	 * @covers Luki_Config_xmlAdapter::setFilename
	 * @covers Luki_Config_xmlAdapter::__construct
	 * @todo   Implement testSetFilename().
	 */
	public function testSetFilename()
	{
		$bSet = $this->object->setFilename('');
		
		$this->assertEquals($bSet, FALSE);
	}

	/**
	 * @covers Luki_Config_xmlAdapter::setFilename
	 * @todo   Implement testSetFilename().
	 */
	public function testSetFilename2()
	{
		$bSet = $this->object->setFilename($this->file);
		
		$this->assertEquals($bSet, TRUE);
	}

}
