<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cit� Solution <technique@in-cite.net>
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

require_once(t3lib_extMgm::extPath('ics_od_datastore') . 'opendata/datasource/tx_icsoddatastore_sourceconnexion.php');

/**
 * Short description of the command
 *
 * @file class.tx_icsoddatastore_licence_datasource.php
 * @author    In Cit� Solution <technique@in-cite.net>
 * @package    TYPO3.ics_od_datastore
 */
class tx_icsoddatastore_licence_datasource
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
	 * @return	void
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
		// * User inclusions get
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions get

		return $this->_datasourceDB->exec_SELECTgetRows(
			$queryarray['fields'],
			$queryarray['fromtable'],
			$queryarray['where'],
			$queryarray['groupby'],
			$queryarray['order'],
			$queryarray['limit']
		);
	} // End get

	/**
	 * Retrieves datasource's records
	 *
	 * @param	array		$params	The parameters to query on database
	 * @return	array		Array of records
	 */
	public function getLicencesAll($params)
	{
		$queryarray = array();
		$queryarray['fields'] =
			'`tx_icsoddatastore_licences`.`uid` AS `id`, ' .
			'`tx_icsoddatastore_licences`.`name` AS `name`, ' .
			'`tx_icsoddatastore_licences`.`link` AS `link`, ' .
			'`tx_icsoddatastore_licences`.`logo` AS `logo`';
		$queryarray['fromtable'] =
			'`tx_icsoddatastore_licences`';
		$queryarray['where'] =
			'1' . t3lib_BEfunc::deleteClause('tx_icsoddatastore_licences') . t3lib_BEfunc::BEenableFields('tx_icsoddatastore_licences');
		$queryarray['groupby'] =
			'';
		$queryarray['order'] =
			'';
		$queryarray['limit'] =
			'';
		// *************************
		// * User inclusions All
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions All

		return $this->get($queryarray);
	} // End getLicencesAll

} // End of class tx_icsoddatastore_licence_datasource
