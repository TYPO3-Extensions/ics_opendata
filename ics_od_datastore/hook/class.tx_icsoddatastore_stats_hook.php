<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Caxton Plan.net <technique@in-cite.net>
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

class tx_icsoddatastore_stats_hook {

	/**
	 * Render stats fields markers
	 *
	 * @param	array		$markers		markers array
	 * @param	array		$subpartArray	subparts array
	 * @param	string		$template		Template HTML
	 * @param	array		$dataset		The dataset
	 * @param	array		$conf		configuration array
	 * @param	array		$pObj		pObj
	 * @return	void
	 */
	function additionalFieldsMarkers(&$markers, &$subpartArray, &$template, $dataset, $conf, $pObj) {
		$markers['###STAT_DL_LABEL###'] = $pObj->cObj->stdWrap($GLOBALS['TSFE']->sL('LLL:EXT:ics_od_datastore/hook/locallang.xml:stat_dl'), $conf['count.']['label.']);
		$markers['###STAT_DL_VALUE###'] = $pObj->cObj->stdWrap($this->getDatasetDL($dataset['uid']), $conf['stat_dl.']);
	}
	
	/**
	 * Retrieves dataset upload count
	 *
	 * @param	int		$dataset: Dataset's ID
	 * @return	mixed		The upload count
	 */
	private function getDatasetDL($dataset) {
		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
			'`uid`, `filegroup`, SUM(`count`) AS `total`',
			'tx_icsoddatastore_statistics',
			'`filegroup`='.$dataset,
			'filegroup'
		);
		return $row['total'];
	}
	
	function addSearchRestriction(&$whereClause, &$queryJoin, $conf, $pObj) {
		if ($pObj->piVars['sort']['column']!='stat_dl' && $conf['sorting.']['name']!='stat_dl')
			return;
		$queryJoin .= ' LEFT OUTER JOIN `tx_icsoddatastore_statistics`
			ON `tx_icsoddatastore_statistics`.`filegroup` = `tx_icsoddatastore_filegroups`.`uid` ';
	}
	
	function selectQuery_extraColumnSorting($column, $sortOrder, &$fields, &$orderBy, $conf, $pObj) {
		if ($column!='stat_dl')
			return;
		$fields[] = 'SUM(`tx_icsoddatastore_statistics`.`count`) as stat_dl';
		$orderBy = 'stat_dl DESC';
	}
	
}