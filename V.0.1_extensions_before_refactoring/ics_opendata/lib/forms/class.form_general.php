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
 * Form : general informations about the new opendata extension
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormGeneral
        implements tx_icsopendata_Form
{
    // === ATTRIBUTS ============================================================================== //
	
	private $_extkey = 'ics_opendata';
	
    // === OPERATIONS ============================================================================= //

    /**
     * Inputs validation for the current form
	 * set $errors array if inputs are not valid
     *
     * @return Boolean
     */
    public function validInput()
    {
		$post = t3lib_div::_POST();
		$errors = Array();

		$GLOBALS['repository']->set('errors',$errors);
		
		if(!empty($errors))
			return false;
			 
        return true;
    }
	
    /**
     * Retrieve the content of the current form
     *
     * @param  $FormData : Data POST saved
     * @return String
     */
    public function renderForm($FormData, $pObj)
    {
		// Get data
		$post = t3lib_div::_POST();		
		
		// Main content header
		$content .= '
					<h2>' . $GLOBALS['LANG']->getLL('general.title') . '</h2>';

		
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
		
		if( isset($post['updategeneral']) && empty($errors) ) {
			$GLOBALS['repository']->set('extensiontitle', htmlspecialchars($post['extensiontitle']));
			$GLOBALS['repository']->set('extensiondescription', htmlspecialchars($post['extensiondescription']));
			$GLOBALS['repository']->set('authorname', htmlspecialchars($post['authorname']));
			$GLOBALS['repository']->set('authoremail', htmlspecialchars($post['authoremail']));
			
		}
		$exttitle = $GLOBALS['repository']->get('extensiontitle');
		$extdescription = $GLOBALS['repository']->get('extensiondescription');
		$authorname = $GLOBALS['repository']->get('authorname');
		$authoremail = $GLOBALS['repository']->get('authoremail');
		
		
		// Main content inputs
		$content .= '
					<h3>' . $GLOBALS['LANG']->getLL('general.help') . '</h3>';
					
		$content .= '
					<fieldset>
						<legend>
							' . $GLOBALS['LANG']->getLL('general.information') . '
						</legend>
						<table>
							<tbody>
								<tr>
									<td>
										' . $GLOBALS['LANG']->getLL('general.exttitle') . ' : 
									</td>
									<td>
										<input type="text" name="extensiontitle" value="' . $exttitle . '">
									</td>
								</tr>
								<tr>
									<td>
										' . $GLOBALS['LANG']->getLL('general.extdescription') . ' : 
									</td>
									<td>
										<textarea name="extensiondescription" rows=8 cols=60 >' . $extdescription . '</textarea>
									</td>
								</tr>
								<tr>
									<td>
										' . $GLOBALS['LANG']->getLL('general.authorname') . ' : 
									</td>
									<td>
										<input type="text" name="authorname" value="' . $authorname . '">
									</td>
								</tr>
								<tr>
									<td>
										' . $GLOBALS['LANG']->getLL('general.authoremail') . ' : 
									</td>
									<td>
										<input type="text" name="authoremail" value="' . $authoremail . '">
									</td>
								</tr>
							</tbody>
						</table>
					</fieldset>';
						
		$content .= '
					<input type="submit" name="updategeneral" value="Update">';
		
        return $content;
    } // End renderForm
	
    /**
     * Retrieve the id of the next form, depending on inputs
     *
     * @return String
     */
    public function getNextFormId(){
		$errors = $GLOBALS['repository']->get('errors');
		$post = t3lib_div::_POST();
		
		if(!empty($errors)){
			return 'general';
		}
			
		return 'general';
	}

} /* end of class tx_icsopendata_FormGeneral */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_general.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_general.php']);
}

?>