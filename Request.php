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
class Request {

    public $cookie; 
    public $files; 
    public $get; 
    public $post;
    public $server;
    
    private $ajax = NULL;
    private $baseUrl = NULL;
    private $clientIP = NULL;
    private $crumb = NULL;
    private $crumbCount = NULL;
    private $fullUrl = NULL;
    private $httpHost = NULL;
    private $httpUserAgent = NULL;
    private $languages = NULL;
    private $pathInfo = NULL;
    private $protocol = NULL;
    private $queryString = NULL;
    private $redirectStatus = NULL;
    private $requestMethod = NULL;
    private $requestTime = NULL;
    private $requestUri = NULL;
    private $safe = NULL;    
    private $scriptName = NULL;
    private $serverName= NULL;
    private $shortUrl = NULL;
    private $url = NULL;
   
	/**
	 * Constructor
	 */
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
        if(is_null($this->requestTime)) {
            $this->requestTime = $this->server->get('REQUEST_TIME');
        }
        
        return $this->requestTime;
    }
    
    public function getRequestUri()
    {
        if(is_null($this->requestUri)) {
            $this->requestUri = $this->server->get('REQUEST_URI');
        }
        
        return $this->requestUri;        
    }
    
    public function getRequestMethod()
    {
        if(is_null($this->requestMethod)) {
            $this->requestMethod = $this->server->get('REQUEST_METHOD');
        }
        
        return $this->requestMethod;        
    }
    
    public function getClietnIP()
    {
        if(is_null($this->clientIP)) {
            $this->clientIP = $this->server->get('REMOTE_ADDR');
        }
        
        return $this->clientIP;
    }

    public function getScriptName()
    {
        if(is_null($this->scriptName)) {
            $this->scriptName = $this->server->get('SCRIPT_NAME');
        }
        
        return $this->scriptName;
    }
    
    public function getPathInfo()
    {
        if(is_null($this->pathInfo)) {
            $this->pathInfo = $this->server->get('PATH_INFO');
        }
        
        return $this->pathInfo;
    }
    
    public function getRedirectStatus()
    {
        if(is_null($this->redirectStatus)) {
            $this->redirectStatus = $this->server->get('REDIRECT_STATUS');
        }
        
        return $this->redirectStatus;
    }
    
    public function getHost()
    {
        if(is_null($this->httpHost)) {
            $this->httpHost = $this->server->get('HTTP_HOST');
        }
        
        return $this->httpHost;
    }
    
    public function getUserAgent()
    {
        if(is_null($this->httpUserAgent)) {
            $this->httpUserAgent = $this->server->get('HTTP_USER_AGENT');
        }
        
        return $this->httpUserAgent;
    }

    public function getLanguages()
    {
        if(is_null($this->languages)) {
            $sLanguages = $this->server->get('HTTP_ACCEPT_LANGUAGE');
            $aLanguages = explode(',', $sLanguages);
            
            foreach($aLanguages as $sLanguage) {
                $aLanguage = explode(';', $sLanguage);
                $this->languages[] = $aLanguage[0];
            }
        }
        
        unset($sLanguage, $sLanguages, $aLanguage, $aLanguages);
        return $this->languages;
    }
    
    public function getProtocol()
    {
        if(is_null($this->protocol)) {
            $this->protocol = 'http';

            if($this->isSafe()) {
                $this->protocol .= 's';
            }

            $this->protocol .= '://';
        }
        
        return $this->protocol;
    }
    
    public function getServerName()
    {
        if(is_null($this->serverName)) {
            $this->serverName = $this->server->get('SERVER_NAME');
        }
        
        return $this->serverName;
    }

    public function getQueryString()
    {
        if(is_null($this->queryString)) {
            $this->queryString = $this->server->get('QUERY_STRING');
        }
        
        return $this->queryString;
    }

    public function getBaseUrl()
    {
        if(is_null($this->baseUrl)) {
            $sFile = basename($this->getScriptName());
            $this->baseUrl = ltrim(preg_replace('/' . $sFile . '/', '', $this->scriptName), '/');
        }

        unset($sFile);
        return $this->baseUrl;
    }

    public function getFullUrl()
    {
        if(is_null($this->fullUrl)) {
            $this->fullUrl = $this->getShortUrl() .
                             $this->getURL();
        }
        
        return $this->fullUrl;
    }

    public function getShortUrl()
    {
        if(is_null($this->shortUrl)) {
            $this->shortUrl = $this->getProtocol() .
                             $this->getHost() . '/' .
                             $this->getBaseUrl();
        }
        
        return $this->shortUrl;
    }

    /**
	 * Get actual URL
	 * 
	 * @return string
	 */
	public function getURL()
	{
        if(is_null($this->url)) {
            $aFrom = array('?' . $this->getQueryString());
            $this->getBaseUrl();
            if(!empty($this->baseUrl)) {
                $aFrom[] = '/' . $this->baseUrl;
            }
            $this->url = urldecode($this->getRequestUri());

            foreach($aFrom as $sItem) {
                $this->url = str_replace($sItem, '', $this->url);
            }
            
            if('/' == substr($this->url, 0, 1)) {
                $this->url = substr($this->url, 1);
            }
        }
        
        unset($aFrom);
		return $this->url;
	}

    /**
	 * Get crumb
	 *
	 * @param integer $nIndex Index on route
	 * @return string Crumb from route
	 */
	public function getCrumb($nIndex=NULL)
	{
        if(is_null($this->crumb)) {
            $this->crumb = explode('/', $this->getURL());
        }

		$sReturn = NULL;

		if(is_null($nIndex)) {
			$sReturn = $this->crumb;
		}
		else {
            $nIndex = (int)$nIndex;
			if(!empty($this->crumb[$nIndex])) {
				$sReturn = $this->crumb[$nIndex];
			}
		}

		unset($nIndex);

		return $sReturn;
	}
    
    /**
     * Return crumb count
     * @return integer
     */
    public function getCrumbCount()
    {
        if(is_null($this->crumbCount)) {
            $this->crumbCount = count($this->getCrumb());
        }
        
        return $this->crumbCount;
    }
    
    public function isAjax()
    {
        if(is_null($this->ajax)) {
            if(strtolower($this->server->get('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest' ) {
                $this->ajax = TRUE;
            }
            else {
                $this->ajax = FALSE;
            }
        }
        
        return $this->ajax;
    }
    
    public function isSafe()
    {
        if(is_null($this->safe)) {
            if($this->server->get('HTTPS') === NULL) {
                $this->safe = FALSE;
            }
            else {
                $this->safe = TRUE;
            }
        }
        
        return $this->safe;
    }
    
}

# End of file