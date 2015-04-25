<?php

/**
 * Elasticsearch class
 *
 * Luki framework
 * Date 6.9.2014
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

use Luki\Elasticsearch\Record;
use Luki\Elasticsearch\Search;

/**
 * Elasticsearch class
 *
 * Use Elasticsearch for search data
 *
 * @package Luki
 */
class Elasticsearch
{
    private $_server = 'http://localhost';
    public static $port = '9200';
    private $_url = 'http://localhost:9200/';
    private $_index;
    
    public function setServer($server)
    {
        $this->_server = $server;
        $this->_generateUrl();
        
        unset($server);
        return $this;
    }
    
    public function getServer()
    {
        return $this->_server;
    }
    
    public function setPort($port)
    {
        $this->_port = $port;
        $this->_generateUrl();
        
        unset($port);
        return $this;
    }
    
    public function getPort()
    {
        return $this->_port;
    }
    
    public function getUrl()
    {
        return $this->_url;
    }
    
    public function setIndex($index)
    {
        $this->_index = $index;
        $this->_generateUrl();
        
        unset($index);
        return $this;
    }
    
    public function getIndex()
    {
        return $this->_index;
    }
    
    public function insert(Record $record)
    {
        $link = $this->_url . $record->getType() . '/' . $record->getId();
        $content = json_encode($record->getContent());        
#        self::sendToServer('PUT', $link, $content);

        unset($record, $link, $content);
        return $this;
    }
    
    public function search($text, $type)
    {
        $search = new Search($this->_url);
        $hits = $search->setText($text)
                       ->setType($type)
                       ->search();
        
        unset($text, $type, $search);
        return $hits;
    }
    
    private function _generateUrl()
    {
        $this->_url = $this->_server;
        
        if(!empty($this->_port)) {
            $this->_url .= ':' . $this->_port;
        }

        if(!empty($this->_index)) {
            $this->_url .= '/' . $this->_index;
        }

        $this->_url .= '/';
    }
    
    public static function sendToServer($type, $url, $query) 
    {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_PORT, self::$port);
        curl_setopt($ci, CURLOPT_TIMEOUT, 200);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ci, CURLOPT_POSTFIELDS, $query);
        $response = json_decode(curl_exec($ci));

        unset($type, $url, $query, $ci);
        return $response;
    }
}

# End of file