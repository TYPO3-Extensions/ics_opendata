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

		// if (isset($object->piVars['categories']) && count($object->piVars['categories'])) {
		if (isset($object->catCriteria) && count($object->catCriteria)) {
			$queryJoin .= $tools->getSQLJoin();
			// $categories = $object->piVars['categories'];
			// foreach ($categories as $category) {
				// $children = $tools->getCategoryTreeChildren($category);
				// if (is_array($children) && !empty($children)) {
					// $categories = array_merge($categories, array_keys($children));
				// }
			// }
			// $whereClause .= $tools->getSQLWhere($categories);
			$whereClause .= $tools->getSQLWhere($object->catCriteria);
			// t3lib_div::debug($whereClause);
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
		$fields = t3lib_div::trimExplode(',', $conf['fields']);
		if (!in_array('tx_icsodcategories_categories', $fields)) {
			$subpartArray['###SUBPART_CATEGORIES###'] = '';
			$subpartArray['###SUBPART_CATTREEPARENTS###'] = '';
			return;
		}
		
	
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['filegroups'], $object->cObj);

		$local_lang = t3lib_div::readLLfile(t3lib_div::getFileAbsFileName('EXT:ics_od_categories/locallang.xml'), $GLOBALS['TSFE']->lang);

		$object->LOCAL_LANG['default'] = array_merge($object->LOCAL_LANG['default'], $local_lang['default']);
		$object->LOCAL_LANG[$GLOBALS['TSFE']->lang] = array_merge($object->LOCAL_LANG[$GLOBALS['TSFE']->lang], $local_lang[$GLOBALS['TSFE']->lang]);
		// Additionnal markers categories
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
		if ($categories && is_array($categories) && !empty($categories)) {
			$output = $this->additionalFieldsMarkers_category($categories, $conf['categories.']);
			$markers['###CATEGORIES_LABEL###'] = $object->cObj->stdWrap($object->pi_getLL('categories_label', 'Categories', true), $conf['categories.']['label.']);
			$markers['###CATEGORIES_VALUE###'] = $object->cObj->stdWrap($output, $conf['categories.']);
		} else {
			$subpartArray['###SUBPART_CATEGORIES###'] = '';
		}

		// Additionnal markers categories with tree parents
		// Query again for the first category
		if (!$conf['catTreeParents.']['display']) {
			$subpartArray['###SUBPART_CATTREEPARENTS###'] = '';
		}
		else {
			if ($conf['categories.']['sorting']!='POSITION') {
				$orderBy = '`'.$tools->tables['mm'].'`.`sorting_foreign`';
				$categories = $tools->getCategoriesElement($filegroup['uid'], $orderBy);
			}
			if (!is_array($categories) || empty($categories)) {
				$subpartArray['###SUBPART_CATTREEPARENTS###'] = '';
			}
			else {
				$category = array_shift($categories);
				$treeParents = $tools->getCategoryTreeParents($category['uid']);
				$output = $this->additionalFieldsMarkers_category($treeParents, $conf['catTreeParents.']);
				$markers['###CATTREEPARENTS_LABEL###'] = $object->cObj->stdWrap($object->pi_getLL('catTreeParents', 'Category', true), $conf['catTreeParents.']['label.']);
				$markers['###CATTREEPARENTS_VALUE###'] = $object->cObj->stdWrap($output, $conf['catTreeParents.']['value.']);
			}
		}
	}
	
	/**
	 * Render additionnal field category
	 *
	 * @param	array	$categories
	 * @param	array	$conf
	 * @return	string	The category
	 */
	private function additionalFieldsMarkers_category($categories, $conf) {
		$outputCats = array();
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		foreach ($categories as $category) {
			$data = array(
				'uid' => $category['uid'],
				'name' => $category['name'],
				'description' => $category['description'],
				'parent' => $category['parent'],
				'picto' => ($category['picto'] && file_exists($category['picto']))? $category['picto']: '',
			);
			$cObj->start($data, 'Category');
			$cObj->setParent($object->data, $object->currentRecord);
			$outputCats[] = $cObj->stdWrap('', $conf['category.']);
		}
		$output .= implode($conf['separator'], $outputCats);
		
		return $output;
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

		$markers['###CATEGORIES###'] = '';
		
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
			$treeChildren = $tools->getCategoryTreeChildren(5);
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
			
			for ($i=0; $i<$columns; $i++) {
				// Renders categories
				$markers['###CATEGORIES###'] .= $this->additionalFieldsSearchMarkers_categories(
					// $categories,
					array_slice($categories, intval($i*$elemByCol), $elemByCol, true),
					0,
					$object->cObj->getSubpart($object->templateCode, '###TEMPLATE_SEARCH_CATEGORIES###'),
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
		
		$markers['###TITLE_CATEGORIES###'] = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_categories/locallang.xml:categories');
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
			$subCategories = array();
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
		$tools->init($object->tables['filegroups'], $object->cObj);
		
		$categories = array();
		if (is_array($object->piVars['categories']) && !empty($object->piVars['categories']))
			$categories = $object->piVars['categories'];
		
		//-- Removes deleted categories from criteria
		$delCats = $object->piVars['deleted']['categories'];
		$childIds = array();
		$children = array();
		if (is_array($delCats) && !empty($delCats)) {
			$treeParents = array();
			foreach ($delCats as $cat) {
				if ($cat=intval($cat)) {
					// Gets the category parent
					$treeParents = array_merge($treeParents, $tools->getCategoryTreeParents($cat));
					// Gets the children
					$children = array_merge($children, $tools->getCategoryTreeChildren($cat));
				}
			}
			// foreach ($treeParents as $parent) {
				// $delCats[] = $parent['uid'];
			// }
			foreach ($children as $child) {
				$delCats[] = $child['uid'];
			}
			$delCats = array_unique($delCats);
			
			// Removes deleted categories
			$categories = array_diff($categories, $delCats);
		}
		
		//-- Adds children to criteria
		// $childIds = array();
		// $children = array();
		// foreach ($categories as $cat) {
			// $children = $tools->getCategoryTreeChildren($cat);
			// if (is_array($children) && !empty($children)) {
				// foreach ($children as $child) {
					// $childIds[] = $child['uid'];
				// }
				
				// // Adds children
				// $categories = array_merge($categories, $childIds);
			// }
		// }
		
		// Cleans criteria
		$categories = array_unique($categories);
		//-- Cleans value "0"
		$categories = array_diff($categories, array(0));

		$object->catCriteria = $categories;
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
	
	/**
	 * Renders additionnal stats markers
	 *
	 * @param	string	$type: The type of stat (DATASET, FILE)
	 * @param	array	$dataRow: The data row
	 * @param	array	$markers: The markers array
	 * @param	string	$template: The template HTML
	 * @param	array	$conf: The typoscrip configuration
	 * @param	object	$object: The tslib_pibase object
	 * @param	object	$cObj: The tslib_cObj object
	 * @retun	void
	 */
	function additionnalStatsMarkers($type, $dataRow, &$markers, $template, $conf, $object, $cObj) {
		if (!$dataRow['tx_icsodcategories_categories']) {
			$markers['CATEGORY'] = '';
			return;
		}

		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($object->tables['filegroups'], $object->cObj);
		
		$rows = $tools->getCategories(false, t3lib_div::trimExplode(',', $dataRow['tx_icsodcategories_categories'], true));
		
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($rows[0], 'Category');
		$cObj->setParent($this->object, $object->currentRecord);
		
		$markers['CATEGORY'] = $cObj->stdWrap('', $conf['renderObj.'][$type.'.']['category.']);
	}
	
}

?>