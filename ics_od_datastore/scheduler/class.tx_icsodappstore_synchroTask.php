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
 * Class 'tx_icsoddatastore_synchroTask' for the 'ics_od_datastore' extension.
 *
 * @author	Tsi YANG <tsi.yang@plan-net.fr>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */
class tx_icsoddatastore_synchroTask  extends tx_scheduler_Task {
	/*
		TSConfig possible configurations:
		ics_od_datastore.synchroTask {
			# The file storage path: generally fileadmin sub-folder
			fileStoragePath = 
		}
	
	*/
	public function execute() {
		// Get TSconf
		$pagesTSC = t3lib_BEfunc::getPagesTSConfig($this->getRoot());
		$this->TSConfig = $pagesTSC['ics_od_datastore.']['synchroTask.'];
		
		if (!$this->TSConfig) {
			$trace = debug_backtrace();
			trigger_error(
				'The TSConfig ics_od_datastore.synchroTask is not defined' .
				' in ' . $trace[1 + $backlevel]['file'] .
				' on line ' . $trace[1 + $backlevel]['line'],
				E_USER_ERROR
			);
		} 
		else {
			if (!is_dir(t3lib_div::getFileAbsFileName($this->TSConfig['fileStoragePath']))) {
				$trace = debug_backtrace();
				trigger_error(
					$this->TSConfig['fileStoragePath'] . ' is not folder' .
					' in ' . $trace[1 + $backlevel]['file'] .
					' on line ' . $trace[1 + $backlevel]['line'],
					E_USER_ERROR
				);
			}
			else {
				$datasetFiles = $this->getDatasetFiles();
				if (!is_array($datasetFiles) || empty($datasetFiles)) {
					$trace = debug_backtrace();
					trigger_error(
						'There is no dataset\'s file' .
						' in ' . $trace[1 + $backlevel]['file'] .
						' on line ' . $trace[1 + $backlevel]['line'],
						E_USER_WARNING
					);
				}
				else {
					$files = $this->fetchFiles($this->TSConfig['fileStoragePath']);
					$this->processSynchronization($datasetFiles, $files);
				}
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
		if(is_array($pid) && count($pid)) {
			return $pid[0]['pid'];
		}
		return false;
	}
	
	/**
	 * Retrieves dataset files
	 *
	 * @return	mixed	Dataset files
	 */
	function getDatasetFiles() {
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_icsoddatastore_files',
			'record_type=0 AND deleted=0 AND hidden=0'
		);
	}
	
	/** 
	 * Retrieves files
	 *
	 * @return	mixed	Files
	 */
	function fetchFiles($directory) {
		$files = array();
		$scanFiles = scandir(t3lib_div::getFileAbsFileName($directory));
		foreach ($scanFiles as $file) {
			$fileName = $directory . '/' . $file;
			$absFileName = t3lib_div::getFileAbsFileName($fileName);
			if (is_file($absFileName)) {
				$files[$fileName] = array(
					'filename' => $fileName,
					'mtime' => filemtime(t3lib_div::getFileAbsFileName($fileName)),
				);
			}
			elseif ($file != '.' && $file != '..') {
				$fetchFiles = $this->fetchFiles($fileName);
				if (is_array($fetchFiles) && !empty($fetchFiles)) {
					$files = array_merge($files, $fetchFiles);
				}
			}
		}
		return $files;
	}
	
	/**
	 * Processes the synchronization
	 *
	 * @return	void
	 */
	function processSynchronization($datasetFiles, $files) {
		$datasets = array();
		// Updates files
		foreach ($datasetFiles as $datasetFile) {
			if ($files[$datasetFile['file']] && $files[$datasetFile['file']]['mtime']>$datasetFile['tstamp']) {
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
					'tx_icsoddatastore_files',
					'uid=' . $datasetFile['uid'],
					array(
						'tstamp' => $files[$datasetFile['file']]['mtime'],
					)
				);
				$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
					'tx_icsoddatastore_files_filegroup_mm.uid_foreign as uid',
					'tx_icsoddatastore_files',
					'tx_icsoddatastore_files_filegroup_mm',
					'tx_icsoddatastore_filegroups',
					' AND tx_icsoddatastore_files_filegroup_mm.uid_local=' . $datasetFile['uid']
				);
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
					if (!isset($datasets[$row['uid']]) || $datasets[$row['uid']]<$files[$datasetFile['file']]['mtime'])
						$datasets[$row['uid']] = $files[$datasetFile['file']]['mtime'];
				}
			}
		}
		// Updates datasets
		foreach ($datasets as $dataset=>$tstamp) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_icsoddatastore_filegroups',
				'uid=' . $dataset,
				array(
					'tstamp' => $tstamp,
					'update_date' => $tstamp,
				)
			);
		}
	}
}