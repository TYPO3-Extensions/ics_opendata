<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Tsi <tsi@in-cite.net>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(t3lib_extMgm::extPath('ics_opendata_api') . 'lib/class.tx_icsopendataapi_common.php');


/**
 * Plugin 'display applications' for the 'ics_opendata_api' extension.
 *
 * @author	Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendataapi
 */
class tx_icsopendataapi_pi1 extends tx_icsopendataapi_common {
	var $prefixId      = 'tx_icsopendataapi_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_icsopendataapi_pi1.php';	// Path to this script relative to the extension dir.
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
	
		$this->init();
		
		if (!$GLOBALS['TSFE']->fe_user->user['uid']) {
			$content .= $this->renderContentError($this->pi_getLL('nologin'));
		}else{	
			$errors = array();
			if ( isset($this->piVars['publish'])) {
				$succes = $this->publish($this->piVars['publish'], false, $errors);
			}elseif ( isset($this->piVars['unpublish'])) {
				$succes = $this->publish($this->piVars['unpublish'], true, $errors);
			}
			$content .= $this->getContent($errors);
		}
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	/**
	 * Retrieves the template and substitue markers
	 *
	 * @return string $content
	 */
	function getContent($errors = array()) {
		$html = $this->cObj->fileResource($this->templateFile);
		$template = array();
		$template = $this->cObj->getSubpart($html, '###TEMPLATE_APPLICATION_LIST###');
		
		$output_errors = '';
		if (!empty($errors)) {
			$subpart_error = $this->cObj->getSubpart($template, '###TEMPLATE_ERRORS###');	
			foreach ($errors as $error) {
				$markers = array('###ERROR_MSG###' => $error);
				$output_errors .= $this->cObj->substituteMarkerArray($subpart_error, $markers);
			}
		}
		$template = $this->cObj->substituteSubpart($template, '###TEMPLATE_ERRORS###', $output_errors);
		
		$prefixId = $this->prefixId;
		$this->prefixId = 'tx_icsopendataapi_pi2';
		$link_create = $this->pi_linkTP_keepPIvars_url(array(), 0, 1, $this->conf['pidcreate']);
		
		$this->prefixId = $prefixId;
		
		$markerArray = array(
			'###APPLICATION_CAPTION###' => htmlspecialchars($this->pi_getLL('titre')),
			'###TITLE_NOM###' => htmlspecialchars($this->pi_getLL('name')),
			'###TITLE_DESCRIPTION###' => htmlspecialchars($this->pi_getLL('description')),
			'###TITLE_PLATFORM###' => htmlspecialchars($this->pi_getLL('platform')),
			'###TITLE_KEY###' => htmlspecialchars($this->pi_getLL('key')),
			'###TITLE_MODIF###' => htmlspecialchars($this->pi_getLL('edit')),
			'###TITLE_PUBLISH###' => htmlspecialchars($this->pi_getLL('publish')),
			'###TITLE_STAT###' => htmlspecialchars($this->pi_getLL('stat')),
			'###TITLE_LOGO###' => htmlspecialchars($this->pi_getLL('logo')),
			'###TITLE_ACTIONS###' => htmlspecialchars($this->pi_getLL('actions')),
			'###LINK_CREATE###' => $link_create,
			'###CREATE###' => htmlspecialchars($this->pi_getLL('create')),
			'###MESSAGE###' => htmlspecialchars($this->pi_getLL('message_empty')),
		);
				
		$applications = $this->getApplications(tx_icsopendataapi_common::APPMODE_USER);
		if (!$applications) {
			$template = $this->cObj->substituteSubpart($template, '###TEMPLATE_NOT_EMPTY###', '');
			$content .= $this->cObj->substituteMarkerArrayCached(
				$template, 
				$markerArray, 
				array()
			);
			
		}else{
			$subpartArray = array();
			$template = $this->cObj->substituteSubpart($template, '###TEMPLATE_EMPTY###', '');
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$_procObj->applicationFieldsRenderData($markerArray, $subpartArray, $template, $application, $this->conf, $this);
				}
			}
			$subpartArray['###APPLICATION_ITEM###'] = $this->getAppliItemContent($template,$applications);
			
			// Restores markers of template
			$content .= $this->cObj->substituteMarkerArrayCached(
				$template, 
				$markerArray, 
				$subpartArray
			);
		}
		
		return $content;
	}
	
	/**
	 * Retrieves applications and replaces the markers
	 *
	 * @param $template string
	 * @return $content string
	 */
	function getAppliItemContent($template, $applications) {
		global $TCA;
		$table = $this->tables['applications'];
		t3lib_div::loadTCA($table);
		
		$template = $this->cObj->getSubpart($template, '###APPLICATION_ITEM###');
		
		if ($applications) {
			foreach ($applications as $application) {
				$subpart = $template;
				
				$prefixId = $this->prefixId;
				$this->prefixId = 'tx_icsopendataapi_pi2';
				$link_edit = $this->pi_linkTP_keepPIvars_url(array('keyuid' => $application['uid']), 0, 1, $this->conf['pidmodif']);
				
				$this->prefixId = 'tx_icsopendataapi_pi3';
				$link_statistic = $this->pi_linkTP_keepPIvars_url(array('uid' => $application['uid']), 0, 1, $this->conf['pidstat']);
				
				$this->prefixId = 'tx_icsopendataapi_pi4';
				$link_view = $this->pi_linkTP_keepPIvars_url(array('uid' => $application['uid']), 0, 1, $this->conf['pidview']);
				
				$this->prefixId = $prefixId;
				
				// PUBLICATION
				$link_publish = '';
				$label_publish = '';
				
				if (!$application['lock_publication']) {
					$publish = $this->pi_getLL('publication_status_no');
					$link_publish = $this->pi_linkTP_keepPIvars_url(array('publish' => $application['uid']), 0, 1);
					$label_publish = $this->pi_getLL('link_publish');
					if ($application['publication_date'] > 0 && $application['publish']) {
						$publish = date('d-m-Y', $application['publication_date']);
						$link_publish = $this->pi_linkTP_keepPIvars_url(array('unpublish' => $application['uid']), 0, 1);
						$label_publish = $this->pi_getLL('link_unpublish');
					}
				}else{
					$publish = $this->pi_getLL('publication_status_lock');
					
					$subpart = $this->cObj->substituteSubpart($subpart, '###APPLICATION_PUBLISH###', '');
				}
				
				$markers = array(
					'###NOM###' => $application['application'],
					'###LOGO###' => $this->renderLogo('logo', $TCA[$table]['columns']['logo'], $application['logo'] ),
					'###DESCRIPTION###' => $application['description'],
					'###PLATFORM###' => $application['platform'],
					'###KEY###' => $application['key_appli'],
					'###LINK_MODIF###' => $link_edit,
					'###LABEL_MODIF###' => htmlspecialchars($this->pi_getLL('edit')),
					'###LINK_PUBLISH###' => $link_publish,
					'###LABEL_PUBLISH###' => htmlspecialchars($label_publish),
					'###LINK_VIEW###' => $link_view,
					'###LABEL_VIEW###' => htmlspecialchars($this->pi_getLL('view')),
					'###LINK_STAT###' => $link_statistic,
					'###LABEL_STAT###' => htmlspecialchars($this->pi_getLL('stat')),
					'###PUBLISH###' => htmlspecialchars($publish),
				);
				$subpartArray = array();
				// Hook for extra markers
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'])) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderControls'] as $_classRef) {
						$_procObj = & t3lib_div::getUserObj($_classRef);
						$_procObj->applicationFieldsRenderData($markers, $subpartArray, $subpart, $application, $this->conf, $this);
					}
				}
				$content .= $this->cObj->substituteMarkerArrayCached($subpart, $markers, $subpartArray);
				
			}
		}
		return $content;
	}
	
	/**
	 * Publish application
	 * @param $uid int
	 */
	function publish($uid, $unpublish = false, &$errors = array()) {
		$applications = $this->getApplications(tx_icsopendataapi_common::APPMODE_SINGLEUSER, null, $uid);
		
		if (!$applications) {
			$errors[] = $this->pi_getLL('error_publish');
			return false;
		}
		$application = $applications[0];
		if (!$unpublish) {
			$fields = 'publish';
			if ($application['publication_date'] == 0) {
				$fields = 'publication_date, publish';
			}
			
			$update = array(
				'publication_date' => time(),
				'publish' => 1,
			);
			$req = $this->cObj->DBgetUpdate( 
				$this->tables['applications'],
				$uid,
				$update,
				$fields,
				true
			);	
			
			$errors[] = sprintf($this->pi_getLL('success_publish'), $application['application']);
		}else{
			// D�publier
			$update = array(
				'publish' => 0,
			);
			$req = $this->cObj->DBgetUpdate( 
				$this->tables['applications'],
				$uid,
				$update,
				'publish',
				true
			);	
			
			$errors[] = sprintf($this->pi_getLL('success_unpublish'), $application['application']);
		}
		return true;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata_api/pi1/class.tx_icsopendataapi_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata_api/pi1/class.tx_icsopendataapi_pi1.php']);
}

?>