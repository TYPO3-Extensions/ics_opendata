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
 * Form : list of source parameters and source tables
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormSourceProfile
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
		// Get data
		$post = t3lib_div::_POST();
		$sourceid = $post['sourceselected'];
		if ( empty($sourceid) || isset($post['sourcecreated']) ) {
			$sourceid = $GLOBALS['repository']->get('sourceid');
		}
		
		$source = $GLOBALS['repository']->getSourceData($sourceid);
		$sourcename = $source->getName();
		$sourcetype = $source->getType();
		
		// Unselect table (delete xml element)
		if( isset($post['deletetable']) ) {
			$sourceinfos = $GLOBALS['repository']->getSourceInfos($post['sourceselected']);
			$basexml = $GLOBALS['repository']->get('basexml');
			if( !empty($basexml) ) {
				$sourcexml = &$basexml->getXmlChild($post['sourceselected']);
				$sourcexml->setXmlChild($post['tabledeleted'], null);
			}
			
			unset($sourceinfos['selecteditems'][$post['tabledeleted']]);
			$GLOBALS['repository']->setSourceInfos($post['sourceselected'], $sourceinfos);
			$GLOBALS['repository']->set('basexml', $basexml);
		}
		
		// Main content header
		$content = '
					<h2>' . $GLOBALS['LANG']->getLL('sourceprofile.title') . '</h2>
					<input type="hidden" name="action" value="next"/>
					<input type="hidden" id="' . $this->_extkey . 'sourceselected" name="sourceselected" value="' . $sourceid . '">
					<input type="hidden" id="' . $this->_extkey . 'tableselected" name="tableselected">
					<input type="hidden" id="' . $this->_extkey . 'editsource" name="editsource">';
		
		
		// Display Error
		$errors = $GLOBALS['repository']->get('errors');
		if(isset($errors) && !empty($errors)){
			$content .= '
					<div class="error">';
			foreach($errors as $err){
				$content .= '<p> ERROR : ' . $err . '</p>';
			}
			$content .= '
					</div>';
		}
		
		$content .= '
					<h3>' . $GLOBALS['LANG']->getLL('sourceprofile.help') . '</h3>';
		// Parameters
		$content .= '
					<fieldset>
						<legend>'
							 . $GLOBALS['LANG']->getLL('sourceprofile.params') . '
							<a href="#" onclick="' . htmlspecialchars('
								document.getElementById("' . $this->_extkey . 'editsource").value = "1";
								document.getElementById("' . $this->_extkey . 'sourceselected").value = "' . $sourceid . '";
								document.getElementById("opendataform").submit();') . '">
								' . t3lib_iconWorks::getSpriteIcon('actions-document-open')  . '
							</a>
						</legend>
						<div class"printparams">
							' . $source->printParams() . '	
						</div>
					</fieldset>';
		
		// Tables
		$sourceinfos = $GLOBALS['repository']->getSourceInfos($sourceid);
		$selectedtables = $sourceinfos['selecteditems'];
		
		$content .= '
					<fieldset>
						<legend>' . $GLOBALS['LANG']->getLL('sourceprofile.tables') . '</legend>';

		// --- tables not selected
		$content .= '
						<div class="tablesnotselected">
							<h4>' . $GLOBALS['LANG']->getLL('sourceprofile.tables') . ' : </h4>';
		for($i = 0; $i<$source->countTables();$i++) {
			$table = $source->getTable($i);
			$tablename = $table->getName();
			if(!isset($selectedtables[$tablename])) {
				$content .= '
							<p>
								<a href="#" onclick="' . htmlspecialchars('
									document.getElementById("' . $this->_extkey . 'tableselected").value = "' . $tablename . '";
									document.getElementById("' . $this->_extkey . 'sourceselected").value = "' . $sourceid . '";
									document.getElementById("opendataform").submit();') . '">
									' . t3lib_iconWorks::getSpriteIcon('actions-edit-add')  . '' . $tablename . '
								</a>
							</p>';
			}
		}
		
		// --- selected tables
		if( !empty($selectedtables) ) {
			$content .= '
						</div>
						<div class="tablesselected">
							<h4>' . $GLOBALS['LANG']->getLL('sourceprofile.selectedtable') . ' : </h4>';
			foreach($selectedtables as $tablename=>$value) {
				$content .= '
							<p>
								<a href="#" onclick="' . htmlspecialchars('
									document.getElementById("' . $this->_extkey . 'tableselected").value = "' . $tablename . '";
									document.getElementById("' . $this->_extkey . 'sourceselected").value = "' . $sourceid . '";
									document.getElementById("opendataform").submit();') . '">
									' . t3lib_iconWorks::getSpriteIcon('actions-document-open')  . '<strong>' . $tablename . '</strong>
								</a>
							</p>';
			}
			$content .= '
						</div>
					</fieldset>';
		}
        return $content;
    } // End renderForm
	
    /**
     * Short description of method getNextForm
     *
     * @return String
     */
    public function getNextFormId()
    {
		$errors = $GLOBALS['repository']->get('errors');
		$post = t3lib_div::_POST();
		
		if( !empty($post['editsource']) ) {
			$sourceid = $post['sourceselected'];
			$source = $GLOBALS['repository']->getSourceData($sourceid);
			$sourcetype = $source->getType();
			return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources'][$sourcetype]['formsourceid'];
		}
		
		if(!empty($errors))
			return 'table';
		
        return 'tableprofile';
    }


} /* end of class tx_icsopendata_FormSourceProfile */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_sourceprofile.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_sourceprofile.php']);
}

?>