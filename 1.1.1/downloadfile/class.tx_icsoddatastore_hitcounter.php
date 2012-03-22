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
 * Hit counter
 *
 * @author    Tsi Yang <tsi@in-cite.net>
 * @package    TYPO3
 */
class tx_icsoddatastore_hitcounter {
	function main($file) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid_local, uid_foreign',
			'tx_icsoddatastore_files_filegroup_mm',
			'uid_local = ' . $file['uid']
		);
		if (is_array($rows) && !empty($rows)) {
			$table = 'tx_icsoddatastore_downloads';
			$insertArray = array(
				'pid' => $file['pid'],
				'tstamp' => time(),
				'crdate' => time(),
				'filegroup' => $rows[0]['uid_foreign'],
				'ip' => t3lib_div::getIndpEnv('REMOTE_ADDR'),
				'file' => $file['uid'],
			);
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				$table,
				$insertArray
			);
		}
	}
}