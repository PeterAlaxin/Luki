<?php
/**
 * Reader class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Reader
 * @filesource
 */

namespace Luki;

class Reader
{

    private $file = null;
    private $adapter = null;

    public function __construct($file)
    {
        $this->file = $file;
        $this->findAdapter();
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    private function findAdapter()
    {
        $extension = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
        $name = __NAMESPACE__ . '\Reader\\' . ucfirst($extension) . 'Adapter';
        $this->adapter = new $name($this->file);
    }

    public function getFilesize()
    {
        $filesize = $this->adapter->getFilesize();

        return $filesize;
    }

    public function setBlocksize($newSize)
    {
        $this->adapter->setBlocksize($newSize);

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

        return $percent;
    }
}
