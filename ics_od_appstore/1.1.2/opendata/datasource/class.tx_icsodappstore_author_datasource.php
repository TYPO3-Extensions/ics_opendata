<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 author name <author@mail.com>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   51: class tx_icsodappstore_author_datasource
 *   70:     public function __construct()
 *   90:     public function get($queryarray)
 *  127:     public function getAuthorsAll($params)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('ics_od_appstore') . 'opendata/datasource/tx_icsodappstore_sourceconnexion.php');

/**
 * Short description of the command
 *
 * @file class.tx_icsodappstore_author_datasource.php
 * @author    author name <author@mail.com>
 * @package    TYPO3.ics_od_appstore
 */
class tx_icsodappstore_author_datasource
{
	// *************************
	// * User inclusions 0
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...

	// * End user inclusions 0


	private $_datasourceDB = null;

	/**
	 * Constructor
	 *
	 * @return	[type]		...
	 */
	public function __construct()
	{
		$this->_datasourceDB = typo3db_opendatapkg_connect();
		// *************************
		// * User inclusions constructor
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions constructor

	}

	/**
	 * Retrieves datasource's records
	 *
	 * @param	array		$queryarray	The query array to query on database
	 * @return	array		Array of records
	 */
	public function get($queryarray)
	{
		// *************************
		// * User inclusions get 0
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions get 0
		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			$queryarray['fields'],
			$queryarray['fromtable'],
			$queryarray['where'],
			$queryarray['groupby'],
			$queryarray['order'],
			$queryarray['limit'],
			$queryarray['uidIndexField']
		);
		// *************************
		// * User inclusions get 1
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions get 1

		return $rows;
	} // End get

	/**
	 * Retrieves datasource's records
	 *
	 * @param	array		$params	The parameters to query on database
	 * @return	array		Array of records
	 */
	public function getAuthorsAll($params)
	{
		// *************************
		// * User inclusions All 0
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions All 0

		$queryarray = array();
		$queryarray['fields'] =
			'`fe_users`.`uid` AS `id`, ' .
			'`fe_users`.`name` AS `name`';
		$queryarray['fromtable'] =
			'`fe_users`';
		$queryarray['where'] =
			'1' . t3lib_BEfunc::deleteClause('fe_users') . t3lib_BEfunc::BEenableFields('fe_users');
		$queryarray['groupby'] =
			'';
		$queryarray['order'] =
			'';
		$queryarray['limit'] =
			'';
		$queryarray['uidIndexField'] =
			'';
		// *************************
		// * User inclusions All 1
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// Build query where
		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_icsodappstore.'];
		$groups = t3lib_div::trimExplode(',', $conf['usergroup'], true);
		$groups = '\'' . implode('\', \'', $groups) . '\'';

		$queryarray['where'] .= ' AND usergroup IN (' . $groups . ')';
		// * End user inclusions All 1

		return $this->get($queryarray);
	} // End getAuthorsAll

	// *************************
	// * User inclusions other processing
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...

	// * End user inclusions other processing


} // End of class tx_icsodappstore_author_datasource
