<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cite Solution <techbnique@in-cite.net>
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
 * Class 'tx_icsoddatastore_tcahelper' for the 'ics_od_datastore' extension.
 * TCA Helper
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	ics_od_datastore
 *
 */
class tx_icsoddatastore_tcahelper {

	/**
	 * Retrieves record title
	 *
	 * @param	&array	$params: The params
	 * @param	&object	$ref: Reference to parent object
	 * @return	void
	 */
	public function getRecordTitle(&$params, &$ref) {
		$table = $params['table'];
		$row = $params['row'];
		
		if ($table!='tx_icsoddatastore_filegroups')
			return;
			
		if ($row['cruser_id']) {
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_od_datastore']);
			$user = t3lib_BEfunc::getRecordTitle('be_users', t3lib_BEfunc::getRecord('be_users', $row['cruser_id']));
			$params['title'] = str_replace('###USER###', $user, $extConf['tcahelper.']['filegroups_title.']['prefix'].$row['title']);
		}
		else {
			$params['title'] = $row['title'];
		}
			
	}
}