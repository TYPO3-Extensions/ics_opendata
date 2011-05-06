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
 * Form : select an opendata extension to edit it
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormLoadExtension
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
		$post = t3lib_div::_POST();
		$errors = Array();

		$repcontent = file_get_contents($post['loadedrepository'] . '/doc/ics_od_repository.dat');
		$repository = unserialize(gzuncompress(base64_decode($repcontent)));
		if( !is_a($repository, 'tx_icsopendata_Repository') ) {
			$errors[] = "REPOSITORY_NOT_FOUND";
		}

		if(!empty($errors))
			return false;
			 
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
		// retrieve extension list
		$datapath = t3lib_extMgm::extPath( "ics_opendata", "doc/ics_od.dat");
		$extensionlist = file_get_contents($datapath);
		if( $extensionlist ) {
			$extensionlist = unserialize($extensionlist);
		}
		else {
			$extensionlist = array();
		}
		foreach( $extensionlist as $extkey=>$path ) {
			if( !is_dir($path) ) {
				unset($extensionlist[$extkey]);
			}
		}
		file_put_contents($datapath, serialize($extensionlist));
		
		$content = '
				<h2>' . $GLOBALS['LANG']->getLL('loadext.title') . '</h2>
				<input type="hidden" name="formid" value="loadext">';
			
		$content .= '
				<h3>' . $GLOBALS['LANG']->getLL('loadext.help') . '</h3>
				<fieldset>
					<legend>' . $GLOBALS['LANG']->getLL('loadext.repbrowser') . '</legend>
					<label for="' . $this->_extkey . 'rep_path"> ' . $GLOBALS['LANG']->getLL('loadext.extension') . '</label>
					<select id="' . $this->_extkey . 'rep_path" name="loadedrepository">';
					
		foreach( $extensionlist as $extkey=>$path ) {
			$content .= '
						<option value="' . $path . '">' . $extkey . '</option>';
		}
		$content .= '
					</select>
				</fieldset>';
				
		$content .= '
				<input type="submit" name="loadrepository" value="' . $GLOBALS['LANG']->getLL('loadext.load') . '">';
        return $content;
    } // End renderForm
	
    /**
     * Short description of method getNextForm
     *
     * @return Integer
     */
    public function getNextFormId()
    {
        $errors = $GLOBALS['repository']->get('errors');
		$post = t3lib_div::_POST();
		
		if(!empty($errors)){
			return 'loadext';
		}
		
		if( isset($post['loadrepository']) )
			return 'result';
			
		return 'loadext';
    }


} /* end of class tx_icsopendata_FormLoadExtension */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_loadextension.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_loadextension.php']);
}

?>