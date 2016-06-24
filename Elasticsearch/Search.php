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

use Luki\Elasticsearch;

/**
 * Elasticsearch Sesarch
 * 
 * @package Luki
 */
class Search
{

    private $_url;
    private $_text;
    private $_searchQuery;
    private $_finalQuery;
    private $_found = array();
    private $_level = 0;
    private $_type;
    
    public function __construct($url)
    {
        $this->_url = $url;
        unset($url);
    }
    
    public function setText($text)
    {
        $this->_text = (string)$text;
        $this->_searchQuery = array(
          'query' => array(
            'query_string' => array(
              'query' => $this->_text,
              'default_field' => 'content',
              'default_operator' => 'AND'
            )
          )
        );
        
        unset($text);
        return $this;
    }
    
    public function setType($type)
    {
        $this->_type = $type;
        
        if(!empty($type)) {
            $this->_url .= $type . '/';
        }
        
        unset($type);
        return $this;
    }
    
    public function search()
    {
        $this->_level++;
        
        if($this->_level < 4) {
            $this->_prepareFinalQuery();
           
            $result = Elasticsearch::sendToServer('GET', $this->_url . '_search?size=20', $this->_finalQuery);

            $this->_found['total'] = $result->hits->total;
            if($result->hits->total > 0) {
                $this->_extractHits($result);            
            }
            else {
                $this->_searchSuggest();
            }
        }
        
        unset($result);
        return $this->_found;
    }
    
    private function _prepareFinalQuery()
    {
        $this->_finalQuery = json_encode($this->_searchQuery);
    }
    
    private function _extractHits($result)
    {
        foreach($result->hits->hits as $hit) {
            $this->_found['hits'][] = $hit;
        }
        
        unset($result, $hit);
    }
    
    private function _searchSuggest()
    {
        $this->_searchQuery = array(
          'suggest' => array(
            'my_suggestions' => array(
              'text' => $this->_text,
              'term' => array(
                'size' => 1,
                'field' => 'content',
                'sort' => 'score',
                'suggest_mode' => 'popular'
              )
            )
          )
        );
        
        $result = Elasticsearch::sendToServer('GET', $this->_url . '_search?search_type=count', json_encode($this->_searchQuery));
        
        $this->setText($result->suggest->my_suggestions[0]->options[0]->text);
        $this->_found['suggest'] = $this->_text;
        $this->search();
    }
}

# End of file