<?php

/**
 * Menu Navigation Format
 *
 * Luki framework
 * Date 17.12.2012
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

namespace Luki\Navigation\Format;

use Luki\Navigation;
use Luki\Navigation\Format\basicInterface;

/**
 * Menu Navigation Format
 * 
 * @package Luki
 */
class Menu implements basicInterface
{

    private $_parentLevel = 'ul';
    private $_parentString = '<%ParentLevel% id="%ParentID%" class="%ParentClass%">%Content%</%ParentLevel%>';
    private $_childLevel = 'li';
    private $_childString = '<%ChildLevel% id="%ChildID%" class="%ChildClass% %hidden%">%Content%%ChildContent%</%ChildLevel%>';
    private $_format = '<a href="%url%" title="%title%" class="%class% %active%" target="%target%">%label%</a>';
    private $_navigation = NULL;
    private $_start = 0;
    private $_isStarted = FALSE;
    private $_isAll = FALSE;
    private $_id = '';
    private $_class = '';
    private $_used = array(
      'label',
      'title',
      'class',
      'target',
      'active'
    );
    private $_options = array(
      'parentLevel' => '_parentLevel',
      'parentString' => '_parentString',
      'childLevel' => '_childLevel',
      'childString' => '_childString',
      'start' => '_start',
      'format' => '_format',
      'all' => '_isAll',
      'id' => '_id',
      'class' => '_class',
    );

    public function __construct(Navigation $navigation)
    {
        $this->_navigation = $navigation;

        unset($navigation);
    }

    public function setFormat($format)
    {
        $this->_format = $format;

        unset($format);
        return $this;
    }

    public function Format($options = array())
    {
        $content = '';

        if ( !empty($options) ) {
            $this->_setupOptions($options);
        }

        foreach ( $this->_navigation->getNavigation() as $item ) {

            if ( '' == $item->hidden or $this->_isAll ) {
                $content .= $this->_childLevel($item, $item->crumb);
            }
        }

        $formatedContent = preg_replace('/%Content%/', $content, $this->_parentLevel($this->_id, $this->_class));

        unset($options, $content, $item);
        return $formatedContent;
    }

    private function _setupOptions($options)
    {

        foreach ( $options as $key => $value ) {
            if ( !empty($this->_options[$key]) ) {
                $optionsKey = $this->_options[$key];
                $this->$optionsKey = $value;
            }
        }

        unset($options, $key, $value, $optionsKey);
    }

    private function _format($item, $crumb)
    {
        $format = $this->_format;

        foreach ( $this->_used as $key ) {
            $format = preg_replace('/%' . $key . '%/', $item->$key, $format);
        }

        $formatedUrl = preg_replace('/%url%/', $crumb, $format);

        unset($item, $crumb, $format, $key);
        return $formatedUrl;
    }

    private function _childLevel($item, $crumb = '')
    {
        $formatedChildLevel = '';

        if ( '' != $item->hidden and ! $this->_isAll ) {
            return $formatedChildLevel;
        }

        if ( !$this->_isStarted and $this->_start > 0 ) {
            if ( $item->_id == $this->_start ) {
                $this->_isStarted = TRUE;
            }
        }

        if ( 0 == $this->_start or $this->_isStarted ) {
            $from = array('/%hidden%/', '/%ChildLevel%/', '/%ChildID%/', '/%ChildClass%/', '/%Content%/');
            $to = array($item->hidden, $this->_childLevel, $item->_id, $item->_class, $this->_format($item, $crumb));
            
            $formatedChildLevel = preg_replace($from, $to, $this->_childString);
        }

        $childContent = '';
        $navigation = $item->getNavigation();
        if ( count($navigation) > 0 ) {

            foreach ( $navigation as $childItem ) {
                $childContent .= $this->_childLevel($childItem, $crumb . '/' . $childItem->crumb);
            }

            if ( 0 == $this->_start or $this->_isStarted ) {
                $childContent = preg_replace('/%Content%/', $childContent, $this->_parentLevel($item->_id, 'sub'));
            }                            
        }
        
        $formatedChildLevel = $this->_sanitize(preg_replace('/%ChildContent%/', $childContent, $formatedChildLevel));            

        if ( $this->_isStarted and $this->_start > 0 ) {
            if ( $item->_id == $this->_start ) {
                $this->_isStarted = FALSE;
            }
        }

        unset($item, $crumb, $navigation, $childContent, $childItem, $from, $to);
        return $formatedChildLevel;
    }

    private function _parentLevel($id = '', $class = '')
    {
        $from = array('/%ParentLevel%/', '/%ParentID%/', '/%ParentClass%/');
        $to = array($this->_parentLevel, $id, $class);
        
        $formatedParentLevel = $this->_sanitize(preg_replace($from, $to, $this->_parentString));       
        
        unset($id, $class, $from, $to);
        return $formatedParentLevel;
    }
    
    private function _sanitize($text)
    {
        $sanitizeFrom = array('/ id=""/', '/ class=""/', '/ class=" "/', '/ title=""/');
        $sanitizeTo = array('', '', '', '');
        
        $output = preg_replace($sanitizeFrom, $sanitizeTo, $text);

        unset($text, $sanitizeFrom, $sanitizeTo);
        return $output;
    }

}

# End of file