<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 In-Cite Solution <technique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Form manager for 'ics_opendata'
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormManager
{
    // === ATTRIBUTS ============================================================================== //
	
	private $_formmenu = null;
	private $_formsid = Array();
	private $_formsclass = Array();
	private $_nbforms = null;
	private $_firstformid = null;
	private $_lastformid = null;
	
	private $_extkey = 'ics_opendata';
	
    // === OPERATIONS ============================================================================= //

    /**
     * Constructor for the FormManager : Initialize and define $formsid linked to $formsclass
     *
     * @return -
     */
    public function __construct()
    {
		$this->_formsid[] = 'source';
		$this->_formsclass['source'] = 'EXT:ics_opendata/lib/forms/class.form_source.php:&tx_icsopendata_FormSource';
		$this->_formsid[] = 'general';
		$this->_formsclass['general'] = 'EXT:ics_opendata/lib/forms/class.form_general.php:&tx_icsopendata_FormGeneral';
		$this->_formsid[] = 'sourceprofile';
		$this->_formsclass['sourceprofile'] = 'EXT:ics_opendata/lib/forms/class.form_sourceprofile.php:&tx_icsopendata_FormSourceProfile';
		$this->_formsid[] = 'tableprofile';
		$this->_formsclass['tableprofile'] = 'EXT:ics_opendata/lib/forms/class.form_tableprofile.php:&tx_icsopendata_FormTableProfile';
		$this->_formsid[] = 'linkcustom';
		$this->_formsclass['linkcustom'] = 'EXT:ics_opendata/lib/forms/class.form_linkcustom.php:&tx_icsopendata_FormLinkCustom';
		$this->_formsid[] = 'commandprofile';
		$this->_formsclass['commandprofile'] = 'EXT:ics_opendata/lib/forms/class.form_commandprofile.php:&tx_icsopendata_FormCommandProfile';
		$this->_formsid[] = 'filter';
		$this->_formsclass['filter'] = 'EXT:ics_opendata/lib/forms/class.form_filter.php:&tx_icsopendata_FormFilter';
		
		$this->_formsid[] = 'sumup';
		$this->_formsclass['sumup'] = 'EXT:ics_opendata/lib/forms/class.form_sumup.php:&tx_icsopendata_FormSumUp';
		$this->_formsid[] = 'result';
		$this->_formsclass['result'] = 'EXT:ics_opendata/lib/forms/class.form_result.php:&tx_icsopendata_FormResult';
		
		$this->_formsid[] = 'loadext';
		$this->_formsclass['loadext'] = 'EXT:ics_opendata/lib/forms/class.form_loadextension.php:&tx_icsopendata_FormLoadExtension';
		
		// Sources
		$sourceclass = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources'];
		foreach($sourceclass as $src) {
			$this->_formsid[] = $src['formsourceid'];
			$this->_formsclass[$src['formsourceid']] = $src['formsource'];
		}
		
		// Menu
		$this->_formmenu = t3lib_div::makeInstance('tx_icsopendata_FormMenu');
		
		$this->_nbforms = sizeof($this->_formsclass);
		$this->_firstformid = 'source';
		$this->_lastformid = 'result';
    }

	
    /**
     * getForm : return the form corresponding to the parameter $FormId
     *
     * @param  String $FormId
     * @return tx_icsopendata_form;
     */
    public function getForm($FormId)
    {
		if(!empty($this->_formsclass[$FormId])){
			$class = $this->_formsclass[$FormId];
			$form = t3lib_div::getUserObj($class, false);
		}
		
		return $form;
    }
	
    /**
     * getFirstFormId : return id of the first form
     *
     * @return tx_icsopendata_form
     */
    public function getFirstFormId()
    {
		return $this->_firstformid;
    }
	
    /**
     * getLastFormId : return id of the last form
     *
     * @return tx_icsopendata_form
     */
    public function getLastFormId()
    {
		return $this->_lastformid;
    }
	
    /**
     * getMenu : return form menu
     *
     * @return tx_icsopendata_form
	 */
    public function getFormMenu()
    {
		return $this->_formmenu;
    }
	
    /**
     * nextAction : return the id of the next form to render
     *
     * @param  String $FormId : id of the current form
     * @param  String $Action next/analyse/generate 
     * @return String $nextid
     */
    public function nextAction($FormId, $Action)
    {		
		$menuid = $this->_formmenu->getNextFormId();
		if(empty($menuid)) {
			$formid = $FormId;
			$form = $this->getForm($FormId);
			if(!empty($form)){
				if($form->validInput()){
					$this->saveFormData($FormId);
					if($Action == 'analyze')
						$form->analyze();
					if($Action == 'link')
						$form->link();
				}
				$formid = $form->getNextFormId();
			}
		}
		else {
			$formid = $this->_formmenu->getNextFormId();
			$GLOBALS['repository']->set('errors',array());
		}

		return $formid;
    }
	
	/**
	* saveFormData : save _POST data of the $formid form
	*
	* @param : String : $FormId
	* @return -
	*/
	public function saveFormData($FormId)
	{
		$post = t3lib_div::_POST();
			
		$data = Array();
		foreach($post as $key=>$value){
			if($key != 'repository' && $key != 'formaction')
				$data[$key] = $value;
		}
		
		$formsdata = $GLOBALS['repository']->get('formsdata');
		if(empty($formsdata))
			$formsdata = Array();
			
		$formsdata[$FormId] = $data;	
		$GLOBALS['repository']->set('formsdata',$formsdata);
	}
	
	
	/**
	* getFormData : return data of the $FormId form
	*
	* @param : String $FormId
	* @return Array(key=>value), Array() if form data are not set
	*/
	public function getFormData($FormId){
		$formsdata = $GLOBALS['repository']->get('formsdata');
		if(empty($formsdata) || !isset($formsdata[$FormId]))
			return Array();
			
		return $formsdata[$FormId];
	}


} /* End of class tx_icsopendata_FormManager */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_manager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_manager.php']);
}

?>