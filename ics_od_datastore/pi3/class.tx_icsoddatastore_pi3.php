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

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Stats files downloads' for the 'ics_od_datastore' extension.
 *
 * @author	YANG Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */
class tx_icsoddatastore_pi3 extends tslib_pibase {
	var $prefixId      = 'tx_icsoddatastore_pi3';		// Same as class name
	var $scriptRelPath = 'pi3/class.tx_icsoddatastore_pi3.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_od_datastore';	// The extension key.

	var $templateFile = 'typo3conf/ext/ics_od_datastore/res/template.html'; /**< Path of template file */	
	var $tables = array(
		'stats' => 'tx_icsoddatastore_statistics',
		'datasets' => 'tx_icsoddatastore_filegroups',
		'files' => 'tx_icsoddatastore_files',
	);

	private static $defaultSize = 10;	// Default size of rank
	private static $defaultType = 'dataset';	// Default type of rank

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

		$this->pi_initPIflexForm();
		$this->init();
		
		$rows = $this->getStats();
		$content = $this->renderStats($rows);

		return $this->pi_wrapInBaseClass($content);
	}
	
	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	function init() {
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template', 'main');
		$templateFile = $templateFile? $templateFile: $this->conf['template'];
		if ($templateFile)
			$this->templateFile = $templateFile;
		$this->templateCode = $this->cObj->fileResource($this->templateFile);
		
		$type = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'type', 'main');
		$this->conf['view.']['type'] = $type? $type: $this->conf['view.']['type'];
		$this->conf['view.']['type'] = $this->conf['view.']['type']? $this->conf['view.']['type']: self::$defaultType;
		
		$size = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'size', 'main');
		$this->conf['view.']['size'] = $size? $size: $this->conf['view.']['size'] ;
		$this->conf['view.']['size'] = $this->conf['view.']['size'] ? $this->conf['view.']['size'] : self::$defaultSize;
		
		$period = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'period', 'main');
		$this->conf['view.']['period'] = $period? $period: $this->conf['view.']['period'];
	}
	
	/**
	 * Retrieves stats
	 *
	 * @return mixed	Stats rows
	 */
	function getStats() {
		$groupBy = '';
		$orderBy = '';
		$where_clause = '1';
		
		$type = (string)strtoupper(trim($this->conf['view.']['type']));
		switch ($type) {
			case 'DATASET':
				$fields = array('`'.$this->tables['stats'].'`.`filegroup`', 'SUM(`'.$this->tables['stats'].'`.`count`) as total');
				$tables = $this->tables['stats'].' JOIN '.$this->tables['datasets'].' ON `'.$this->tables['stats'].'`.`filegroup` = `'.$this->tables['datasets'].'`.`uid`';
				$where_clause .= ' ' . $this->cObj->enableFields($this->tables['datasets']);
				$groupBy = '`'.$this->tables['stats'].'`.`filegroup`';
				$orderBy = 'total DESC';
				break;
			case 'FILE':
				$fields = array('`'.$this->tables['stats'].'`.`file`', '`'.$this->tables['stats'].'`.filegroup`', 'SUM(`'.$this->tables['stats'].'`.`count`) as total');
				$tables = $this->tables['stats'].' JOIN '.$this->tables['files'].' ON `'.$this->tables['stats'].'`.`file` = `'.$this->tables['files'].'`.`uid`';
				$where_clause .= ' ' . $this->cObj->enableFields($this->tables['files']);
				$groupBy = 'file';
				$orderBy = 'total DESC';
				break;
			case 'CATEGORY':
			default:
				trigger_error('Any code implemented for type ' . $type, E_USER_ERROR);
		}
		if ($this->conf['view.']['period']) {
			$date = mktime(0, 0, 0, date('m'), date('d')- $this->conf['view.']['period'], date('Y'));
			$where_clause .= ' AND date>=' . $date;
		}
		$where_clause .= ' ' . $this->cObj->enableFields($this->tables['stats']);
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			(isset($fields) && is_array($fields) && !empty($fields))? implode(',', $fields): '*',
			$tables,
			$where_clause,
			$groupBy,
			$orderBy,
			$this->conf['view.']['size'] 
		);
	}
	
	/**
	 * Render stats
	 *
	 * @param	array		$rows: Statistics rows
	 * @return string		Stats HTML content
	 */
	function renderStats(array $rows = null) {
		if (!isset($rows) || empty($rows))
			return $this->renderEmpty($this->pi_getLL('statsEmpty'));
			
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_STATISTICS###');
		$subparts = array();
		$itemTemplate = $this->cObj->getSubpart($template, '###TOP_ITEM###');
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$type = (string)strtolower(trim($this->conf['view.']['type']));
		foreach ($rows as $index=>$row) {
			$data = $this->renderData($row);
			$dataRow = array(
				'rank' => $index +1,
				'uid' => $data[0],
				'title' => $data[1],
				'count' => $this->renderCount($row),
				// TODO : ajouter url dans le cas type = file avec url réécrit
			);
			$cObj->start($dataRow, 'Stats');
			$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
			$markers = array();
			$lMarkers = array(
				'RANK' => $cObj->stdWrap($dataRow['rank'], $this->conf['renderObj.'][$type . '.']['rank.']),
				'DATA' => $cObj->stdWrap($dataRow['title'], $this->conf['renderObj.'][$type . '.']['data.']),
				'COUNT' => $cObj->stdWrap($dataRow['count'], $this->conf['renderObj.'][$type . '.']['count.']),
			);
			$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $lMarkers, '###|###');
			$subparts['###TOP_ITEM###'] .= $this->cObj->substituteMarkerArray($itemContent, $markers, '###|###');			
		}
		$markers = array(
			'TITLE' => $this->pi_getLL('top_dl', 'Top download', true)
		);
		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}
	
	/**
	 * Render data
	 *
	 * @param	array		$row: Statistic's row
	 * @return string		Data HTML content wrapped
	 */
	private function renderData(array $row) {
		$data = array();
		$type = (string)strtoupper(trim($this->conf['view.']['type']));
		switch ($type) {
			case 'DATASET':
				$data = $row['filegroup'];
				$datasets = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'title',
					$this->tables['datasets'],
					'uid=' . $row['filegroup'] . ' ' . $this->cObj->enableFields($this->tables['datasets']),
					'',
					'',
					1
				);
				if (is_array($datasets) && !empty($datasets))
					$data = array($row['filegroup'], $datasets[0]['title']);
				break;
			case 'FILE':
				$data = $row['file'];
				$files = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*',
					$this->tables['files'],
					'uid=' . $row['file'] . ' ' . $this->cObj->enableFields($this->tables['files']),
					'',
					'',
					1
				);
				if (is_array($files) && !empty($files)) {
					$makeLink = t3lib_div::makeInstance('tx_icsoddatastore_makelink');
					$data = array($row['file'], $makeLink->generateUrl($files[0]));
				}
				break;
			case 'CATEGORY':
			default:
				trigger_error('Any data render implemented for type ' . $type, E_USER_ERROR);
		}
		return $data;
	}
	
	/**
	 * Render count
	 *
	 * @param	array		$row: Statistic's row
	 * @return string		Data HTML content wrapped
	 */
	private function renderCount(array $row) {
		$count = 0;
		$type = (string)strtoupper(trim($this->conf['view.']['type']));
		switch ($type) {
			case 'DATASET':
				$count = $row['total'];
				break;
			case 'FILE':
				$count = $row['total'];
				break;
			case 'CATEGORY':
			default:
				trigger_error('Any count render implemented for type ' . $type, E_USER_ERROR);
		}
		return $count;
	}

	/**
	 * Render empty message
	 *
	 * @return	string		html content
	 */
	function renderEmpty($msg) {
		return $this->cObj->stdWrap($msg, $this->conf['empty.']);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_datastore/pi3/class.tx_icsoddatastore_pi3.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_datastore/pi3/class.tx_icsoddatastore_pi3.php']);
}

?>