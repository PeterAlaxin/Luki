<?php

/**
 * Template class
 *
 * Luki framework
 * Date 6.1.2013
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

use Luki\File;
use Luki\Request;
use Luki\Storage;
use Luki\Template\Block;
use Luki\Template\Filters\Capitalize;
use Luki\Time;

/**
 * Template class
 *
 * @package Luki
 */
class Template
{

    protected static $_twigPath = '';
    protected $_template = '';
    protected $_class = '';
    protected $_classContent = '';
    protected $_data = array();
    protected $_variables = array();
    protected $_blocks = array();
    protected $_twig = '';
    protected $_newClass = '';
    protected $_filters = array();
    protected $_functions = array();
    protected $_tests = array();
    protected $_extendedClass = '';

    function __construct($template, $data = NULL)
    {
        $this->_template = $template;
        $this->_data = (array) $data;
        $this->_addApplicationData();

        $templateWithoutTwig = preg_replace('/.twig/', '', $template);
        $templateWithoutTemplate = preg_replace('/\/template/', '', $templateWithoutTwig);
        $class = ucwords(preg_replace('/\//', ' ', $templateWithoutTemplate));

        $this->_class = implode('', array_slice(explode(' ', $class), -2));

        if ( Storage::isProfiler() ) {
            Time::stopwatchStart('Luki_Template_' . $this->_class);
        }

        $this->_newClass = self::$_twigPath . preg_replace('/_/', '/', $this->_class) . '.php';
        if ( !file($this->_newClass) or filectime($this->_template) > filectime($this->_newClass) ) {
            $this->_generateTemplate();
        }

        unset($template, $templateWithoutTwig, $templateWithoutTemplate, $data, $class);
    }

    public static function setPath($newPath)
    {
        self::$_twigPath = $newPath;

        unset($newPath);
    }

    public function Render()
    {
        $templateClass = new $this->_class($this->_data);

        ob_start();
        $templateClass->Render();
        $output = ob_get_contents();
        ob_end_clean();

        if ( Storage::isProfiler() ) {
            $stopTime = Time::getStopwatch('Luki_Template_' . $this->_class);
            Storage::Profiler()->Add('Template', array( 'name' => $this->_class, 'time' => $stopTime ));
        }

        unset($templateClass, $stopTime);
        return $output;
    }

    private function _generateTemplate()
    {
        $this->_twig = file_get_contents($this->_template);

        $this->_clearComments();
        $this->_extendClass();
        $this->_begin();

        if ( empty($this->_extendedClass) ) {
            $this->_defineFilters();
            $this->_defineFunctions();
            $this->_defineTests();
        }

        $this->_defineBlocks();
        $this->_addFunctions();
        $this->_useBlocks();
        $this->_end();

        file_put_contents($this->_newClass, $this->_classContent);
    }

    private function _clearComments()
    {
        $matches = array();
        preg_match_all('|{# (.*) #}|U', $this->_twig, $matches, PREG_SET_ORDER);

        foreach ( $matches as $match ) {
            $this->_twig = str_replace($match[0], '', $this->_twig);
        }

        unset($matches, $match);
    }

    private function _extendClass()
    {
        $matches = array();
        preg_match_all('|{% extends "(.*)" %}|U', $this->_twig, $matches, PREG_SET_ORDER);

        if ( !empty($matches) and ! empty($matches[0][1]) ) {

            $extends = explode('/', $matches[0][1]);
            $extendedClass = Loader::isFile($extends[0] . '/template/' . $extends[1] . '.twig');
            $template = new Template($extendedClass, array());

            foreach ( $extends as $key => $extended ) {
                $extends[$key] = Capitalize::Get($extended);
            }

            $this->_extendedClass = implode('', $extends);
        }

        foreach ( $matches as $match ) {
            $this->_twig = str_replace($match[0], '', $this->_twig);
        }

        unset($matches, $match, $extends, $extendedClass, $template, $key, $extended);
    }

    private function _begin()
    {
        $this->_classContent = self::phpRow('<?php', 0);

        if ( !empty($this->_extendedClass) ) {
            $this->_classContent .= self::phpRow('class ' . $this->_class . ' extends ' . $this->_extendedClass, 0);
            $this->_classContent .= self::phpRow('{', 0, 2);
            $this->_classContent .= self::phpRow('public function __construct($aData)');
            $this->_classContent .= self::phpRow('{');
            $this->_classContent .= self::phpRow('parent::__construct($aData);', 2);
            $this->_classContent .= self::phpRow('}', 1, 2);
        } else {
            $this->_classContent .= self::phpRow('class ' . $this->_class, 0);
            $this->_classContent .= self::phpRow('{', 0, 2);
            $this->_classContent .= self::phpRow('public $aFilters = array();', 1, 2);
            $this->_classContent .= self::phpRow('public $aFunctions = array();', 1, 2);
            $this->_classContent .= self::phpRow('public $aData = array();', 1, 2);
            $this->_classContent .= self::phpRow('public $aLoop = array();', 1, 2);
            $this->_classContent .= self::phpRow('public function __construct($aData)');
            $this->_classContent .= self::phpRow('{');
            $this->_classContent .= self::phpRow('$this->aData = $aData;', 2);
            $this->_classContent .= self::phpRow('$this->aData["loop"] = array();', 2);
            $this->_classContent .= self::phpRow('$this->_defineFilters();', 2);
            $this->_classContent .= self::phpRow('$this->_defineFunctions();', 2);
            $this->_classContent .= self::phpRow('$this->_defineTests();', 2);
            $this->_classContent .= self::phpRow('}', 1, 2);
            $this->_classContent .= self::phpRow('public function Render()');
            $this->_classContent .= self::phpRow('{');
            $this->_classContent .= self::phpRow('$this->_mainBlock();', 2);
            $this->_classContent .= self::phpRow('}', 1, 2);
        }
    }

    private function _end()
    {
        $this->_classContent .= self::phpRow('}', 0, 0);
    }

    private function _defineFilters()
    {
        $this->_classContent .= self::phpRow('public function _defineFilters()');
        $this->_classContent .= self::phpRow('{');

        $files = File::getFilesInDirectory(__DIR__ . '/Template/Filters');

        foreach ( $files as $file ) {
            $file = preg_replace('/.php/', '', $file);
            $filter = strtolower($file);
            $text = '$this->aFilters["' . $filter . '"] = new Luki\\Template\\Filters\\' . $file . ';';
            $this->_classContent .= self::phpRow($text, 2);
            $this->_filters[] = $filter;
        }
        $this->_classContent .= self::phpRow('}', 1, 2);

        unset($files, $file, $filter, $text);
    }

    private function _defineFunctions()
    {
        $this->_classContent .= self::phpRow('public function _defineFunctions()');
        $this->_classContent .= self::phpRow('{');

        $files = File::getFilesInDirectory(__DIR__ . '/Template/Functions');

        foreach ( $files as $file ) {
            $file = preg_replace('/.php/', '', $file);
            $function = strtolower($file);
            $text = '$this->aFunctions["' . $function . '"] = new Luki\\Template\\Functions\\' . $file . ';';
            $this->_classContent .= self::phpRow($text, 2);
        }
        $this->_classContent .= self::phpRow('}', 1, 2);

        unset($files, $file, $function, $text);
    }

    private function _defineTests()
    {
        $this->_classContent .= self::phpRow('public function _defineTests()');
        $this->_classContent .= self::phpRow('{');

        $files = File::getFilesInDirectory(__DIR__ . '/Template/Tests');

        foreach ( $files as $file ) {
            $file = preg_replace('/.php/', '', $file);
            $function = strtolower($file);
            $text = '$this->aTests["' . $function . '"] = new Luki\\Template\\Tests\\' . $file . ';';
            $this->_classContent .= self::phpRow($text, 2);
        }
        $this->_classContent .= self::phpRow('}', 1, 2);

        unset($files, $file, $function, $text);
    }

    private function _defineBlocks()
    {
        $mainBlock = $this->_parseBlocks($this->_twig);

        if ( empty($this->_extendedClass) ) {
            $this->_blocks['main'] = new Block($mainBlock);
        }

        foreach ( $this->_blocks as $name => $block ) {
            $blockContent = $this->_getContent($name, $block);
            $this->_classContent .= self::phpRow('public function _' . $name . 'Block()');
            $this->_classContent .= self::phpRow('{');
            $this->_classContent .= self::phpRow(' ?>' . $blockContent . '<?php ', 2);
            $this->_classContent .= self::phpRow('}', 1, 2);
            $this->_variables = array_merge($this->_variables, $block->getVariables());
        }

        unset($name, $block, $mainBlock, $blockContent);
    }

    private function _getContent($name, $block)
    {
        if ( empty($this->_extendedClass) ) {
            $content = preg_replace('/{% parent %}/', '', $block->getContent());
        } else {
            $text = '<?php parent::_' . $name . 'Block(); ?>';
            $content = preg_replace('/{% parent %}/', self::phpRow($text), $block->getContent());
        }

        unset($name, $block, $text);
        return $content;
    }

    private function _parseBlocks($block)
    {
        $startMatches = array();
        $endMatches = array();
        $blockMatches = array();

        preg_match_all('|({% block (.*) %})|U', $block, $startMatches, PREG_SET_ORDER);
        preg_match_all('|({% endblock(.*) %})|U', $block, $endMatches, PREG_SET_ORDER);

        if ( count($startMatches) != count($endMatches) ) {
            echo 'Template error in block counts';
            exit;
        }

        while ( count($startMatches) > 0 ) {
            foreach ( $startMatches as $blocks ) {
                $text = '|({% block ' . $blocks[2] . ' %})([\s\S]*)({% endblock(.*) %})|U';
                preg_match_all($text, $block, $blockMatches, PREG_SET_ORDER);

                foreach ( $blockMatches as $subBlock ) {
                    if ( 0 === preg_match_all('/{% block (.*) %}/', $subBlock[2], $aSubBlockMatches) ) {
                        $this->_blocks[$blocks[2]] = new Block($subBlock);
                        $block = str_replace($subBlock[0], '<?php $this->_' . $blocks[2] . 'Block(); ?>', $block);
                    }
                }
            }

            preg_match_all('|({% block (.*) %})|U', $block, $startMatches, PREG_SET_ORDER);
        }

        unset($startMatches, $endMatches, $blockMatches, $blocks, $subBlock, $text);
        return $block;
    }

    private function _addFunctions()
    {
        foreach ( $this->_variables as $variable ) {

            $functionName = $variable->getFunctionName();

            if ( !empty($functionName) and ! in_array($functionName, $this->_functions) ) {
                $this->_classContent .= $variable->getFunction();
                $this->_functions[] = $functionName;
            }
        }

        unset($variable, $functionName);
    }

    private function _useBlocks()
    {
        $matches = array();
        preg_match_all('/{{ (block)(\(["\'])(.*)(["\']\)) }}/U', $this->_classContent, $matches, PREG_SET_ORDER);

        foreach ( $matches as $variable ) {
            $block = '<?php $this->_' . $variable[3] . 'Block(); ?>';
            $this->_classContent = str_replace($variable[0], $block, $this->_classContent);
        }

        unset($matches, $variable, $block);
    }

    public static function phpRow($string, $tab = 1, $eol = 1)
    {
        for ( $n = 1; $n <= $tab; $n++ ) {
            $string = "\t" . $string;
        }

        for ( $n = 1; $n <= $eol; $n++ ) {
            $string .= "\n";
        }

        unset($tab, $eol);
        return $string;
    }

    private function _addApplicationData()
    {
        $this->_data['app'] = array(
          'request' => $this->_addRequest(),
          'storage' => $this->_addData(Storage::getData()),
          'constant' => $this->_addConstants()      
        );
    }

    private function _addRequest()
    {
        $request = $this->_getRequest();

        $formatedRequest = array(
          'requestTime' => $request->getRequestTime(),
          'requestUri' => $request->getRequestUri(),
          'requestMethod' => $request->getRequestMethod(),
          'clientIp' => $request->getClientIP(),
          'scriptName' => $request->getScriptName(),
          'pathInfo' => $request->getPathInfo(),
          'redirectStatus' => $request->getRedirectStatus(),
          'host' => $request->getHost(),
          'userAgent' => $request->getUserAgent(),
          'userAgent' => $request->getUserAgent(),
          'languages' => $request->getLanguages(),
          'protocol' => $request->getProtocol(),
          'serverName' => $request->getServerName(),
          'queryString' => $request->getQueryString(),
          'baseUrl' => $request->getBaseUrl(),
          'fullUrl' => $request->getFullUrl(),
          'shortUrl' => $request->getShortUrl(),
          'url' => $request->getURL(),
          'crumb' => $request->getCrumb(),
          'crumbCount' => $request->getCrumbCount(),
          'isAjax' => $request->isAjax(),
          'isSafe' => $request->isSafe(),
          'get' => $this->_addData($request->get->getData()),
          'post' => $this->_addData($request->post->getData()),
          'files' => $this->_addData($request->files->getData()),
          'cookie' => $this->_addData($request->cookie->getData()),
          'server' => $this->_addData($request->server->getData()),
        );

        return $formatedRequest;
    }
    
    private function _addConstants()
    {
        $constants = get_defined_constants(TRUE);
        
        return $constants['user'];
    }

    private function _getRequest()
    {
        if ( Storage::isRequest() ) {
            $request = Storage::Request();
        } else {
            $request = new Request;
        }

        return $request;
    }

    private function _addData($data)
    {
        $return = array();

        foreach ( $data as $key => $value ) {
            $return[$key] = $value;
        }

        unset($data, $key, $value);
        return $return;
    }

}

# End of file