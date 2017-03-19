<?php
/**
 * Controller class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Controller
 * @filesource
 */

namespace Luki;

use Luki\Cache;
use Luki\Headers;
use Luki\Loader;
use Luki\Storage;
use Luki\Template;

class Controller
{
    protected $renderAllowed = true;
    protected $models = array();
    protected $output = '';
    protected $data = array();
    protected $methods = array();
    protected $template = '';
    protected $endProgramAfterRender = true;
    protected $route = array();
    protected $useCache = true;
    protected $expiration = 0;
    public $headers;

    function __construct()
    {
        $this->route = explode('\\',
                get_class($this));
        $this->headers = new Headers();

        $this->setDefaultTemplate();
        $this->setDefaultModel();
        $this->setExpiration(Cache::EXPIRE_IN_DAY);
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function __call($name, $arguments = array())
    {
        $result = null;

        foreach ($this->methods as $model => $methods) {
            if (in_array($name,
                            $methods)) {
                $result = call_user_func_array(array($this->models[$model], $name),
                        $arguments);
                break;
            }
        }

        return $result;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            $value = $this->data[$name];
        } else {
            $value = null;
        }

        return $value;
    }

    public function __isset($name)
    {
        $isSet = isset($this->data[$name]);

        return $isSet;
    }

    public function __unset($name)
    {
        unset($this->data[$name],
                $name);
    }

    public function preDispatch()
    {
        return $this;
    }

    public function postDispatch()
    {
        if ($this->renderAllowed) {
            $this->Render();
        }

        return $this;
    }

    public function getTemplateName()
    {
        return $this->template;
    }

    public function changeTemplateName($template)
    {
        $this->template = $template;

        return $this;
    }

    public function removeModel($model)
    {
        if (isset($this->models[$model])) {
            unset($this->models[$model],
                    $this->methods[$model]);
        }

        return $this;
    }

    public function noRender()
    {
        $this->renderAllowed = false;

        return $this;
    }

    public function Render()
    {
        $this->output = null;

        if (Storage::isSaved('Cache') and $this->useCache) {
            $hash = $this->template.'_'.md5(json_encode($this->data));
            $this->output = Storage::Cache()->Get($hash);
        }

        if (empty($this->output)) {
            $oTemplate = new Template($this->template,
                    $this->data);
            $this->output = $oTemplate->Render();

            if (Storage::isSaved('Cache') and $this->useCache) {
                Storage::Cache()->Set($hash,
                        $this->output,
                        $this->expiration);
            }
        }

        return $this;
    }

    public function getOutput()
    {
        $this->headers->setHeaders();

        return $this->output;
    }

    public function Show()
    {
        echo $this->Render()
                ->getOutput();

        if ($this->endProgramAfterRender) {
            exit;
        }

        return $this;
    }

    public function noEndProgramAfterRender()
    {
        $this->endProgramAfterRender = false;

        return $this;
    }

    private function setDefaultTemplate()
    {
        if (!empty($this->route[0]) and ! empty($this->route[1])) {
            $this->template = Loader::isFile($this->route[0].'/template/'.$this->route[1].'.twig');
        }
    }

    private function setDefaultModel()
    {
        if (!empty($this->route[0]) and ! empty($this->route[1])) {
            $this->addModel($this->route[0].'_model_'.$this->route[1]);
        }
    }

    public function addModel($model)
    {
        $modelClassFileWithPath = Loader::isClass($model);

        if (!empty($modelClassFileWithPath)) {
            $modelWithPath = '\\'.preg_replace('/_/',
                            '\\',
                            $model);
            $this->models[$model] = new $modelWithPath;
            $this->methods[$model] = get_class_methods($this->models[$model]);
        }

        return $this;
    }

    public function enableCache()
    {
        $this->useCache = true;
    }

    public function disableCache()
    {
        $this->useCache = false;
    }

    public function setExpiration($expiration)
    {
        $this->expiration = (int) $expiration;
    }
}