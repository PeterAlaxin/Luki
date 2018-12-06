<?php
/**
 * Elasticsearch search
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

use Luki\Elasticsearch;

class Search
{
    private $url;
    private $text;
    private $searchQuery;
    private $finalQuery;
    private $found = array();
    private $level = 0;
    private $type;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function setText($text)
    {
        $this->text        = (string) $text;
        $this->searchQuery = array(
            'query' => array(
                'query_string' => array(
                    'query'            => $this->text,
                    'default_field'    => 'content',
                    'default_operator' => 'AND'
                )
            )
        );

        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;

        if (!empty($type)) {
            $this->url .= $type.'/';
        }

        return $this;
    }

    public function search()
    {
        $this->level++;

        if ($this->level < 4) {
            $this->prepareFinalQuery();

            $result = Elasticsearch::sendToServer('GET', $this->url.'_search?size=20', $this->finalQuery);

            $this->found['total'] = $result->hits->total;
            if ($result->hits->total > 0) {
                $this->extractHits($result);
            } else {
                $this->searchSuggest();
            }
        }

        return $this->found;
    }

    private function prepareFinalQuery()
    {
        $this->finalQuery = json_encode($this->searchQuery);
    }

    private function extractHits($result)
    {
        foreach ($result->hits->hits as $hit) {
            $this->found['hits'][] = $hit;
        }
    }

    private function searchSuggest()
    {
        $this->searchQuery = array(
            'suggest' => array(
                'my_suggestions' => array(
                    'text' => $this->text,
                    'term' => array(
                        'size'         => 1,
                        'field'        => 'content',
                        'sort'         => 'score',
                        'suggest_mode' => 'popular'
                    )
                )
            )
        );

        $result = Elasticsearch::sendToServer('GET', $this->url.'_search?search_type=count',
                json_encode($this->searchQuery));

        $this->setText($result->suggest->my_suggestions[0]->options[0]->text);
        $this->found['suggest'] = $this->text;
        $this->search();
    }
}