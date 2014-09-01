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

		$categories = $tools->getCategoriesTree();
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
			$markers['######OPTION_LEVEL######'] = '';
			$categoryEmpty = $object->cObj->substituteMarkerArray($subpart, $markers);
		}

		foreach ($categories as $category) {
			$markers['###CATEGORY_VALUE###'] = htmlspecialchars($category['uid']);
			$markers['###CATEGORY_LABEL###'] = htmlspecialchars($category['name']);
			if (in_array($category['uid'], $applicationCategories))
				$markers['###CATEGORY_SELECTED###'] = 'selected="selected"';
			else
				$markers['###CATEGORY_SELECTED###'] = '';
			$markers['###OPTION_LEVEL###'] = $category['level'];
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

	
	/**
	 * Render Form Fields
	 *
	 * @param	array		$markerArray
	 * @param	array		$subpartArray
	 * @param	string		$template
	 * @param	object		$object
	 * @return	void
	 */
	function additionalFieldsSearchMarkers(&$markerArray, &$subpartArray, &$template, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['applications'], $object->cObj);
		
		$conf = $object->conf;
		
		$markerArray['###CATEGORIES###'] = '';

		$usedCats = $tools->getCategories(true);
		if ($usedCats && !empty($usedCats)) {
			$usedCatIds = array_keys($usedCats);
			$orderBy = '';
			if (strtoupper($conf['displaySearch.']['categories.']['sorting'])== 'POSITION') {
				$orderBy = 'tx_icsodcategories_categories_relation_mm.sorting_foreign';
			}
			if (strtoupper($conf['displaySearch.']['categories.']['sorting'])== 'NAME') {
				$orderBy = 'tx_icsodcategories_categories.name';
			}
			// Retrieves the categories of Level 1
			$catOfRootLevel = $tools->getCategories(false, null, $orderBy, ' AND `tx_icsodcategories_categories`.`parent`=0');
			// $treeChildren = $tools->getCategoryTreeChildren(5);
			foreach ($catOfRootLevel as $cat) {
				if (in_array($cat['uid'], $usedCatIds)) {
					$categories[$cat['uid']] = $catOfRootLevel[$cat['uid']];
				}
				else {
					$treeChildren = $tools->getCategoryTreeChildren($cat['uid']);
					foreach ($treeChildren as $child) {
						if (in_array($child['uid'], $usedCatIds)) {
							$categories[$cat['uid']] = $catOfRootLevel[$cat['uid']];
							break;
						}
					}
				}
			}
			
			$checked_cats = $object->catCriteria;

			$columns = intval($conf['displaySearch.']['catConf.']['columns']);
			if ($columns) {
				$elemByCol = intval(ceil(count($categories)/$columns));
			}
			else {
				$columns = 1;
				$elemByCol = count($categories);
			}
			
			$templateCode = $object->cObj->fileResource($object->templateFile);
			for ($i=0; $i<$columns; $i++) {
				// Renders categories
				$markerArray['###CATEGORIES###'] .= $this->additionalFieldsSearchMarkers_categories(
					// $categories,
					array_slice($categories, intval($i*$elemByCol), $elemByCol, true),
					0,
					$object->cObj->getSubpart($templateCode, '###TEMPLATE_SEARCH_CATEGORIES###'),
					array(
						'conf' => $conf,
						'object' => $object,
						'tools' => $tools,
						'checked_cats' => $checked_cats,
						'usedCats' => $usedCats,
					)
				);
			}
		}
		$markerArray['###TITLE_CATEGORIES###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:categories');		
	}
	
	/**
	 * Renders search categories fields markers
	 *
	 * @param	array	$categories: The categories to display
	 * @param	int		$level: The level of catgories, 0 is root
	 * @param	string	$template: The template
	 * @param	array	$conf: The typoscript configuration
	 * @param	object	$object: The tslib_pibase object
	 * @param	object	$tools: The tx_icsodcategories_tools object
	 * @return	string	The HTML content of search categories
	 */
	private function additionalFieldsSearchMarkers_categories($categories=null, $level=0, $template, $params) {
		$conf = $params['conf'];
		$object = $params['object'];
		$tools = $params['tools'];
		$checked_cats = $params['checked_cats'];
		$usedCats = $params['usedCats'];
		$usedCatIds = array_keys($usedCats);
		$orderBy = '';
		if (strtoupper($conf['displaySearch.']['categories.']['sorting'])== 'POSITION') {
			$orderBy = 'tx_icsodcategories_categories_relation_mm.sorting_foreign';
		}
		if (strtoupper($conf['displaySearch.']['categories.']['sorting'])== 'NAME') {
			$orderBy = 'tx_icsodcategories_categories.name';
		}
	
		$content = '';
		if (!is_array($categories) || empty($categories))
			return $content;

		if ($level>0){
			$lTemplate = $object->cObj->getSubpart($template, '###TEMPLATE_SUBCATEGORIES'.$level.'###');
			if (!$lTemplate) {
				$lTemplate = $object->cObj->getSubpart($template, '###TEMPLATE_SUBCATEGORIES###');
			}
		}
		else {
			$lTemplate = $object->cObj->getSubpart($template, '###TEMPLATE_CATEGORIES###');
		}
		$itemTemplate = $object->cObj->getSubpart($lTemplate, '###CATEGORIES_ITEM###');
		$itemContent = '';
		foreach ($categories as $category) {
			$checked = '';
			if (in_array($category['uid'], $checked_cats))
				$checked = 'checked="checked"';
			$itemMarkers = array(
				'###PREFIXID###' => $object->prefixId,
				'###CATEGORIES_VALUE###' => $category['uid'],
				'###CATEGORIES_LABEL###' => $category['name'],
				'###CATEGORIES_NAME###' => $object->prefixId.'[categories][]',
				'###CHECKED###' => $checked,
				'###SUBCATEGORIES###' => '',
			);
			if (($level<$conf['displaySearch.']['catConf.']['recursive']) || ($conf['displaySearch.']['catConf.']['recursive']=='ALL')) {
				$children = $tools->getCategories(false, null, $orderBy, ' AND `tx_icsodcategories_categories`.`parent`='.$category['uid']);
				$subCategories = array();
				foreach ($children as $cat) {
					if (in_array($cat['uid'], $usedCatIds)) {
						$subCategories[$cat['uid']] = $children[$cat['uid']];
					}
					else {
						$treeChildren = $tools->getCategoryTreeChildren($cat['uid']);
						foreach ($treeChildren as $child) {
							if (in_array($child['uid'], $usedCatIds)) {
								$subCategories[$cat['uid']] = $children[$cat['uid']];
								break;
							}
						}
					}
				}
				$itemMarkers['###SUBCATEGORIES###'] = $this->additionalFieldsSearchMarkers_categories(
					$subCategories,
					$level+1,
					$template,
					$params
				);
			}
			$itemContent .= $object->cObj->substituteMarkerArray($itemTemplate, $itemMarkers);
		}

		$content = $object->cObj->substituteSubpartArray($lTemplate, array('###CATEGORIES_ITEM###' => $itemContent));
		return $content;
	}
	
    /**
     * Adds Restriction SQL Search
     *
     * @param   string    $addWhere: The SQL where clause
     * @param   string    $join: The SQL join
	 * @param	array     $filter: The params to filter	
     * @param   array    $conf: The conf
     * @param   tx_icsoddatastore_pi1    $pi_obj: Inherit from tslib_pibase
     * @return    void
     */
	function addSearchRestriction(&$addWhere, &$join, $filter, $conf, $object){
		if (is_array($filter['categories']) && !empty($filter['categories'])) {
			$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
			$tools->init($object->tables['applications'], $object->cObj);
			$join .= $tools->getSQLJoin();
			$addWhere[] = substr_replace(trim($tools->getSQLWhere($filter['categories'])), '', 0, 4);
		}
	}

	/**
	 * Initializes criteria
	 *
	 * @param	array	$piVars
	 * @param	array	$criteria
	 * @param	array	$conf
	 * @param	object	$object: tslib_pibase
	 * @return	void
	 */
	function addInitCriteria(&$piVars, &$criteria, &$conf, $object) {
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['applications'], $object->cObj);
		
		$categories = array();
		if (is_array($object->piVars['categories']) && !empty($object->piVars['categories']))
			$categories = $object->piVars['categories'];
		
		$delCats = $object->piVars['deleted']['categories'];
		if (is_array($delCats) && !empty($delCats)) {
			$treeParents = array();
			foreach ($delCats as $cat) {
				$treeParents = array_merge($treeParents, $tools->getCategoryTreeParents($cat));
			}
			foreach ($treeParents as $parent) {
				$delCats[] = $parent['uid'];
			}
			$delCats = array_unique($delCats);
			$categories = array_diff($categories, $delCats);
		}
		foreach ($categories as $cat) {
			$children = $tools->getCategoryTreeChildren($cat);
			if (is_array($children) && !empty($children)) {
				foreach ($children as $child) {
					$childIds[] = $child['uid'];
				}
				$categories = array_merge($categories, $childIds);
			}
		}
		$categories = array_unique($categories);
		//-- Cleans value "0"
		$categories = array_diff($categories, array(0));

		$object->catCriteria = $categories;
		$object->criteria['categories'] = $categories;
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
		$tools->init($object->tables['applications'], $object->cObj);

		if (is_array($object->catCriteria) && !empty($object->catCriteria)) {
			$rows = $tools->getCategories(false, $object->catCriteria, '`'.$tools->tables['categories'].'`.`name`');
		}
		$content = '';
		if (is_array($rows) && !empty($rows)) {
			$lTemplate = $object->cObj->getSubpart($template, '###SUBPART_SC_CATEGORIES###');
			$itemTemplate = $object->cObj->getSubpart($lTemplate, '###SC_CATEGORY_ITEM###');
			$itemContent = '';
			foreach ($rows as $row) {
				$itemMarkers = array(
					'###CATEGORY_LABEL###' => $row['name'],
					'###CATEGORY_VALUE###' => $row['uid'],
					'###CATEGORY_NAME###' => $object->prefixId.'[deleted][categories][]',
				);
				$itemContent .= $object->cObj->substituteMarkerArray($itemTemplate, $itemMarkers);
			}
			$lTemplate = $object->cObj->substituteSubpart($lTemplate, '###SC_CATEGORY_ITEM###', $itemContent);
			$lMarkers['###TITLE_CATEGORIES###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:sc_categories_label');
			$content = $object->cObj->substituteMarkerArray($lTemplate, $lMarkers);
		}
		$subpartArray['###SUBPART_SC_CATEGORIES###'] = $content;
	}
	
}
?>