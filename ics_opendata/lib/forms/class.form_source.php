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
 * Form : Choose source type
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormSource
        implements tx_icsopendata_Form
{
    
	// === ATTRIBUTS ============================================================================== //
	
	private $_extkey = 'ics_opendata';
	
    // === OPERATIONS ============================================================================= //

    /**
     * validInput : Validate _POST data
     *
	 * @param Integer $FormId
     * @return boolean
     */
    public function validInput()
    {
		$errors = Array();
		$post = t3lib_div::_POST();
		if(empty($post['sourcetype']))
			$errors[] = "ERROR_SOURCE_TYPE";
		
		$GLOBALS['repository']->set('errors',$errors);
		
		if(!empty($errors))
			return false;
		
		return true;
    }

	
    /**
     * renderForm : return the main content of the form
     *
     * @param  Array(mixed) $FormData
     * @return String
     */
    public function renderForm($FormData, $pObj)
    {
        $content = '';
		
		// Get source list and sources classes
		$sourcelist = array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources']);
		
		// Main content header
		$content .= '
					<h2>' . $GLOBALS['LANG']->getLL('source.title') . '</h2>
					<input type = "hidden" name="action" value="next"/>
					<input type = "hidden" name="newsource" value="newsource"/>';
		
		// Display Error
		$errors = $GLOBALS['repository']->get('errors');
		if(isset($errors) && !empty($errors)){
			$content .= '
					<div class="error">';
			foreach($errors as $err){
				$content .= '
						<p> ERROR : ' . $err . '</p>';
			}
			$content .= '
					</div>';
		}
		
		$content .= '
					<h3>' . $GLOBALS['LANG']->getLL('source.help') . '</h3>';
		// Source list
		$content .= '
					<select name="sourcetype" onChange="submit()">
						<option value=""></option>';
						foreach($sourcelist as $src){
							$content .= '
						<option value="' . $src . '">' . $src . '</option>';
						}
		$content .= '
					</select>';
		
		return $content;
    }//End renderForm
	
    /**
     * getNextFormId : return the next form id depending on sourcetype
     *
     * @return String : next form id
     */
    public function getNextFormId()
    {
		$errors = $GLOBALS['repository']->get('errors');
		if(empty($errors)){
			$post = t3lib_div::_POST();
			return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources'][$post['sourcetype']]['formsourceid'];
		}
		
        return 'source';
    }
	
} /* end of class tx_icsopendata_FormSource */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_source.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_source.php']);
}

?>