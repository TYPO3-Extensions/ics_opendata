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
 * Hook 'Application categories' for the 'user_datastore' extension.
 *
 * @author	Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_userdatastore
 */
class user_datastore_applicationCategories {
	
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
		$markerArray['###DATASTORE_CATEGORIES_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:categories');
		$markerArray['###DATASTORE_CATEGORIES_NAME###'] = $object->prefixId . '[categories][]';		
		$categories = $this->getCategories();		
		$applicationCategories = $this->getApplicationsCategories(array($application['uid']));
		$subpart = $object->cObj->getSubpart($template, '###DATASTORE_CATEGORIES_GROUP###');
		if ($lConf['user_datastore.']['categoryEmpty']) {
			$markers['###CATEGORY_LABEL###'] = '';
			$markers['###CATEGORY_VALUE###'] = '';
			$markers['###CATEGORY_SELECTED###'] = '';
			$categoryEmpty = $object->cObj->substituteMarkerArray($subpart, $markers);
		}
		foreach ($categories as $category) {
			$markers['###CATEGORY_VALUE###'] = htmlspecialchars($category['uid']);
			$markers['###CATEGORY_LABEL###'] = htmlspecialchars($category['name']);
			if (in_array($category['uid'], $applicationCategories))
				$markers['###CATEGORY_SELECTED###'] = 'selected="selected"';
			else
				$markers['###CATEGORY_SELECTED###'] = '';
			$categoriesOptions .= $object->cObj->substituteMarkerArray($subpart, $markers);
		}
		$subpartArray['###DATASTORE_CATEGORIES_GROUP###'] = $categoryEmpty . $categoriesOptions;
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
		$markerArray['###TITLE_DATASTORE_CATEGORIES###'] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:categories');
		$markerArray['###DATASTORE_CATEGORIES_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:categories');
		if ($application) {
			$subpart = $object->cObj->getSubpart($template, '###DATASTORE_CATEGORIES_GROUP###');				
			$categoryIDs = $this->getApplicationsCategories(array($application['uid']));
			foreach ($categoryIDs as $categoryID) {
				$category = t3lib_BEfunc::getRecord('tx_icsopendatastore_categories', $categoryID, 'name, description');
				if ($categoriesGroup)
					$markers['###DATASTORE_CATEGORY_SEPARATOR###'] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:category_separator');
				else
					$markers['###DATASTORE_CATEGORY_SEPARATOR###'] = '';
				$markers['###DATASTORE_CATEGORY###'] = $category['name'];
				$categoriesGroup .= $object->cObj->substituteMarkerArray($subpart, $markers);
			}
			$subpartArray['###DATASTORE_CATEGORIES_GROUP###'] = $categoriesGroup;
		} else {
			$subpartArray['###DATASTORE_CATEGORIES_GROUP###'] = '';
		}
		if (empty($categoryIDs)) {
			$subpartArray['###DATASTORE_CATEGORIES###'] = '';
		}
	}

	/**
	 * Récupère les catégories
	 *
	 */
	private function getCategories () {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$categories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, name, description, parent',
			'tx_icsopendatastore_categories',
			'1' . $cObj->enableFields('tx_icsopendatastore_categories'),
			'',
			'name ASC',
			'',
			'uid'
		);
		return $categories;
	}
	
	/**
	 * Récupère les catégories des applications
	 *
	 * @param array $applications Le tableau contenant les ID des applications
	 *
	 * @return mixed $applicationsCategories La liste d' ID de catégories d'applications
	 */
	private function getApplicationsCategories($applications) {
		$applicationsCategories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'DISTINCT `tx_userdatastore_categories_applications_mm`.`uid_foreign`',
			'`tx_icsopendataapi_applications`
			 JOIN `tx_userdatastore_categories_applications_mm` ON `tx_icsopendataapi_applications`.`uid` = `tx_userdatastore_categories_applications_mm`.`uid_local`',
			'`tx_icsopendataapi_applications`.`uid` IN (' . implode(',', $applications) . ')',
			'',
			'`tx_userdatastore_categories_applications_mm`.`sorting` ASC',
			'',
			'uid_foreign'
		);			
		return array_keys($applicationsCategories);
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
		$categories = $object->piVars['categories'];
		if (is_array($categories ) && !empty($categories)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'`tx_userdatastore_categories_applications_mm`', 
				'`uid_local` = ' . $uid
			);
			$sortIndex = 0;
			foreach ($categories as $category) {
				if ($category) {
					$data = array(
						'`uid_local`' => $uid,
						'`uid_foreign`' => $category,
						'`sorting`' => ++$sortIndex,
					);		
					$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'`tx_userdatastore_categories_applications_mm`', 
						$data
					);
				}
			}
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'`tx_icsopendataapi_applications`',
				'`uid` = ' . $uid,				
				array('`tx_userdatastore_categories`' => $sortIndex)
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
		$categories = $object->piVars['categories'];
		$realCategories = array_keys($this->getCategories());
		foreach ($categories as $category) {
			if ($category && !in_array($category, $realCategories)) {
				$errorMsgs[] = $GLOBALS['TSFE']->sL('LLL:EXT:user_datastore/hook/locallang.xml:categories_notExists');
				return false;
			}
		}
		return true;
	}
}

?>