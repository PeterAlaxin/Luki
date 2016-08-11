<?php
/**
 * Menu Navigation Format
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Navigation
 * @filesource
 */

namespace Luki\Navigation\Format;

use Luki\Navigation\Format\BasicFactory;
use Luki\Navigation\Format\basicInterface;

/**
 * Menu Navigation Format
 * 
 * @package Luki
 */
class Menu extends BasicFactory implements basicInterface
{

    public $format = '<a href="%url%" title="%title%" class="%class% %active%" target="%target%">%label%</a>';
    private $parentLevel = 'ul';
    private $parentString = '<%ParentLevel% id="%ParentID%" class="%ParentClass%">%Content%</%ParentLevel%>';
    private $childLevel = 'li';
    private $childString = '<%ChildLevel% id="%ChildID%" class="%ChildClass% %hidden%">%Content%%ChildContent%</%ChildLevel%>';
    private $start = 0;
    private $isStarted = false;
    private $isAll = false;
    private $id = '';
    private $class = '';
    private $used = array('label', 'title', 'class', 'target', 'active');
    private $options = array(
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

    public function Format($options = array())
    {
        $content = '';

        if (!empty($options)) {
            $this->setupOptions($options);
        }

        foreach ($this->navigation->getNavigation() as $item) {

            if ('' == $item->hidden or $this->isAll) {
                $content .= $this->childLevel($item, $item->crumb);
            }
        }

        $formatedContent = preg_replace('/%Content%/', $content, $this->parentLevel($this->id, $this->class));

        return $formatedContent;
    }

    private function setupOptions($options)
    {

        foreach ($options as $key => $value) {
            if (!empty($this->options[$key])) {
                $optionsKey = $this->options[$key];
                $this->$optionsKey = $value;
            }
        }
    }

    private function formatUrl($item, $crumb)
    {
        $format = $this->format;

        foreach ($this->used as $key) {
            $format = preg_replace('/%' . $key . '%/', $item->$key, $format);
        }

        $formatedUrl = preg_replace('/%url%/', $crumb, $format);

        return $formatedUrl;
    }

    private function childLevel($item, $crumb = '')
    {
        $formatedChildLevel = '';

        if ('' != $item->hidden and ! $this->isAll) {
            return $formatedChildLevel;
        }

        if (!$this->isStarted and $this->start > 0) {
            if ($item->id == $this->start) {
                $this->isStarted = true;
            }
        }

        if (0 == $this->start or $this->isStarted) {
            $from = array('/%hidden%/', '/%ChildLevel%/', '/%ChildID%/', '/%ChildClass%/', '/%Content%/');
            $to = array($item->hidden, $this->childLevel, $item->id, $item->class, $this->formatUrl($item, $crumb));

            $formatedChildLevel = preg_replace($from, $to, $this->childString);
        }

        $childContent = '';
        $navigation = $item->getNavigation();
        if (count($navigation) > 0) {

            foreach ($navigation as $childItem) {
                $childContent .= $this->childLevel($childItem, $crumb . '/' . $childItem->crumb);
            }

            if (0 == $this->start or $this->isStarted) {
                $childContent = preg_replace('/%Content%/', $childContent, $this->parentLevel($item->id, 'sub'));
            }
        }

        $formatedChildLevel = $this->sanitizeText(preg_replace('/%ChildContent%/', $childContent, $formatedChildLevel));

        if ($this->isStarted and $this->start > 0) {
            if ($item->id == $this->start) {
                $this->isStarted = false;
            }
        }

        return $formatedChildLevel;
    }

    private function parentLevel($id = '', $class = '')
    {
        $from = array('/%ParentLevel%/', '/%ParentID%/', '/%ParentClass%/');
        $to = array($this->parentLevel, $id, $class);

        $formatedParentLevel = $this->sanitizeText(preg_replace($from, $to, $this->parentString));

        return $formatedParentLevel;
    }

    private function sanitizeText($text)
    {
        $sanitizeFrom = array('/ id=""/', '/ class=""/', '/ class=" "/', '/ title=""/');
        $sanitizeTo = array('', '', '', '');

        $output = preg_replace($sanitizeFrom, $sanitizeTo, $text);

        return $output;
    }
}
