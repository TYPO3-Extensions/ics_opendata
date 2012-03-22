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
 *   54: class tx_icsodcategories_appstore
 *   64:     function applicationFieldsRenderLabel(&$markerArray, $conf, $object)
 *   79:     function applicationFieldsRenderData(&$markerArray, &$subpartArray, $template, $application = null, $lConf, $object)
 *  123:     function applicationFieldsRenderForm(&$markerArray, &$subpartArray, $template, $application = null, $lConf, $object)
 *  174:     function applicationFieldsDBUpdate($uid, &$data, &$fields, $conf, $object)
 *  189:     function applicationFieldsDBUpdateAfter($uid, $data, $fields, $conf, $object)
 *  208:     function applicationFieldsValidate($errors, $application, $conf, $object)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('ics_od_categories') . 'lib/class.tx_icsodcategories_tools.php');

/**
 * Plugin 'Common for plugins' for the 'ics_od_categories' extension.
 *
 * @author	Emilie Prud'homme <emilie@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsodcategories
 */
class tx_icsodcategories_appstore {

	/**
	 * Render label fields
	 *
	 * @param	array		$markerArray
	 * @param	array		$lConf
	 * @param	object		$object
	 * @return	void
	 */
	function applicationFieldsRenderLabel(&$markerArray, $conf, $object) {
		$markerArray['###TITLE_CATEGORIES###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:categories');
	}

	/**
	 * Render fields application
	 *
	 * @param	array		$markerArray
	 * @param	array		$subpartArray
	 * @param	string		$template
	 * @param	array		$application
	 * @param	array		$lConf
	 * @param	object		$object
	 * @return	void
	 */
	function applicationFieldsRenderData(&$markerArray, &$subpartArray, $template, $application = null, $lConf, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['applications'], $object->cObj);

		$markerArray['###CATEGORIES_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:categories');
		$hasCategories = false;
		if ($application) {
			$subpart = $object->cObj->getSubpart($template, '###SUBPART_CATEGORIES###');
			$categories = $tools->getCategoriesElement($application['uid']);

			if (is_array($categories) && !empty($categories)) {
				$hasCategories = true;
				$categoriesName =array();
				foreach ($categories as $category) {
					$categoriesName[] = $category['name'];
				}
				$markerArray['###CATEGORIES###'] = implode(', ', $categoriesName);

				$markersCategories = array(
					'###CATEGORIES###' => $markerArray['###CATEGORIES###'],
					'###CATEGORIES_LABEL###' => $markerArray['###CATEGORIES_LABEL###']
				);

				$subpartArray['###SUBPART_CATEGORIES###'] = $object->cObj->substituteMarkerArray($subpart, $markersCategories);
			}
		}
		if (!$hasCategories) {
			$markerArray['###CATEGORIES###'] = '';
			$subpartArray['###SUBPART_CATEGORIES###'] = '';
		}

	}

	/**
	 * Render Form Fields
	 *
	 * @param	array		$markerArray
	 * @param	array		$subpartArray
	 * @param	string		$template
	 * @param	array		$application
	 * @param	array		$lConf
	 * @param	object		$object
	 * @return	void
	 */
	function applicationFieldsRenderForm(&$markerArray, &$subpartArray, $template, $application = null, $lConf, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['applications'], $object->cObj);

		$markerArray['###CATEGORIES_LABEL###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:categories');
		$markerArray['###CATEGORIES_NAME###'] = $object->prefixId . '[categories][]';

		$categories = $tools->getCategories();
		$applicationCategories = array();

		if ($application) {
			$appcategories = $tools->getCategoriesElement($application['uid']);
			if (is_array($appcategories) && !empty($appcategories)) {
				foreach ($appcategories as $category) {
					$applicationCategories[] = $category['uid'];
				}
			}
		}
		if (is_array($object->piVars['categories']))
			$applicationCategories = array_merge($applicationCategories, $object->piVars['categories']);

		$subpart = $object->cObj->getSubpart($template, '###CATEGORIES_ITEMS###');
		if ($lConf['ics_od_categories.']['categoryEmpty']) {
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
		$subpartArray['###CATEGORIES_ITEMS###'] = $categoryEmpty . $categoriesOptions;
	}

	/**
	 * Application preparation update
	 *
	 * @param	int		$uid 	application uid (0 if create)
	 * @param	array		$data 	Data update
	 * @param	array		$fields	fields update
	 * @param	array		$lConf
	 * @param	object		$object
	 * @return	void
	 */
	function applicationFieldsDBUpdate($uid, &$data, &$fields, $conf, $object) {
		$data['tx_icsodcategories_categories'] = count($object->piVars['categories']);
		$fields[] = 'tx_icsodcategories_categories';
	}

	/**
	 * Operation after database application update
	 *
	 * @param	int		$uid 	application uid (0 if create)
	 * @param	array		$data 	Data update
	 * @param	array		$fields	fields update
	 * @param	array		$lConf
	 * @param	object		$object
	 * @return	void
	 */
	function applicationFieldsDBUpdateAfter($uid, $data, $fields, $conf, $object) {
		$categories = $object->piVars['categories'];
		if (is_array($categories ) && !empty($categories)) {
			$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
			$tools->init($object->tables['applications'], $object->cObj);
			$tools->reinitCategoriesToElement($uid);
			$tools->addCategoriesToElement($uid, $categories);
		}
	}

	/**
	 * Validate post data
	 *
	 * @param	array		$errors	Array with errors messages
	 * @param	array		$application	Data application
	 * @param	array		$lConf
	 * @param	object		$object
	 * @return	void
	 */
	function applicationFieldsValidate($errors, $application, $conf, $object) {
		if (is_array($object->piVars['categories']) && !empty($object->piVars['categories'])) {
			$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
			$tools->init($object->tables['applications'], $object->cObj);
			$categories = $tools->getCategories();

			$categoriesUid = array();
			if (is_array($categories) && !empty($categories)) {
				foreach ($categories as $category) {
					$categoriesUid[] = $category['uid'];
				}
			}

			foreach ($object->piVars['categories'] as $category) {
				if (!in_array($category, $categoriesUid)) {
					$errors[] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:categories_not_exist');
					return false;
				}
			}
		}
		return true;
	}

}

?>