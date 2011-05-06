<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 In Cite Solution <technique@in-cite.net>
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
 * Hook 'Application datasets' for the 'user_datastore' extension.
 *
 * @author	Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_userdatastore
 */
class user_datastore_applicationDatasets {
	
	/**
	 * Rendu des champs d'une application dans un formulaire
	 *
	 * @param array $markerArray
	 * @param array $subpartArray
	 * @param array $application
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsRenderForm(&$markerArray, &$subpartArray, $template, $application = null, $lConf, $object) {
		$markerArray['###DATASTORE_DATASETS_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:datasets');
		$markerArray['###DATASTORE_DATASETS_NAME###'] = $object->prefixId . '[datasets][]';		
		$datasets = $this->getDatasets();		
		$applicationDatasets = $this->getApplicationsDatasets(array($application['uid']));
		$subpart = $object->cObj->getSubpart($template, '###DATASTORE_DATASETS_GROUP###');
		if ($lConf['user_datastore.']['datasetEmpty']) {
			$markers['###DATASET_LABEL###'] = '';
			$markers['###DATASET_VALUE###'] = '';
			$markers['###DATASET_SELECTED###'] = '';
			$datasetEmpty = $object->cObj->substituteMarkerArray($subpart, $markers);
		}
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
	
	/**
	 * Rendu des données de champs d'une application
	 *
	 * @param array $markerArray
	 * @param string $subpart
	 * @param array $application
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsRenderData(&$markerArray, &$subpartArray, $template, $application = null, $lConf, $object) {
		$markerArray['###TITLE_DATASTORE_DATASETS###'] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:datasets');
		$markerArray['###DATASTORE_DATASETS_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:datasets');
		if ($application) {
			$subpart = $object->cObj->getSubpart($template, '###DATASTORE_DATASETS_GROUP###');				
			$datasetIDs = $this->getApplicationsDatasets(array($application['uid']));
			foreach ($datasetIDs as $datasetID) {
				$dataset = t3lib_BEfunc::getRecord('tx_icsopendatastore_filegroups', $datasetID, 'title');
				if ($datasetsGroup)
					$markers['###DATASTORE_DATASET_SEPARATOR###'] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:dataset_separator');
				else
					$markers['###DATASTORE_DATASET_SEPARATOR###'] = '';
				$markers['###DATASTORE_DATASET###'] = $dataset['title'];
				$datasetsGroup .= $object->cObj->substituteMarkerArray($subpart, $markers);
			}
			$subpartArray['###DATASTORE_DATASETS_GROUP###'] = $datasetsGroup;
		} else {
			$subpartArray['###DATASTORE_DATASETS_GROUP###'] = '';
		}
		if (empty($datasetIDs)) {
			$subpartArray['###DATASTORE_DATASETS###'] = '';
		}
	}

	/**
	 * Récupère les jeux de données
	 *
	 */
	private function getDatasets () {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$datasets = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, title, description',
			'tx_icsopendatastore_filegroups',
			'1' . $cObj->enableFields('tx_icsopendatastore_filegroups'),
			'',
			'title ASC',
			'',
			'uid'
		);
		return $datasets;
	}
	
	/**
	 * Récupère les jeux de données des applications
	 *
	 * @param array $applications Le tableau contenant les ID des applications
	 *
	 * @return mixed $applicationsDatasets La liste d' ID de jeux de données d'applications
	 */
	private function getApplicationsDatasets($applications) {
		$applicationsDatasets = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'DISTINCT `tx_userdatastore_filegroups_applications_mm`.`uid_foreign`',
			'`tx_icsopendataapi_applications`
			 JOIN `tx_userdatastore_filegroups_applications_mm` ON `tx_icsopendataapi_applications`.`uid` = `tx_userdatastore_filegroups_applications_mm`.`uid_local`',
			'`tx_icsopendataapi_applications`.`uid` IN (' . implode(',', $applications) . ')',
			'',
			'`tx_userdatastore_filegroups_applications_mm`.`sorting` ASC',
			'',
			'uid_foreign'
		);			
		return array_keys($applicationsDatasets);
	}
	
	/**
	 * Mise à jour des champs
	 *
	 * @param integer $uid ID d'une application
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return void
	 */
	function applicationFieldsDBUpdate($uid, $lConf, $object) {
		$datasets = $object->piVars['datasets'];
		if (is_array($datasets ) && !empty($datasets)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'`tx_userdatastore_filegroups_applications_mm`', 
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
						'`tx_userdatastore_filegroups_applications_mm`', 
						$data
					);
				}
			}
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'`tx_icsopendataapi_applications`',
				'`uid` = ' . $uid,				
				array('`tx_userdatastore_filegroups`' => $sortIndex)
			);
		}
	}
	
	/**
	 * Validation de saisie des champs
	 *
	 * @param array $lConf
	 * @param object $object
	 *
	 * @return boolean
	 */
	function applicationFieldsValidate(&$errorMsgs, $lConf, $object) {
		$datasets = $object->piVars['datasets'];
		$realDatasets = array_keys($this->getDatasets());
		foreach ($datasets as $dataset) {
			if ($dataset && !in_array($dataset, $realDatasets)) {
				$errorMsgs[] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:datasets_notExists');
				return false;
			}
		}
		return true;
	}
}

?>