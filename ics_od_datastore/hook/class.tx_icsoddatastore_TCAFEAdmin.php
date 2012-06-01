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
 *   58: class tx_icsoddatastore_TCAFEAdmin
 *   90:     function init($pi_base, $table, $fields=null, $fieldLabels=null, $recordId=0, array $conf)
 *  125:     function startOff(&$content, array $conf, $pi_base)
 *  145:     function process_afterInit($table, &$fields, $fieldLabels=null, &$content, &$conf, $pi_base)
 *  171:     function user_TCAFEAdmin(&$content, &$conf, $pi_base)
 *  205:     function renderValue($pi_base, $table, $field, $fieldLabels=null, &$value, $recordId, array $conf, $renderer)
 *  235:     function renderEntries($pi_base, $table, array $fields, array $fieldLabels, $recordId=0, array $conf, $renderer=null)
 *  261:     function handleFormField($pi_base, $table, $field, array $fieldLabels, $recordId=0, array $conf, $renderer=null)
 *  329:     private function renderForm_filemount($filemounts=null)
 *  366:     function controlEntry($pi_base, $table, $field, $value, $recordId=0, array $conf, tx_icstcafeadmin_controlForm $controller, &$control)
 *  410:     function process_valueToDB($pi_base, $table, $field, &$value, $recordId=0, array $conf, tx_icstcafeadmin_DBTools $dbTools)
 *  483:     function deleteRecord($table, $recordId=0, array $conf, $pi_base, &$delete)
 *
 * TOTAL FUNCTIONS: 11
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Class 'tx_icsoddatastore_TCAFEAdmin' for the 'ics_od_datastore' extension.
 *
 * This class implements ics_TCAFE_Admin hooks.
 * It is used to fill and process file field of table tx_icsoddatastore_files.
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
	
	private $oddatastore_tables = array(
		'tx_icsoddatastore_filegroups',
		'tx_icsoddatastore_fileformats',
		'tx_icsoddatastore_licences',
		'tx_icsoddatastore_downloads',
		'tx_icsoddatastore_files',
		'tx_icsoddatastore_tiers',
		'tx_icsoddatastore_filetypes',
		'tx_icsoddatastore_monthdownloads',
		'tx_icsoddatastore_statistics',
	);

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

		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			count($this->fields)? implode(',', $this->fields): '*',
			$this->table,
			'deleted=0 AND uid='.$recordId,
			'',
			'',
			'1'
		);
		if (is_array($rows) && !empty($rows))
			$this->row = $rows[0];
	}

	/**
	 * Process plugin first of all
	 *
	 * @param	string			$table: The tablename
	 * @param	string		$content: The content
	 * @param	array		$conf: Typoscript configuration
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @return	boolean		"True" whether not run into error, otherwise "False"
	 */
	function startOff($table, &$content, array $conf, $pi_base) {
		if (!in_array($table, $this->oddatastore_tables))
			return true;
		
		if (!$GLOBALS['TSFE']->fe_user->user['uid']) {
			tx_icstcafeadmin_debug::error('Any user is logged.');
			$content = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_datastore/hook/locallang.xml:anyUser');
			return false;
		}
		return true;
	}

	/**
	 * Process plugin after init
	 *
	 * @param	string		$table: The tablename
	 * @param	&array		$fields: Array of field name
	 * @param	array		$fieldLabels: Associative array of fields labels like field=>labelfield
	 * @param	&string		$content: The content
	 * @param	array		$conf: Typoscript configuration
	 * @param	tslib_pibase		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @return	boolean		"True" whether not run into error, otherwise "False"
	 */
	function process_afterInit($table, &$fields, $fieldLabels=null, &$content, &$conf, $pi_base) {
		if ($table!='tx_icsoddatastore_filegroups' || !(in_array('files', $fields)))
			return true;

		// Puts field files at end on single view
		if ($pi_base->showUid && in_array('SINGLE', $pi_base->codes)) {
			$locFields = array_diff($fields, array('files'));
			$locFields[] = 'files';
			$fields = $locFields;
		}
		// Removes field files on form view
		if (($pi_base->showUid && in_array('EDIT', $pi_base->codes)) || ($pi_base->newUid && in_array('NEW', $pi_base->codes)) ) {
			$fields = array_diff($fields, array('files'));
		}

		return true;
	}

	/**
	 * Process user TCA FE Admin
	 *
	 * @param	string			$table: The tablename
	 * @param	&string			$content: The PlugIn content
	 * @param	&array			$conf: The PlugIn configuration
	 * @param	tslib_pibase	$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @return	boolean		“True” whether process, otherwise “False”
	 */
	function user_TCAFEAdmin($table, &$content, &$conf, $pi_base) {
		if (!in_array($table, $this->oddatastore_tables) || !$conf['tx_icsoddatastore_files_list.']['from_otherTableView'])
			return false;
		
		if (!$GLOBALS['TSFE']->fe_user->user['uid']) {
			tx_icstcafeadmin_debug::error('Any user is logged.');
			$content = $GLOBALS['TSFE']->sL('LLL:EXT:ics_od_datastore/hook/locallang.xml:anyUser');
			return false;
		}

		$pi_base->init();
		$content = $pi_base->renderContent();
		
		return true;
	}

	/**
	 * Renders value
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	string		$table: The table name
	 * @param	string		$field: The field name
	 * @param	array		$fieldsLabels: Associative array of fields labels like field=>labelfield
	 * @param	mixed		$value : The value to process
	 * @param	int		$recordId: The recordId
	 * @param	array		$conf: Typoscript conf array
	 * @param	object		$renderer: The renderer
	 * @return	boolean		“True” whether the value is processed, otherwise “False”
	 */
	function renderValue($pi_base, $table, $field, $fieldLabels=null, &$value, $recordId, array $conf, $renderer) {
		if ($table!='tx_icsoddatastore_filegroups' || $field!='files' || $conf['currentView']!='viewSingle')
			return false;

		$lConf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_icstcafeadmin_pi1.'];
		$lConf['tx_icsoddatastore_files_list.']['from_otherTableView'] = true;
		$lConf['userFunc'] = 'tx_icstcafeadmin_pi1->user_TCAFEAdmin';
		$lConf['pidStorages'] = implode(',', $renderer->storage);
		$lConf['view.']['modes'] = 'LIST';
		$lConf['table.']['tablename'] = 'tx_icsoddatastore_files';
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start(array());
		$objType = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_icstcafeadmin_pi1'];
		$value = $cObj->cObjGetSingle('USER', $lConf);

		return true;
	}

	/**
	 * Generates form entries
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	string		$table
	 * @param	string		$field
	 * @param	array		$fieldLabels: : Associative array of fields labels like field=>labelfield
	 * @param	int		$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	string		HTML form field content
	 */
	function renderEntries($pi_base, $table, array $fields, array $fieldLabels, $recordId=0, array $conf, $renderer=null) {
		if ($table!='tx_icsoddatastore_files')
			return '';
			
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');

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
	 * @param	int		$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: Instance of tx_icstcafeadmin_FormRenderer
	 * @return	string		HTML form field content
	 */
	function handleFormField($pi_base, $table, $field, array $fieldLabels, $recordId=0, array $conf, $renderer=null) {
		$fields = array('file', 'url', 'record_type');
		if ($table!='tx_icsoddatastore_files' || !in_array($field, $fields))
			return '';
		if (!isset($renderer))
			return '';
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');

		$this->init($pi_base, $table, null, $fieldLabels, $recordId, $conf);
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
	 * @param	int		$recordId : The record id
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_controlForm		$controller: Instance of tx_icstcafeadmin_controlForm
	 * @param	&boolean		$control: Reference to control flag
	 * @return	boolean		"True" if extra eval is processing, otherwise "False"
	 */
	function controlEntry($pi_base, $table, $field, $value, $recordId=0, array $conf, tx_icstcafeadmin_controlForm $controller, &$control) {
		$fields = array('file', 'url');
		if ($table!='tx_icsoddatastore_files' || !in_array($field, $fields))
			return false;
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');

		$this->init($pi_base, $table, null, $fieldLabels, $recordId, $conf);

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
	 * @param	int		$recordId: The record id
	 * @param	array		$conf: Typoscript configuration
	 * @param	tx_icstcafeadmin_DBTools		$DBTools: Instance of tx_icstcafeadmin_DBTools
	 * @return	boolean		"True" if the value is processing, otherwise "False"
	 */
	function process_valueToDB($pi_base, $table, $field, &$value, $recordId=0, array $conf, tx_icstcafeadmin_DBTools $dbTools) {
		$fields = array('file', 'url', 'md5');
		if ($table!='tx_icsoddatastore_files' || !in_array($field, $fields))
			return false;
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');

		$this->init($pi_base, $table, null, null, $recordId, $conf);
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
	 * @param	int		$recordId: The record uid
	 * @param	array		$conf: Typoscript configuration
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @param	&boolean		$delete: Reference to delete flag
	 * @return	boolean		"True" if the value is processing, otherwise "False"
	 */
	function deleteRecord($table, $recordId=0, array $conf, $pi_base, &$delete) {
		if ($table != 'tx_icsoddatastore_files')
			return false;
		if (!$GLOBALS['TSFE']->fe_user->user['uid'])
			throw new Exception('Any user is logged.');

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