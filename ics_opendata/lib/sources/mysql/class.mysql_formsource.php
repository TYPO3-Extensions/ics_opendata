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
 * Form : Set parameters for the soucetype MYSQL
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_MysqlFormSource
        implements tx_icsopendata_SourceFormSource
{
    // === ATTRIBUTS ============================================================================== //
	
	protected $_extkey = 'ics_opendata';
	
    // === OPERATIONS ============================================================================= //

    /**
     * Short description of method form
     *
     * @return mixed
     */
    public function renderForm($FormData, $pObj)
    {
		$post = t3lib_div::_POST();
		if( empty($post['newsource']) ) {
			$sourcedata = $GLOBALS['repository']->getSourceData($post['sourceselected']);
			// Get Data
			if( !empty($sourcedata) ) {
				$mysqlhost = $sourcedata->getHost();
				$mysqllogin = $sourcedata->getLogin();
				$mysqlpass = $sourcedata->getPassword();
				$mysqlbase = $sourcedata->getBase();
			}
		}
		// Main content header
		$content = '
					<h2>' . $GLOBALS['LANG']->getLL('mysqlsource.title') . '</h2>
					<input type = "hidden" name="action" value="analyze"/>';
		
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
		
		// Form
		$content .= '
					<h3>' . $GLOBALS['LANG']->getLL('mysqlsource.help') . '</h3>
					<div class="parameterinputs">
						<label for="mysql_host"> ' . $GLOBALS['LANG']->getLL('mysqlsource.host') . ' = </label>
						<input type="text" id="mysql_host" name="mysqlhost" value="' . $mysqlhost . '">
						
						<label for="mysql_login"> ' . $GLOBALS['LANG']->getLL('mysqlsource.login') . ' = </label>
						<input type="text" id="mysql_login" name="mysqllogin" value="' . $mysqllogin . '">
						
						<label for="mysql_pass"> ' . $GLOBALS['LANG']->getLL('mysqlsource.pass') . ' = </label>
						<input type="password" id="mysql_pass" name="mysqlpass" value="' . $mysqlpass . '">
						
						<label for="mysql_base"> ' . $GLOBALS['LANG']->getLL('mysqlsource.base') . ' = </label>
						<input type="text" id="mysql_base" name="mysqlbase" value="' . $mysqlbase . '">
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
		$post = t3lib_div::_POST();
		
		// Test host
		if(empty($post['mysqlhost']))
			$errors[] = "ERROR_MYSQL_HOST_NOT_SET";
			
		// Test login
		if(empty($post['mysqllogin']))
			$errors[] = "ERROR_MYSQL_LOGIN_NOT_SET";
			
		// Test pass
		if(empty($post['mysqlpass']))
			$errors[] = "ERROR_MYSQL_PASS_NOT_SET";
			
		// Test base
		if(empty($post['mysqlbase']))
			$errors[] = "ERROR_MYSQL_BASE_NOT_SET";
			
		$GLOBALS['repository']->set('errors',$errors);

		if(!empty($errors))
			return false;
			
        return true;
    }
	
	
	/**
	* analyze : analyze the source and fill $data object
	*
	* @return boolean : false if the analyze fail
	*/
	public function analyze()
	{
		$errors = Array();

		// Get source class
		$post = t3lib_div::_POST();
		$class = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources']['mysql']['dataclass'];

		// Get parameters and instanciate data object
		$host = htmlspecialchars($post['mysqlhost']);
		$login = htmlspecialchars($post['mysqllogin']);
		$pass = htmlspecialchars($post['mysqlpass']);
		$base = htmlspecialchars($post['mysqlbase']);
		$name = $base;
		
		$source = t3lib_div::getUserObj($class, false);
		$error = $source->initData($name, $host, $login, $pass, $base);
		if(!empty($error)) {
			$errors[] = 'ERROR_' . $error;
			$GLOBALS['repository']->set('errors',$errors);
			return false;
		}
		
		// Save data in repository
		$sourceid = $source->getSourceId();

		$GLOBALS['repository']->setSourceData($sourceid,$source);
		$GLOBALS['repository']->set('errors',$errors);
		$GLOBALS['repository']->set('sourceid',$sourceid);

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
			return 'mysqlsource';
		}
		
        return 'sourceprofile';
    }



} /* end of class tx_icsopendata_MysqlFormSource */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/class.mysql_formsource.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/class.mysql_formsource.php']);
}

?>