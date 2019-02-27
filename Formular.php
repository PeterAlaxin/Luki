<?php
/**
 * Formular class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Formular
 * @filesource
 */

namespace Luki;

use Luki\Formular\Hidden;
use Luki\Security;
use Luki\Storage;
use Luki\Url;

class Formular
{
    private $name          = '';
    private $id            = '';
    private $method        = 'post';
    private $inputs        = array();
    private $enctype       = 'application/x-www-form-urlencoded';
    private $hasFocus      = true;
    private $action        = '';
    private $errors        = array();
    private $autocomplete  = 'on';
    private $noValidate    = false;
    private $target        = '_self';
    private $class         = '';
    private $tokenValidity = 300;
    private $tokenName     = '';

    public function __construct($name)
    {
        $this->name = $name;
        $this->id   = $name.'_id';
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setMethod($method)
    {
        if (in_array($method, array('post', 'get'))) {
            $this->method = $method;
        }

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setEnctype($enctype)
    {
        if (in_array($enctype, array('application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'))) {
            $this->enctype = $enctype;
        }

        return $this;
    }

    public function getEnctype()
    {
        return $this->enctype;
    }

    public function setFocus($focus)
    {
        $this->hasFocus = (bool) $focus;

        return $this;
    }

    public function getFocus()
    {
        return $this->hasFocus;
    }

    public function setAction($action)
    {
        $this->action = (string) $action;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAutocomplete($autocomplete)
    {
        if (in_array($autocomplete, array('on', 'off'))) {
            $this->autocomplete = $autocomplete;
        }

        return $this;
    }

    public function getAutocomplete()
    {
        return $this->autocomplete;
    }

    public function setNovalidate()
    {
        $this->noValidate = true;

        return $this;
    }

    public function getNovalidate()
    {
        return $this->noValidate;
    }

    public function setTarget($target)
    {
        if (in_array($target, array('_blank', '_self', '_parent', '_top'))) {
            $this->target = $target;
        }

        return $this;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function addInput($input)
    {
        $this->inputs[$input->getName()] = $input;
        $this->errors[$input->getName()] = array();

        return $this;
    }

    public function getInput($name)
    {
        $input = $this->inputs[$name];

        return $input;
    }

    public function setInput($name, $input)
    {
        $this->inputs[$name] = $input;

        return $this;
    }

    public function getInputs()
    {
        $inputs = array();

        foreach ($this->inputs as $name => $input) {
            $inputs[$name] = $input->getHtml();
        }

        return $inputs;
    }

    public function fillValues($values)
    {
        foreach ($values as $name => $value) {
            if (array_key_exists($name, $this->inputs)) {
                $this->inputs[$name]->setValue($value);
            }
        }

        return $this;
    }

    public function isValid()
    {
        $valid = true;

        foreach ($this->inputs as $name => $input) {
            if (!$input->isValid()) {
                $this->errors[$name] = $input->getErrors();
                $valid               = false;
            }
        }

        return $valid;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFormular()
    {
        $formular = array(
            'form'    => $this->getFormularHeader(),
            'inputs'  => $this->getInputs(),
            'errors'  => $this->getErrors(),
            'timeout' => $this->getTokenValidity()
        );

        return $formular;
    }

    public function getFormularHeader()
    {
        $header = '<form ';
        $header .= 'action="'.$this->getAction().'" ';
        $header .= 'autocomplete="'.$this->getAutocomplete().'" ';
        $header .= 'enctype="'.$this->getEnctype().'" ';
        $header .= 'id="'.$this->getId().'" ';
        $header .= 'method="'.$this->getMethod().'" ';
        $header .= 'name="'.$this->getName().'" ';
        $header .= 'target="'.$this->getTarget().'" ';
        $header .= 'class="'.$this->getClass().'" ';

        if ($this->getNovalidate()) {
            $header .= 'novalidate ';
        }

        $header .= '>';

        return $header;
    }

    public function getResult()
    {
        if (empty($this->errors)) {
            $return = array('status' => 0);
        } else {
            $return = array('status' => 1);
            foreach ($this->getErrors() as $field => $errors) {
                $return['fields'][]       = $field;
                $return['errors'][$field] = $errors;
            }
        }

        return $return;
    }

    public function setTokenValidity($value = 600)
    {
        $this->tokenValidity = (int) $value;

        return $this;
    }

    public function getTokenValidity()
    {
        return $this->tokenValidity;
    }

    public function addToken($data, $id = 0)
    {
        $this->tokenName = $this->name.'_'.$id.'_token';
        $token           = new Hidden('__token', 'Secure Token');

        if (empty($data['__token'])) {
            $token->setValue($this->generateToken());
        } else {
            $token->setValue($data['__token']);
        }
        $this->addInput($token);

        return $this;
    }

    public function verifyToken()
    {
        if (empty($this->inputs['__token'])) {
            return true;
        }

        $tokenFromFromular = $this->inputs['__token']->getValue();
        $tokenFromCache    = Storage::Cache()->Get($this->tokenName);
        $isValid           = false;

        if (Security::passwordVerify(Storage::Request()->getFullUrl(), $tokenFromFromular)) {
            if ($tokenFromFromular === $tokenFromCache) {
                $isValid = true;
            }
        } else {
            Url::Reload('https://en.wikipedia.org/wiki/Cross-site_request_forgery');
        }

        $this->inputs['__token']->setValue($this->generateToken());

        return $isValid;
    }

    private function generateToken()
    {
        $hash = Security::passwordHash(Storage::Request()->getFullUrl());
        Storage::Cache()->Set($this->tokenName, $hash, $this->tokenValidity);

        return $hash;
    }

    public function getToken()
    {
        $token = Storage::Cache()->Get($this->tokenName);

        return $token;
    }
}