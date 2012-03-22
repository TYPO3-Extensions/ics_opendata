<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cité Solution <technique@in-cite.net>
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
 * Hook 'Application datasets' for the 'ics_od_stores_rel' extension.
 *
 * @author	Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_userdatastore
 */
class tx_icsodstoresrel_appsDatasets {
		
	/**
	 * Render fields form application (edit or create)
	 *
	 * @param array $markerArray
	 * @param array $subpartArray
	 * @param string $template
	 * @param array $application
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsRenderForm(&$markerArray, &$subpartArray, $template, $application = null, $lConf, $object) {
		$markerArray['###DATASTORE_DATASETS_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_stores_rel/hook/locallang.xml:datasets');
		$markerArray['###DATASTORE_DATASETS_NAME###'] = $object->prefixId . '[datasets][]';		
		$subpart = $object->cObj->getSubpart($template, '###DATASTORE_DATASETS_GROUP###');
		if ($lConf['ics_od_stores_rel.']['datasetEmpty']) {
			$markers['###DATASET_LABEL###'] = '';
			$markers['###DATASET_VALUE###'] = '';
			$markers['###DATASET_SELECTED###'] = '';
			$datasetEmpty = $object->cObj->substituteMarkerArray($subpart, $markers);
		}
		$datasets = $this->getDatasets();		
		$applicationDatasets = $this->getApplicationsDatasets(array($application['uid']));
		if (!is_array($datasets) || empty($datasets)) {
			$subpartArray['###DATASTORE_DATASETS_GROUP###'] = $datasetEmpty;
		} else {
			foreach ($datasets as $dataset) {
				$markers['###DATASET_VALUE###'] = htmlspecialchars($dataset['uid']);
				$markers['###DATASET_LABEL###'] = htmlspecialchars($dataset['title']);
				if (in_array($dataset['uid'], $applicationDatasets))
					$markers['###DATASET_SELECTED###'] = 'selected="selected"';
				else
					$markers['###DATASET_SELECTED###'] = '';
				$datasetsOptions .= $object->cObj->substituteMarkerArray($subpart, $markers);
			}
			$subpartArray['###DATASTORE_DATASETS_GROUP###'] = $datasetEmpty . $datasetsOptions;
		}
	}
	
	/**
	 * Render label fields
	 *
	 * @param array $markerArray
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsRenderLabel(&$markerArray, $conf, $object) {
		$markerArray['###TITLE_DATASTORE_DATASETS###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_stores_rel/hook/locallang.xml:datasets');
		
	}
	
	/**
	 * Render fields application
	 *
	 * @param array $markerArray
	 * @param array $subpartArray
	 * @param string $template
	 * @param array $application
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsRenderData(&$markerArray, &$subpartArray, $template, $application = null, $lConf, $object) {
		$markerArray['###DATASTORE_DATASETS_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_stores_rel/hook/locallang.xml:datasets');
		if ($application) {
			$subpart = $object->cObj->getSubpart($template, '###DATASTORE_DATASETS_GROUP###');				
			$datasetIDs = $this->getApplicationsDatasets(array($application['uid']));
			if (!is_array($datasetIDs) || empty($datasetIDs)) {
				$subpartArray['###DATASTORE_DATASETS_GROUP###'] = '';
			} else {
				foreach ($datasetIDs as $datasetID) {
					$dataset = t3lib_BEfunc::getRecord('tx_icsoddatastore_filegroups', $datasetID, 'title');
					if ($datasetsGroup)
						$markers['###DATASTORE_DATASET_SEPARATOR###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_stores_rel/hook/locallang.xml:dataset_separator');
					else
						$markers['###DATASTORE_DATASET_SEPARATOR###'] = '';
					$markers['###DATASTORE_DATASET###'] = $dataset['title'];
					$datasetsGroup .= $object->cObj->substituteMarkerArray($subpart, $markers);
				}
				$subpartArray['###DATASTORE_DATASETS_GROUP###'] = $datasetsGroup;
			}
		} else {
			$subpartArray['###DATASTORE_DATASETS_GROUP###'] = '';
		}
		if (empty($datasetIDs)) {
			$subpartArray['###DATASTORE_DATASETS###'] = '';
		}
	}

	/**
	 * Return filegroups
	 *
	 * @return array
	 */
	private function getDatasets () {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$datasets = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, title, description',
			'tx_icsoddatastore_filegroups',
			'1' . $cObj->enableFields('tx_icsoddatastore_filegroups'),
			'',
			'title ASC',
			'',
			'uid'
		);
		return $datasets;
	}
	
	/**
	 * Return filegroups of applications
	 *
	 * @param array $applications Le tableau contenant les ID des applications
	 *
	 * @return mixed $applicationsDatasets La liste d' ID de jeux de données d'applications
	 */
	private function getApplicationsDatasets($applications) {
		if (!is_array($applications) || empty($applications))
			return false;
			
		$applicationsDatasets = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'DISTINCT `tx_icsodstoresrel_apps_filegroups_mm`.`uid_foreign`',
			'`tx_icsodappstore_applications`
			 JOIN `tx_icsodstoresrel_apps_filegroups_mm` ON `tx_icsodappstore_applications`.`uid` = `tx_icsodstoresrel_apps_filegroups_mm`.`uid_local`',
			'`tx_icsodappstore_applications`.`uid` IN (' . implode(',', $applications) . ')',
			'',
			'`tx_icsodstoresrel_apps_filegroups_mm`.`sorting` ASC',
			'',
			'uid_foreign'
		);
		if (!is_array($applicationsDatasets))
			return false;
		
		return array_keys($applicationsDatasets);
		
	}
	
	/**
	 * Operation after database application update
	 *
	 * @param int 	$uid 	application uid (0 if create)
	 * @param array $data 	Data update
	 * @param array $fields	fields update
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsDBUpdateAfter($uid, $data, $fields, $lConf, $object) {
		$datasets = $object->piVars['datasets'];
		if (is_array($datasets ) && !empty($datasets)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'`tx_icsodstoresrel_apps_filegroups_mm`', 
				'`uid_local` = ' . $uid
			);
			$sortIndex = 0;
			foreach ($datasets as $dataset) {
				if ($dataset) {
					$data = array(
						'`uid_local`' => $uid,
						'`uid_foreign`' => $dataset,
						'`sorting`' => ++$sortIndex,
					);		
					$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'`tx_icsodstoresrel_apps_filegroups_mm`', 
						$data
					);
				}
			}
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'`tx_icsodappstore_applications`',
				'`uid` = ' . $uid,				
				array('`tx_userdatastore_filegroups`' => $sortIndex)
			);
		}
	}
	
	/**
	 * Application preparation update
	 *
	 * @param int 	$uid 	application uid (0 if create)
	 * @param array $data 	Data update
	 * @param array $fields	fields update
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsDBUpdate($uid, $data, $fields, $lConf, $object) {
		return true;
	}
	
	/**
	 * Validate post data 
	 *
	 * @param array $errors	Array with errors messages
	 * @param array $application	Data application
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsValidate(&$errorMsgs, $application, $lConf, $object) {
		$datasets = $object->piVars['datasets'];
		if (is_array($datasets) && !empty($datasets)) {
			$realDatasets = array_keys($this->getDatasets());
			foreach ($datasets as $dataset) {
				if ($dataset && !in_array($dataset, $realDatasets)) {
					$errorMsgs[] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_stores_rel/hook/locallang.xml:datasets_notExists');
					return false;
				}
			}
		}
		return true;
	}
}

?>