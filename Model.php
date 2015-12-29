<?php

/**
 * Model class
 *
 * Luki framework
 * Date 6.1.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

use Luki\Data;
use Luki\Data\basicInterface;
use Luki\Storage;
use Luki\Url;
use Luki\Entity;

/**
 * Model class
 *
 * @package Luki
 */
abstract class Model
{

    public $data = array();
    
    public function addData($name, basicInterface $dataAdapter)
    {
        $this->data[$name] = new Data($dataAdapter);

        unset($name, $dataAdapter);
        return $this;
    }

    public function getData($name)
    {
        $dataAdapter = NULL;

        if ( isset($this->data[$name]) ) {
            $dataAdapter = $this->data[$name];
        }

        unset($name);
        return $dataAdapter;
    }

    public function getAdapter($options)
    {
        $dataAdapter = FALSE;

        if ( !empty($options['adapter']) ) {
            $adapterName = $options['adapter'] . 'Adapter';
            $dataAdapter = new $adapterName($options);
        }

        unset($options, $adapterName);
        return $dataAdapter;
    }

    public function getFromCache($name = '')
    {
        $cache = FALSE;

        if ( Storage::isCache() and Storage::Cache()->isUsedCache() ) {
            $name = $this->_getCacheName($name);
            $cache = Storage::Cache()->Get($name);
        }

        unset($name);
        return $cache;
    }

    public function setToCache($content, $name = '', $expiration = 3600)
    {
        if ( Storage::isCache() ) {
            $name = $this->_getCacheName($name);
            Storage::Cache()->Set($name, $content, $expiration);
        }

        unset($content, $name, $expiration);
        return $this;
    }

    public function getEntity($table, basicInterface $dataAdapter)
    {
        $entityName = $table . 'Entity';
        $entityFile = Storage::dirEntity() . '/' . $entityName . '.php';
        if(!is_file($entityFile)) {
            $newEntity = new Entity($table);
            $newEntity->setData($dataAdapter)
                      ->setFile($entityFile)
                      ->createEntity();
        }
        
        require_once($entityFile);
        $entity = new $entityName($dataAdapter);
        
        unset($table, $dataAdapter, $entityName, $entityFile, $newEntity);
        return $entity;
    }
    
    private function _getCacheName($name)
    {
        $callers = debug_backtrace();
        $newName = $callers[2]['class'] . '_' . $callers[2]['function'];

        if ( !empty($name) ) {
            $newName .= '_' . $name;
        } elseif ( !empty($callers[2]['args']) ) {
            $newName .= '_' . Url::makeLink(implode('_', $callers[2]['args']), FALSE);
        }

        unset($callers, $name);
        return $newName;
    }    
}

# End of file