<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cité Solution <technique@in-cite.net>
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
 * Class 'tx_icsoddatastore_dynflex' for the 'ics_od_datastore' extension
 * Generates dynamic flex.
 *
 * @author Tsi Yang <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */
class tx_icsoddatastore_dynflex	{
	var $tables = array(
		'filegroups' => 'tx_icsoddatastore_filegroups',
		'tiers' => 'tx_icsoddatastore_tiers',
	);
	
	/** 
	 * Add agencies items
	 *
	 * @return agencies config
	 */
	public function addAgencies($config) {
		$tiers = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'uid, name',
			$this->tables['tiers'],
			'1 AND deleted=0',
			'',
			'',
			'',
			'uid'
		);
		$tiersIDs = array_keys($tiers);
		$agencies = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'agency',
			$this->tables['filegroups'],
			'1 AND deleted=0',
			'agency',
			'',
			'',
			'agency'
		);
		$agencies = array_keys($agencies);
		$agencies = array_intersect($tiersIDs, $agencies);
		$optionList = array();
		foreach ($agencies as $agency) {
			$optionList[] = array(
				0 => $tiers[$agency]['name'],
				1 => $tiers[$agency]['uid']
			);
		}
		
		$config['items'] = array_merge($config['items'], $optionList);
		return $config;
	}
}
?>