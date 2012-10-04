<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 In Cité Solution <technique@in-cite.net>
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
 *
 *
 *   54: class tx_icsodcategories_datastore
 *   65:     function addSearchRestriction(&$whereClause, &$queryJoin, $conf, $object)
 *   86:     function additionalFieldsMarkers(&$markers, &$subpartArray, &$template, $filegroup, $conf, $object)
 *  141:     function additionalFieldsSearchMarkers(&$markers, &$subpartArray, &$template, $conf, $object)
 *  185:     function additionalSelectedCriteriaMarkers(&$markers, &$subpartArray, &$template, $conf, $object)
 *  213:     function additionalFieldsRSSMarkers(&$markersDataset, &$subpartArray, $template, $filegroup, $conf, $object)
 *  247:     function renderFilegroupExtraFields($field, $dataArray, &$content, $object)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Plugin 'Common for plugins' for the 'ics_od_categories' extension.
 *
 * @author	Emilie Prud'homme <emilie@in-cite.net>
 * @author	Tsi Yang <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsodcategories
 */
class tx_icsodcategories_datastore {

	/**
	 * Add Restriction SQL Search
	 *
	 * @param	string		$whereClause	clause where SQL
	 * @param	string		$queryJoin		join SQL
	 * @param	array		$conf		configuration array
	 * @param	array		$object		object
	 * @return	void
	 */
	function addSearchRestriction(&$whereClause, &$queryJoin, $conf, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['filegroups'], $object->cObj);

		if (isset($object->piVars['categories']) && count($object->piVars['categories'])) {
			$queryJoin .= $tools->getSQLJoin();
			$whereClause .= $tools->getSQLWhere($object->piVars['categories']);
		}
	}

	/**
	 * Render categories fields markers
	 *
	 * @param	array		$markers		markers array
	 * @param	array		$subpartArray	subparts array
	 * @param	string		$template		Template HTML
	 * @param	array		$filegroup		data filegroup
	 * @param	array		$conf		configuration array
	 * @param	array		$object		object
	 * @return	void
	 */
	function additionalFieldsMarkers(&$markers, &$subpartArray, &$template, $filegroup, $conf, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['filegroups'], $object->cObj);

		switch ((string)strtoupper(trim($conf['categories.']['sorting']))) {
			case 'NAME':
				$orderBy = '`'.$tools->tables['categories'].'`.`name`';
				break;
			case 'POSITION':
				$orderBy = '`'.$tools->tables['mm'].'`.`sorting_foreign`';
				break;
			default:
				$orderBy = '';
		}

		$categories = $tools->getCategoriesElement($filegroup['uid'], $orderBy);
		$output = '';

		if ($categories && is_array($categories) && !empty($categories)) {
			$listNameCategories = array();
			$pictos = array();
			$outputCats = array();
			$cObj = t3lib_div::makeInstance('tslib_cObj');
			foreach ($categories as $category) {
				$data = array(
					'name' => $category['name'],
					'picto' => ($category['picto'] && file_exists($category['picto']))? $category['picto']: '',
				);
				$cObj->start($data, 'Category');
				$cObj->setParent($object->data, $object->currentRecord);
				$outputCats[] = $cObj->stdWrap('', $conf['categories.']['category.']);
			}
			$output .= implode($conf['categories.']['separator'], $outputCats);

			$subpart = $object->cObj->getSubpart($template, '###SUBPART_CATEGORIES###');
			$template = $object->cObj->substituteSubpart($template, '###SUBPART_CATEGORIES###', $subpart);

		} else {
			$subpartArray['###SUBPART_CATEGORIES###'] = '';
		}

		$markers['###CATEGORIES_LABEL###'] = $object->cObj->stdWrap($GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:categories_label'), $conf['categories.']['label.']);
		$markers['###CATEGORIES_VALUE###'] = $object->cObj->stdWrap($output, $conf['categories.']);
	}

	/**
	 * Render search categories fields markers
	 *
	 * @param	array		$markers		markers array
	 * @param	array		$subpartArray	subparts array
	 * @param	string		$template		Template HTML
	 * @param	array		$conf		configuration array
	 * @param	array		$object		object
	 * @return	void
	 */
	function additionalFieldsSearchMarkers(&$markers, &$subpartArray, &$template, $conf, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['filegroups'], $object->cObj);

		$categories = $tools->getCategories(true);
		$output = '';

		if ($categories && is_array($categories) && !empty($categories)) {

			$subpart = $object->cObj->getSubpart($template, '###CATEGORIES_ITEM###');
			$output = '';
			foreach ($categories as $category) {
				$checked = '';
				if (is_array($object->piVars['categories']) && in_array($category['uid'], $object->piVars['categories']))
					$checked = 'checked="checked"';

				$markersCategories = array(
					'###CATEGORIES_VALUE###' => $category['uid'],
					'###CATEGORIES_LABEL###' => $category['name'],
					'###PREFIXID###' => $object->prefixId,
					'###CHECKED###' => $checked,
				);

				$output .= $object->cObj->substituteMarkerArray($subpart, $markersCategories);
			}
			$subpartArray['###CATEGORIES_ITEM###'] = $output;

		} else {
			$subpartArray['###CATEGORIES_ITEM###'] = '';
		}

		$markers['###TITLE_CATEGORIES###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:categories');
	}

	/**
	 * Render selected criteria fields markers
	 *
	 * @param	array		$markers		markers array
	 * @param	array		$subpartArray	subparts array
	 * @param	string		$template		Template HTML
	 * @param	array		$conf		configuration array
	 * @param	array		$object		object
	 * @return	void
	 */
	function additionalSelectedCriteriaMarkers(&$markers, &$subpartArray, &$template, $conf, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['filegroups'], $object->cObj);

		$categories = array();
		if (is_array($object->piVars['categories']) && !empty($object->piVars['categories'])) {
			$rows = $tools->getCategories(true, $object->piVars['categories'], '`'.$tools->tables['categories'].'`.`name`');
			if (is_array($rows) && !empty($rows)) {
				foreach ($rows as $row) {
					$categories[] = $row['name'];
				}
			}
		}
		$markers['###SC_CATEGORIES_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:sc_categories_label');
		$markers['###SC_CATEGORIES_VALUE###'] = $object->cObj->stdWrap(implode(',', $categories), $conf['displaySearch.']['categories.']);
	}

	/**
	 * Render RSS categories fields markers
	 *
	 * @param	array		$markersDataset		markers array
	 * @param	array		$subpartArray	subparts array
	 * @param	string		$template		Template HTML
	 * @param	array		$filegroup		data filegroup
	 * @param	array		$conf		configuration array
	 * @param	array		$object		object
	 * @return	void
	 */
	function additionalFieldsRSSMarkers(&$markersDataset, &$subpartArray, $template, $filegroup, $conf, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init('tx_icsoddatastore_filegroups', $object->cObj);

		$categories = $tools->getCategoriesElement($filegroup['uid']);
		$output = '';

		if ($categories && is_array($categories) && !empty($categories)) {
			$listNameCategories = array();
			foreach ($categories as $category) {
				$listNameCategories[] = $category['name'];
			}
			$output .= implode(', ', $listNameCategories);

			$subpart = $object->cObj->getSubpart($template, '###SUBPART_CATEGORIES###');
			$markers = array(
				'###CATEGORIES_VALUE###' => $output
			);
			$subpartArray['###SUBPART_CATEGORIES###'] = $object->cObj->substituteMarkerArray($subpart, $markers);
		} else {
			$subpartArray['###SUBPART_CATEGORIES###'] = '';
		}
		$markersDataset['###CATEGORIES_VALUE###'] = $output;
	}

	/**
	 * Render filegroup extra fields
	 *
	 * @param	string		$field		The field name
	 * @param	int			$dataset	Dataset's uid
	 * @param	array		$dataArray	The data array
	 * @param	&string		$content	The content
	 * @param	object		$object		object
	 * @return	boolean
	 */
	function renderFilegroupExtraFields($field, $dataset, $dataArray, &$content, $object) {
		if ($field != 'tx_icsodcategories_categories')
			return false;

		$categoriesValue = '';
		if(!empty($dataArray['tx_icsodcategories_categories'])) {
			$categories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'`name`',
				'`tx_icsodcategories_categories`
					INNER JOIN `tx_icsodcategories_categories_relation_mm`
					ON `tx_icsodcategories_categories_relation_mm`.`uid_local` = `tx_icsodcategories_categories`.`uid`',
				'`tx_icsodcategories_categories_relation_mm`.`uid_foreign` = ' . $dataset
			);
			if(is_array($categories) && count($categories)) {
				foreach($categories as $category) {
					$aCat[] = $category['name'];
				}
				$categoriesValue = implode(', ', $aCat);
			}
		}

		$content .= '<div style="clear:both;float:left;width:100%;">
			<div style="float:left;width:15em;">' . $GLOBALS['LANG']->sL('LLL:EXT:ics_od_categories/locallang_hook.xml:'.$field) . '</div>
			<div style="float:left;width:60%;">' . $categoriesValue . '</div>
		</div>';

		return true;
	}
}

?>