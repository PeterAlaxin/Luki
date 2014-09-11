<?php

/**
 * Starter class
 *
 * Luki framework
 * Date 22.07.2013
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

use Luki\Cache;
use Luki\Config;
use Luki\Data;
use Luki\Dispatcher;
use Luki\Elasticsearch;
use Luki\Loader;
use Luki\Profiler;
use Luki\Request;
use Luki\Session;
use Luki\Storage;
use Luki\Template;
use Luki\Time;

/**
 * Starter class
 *
 * @package Luki
 */
class Starter
{

    const LOADER_NOT_EXISTS = 'Loader file "%s" does not exists!';

    public static function Start($starterFile)
    {
        ob_start(array( 'self', 'sanitizeOutput' ));

        $memoryUsage = memory_get_usage();

        self::installLoader();

        $microTime = Time::explodeMicrotime();

        self::openStarterFile($starterFile);
        self::defineConstants();
        self::initFolders();
        self::addPathToLoader();
        self::setLocale();
        self::setTimezone();
        self::initRequest();
        self::initProfiler($microTime, $memoryUsage);
        self::initSession();
        self::initCache();
        self::initDatabase();
        self::initTemplate();
        self::initElasticsearch();

        self::dispatchURL();

        ob_end_flush();

        unset($starterFile, $microTime, $memoryUsage);
        exit;
    }

    public static function installLoader()
    {
        try {
            $loaderFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Loader.php';

            if ( is_file($loaderFile) ) {
                require_once($loaderFile);
                Loader::Init();
            } else {
                throw new \Exception(sprintf(self::LOADER_NOT_EXISTS, $loaderFile));
            }
        }
        catch ( \Exception $exception ) {
            exit($exception->getMessage());
        }

        unset($loaderFile);
    }

    public static function openStarterFile($starterFile)
    {
        $adapterName = Config::findAdapter($starterFile);
        $adapter = new $adapterName($starterFile);

        Storage::Set('Configuration', new Config($adapter));

        if ( 'development' == Storage::Configuration()->getValue('environment', 'definition') ) {
            Storage::Set('Development', TRUE);
        }

        unset($starterFile, $adapterName, $adapter);
    }

    public static function initFolders()
    {
        $folders = Storage::Configuration()->getSection('folder');

        foreach ( $folders as $key => $path ) {
            Storage::Set($key, $path);
        }

        unset($folders, $key, $path);
    }

    public static function initRequest()
    {
        Storage::Set('Request', new Request());
        Storage::Request()->getFullUrl();
    }

    public static function initProfiler($microTime, $memory)
    {
        if ( Storage::isDevelopment() ) {
            Storage::Set('Profiler', new Profiler($microTime, $memory));
        }
    }

    public static function initCache()
    {
        $cache = Storage::Configuration()->getSection('cache');

        if ( !empty($cache) ) {
            $adapterName = Cache::findAdapter($cache['adapter']);
            $adapter = new $adapterName($cache);
            Storage::Set('Cache', new Cache($adapter));

            if ( !empty($cache['expiration']) ) {
                Storage::Cache()->setExpiration($cache['expiration']);
            }

            if ( isset($cache['useCache']) ) {
                Storage::Cache()->useCache($cache['useCache']);
            }
        }

        unset($cache, $adapterName, $adapter);
    }

    public static function initElasticsearch()
    {
        $elasticsearch = Storage::Configuration()->getSection('elasticsearch');

        if ( !empty($elasticsearch) ) {
            Storage::Set('Elasticsearch', new Elasticsearch);

            if ( !empty($elasticsearch['server']) ) {
                Storage::Elasticsearch()->setServer($elasticsearch['server']);
            }

            if ( isset($elasticsearch['port']) ) {
                Storage::Elasticsearch()->setPort($elasticsearch['port']);
            }

            if ( isset($elasticsearch['index']) ) {
                Storage::Elasticsearch()->setIndex($elasticsearch['index']);
            }
        }

        unset($elasticsearch);
    }

    public static function addPathToLoader()
    {
        $loader = Storage::Configuration()->getSection('loader');

        if ( !empty($loader) ) {
            foreach ( $loader as $path ) {
                if ( is_array($path) ) {
                    foreach ( $path as $onePath ) {
                        Loader::addPath($onePath);
                    }
                } else {
                    Loader::addPath($path);
                }
            }
        }

        unset($loader, $path, $onePath);
    }

    public static function initDatabase()
    {
        $databases = Storage::Configuration()->getSection('database');

        if ( !empty($databases) ) {
            foreach ( $databases as $name => $database ) {
                $adapterName = Data::findAdapter($database['adapter']);
                $adapter = new $adapterName($database);
                Storage::Set($name, $adapter);
            }
        }

        unset($databases, $name, $database, $adapterName, $adapter);
    }

    public static function initTemplate()
    {
        $path = Storage::Configuration()->getValue('twigPath', 'definition');
        Template::setPath($path);

        unset($path);
    }

    public static function initSession()
    {
        $sessionType = Storage::Configuration()->getValue('session', 'definition');

        if ( !empty($sessionType) ) {
            Session::Start($sessionType);
        }

        unset($sessionType);
    }

    public static function defineConstants()
    {
        $constants = Storage::Configuration()->getSection('constants');

        foreach ( $constants as $key => $value ) {
            define($key, $value, TRUE);
        }

        unset($constants, $key, $value);
    }

    public static function setTimezone()
    {
        $timeZone = Storage::Configuration()->getValue('timezone', 'definition');

        if ( !empty($timeZone) ) {
            date_default_timezone_set($timeZone);
        }

        unset($timeZone);
    }

    public static function setLocale()
    {
        $locale = Storage::Configuration()->getValue('locale', 'definition');

        if ( !empty($locale) ) {
            setlocale(LC_ALL, $locale);
        }

        unset($locale);
    }

    public static function dispatchURL()
    {
        $dispatcherName = Storage::Configuration()->getValue('dispatcher', 'definition');
        $adapterName = Config::findAdapter($dispatcherName);
        $adapter = new $adapterName($dispatcherName);

        $dispatcher = new Dispatcher(Storage::Request(), new Config($adapter));
        echo $dispatcher->Dispatch();

        unset($dispatcher, $adapterName, $adapter, $dispatcherName);
    }

    public static function sanitizeOutput($output)
    {
        if ( !Storage::isDevelopment() ) {
            $search = array(
              '/\>[^\S ]+/s', //strip whitespaces after tags, except space
              '/[^\S ]+\</s', //strip whitespaces before tags, except space
              '/(\s)+/s'  // shorten multiple whitespace sequences
            );
            $replace = array(
              '>',
              '<',
              '\\1'
            );
            $output = preg_replace($search, $replace, $output);
        }

        unset($search, $replace);
        return $output;
    }

}

# End of file