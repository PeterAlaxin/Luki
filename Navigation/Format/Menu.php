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

/**
 * Menu Navigation Format
 * 
 * @package Luki
 */
class Luki_Navigation_Format_Menu implements Luki_Navigation_Format_Interface {

	private $sParentLevel = 'ul';
	private $sParentString = '<%ParentLevel% id="%ParentID%" class="%ParentClass%">%Content%</%ParentLevel%>';
	private $sChildLevel = 'li';
	private $sChildString = '<%ChildLevel% id="%ChildID%" class="%ChildClass% %hidden%">%Content%</%ChildLevel%>';
	private $sFormat = '<a href="%url%" title="%title%" class="%class% %active%" target="%target%">%label%</a>';
	private $oNavigation = NULL;
	private $nStart = 0;
	private $bStarted = FALSE;
	private $bAll = FALSE;
	private $sID = '';
	private $sClass = '';
	private $aUsed = array(
		'label',
		'title',
		'class',
		'target',
		'active'
	);
	private $aOptions = array(
		'parentLevel' => 'sParentLevel',
		'parentString' => 'sParentString',
		'childLevel' => 'sChildLevel',
		'childString' => 'sChildString',
		'start' => 'nStart',
		'format' => 'sFormat',
		'all' => 'bAll',
		'id' => 'sID',
		'class' => 'sClass',
	);

	public function __construct($oNavigation)
	{
		$this->oNavigation = $oNavigation;

		unset($oNavigation);
	}

	public function setFormat($sFormat)
	{
		$this->sFormat = $sFormat;

		unset($sFormat);
		return $this;
	}

	public function Format($aOptions = array())
	{
		$sContent = '';

		if(!empty($aOptions)) {
			$this->_setupOptions($aOptions);
		}

		foreach ($this->oNavigation->getNavigation() as $oItem) {

			if('' == $oItem->hidden or $this->bAll) {
				$sContent .= $this->_childLevel($oItem, $oItem->crumb);
			}
		}

		$sReturn = preg_replace('/%Content%/', $sContent, $this->_parentLevel($this->sID, $this->sClass));

		unset($aOptions, $sContent, $oItem);
		return $sReturn;
	}

	private function _setupOptions($aOptions)
	{

		foreach ($aOptions as $sKey => $sValue) {
			if(!empty($this->aOptions[$sKey])) {
				$sOptionsKey = $this->aOptions[$sKey];
				$this->$sOptionsKey = $sValue;
			}
		}

		unset($aOptions, $sKey, $sValue);
	}

	private function _format($oItem, $sCrumb)
	{
		$sFormat = $this->sFormat;

		foreach ($this->aUsed as $sKey) {
			$sFormat = preg_replace('/%' . $sKey . '%/', $oItem->$sKey, $sFormat);
		}

		$sReturn = preg_replace('/%url%/', $sCrumb, $sFormat);

		unset($oItem, $sCrumb, $sFormat, $sKey);
		return $sReturn;
	}

	private function _childLevel($oItem, $sCrumb = '')
	{
		$sReturn = '';

		if('' != $oItem->hidden and !$this->bAll) {
			return $sReturn;
		}

		if(!$this->bStarted and $this->nStart > 0) {
			if($oItem->id == $this->nStart) {
				$this->bStarted = TRUE;
			}
		}

		if(0 == $this->nStart or $this->bStarted) {
			$sReturn = preg_replace('/%hidden%/', $oItem->hidden, $this->sChildString);
			$sReturn = preg_replace('/%ChildLevel%/', $this->sChildLevel, $sReturn);
			$sReturn = preg_replace('/%ChildID%/', $oItem->id, $sReturn);
			$sReturn = preg_replace('/%ChildClass%/', $oItem->class, $sReturn);
			$sReturn = preg_replace('/%Content%/', $this->_format($oItem, $sCrumb), $sReturn);
		}

		$aNavigation = $oItem->getNavigation();

		if(count($aNavigation) > 0) {
			$sContentChild = '';

			foreach ($aNavigation as $oItemChild) {
				$sContentChild .= $this->_childLevel($oItemChild, $sCrumb . '/' . $oItemChild->crumb);
			}

			if(0 == $this->nStart or $this->bStarted) {
				$sReturn .= preg_replace('/%Content%/', $sContentChild, $this->_parentLevel($oItem->id, 'sub'));
			}
			else {
				$sReturn .= $sContentChild;
			}
		}

		if($this->bStarted and $this->nStart > 0) {
			if($oItem->id == $this->nStart) {
				$this->bStarted = FALSE;
			}
		}

		unset($oItem, $sCrumb, $aNavigation, $sContentChild, $oItemChild);
		return $sReturn;
	}

	private function _parentLevel($sID = '', $sClass = '')
	{
		$sReturn = preg_replace('/%ParentLevel%/', $this->sParentLevel, $this->sParentString);
		$sReturn = preg_replace('/%ParentID%/', $sID, $sReturn);
		$sReturn = preg_replace('/%ParentClass%/', $sClass, $sReturn);

		unset($sID, $sClass);
		return $sReturn;
	}

}

# End of file