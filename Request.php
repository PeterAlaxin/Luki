<?php

/**
 * Request class
 *
 * Luki framework
 * Date 16.12.2012
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

use Luki\Request\cookieAdapter;
use Luki\Request\filesAdapter;
use Luki\Request\getAdapter;
use Luki\Request\postAdapter;
use Luki\Request\serverAdapter;

/**
 * Request class
 *
 * @package Luki
 */
class Request
{

    public $cookie;
    public $files;
    public $get;
    public $post;
    public $server;
    
    private $_ajax = NULL;
    private $_baseUrl = NULL;
    private $_clientIP = NULL;
    private $_crumb = NULL;
    private $_crumbCount = NULL;
    private $_fullUrl = NULL;
    private $_httpHost = NULL;
    private $_httpUserAgent = NULL;
    private $_languages = NULL;
    private $_pathInfo = NULL;
    private $_protocol = NULL;
    private $_queryString = NULL;
    private $_redirectStatus = NULL;
    private $_requestMethod = NULL;
    private $_requestTime = NULL;
    private $_requestUri = NULL;
    private $_safe = NULL;
    private $_scriptName = NULL;
    private $_serverName = NULL;
    private $_shortUrl = NULL;
    private $_url = NULL;

    public function __construct()
    {
        $this->cookie = new cookieAdapter();
        $this->files = new filesAdapter();
        $this->get = new getAdapter();
        $this->post = new postAdapter();
        $this->server = new serverAdapter();
    }

    public function getRequestTime()
    {
        if ( is_null($this->_requestTime) ) {
            $this->_requestTime = $this->server->get('REQUEST_TIME');
        }

        return $this->_requestTime;
    }

    public function getRequestUri()
    {
        if ( is_null($this->_requestUri) ) {
            $this->_requestUri = $this->server->get('REQUEST_URI');
        }

        return $this->_requestUri;
    }

    public function getRequestMethod()
    {
        if ( is_null($this->_requestMethod) ) {
            $this->_requestMethod = $this->server->get('REQUEST_METHOD');
        }

        return $this->_requestMethod;
    }

    public function getClientIP()
    {
        if ( is_null($this->_clientIP) ) {
            $this->_clientIP = $this->server->get('REMOTE_ADDR');
        }

        return $this->_clientIP;
    }

    public function getScriptName()
    {
        if ( is_null($this->_scriptName) ) {
            $this->_scriptName = $this->server->get('SCRIPT_NAME');
        }

        return $this->_scriptName;
    }

    public function getPathInfo()
    {
        if ( is_null($this->_pathInfo) ) {
            $this->_pathInfo = $this->server->get('PATH_INFO');
        }

        return $this->_pathInfo;
    }

    public function getRedirectStatus()
    {
        if ( is_null($this->_redirectStatus) ) {
            $this->_redirectStatus = $this->server->get('REDIRECT_STATUS');
        }

        return $this->_redirectStatus;
    }

    public function getHost()
    {
        if ( is_null($this->_httpHost) ) {
            $this->_httpHost = $this->server->get('HTTP_HOST');
        }

        return $this->_httpHost;
    }

    public function getUserAgent()
    {
        if ( is_null($this->_httpUserAgent) ) {
            $this->_httpUserAgent = $this->server->get('HTTP_USER_AGENT');
        }

        return $this->_httpUserAgent;
    }

    public function getLanguages()
    {
        if ( is_null($this->_languages) ) {
            $languages = explode(',', $this->server->get('HTTP_ACCEPT_LANGUAGE'));

            foreach ( $languages as $language ) {
                $languageExploded = explode(';', $language);
                $this->_languages[] = $languageExploded[0];
            }
        }

        unset($language, $languageExploded, $languages);
        return $this->_languages;
    }

    public function getProtocol()
    {
        if ( is_null($this->_protocol) ) {
            $this->_protocol = 'http';

            if ( $this->isSafe() ) {
                $this->_protocol .= 's';
            }

            $this->_protocol .= '://';
        }

        return $this->_protocol;
    }

    public function getServerName()
    {
        if ( is_null($this->_serverName) ) {
            $this->_serverName = $this->server->get('SERVER_NAME');
        }

        return $this->_serverName;
    }

    public function getQueryString()
    {
        if ( is_null($this->_queryString) ) {
            $this->_queryString = $this->server->get('QUERY_STRING');
        }

        return $this->_queryString;
    }

    public function getBaseUrl()
    {
        if ( is_null($this->_baseUrl) ) {
            $file = '/' . basename($this->getScriptName()) . '/';
            $this->_baseUrl = ltrim(preg_replace($file, '', $this->_scriptName), '/');
        }

        unset($file);
        return $this->_baseUrl;
    }

    public function getFullUrl()
    {
        if ( is_null($this->_fullUrl) ) {
            $this->_fullUrl = $this->getShortUrl() .
                    $this->getURL();
        }

        return $this->_fullUrl;
    }

    public function getShortUrl()
    {
        if ( is_null($this->_shortUrl) ) {
            $this->_shortUrl = $this->getProtocol() .
                    $this->getHost() . '/' .
                    $this->getBaseUrl();
        }

        return $this->_shortUrl;
    }

    public function getURL()
    {
        if ( is_null($this->_url) ) {
            $from = array( '?' . $this->getQueryString() );
            $this->getBaseUrl();
            if ( !empty($this->_baseUrl) ) {
                $from[] = '/' . $this->_baseUrl;
            }
            $this->_url = urldecode($this->getRequestUri());

            foreach ( $from as $item ) {
                $this->_url = str_replace($item, '', $this->_url);
            }

            if ( '/' == substr($this->_url, 0, 1) ) {
                $this->_url = substr($this->_url, 1);
            }
        }

        unset($from, $item);
        return $this->_url;
    }

    public function getCrumb($index = NULL)
    {
        if ( is_null($this->_crumb) ) {
            $this->_crumb = explode('/', $this->getURL());
        }

        $crumb = NULL;

        if ( is_null($index) ) {
            $crumb = $this->_crumb;
        } else {
            $index = (int) $index;
            if ( !empty($this->_crumb[$index]) ) {
                $crumb = $this->_crumb[$index];
            }
        }

        unset($index);
        return $crumb;
    }

    public function getCrumbCount()
    {
        if ( is_null($this->_crumbCount) ) {
            $this->_crumbCount = count($this->getCrumb());
        }

        return $this->_crumbCount;
    }

    public function isAjax()
    {
        if ( is_null($this->_ajax) ) {
            if ( strtolower($this->server->get('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest' ) {
                $this->_ajax = TRUE;
            } else {
                $this->_ajax = FALSE;
            }
        }

        return $this->_ajax;
    }

    public function isSafe()
    {
        if ( is_null($this->_safe) ) {
            if ( $this->server->get('HTTPS') === NULL ) {
                $this->_safe = FALSE;
            } else {
                $this->_safe = TRUE;
            }
        }

        return $this->_safe;
    }

}

# End of file