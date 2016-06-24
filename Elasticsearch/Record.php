<?php

/**
 * Elasticsearch record
 *
 * Luki framework
 * Date 6.9.2014
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

namespace Luki\Elasticsearch;

/**
 * Elasticsearch Record
 * 
 * @package Luki
 */
class Record
{

    private $_id;
    private $_type;
    private $_content = array();
    
    public function setId($id)
    {
        $this->_id = (int)$id;
        
        unset($id);
        return $this;
    }
    
    public function getId()
    {
        return $this->_id;
    }

    public function setType($type)
    {
        $this->_type = (string)$type;
        
        unset($type);
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setContent($content)
    {
        foreach((array)$content as $key => $value){
            $this->addContent($key, $value);
        }
        
        unset($content, $key, $value);
        return $this;
    }
    
    public function addContent($key, $value)
    {
        $this->_content[(string)$key] = str_replace("'", '"', (string)$value);
        
        unset($key, $value);
        return $this;
    }
    
    public function getContent()
    {
        return $this->_content;
    }
}

# End of file