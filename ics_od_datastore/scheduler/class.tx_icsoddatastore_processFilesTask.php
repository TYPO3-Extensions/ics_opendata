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
 * Class 'tx_icsoddatastore_processFilesTask' for the 'ics_od_datastore' extension.
 * Synchronize md5 and file size
 
 * @author	Tsi YANG <tsi.yang@plan-net.fr>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */
class tx_icsoddatastore_processFilesTask  extends tx_scheduler_Task {
	public function execute() {
		$roots = $this->getRoot();
		if (!is_array($roots) || empty($roots)) {
			t3lib_div::devLog(
				'No roots were found', 
				'ics_od_datastore', 
				0,
				array($roots)
			);
			return;
		}
		foreach ($roots as $root) {
			$storage = $root['pid'];
			$pagesTSC = t3lib_BEfunc::getPagesTSConfig($storage);
			if ($pagesTSC['ics_od_datastore.']['processFilesTask.']['process']) {
				$TSConfig = $pagesTSC['ics_od_datastore.']['processFilesTask.'];
				$this->processFiles($TSConfig);
			}
		}
		return true;
	}

	/**
	 * Retrieves root pid
	 *
	 * @return	mixed
	 */
	function getRoot() {
		$pid = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`sys_template`.`pid`',
			'`sys_template`, `pages`',
			'`root` = 1 AND `pages`.`pid` = 0 AND `pages`.`uid`=`sys_template`.`pid` AND `sys_template`.`deleted` = 0 AND `sys_template`.`hidden` = 0 AND `pages`.`deleted` = 0 AND `pages`.`hidden` = 0'
		);
		return $pid;
	}
	
	/**
	 * Process files
	 *
	 * @param	array	$TSConfig
	 * @return void
	 */
	function processFiles($TSConfig) {
		$requestTime = $_SERVER['REQUEST_TIME'];
		$time = $requestTime;
		if ($TSConfig['time']) {
			$time = $TSConfig['time'];
			$timeArray = t3lib_div::trimExplode(',', $time);
			if (count($time)>1) {
				$time = mktime($time[0], $time[1], $time[2], $time[3], $time[4], $time[5]);
			}
			else {
				$time = $timeArray[0];
			}
		}
		$dirname = $TSConfig['dirname'];
		$mfiles = $this->getMFiles($dirname, $requestTime, $time);
	
		if (!is_array($mfiles) || empty($mfiles)) {
			t3lib_div::devLog(
				'No file updated', 
				'ics_od_datastore', 
				0,
				array($mfiles)
			);
			return;
		}
		
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'tx_icsoddatastore_files.uid as fileId, 
				tx_icsoddatastore_files.file,
				tx_icsoddatastore_files.size,
				tx_icsoddatastore_files.md5',
			'tx_icsoddatastore_files
				LEFT JOIN tx_icsoddatastore_files_filegroup_mm ON tx_icsoddatastore_files_filegroup_mm.uid_local=tx_icsoddatastore_files.filegroup
				LEFT JOIN tx_icsoddatastore_filegroups ON tx_icsoddatastore_filegroups.uid=tx_icsoddatastore_files_filegroup_mm.uid_foreign',
			'tx_icsoddatastore_files.file IN("'.implode('","', $mfiles).'")'
		);

		if (is_array($rows) && !empty($rows)) {
			$tstamp = $_SERVER['REQUEST_TIME'];
			foreach ($rows as $row) {
				$data = array();
				if ($row['record_type']==0) {
					$filename = t3lib_div::getFileAbsFileName($row['file']);
					if (file_exists($filename)) {
						$data = array(
							'size' => filesize($filename),
							'md5' => md5_file($filename),
						);
					}
				}
				if ($row['record_type']==1) {
					$data = array(
						'md5' => md5_file($row['url']),
					);
				}
				if (!empty($data)) {
					$data['tstamp'] = $tstamp;
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
						'tx_icsoddatastore_files',
						'uid='.$row['fileId'],
						$data
					);
				}
			}
		}
	}
	
	function getMFiles($dirname, $requestTime, $time) {
		$mfiles = array();
		if ($folder = opendir(t3lib_div::getFileAbsFileName($dirname))) {
			while(false !== ($file = readdir($folder))) {
				$filename = $dirname.$file;
				$abs_filename = t3lib_div::getFileAbsFileName($filename);
				if (is_file($abs_filename)) {
					if (filectime($abs_filename)>intval(($requestTime-$time))) {
						$mfiles[]= $filename;
					}
				}
				else {
					if ($file != '.' && $file != '..') {
						$sub_mfiles = $this->getMFiles($filename.'/', $requestTime, $time);
						if (is_array($sub_mfiles) && !empty($sub_mfiles)) {
							$mfiles = array_merge($mfiles, $sub_mfiles);
						}
					}
				}
			}
		}
		return $mfiles;
	}
}