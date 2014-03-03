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
/*
 * $Id$
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */


/**
 *
 * @author	Emilie Sagniez <emilie@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsoddataviz
 */
class tx_icsoddataviz_datastore {

	function additionalFieldsMarkers(&$markers, &$subpartArray, $template, $row, $conf, $piObj) {
		$codes = t3lib_div::trimExplode(',', $piObj->config['code'], 1);
		if (in_array('SINGLE', $codes) && isset($piObj->piVars['uid'])) {
			// uniquement sur la vue détails
			$markers['###UID###'] = $row['uid'];
			
			$files = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'`tx_icsoddatastore_files`.uid,
					`tx_icsoddatastore_files`.tstamp,
					`tx_icsoddatastore_files`.file',
				'`tx_icsoddatastore_files`
					INNER JOIN `tx_icsoddatastore_files_filegroup_mm`
						ON `tx_icsoddatastore_files_filegroup_mm`.`uid_local` = `tx_icsoddatastore_files`.`uid`
					INNER JOIN `tx_icsoddatastore_filegroups`
						ON	`tx_icsoddatastore_files_filegroup_mm`.`uid_foreign` =  `tx_icsoddatastore_filegroups`.`uid`
						AND `tx_icsoddatastore_filegroups`.`uid` = ' . $row['uid'] . ' ' . $piObj->cObj->enableFields('tx_icsoddatastore_filegroups'),
				'`tx_icsoddatastore_files`.`file` like \'%.csv\' ' .  $piObj->cObj->enableFields('tx_icsoddatastore_files'),
				'',
				'',
				1
			);
			if (is_array($files) && !empty($files)) {
				$subpart = $piObj->cObj->getSubpart($template, '###SUBPART_DATAVIZ_FILE###');
				$outputFiles = '';
				foreach ($files as $file) {
					$markersFile = array(
						'###FILEUID###' => $file['uid'],
						'###FILENAME###' => basename($file['file']),
					);
					$outputFiles .= $piObj->cObj->substituteMarkerArray($subpart, $markersFile);
				}
				$subpartArray['###SUBPART_DATAVIZ_FILE###'] = $outputFiles;
			} else {
				$subpartArray['###SUBPART_DATAVIZ###'] = '';
			}			
		}
	}
}

?>