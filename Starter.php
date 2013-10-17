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
class Starter {

    const LOADER_NOT_EXISTS = 'Loader file "%s" does not exists!';

    private static $oConfiguration;

    public static function Start($sStarterFile)
    {
        ob_start(array('self', 'sanitizeOutput'));

        $nMemory = memory_get_usage();

        self::installLoader();

        $aMicrotime = Time::explodeMicrotime();

        self::openStarterFile($sStarterFile);
        self::addPathToLoader();
        self::setLocale();
        self::setTimezone();
        self::initRequest();
        self::initProfiler($aMicrotime, $nMemory);
        self::initSession();
        self::initCache();
        self::initDatabase();
        self::initTemplate();

        self::dispatchURL();

        ob_end_flush();

        unset($sStarterFile, $aMicrotime, $nMemory);
        exit;
    }

    public static function installLoader()
    {
        try {
            $sLoaderFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Loader.php';

            if(is_file($sLoaderFile)) {
                require_once($sLoaderFile);
                Loader::Init();
            }
            else {
                throw new \Exception(sprintf(self::LOADER_NOT_EXISTS, $sLoaderFile));
            }
        }
        catch (\Exception $oException) {
            exit($oException->getMessage());
        }

        unset($sLoaderFile);
    }

    public static function openStarterFile($sStarterFile)
    {
        $sAdapter = Config::findAdapter($sStarterFile);
        $oAdapter = new $sAdapter($sStarterFile);

        self::$oConfiguration = new Config($oAdapter);

        unset($sStarterFile, $sAdapter, $oAdapter);
    }

    public static function initRequest()
    {
        Storage::Set('Request', new Request());

        Storage::Request()->getFullUrl();
    }

    public static function initProfiler($aMicrotime, $nMemory)
    {
        $sEnvironment = self::$oConfiguration->getValue('environment', 'definition');

        if('development' == $sEnvironment) {
            Storage::Set('Profiler', new Profiler($aMicrotime, $nMemory));
        }

        unset($sEnvironment);
    }

    public static function initCache()
    {
        $aCache = self::$oConfiguration->getSection('cache');

        if(!empty($aCache)) {
            $sAdapter = Cache::findAdapter($aCache['adapter']);
            $oAdapter = new $sAdapter($aCache);
            Storage::Set('Cache', new Cache($oAdapter));

            if(!empty($aCache['expiration'])) {
                Storage::Cache()->setExpiration($aCache['expiration']);
            }
        }

        unset($aCache, $sAdapter, $oAdapter);
    }

    public static function addPathToLoader()
    {
        $aLoader = self::$oConfiguration->getSection('loader');

        if(!empty($aLoader)) {
            foreach ($aLoader as $sPath) {
                Loader::addPath($sPath);
            }
        }

        unset($aLoader, $sPath);
    }

    public static function initDatabase()
    {
        $aDatabases = self::$oConfiguration->getSection('database');

        if(!empty($aDatabases)) {
            foreach ($aDatabases as $sName => $aDatabase) {
                $sAdapter = Data::findAdapter($aDatabase['adapter']);
                $oAdapter = new $sAdapter($aDatabase);
                Storage::Set($sName, $oAdapter);
            }
        }

        unset($aDatabases, $sName, $aDatabase, $sAdapter, $oAdapter);
    }

    public static function initTemplate()
    {
        $sPath = self::$oConfiguration->getValue('twigPath', 'definition');
        Template::setPath($sPath);

        unset($sPath);
    }

    public static function initSession()
    {
        $sSessionType = self::$oConfiguration->getValue('session', 'definition');

        if(!empty($sSessionType)) {
            Session::Start($sSessionType);
        }

        unset($sSessionType);
    }

    public static function setTimezone()
    {
        $sTimeZone = self::$oConfiguration->getValue('timezone', 'definition');

        if(!empty($sTimeZone)) {
            date_default_timezone_set($sTimeZone);
        }

        unset($sTimeZone);
    }

    public static function setLocale()
    {
        $sLocale = self::$oConfiguration->getValue('locale', 'definition');

        if(!empty($sLocale)) {
            setlocale(LC_ALL, $sLocale);
        }

        unset($sLocale);
    }

    public static function dispatchURL()
    {
        $sDispatcher = self::$oConfiguration->getValue('dispatcher', 'definition');
        $sAdapter = Config::findAdapter($sDispatcher);
        $oAdapter = new $sAdapter($sDispatcher);

        $oDispatcher = new Dispatcher(Storage::Request(), new Config($oAdapter));
        echo $oDispatcher->Dispatch();

        unset($sDispatcher, $sAdapter, $oAdapter, $oDispatcher);
    }

    public static function sanitizeOutput($output)
    {
        $sEnvironment = self::$oConfiguration->getValue('environment', 'definition');
                
        if('development' != $sEnvironment) {
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
        
        unset($sEnvironment, $search, $replace);
        return $output;
    }

}

# End of file