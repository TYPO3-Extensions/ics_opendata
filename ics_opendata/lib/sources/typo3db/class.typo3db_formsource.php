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

require_once(t3lib_extMgm::extPath('ics_opendata') . 'lib/sources/mysql/class.mysql_formsource.php');

/**
 * Form : Set parameters for the soucetype MYSQL
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_Typo3dbFormSource extends tx_icsopendata_MysqlFormSource
{
    // === ATTRIBUTS ============================================================================== //
	
	protected $_extkey = 'ics_opendata';
	
    // === OPERATIONS ============================================================================= //

	
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
		$class = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources']['typo3db']['dataclass'];

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
			return 'typo3dbsource';
		}
		
        return 'sourceprofile';
    }



} /* end of class tx_icsopendata_typo3dbFormSource */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/typo3db/class.typo3db_formsource.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/typo3db/class.typo3db_formsource.php']);
}

?>