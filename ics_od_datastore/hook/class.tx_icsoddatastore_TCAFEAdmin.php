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
 *   52: class tx_icsoddatastore_TCAFEAdmin
 *   83:     function init($pi_base, $table, $field, $fieldLabels=null, $row=null, array $conf)
 *  111:     function handleFormField($pi_base, $table, $field, array $fieldLabels, $row=null, array $conf, $renderer=null)
 *  180:     private function renderForm_filemount($filemounts=null)
 *  217:     function controlEntry($pi_base, $table, $field, $value, array $conf, tx_icstcafeadmin_controlForm $controller, &$control)
 *  255:     function process_valueToDB($pi_base, $table, $field, &$value, $row=null, array $conf, tx_icstcafeadmin_DBTools $dbTools)
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
	private $row=null;

	var $renderer;

	var $dbTools;

	/**
	 * Initialize properties
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	string		$table
	 * @param	string		$field
	 * @param	array		$fieldLabels: : Associative array of fields labels like field=>labelfield
	 * @param	int			$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	void
	 */
	function init($pi_base, $table, $field, $fieldLabels=null, $recordId=0, array $conf) {
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
		
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->table,
			'1' . $this->pibase->cObj->enableFields($this->table) . ' AND uid='.$recordId,
			'',
			'',
			'1'
		);
		if (is_array($rows) && !empty($rows))
			$this->row = $rows[0];
	}

	/**
	 * Generates form entries
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	string		$table
	 * @param	string		$field
	 * @param	array		$fieldLabels: : Associative array of fields labels like field=>labelfield
	 * @param	int			$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	string		HTML form field content
	 */
	function renderEntries($pi_base, $table, array $fields, array $fieldLabels, $recordId=0, array $conf, $renderer=null) {
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');
		
		if ($table!='tx_icsoddatastore_files')
			return '';
			
		$fields = array_diff($fields, array('md5'));
		foreach ($fields as $field) {
			$content .= $renderer->handleFormField($field);
		}
		return $content;
	}
	
	/**
	 * Generates form field
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	string		$table
	 * @param	string		$field
	 * @param	array		$fieldLabels: : Associative array of fields labels like field=>labelfield
	 * @param	int			$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	string		HTML form field content
	 */
	function handleFormField($pi_base, $table, $field, array $fieldLabels, $recordId=0, array $conf, $renderer=null) {
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');
			
		$fields = array('file', 'url', 'record_type');
		if ($table!='tx_icsoddatastore_files' || !in_array($field, $fields))
			return '';
		if (!isset($renderer))
			return '';

		$this->init($pi_base, $table, $field, $fieldLabels, $recordId, $conf);
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
					'file' => $this->renderer->handleFormField_typeGroup_file($field, $config, $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_FILES_FIELD_FILE###')),
					'record_type' => $this->renderer->getEntryValue('record_type'),
				);
				$cObj->start($data, 'File');
				$cObj->setParent($cObj->data, $this->cObj->currentRecord);
				$content = $cObj->stdWrap('', $this->conf['renderForm.'][$table.'.'][$field.'.']);
				break;
			case 'url':
				$cObj = t3lib_div::makeInstance('tslib_cObj');
				$data = array(
					'url' => $this->renderer->handleFormField_typeInput($field, $config),
					'record_type' => $this->renderer->getEntryValue('record_type'),
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
	 * Render filemount entry
	 *
	 * @param	array		$filemounts: Array of filemounts
	 * @return	string		HTML filemount entry content
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
	 * @param	int			$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_controlForm		$controller: Instance of tx_icstcafeadmin_controlForm
	 * @param	&boolean		$control: Reference to control flag
	 *
	 * @return	boolean		"True" if extra eval is processing, otherwise "False"
	 */
	function controlEntry($pi_base, $table, $field, $value, $recordId=0, array $conf, tx_icstcafeadmin_controlForm $controller, &$control) {
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');
			
		$fields = array('file', 'url');
		if ($table!='tx_icsoddatastore_files' || !in_array($field, $fields))
			return false;

		$this->init($pi_base, $table, $field, $fieldLabels, $recordId, $conf);

		switch ($field) {
			case 'file':
				if ($this->piVars['record_type']==0) {	// The record is a file
					if (!$this->row['file']) {
						if (!$this->piVars['tx_icsdatastore_filemount'])
							$control = false;
						if ($control && empty($_FILES[$this->prefixId]['tmp_name'][$field]['file']))
							$control = false;
					}
				}
				break;
			case 'url':
				if ($this->piVars['record_type']==1) {	// The record is an url
					if (empty($this->piVars['url']))
						$control = false;
				}
				break;
		}

		return true;
	}

	/**
	 * Process value to DB
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	string		$table: The table name
	 * @param	string		$field: The fieldname
	 * @param	mixed		$value: The value to process
	 * @param	int			$recordId: The record id
	 * @param	array		$conf: Typoscript configuration
	 * @param	tx_icstcafeadmin_DBTools		$DBTools: Instance of tx_icstcafeadmin_DBTools
	 *
	 * @return	boolean		"True" if the value is processing, otherwise "False"
	 */
	function process_valueToDB($pi_base, $table, $field, &$value, $recordId=0, array $conf, tx_icstcafeadmin_DBTools $dbTools) {		
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');
			
		$fields = array('file', 'url', 'md5');
		if ($table!='tx_icsoddatastore_files' || !in_array($field, $fields))
			return false;

		$this->init($pi_base, $table, $field, null, $recordId, $conf);
		$this->dbTools = $dbTools;

		switch ($field) {
			case 'file':
				$lValue = '';
				if ($this->piVars['record_type']==0) {	// The record is a file
					if ($this->row['file']) {
						$lValue = $this->row['file'];
					}
					else {
						if (!$this->piVars['tx_icsdatastore_filemount'])
							throw new Exception('Required field tx_icsdatastore_filemount must not be empty.');
						if (empty($_FILES[$this->prefixId]['tmp_name'][$field]['file']))
							throw new Exception('Required field ' . $field . ' must not be empty.');
						t3lib_div::loadTCA($this->table);
						$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
						
						$uploadFolder = $this->piVars['tx_icsdatastore_filemount'];
						if ($this->piVars['filegroup']) {
							$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_od_datastore']);
							$uploadFolder = $this->piVars['tx_icsdatastore_filemount'].$extConf['datasetFolder.']['prefix'].$this->piVars['filegroup'].$extConf['datasetFolder.']['suffix'].'/';
							if (!file_exists(t3lib_div::getFileAbsFileName($uploadFolder)))
								t3lib_div::mkdir(t3lib_div::getFileAbsFileName($uploadFolder));
						}
						$newFile = $uploadFolder.basename(t3lib_div::fixWindowsFilePath($_FILES[$this->prefixId]['name'][$field]['file']));
						if (file_exists($newFile))
							@unlink(t3lib_div::getFileAbsFileName($newFile));
						$lValue = $this->dbTools->renderField_group_parseFiles($this->table, $field, $row, $this->piVars[$field], $config, $uploadFolder, false);
					}
				}
				$value = $lValue;
				break;
			case 'url':
				$lValue = '';
				if ($this->piVars['record_type']==1) {	// The record is an url
					$lValue = $value;
					if (empty($value))
						throw new Exception('Required field ' . $field . ' must not be empty.');					
				}
				$value = $lValue;
				break;
			case 'md5':
				$group_files = $this->dbTools->getGroup_files();
				if ($group_files['newFile']['file'])
					$value = md5_file(t3lib_div::getFileAbsFileName($group_files['newFile']['file']));
				else
					$value = md5_file(t3lib_div::getFileAbsFileName($this->row['file']));
				break;
			default:
		}

		return true;
	}

	/**
	 * Delete record
	 *
	 * @param	string		$table: The table name
	 * @param	int			$recordId: The record uid
	 * @param	array		$conf: Typoscript configuration
	 * @param	tslib_pibase	$pi_base: Instance of tslib_pibase
	 * @param	&boolean	$delete: Reference to delete flag
	 *
	 * @return	boolean		"True" if the value is processing, otherwise "False"
	 */
	function deleteRecord($table, $recordId=0, array $conf, $pi_base, &$delete) {
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');
			
		if ($table != 'tx_icsoddatastore_files')
			return false;
		
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'file, record_type',
			$table,
			'1' . $pi_base->cObj->enableFields($table) . ' AND uid='. $recordId,
			'',
			'',
			'1'
		);
		if (is_array($rows) && !empty($rows)) {
			$row = $rows[0];
			if ($row['record_type']==0) {
				@unlink(t3lib_div::getFileAbsFileName($row['file']));
			}
		}
		
		$delete = $pi_base->cObj->DBgetUpdate(
			$table,
			$recordId,
			array('deleted' => '1'),
			'deleted',
			true
		);
		
		return $delete;
	}
}