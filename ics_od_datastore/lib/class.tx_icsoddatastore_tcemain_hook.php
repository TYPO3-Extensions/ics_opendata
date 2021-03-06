<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Plan.Net France <typo3@plan-net.fr>
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
 * Hook on TCEmain. 
 * Process file: calculates the md5 and file size
 *
 * @author	Tsi <tsi.yang@plan-net.fr>
 * @package	TYPO3
 * @subpackage	ics_od_datastore
 */
class tx_icsoddatastore_tcemain_hook {
	
	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$tce) {
		if (($table != 'tx_icsoddatastore_files') || (($status == 'new') && (!isset($fieldArray['record_type']))))
			return;
		
		if (!isset($fieldArray['record_type'])) {
			if ($status != 'new') {
				if (isset($fieldArray['file']) || isset($fieldArray['url'])) {
					$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
						'record_type',
						$table,
						'uid=' . $id
					);
					switch ($row['record_type']) {
						case 0: if (!isset($fieldArray['file'])) return;
							$row['file'] = $fieldArray['file'];
							break;
						case 1: if (!isset($fieldArray['url'])) return;
							$row['url'] = $fieldArray['url'];
							break;
						default: return;
					}
				}
			}
		}
		else {
			switch ($fieldArray['record_type']) {
				case 0: if (!isset($fieldArray['file'])) return; break;
				case 1: if (!isset($fieldArray['url'])) return; break;
				default: return;
			}
			$row = $fieldArray;
		}
		
		if ($row['record_type']==0) {
			$file = t3lib_div::getFileAbsFileName($row['file']);
			if (file_exists($file)) {
				$fieldArray['size'] = filesize($file);
				$fieldArray['md5'] = md5_file($file);
			}
		}
		if ($row['record_type']==1) {
			$fieldArray['md5'] = md5_file($row['url']);
		}
	}

}

?>