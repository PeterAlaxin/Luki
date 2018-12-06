<?php
/**
 * Elasticsearch record
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Elasticsearch
 * @filesource
 */

namespace Luki\Elasticsearch;

class Record
{
    private $id;
    private $type;
    private $content = array();

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        $this->type = (string) $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setContent($content)
    {
        foreach ((array) $content as $key => $value) {
            $this->addContent($key, $value);
        }

        return $this;
    }

    public function addContent($key, $value)
    {
        $this->content[(string) $key] = str_replace("'", '"', (string) $value);

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }
}