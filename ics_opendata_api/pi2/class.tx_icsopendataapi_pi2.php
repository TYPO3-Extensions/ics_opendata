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
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(t3lib_extMgm::extPath('ics_opendata_api') . 'lib/randomGenerator.php');
require_once(t3lib_extMgm::extPath('ics_opendata_api') . 'lib/class.tx_icsopendataapi_common.php');


/**
 * Plugin 'Update application form' for the 'ics_opendata_api' extension.
 *
 * @author	Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendataapi
 */
class tx_icsopendataapi_pi2 extends tx_icsopendataapi_common {
	var $prefixId      = 'tx_icsopendataapi_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_icsopendataapi_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_opendata_api';	// The extension key.

	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->uniq = uniqid($this->extKey);
	
		$this->init();
	
		// Insert style and javascript on header of the page
		if (isset($this->conf['styleApi'])) {
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<link rel="stylesheet" type="text/css" href="' . $this->conf['styleApi'] .'" />';
		}
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] .= '<script src="typo3conf/ext/ics_opendata_api/res/script.js" type="text/javascript"></script>';
		
		t3lib_div::loadTCA($this->tables['applications']);
		
		if ($this->conf['pid'] == "") {
			return $this->pi_wrapInBaseClass($this->renderContentError($this->pi_getLL('pid_valid')));
		}
		if (!$GLOBALS['TSFE']->fe_user->user['uid']) {
			return $this->pi_wrapInBaseClass($this->renderContentError($this->pi_getLL('nologin')));
		}
		
		$usergroup = t3lib_div::trimExplode(',',  $this->conf['usergroup'], true);
		$groupsUser = explode(',',$GLOBALS['TSFE']->gr_list,$GLOBALS['TSFE']->fe_user->user['uid']);
		
		$validUser = false;
		if (!empty($usergroup) && is_array($groupsUser) && !empty($groupsUser)) {
			foreach($usergroup as $group) {
				if (in_array($group, $groupsUser)) {
					$validUser = true;
					break;
				}
			}
		}
		
		if (!$validUser) {
			return $this->pi_wrapInBaseClass($this->renderContentError($this->pi_getLL('error_group')));
		}
		
		$uid = $this->piVars['keyuid'];
		
		if ($uid) {
			// EDIT 
			$applications = $this->getApplications(tx_icsopendataapi_common::APPMODE_SINGLEUSER, null, $uid);
			if (!$applications) {
				$content .= $this->renderContentError($this->pi_getLL('application_not_exists'));
			}else{
				$errors = array();
				if ($this->piVars['btn_registration']) { // Form is submitted
					// Hook to validate extra fields 
					if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'])) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'] as $_classRef) {
							$_procObj = & t3lib_div::getUserObj($_classRef);
							$succes = $_procObj->applicationFieldsValidate($errors, $this->conf, $this);
						}
					}
					$succes = ($succes)? $this->updateDB(true, $errors, $applications[0]) : $succes;
					$applications = $this->getApplications(tx_icsopendataapi_common::APPMODE_SINGLEUSER, null, $uid);
				}
				$content .= $this->renderFormEdit($applications[0], $errors);
			}
		} else {
			// CREATE
			if ($this->piVars['btn_registration']) { // Form is submitted
				$errors = array();
				// Hook to validate extra fields 
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'])) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'] as $_classRef) {
						$_procObj = & t3lib_div::getUserObj($_classRef);
						$succes = $_procObj->applicationFieldsValidate($errors, $this->conf, $this);
					}
				}
				$succes = ($succes)? $this->updateDB(false, $errors) : $succes;
				
				if ($succes) {
					$content .= $this->renderSucces($errors);
				} else {
					$content .= $this->renderFormCreate($errors);
				}
			} else
				$content .= $this->renderFormCreate();
		}
			
		
				
		return $this->pi_wrapInBaseClass($content);
	}
	
	function renderSucces($messages) {
		// Get the application form template
		$html = $this->cObj->fileResource($this->templateFile);
		$template = array();
		$template = $this->cObj->getSubpart($html, '###TEMPLATE_APPLICATION_FORM_SUCCES###');	
		
		$output_messages = '';
		if (!empty($messages)) {
			$subpart_messages = $this->cObj->getSubpart($template, '###TEMPLATE_MESSAGES###');	
			foreach($messages as $message) {
				$markers = array('###MESSAGE###' => $message);
				$output_messages .= $this->cObj->substituteMarkerArray($subpart_messages, $markers);
			}
		}
		$template = $this->cObj->substituteSubpart($template, '###TEMPLATE_MESSAGES###', $output_messages);
		
		$markers = array(
			'###BACK###' => $this->pi_getLL('back'),
			'###BACK_URL###' => $this->pi_linkTP_keepPIvars_url(array(), 0, 1),
		);
		$template = $this->cObj->substituteMarkerArray($template, $markers);
		
		return $template;
	}
	
	function renderFormCreate($errors = array()) {
		// Get the application form template
		$html = $this->cObj->fileResource($this->templateFile);
		$template = array();
		$template = $this->cObj->getSubpart($html, '###TEMPLATE_APPLICATION_FORM###');	
		$markerArray = $this->getMarkersForm();
		
		$output_errors = '';
		if (!empty($errors)) {
			$subpart_error = $this->cObj->getSubpart($template, '###TEMPLATE_ERRORS###');	
			foreach($errors as $error) {
				$markers = array('###ERROR_MSG###' => $error);
				$output_errors .= $this->cObj->substituteMarkerArray($subpart_error, $markers);
			}
		}
		$template = $this->cObj->substituteSubpart($template, '###TEMPLATE_ERRORS###', $output_errors);
		
		$subpart_logo = $this->cObj->getSubpart($template, '###TEMPLATE_LOGO###');
		$logo = $this->renderFieldImage($application['logo'], 'logo', $subpart_logo);
		
		$subpart_screenshot = $this->cObj->getSubpart($template, '###TEMPLATE_SCREENSHOT###');
		$screenshot = $this->renderFieldImage($application['screenshot'], 'screenshot', $subpart_screenshot);
		
		$subpartArray = array(
			'###TEMPLATE_LOGO###' => $logo,
			'###TEMPLATE_SCREENSHOT###' => $screenshot
		);

		// Hook pour modifier les conf 
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->applicationFieldsRenderForm($markerArray, $subpartArray, $template, $application, $this->conf, $this);
			}
		}

		// Replace the markers
		$content .= $this->cObj->substituteMarkerArrayCached(
			$template, 
			$markerArray, 
			$subpartArray
		);
		
		return $content;	
	}
	
	/**
	 * Check whether the application exist
	 * @return boolean "true" if exist, otherwise "false"
	 */
	function existAppli() {
		$where = array(
			'application' => $this->piVars['application'],
		);
		$applications = $this->getApplications(tx_icsopendataapi_common::APPMODE_SEARCH, 'application', $where);
		if ($applications)
			return true;
		return false;
	}
	
	
	/**
	 * Register the application
	 */
	function registration() {			
		while( (is_null($this->key_appli)) 
			|| (!$this->is_uniqueKey()) ) {
			$this->key_appli = pwdGenerator(15);
		}
	}
	
	/**
	 * Check if the key is unique
	 * @return boolean "true" otherwise "false"
	 */
	function is_uniqueKey() {	
		$where = array(
			'key_appli' => $this->key_appli
		);
		$applications = $this->getApplications(tx_icsopendataapi_common::APPMODE_SEARCH, 'key_appli', $where);
		if ($applications)
			return true;
		return false;
	}
	
	
	/**
	 * Get the content
	 *
	 * @return $content
	 */
	function renderFormEdit($application, $errors = array()) {
		$html = $this->cObj->fileResource($this->templateFile);
		$template = array();
		$template = $this->cObj->getSubpart($html, '###TEMPLATE_APPLICATION_FORM###');
		
		$output_errors = '';
		if (!empty($errors)) {
			$subpart_error = $this->cObj->getSubpart($template, '###TEMPLATE_ERRORS###');	
			foreach($errors as $error) {
				$markers = array('###ERROR_MSG###' => $error);
				$output_errors .= $this->cObj->substituteMarkerArray($subpart_error, $markers);
			}
		}
		$template = $this->cObj->substituteSubpart($template, '###TEMPLATE_ERRORS###', $output_errors);
		
		$this->piVars = array_merge($application, $this->piVars);
	
		$markerArray = $this->getMarkersForm(true);
				
		$subpart_logo = $this->cObj->getSubpart($template, '###TEMPLATE_LOGO###');
		$logo = $this->renderFieldImage($application['logo'], 'logo', $subpart_logo);
		
		$subpart_screenshot = $this->cObj->getSubpart($template, '###TEMPLATE_SCREENSHOT###');
		$screenshot = $this->renderFieldImage($application['screenshot'], 'screenshot', $subpart_screenshot);
		
		$subpartArray = array(
			'###TEMPLATE_LOGO###' => $logo,
			'###TEMPLATE_SCREENSHOT###' => $screenshot
		);
		
		// Hook pour modifier les conf 
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->applicationFieldsRenderForm($markerArray, $subpartArray, $template, $application, $this->conf, $this);
			}
		}

		$content .= $this->cObj->substituteMarkerArrayCached(
			$template, 
			$markerArray, 
			$subpartArray
		);
		
		return $content;
	}
	
	function renderFieldImage($value, $field, $template) {
		t3lib_div::loadTCA($this->tables['applications']);
		$fieldconf = $GLOBALS['TCA'][$this->tables['applications']]['columns'][$field];
		
		$fieldUpper = strtoupper($field);
		
		$files = t3lib_div::trimExplode(',', (is_array($value)) ? $value['files'] : $value, true);
				
		$markers = array(
			'###'.$fieldUpper.'_LABEL###' => $this->pi_getLL($field),
			'###'.$fieldUpper.'_NAME###' => $this->prefixId.'['.$field.'][files]',
			'###'.$fieldUpper.'_VALUE###' => htmlspecialchars(implode(',', $files)),
			'###'.$fieldUpper.'_ADD_NAME###' => $this->prefixId.'['.$field.'][file]',
			'###'.$fieldUpper.'_MAXSIZE_LABEL###' => htmlspecialchars($this->pi_getLL('max_size')),
			'###'.$fieldUpper.'_MAXSIZE###' => $fieldconf['config']['max_size'],
		);
		$template = $this->cObj->substituteMarkerArray($template, $markers);
	
		if (count($files) >= $fieldconf['config']['maxitems']) {
			$template = $this->cObj->substituteSubpart($template, '###'.$fieldUpper.'_ADD###', $this->pi_getLL('files_number_max'));
		}
		
		$outputFileDelete = '';
		if (count($files)) {
			$subpartFileDelete = $this->cObj->getSubpart($template, '###'.$fieldUpper.'_EXIST###');
			
			foreach ($files as $file) {
				$uniqid = uniqid();
				
				if ($field == 'logo')
					$imgResource = $this->cObj->getImgResource( $fieldconf['config']['uploadfolder'] . '/' . $file, array('width'=>'64m') );
				else
					$imgResource = $this->cObj->getImgResource( $fieldconf['config']['uploadfolder'] . '/' . $file, array('width'=>'150px', 'height'=>'150px') );
				
				$origFile_explode = explode('/', $imgResource['origFile']);
				$imgAlt = htmlspecialchars($this->pi_getLL($fieldname)) . ' ' . $origFile_explode[count($origFile_explode)-1];
				$imgTitle = htmlspecialchars($this->pi_getLL($fieldname)) . ' ' . $origFile_explode[count($origFile_explode)-1];
			
				$markers = array(
					'###IMAGE###' => '<img src="' . $imgResource[3] . '" alt="' . $imgAlt . '" title="' . $imgTitle . '" />',
					'###INDICE###' => $uniqid,
					'###'.$fieldUpper.'_DELETE_NAME###' => $this->prefixId.'['.$field.']['.$uniqid.']',
					'###'.$fieldUpper.'_DELETE_VALUE###' => htmlspecialchars($file),
					'###'.$fieldUpper.'_DELETE_LABEL###' => $this->pi_getLL('filedelete'),
				);
				$outputFileDelete .= $this->cObj->substituteMarkerArray($subpartFileDelete, $markers);
			}
		}
		$template = $this->cObj->substituteSubpart($template, '###'.$fieldUpper.'_EXIST###', $outputFileDelete);

		return $template;
	}
		
	/**
	 */
	function renderField_group_parseFiles($fieldname, $fieldconf, &$files, &$errors, &$succes) {
		if (isset($this->piVars[$fieldname]) && is_array($this->piVars[$fieldname]) && !empty($this->piVars[$fieldname]))
		{
			$pFiles = $this->piVars[$fieldname];
			unset($pFiles['files']);
			$delete = array_intersect($files, array_values($pFiles));
			
			$files = array_diff($files, array_values($pFiles));
			
			foreach ($delete as $file) {
				if ($file) {
					@unlink(t3lib_div::getFileAbsFileName($fieldconf['config']['uploadfolder'] . '/' . $file));
					$succes[] = sprintf($this->pi_getLL('file_delete'), $file);
				}
			}
		}
	
	
		if ($_FILES[$this->prefixId]['tmp_name'][$fieldname]['file']) {
			if (!is_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$fieldname]['file'])) {
				$errors[] = sprintf($this->pi_getLL('file_error_uploaded'), $_FILES[$this->prefixId]['name'][$fieldname]['file']);
			}
			elseif ($_FILES[$this->prefixId]['error'][$fieldname]['file'] != UPLOAD_ERR_OK) {
				$errors[] = sprintf($this->pi_getLL('file_error_uploaded'), $_FILES[$this->prefixId]['name'][$fieldname]['file']);
			}elseif ($_FILES[$this->prefixId]['size'][$fieldname]['file'] > ($fieldconf['config']['max_size'] * 1024))
			{
				$errors[] = sprintf($this->pi_getLL('file_error_size'), $_FILES[$this->prefixId]['name'][$fieldname]['file'], $fieldconf['config']['max_size']);
			}else{
				$newfile = basename(t3lib_div::fixWindowsFilePath($_FILES[$this->prefixId]['name'][$fieldname]['file']));
				
				$filefunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
				$newfile = $filefunc->cleanFileName($newfile);
				$newfile = $filefunc->getUniqueName($newfile, t3lib_div::getFileAbsFileName($fieldconf['config']['uploadfolder']));
				$newfile = basename($newfile);
				
				$ext = '';
				$allowed = t3lib_div::trimExplode(',', $fieldconf['config']['allowed'], true);
				$disallowed = t3lib_div::trimExplode(',', $fieldconf['config']['disallowed'], true);
			
				if (strrpos($newfile, '.') !== false)
					$ext = strtolower(substr($newfile, strrpos($newfile, '.') + 1));
			
				if (in_array($ext, $allowed) || !in_array($ext, $disallowed))
				{
					if (move_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$fieldname]['file'], t3lib_div::getFileAbsFileName($fieldconf['config']['uploadfolder'] . '/' . $newfile))) {
						$files[] = $newfile;
						$succes[] = sprintf($this->pi_getLL('file_uploaded'), $newfile);
					}
				}else{
					$errors[] = sprintf($this->pi_getLL('file_error_extension'), $_FILES[$this->prefixId]['name'][$fieldname]['file']);
				}
			}
		}
	}

	/**
	 * Update application's data
	 */
	function updateDB($update = true, &$errors = array(), $application = null) {
		
		$succes = array();
		
		// fields required
		if (!$this->piVars['application'])
			$errors[] =  $this->pi_getLL('required_name');
		if (!$this->piVars['description'])
			$errors[] =  $this->pi_getLL('required_description');
		
		if (!empty($errors)) {
			return false;
		}
		
		$logo = $this->valueToDB('logo', $this->piVars['logo'], $errors, $succes);
		$screenshot = $this->valueToDB('screenshot', $this->piVars['screenshot'], $errors, $succes);	

		if (!empty($errors)) {
			return false;
		}
		
		if (!$update) {
			// INSERT 
			if ($this->existAppli()) {
				$errors[] = $this->pi_getLL('application_already');
			}else{
				$this->registration();
			}
		}
		
		if (!empty($errors)) {
			return false;
		}
		
		$data = array(
			'application' => $this->piVars['application'],
			'description' => $this->piVars['description'],
			'platform' => $this->piVars['platform'],
			'logo' => $logo,
			'screenshot' => $screenshot,
			'link' => $this->piVars['link'],
			'update_date' => time(),
		);
		$fields = array('application', 'description', 'platform', 'logo', 'screenshot', 'link', 'update_date', 'categories');
		if (!$update) {
			$data['key_appli'] = $this->key_appli;
			$data['max'] = '999999999';
			$fields[] = 'key_appli';
			$fields[] = 'max';
		}
		
		if (isset($this->piVars['publish']) && $this->piVars['publish']) {
			if (!$application['publication_date']) {
				$data['publication_date'] = time();
				$data['publish'] = 1;
				$fields[] = 'publication_date, publish';
			}else{
				$data['publish'] = 1;
				$fields[] = 'publish';
			}
		}else{
			$data['publish'] = 0;
			$fields[] = 'publish';
		}
		
		if ($update) {
			$req = $this->cObj->DBgetUpdate( 
				$this->tables['applications'],
				$this->piVars['keyuid'],
				$data,
				implode(',', $fields),
				true
			);
			// Hook pour enregistrer les autres champs d'une application
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$_procObj->applicationFieldsDBUpdate($this->piVars['keyuid'], $this->conf, $this);
				}
			}
			
			$errors[] = htmlspecialchars($this->pi_getLL('application_updated')) . $this->key_appli;
			
		}else{
			$req = $this->cObj->dbgetinsert(
				$this->tables['applications'],
				$this->conf['pid'],
				$data,
				implode(',', $fields),
				true
			);
			// Hook pour enregistrer les autres champs d'une application
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$_procObj->applicationFieldsDBUpdate($GLOBALS['TYPO3_DB']->sql_insert_id(), $this->conf, $this);
				}
			}
			
			$errors[] = htmlspecialchars($this->pi_getLL('registration_validated')) . ' ' . $this->key_appli;
		}
		
		if (!empty($succes))
			$errors = array_merge($errors, $succes);
		return true;
	}
	
	/**
	 * Transforme une valeur pour base de données
	 *
	 * @param string $fieldname Le nom du champ
	 * @param mixed $value La valeur du champ
	 * @param array $errors
	 * @param array $succes
	 * 
	 * @return string La valeur transformée
	 */
	function valueToDB($fieldname, $value, &$errors, &$succes) {
		global $TCA;
		$table = $this->tables['applications'];
		t3lib_div::loadTCA($table);		
		
		$files = t3lib_div::trimExplode(',', (is_array($value)) ? ($value['files']) : ($value), true);
		$this->renderField_group_parseFiles($fieldname, $TCA[$table]['columns'][$fieldname], $files, $errors, $succes);
		$value = $files;
		$value = implode(',', $value);
		return $value;
	}
	
	/**
	 * Récupère les marqueurs du formulaire
	 *
	 * @param boolean $edit "true" si édition, sinon "false"
	 *
	 * @return mixed Les marqueurs
	 */
	function getMarkersForm($edit = false) {		
		$markerArray = array(
			'###URL###' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),
			'###TITLE_FORM_APPLICATION###' => $edit ? htmlspecialchars($this->pi_getLL('application')) : htmlspecialchars($this->pi_getLL('new_application')),
			'###APPLICATION_LABEL###' => htmlspecialchars($this->pi_getLL('name')),
			'###APPLICATION###' => $this->prefixId.'[application]',
			'###APPLICATION_VALUE###' => $this->piVars['application'] ? $this->piVars['application'] : '',
			'###DESCRIPTION_LABEL###' => htmlspecialchars($this->pi_getLL('description')),
			'###DESCRIPTION###' => $this->prefixId.'[description]',
			'###DESCRIPTION_VALUE###' => $this->piVars['description'] ? $this->piVars['description'] : '',
			'###PLATFORM_LABEL###' => htmlspecialchars($this->pi_getLL('platform')),
			'###PLATFORM###' => $this->prefixId.'[platform]',
			'###PLATFORM_VALUE###' => $this->piVars['platform'] ? $this->piVars['platform'] : '',
			'###LINK_LABEL###' => htmlspecialchars($this->pi_getLL('link')),
			'###LINK###' => $this->prefixId.'[link]',
			'###LINK_VALUE###' => $this->piVars['link'] ? $this->piVars['link'] : '',
			'###PUBLISH_LABEL###' => htmlspecialchars($this->pi_getLL('publish')),
			'###PUBLISH###' => $this->prefixId . '[publish]',
			'###PUBLISH_CHECKED###' => (($this->piVars['publication_date'] > 0 && $this->piVars['publish']) ? 'checked = "checked"': ''),
			'###LAST_PUBLICATION###' => ($this->piVars['publication_date'] > 0? htmlspecialchars($this->pi_getLL('last_publication')) . ' ' . date('d-m-Y', $this->piVars['publication_date']): ''),
			'###UID###' => $this->prefixId.'[uid]',
			'###UID_VALUE###' => $edit ? $this->piVars['keyuid'] : 0,
			'###BTN_REGISTRATION###' => $this->prefixId.'[btn_registration]',
			'###BTN_REGISTRATION_VALUE###' => htmlspecialchars($this->pi_getLL('btn_registration')),
			'###BACK###' => $this->pi_getLL('back'),
			'###BACK_URL###' => $this->pi_linkTP_keepPIvars_url(array(), 0, 1),
		);
		return $markerArray;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata_api/pi2/class.tx_icsopendataapi_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata_api/pi2/class.tx_icsopendataapi_pi2.php']);
}

?>