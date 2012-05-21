<?php
/**
 * ************************************************************
 *  Copyright notice
 *
 *  (c) 2012 In Cite Solution <techbnique@in-cite.net>
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
 *
 *   41: class tx_icsoddatastore_TCAFEAdmin
 *   72:     function init($pi_base, $table, $field, $fieldLabels=null, $row=null, array $conf)
 *  100:     function handleFormField($pi_base, $table, $field, array $fieldLabels, array $row, array $conf, $renderer=null)
 *  141:     private function renderForm_filemount($filemounts=null)
 *  177:     function controlEntry($pi_base, $table, $field, $value, array $evals, array $conf, tx_icstcafeadmin_controlForm $ctrlForm)
 *  201:     function process_valueToDB($pi_base, $table, $field, &$value, $row=null, array $conf, tx_icstcafeadmin_DBTools $dbTools)
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 
/**
 * Class 'tx_icsoddatastore_TCAFEAdmin' for the 'ics_od_datastore' extension.
 *
 * This class implements ics_TCAFE_Admin hooks. 
 * It is used to fill and process file field of table tx_icsdatastore_files.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */

class tx_icsoddatastore_TCAFEAdmin {
	private $pi_base;
	private $prefixId;
	private $extKey;
	private $conf;
	private $cObj;

	private $piVars;

	private $templateCode;

	private $table;
	private $fields;
	private $fieldLabels;

	var $renderer;

	var $dbTools;

	/**
	 * Initialize properties
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	string		$table
	 * @param	string		$field
	 * @param	array		$fieldLabels: : Associative array of fields labels like field=>labelfield
	 * @param	array		$row: The record row
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	void
	 */
	function init($pi_base, $table, $field, $fieldLabels=null, $row=null, array $conf) {
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
	}

	/**
	 * Generates form field
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	string		$table
	 * @param	string		$field
	 * @param	array		$fieldLabels: : Associative array of fields labels like field=>labelfield
	 * @param	array		$row: The record row
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	string		HTML form field content
	 */
	function handleFormField($pi_base, $table, $field, array $fieldLabels, $row=null, array $conf, $renderer=null) {
		$fields = array('file', 'url', 'record_type');
		if ($table!='tx_icsoddatastore_files' || !in_array($field, $fields))
			return '';
		if (!isset($renderer))
			return '';
			
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');

		$this->init($pi_base, $table, $field, $fieldLabels, $row, $conf);
		$this->renderer = $renderer;

		t3lib_div::loadTCA($this->table);
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
		
		$content = '';
		switch ($field) {
			case 'file':
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'tx_icsoddatastore_filemount',
					'fe_groups',
					'1 ' . $this->cObj->enableFields('fe_groups') . ' AND uid IN(' . $GLOBALS['TSFE']->fe_user->user['usergroup'] . ')',
					'',
					'',
					'',
					'tx_icsoddatastore_filemount'
				);

				if (!is_array($rows) || empty($rows))
					throw new Exception('Any filemount is associate to user ' . $GLOBALS['TSFE']->fe_user->user['username'] . ' fe_group(s).');

				$filemounts = array_keys($rows);
				$cObj = t3lib_div::makeInstance('tslib_cObj');
				$data = array(
					'filemounts' => $this->renderForm_filemount($filemounts),
					'file' => $this->renderer->handleFormField_typeGroup($field, $config),
					'record_type' => $row['record_type'],
				);
				$cObj->start($data, 'File');
				$cObj->setParent($cObj->data, $this->cObj->currentRecord);
				$content = $cObj->stdWrap('', $this->conf['renderForm.'][$table.'.'][$field.'.']);
				break;
			case 'url':
				$cObj = t3lib_div::makeInstance('tslib_cObj');
				$data = array(
					'url' => $this->renderer->handleFormField_typeInput($field, $config),
					'record_type' => $row['record_type'],
				);
				$cObj->start($data, 'File');
				$cObj->setParent($cObj->data, $this->cObj->currentRecord);
				$content = $cObj->stdWrap('', $this->conf['renderForm.'][$table.'.'][$field.'.']);
				break;
			case 'record_type':
				$content = $this->renderer->handleFormField_typeSelect_single($this->renderer->getSelectItemArray($field, $config), $field, $config);
				$content = $this->cObj->stdWrap($content, $this->conf['renderForm.'][$table.'.'][$field.'.']);
				break;
			default:
		}
		
		return $content;
	}

	/**
	 * Render fielmount entry
	 *
	 * @param	array		$filemounts: Array of fielmounts
	 * @return	string		HTML fielmount entry content
	 */
	private function renderForm_filemount($filemounts=null) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_FILEMOUNT###');

		$itemTemplate = $this->renderer->cObj->getSubpart($template, '###GROUP_FILEMOUNTS###');
		foreach ($filemounts as $filemount) {
			$locMarkers = array(
				'FILEMOUNT_ITEM_VALUE' => $filemount,
				'FILEMOUNT_ITEM_SELECTED' => ($this->piVars['tx_icsdatastore_filemount'] == $filemount)? 'selected="selected"': '',
				'FILEMOUNT_ITEM_LABEL' => $filemount,
			);
			$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
		}
		$subparts['###GROUP_FILEMOUNTS###'] = $itemContent;

		$markers = array(
			'FILEMOUNT_ID' => 'tx_icsdatastore_filemount',
			'FILEMOUNT_LABEL' => $this->renderer->getLL('datastore_filemount', 'DATASTORE filemount', true),
			'FILEMOUNT_NAME' => $this->prefixId.'[tx_icsdatastore_filemount]',
		);

		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Control entry
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	string		$table: The table name
	 * @param	string		$field: The fieldname
	 * @param	mixed		$value: The value to conrtol
	 * @param	array		$evals: Array of evals on field
	 * @param	array		$conf: Typoscript conf array (plugin.tx_icstcafeadmin_pi1.controlEntries.file.eval = filemount)
	 * @param	tx_icstcafeadmin_controlForm		$ctrlForm: Instance of tx_icstcafeadmin_controlForm
	 * @return	boolean		"True" if entry is checked, otherwise "False"
	 */
	function controlEntry($pi_base, $table, $field, $value, array $evals, array $conf, tx_icstcafeadmin_controlForm $ctrlForm) {

		if ($table!='tx_icsoddatastore_files' || $field!='file')
			return true;

		if (in_array('filemount', $evals)) {
			if (!$this->piVars['tx_icsdatastore_filemount'])
				return false;
		}
		
		/* TODO
			Vérifier la saisie des champs url et fichier
		*/

		return true;
	}

	/**
	 * Process value to DB
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	string		$table: The table name
	 * @param	array		$row: The record rows
	 * @param	string		$field: The fieldname
	 * @param	mixed		$value: The value to process
	 * @param	tx_icstcafeadmin_DBTools		$DBTools: Instance of tx_icstcafeadmin_DBTools
	 * @return	boolean		"True" if the value is processing, otherwise "False"
	 */
	function process_valueToDB($pi_base, $table, $field, &$value, $row=null, array $conf, tx_icstcafeadmin_DBTools $dbTools) {
		if ($table!='tx_icsoddatastore_files' || $field!='file')
			return false;

		$this->init($pi_base, $table, $field, null, $row, $conf);
		$this->dbTools = $dbTools;

		if (!$this->piVars['tx_icsdatastore_filemount'])
			throw new Exception('Filemount must not be empty.');

		$fileArray = array();
		$fileArray = t3lib_div::getAllFilesAndFoldersInPath (
			$fileArray,
			$this->piVars['tx_icsdatastore_filemount']
		);

		var_dump($fileArray);

		return true;
	}
}