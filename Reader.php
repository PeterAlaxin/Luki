<?php

/**
 * Reader class
 *
 * Luki framework
 * Date 23.6.2016
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * Reader class
 *
 * Read sources
 * 
 * @package Luki
 */
class Reader
{

    private $file = NULL;
    private $adapter = NULL;

    public function __construct($file)
    {
        $this->file = $file;
        $this->findAdapter();

        unset($file);
    }

    private function findAdapter()
    {
        $extension = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
        $name = __NAMESPACE__ . '\Reader\\' . $extension . 'Adapter';
        $this->adapter = new $name($this->file);

        unset($extension, $name);
    }

    public function getFilesize()
    {
        $filesize = $this->adapter->getFilesize();

        return $filesize;
    }

    public function setBlocksize($newSize)
    {
        $this->adapter->setBlocksize($newSize);

        unset($newSize);
        return $this;
    }

    public function getBlocksize()
    {
        $blockSize = $this->adapter->getBlocksize();

        return $blockSize;
    }

    public function getReadBytes()
    {
        $readBites = $this->adapter->getReadBytes();

        return $readBites;
    }

    public function getCounter()
    {
        $counter = $this->adapter->getCounter();

        return $counter;
    }

    public function getBlocks()
    {
        $counter = $this->adapter->getBlocks();

        return $counter;
    }

    public function getItem()
    {
        $item = $this->adapter->getItem();

        return $item;
    }

    public function getPercent()
    {
        $all = $this->getFilesize();
        $read = $this->getReadBytes();
        $percent = round(($read / $all) * 10000) / 100;

        unset($all, $read);
        return $percent;
    }

}
