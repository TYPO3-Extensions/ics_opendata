<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cité Solution <technique@in-cite.net>
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
 *   63: class tx_icsodappstore_pi4 extends tx_icsodappstore_common
 *
 *   77:     function main($content, $conf)
 *   92:     function showscreenshot(element)
 *  108:     function setCurrent(element)
 *  154:     function init()
 *  177:     function confValidator()
 *  208:     function getDetailContent()
 *  225:     function detailView($content,$row)
 *  264:     function getListContent()
 *  290:     function listView($content,$rows=array())
 *  420:     function renderScreenshot($fieldname, $fieldconf, $value)
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
if(t3lib_extMgm::isLoaded('ratings'))
	require_once(t3lib_extMgm::extPath('ratings') . 'class.tx_ratings_api.php');


/**
 * Plugin 'Applications store' for the 'ics_od_appstore' extension.
 *
 * @author	Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsodappstore
 */
class tx_icsodappstore_pi4 extends tx_icsodappstore_common {
	var $prefixId      = 'tx_icsodappstore_pi4';		// Same as class name
	var $scriptRelPath = 'pi4/class.tx_icsodappstore_pi4.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_od_appstore';	// The extension key.
	var $debugger	   = false; /**< Activate debugger mode */
	var $where_restriction = ''; /**< Default applications restriction */

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->where_restriction = ' AND `'.$this->tables['applications'].'`.`release_date` >0 AND `'.$this->tables['applications'].'`.`release_date` <'.time().' AND `'.$this->tables['applications'].'`.`lock_publication` = 0 AND `'.$this->tables['applications'].'`.`publish` = 1';
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();
		$this->init();
		
		if( $this->confValidator() ){
			if($this->debugger){
				var_dump($this->conf);
				var_dump($this->piVars);
			}
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] .= '
			<script language="javascript" type="text/javascript">
				function showscreenshot(element){
					for(i=0; i<3; i++){
						elt = "' . $this->prefixId . '_screenshot" +i;
						if(document.getElementById(elt) != null){
							document.getElementById(elt).style.display = "none";
						}
					}
					document.getElementById(element).style.display = "block";
				}

				function setCurrent(element){
					for(i=0; i<3; i++){
						elt = "' . $this->prefixId . '_min" +i;
						if(document.getElementById(elt) != null){
							document.getElementById(elt).className = "";
						}
					}
					document.getElementById(element).className = "current";
				}
			</script>
			';
			if(isset($this->conf['style'])) {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] .= '<link rel="stylesheet" type="text/css" href="' . $this->conf['style'] .'" />';
			}
			if( isset($this->piVars['uid']) ){
				$row = $this->getDetailContent();
				if($row)	{
					$content = $this->detailView($content,$row);
					// Get ratings content
					if(t3lib_extMgm::isLoaded('ratings') && $this->conf['ratings'])	{
						$ratingsAPI = t3lib_div::makeInstance('tx_ratings_api');
						$content .= $ratingsAPI->getRatingDisplay($this->prefixId.'_' . $this->piVars['uid']);
					}
				}	else	{
					$content .= '<p>' . htmlspecialchars($this->pi_getLL('application_not_exist')) . '</p>';
				}
			}
			else{
				$rows = $this->getListContent();
				if($rows)
					$content = $this->listView($content,$rows);
				else
					$content .= '<p>' . htmlspecialchars($this->pi_getLL('application_not_exist')) . '</p>';
				
				$content = $this->renderSearch() . $content;
			}
		}
		else
			$content .= '<p>' . htmlspecialchars($this->pi_getLL('bad_ts')) . '</p>';

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Configuration init
	 *
	 * @return	void
	 */
	function init(){
		parent::init();
		if (!$this->conf['singlePid'])
			$this->conf['singlePid'] = $GLOBALS['TSFE']->id;
			
		if(!isset($this->piVars['page']) || !is_numeric($this->piVars["page"])){
			$this->piVars['page'] = 0;
		}
		if(!isset($this->piVars['uid']) || !is_numeric($this->piVars["uid"])){
			$this->piVars['uid'] = null;
		}
		
		// Gets sorting
		$sortAvailable = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sortAvailable', 'sortAndFilter');
		$this->conf['list.']['orderAvailable'] = $sortAvailable? $sortAvailable: $this->conf['list.']['orderAvailable'];
		$sortDefault = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sortDefault', 'sortAndFilter');
		$this->conf['list.']['orderDefault'] = $sortDefault? $sortDefault: $this->conf['list.']['orderDefault'];
		
		$orderAvailable = explode(',', $this->conf['list.']['orderAvailable']);
		if (!in_array($this->piVars['sort'], $orderAvailable) ) {
			if (in_array($this->conf['list.']['orderDefault'], $orderAvailable) ) {
				$this->piVars['sort'] = $this->conf['list.']['orderDefault'];
			}
		}
		$this->piVars['maxRows'] = $this->piVars['page'] * $rows_by_page;
		
		// Gets favorite applications list
		$fav_applis = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'applications', 'main');
		$this->conf['fav_applis'] = $fav_applis? $fav_applis: $this->conf['fav_applis'];
		
		$this->initCriteria();
	}

	/**
	 * Initalizes criteria
	 *
	 * @return	void
	 */
	function initCriteria() {
		$criteria = $this->piVars;
		unset($criteria['sort']);
		unset($criteria['page']);
		unset($criteria['deleted']);
		
		$this->criteria = $criteria;

		if (is_array($this->piVars['deleted']['platforms']) && !empty($this->piVars['deleted']['platforms'])) {
			$this->criteria['platforms'] = array_diff($this->criteria['platforms'], $this->piVars['deleted']['platforms']);
		}

		// Hook for additionnal init
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['addInitCriteria'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['addInitCriteria'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->addInitCriteria($this->piVars, $this->criteria, $this->conf, $this);
			}
		}

	}
	
	/**
	 * Configuration validation
	 *
	 * @return	boolean
	 */
	function confValidator(){
		$ret = true;
		//Validator setting
		if( !isset($this->conf['list.']['colNum']) ||
			!isset($this->conf['list.']['rowsByCol']) ||
			!isset($this->conf['list.']['orderDefault']) ||
			!isset($this->conf['list.']['orderAvailable']) ||
			!isset($this->conf['list.']['descSize']) ){
			$ret = false;
		}
		else{
			$orderAvailable = explode(',', $this->conf['list.']['orderAvailable']);

			//Validator TCA field
			if( !in_array($this->conf['list.']['orderDefault'],$orderAvailable) ||
				(!$this->existInApplicationTCA($this->conf['list.']['orderDefault']) && $this->conf['list.']['orderDefault']!='favorite')){
				$ret = false;
			}
			else
				foreach($orderAvailable as $k=>$v){
					if(!$this->existInApplicationTCA($v) && $this->conf['list.']['orderDefault']!='favorite') 
						$ret = false;
				}
		}
		return $ret;
	}

	/**
	 * Retrieves content
	 *
	 * @return	string		content details of application
	 */
	function getDetailContent(){
		global $TCA;

		$applications = $this->getApplications(tx_icsodappstore_common::APPMODE_SINGLE, null, $this->piVars['uid']);

		if($applications)
			return $applications[0];
		return false;
	}

	/**
	 * Render application details
	 *
	 * @param	string		$content: content
	 * @param	array		$row: application data
	 * @return	string		content
	 */
	function detailView($content,$row){
		global $TCA;
		$table = $this->tables['applications'];
		t3lib_div::loadTCA($table);
		$html = $this->cObj->fileResource($this->templateFile);
		$template = array();
		$template = $this->cObj->getSubpart($html, '###DETAIL###');
		$resAppPlatforms = $this->getAppPlatforms($row);
		$appPlatforms = array();
		while ($platform = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resAppPlatforms) ) {
			$appPlatforms[] = $platform['title'];
		}
		$pObj = t3lib_div::makeInstance("tslib_cObj");
		$backID = $this->piVars['returnID'] ? $this->piVars['returnID'] : $GLOBALS['TSFE']->id;
		
		if(strpos($row['link'], 'http') === false) $row['link'] = 'http://' . $row['link'];
		$vLink = $row['link'] ? '<a href="' . $row['link'] . '">' . htmlspecialchars($this->pi_getLL('download_api')) .' '.tslib_cObj::crop( $row['link'], 29) . '</a>' : '';
		$vDescription = $row['description'] ? $this->pi_RTEcssText($row['description']) : '';
		$vPublishDate = $row['release_date'] ? strftime("%d/%m/%Y",$row['release_date']) : '';
		$vPublisher = '';
		$aPublisher = array();
		if($row['name']) {
			if($row['first_name']) $aPublisher[] = htmlspecialchars($row['first_name']);
			$aPublisher[] = htmlspecialchars($row['name']);
		} else {
			if($row['first_name']) $aPublisher[] = htmlspecialchars($row['first_name']);
			if($row['last_name']) $aPublisher[] = htmlspecialchars($row['last_name']);			
		}
		if(count($aPublisher)) $vPublisher = implode(' ', $aPublisher);
		$vPlatforms = implode(', ', $appPlatforms);
		
		$subpartArray = array();
		$markerArray = array(
			'###APPLICATION###' => $row['title'],
			'###LOGO###' => $this->renderLogo('logo', $TCA[$table]['columns']['logo'], $row['logo'] ),
			'###DOWNLOAD###' => $row['countcall'],
			'###LINK###' => $vLink,
			'###DESCRIPTION###' => $vDescription,
			'###PUBLISH_DATE###' => $vPublishDate,
			'###PUBLISHER###' => $vPublisher,
			'###SCREENSHOT###' => $this->renderScreenshot('screenshot', $TCA[$table]['columns']['screenshot'], $row['screenshot'] ),
			'###PLATFORMS###' => $vPlatforms,
			'###BACK###' => $GLOBALS['TSFE']->cObj->getTypoLink_URL($backID, array($this->prefixId.'[page]' => $this->piVars['page'], $this->prefixId.'[sort]'=> $this->piVars['sort']) )
		);
		
		
		$subpartArray['###DESCRIPTION_SUBPART###'] = '';
		if ($row['description']) {
			$description_subpart = $this->cObj->getSubpart($template, '###DESCRIPTION_SUBPART###');
			$subpartArray['###DESCRIPTION_SUBPART###'] = $this->cObj->substituteMarkerArray(
				$description_subpart, 
				array('###DESCRIPTION###' => $vDescription)
			);
		}
		
		$subpartArray['###LINK_SUBPART###'] = '';
		if ($row['link']) {
			$link_subpart = $this->cObj->getSubpart($template, '###LINK_SUBPART###');
			$subpartArray['###LINK_SUBPART###'] = $this->cObj->substituteMarkerArray(
				$link_subpart, 
				array('###LINK###' => $vLink)
			);
		}
		
		$subpartArray['###PLATFORMS_SUBPART###'] = '';
		if (!empty($appPlatforms)) {
			$platforms_subpart = $this->cObj->getSubpart($template, '###PLATFORMS_SUBPART###');
			$subpartArray['###PLATFORMS_SUBPART###'] = $this->cObj->substituteMarkerArray(
				$platforms_subpart, 
				array('###PLATFORMS###' => $vPlatforms)
			);
		}		
		
		// Hook for application extra fields
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderData'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderData'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->applicationFieldsRenderData($markerArray, $subpartArray, $template, $row, $this->conf, $this);
			}
		}
		$content .= $this->cObj->substituteMarkerArrayCached(
			$template,
			$markerArray,
			$subpartArray
		);
		return $content;
	}
	
	/**
	 * Retrieves content
	 *
	 * @return	string		content details of application
	 */
	function getListContent(){
		global $TCA;

		$parameter = $this->criteria;
		$number_applications = $this->getApplications(tx_icsodappstore_common::APPMODE_ALL, 'count', $parameter);

		if($number_applications && isset($number_applications[0]['count']) && $number_applications[0]['count']>0){
			$this->piVars['maxRows'] = $number_applications[0]['count'];
			if ($this->piVars['sort'])
				$parameter['sort'] = $this->piVars['sort'];
			if ($this->piVars['page'])
				$parameter['page'] = $this->piVars['page'];
			$applications = $this->getApplications(tx_icsodappstore_common::APPMODE_ALL, null, $parameter);
		}
		else
			$applications = false;

		return $applications;
	}

	/**
	 * Render applications list
	 *
	 * @param	string		$content: content
	 * @param	array		$row: applications data
	 * @return	string		content
	 */
	function listView($content,$rows=array()){
		global $TCA;
		$table = $this->tables['applications'];
		t3lib_div::loadTCA($table);

		$html = $this->cObj->fileResource($this->templateFile);

		if(!$html || $html == '')
			return '<!-- <p style="color:red;">Template not found!</p> -->';
		else{
			//GET subparts
			$template = array();
			$template['TEMPLATE'] = $this->cObj->getSubpart($html, '###TEMPLATE_CATALOG_APPLICATION###');
			$template['TEMPLATE'] = $this->cObj->getSubpart($template['TEMPLATE'], '###LIST###');

			$template['COLS'] = $this->cObj->getSubpart($template['TEMPLATE'], '###COLS###');

			$template['ROWS'] = $this->cObj->getSubpart($template['COLS'], '###ROWS###');

			//<!-- Cols build start
			$markers_array = array();
			$content_cols = '';
			$content_rows = '';
			$count_cols = 0;
			$count_rows = 0;
			foreach($rows as $k => $row){
				$count_rows++;
				$resAppPlatforms = $this->getAppPlatforms($row);
				$appPlatforms = array();
				while ($platform = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resAppPlatforms) ) {
					$appPlatforms[] = $platform['title'];
				}
				
				$vPublisher = '';
				$aPublisher = array();
				if($row['name']) {
					if($row['first_name']) $aPublisher[] = htmlspecialchars($row['first_name']);
					$aPublisher[] = htmlspecialchars($row['name']);
				} else {
					if($row['first_name']) $aPublisher[] = htmlspecialchars($row['first_name']);
					if($row['last_name']) $aPublisher[] = htmlspecialchars($row['last_name']);			
				}
				if(count($aPublisher)) $vPublisher = implode(' ', $aPublisher);
				$markerArray = array(
					'###NUM_ROW###' => $count_rows,
					'###LOGO###' => $this->renderLogo('logo', $TCA[$table]['columns']['logo'], $row['logo'] ),
					'###APPLICATION###' => htmlspecialchars($row['title']),
					'###PUBLISHER###' => $vPublisher,
					'###DOWNLOAD###' => $row['countcall'],
					'###PUBLISH_DATE###' => strftime("%d/%m/%Y",$row['release_date']),
					'###DESCRIPTION###' => tslib_cObj::crop( $row['description'],$this->conf['list.']['descSize']),
					'###LINK###' => $GLOBALS['TSFE']->cObj->getTypoLink_URL($this->conf['singlePid'], array($this->prefixId.'[page]' => $this->piVars['page'], $this->prefixId.'[sort]'=> $this->piVars['sort'],$this->prefixId.'[uid]' => $row['uid'], $this->prefixId.'[returnID]'=> $GLOBALS['TSFE']->id) ),
					'###PLATFORMS###' => implode(',', $appPlatforms),
				);

				$subpartArray = array();
				// Hook
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderData'])) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['applicationFieldsRenderData'] as $_classRef) {
						$_procObj = & t3lib_div::getUserObj($_classRef);
						$_procObj->applicationFieldsRenderData($markerArray, $subpartArray, $template['ROWS'], $row, $this->conf, $this);
					}
				}

				// Row build
				$content_rows .= $this->cObj->substituteMarkerArrayCached($template['ROWS'],$markerArray, $subpartArray);
				//Build a col all n rows
				if($count_rows%$this->conf['list.']['rowsByCol'] == 0 && $count_rows > 0){
					$count_cols++;
					$content_cols .= $this->cObj->substituteMarkerArrayCached($template['COLS'], array('###NUM_COL###' => $count_cols), array('###ROWS###'=>$content_rows));
					$content_rows = '';
				}
			}
			// Build the last col if need
			if($count_rows%$this->conf['list.']['rowsByCol'] != 0 && $count_rows > 0){
				$count_cols++;
				$content_cols .= $this->cObj->substituteMarkerArrayCached($template['COLS'], array('###NUM_COL###' => $count_cols), array('###ROWS###'=>$content_rows));
			}
			//Cols build end -->

			//<!-- Header build start

			$template['FIRST_PAGE'] = '';
			$template['LAST_PAGE'] = '';
			if($this->piVars['page'] > 0)
				$template['FIRST_PAGE'] = $this->cObj->getSubpart($template['TEMPLATE'], '###HIDE_FIRST_PAGE###');


			$rows_by_page = $this->conf['list.']['colNum'] * $this->conf['list.']['rowsByCol'];
			$pages = $this->piVars['maxRows']/$rows_by_page;
			$pages = (int)$pages;
			if($this->piVars['page'] * $rows_by_page + $rows_by_page < $this->piVars['maxRows'])
				$template['LAST_PAGE'] = $this->cObj->getSubpart($template['TEMPLATE'], '###HIDE_LAST_PAGE###');

			$pagebrowser ='';
			if ($this->conf['list.']['usePagebrowse']) {
				$template['FIRST_PAGE'] = '';
				$template['LAST_PAGE'] = '';
				$pagebrowser = $this->getListGetPageBrowser(intval(ceil($this->piVars['maxRows']/$rows_by_page)));
			} else {
				$markerArray = array(
					'###FIRST_PAGE_LINK###' => $GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, array($this->prefixId.'[page]' => 0, $this->prefixId.'[sort]'=> $this->piVars['sort']) ),
					'###LAST_PAGE_LINK###' => $GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, array($this->prefixId.'[page]' => ($pages), $this->prefixId.'[sort]'=> $this->piVars['sort']) ),
				);

				$template['FIRST_PAGE'] = $this->cObj->substituteMarkerArrayCached($template['FIRST_PAGE'],$markerArray);

				$template['LAST_PAGE'] = $this->cObj->substituteMarkerArrayCached($template['LAST_PAGE'],$markerArray);

				if($pages > 1){
					for($i=0; $i<$pages+1; $i++){
						if($this->piVars['page']==$i)
							$pagebrowser .= '<span>'.($i + 1).'</span>';
						else
							$pagebrowser .= $GLOBALS['TSFE']->cObj->getTypoLink(($i + 1)."",$GLOBALS['TSFE']->id, array($this->prefixId.'[page]'=> $i, $this->prefixId.'[sort]'=> $this->piVars['sort'] ));
					}
				}
			}
			
			$orderAvailable = explode(',', $this->conf['list.']['orderAvailable']);
			$sort_content = '';
			$separator ='';
			foreach($orderAvailable as $k=>$v){
				// $label = str_replace('LLL:EXT:ics_od_appstore/locallang_db.xml:','',$TCA[$table]['columns'][$v]['label']);
				$label = 'sort_' . $v;
				// if($k == $this->piVars['sort']) {
				if($v == $this->piVars['sort']) {
					$sort_content .= $separator . '<span class="current">'.htmlspecialchars($this->pi_getLL($label)) . '</span>';
					}
				else {
					// $sort_content .= $separator . '<span><a href="'.$GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id,array($this->prefixId.'[page]'=> $this->piVars['page'],$this->prefixId.'[sort]'=> $k)) . '">' . htmlspecialchars($this->pi_getLL($label)) . '</a></span>';
					$sort_content .= $separator . '<span><a href="'.$GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id,array($this->prefixId.'[page]'=> $this->piVars['page'],$this->prefixId.'[sort]'=> $v)) . '">' . htmlspecialchars($this->pi_getLL($label)) . '</a></span>';
				}
				$separator = '<span class="separator">/</span>';
			}
			$markerArray = array(
				'###PREFIXID###' => $this->prefixId,
				'###SEARCHW###' => $this->piVars['searchW'],
				'###SORT###' => $sort_content,
				'###TOTAL_APPLIS###' => count($rows),
				'###PAGES###' => $pagebrowser,
			);
			$subpartArray = array(
				'###COLS###' => $content_cols,
				'###HIDE_FIRST_PAGE###' => $template['FIRST_PAGE'],
				'###HIDE_LAST_PAGE###' => $template['LAST_PAGE'],
				'###FILTER_PLATFORMS###' => $this->getFilter_platforms($template['TEMPLATE']),
				'###FORM_ACTION###' => $this->pi_getPageLink($this->conf['resultsSearchPid']),
				'###FORM_NAME###' => $this->prefixId.'_form',
				'###SEARCHBUTTON_NAME###' => $this->prefixId.'[submit]',
				'###SEARCHBUTTON_VALUE###' => $this->pi_getLL('search_submit', 'Submit', true),
				'###SEARCHBUTTON_ID###' => $this->prefixId.'_submitSearch',
			);
			//Header build end -->
			
			// Hook for add fields markers to list view
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListFieldsMarkers'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListFieldsMarkers'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$_procObj->additionalListFieldsMarkers($markerArray, $subpartArray, $template, $this);
				}
			}
		
			//Final link
			$content .= $this->cObj->substituteMarkerArrayCached($template['TEMPLATE'], $markerArray, $subpartArray);
			return $content;
		}
	}

	/**
	 * Render screenshot content
	 *
	 * @param	string		$fieldname
	 * @param	array		$fieldconf
	 * @param	string		or array $value
	 * @return	string		content
	 */
	function renderScreenshot($fieldname, $fieldconf, $value){
		global $TSFE;
		$files = t3lib_div::trimExplode(',', (is_array($value)) ? $value['files'] : $value, true);

		$content = '';
		if(isset($files) && is_array($files) && count($files)>0){
			foreach($files as $key=>$file){
				if($key==0){
					$content .= '
						<img src="' . $fieldconf['config']['uploadfolder'] . '/' . $file . '" alt="' . $this->pi_getLL('screenshot'). ' ' . $key . '" title="logo" id="' . $this->prefixId . '_screenshot' . $key . '"/>
						';
				}else{
					$content .= '
						<img src="' . $fieldconf['config']['uploadfolder'] . '/' . $file . '" alt="logo" title="' . $this->pi_getLL('screenshot'). ' ' . $key . '" style="display: none;" id="' . $this->prefixId . '_screenshot' . $key . '"/>
					';
				}

				$imgResource = $this->cObj->getImgResource( $fieldconf['config']['uploadfolder'] . '/' . $file, array('width'=>'150m'));
				if($key==0){
					$img .= '
					<div class="soft-img-key-min soft-img-key-min'.$key.'">
						<a href="javascript: onclick = showscreenshot(\'' . $this->prefixId . '_screenshot' . $key . '\');setCurrent(\'' . $this->prefixId . '_min' . $key . '\'); ">
							<img src="' . $imgResource[3] . '"  alt="'.$this->pi_getLL('screenshot').' ' . strval(intval($key)+1) .'" />
							<div id="' . $this->prefixId . '_min' . $key . '" class="current"></div>
						</a>
					</div>' ;
				}
				else{
					$img .= '
					<div class="soft-img-key-min soft-img-key-min'.$key.'">
						<a href="javascript: onclick = showscreenshot(\'' . $this->prefixId . '_screenshot' . $key . '\');setCurrent(\'' . $this->prefixId . '_min' . $key . '\'); ">
							<img src="' . $imgResource[3] . '"  alt="'.$this->pi_getLL('screenshot').' ' . strval(intval($key)+1) .'" />
							<div id="' . $this->prefixId . '_min' . $key . '"></div>
						</a>
					</div>';
				}
			}
			$content = '
				<div class="screenshot">
					<div class="soft-img-normal">' . $content . '</div>
					<div class="soft-img-key-mins">' . $img . '</div>
				</div>
			';
		}
		return $content;
	}

	/**
	 * Render the search view
	 *
	 * @return	string		$content The search view content
	 */
	function renderSearch() {
		$html = $this->cObj->fileResource($this->templateFile);

		if(!$html || $html == '')
			return '<!-- <p style="color:red;">Template not found!</p> -->';
		else{
			$template = $this->cObj->getSubpart($html, '###TEMPLATE_SEARCH###');
			$markerArray = array(
				'###PREFIXID###' => $this->prefixId,
				'###SEARCHW_LABEL###' => htmlspecialchars($this->pi_getLL('search_searchW', 'Keywords', true)),
				'###SEARCHW_VALUE###' => htmlspecialchars($this->piVars['searchW']),
				'###SELECTED_CRITERIA###' => $this->renderSelectedCriteria(),
			);
			$subpartArray = array(
				'###FILTER_PLATFORMS###' => $this->getFilter_platforms($template),
				'###FORM_ACTION###' => $this->pi_getPageLink($this->conf['resultsSearchPid']),
				'###FORM_NAME###' => $this->prefixId.'_form',
				'###SEARCHBUTTON_NAME###' => $this->prefixId.'[submit]',
				'###SEARCHBUTTON_VALUE###' => $this->pi_getLL('search_submit', 'Submit', true),
				'###SEARCHBUTTON_ID###' => $this->prefixId.'_submitSearch',
			);
			
			// Hook for add fields markers to search view
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalFieldsSearchMarkers'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalFieldsSearchMarkers'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$_procObj->additionalFieldsSearchMarkers($markerArray, $subpartArray, $template, $this);
				}
			}
			$content .= $this->cObj->substituteMarkerArrayCached($template, $markerArray, $subpartArray);
			return $content;
		}
		
	}
	
	/**
	 * Render seleceted Criteria
	 *
	 * @return	string		Selected criteria HTML content
	 */
	function renderSelectedCriteria() {
		$html = $this->cObj->fileResource($this->templateFile);
		$template = $this->cObj->getSubpart($html, '###TEMPLATE_SELECTED_CRITERIA###');

		$subpartArray = array();
		$subpartArray['###SUBPART_SC_SEARCHW###'] = $this->renderSC_searchW($this->cObj->getSubpart($template, '###SUBPART_SC_SEARCHW###'));
		$subpartArray['###SUBPART_SC_PLATFORMS###'] = $this->renderSC_platforms($this->cObj->getSubpart($template, '###SUBPART_SC_PLATFORMS###'));
		$markers = array(
			'###TITLE###' => $this->pi_getLL('selectedCriteria', 'Selected Criteria', true),
		);
		
		// Hook for add fields markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSelectedCriteriaMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSelectedCriteriaMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalSelectedCriteriaMarkers($markers, $subpartArray, $template, $this->conf, $this);
			}
		}
		$template = $this->cObj->substituteSubpartArray($template, $subpartArray);
		return $this->cObj->substituteMarkerArray($template, $markers);
	}
	
	/**
	 * Renders selected criteria keywords
	 *
	 * @param	string	$template
	 * @return	string	The HTML content
	 */
	function renderSC_searchW($template) {
		if (!$this->criteria['searchW'])
			return '';

		$markers = array(
			'###SC_SEARCHW_NAME###' => $this->prefixId.'[deleted][searchW]',
			'###SC_SEARCHW_LABEL###' => $this->pi_getLL('sc_searchW', 'Keywords', true),
			'###SC_SEARCHW_VALUE###' => $this->criteria['searchW'],
		);
		return $this->cObj->substituteMarkerArray($template, $markers);		
	}
	
	/**
	 * Renders selected criteria platforms
	 *
	 * @param	string	$template
	 * @return	string	The HTML content
	 */
	function renderSC_platforms($template) {
		$content = '';
		if (is_array($this->criteria['platforms']) && !empty($this->criteria['platforms'])) {
			foreach ($this->criteria['platforms'] as $platform) {
				if ($elem = intval($platform)) {
					$platforms[] = $platform;
				}
			}
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'uid, title',
				'tx_icsodappstore_platforms',
				'1'.$this->cObj->enableFields('tx_icsodappstore_platforms').' AND uid IN('.implode(',', $platforms).')'
			);
		}
		if (is_array($rows) && !empty($rows)) {
			$markers = array();
			$itemTemplate = $this->cObj->getSubpart($template, '###SC_PLATFORM_ITEM###');
			$itemContent = '';
			foreach ($rows as $row) {
				$itemMarkers = array(
					'###PLATFORM_LABEL###' => $row['title'],
					'###PLATFORM_VALUE###' => $row['uid'],
					'###PLATFORM_NAME###' => $this->prefixId.'[deleted][platforms][]',
				);
				$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers);
			}
			$template = $this->cObj->substituteSubpart($template, '###SC_PLATFORM_ITEM###', $itemContent);
			$markers = array('###TITLE_PLATFORMS###' => $this->pi_getLL('sc_platforms', 'Platforms', true));
			$content = $this->cObj->substituteMarkerArray($template, $markers);
		}
		return $content;
	}
	
	/** 
	 * Renders platforms filters
	 *
	 * @param	string	$template: The HTML template for filter on platform
	 * @return	string	HTML content for filter on platform
	 */
	public function getFilter_platforms($template) {
		$template = $this->cObj->getSubpart($template, '###FILTER_PLATFORMS###');
		$subparts = array();
		
		$subparts['###PLATFORMS_ITEM###'] = '';
		$itemTemplate = $this->cObj->getSubpart($template, '###PLATFORMS_ITEM###');
		$platforms = $this->getAllPlatforms(array('orderBy' => 'title'));
		$selected_platforms = $this->criteria['platforms'];
		if (is_array($platforms) && !empty($platforms)) {
			$itemContent = '';
			foreach ($platforms as $platform) {
				$locMarkers = array(
					'###PLATFORM_VALUE###' => $platform['uid'],
					'###PLATFORM_NAME###' => $this->prefixId.'[platforms][]',
					'###PLATFORM_ID###' => $this->prefixId.'_platform'.$platform['uid'],
					'###PLATFORM_LABEL###' => $platform['title'],
					'###CHECKED###' => (in_array($platform['uid'], $selected_platforms )? 'checked="checked"': ''),
				);
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers);
				$subparts['###PLATFORMS_ITEM###'] .= $itemContent;
			}
		}
		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###PLATFORMS_LABEL###' => $this->pi_getLL('filter_platforms', 'Platforms', true),
		);
	
		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		$content = $this->cObj->substituteMarkerArray($template, $markers);
		
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_appstore/pi4/class.tx_icsodappstore_pi4.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_appstore/pi4/class.tx_icsodappstore_pi4.php']);
}

?>