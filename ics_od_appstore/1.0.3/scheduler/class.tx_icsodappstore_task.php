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

class tx_icsodappstore_task  extends tx_scheduler_Task{
	
	var $tables = array(
		'logs' => 'tx_icsodappstore_logs',
		'monthLogs' => 'tx_icsodappstore_month_logs',
		'stats' => 'tx_icsodappstore_statistics',
	);
	
	public function execute() {
		// Delete month logs
		$GLOBALS['TYPO3_DB']->exec_DELETEquery (
			$this->tables['monthLogs'],
			'DATEDIFF(CURDATE(),FROM_UNIXTIME(tstamp,\'%Y-%m-%d\'))>30'
		);
		
		// Copy logs to month logs
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'*',
			$this->tables['logs'],
			'deleted=0 AND DATEDIFF(CURDATE(),FROM_UNIXTIME(tstamp,\'%Y-%m-%d\'))>0'
		);

		$result = $GLOBALS['TYPO3_DB']->exec_INSERTmultipleRows (
			$this->tables['monthLogs'], 
			array('uid', 'pid', 'tstamp', 'crdate', 'cruser_id', 'deleted', 'hidden', 'application', 'ip', 'cmd'),
			$rows
		);
		
		// Insert stats
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'UNIX_TIMESTAMP(FROM_UNIXTIME(tstamp,\'%Y-%m-%d\')) AS day,application,cmd,COUNT(cmd)',
			$this->tables['logs'],
			'deleted=0 AND DATEDIFF(CURDATE(),FROM_UNIXTIME(tstamp,\'%Y-%m-%d\'))>0',
			'day, cmd, application',
			'day, application'
		);
		$GLOBALS['TYPO3_DB']->exec_INSERTmultipleRows (
			$this->tables['stats'], 
			array('date', 'application', 'cmd', 'count'),
			$rows
		);
		
		// Delete logs
		$GLOBALS['TYPO3_DB']->exec_DELETEquery (
			$this->tables['logs'],
			'DATEDIFF(CURDATE(),FROM_UNIXTIME(tstamp,\'%Y-%m-%d\'))>0'
		);
		
		return true;
	}

}
