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
 * Form : display the generated code
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormResult
	implements tx_icsopendata_Form
{
    
	// === ATTRIBUTS ============================================================================== //
	
	private $_extkey = 'ics_opendata';
	private $_pObj = null;
	
    // === OPERATIONS ============================================================================= //
	
	/**
     * validInput : Validate _POST data
     *
     * @return boolean
     */
    public function validInput()
    {
		$post = t3lib_div::_POST();
		
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
		$this->_pObj = $pObj;
		
		// Get data
		$post = t3lib_div::_POST();		
		
		// Main content header
		$content .= '
					<h2>' . $GLOBALS['LANG']->getLL('code.title') . '</h2>';

		
		// Display Error
		$errors = $GLOBALS['repository']->get('errors');
		if( isset($errors) && !empty($errors) ){
			$content .= '
					<div class="error">';
			foreach($errors as $err){
				$content .= '
						<p> ERROR : ' . $err . '</p>';
			}
			$content .= '
					</div>';
		}
		
		// Warnings
		$commands = $GLOBALS['repository']->get('commands');
		foreach( $commands as $command ) {
			$warnings = $command->getWarning();
			if( !empty($warnings) ) {
				if( !$command->isActive() )
					$warningscontent .= t3lib_iconWorks::getSpriteIcon('status-dialog-error') . ' ';
				$warningscontent .= '
								<strong>' . $command->getName() . '</strong>';
				foreach( $warnings as $war ) {
					$warningscontent .= '
								<p>' . t3lib_iconWorks::getSpriteIcon('status-dialog-warning') . ' ' . $war . '</p>';
				}
			}
		}
		if( !empty($warningscontent) ) {
			$warningscontent = '
						<div class="warning">' . $warningscontent . '
						</div>';
		}
		
		// Generate files
		$generatedcontent = '';
		$codegenerator = t3lib_div::makeInstance('tx_icsopendata_codegeneration');
		$files = $codegenerator->generateCode();
		
		if( isset($post['generateextensionfiles']) ) {
			$this->uploadFiles($files);
			$content .= '
						<div class="extcreated"><p><strong>' . $GLOBALS['LANG']->getLL('extensioncreated') . ' </strong>' . t3lib_iconWorks::getSpriteIcon('status-status-checked'). '</div>';
		}
		
		if( !empty($files['errors']) ) {
			foreach( $files['errors'] as $err ) {
				$generatedcontent .= $err;
			}
		}
		else {
			$filepath = array();
			foreach( $files as $filealias=>$fileinfos ) {
				if( $filealias == 'ext_icon' )
					continue;
				$generatedcontent .= '
							<h4>' . $filealias . '</h4>
							<pre>' . htmlspecialchars($fileinfos['content']) . '</pre>
							<br />';
			}
		}

		// --- Main content -------------------------------------------------------------- //

		// File list
		$content .= '
					<table>
						<thead>
							<th>' . $GLOBALS['LANG']->getLL('filename') . '</th>
							<th>' . $GLOBALS['LANG']->getLL('size') . '</th>
							<th>' . $GLOBALS['LANG']->getLL('overwrite') . '</th>
							<th>' . $GLOBALS['LANG']->getLL('deleteusercontent') . '</th>
						</thead>
						<tbody>';
		foreach( $files as $filealias=>$fileinfos ) {
			$content .= '
							<tr>
								<td>' . $fileinfos['path'] . $fileinfos['filename'] . '</td>
								<td>' . t3lib_div::formatSize(mb_strlen($fileinfos['content'])) . '</td>
								<td><input type="checkbox" name="overwrite[]" value="' . $filealias . '" checked="checked"></td>
								<td><input type="checkbox" name="deleteusercode[]" value="' . $filealias . '"></td>
							</tr>';
		}
		$content .= '				
						</tbody>
					</table>
					<br />';
		
		// Location selection
		$extensionkey = $GLOBALS['repository']->get('extensionkey');
		$content .= '
					<label for="' . $this->_extkey . 'extensionloc"><strong>' . $GLOBALS['LANG']->getLL('writetolocation') . '</strong></label>
					<p>
						<select name="extensionloc" id="' . $this->_extkey . 'extensionloc">'.
							($this->_pObj->importAsType('G')?'<option value="G">Global: '.$this->_pObj->typePaths['G'].$extensionkey.'/'.(@is_dir(PATH_site.$this->_pObj->typePaths['G'].$extensionkey)?' (OVERWRITE)':' (empty)').'</option>':'').
							($this->_pObj->importAsType('L')?'<option value="L" selected="selected">Local: '.$this->_pObj->typePaths['L'].$extensionkey.'/'.(@is_dir(PATH_site.$this->_pObj->typePaths['L'].$extensionkey)?' (OVERWRITE)':' (empty)').'</option>':'').
						'</select>
					</p>
					<input type="submit" name="generateextensionfiles" value="' . $GLOBALS['LANG']->getLL('generatefiles') . '">
					<br />';
		
		if( !empty($warningscontent) ) {
			$content .= '
					<div class="infos">
						<h3>' . $GLOBALS['LANG']->getLL('code.warnings') . '</h3>' . $warningscontent . '
					</div>';
		}
		$content .= '
					<div class="code">
						<h3>' . $GLOBALS['LANG']->getLL('code.code') . '</h3>' . $generatedcontent . '
					</div>';
						
        return $content;
		
    } // End renderForm
	
	// === OTHER OPERATIONS ========================================================================= //
	
	private function uploadFiles($files)
	{
		$post = t3lib_div::_POST();
		$extensionkey = $GLOBALS['repository']->get('extensionkey');
		$extensionpath = PATH_site.$this->_pObj->typePaths[$post['extensionloc']] . $extensionkey;
		
		if( !is_dir($extensionpath) )
			$handle = t3lib_div::mkdir($extensionpath);
			
		if( !is_dir($extensionpath . '/opendata') )
			$handle = t3lib_div::mkdir($extensionpath . '/opendata');
		
		// if( !is_dir($extensionpath . '/opendata/datasources') )
			// $handle = t3lib_div::mkdir($extensionpath . '/opendata/datasources');
		if( !is_dir($extensionpath . '/opendata/datasource') )
			$handle = t3lib_div::mkdir($extensionpath . '/opendata/datasource');
			
		if( !is_dir($extensionpath . '/doc') )
			$handle = t3lib_div::mkdir($extensionpath . '/doc');
		
		$overwrite = $post['overwrite'];
		foreach( $files as $filealias=>$infos ) {
			if( in_array($filealias, $overwrite) ){
				t3lib_div::writeFile($extensionpath . $infos['path'] . $infos['filename'], $infos['content']);
			}
		}
		
		// Save repository
		$repository = htmlspecialchars(base64_encode(gzcompress(serialize($GLOBALS['repository']))));
		t3lib_div::writeFile($extensionpath . '/doc/ics_od_repository.dat', $repository);
		
		// Update extension list
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
		$extensionlist[$extensionkey] = $extensionpath;
		t3lib_div::writeFile($datapath, serialize($extensionlist));
	}
	
	private function cleanStr($in) {
		$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ ]@i','@[^a-zA-Z0-9_]@');
		$replace = array ('e','a','i','u','o','c','_','');
		return preg_replace($search, $replace, $in);
	}
	
	// =============================================================================================== //
	
	public function getNextFormId()
	{
		$post = t3lib_div::_POST();
		$ics_opendata = $post['ics_opendata'];
		
		return 'result';
	}

} /* end of class FormCode */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_result.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_result.php']);
}

?>