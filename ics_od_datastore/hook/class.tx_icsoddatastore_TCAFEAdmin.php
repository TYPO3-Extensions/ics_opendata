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
 *   64: class tx_icsoddatastore_TCAFEAdmin
 *  108:     function init($pi_base, $table, $fields=null, $fieldLabels=null, $recordId=0, array $conf)
 *  144:     function startOff($table, &$content, array $conf, $pi_base)
 *  167:     function process_afterInit($table, &$fields, $fieldLabels=null, &$content, &$conf, $pi_base)
 *  228:     protected function checkFEEdit($pi_base, $dataset)
 *  259:     function user_TCAFEAdmin($table, &$content, &$conf, $pi_base)
 *  288:     function renderValue($pi_base, $table, $field, $fieldLabels=null, &$value, $recordId, array $conf, $renderer)
 *  347:     function single_additionnalMarkers($template, &$markers, &$subpartArray, $table, $field, $row=null, $conf, $pi_base, $renderer)
 *  383:     function renderEntries($pi_base, $table, array $fields, array $fieldLabels, $recordId=0, array $conf, $renderer=null)
 *  408:     function formRenderer_additionnalMarkers($template, &$markers, &$subpartArray, $table, $field, $row, $conf, $pi_base, $renderer)
 *  431:     function handleFormField($pi_base, $table, $field, array $fieldLabels, $recordId=0, array $conf, $renderer=null)
 *  500:     protected function renderForm_filemount($filemounts=null)
 *  537:     function controlEntry($pi_base, $table, $field, $value, $recordId=0, array $conf, tx_icstcafeadmin_controlForm $controller, &$control)
 *  577:     public function extra_controlEntries(&$control, $table, $row, $pi_base, $conf, tx_icstcafeadmin_controlForm $controller)
 *  599:     function process_valueToDB($pi_base, $table, $field, &$value, $recordId=0, array $conf, tx_icstcafeadmin_DBTools $dbTools)
 *  669:     function deleteRecord($table, $recordId=0, array $conf, $pi_base, &$delete)
 *  713:     function getRecords($table, array $requestFields, $whereClause='', $groupBy='', $orderBy='', $limit='', &$rows, array $conf, $pi_base)
 *  773:     public function actions_additionnalDataArray(&$data, $table, $row, $conf, $renderer)
 *
 * TOTAL FUNCTIONS: 17
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

	protected $oddatastore_tables = array(
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
		
		$this->merge_locallang($pi_base, $conf);
	}
	
	/**
	 * Merge locallang
	 *
	 * @param	tslib_pibase	$pi_base
	 * @return	void
	 */
	function merge_locallang($pi_base, $conf) {
		$local_lang = t3lib_div::readLLfile(t3lib_div::getFileAbsFileName('EXT:ics_od_datastore/hook/locallang.xml'), $GLOBALS['TSFE']->lang);
		$pi_base->LOCAL_LANG['default'] = array_merge($pi_base->LOCAL_LANG['default'], $local_lang['default']);
		$pi_base->LOCAL_LANG[$GLOBALS['TSFE']->lang] = array_merge($pi_base->LOCAL_LANG[$GLOBALS['TSFE']->lang], $local_lang[$GLOBALS['TSFE']->lang]);
		
		$confLL = $conf['_LOCAL_LANG.'];
		if (is_array($confLL)) {
			foreach ($confLL as $k => $lA) {
				if (is_array($lA)) {
					$k = substr($k,0,-1);
					foreach($lA as $llK => $llV) {
						if (!is_array($llV)) {
							$pi_base->LOCAL_LANG[$k][$llK] = $llV;
							$pi_base->LOCAL_LANG_charset[$k][$llK] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : $GLOBALS['TSFE']->csConvObj->charSetArray[$k];
						}
					}
				}
			}
		}
	}

	/**
	 * Process plugin first of all
	 *
	 * @param	string		$table: The tablename
	 * @param	string		$content: The content
	 * @param	array		$conf: Typoscript configuration
	 * @param	tslib_pibase		$pi_base: Instance of tslib_pibase
	 * @return	boolean		"True" whether not run into error, otherwise "False"
	 */
	function startOff($table, &$content, array $conf, $pi_base) {
		if (!in_array($table, $this->oddatastore_tables))
			return true;

		$this->merge_locallang($pi_base, $conf);
		
		if (!$GLOBALS['TSFE']->fe_user->user['uid']) {
			tx_icstcafeadmin_debug::error('Any user is logged.');
			$content = $pi_base->pi_getLL('anyUser');
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
		if (!in_array($table, $this->oddatastore_tables))
			return true;

		$this->merge_locallang($pi_base, $conf);
			
		if ($GLOBALS['TSFE']->fe_user->user['tx_icsoddatastore_tiers']<=0) {
			tx_icstcafeadmin_debug::error('Any allowed tiers.');
			$content = $pi_base->pi_getLL('anyAllowedTiers');
			return false;
		}
		
		// Process  tx_icsoddatastore_filegroups
		if ($table == 'tx_icsoddatastore_filegroups') {
			if ($pi_base->showUid && !$this->checkFEEdit($pi_base, $pi_base->showUid)) {
				if(in_array('EDIT', $pi_base->codes)) {
					tx_icstcafeadmin_debug::error('You are not allowed to edit dataset.');
					$content = $pi_base->pi_getLL('notAllowedEditDataset');
				}
				else {
					tx_icstcafeadmin_debug::error('You do not have access rights to the dataset.');
					$content = $pi_base->pi_getLL('notAllowedAccessDataset');
				}
				return false;
			}
			if (in_array('files', $fields)) {
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
			}
		}
		// Process tx_icsoddatastore_files
		if ($table == 'tx_icsoddatastore_files') {
			if ($pi_base->piVars['mode']=='NEW') {
				$conf['table.']['dataset'] = $pi_base->piVars['dataset'];
			}
			elseif ($pi_base->showUid) {
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'tx_icsoddatastore_files_filegroup_mm.uid_foreign as dataset',
					'tx_icsoddatastore_files
						JOIN tx_icsoddatastore_files_filegroup_mm ON tx_icsoddatastore_files.uid = tx_icsoddatastore_files_filegroup_mm.uid_local',
					'tx_icsoddatastore_files.uid = ' . $pi_base->showUid,
					'',
					'1'
				);
				if (is_array($rows))
					$conf['table.']['dataset'] = $rows[0]['dataset'];
			}
			if (!$conf['table.']['dataset'] || !$this->checkFEEdit($pi_base, $conf['table.']['dataset'])) {
				if(in_array('EDIT', $pi_base->codes)) {
					tx_icstcafeadmin_debug::error('You are not allowed to edit dataset files.');
					$content = $pi_base->pi_getLL('notAllowedEditDatasetFiles');
				}
				else {
					tx_icstcafeadmin_debug::error('You do not have access rights the dataset files.');
					$content = $pi_base->pi_getLL('notAllowedAccessDatasetFiles');
				}
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks FE Edit
	 *
	 * @param	tslib_pibase		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	int		$dataset: Dataset Id
	 * @return	boolean		"TRUE" whether FE edit is cheched, otherwise "FALSE"
	 */
	protected function checkFEEdit($pi_base, $dataset) {
		t3lib_div::loadTCA('fe_users');
		$config = $GLOBALS['TCA']['fe_users']['columns']['tx_icsoddatastore_tiers']['config'];
		$loadDBGroup = t3lib_div::makeInstance('FE_loadDBGroup');
		$loadDBGroup->start('', 'tx_icsoddatastore_tiers', 'tx_icsoddatastore_feusers_tiers_mm', $GLOBALS['TSFE']->fe_user->user['uid'], 'fe_users', $config);
		foreach($loadDBGroup->itemArray as $item) {
			$allowed_tiers[] = $item['id'];
		}
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, agency, contact, publisher, creator, manager, owner',
			'tx_icsoddatastore_filegroups',
			'1  AND uid=' . $dataset .
				' AND (agency IN('.implode(',', $allowed_tiers).') OR contact IN('.implode(',', $allowed_tiers).')
					OR publisher IN('.implode(',', $allowed_tiers).') OR creator IN('.implode(',', $allowed_tiers).')
					OR manager IN('.implode(',', $allowed_tiers).') OR owner IN('.implode(',', $allowed_tiers).'))'
		);
		if (empty($rows))
			return false;

		return true;
	}

	/**
	 * Process user TCA FE Admin
	 *
	 * @param	string		$table: The tablename
	 * @param	&string		$content: The PlugIn content
	 * @param	&array		$conf: The PlugIn configuration
	 * @param	tslib_pibase		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @return	boolean		�True� whether process, otherwise �False�
	 */
	function user_TCAFEAdmin($table, &$content, &$conf, $pi_base) {
		if (!in_array($table, $this->oddatastore_tables) || !$conf['tx_icsoddatastore_files_list.']['from_otherTableView'])
			return false;

		$this->merge_locallang($pi_base, $conf);
		
		if (!$GLOBALS['TSFE']->fe_user->user['uid']) {
			tx_icstcafeadmin_debug::error('Any user is logged.');
			$content = $pi_base->pi_getLL('anyUser');
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
	 * @return	boolean		�True� whether the value is processed, otherwise �False�
	 */
	function renderValue($pi_base, $table, $field, $fieldLabels=null, &$value, $recordId, array $conf, $renderer) {
		$this->cObj = $pi_base->cObj;
		$result = false;
		switch ($table) {
			case 'tx_icsoddatastore_filegroups':
				if ($field=='files' && $conf['currentView']=='viewSingle') {
					$lConf = $GLOBALS['TSFE']->tmpl->setup['tx_icsoddatastore_files.']['config.']['tx_icstcafeadmin_pi1.'];
					if (!$lConf['pidStorages'])
						$lConf['pidStorages'] = implode(',', $renderer->storage);
					$lConf['table.']['dataset'] = $recordId;

					$cObj = t3lib_div::makeInstance('tslib_cObj');
					$cObj->start(array());
					$value = $cObj->cObjGetSingle('USER', $lConf);
					$result = true;
				}
				break;
			case 'tx_icsoddatastore_files':
				switch ($field) {
					case 'file':
						if ($conf['currentView']=='viewList') {
							if ($recordId = intval($recordId)) {
								$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
									'*',
									'tx_icsoddatastore_files',
									'uid='.$recordId
								);
							}
							if ($row['record_type']) {
								$value = $this->cObj->stdWrap($row['url'], $conf['renderConf.']['tx_icsoddatastore_files.']['url.']['viewList.']);
							}
							else {
								$value = $this->cObj->stdWrap($row['file'], $conf['renderConf.']['tx_icsoddatastore_files.']['file.']['viewList.']);
							}

							$result = true;
						}
						break;
					default:
				}
				break;
			default:
		}

		return $result;
	}

	/**
	 * Renders single additionnalMarkers
	 *
	 * @param	string		$template: The template HTML
	 * @param	&array		$markers: The marker array
	 * @param	&array		$subpartArray: The zsubpart array
	 * @param	string		$table: The table name
	 * @param	string		$field : The field name
	 * @param	array		$row: The record
	 * @param	array		$conf: Typoscript conf array
	 * @param	object		$renderer: The renderer
	 * @param	[type]		$renderer: ...
	 * @return	boolean		�True� whether the value is processed, otherwise �False�
	 */
	function single_additionnalMarkers($template, &$markers, &$subpartArray, $table, $field, $row=null, $conf, $pi_base, $renderer) {
		if ($table!='tx_icsoddatastore_filegroups' || $field!='files')
			return false;

		// New file
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$data = $renderer->cObjDataActions(null);
		$data['table'] = 'tx_icsoddatastore_files';
		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA('tx_icsoddatastore_files');
		$data['fields'] = implode(',', array_keys($GLOBALS['TCA']['tx_icsoddatastore_files']['columns']));
		$data['dataset'] = $row['uid'];
		$cObj->start($data, 'TCAFE_Admin_actions');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		$markers['NEW_FILE'] = $cObj->stdWrap('', $conf['renderOptions.']['singleDatasetOptionList.']['new.']);

		// Edit dataset
		$cObj->start($renderer->cObjDataActions($row), 'TCAFE_Admin_actions');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		$markers['EDIT_DATASET'] = $cObj->stdWrap('', $conf['renderOptions.']['singleDatasetOptionList.']['edit.']);

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

		$fields = array_diff($fields, array('md5'));
		$template = $template? $template: $pi_base->templateCode;
		foreach ($fields as $field) {
			$subTemplate = $pi_base->cObj->getSubpart($template, '###ALT_SUBPART_FORM_'.strtoupper($field).'###');
			$content .= $renderer->handleFormField($field, $subTemplate);
		}
		return $content;
	}

	/**
	 * Renders form renderer additionnal markers
	 *
	 * @param	string		$template: The template HTML
	 * @param	&array		$markers: The marker array
	 * @param	&array		$subpartArray: The subpart array
	 * @param	string		$table: The table name
	 * @param	string		$field : The field name
	 * @param	array		$row: The record
	 * @param	array		$conf: Typoscript conf array
	 * @param	tslib_pibase		$renderer: The pi
	 * @param	tx_icstcafeadmin_FormRenderer		$renderer: The renderer
	 * @return	boolean		�True� whether the value is processed, otherwise �False�
	 */
	function formRenderer_additionnalMarkers($template, &$markers, &$subpartArray, $table, $field, $row, $conf, $pi_base, $renderer) {
		if (!in_array($table, $this->oddatastore_tables))
			return false;

		$this->merge_locallang($pi_base, $conf);
		
		$locMarkers = array($pi_base);
		switch ($table) {
			case 'tx_icsoddatastore_filegroups':
				$locMarkers = array(
					'TEXT_INFO' => $pi_base->pi_getLL('dataset_form_info'),
				);
				break;
			case 'tx_icsoddatastore_files':
				$locMarkers = array(
					'TEXT_INFO' => $pi_base->pi_getLL('files_form_info'),
				);
				break;
			default:
		}
		$markers = array_merge($markers, $locMarkers);
		return true;
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
		$fields = array('file', 'url', 'record_type', 'filegroup');
		if ($table!='tx_icsoddatastore_files' || !in_array($field, $fields))
			return '';
		if (!isset($renderer))
			return '';

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
					'1 ' . $this->cObj->enableFields('fe_groups') . ' AND uid IN(' . $GLOBALS['TSFE']->fe_user->user['usergroup'] . ') AND tx_icsoddatastore_filemount!=\'\'',
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
					'filemounts' => $this->pibase->showUid? '': $this->renderForm_filemount($filemounts),
					'file' => $this->renderer->handleFormField_typeGroup_file($field, $config, $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_FILES_FIELD_FILE###')),
					'record_type' => $this->row['record_type'],
				);
				$cObj->start($data, 'File');
				$cObj->setParent($cObj->data, $this->cObj->currentRecord);
				$content = $cObj->stdWrap('', $this->conf['renderForm.'][$this->table.'.'][$field.'.']);
				break;
			case 'url':
				$cObj = t3lib_div::makeInstance('tslib_cObj');
				$data = array(
					'url' => $this->renderer->handleFormField_typeInput($field, $config),
					'record_type' => $this->row['record_type'],
				);
				$cObj->start($data, 'File');
				$cObj->setParent($cObj->data, $this->cObj->currentRecord);
				$content = $cObj->stdWrap('', $this->conf['renderForm.'][$this->table.'.'][$field.'.']);
				break;
			case 'record_type':
				$content = $this->renderer->handleFormField_typeSelect_single($this->renderer->getSelectItemArray($field, $config), $field, $config);
				$content = $this->cObj->stdWrap($content, $this->conf['renderForm.'][$this->table.'.'][$field.'.']);
				break;
			case 'filegroup':
				$content = $this->cObj->stdWrap($conf['table.']['dataset'], $this->conf['renderForm.'][$this->table.'.'][$field.'.']);
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
	protected function renderForm_filemount($filemounts=null) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_FILEMOUNT###');

		$itemTemplate = $this->renderer->cObj->getSubpart($template, '###GROUP_FILEMOUNTS###');
		foreach ($filemounts as $filemount) {
			$selected = ($this->piVars['tx_icsdatastore_filemount'] == $filemount)? 'selected="selected"': '';
			$selected = (count($filemounts)>1)? $selected: 'selected="selected"';
			$locMarkers = array(
				'FILEMOUNT_ITEM_VALUE' => $filemount,
				'FILEMOUNT_ITEM_SELECTED' =>  $selected,
				'FILEMOUNT_ITEM_LABEL' => $filemount,
			);
			$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
		}
		$subparts['###GROUP_FILEMOUNTS###'] = $itemContent;

		$markers = array(
			'FILEMOUNT_ID' => 'tx_icsdatastore_filemount',
			'FILEMOUNT_LABEL' => $this->cObj->stdWrap($this->pibase->pi_getLL('datastore_filemount'), $this->conf['renderForm.'][$this->table.'.']['datastore_filemount.']['label.']),
			'FILEMOUNT_NAME' => $this->prefixId.'[tx_icsdatastore_filemount]',
		);

		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Generate Form field type "select"
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
	function handleFormField_typeSelect($pi_base, $table, $field, array $fieldLabels, $recordId=0, array $conf, $renderer=null) {
		$fields = array('agency', 'contact', 'publisher', 'creator', 'manager', 'owner');
		if ($table!='tx_icsoddatastore_filegroups' || !in_array($field, $fields) || !isset($renderer))
			return null;

		$this->init($pi_base, $table, null, $fieldLabels, $recordId, $conf);
		$this->renderer = $renderer;
			
		$addWhere_tablenames = ' AND (`tx_icsoddatastore_feusers_tiers_mm`.`tablenames` = \'fe_users\' || `tx_icsoddatastore_feusers_tiers_mm`.`tablenames` = \'\')';
		// Get records
		$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			'`tx_icsoddatastore_tiers`.`uid` as value, `tx_icsoddatastore_tiers`.`name` as label',
			'tx_icsoddatastore_tiers',
			'tx_icsoddatastore_feusers_tiers_mm',
			'fe_users',
			' AND `tx_icsoddatastore_feusers_tiers_mm`.`uid_foreign` = ' . $GLOBALS['TSFE']->fe_user->user['uid'] . $addWhere_tablenames,
			'',
			'sorting_foreign'
		);
		$feuser_tiers = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$feuser_tiers[] = $row['value'];
		}
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
		if (!$this->row[$field] || ($this->row[$field] && in_array($this->row[$field], $feuser_tiers))) {
			$content = $renderer->handleFormField_typeSelect_single($renderer->getSelectItemArray($field, $config), $field, $config, $conf['subTemplate']);
		}
		else {
			$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
				'name as label',
				'tx_icsoddatastore_tiers',
				'1' . $this->cObj->enableFields('tx_icsoddatastore_tiers') . ' AND uid='. $this->row[$field]
			);
			$template = $this->cObj->getSubpart($renderer->templateCode, '###TEMPLATE_FORM_TIERS_INFO###');
			$markers = array(
				'FIELDLABEL' => $this->cObj->stdWrap($fieldLabels[$field], $this->conf['renderConf.'][$this->table.'.'][$field.'.']['viewForm.']['label.']),
				'FIELDVALUE' => $row['label'],
				'HIDDEN_VALUE' => '<input type="hidden" name="tx_icstcafeadmin_pi1['.$field.']" value="'.$this->row[$field].'" />',
			);
			$content = $this->cObj->substituteMarkerArray($template, $markers, '###|###');
		}
		return $content;
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
		$fields = array('agency', 'contact', 'publisher', 'creator', 'manager', 'owner');
		if ($table!='tx_icsoddatastore_filegroups' || !in_array($field, $fields) || !isset($renderer))
			return null;

		$this->init($pi_base, $table, null, $fieldLabels, $recordId, $conf);
		$this->renderer = $renderer;

		$addWhere_tablenames = ' AND (`tx_icsoddatastore_feusers_tiers_mm`.`tablenames` = \'fe_users\' || `tx_icsoddatastore_feusers_tiers_mm`.`tablenames` = \'\')';
		// Get records
		$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			'`tx_icsoddatastore_tiers`.`uid` as value, `tx_icsoddatastore_tiers`.`name` as label',
			'tx_icsoddatastore_tiers',
			'tx_icsoddatastore_feusers_tiers_mm',
			'fe_users',
			' AND `tx_icsoddatastore_feusers_tiers_mm`.`uid_foreign` = ' . $GLOBALS['TSFE']->fe_user->user['uid'] . $addWhere_tablenames,
			'',
			'sorting_foreign'
		);
		$items = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$items[] = $row;
			$values[] = $row['value'];
			$labels[] = $row['label'];
		}
		array_multisort($labels, SORT_ASC, $values, SORT_ASC, $items);
		$items = array_merge(
			array('value'=>0),
			$items
		);
		
		return $items;
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
	 * Control entry
	 *
	 * @param	&boolean		$control: Reference to control flag
	 * @param	string		$table: The table name
	 * @param	array		$row : The record row
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_controlForm		$controller: Instance of tx_icstcafeadmin_controlForm
	 * @return	boolean		"True" whether extra control is processing, otherwise "False"
	 */
	public function extra_controlEntries(&$control, $table, $row, $pi_base, $conf, tx_icstcafeadmin_controlForm $controller) {
		if ($table!='tx_icsoddatastore_filegroups')
			return false;

		$control = $pi_base->piVars['agency'] || $pi_base->piVars['contact'] || $pi_base->piVars['publisher'] || $pi_base->piVars['creator'] || $pi_base->piVars['manager'] || $pi_base->piVars['owner'];

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
	 * @return	boolean		�True� whether process, otherwise �False�
	 */
	function deleteRecord($table, $recordId=0, array $conf, $pi_base, &$delete) {
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

		return true;
	}

	/**
	 * Retrieves records
	 *
	 * @param	string		$table: The table name
	 * @param	array		$requestFields: The array of request fields
	 * @param	string		$whereClause:
	 * @param	string		$groupBy:
	 * @param	string		$orderBy:
	 * @param	string		$limit:
	 * @param	mixed		$rows :
	 * @param	array		$conf: Typoscript conf array
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @return	boolean		�True� whether process, otherwise �False�
	 */
	function getRecords($table, array $requestFields, $whereClause='', $groupBy='', $orderBy='', $limit='', &$rows, array $conf, $pi_base) {
		// Retrieves tx_icsoddatastore_files records
		if ($table == 'tx_icsoddatastore_files' && $conf['table.']['dataset']) {
			foreach ($requestFields as $field) {
				$select[] = $table.'.'.$field . ' AS ' . $field;
			}
			$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				implode(',', $select),
				'tx_icsoddatastore_files',
				'tx_icsoddatastore_files_filegroup_mm',
				'tx_icsoddatastore_filegroups',
				' AND ' . $whereClause . ' AND tx_icsoddatastore_files_filegroup_mm.uid_foreign='.$conf['table.']['dataset'],
				$groupBy,
				$orderBy,
				$limit
			);
			$output = array();
			while ($output[] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {;}
			array_pop($output);
			$GLOBALS['TYPO3_DB']->sql_free_result($result);
			$rows = $output;
			return true;
		}
		// Retrieves tx_icsoddatastore_filegroups records
		if ($table == 'tx_icsoddatastore_filegroups') {
			t3lib_div::loadTCA('fe_users');
			$config = $GLOBALS['TCA']['fe_users']['columns']['tx_icsoddatastore_tiers']['config'];
			$loadDBGroup = t3lib_div::makeInstance('FE_loadDBGroup');
			$loadDBGroup->start('', 'tx_icsoddatastore_tiers', 'tx_icsoddatastore_feusers_tiers_mm', $GLOBALS['TSFE']->fe_user->user['uid'], 'fe_users', $config);
			foreach($loadDBGroup->itemArray as $item) {
				$allowed_tiers[] = $item['id'];
			}
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				implode(',', $requestFields),
				'tx_icsoddatastore_filegroups',
				$whereClause .
					' AND (agency IN('.implode(',', $allowed_tiers).') OR contact IN('.implode(',', $allowed_tiers).')
					OR publisher IN('.implode(',', $allowed_tiers).') OR creator IN('.implode(',', $allowed_tiers).')
					OR manager IN('.implode(',', $allowed_tiers).') OR owner IN('.implode(',', $allowed_tiers).'))',
				$groupBy,
				$orderBy,
				$limit,
				'uid'
			);
			return false;
		}

		return false;
	}

	/**
	 * Retrieves tslib_cObj data array
	 *
	 * @param	array		$data: The data array
	 * @param	string		$table: The table name
	 * @param	array		$row: The record row
	 * @param	array		$conf: The conf
	 * @param	tx_icstcafeadmin_ListRenderer		$renderer: the renderer
	 * @return	boolean
	 */
	public function actions_additionnalDataArray(&$data, $table, $row, $conf, $renderer) {
		if ($table != 'tx_icsoddatastore_files')
			return false;

		$data['dataset'] = $conf['table.']['dataset'];
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php']);
}