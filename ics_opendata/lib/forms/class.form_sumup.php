<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 In Cité Solution <technique@in-cite.net>
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
/*
 * $Id$
 */

/**
 * Form : Display a summary of all form data
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormSumUp
        implements tx_icsopendata_Form
{

    // === ATTRIBUTS ============================================================================== //

	private $_extkey = 'ics_opendata';
	
    // === OPERATIONS ============================================================================= //

    /**
     * Short description of method validInput
     *
     * @return Integer
     */
    public function validInput()
    {
        return true;
    }

    /**
     * Short description of method renderForm
     *
     * @param  $FormData
     * @return String
     */
    public function renderForm($FormData, $pObj)
    {
		$content = '';
		
		// Get data and base xml
		$post = t3lib_div::_POST();
		$basexml = $GLOBALS['repository']->get('basexml');
		
		// Main content header
		$content .= "<h2>" . $GLOBALS['LANG']->getLL('sumup.title') . "</h2>";
		$content .= "<input type = 'hidden' name='action' value='next'/>";
		
		// Display Error
		$errors = $GLOBALS['repository']->get('errors');
		if(isset($errors) && !empty($errors)){
			$content .= '<div class="error">';
			foreach($errors as $err){
				$content .= "<p> ERROR : " . $err . "</p>";
			}
			$content .= '</div>';
		}
		
		$content .= "<h3>" . $GLOBALS['LANG']->getLL('sumup.help') . "</h3>";
		// Source description
				
		// Xml elements
		if(!empty($basexml)) {
			$content .= '<div class="xmlelement">
							<h3>XML Element : </h3>' . 
							$basexml->printElement() . '
						</div>';
		}
		else {
			$content .= 'No items selected';
		}
		
		// Commands
		
        return $content;
    }

    /**
     * Short description of method getNextForm
     *
     * @return Integer
     */
    public function getNextFormId()
    {
		$errors = $GLOBALS['repository']->get('errors');
		$post = t3lib_div::_POST();
		
		if(isset($post['formaction'])) {
			if ($post['formaction'] == 'sourceparams'){
				$sourcetype = $GLOBALS['repository']->get('sourcetype');
				return $GLOBALS['repository']->getSourceFormId($sourcetype,'formsourceid');
			}
			return $post['formaction'];
		}
		
		if(!empty($errors))
			return 'linkparams';
		
        return 'generation';
    }


} /* end of class tx_icsopendata_FormSumUp */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_sumup.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_sumup.php']);
}

?>