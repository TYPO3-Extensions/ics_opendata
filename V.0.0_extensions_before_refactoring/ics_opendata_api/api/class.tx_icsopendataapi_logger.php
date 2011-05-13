<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 In Cité Solution <technique@in-cite.net>
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
 * Updates log informations.
 *
 * @author    Tsi Yang <tsi@in-cite.net>
 * @package    TYPO3
 */
class tx_icsopendataapi_logger {

	private $key; /**< The API Key to log for. */
	private $cmd; /**< The executed command. */
	private $pid; /**< The pid of the page where the API Key is defined. */
	private $rid; /**< The record id of the API Key record. */
	private $usage; /**< The usage count. */

	/**
	 * Initializes the logger.
	 *
	 * @param $params array The service parameters
	 */
	function init(array $params) {
		$this->key = $params['key'];
		$this->cmd = $params['cmd'];
		
		$row = $this->getRow();
		$this->pid = $row['pid'];
		$this->rid = $row['uid'];
		$this->usage = $row['count_use'];
	}
	
	/**
	 * Logs the call and increments the counter.
	 */
	function logCall() {
		$this->insertCall();
		$this->incrementsUsage();
	}
	
	/**
	 * Adds the log record to the call log.
	 */
	private function insertCall() {
		global $TYPO3_DB;
	
		$table = 'tx_icsopendataapi_logs';
		$insertArray = array(
			'pid' => $this->pid,
			'tstamp' => time(),
			'crdate' => time(),
			'tx_icsopendataapi_application' => $this->rid,
			'ip' => t3lib_div::getIndpEnv('REMOTE_ADDR'),
			'cmd' => $this->cmd,
		);
		$TYPO3_DB->exec_INSERTquery(
			$table, 
			$insertArray
		);
	}
	/**
	 * Updates the usage counter.
	 */
	private function incrementsUsage() {
		global $TYPO3_DB;
		// Hardcoded update for concurrency support.
		$TYPO3_DB->sql_query('UPDATE tx_icsopendataapi_applications SET count_use = count_use + 1, tstamp = UNIX_TIMESTAMP() WHERE uid = ' . $this->rid);
	}
	
	/**
	 * Retrieves the application's record. 
	 *
	 * @return array The application's record.
	 */
	private function getRow() {
		global $TYPO3_DB;
		$rows = $TYPO3_DB->exec_SELECTgetRows(
			'*',
			'tx_icsopendataapi_applications',
			'key_appli = ' . $TYPO3_DB->fullquotestr($this->key, $table) . ' ' .
			'AND hidden = 0 AND deleted = 0'
		);
		return $rows[0];
	}

}
