<?php
/**
 * Elasticsearch class
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

namespace Luki;

use Luki\Elasticsearch\Record;
use Luki\Elasticsearch\Search;

class Elasticsearch
{
    private $server     = 'http://localhost';
    public static $port = '9200';
    private $url        = 'http://localhost:9200/';
    private $index;

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function setServer($server)
    {
        $this->server = $server;
        $this->generateUrl();

        return $this;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function setPort($port)
    {
        $this->port = $port;
        $this->generateUrl();

        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setIndex($index)
    {
        $this->index = $index;
        $this->generateUrl();

        return $this;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function insert(Record $record)
    {
        $link    = $this->url.$record->getType().'/'.$record->getId();
        $content = json_encode($record->getContent());
        self::sendToServer('PUT', $link, $content);

        return $this;
    }

    public function search($text, $type)
    {
        $search = new Search($this->url);
        $hits   = $search->setText($text)
            ->setType($type)
            ->search();

        return $hits;
    }

    private function generateUrl()
    {
        $this->url = $this->server;

        if (!empty($this->port)) {
            $this->url .= ':'.$this->port;
        }

        if (!empty($this->index)) {
            $this->url .= '/'.$this->index;
        }

        $this->url .= '/';
    }

    public static function sendToServer($type, $url, $query)
    {
        $ci       = curl_init();
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_PORT, self::$port);
        curl_setopt($ci, CURLOPT_TIMEOUT, 200);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ci, CURLOPT_POSTFIELDS, $query);
        $response = json_decode(curl_exec($ci));

        return $response;
    }
}