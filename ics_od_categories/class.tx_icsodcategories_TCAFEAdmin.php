<?php
/**
 * ************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Plan Net France <typo3@plan-net.fr>
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
 * **************************************************************/
 /**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 */

/**
 * Class 'tx_icsodcategories_TCAFEAdmin' for the 'ics_od_categories' extension.
 *
 * This class implements ics_TCAFE_Admin hooks.
 *
 * @author	Emilie Sagniez <emilie.sagniez@plan-net.fr>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icsodcategories_TCAFEAdmin {
	protected $pi_base;
	protected $prefixId;
	protected $extKey;
	protected $conf;
	protected $cObj;

	protected $piVars;

	protected $templateCode;

	protected $table;
	protected $fields;
	protected $fieldLabels;
	protected $row=null;

	var $renderer;

	var $dbTools;

	/**
	 * Initialize properties
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	string		$table
	 * @param	array		$fields: Array of fieldname
	 * @param	array		$fieldLabels: : Associative array of fields labels like field=>labelfield
	 * @param	int		$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	void
	 */
	function init($pi_base, $table, $fields=null, $fieldLabels=null, $recordId=0, array $conf) {
		$this->pibase = $pi_base;
		$this->prefixId = $pi_base->prefixId;
		$this->extKey = $pi_base->extKey;
		$this->conf = $conf;
		$this->cObj = $pi_base->cObj;

		$this->piVars = $pi_base->piVars;

		$this->templateCode = $pi_base->templateCode;

		$this->table = $table;
		$this->fields = $fields;
		$this->fieldLabels = $fieldLabels;

		if ($recordId = intval($recordId)) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				count($this->fields)? implode(',', $this->fields): '*',
				$this->table,
				'deleted=0 AND uid='.$recordId,
				'',
				'',
				'1'
			);
		}
		if (is_array($rows) && !empty($rows))
			$this->row = $rows[0];
	}
	
	/**
	 * Retrieves selector box items (pair of key/label)
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	string		$table
	 * @param	string		$field
	 * @param	array		$fieldLabels: : Associative array of fields labels like field=>labelfield
	 * @param	int		$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	mixed		Item array where item is an associative array with value/label
	 */
	function getSelectItemArray($pi_base, $table, $field, array $fieldLabels, $recordId=0, array $conf, $renderer=null) {
		$fields = array('tx_icsodcategories_categories');
		if ($table!='tx_icsoddatastore_filegroups' || !in_array($field, $fields) || !isset($renderer))
			return null;

		$this->init($pi_base, $table, null, $fieldLabels, $recordId, $conf);
		$this->renderer = $renderer;

		$items = array();
		t3lib_div::loadTCA('tx_icsodcategories_categories');
		if ($label = $GLOBALS['TCA']['tx_icsodcategories_categories']['ctrl']['label']) {
			$items[] = array('value'=>0);
			$this->getFilegroupsCategoriesTree($items, $label);
		}
		
		return $items;
	}
	
	function getFilegroupsCategoriesTree(&$items, $label, $uidParent = 0, $incr = 0) {
		// Get records
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`uid` AS value, `'.$label.'` AS label',
			'tx_icsodcategories_categories',
			'parent = ' . $uidParent . ' ' . $this->cObj->enableFields('tx_icsodcategories_categories'),
			'name'
		);
		if (is_array($rows) && !empty($rows)) {
			foreach ($rows as $row) {
				$row['incr'] = $incr;
				$items[] = $row;
				$this->getFilegroupsCategoriesTree($items, $label, $row['value'], ($incr+1));
			}
		}
	}
	
	function handleFormField_typeSelect_multiple_addMarkers($item, $field, $options, &$locMarkers, $conf, $obj) {
		if ($field != 'tx_icsodcategories_categories') 
			return null;
		$locMarkers['OPTION_INCR'] = $item['incr'] ? $item['incr'] : 0;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_categories/class.tx_icsodcategories_TCAFEAdmin.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_categories/class.tx_icsodcategories_TCAFEAdmin.php']);
}