<?php

/**
 * XML adapter
 *
 * Luki framework
 * Date 9.12.2012
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

namespace Luki\Reader;

/**
 * XML data adapter
 * 
 * @package Luki
 */
class xmlAdapter
{

    private $handle;
    private $totalBytes;
    private $readBytes = 0;
    private $blockSize = 8192;
    private $itemCounter = 0;
    private $block;
    private $blockCounter = 0;
    private $root = NULL;
    private $start;
    private $nodes;
    private $node = NULL;
    private $element;
    private $minPosition;
    private $elementName;
    private $endTag;
    private $endTagPosition;

    public function __construct($url)
    {
        $this->open($url);

        unset($url);
    }

    public function __destruct()
    {
        if ( $this->handle ) {
            fclose($this->handle);
        }
    }

    private function open($url)
    {
        $this->handle = fopen($url, 'r');

        if ( $this->handle ) {
            $this->setFilesize($url);

            if ( $this->totalBytes < $this->blockSize ) {
                $this->blockSize = $this->totalBytes;
            }
        }

        unset($url);
    }

    private function setFilesize($url)
    {
        $isRemote = empty(@filectime($url));

        if ( $isRemote ) {
            $headers = get_headers($url, 1);
            $this->totalBytes = $headers['Content-Length'];
        } else {
            $this->totalBytes = filesize($url);
        }

        unset($url, $isRemote, $headers);
    }

    public function getFilesize()
    {
        return $this->totalBytes;
    }

    public function setBlocksize($newSize)
    {
        $oldSize = $this->blockSize;
        $this->blockSize = (int) $newSize;

        if ( empty($this->blockSize) ) {
            $this->blockSize = $oldSize;
        }

        unset($newSize, $oldSize);
    }

    public function getBlocksize()
    {
        return $this->blockSize;
    }

    public function getReadBytes()
    {
        return $this->readBytes;
    }

    public function getCounter()
    {
        return $this->itemCounter;
    }

    public function getBlocks()
    {
        return $this->blockCounter;
    }

    public function getItem()
    {
        while ( TRUE ) {
            if ( $this->blockCounter == 0 ) {
                $this->readBlock();
            }

            if ( !isset($this->root) ) {
                $this->findRoot();
            }

            $this->findNode();

            if ( !is_null($this->node) ) {
                break;
            } else {
                $this->readBlock();
            }
        }

        $node = simplexml_load_string($this->node);

        return $node;
    }

    private function readBlock()
    {
        $this->block .= fread($this->handle, $this->blockSize);

        preg_match_all('/<!-- (.*) -->/U', $this->block, $matches);
        foreach ( $matches[0] as $match ) {
            $this->block = str_replace($match . "\n", '', $this->block);
        }

        $this->readBytes += $this->blockSize;
        if ( $this->readBytes >= $this->totalBytes ) {
            $this->readBytes = $this->totalBytes;
        }
        $this->blockCounter++;

        unset($matches, $match);
    }

    private function findRoot()
    {
        preg_match('/<([^>\?]+)>/', $this->block, $matches);

        if ( isset($matches[1]) ) {
            // Found root node
            $this->root = $matches[1];
            $this->start = strpos($this->block, $matches[0]) + strlen($matches[0]);
        }

        unset($matches);
    }

    private function findNode()
    {
        $this->nodes = substr($this->block, $this->start);
        preg_match('/<([^>]+)>/', $this->nodes, $matches);

        if ( isset($matches[1]) ) {
            $this->element = $matches[1];
            $this->findMinPosition();
            $this->findElemendName();
            $this->findEndTagPosition();
            $this->setNode();
        } else {
            $this->node = NULL;
        }

        unset($matches);
    }

    private function findMinPosition()
    {
        $found = array();
        $positions = array(
          strpos($this->element, " "),
          strpos($this->element, "\r"),
          strpos($this->element, "\n"),
          strpos($this->element, "\t") );

        foreach ( $positions as $position ) {
            if ( $position !== false ) {
                $found[] = $position;
            }
        }

        if ( $found === array() ) {
            $this->minPosition = FALSE;
        } else {
            $this->minPosition = min($found);
        }

        unset($positions, $position, $found);
    }

    private function findElemendName()
    {
        if ( $this->minPosition !== FALSE && $this->minPosition != 0 ) {
            $this->elementName = substr($this->element, 0, $this->minPosition);
        } else {
            $this->elementName = $this->element;
        }
    }

    private function findEndTagPosition()
    {
        $this->endTag = "</" . $this->elementName . ">";
        $this->endTagPosition = FALSE;

        $lastCharPosition = strlen($this->element) - 1;
        if ( substr($this->element, $lastCharPosition) == "/" ) {
            $this->endTag = "/>";
            $this->endTagPosition = $lastCharPosition;

            $position = strpos($this->nodes, "<");
            if ( $position !== FALSE ) {
                $this->endTagPosition += $position + 1;
            }
        }

        if ( $this->endTagPosition === FALSE ) {
            $this->endTagPosition = strpos($this->nodes, $this->endTag);
        }

        unset($lastCharPosition);
    }

    private function setNode()
    {
        if ( $this->endTagPosition !== FALSE ) {
            $this->node = trim(substr($this->nodes, 0, $this->endTagPosition + strlen($this->endTag)));
            $this->block = substr($this->block, strpos($this->block, $this->endTag) + strlen($this->endTag));
            $this->start = 0;
            $this->itemCounter++;
        } else {
            if ( $this->totalBytes == $this->readBytes ) {
                $this->node = FALSE;
            } else {
                $this->node = NULL;
            }
        }
    }

}
