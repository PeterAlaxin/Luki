<?php
/**
 * Model class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Model
 * @filesource
 */

namespace Luki;

use Luki\Data;
use Luki\Data\BasicInterface;
use Luki\Entity;
use Luki\Storage;
use Luki\Url;

abstract class Model
{

    public $data = array();

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function addData($name, BasicInterface $adapter)
    {
        $this->data[$name] = new Data($adapter);

        return $this;
    }

    public function getData($name)
    {
        $adapter = null;

        if (isset($this->data[$name])) {
            $adapter = $this->data[$name];
        }

        return $adapter;
    }

    public function getAdapter($options)
    {
        if (!empty($options['adapter'])) {
            $adapterName = $options['adapter'] . 'Adapter';
            $adapter = new $adapterName($options);
        } else {
            $adapter = false;
        }

        return $adapter;
    }

    public function getFromCache($name = '')
    {
        if (Storage::isCache() and Storage::Cache()->isUsedCache()) {
            $name = $this->getCacheName($name);
            $cache = Storage::Cache()->Get($name);
        } else {
            $cache = null;
        }

        return $cache;
    }

    public function setToCache($content, $name = '', $expiration = 3600)
    {
        if (Storage::isCache()) {
            $name = $this->getCacheName($name);
            Storage::Cache()->Set($name, $content, $expiration);
        }

        return $this;
    }

    public function getEntity($table, BasicInterface $adapter)
    {
        $entityName = $table . 'Entity';
        $entityFile = Storage::dirEntity() . '/' . $entityName . '.php';
        if (!is_file($entityFile)) {
            $newEntity = new Entity($table);
            $newEntity->setData($adapter)
                ->setFile($entityFile)
                ->createEntity();
        }

        require_once($entityFile);
        $entity = new $entityName($adapter);

        return $entity;
    }

    private function getCacheName($name)
    {
        $callers = debug_backtrace();
        $newName = $callers[2]['class'] . '_' . $callers[2]['function'];

        if (!empty($name)) {
            $newName .= '_' . $name;
        } elseif (!empty($callers[2]['args'])) {
            $newName .= '_' . Url::makeLink(implode('_', $callers[2]['args']), false);
        }

        return $newName;
    }
}
