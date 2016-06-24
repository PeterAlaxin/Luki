<?php

/**
 * Controller class
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

use Luki\Cache;
use Luki\Loader;
use Luki\Storage;
use Luki\Template;

/**
 * Controller class
 *
 * MVC Controller
 *
 * @package Luki
 */
class Controller
{

    protected $_renderAllowed = TRUE;
    protected $_models = array();
    protected $_output = '';
    protected $_data = array();
    protected $_methods = array();
    protected $_template = '';
    protected $_endProgramAfterRender = TRUE;
    protected $_route = array();
    protected $_useCache = TRUE;
    protected $_expiration = 0;

    function __construct()
    {
        $this->_route = explode('\\', get_class($this));

        $this->setDefaultTemplate();
        $this->setDefaultModel();
        $this->setExpiration(Cache::EXPIRE_IN_DAY);
    }

    public function __call($name, $arguments = array())
    {
        $result = NULL;

        foreach ( $this->_methods as $model => $methods ) {
            if ( in_array($name, $methods) ) {
                $result = call_user_func_array(array( $this->_models[$model], $name ), $arguments);
                break;
            }
        }

        unset($name, $arguments, $model, $methods);
        return $result;
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;

        unset($name, $value);
    }

    public function __get($name)
    {
        $value = NULL;

        if ( isset($this->_data[$name]) ) {
            $value = $this->_data[$name];
        }

        unset($name);
        return $value;
    }

    public function __isset($name)
    {
        $isSet = isset($this->_data[$name]);

        unset($name);
        return $isSet;
    }

    public function __unset($name)
    {
        unset($this->_data[$name], $name);
    }

    public function preDispatch()
    {
        return $this;
    }

    public function postDispatch()
    {
        if ( $this->_renderAllowed ) {
            $this->Render();
        }

        return $this;
    }

    public function getTemplateName()
    {
        return $this->_template;
    }

    public function changeTemplateName($newTemplateName)
    {
        $this->_template = $newTemplateName;

        unset($newTemplateName);
        return $this;
    }

    public function removeModel($model)
    {
        if ( isset($this->_models[$model]) ) {
            unset($this->_models[$model], $this->_methods[$model]);
        }

        unset($model);
        return $this;
    }

    public function noRender()
    {
        $this->_renderAllowed = FALSE;

        return $this;
    }

    public function Render()
    {
        $this->_output = NULL;

        if ( Storage::isSaved('Cache') and $this->_useCache ) {
            $hash = $this->_template . '_' . md5(json_encode($this->_data));
            $this->_output = Storage::Cache()->Get($hash);
        }

        if ( empty($this->_output) ) {
            $oTemplate = new Template($this->_template, $this->_data);
            $this->_output = $oTemplate->Render();

            if ( Storage::isSaved('Cache') and $this->_useCache ) {
                Storage::Cache()->Set($hash, $this->_output, $this->_expiration);
            }
        }

        unset($oTemplate, $hash);
        return $this;
    }

    public function getOutput()
    {
        return $this->_output;
    }

    public function Show()
    {
        echo $this->Render()
                ->getOutput();

        if ( $this->_endProgramAfterRender ) {
            exit;
        }

        return $this;
    }

    public function noEndProgramAfterRender()
    {
        $this->_endProgramAfterRender = FALSE;

        return $this;
    }

    private function setDefaultTemplate()
    {
        if ( !empty($this->_route[0]) and ! empty($this->_route[1]) ) {
            $this->_template = Loader::isFile($this->_route[0] . '/template/' . $this->_route[1] . '.twig');
        }
    }

    private function setDefaultModel()
    {
        if ( !empty($this->_route[0]) and ! empty($this->_route[1])
        ) {
            $this->addModel($this->_route[0] . '_model_' . $this->_route[1]);
        }
    }

    public function addModel($model)
    {
        $modelClassFileWithPath = Loader::isClass($model);

        if ( !empty($modelClassFileWithPath) ) {
            $modelWithPath = '\\' . preg_replace('/_/', '\\', $model);
            $this->_models[$model] = new $modelWithPath;
            $this->_methods[$model] = get_class_methods($this->_models[$model]);
        }

        unset($model, $modelClassFileWithPath, $modelWithPath);
        return $this;
    }

    public function enableCache()
    {
        $this->_useCache = TRUE;
    }

    public function disableCache()
    {
        $this->_useCache = FALSE;
    }

    public function setExpiration($expiration)
    {
        $this->_expiration = (int) $expiration;

        unset($expiration);
    }

}
