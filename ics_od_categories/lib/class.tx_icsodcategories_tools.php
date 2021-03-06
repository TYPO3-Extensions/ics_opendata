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
 *   61: class tx_icsodcategories_tools extends tslib_pibase
 *
 *   76:     function init($tablename, $cObj)
 *   87:     function setTableExt($tablename)
 *   96:     function getTableExt()
 *  105:     function getCategories()
 *  131:     function getCategory($uid)
 *  148:     function getCategoriesElement($uid)
 *  169:     function getSQLJoin()
 *  183:     function getSQLWhere($categories = array())
 *  200:     function reinitCategoriesToElement($uid)
 *  214:     function addCategoriesToElement($uid, $listCategories)
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Common for plugins' for the 'ics_od_categories' extension.
 *
 * @author	Emilie Prud'homme <emilie@in-cite.net>
 * @author	Tsi Yang <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsodcategories
 */
class tx_icsodcategories_tools extends tslib_pibase {

	var $tables = array(
		'categories' => 'tx_icsodcategories_categories',
		'mm' => 'tx_icsodcategories_categories_relation_mm',
		'ext' => '',
	); /**< Tablenames categories, relation table and external relation */

	/**
	 * __Constructor
	 *
	 * @param	string		$tablename	external tablename
	 * @param	string		$cObj	cObject
	 * @return	void
	 */
	function init($tablename, $cObj) {
		$this->setTableExt($tablename);
		$this->cObj = $cObj;
	}

	/**
	 * Define name of external table
	 *
	 * @param	string		$tablename	external tablename
	 * @return	void
	 */
	function setTableExt($tablename) {
		$this->tables['ext'] = $tablename;
	}

	/**
	 * Return name of external table
	 *
	 * @return	string
	 */
	function getTableExt() {
		return $this->tables['ext'];
	}

	/**
	 * List of all categories
	 *
	 * @param	boolean	$current Selected used categories only
	 * @param	array	$uids	Categories uids
	 * @param	string	$orderBy: SQL select orderBy
	 * @param	string	$where: SQL select where
	 * @return	array
	 */
	function getCategories($current = false, $uids = null, $orderBy='', $where='') {
		// $where = $innerjoin = '';
		$innerjoin = '';
		if ($current) {
			$innerjoin .= ' INNER JOIN `'.$this->tables['mm'].'`
				ON `'.$this->tables['mm'].'`.`uid_local` = `'. $this->tables['categories'] .'`.`uid`
				INNER JOIN `'.$this->tables['ext'].'`
					ON `'.$this->tables['mm'].'`.`uid_foreign` = `'. $this->tables['ext'] .'`.`uid`
					AND `'.$this->tables['mm'].'`.`tablenames` = \'' . $this->tables['ext'] . '\'';
			$where .= $this->cObj->enableFields($this->tables['ext']);
		}
		if (is_array($uids) && !empty($uids)) {
			$where .= ' AND `' . $this->tables['categories'].'`.`uid` IN (' . implode(',', $uids) . ')';
		}
		// t3lib_div::debug(
			// $GLOBALS['TYPO3_DB']->SELECTQuery (
				// 'DISTINCT `'.$this->tables['categories'].'`.`uid`,
					// `'.$this->tables['categories'].'`.`name`,
					// `'.$this->tables['categories'].'`.`description`,
					// `'.$this->tables['categories'].'`.`parent`,
					// `'.$this->tables['categories'].'`.`picto`',
				// '`'.$this->tables['categories'].'`'.$innerjoin,
				// '1 ' . $this->cObj->enableFields($this->tables['categories']).$where,
				// '',
				// $orderBy,
				// '',
				// 'uid'
			// )
		// );
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'DISTINCT `'.$this->tables['categories'].'`.`uid`,
				`'.$this->tables['categories'].'`.`name`,
				`'.$this->tables['categories'].'`.`description`,
				`'.$this->tables['categories'].'`.`parent`,
				`'.$this->tables['categories'].'`.`picto`',
			'`'.$this->tables['categories'].'`'.$innerjoin,
			'1 ' . $this->cObj->enableFields($this->tables['categories']).$where,
			'',
			$orderBy,
			'',
			'uid'
		);
		
	}

	function getCategoriesTree($parent=0, $level=0) {
		// Get records
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`uid`, `name`, `description`, `parent`, `picto`', 
			'tx_icsodcategories_categories',
			'parent = ' . $parent . ' ' . $this->cObj->enableFields('tx_icsodcategories_categories'),
			'name'
		);
		if (is_array($rows) && !empty($rows)) {
			foreach ($rows as $row) {
				$row['level'] = $level;
				$categories[$row['uid']] = $row;
				$catTree = $this->getCategoriesTree($row['uid'], ($level+1));
				if (is_array($catTree) && !empty($catTree)) {
					$categories = $categories + $catTree;
				}
			}
		}
		// t3lib_div::debug($categories);
		return $categories;
	}
	
	/**
	 * Category data
	 *
	 * @param	int		$uid	category's uid
	 * @return	array
	 */
	function getCategory($uid) {
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'`'.$this->tables['categories'].'`.`uid`,
			`'.$this->tables['categories'].'`.`name`,
			`'.$this->tables['categories'].'`.`description`,
			`'.$this->tables['categories'].'`.`parent`,
			`'.$this->tables['categories'].'`.`picto`',
			'`'.$this->tables['categories'].'`',
			'`'.$this->tables['categories'].'`.`uid` = ' . $uid . ' ' . $this->cObj->enableFields($this->tables['categories'])
		);
	}

	/**
	 * List of elements categories
	 *
	 * @param	int		$uid		uid of external element
	 * @param	string	$orderBy	Order By
	 * @return	array
	 */
	function getCategoriesElement($uid, $orderBy='') {
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'`'.$this->tables['categories'].'`.`uid`,
				`'.$this->tables['categories'].'`.`name`,
				`'.$this->tables['categories'].'`.`description`,
				`'.$this->tables['categories'].'`.`parent`,
				`'.$this->tables['categories'].'`.`picto`',
			'`'.$this->tables['categories'].'`
				INNER JOIN `'.$this->tables['mm'].'`
					ON `'.$this->tables['mm'].'`.`uid_local` = `'.$this->tables['categories'].'`.`uid`
				INNER JOIN `'.$this->tables['ext'].'`
					ON `'.$this->tables['mm'].'`.`uid_foreign` = `'.$this->tables['ext'].'`.`uid`
					AND `'.$this->tables['mm'].'`.`tablenames` = \'' . $this->tables['ext'] . '\'',
			'`'.$this->tables['ext'].'`.`uid` = ' . $uid . ' ' . $this->cObj->enableFields($this->tables['categories']) . $this->cObj->enableFields($this->tables['ext']),
			'',
			$orderBy
		);
	}
	


	/**
	 * SQL Jointure
	 *
	 * @return	string
	 */
	function getSQLJoin() {
		return ' INNER JOIN `'.$this->tables['mm'].'`
					ON `'.$this->tables['mm'].'`.`uid_foreign` = `'.$this->tables['ext'].'`.`uid`
					AND `'.$this->tables['mm'].'`.`tablenames` = \'' . $this->tables['ext'] . '\'
				INNER JOIN `'.$this->tables['categories'].'`
					ON `'.$this->tables['mm'].'`.`uid_local` = `'.$this->tables['categories'].'`.`uid` ';
	}

	/**
	 * SQL Where
	 *
	 * @param	array		$categories
	 * @return	string
	 */
	function getSQLWhere($categories = array()) {
		$where = '';
		$where .= $this->cObj->enableFields($this->tables['categories']);

		if (is_array($categories) && !empty($categories)) {
			$where .= ' AND `'.$this->tables['categories'].'`.`uid` IN (' . implode(',', $categories). ')';
		}

		return $where;
	}

	/**
	 * Remove all Categories to Uid
	 *
	 * @param	int		$uid uid of element
	 * @return	string
	 */
	function reinitCategoriesToElement($uid) {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'`' . $this->tables['mm'] . '`',
			'`uid_foreign` = ' . $uid . ' AND `tablenames` = \'' . $this->tables['ext'] . '\''
		);
	}

	/**
	 * Add Categories to Uid
	 *
	 * @param	int		$uid uid of element
	 * @param	array		$listCategories list of categories
	 * @return	string
	 */
	function addCategoriesToElement($uid, $listCategories) {
		if (is_array($listCategories) && !empty($listCategories))  {
			foreach ($listCategories as $category) {
				if ($category) {
					$data = array(
						'`uid_local`' => $category,
						'`uid_foreign`' => $uid,
						'`tablenames`' => $this->tables['ext'],
					);
					$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'`' . $this->tables['mm'] . '`',
						$data
					);
				}
			}
		}
	}
	
	/**
	 * Retrieves category tree parents
	 *
	 * @param	int	$catId: The category's id
	 * @return	mixed	Array of category parents
	 */
	function getCategoryTreeParents($catId) {
		$catId = intval($catId);
		$treeParents = array();
		do {
			$cat = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow (
				'*',
				'tx_icsodcategories_categories',
				'uid='.$catId
			);
			$treeParents[] = $cat;
			$catId = $cat['parent'];
		} while ($catId!=0);
		return array_reverse($treeParents);
	}
	
	/**
	 * Retrieves category children
	 *
	 * @param	int		$parent: The parent uid
	 */
	function getCategoryTreeChildren($parent) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_icsodcategories_categories',
			'parent='.$parent,
			'',
			'',
			'',
			'uid'
		);
		if (is_array($rows) && !empty($rows)) {
			foreach ($rows as $row) {
				$children = $this->getCategoryTreeChildren($row['uid']);
				if (is_array($children) && !empty($children)) {
					// $rows = array_merge($rows, $children);
					$rows = $rows + $children;
				}
			}
			
		}
		return $rows;
	}
}

?>