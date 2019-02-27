<?php
/**
 * Starter class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Starter
 * @filesource
 */

namespace Luki;

use Luki\Cache;
use Luki\Config;
use Luki\Data;
use Luki\Elasticsearch;
use Luki\Language;
use Luki\Loader;
use Luki\Log;
use Luki\Profiler;
use Luki\Request;
use Luki\Session;
use Luki\Storage;
use Luki\Template;
use Luki\Time;

class Starter
{
    const LOADER_NOT_EXISTS = 'Loader file "%s" does not exists!';

    public static function Start($starterFile)
    {
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Functions.php';
        set_exception_handler("default_exception_handler");

        ob_start(array('self', 'sanitizeOutput'));

        $memoryUsage = memory_get_usage();

        self::installLoader();

        $microTime = Time::explodeMicrotime();

        self::openStarterFile($starterFile);
        self::defineConstants();
        self::initFolders();
        self::addPathToLoader();
        self::setLocale();
        self::setTimezone();
        self::initLog();
        self::initSession();
        self::initRequest();
        self::initProfiler($microTime, $memoryUsage);
        self::initCache();
        self::initDatabase();
        self::initLanguage();
        self::initTemplate();
        self::initElasticsearch();
        self::detectRobot();

        self::dispatchURL();

        ob_end_flush();
        exit;
    }

    public static function installLoader()
    {
        try {
            $loaderFile = dirname(__FILE__).DIRECTORY_SEPARATOR.'Loader.php';

            if (is_file($loaderFile)) {
                require_once($loaderFile);
                Loader::Init();
            } else {
                throw new \Exception(sprintf(self::LOADER_NOT_EXISTS, $loaderFile));
            }
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
    }

    public static function openStarterFile($starterFile)
    {
        $adapterName = Config::findAdapter($starterFile);
        $adapter     = new $adapterName($starterFile);

        Storage::Set('Configuration', new Config($adapter));

        if ('development' == Storage::Configuration()->getValue('environment', 'definition')) {
            Storage::Set('Development', true);
        } else {
            Storage::Set('Production', true);
        }
    }

    public static function initLanguage()
    {
        $languages  = Storage::Configuration()->getSection('language');
        $translator = null;

        foreach ($languages as $language => $file) {
            if (empty($translator)) {
                $translator = new Language($language, $file);
            } else {
                $translator->addToLanguages($language, $file);
            }
        }

        if (!empty($translator)) {
            Storage::Set('Language', $translator);
        }
    }

    public static function initFolders()
    {
        foreach (Storage::Configuration()->getSection('folder') as $key => $path) {
            Storage::Set($key, $path);
        }
    }

    public static function initRequest()
    {
        Storage::Set('Request', new Request());
        Storage::Request()->getFullUrl();
    }

    public static function initProfiler($microTime, $memory)
    {
        if (Storage::isDevelopment()) {
            Storage::Set('Profiler', new Profiler($microTime, $memory));
            Storage::Profiler()->Add('Session', session_id());
        }
    }

    public static function initCache()
    {
        $cache = Storage::Configuration()->getSection('cache');

        if (!empty($cache)) {
            $adapterName = Cache::findAdapter($cache['adapter']);
            $adapter     = new $adapterName($cache);
            Storage::Set('Cache', new Cache($adapter));

            if (!empty($cache['expiration'])) {
                Storage::Cache()->setExpiration($cache['expiration']);
            }

            if (isset($cache['useCache'])) {
                Storage::Cache()->useCache($cache['useCache']);
            }
        }
    }

    public static function initElasticsearch()
    {
        $elasticsearch = Storage::Configuration()->getSection('elasticsearch');

        if (!empty($elasticsearch)) {

            Storage::Set('Elasticsearch', new Elasticsearch);

            if (!empty($elasticsearch['server'])) {
                Storage::Elasticsearch()->setServer($elasticsearch['server']);
            }

            if (isset($elasticsearch['port'])) {
                Storage::Elasticsearch()->setPort($elasticsearch['port']);
            }

            if (isset($elasticsearch['index'])) {
                Storage::Elasticsearch()->setIndex($elasticsearch['index']);
            }
        }
    }

    public static function addPathToLoader()
    {
        foreach (Storage::Configuration()->getSection('loader') as $path) {
            if (is_array($path)) {
                foreach ($path as $onePath) {
                    Loader::addPath($onePath);
                }
            } else {
                Loader::addPath($path);
            }
        }
    }

    public static function initDatabase()
    {
        $databases = Storage::Configuration()->getSection('databases');

        if (!empty($databases)) {
            foreach ($databases as $name => $database) {
                $adapterName = Data::findAdapter($database['adapter']);
                $adapter     = new $adapterName($database);
                Storage::Set($name, $adapter);
            }
        } else {
            $database = Storage::Configuration()->getSection('database');
            if (!empty($database)) {
                $adapterName = Data::findAdapter($database['adapter']);
                $adapter     = new $adapterName($database);
                Storage::Set($database['name'], $adapter);
            }
        }
    }

    public static function initTemplate()
    {
        $path = Storage::Configuration()->getValue('twigPath', 'definition');
        Template::setPath($path);
    }

    public static function initLog()
    {
        $logDefinition = Storage::Configuration()->getSection('log');

        if (!empty($logDefinition)) {
            $formatName = Log::findFormat($logDefinition['format']);
            $format     = new $formatName();
            $filename   = $logDefinition['dir'].'/'.strftime($logDefinition['name'], strtotime('now'));
            $writerName = Log::findWriter($logDefinition['writer']);
            $writer     = new $writerName($filename);
            Storage::Set('Log', new Log($format, $writer));
            Storage::Set('LogRedirect', !empty($logDefinition['logRedirect']));
        }
    }

    public static function initSession()
    {
        $sessionType = Storage::Configuration()->getValue('session', 'definition');

        if (!empty($sessionType)) {
            Session::Start($sessionType);
        }
    }

    public static function defineConstants()
    {
        foreach (Storage::Configuration()->getSection('constants') as $key => $value) {
            define($key, $value, true);
        }
    }

    public static function setTimezone()
    {
        $timeZone = Storage::Configuration()->getValue('timezone', 'definition');

        if (!empty($timeZone)) {
            date_default_timezone_set($timeZone);
        }
    }

    public static function setLocale()
    {
        $locale = Storage::Configuration()->getValue('locale', 'definition');

        if (!empty($locale)) {
            setlocale(LC_ALL, $locale);
        }
    }

    public static function dispatchURL()
    {
        if (Storage::Configuration()->isValue('dispatcher', 'definition')) {
            $file  = Storage::Configuration()->getValue('dispatcher', 'definition');
            $class = 'Luki\Dispatcher';
        } elseif (Storage::Configuration()->isValue('router', 'definition')) {
            $file  = Storage::Configuration()->getValue('router', 'definition');
            $class = 'Luki\Router';
        } else {
            Exit('Missing definition for Routing or Dispatcher');
        }

        $adapterName = Config::findAdapter($file);
        $adapter     = new $adapterName($file);
        $dispatcher  = new $class(Storage::Request(), new Config($adapter));
        echo $dispatcher->Dispatch();

        $dispatcher->__destruct();
        $adapter->__destruct();
    }

    public static function sanitizeOutput($output)
    {
        if (!Storage::isDevelopment()) {
            if (!Storage::Configuration()->isValue('sanitize', 'definition') or 1 == Storage::Configuration()->getValue('sanitize',
                    'definition')) {
                $search  = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
                $replace = array('>', '<', '\\1');
                $output  = preg_replace($search, $replace, $output);
            }
        }

        return $output;
    }

    public static function showErrors()
    {
        ob_end_flush();
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }

    public static function detectRobot()
    {
        $robots = '/(bot|crawler|spider|80legs|baidu|yahoo! slurp|ia_archiver|mediapartners-google|lwp-trivial|nederland.zoek|ahoy|anthill|appie|arale|araneo|ariadne|atn_worldwide|atomz|bjaaland|ukonline|calif|combine|cosmos|cusco|cyberspyder|digger|grabber|downloadexpress|ecollector|ebiness|esculapio|esther|felix ide|hamahakki|kit-fireball|fouineur|freecrawl|desertrealm|gcreep|golem|griffon|gromit|gulliver|gulper|whowhere|havindex|hotwired|htdig|ingrid|informant|inspectorwww|iron33|teoma|ask jeeves|jeeves|image.kapsi.net|kdd-explorer|label-grabber|larbin|linkidator|linkwalker|lockon|marvin|mattie|mediafox|merzscope|nec-meshexplorer|udmsearch|moget|motor|muncher|muninn|muscatferret|mwdsearch|sharp-info-agent|webmechanic|netscoop|newscan-online|objectssearch|orbsearch|packrat|pageboy|parasite|patric|pegasus|phpdig|piltdownman|pimptrain|plumtreewebaccessor|getterrobo-plus|raven|roadrunner|robbie|robocrawl|robofox|webbandit|scooter|search-au|searchprocess|senrigan|shagseeker|site valet|skymob|slurp|snooper|speedy|curl_image_client|suke|www.sygol.com|tach_bw|templeton|titin|topiclink|udmsearch|urlck|valkyrie libwww-perl|verticrawl|victoria|webscout|voyager|crawlpaper|webcatcher|t-h-u-n-d-e-r-s-t-o-n-e|webmoose|pagesinventory|webquest|webreaper|webwalker|winona|occam|robi|fdse|jobo|rhcs|gazz|dwcp|yeti|fido|wlm|wolp|wwwc|xget|legs|curl|webs|wget|sift|cmc)+/i';
        Storage::Set('Robot', preg_match($robots, $_SERVER['HTTP_USER_AGENT']));
    }
}