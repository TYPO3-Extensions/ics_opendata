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
 * Form : Set parameters for the soucetype CSV
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_CsvFormSource
        implements tx_icsopendata_SourceFormSource
{

	// === ATTRIBUTS ============================================================================== //
   
	private $_csvtemppath = "/var/www/vhosts/opendatapkg/httpdocs/fileadmin/csv_files";
	private $_extensionallowed = Array('text/plain','text/csv');
	private $_extkey = 'ics_opendata';
	
    // === OPERATIONS ============================================================================= //

    /**
     * renderForm : return html for main content
     *
     * @return String 
     */
    public function renderForm($FormData, $pObj)
    {
		// Default value
		if( !empty($post['newsource']) ) {
			$sourcedata = $GLOBALS['repository']->getSourceData[$post['sourceselected']];
			if( !empty($sourcedata) ) {
				$csvdelimiter = $sourcedata->getDelimiter();
				$csvenclosure = $sourcedata->getEnclosure();
				$csvescape = $sourcedata->getEscape();
			}
		}
		else {
			$csvdelimiter = ',';
			$csvenclosure = '';
			$csvescape = '';
		}
		
		// Main content header
		$content = '
					<h2>' . $GLOBALS['LANG']->getLL('csvsource.title') . '</h2>
					<input type="hidden" id="actionid" name="action" value="analyze"/>';
					
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
		
		// Default form
		$content .= '
					<h3>' . $GLOBALS['LANG']->getLL('csvsource.help') . '</h3>
					<div class="parameterinputs">
						<input type="hidden" id="csv_length" name="csvlength" value=0>

						<label for="csv_path"> ' . $GLOBALS['LANG']->getLL('csvsource.path') . ' =  </label>
						<input type="file" id="csv_path" name="csvfile" value="Parcourir">
						
						<label for="csv_delimiter"> ' . $GLOBALS['LANG']->getLL('csvsource.delimiter') . ' = </label>
						<input type="text" id="csv_delimiter" name="csvdelimiter" maxlength="1" value="' . $csvdelimiter . '">
						
						<label for="csv_enclosure"> ' . $GLOBALS['LANG']->getLL('csvsource.enclosure') . ' = </label>
						<input type="text" id="csv_enclosure" name="csvenclosure" maxlength="1" value="' . $csvenclosure . '">
						
						<label for="csv_escape"> ' . $GLOBALS['LANG']->getLL('csvsource.escape') . ' = </label>
						<input type="text" id="csv_escape" name="csvescape" maxlength="1" value="' . $csvescape . '">
					</div>
					
					<input type="submit" name="sourcecreated" value="Add source"/>';
		
		return $content;
    }

    /**
     * validInput : Validate _POST data
     *
     * @return boolean
     */
    public function validInput()
    {
		$errors = Array();
		// Upload test
		if(!isset($_FILES['csvfile'])){
			$errors[] = "ERROR_CSV_UPLOAD";
		}
		else{
			$tempfile = $_FILES['csvfile']['tmp_name'];

			// Extension test
			if (!in_array($_FILES['csvfile']['type'],$this->_extensionallowed)) {
				$errors[] = "ERROR_CSV_EXTENSION";
			}
		}
		
		// Test csvdelimiter
		$post = t3lib_div::_POST();
		if(isset($post['csvdelimiter']) && !empty($post['csvdelimiter'])){
			if(strlen($post['csvdelimiter'] > 1))
				$errors[] = "ERROR_CSV_DELIMITER";
		}
		else{
			$errors[] = "ERROR_CSV_DELIMITER_NOT_SET";
		}
		
		// Test csvenclosure
		if(isset($post['csvenclosure']) && !empty($post['csvenclosure'])){
			if(strlen($post['csvenclosure'] > 1))
				$errors[] = "ERROR_CSV_ENCLOSURE";
		}
		
		// Test csvescape
		if(isset($post['csvescape']) && !empty($post['csvescape'])){
			if(strlen($post['csvescape'] > 1))
				$errors[] = "ERROR_CSV_ESCAPE";
		}
		
		$GLOBALS['repository']->set('errors',$errors);
		
		if(!empty($errors))
			return false;
		
        return true;
    } //End validInput
	
	
	/**
	* analyze : analyze the source and fill $data object
	*
	* @return boolean : false if the analyze fail
	*/
	public function analyze()
	{
		$errors = Array();
		// Move file in fileadmin directory
		$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$tempfile = $_FILES['csvfile']['tmp_name'];
		$filename = basename ($_FILES['csvfile']['name'],strrchr($_FILES['csvfile']['name'],'.'));
		$tempname = $fileFunc->cleanFileName(basename($tempfile));
		
		$path = $this->_csvtemppath;
		$filepath = $fileFunc->getUniqueName($tempname, $path);
		
		var_dump($tempfile, $path, $tempname, $filepath);
		if (!t3lib_div::upload_copy_move($tempfile, $filepath)){
			$errors[] = "ERROR_ANALYZE_MOVE";
			$GLOBALS['repository']->set('errors',$errors);
			return false;
		}
		
		// Unlink the file
		t3lib_div::unlink_tempfile($tempfile);
		
		
		// Get source class
		$post = t3lib_div::_POST();
		$class = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources']['csv']['dataclass'];


		// Get parameters and instanciate data object
		$length = htmlspecialchars($post['csvlength']);
		$delimiter = htmlspecialchars($post['csvdelimiter']);
		$enclosure = htmlspecialchars($post['csvenclosure']);
		$escape = htmlspecialchars($post['csvescape']);
		
		if( !empty($class) ) {
			$source = t3lib_div::getUserObj($class, false);
			
			if( !empty($source) ) {
				$source->initData($filename, $filepath, $length, $delimiter, $enclosure, $escape);

				// Save data in repository
				$sourceid = $source->getSourceId();
				
				$GLOBALS['repository']->setSourceData($sourceid,$source);
				$GLOBALS['repository']->set('errors',$errors);
				$GLOBALS['repository']->set('sourceid',$sourceid);
			}
			else {
				$errors[] = "ERROR_CLASS_DATACSV_NOT_FOUND";
			}
		}
		else {
			$errors[] = "ERROR_CLASS_DATACSV_NOT_DEFINED";
		}
		
		if(!empty($errors))
			return false;
			
		return true;
		
	} // End analyze
	

	/**
     * getNextFormId : return the next form id
     *
     * @return String : next form id
     */
    public function getNextFormId()
    {
		$errors = $GLOBALS['repository']->get('errors');
		$post = t3lib_div::_POST();
		
		if(!empty($errors)){
			return 'csvsource';
		}

		return 'sourceprofile';
    }
    

} /* end of class tx_icsopendata_CsvFormSource */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/csv/class.csv_formsource.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/csv/class.csv_formsource.php']);
}

?>