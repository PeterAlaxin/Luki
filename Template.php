<?php
/**
 * Template class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Template
 * @filesource
 */

namespace Luki;

use Luki\Alerts;
use Luki\File;
use Luki\Request;
use Luki\Storage;
use Luki\Template\Block;
use Luki\Template\Filters\Capitalize;
use Luki\Time;

class Template
{
    protected static $twigPath = '';
    protected $template        = '';
    protected $class           = '';
    protected $classContent    = '';
    protected $data            = array();
    protected $variables       = array();
    protected $blocks          = array();
    protected $twig            = '';
    protected $newClass        = '';
    protected $filters         = array();
    protected $functions       = array();
    protected $tests           = array();
    protected $extendedClass   = '';

    function __construct($template, $data = null)
    {
        $this->template = $template;
        $this->data     = (array) $data;
        $this->addApplicationData();

        $templateWithoutTwig     = preg_replace('/.twig/', '', $template);
        $templateWithoutTemplate = preg_replace('/\/template/', '', $templateWithoutTwig);
        $class                   = ucwords(preg_replace('/\//', ' ', $templateWithoutTemplate));

        if (empty($class)) {
            throw new \Exception('Wrong template: '.$this->template);
        }

        $this->setClassName($class);

        if (Storage::isProfiler()) {
            Time::stopwatchStart('Luki_Template_'.$this->class);
        }

        $this->newClass = self::$twigPath.preg_replace('/_/', '/', $this->class).'.php';
        if (!is_file($this->newClass) or filectime($this->template) > filectime($this->newClass)) {
            $this->generateTemplate();
        }
    }

    private function setClassName($class)
    {
        $aTemplate   = explode('/', $this->template);
        $position    = array_search('template', $aTemplate);
        $this->class = implode('', array_slice(explode(' ', $class), -(count($aTemplate) - $position)));
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function setPath($newPath)
    {
        self::$twigPath = $newPath;
    }

    public function Render()
    {
        $templateClass = new $this->class($this->data);

        ob_start();
        $templateClass->Render();
        $output = ob_get_contents();
        ob_end_clean();

        if (Storage::isProfiler()) {
            $stopTime = Time::getStopwatch('Luki_Template_'.$this->class);
            Storage::Profiler()->Add('Template', array('name' => $this->class, 'time' => $stopTime));
        }

        return $output;
    }

    private function generateTemplate()
    {
        $this->twig = file_get_contents($this->template);

        $this->clearComments();
        $this->extendClass();
        $this->begin();

        if (empty($this->extendedClass)) {
            $this->defineFilters();
            $this->defineFunctions();
            $this->defineTests();
        }

        $this->defineBlocks();
        $this->addFunctions();
        $this->useBlocks();
        $this->end();

        file_put_contents($this->newClass, $this->classContent);
    }

    private function clearComments()
    {
        $matches = array();
        preg_match_all('|{# (.*) #}|U', $this->twig, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $this->twig = str_replace($match[0], '', $this->twig);
        }
    }

    private function extendClass()
    {
        $matches = array();
        preg_match_all('|{% extends "(.*)" %}|U', $this->twig, $matches, PREG_SET_ORDER);

        if (!empty($matches) and ! empty($matches[0][1])) {

            $extends       = explode('/', $matches[0][1]);
            $extendedClass = Loader::isFile($extends[0].'/template/'.$extends[1].'.twig');
            $template      = new Template($extendedClass, array());

            foreach ($extends as $key => $extended) {
                $extends[$key] = Capitalize::Get($extended);
            }

            $this->extendedClass = implode('', $extends);
        }

        foreach ($matches as $match) {
            $this->twig = str_replace($match[0], '', $this->twig);
        }
    }

    private function begin()
    {
        $this->classContent = self::phpRow('<?php', 0);

        if (!empty($this->extendedClass)) {
            $this->classContent .= self::phpRow('class '.$this->class.' extends '.$this->extendedClass, 0);
            $this->classContent .= self::phpRow('{', 0, 2);
            $this->classContent .= self::phpRow('public function __construct($aData)');
            $this->classContent .= self::phpRow('{');
            $this->classContent .= self::phpRow('parent::__construct($aData);', 2);
            $this->classContent .= self::phpRow('}', 1, 2);
        } else {
            $this->classContent .= self::phpRow('class '.$this->class, 0);
            $this->classContent .= self::phpRow('{', 0, 2);
            $this->classContent .= self::phpRow('public $aFilters = array();', 1, 2);
            $this->classContent .= self::phpRow('public $aFunctions = array();', 1, 2);
            $this->classContent .= self::phpRow('public $aData = array();', 1, 2);
            $this->classContent .= self::phpRow('public $aLoop = array();', 1, 2);
            $this->classContent .= self::phpRow('public function __construct($aData)');
            $this->classContent .= self::phpRow('{');
            $this->classContent .= self::phpRow('$this->aData = $aData;', 2);
            $this->classContent .= self::phpRow('$this->aData["loop"] = array();', 2);
            $this->classContent .= self::phpRow('$this->_defineFilters();', 2);
            $this->classContent .= self::phpRow('$this->_defineFunctions();', 2);
            $this->classContent .= self::phpRow('$this->_defineTests();', 2);
            $this->classContent .= self::phpRow('}', 1, 2);
            $this->classContent .= self::phpRow('public function Render()');
            $this->classContent .= self::phpRow('{');
            $this->classContent .= self::phpRow('$this->_mainBlock();', 2);
            $this->classContent .= self::phpRow('}', 1, 2);
        }
    }

    private function end()
    {
        $this->classContent .= self::phpRow('public function __destruct()');
        $this->classContent .= self::phpRow('{');
        $this->classContent .= self::phpRow('foreach ( $this as &$value ) {', 2);
        $this->classContent .= self::phpRow('$value = null;', 3);
        $this->classContent .= self::phpRow('}', 2);
        $this->classContent .= self::phpRow('}');

        $this->classContent .= self::phpRow('}', 0, 0);
    }

    private function defineFilters()
    {
        $this->classContent .= self::phpRow('public function _defineFilters()');
        $this->classContent .= self::phpRow('{');

        $files = File::getFilesInDirectory(__DIR__.'/Template/Filters');

        foreach ($files as $file) {
            $file               = preg_replace('/.php/', '', $file);
            $filter             = strtolower($file);
            $text               = '$this->aFilters["'.$filter.'"] = new Luki\\Template\\Filters\\'.$file.';';
            $this->classContent .= self::phpRow($text, 2);
            $this->filters[]    = $filter;
        }
        $this->classContent .= self::phpRow('}', 1, 2);
    }

    private function defineFunctions()
    {
        $this->classContent .= self::phpRow('public function _defineFunctions()');
        $this->classContent .= self::phpRow('{');

        $files = File::getFilesInDirectory(__DIR__.'/Template/Functions');

        foreach ($files as $file) {
            $file               = preg_replace('/.php/', '', $file);
            $function           = strtolower($file);
            $text               = '$this->aFunctions["'.$function.'"] = new Luki\\Template\\Functions\\'.$file.';';
            $this->classContent .= self::phpRow($text, 2);
        }
        $this->classContent .= self::phpRow('}', 1, 2);
    }

    private function defineTests()
    {
        $this->classContent .= self::phpRow('public function _defineTests()');
        $this->classContent .= self::phpRow('{');

        $files = File::getFilesInDirectory(__DIR__.'/Template/Tests');

        foreach ($files as $file) {
            $file               = preg_replace('/.php/', '', $file);
            $function           = strtolower($file);
            $text               = '$this->aTests["'.$function.'"] = new Luki\\Template\\Tests\\'.$file.';';
            $this->classContent .= self::phpRow($text, 2);
        }
        $this->classContent .= self::phpRow('}', 1, 2);
    }

    private function defineBlocks()
    {
        $mainBlock = $this->parseBlocks($this->twig);

        if (empty($this->extendedClass)) {
            $this->blocks['main'] = new Block($mainBlock);
        }

        foreach ($this->blocks as $name => $block) {
            $blockContent       = $this->getContent($name, $block);
            $this->classContent .= self::phpRow('public function _'.$name.'Block()');
            $this->classContent .= self::phpRow('{');
            $this->classContent .= self::phpRow(' ?>'.$blockContent.'<?php ', 2);
            $this->classContent .= self::phpRow('}', 1, 2);
            $this->variables    = array_merge($this->variables, $block->getVariables());
        }
    }

    private function getContent($name, $block)
    {
        if (empty($this->extendedClass)) {
            $content = preg_replace('/{% parent %}/', '', $block->getContent());
        } else {
            $text    = '<?php parent::_'.$name.'Block(); ?>';
            $content = preg_replace('/{% parent %}/', self::phpRow($text), $block->getContent());
        }

        return $content;
    }

    private function parseBlocks($block)
    {
        $startMatches = array();
        $endMatches   = array();
        $blockMatches = array();

        preg_match_all('|({% block (.*) %})|U', $block, $startMatches, PREG_SET_ORDER);
        preg_match_all('|({% endblock(.*) %})|U', $block, $endMatches, PREG_SET_ORDER);

        if (count($startMatches) != count($endMatches)) {
            echo 'Template error in block counts';
            exit;
        }

        while (count($startMatches) > 0) {
            foreach ($startMatches as $blocks) {
                $text = '|({% block '.$blocks[2].' %})([\s\S]*)({% endblock(.*) %})|U';
                preg_match_all($text, $block, $blockMatches, PREG_SET_ORDER);

                foreach ($blockMatches as $subBlock) {
                    if (0 === preg_match_all('/{% block (.*) %}/', $subBlock[2], $aSubBlockMatches)) {
                        $this->blocks[$blocks[2]] = new Block($subBlock);
                        $block                    = str_replace($subBlock[0], '<?php $this->_'.$blocks[2].'Block(); ?>',
                            $block);
                    }
                }
            }

            preg_match_all('|({% block (.*) %})|U', $block, $startMatches, PREG_SET_ORDER);
        }

        return $block;
    }

    private function addFunctions()
    {
        foreach ($this->variables as $variable) {

            $functionName = $variable->getFunctionName();

            if (!empty($functionName) and ! in_array($functionName, $this->functions)) {
                $this->classContent .= $variable->getFunction();
                $this->functions[]  = $functionName;
            }
        }
    }

    private function useBlocks()
    {
        $matches = array();
        preg_match_all('/{{ (block)(\(["\'])(.*)(["\']\)) }}/U', $this->classContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $variable) {
            $block              = '<?php $this->_'.$variable[3].'Block(); ?>';
            $this->classContent = str_replace($variable[0], $block, $this->classContent);
        }
    }

    public static function phpRow($string, $tab = 1, $eol = 1)
    {
        for ($n = 1; $n <= $tab; $n++) {
            $string = "    ".$string;
        }

        for ($n = 1; $n <= $eol; $n++) {
            $string .= "\n";
        }

        return $string;
    }

    private function addApplicationData()
    {
        $this->data['app'] = array(
            'request'  => $this->addRequest(),
            'storage'  => $this->addData(Storage::getData()),
            'constant' => $this->addConstants(),
            'alerts'   => Alerts::getAlerts(),
            'session'  => $_SESSION
        );
    }

    private function addRequest()
    {
        $request = $this->getRequest();

        $formatedRequest = array(
            'requestTime'    => $request->getRequestTime(),
            'requestUri'     => $request->getRequestUri(),
            'requestMethod'  => $request->getRequestMethod(),
            'clientIp'       => $request->getClientIP(),
            'scriptName'     => $request->getScriptName(),
            'pathInfo'       => $request->getPathInfo(),
            'redirectStatus' => $request->getRedirectStatus(),
            'host'           => $request->getHost(),
            'userAgent'      => $request->getUserAgent(),
            'userAgent'      => $request->getUserAgent(),
            'languages'      => $request->getLanguages(),
            'protocol'       => $request->getProtocol(),
            'serverName'     => $request->getServerName(),
            'queryString'    => $request->getQueryString(),
            'baseUrl'        => $request->getBaseUrl(),
            'fullUrl'        => $request->getFullUrl(),
            'shortUrl'       => $request->getShortUrl(),
            'url'            => $request->getURL(),
            'crumb'          => $request->getCrumb(),
            'crumbCount'     => $request->getCrumbCount(),
            'isAjax'         => $request->isAjax(),
            'isSafe'         => $request->isSafe(),
            'get'            => $this->addData($request->get->getData()),
            'post'           => $this->addData($request->post->getData()),
            'files'          => $this->addData($request->files->getData()),
            'cookie'         => $this->addData($request->cookie->getData()),
            'server'         => $this->addData($request->server->getData()),
        );

        return $formatedRequest;
    }

    private function addConstants()
    {
        $constants = get_defined_constants(true);

        if (!empty($constants['user'])) {
            $userConstants = $constants['user'];
        } else {
            $userConstants = array();
        }

        return $userConstants;
    }

    private function getRequest()
    {
        if (Storage::isRequest()) {
            $request = Storage::Request();
        } else {
            $request = new Request;
        }

        return $request;
    }

    private function addData($data)
    {
        $return = array();

        foreach ($data as $key => $value) {
            $return[$key] = $value;
        }

        return $return;
    }
}