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
 *   79: class tx_icsoddatastore_pi1 extends tslib_pibase
 *
 *              SECTION: < Default search criteria 
 *  105:     function main($content, $conf)
 *  155:     function controlVars(&$content)
 *  169:     function init()
 *  287:     function renderSearch()
 *  341:     function renderSelectedCriteria()
 *  413:     function renderSorting()
 *  449:     function renderFileformatItems($template, $aFileformats)
 *  481:     function renderAgenciesItems($template, $aTiers)
 *  508:     function renderLicenceItems($template, array $aLicences)
 *  531:     function renderList()
 *  576:     function renderListHeader($template)
 *  607:     function renderListRows($template)
 *  778:     function renderListRow($template, $row)
 *  857:     function renderFiles($view, $filegroup, $template)
 *  956:     function renderSingle($id)
 * 1085:     function getImgResource($resource, $desc, $width = 62, $height = 20, $external = false)
 * 1107:     function getFiles_mm($filegroup)
 * 1125:     function getFileSize($file)
 * 1140:     function getFileformats($searchable = false)
 * 1163:     function getFiletypes()
 * 1180:     function getTiersAgencies()
 * 1196:     function getLicences()
 * 1210:     protected function getListGetPageBrowser($numberOfPages)
 * 1232:     function renderRSS($rssLink, $imgSrc)
 * 1249:     function getExtraQueryString()
 *
 * TOTAL FUNCTIONS: 25
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

if (t3lib_extMgm::isLoaded('ratings'))
	require_once(t3lib_extMgm::extPath('ratings') . 'class.tx_ratings_api.php');


/**
 * Plugin 'Opendata files store' for the 'ics_od_datastore' extension.
 *
 * @author	YANG Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */
class tx_icsoddatastore_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_icsoddatastore_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_icsoddatastore_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_od_datastore';	// The extension key.
	var $tables        = array(
		'filegroups' => 'tx_icsoddatastore_filegroups',
		'fileformats' => 'tx_icsoddatastore_fileformats',
		'filetypes' => 'tx_icsoddatastore_filetypes',
		'file_filegroup_mm' => 'tx_icsoddatastore_files_filegroup_mm',
		'files' => 'tx_icsoddatastore_files',
		'licences' => 'tx_icsoddatastore_licences',
		'tiers' => 'tx_icsoddatastore_tiers',
	); /**< Database tables */

	protected $listFields = array('title', 'description', 'publisher', 'files', 'tstamp'); /**< Default view list fields */
	protected $detailFields = array('uid', 'title', 'publisher', 'agency', 'time_period', 'update_date', 'update_frequency', 'description', 'technical_data', 'contact', 'files', 'licence', 'release_date', 'creator', 'manager', 'owner'); /**< Default view details fields */
	protected $list_criteria = array(); /**< Default search criteria */


	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

		$this->init();
		if ($this->controlVars($content)) {
			$codes = t3lib_div::trimExplode(',', $this->config['code'], 1);
			while (list(, $theCode) = each($codes)) {
				$theCode = (string)strtoupper(trim($theCode));
				switch ($theCode) {
					case 'SINGLE':
						if (isset($this->piVars['uid'])) {
							$content .= $this->renderSingle($this->piVars['uid']);
							if (t3lib_extMgm::isLoaded('ratings') && $this->conf['ratings']) {
								$ratingsAPI = t3lib_div::makeInstance('tx_ratings_api');
								$content .= $ratingsAPI->getRatingDisplay($this->tables['filegroups'] . '_' . $this->piVars['uid']);
							}
						}
						break;
					case 'LIST':
						if (!isset($this->piVars['uid'])) {
							$content .= $this->renderList();
						}
						break;
					case 'SEARCH':
						if (!isset($this->piVars['uid'])) {
							$content .= $this->renderSearch();
						}
						break;
					case 'RSSFEED':
						if ($pageId = $GLOBALS['TSFE']->tmpl->setup['datastore_rss.']['typeNum']) {
							$content .= $this->renderRSS(
								t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '?id=' . $GLOBALS['TSFE']->id . '&type=' . $pageId,
								$this->conf['rss.']['imgSrc']
							);
						}
				}
			}
		}
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Control piVars data
	 *
	 * @param	string		$content: html content
	 * @return	boolean
	 */
	function controlVars(&$content) {
		$error = false;
		if (isset($this->piVars['uid']) && !is_numeric($this->piVars['uid'])) {
			$content .= $this->renderContentError($this->pi_getLL('error_param_uid'));
			$error = true;
		}
		return $error ? false : true;
	}

	/**
	 * Init the plugin
	 *
	 * @return	boolean
	 */
	function init() {
		// Get GP vars
		if ($this->piVars['fileformat'][0] && !is_numeric($this->piVars['fileformat'][0])) {
			$tmp = explode(',',$this->piVars['fileformat'][0]);
			if ($tmp) {
				$this->piVars['fileformat'] = array_diff($this->piVars['fileformat'], array(0 => $this->piVars['fileformat'][0]));
				$this->piVars['fileformat'] = array_merge($this->piVars['fileformat'], $tmp);
			}
		}
		if ($this->piVars['tiers'][0] && !is_numeric($this->piVars['tiers'][0])) {
			$tmp = explode(',',$this->piVars['tiers'][0]);
			if ($tmp) {
				$this->piVars['tiers'] = array_diff($this->piVars['tiers'], array(0 => $this->piVars['tiers'][0]));
				$this->piVars['tiers'] = array_merge($this->piVars['tiers'], $tmp);
			}
		}

		$this->list_criteria = $this->piVars;
		// t3lib_div::debug($this->list_criteria, '0');
		$this->initCriteria();
		// t3lib_div::debug($this->list_criteria, '1');
		$this->list_criteriaNav = array();
		foreach ($this->list_criteria as $criteria => $value) {
			if ( ($criteria != 'uid') && ($criteria != 'submit') && ($criteria != 'returnID') ) {
				$this->list_criteriaNav[$this->prefixId . '[' . $criteria . ']'] = $value;
			}
		}

		// Get setting ==========
		$this->pi_initPIflexForm();

		// List view
		$listFields = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'listFields', 'displayList'), true);
		if (empty($listFields)) {
			$listFields = t3lib_div::trimExplode(',', $this->conf['displayList.']['fields'], true);
		}
		if (!empty($listFields)) {
			$this->listFields = $listFields;
		}
		$this->headersId = array();
		foreach ($this->listFields as $field) {
			$this->headersId[$field] = uniqid($this->prefixId);
		}


		$this->fileLinks = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'fileLink', 'displayList'), true);
		if (empty($this->fileLinks)) {
			$this->fileLinks = t3lib_div::trimExplode(',', $this->conf['displayList.']['fileLink'], true);
		}

		// Single view
		$detailFields = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'detailFields', 'single_setting'), true);
		if (empty($detailFields)) {
			$detailFields = t3lib_div::trimExplode(',', $this->conf['displaySingle.']['fields'], true);
		}
		if ( !empty($detailFields) )
			$this->detailFields = $detailFields;

		// Get template file
		$templateflex_file = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template_file', 'configuration');
		$this->templateCode = $this->cObj->fileResource($templateflex_file ? $templateflex_file : $this->conf['templateFile']);

		// Get view to display
		$code = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display', 'configuration');
		$this->config['code'] = $code ? $code : $this->cObj->stdWrap($this->conf['code'], $this->conf['code.']);

		if (empty($this->config['code'])) {
			$this->config['code'] = !empty($this->config['code'])?$this->config['code']:'SINGLE';
		}

		$this->storage = !empty($this->cObj->data['pages'])?$this->cObj->data['pages']:0;
		$this->fileField = !empty($this->conf['displayList.']['fileField'])?$this->conf['displayList.']['fileField']:'';

		if (!$this->conf['fileformatPictoMaxW'])
			$this->conf['fileformatPictoMaxW'] = 62;
		if (!$this->conf['fileformatPictoMaxW'])
			$this->conf['fileformatPictoMaxH'] = 20;
		if (!$this->conf['licences']['logo.']['maxW'])
			$this->conf['licences']['logo.']['maxW'] = 20;
		if (!$this->conf['licences']['logo.']['maxH'])
			$this->conf['licences']['logo.']['maxH'] = 20;

		$this->nbFileGroup = 0;
		$this->nbFileGroupByPage =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'nbFileGroupByPage', 'configuration');
		if (!$this->nbFileGroupByPage) {
			$this->nbFileGroupByPage = $this->conf['nbFileGroupByPage'];
		}

		// Get select params
		$sortName = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sortName', 'selectParams');
		$this->conf['sorting.']['name'] = $sortName? $sortName: $this->conf['sorting.']['name'];
		$sortOrder = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sortOrder', 'selectParams');
		$this->conf['sorting.']['order'] = $sortOrder? $sortOrder: $this->conf['sorting.']['order'];
		if (!$this->conf['sorting.']['name'] && !$this->conf['sorting.']['order']) {
			$this->conf['sorting.']['name'] = 'update_date';
			$this->conf['sorting.']['order'] = 'DESC';
		}
		$agencies = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'agencies', 'selectParams'), true);
		if (!empty($agencies)) {
			$this->conf['select.']['agencies'] = implode(',', $agencies);
		}

		//==========
		if (!$this->conf['singlePid'])
			$this->conf['singlePid'] = $GLOBALS['TSFE']->id;


		if (!$this->conf['resultsSearchPid'])
			$this->conf['resultsSearchPid'] = $GLOBALS['TSFE']->id;

		if (empty($this->conf['rss.']['imgSrc']))
			$this->conf['rss.']['imgSrc'] = t3lib_extMgm::extRelPath($this->extKey) . 'res/img_rss.png';

		return true;
	}
	
	/**
	 * Initalizes criteria
	 *
	 * @return	void
	 */
	function initCriteria() {
		unset($this->list_criteria['deleted']);
		if (is_array($this->piVars['deleted']['agencies']) && !empty($this->piVars['deleted']['agencies'])) {
			$this->list_criteria['agencies'] = array_diff($this->list_criteria['agencies'], $this->piVars['deleted']['agencies']);
		}
		if (is_array($this->piVars['deleted']['owners']) && !empty($this->piVars['deleted']['owners'])) {
			$this->list_criteria['owners'] = array_diff($this->list_criteria['owners'], $this->piVars['deleted']['owners']);
		}
		if (is_array($this->piVars['deleted']['fileformat']) && !empty($this->piVars['deleted']['fileformat'])) {
			$this->list_criteria['fileformat'] = array_diff($this->list_criteria['fileformat'], $this->piVars['deleted']['fileformat']);
		}
		if (is_array($this->piVars['deleted']['licences']) && !empty($this->piVars['deleted']['licences'])) {
			$this->list_criteria['licences'] = array_diff($this->list_criteria['licences'], $this->piVars['deleted']['licences']);
		}
		
		// Hook for additionnal init
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['addInitCriteria'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['addInitCriteria'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->addInitCriteria($this->piVars, $this->list_criteria, $this->conf, $this);
			}
		}
		
	}

	/**
	 * Render the search view
	 *
	 * @return	string		$content The search view content
	 */
	function renderSearch() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH###');
		$locMarkers = array(
			'###SELECTED_CRITERIA###' => $this->renderSelectedCriteria(),
			'###SORTING###' => $this->renderSorting(),
		);
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers);

		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###KEYWORDS_LABEL###' => htmlspecialchars($this->pi_getLL('search_keywords', 'Keywords', true)),
			'###KEYWORDS_VALUE###' => htmlspecialchars($this->piVars['keywords']),
			'###SEARCHBUTTON_VALUE###' => htmlspecialchars($this->pi_getLL('search_submit', 'Submit', true)),
			'###FORM_ACTION###' => $this->pi_getPageLink($this->conf['resultsSearchPid']),
			'###TITLE_TIERS###' => htmlspecialchars($this->pi_getLL('search_tiersTitle', 'Tiers', true)),
			'###TITLE_AGENCIES###' => htmlspecialchars($this->pi_getLL('search_agenciesTitle', 'Agencies', true)),
			'###TITLE_OWNERS###' => htmlspecialchars($this->pi_getLL('search_ownersTitle', 'Owners', true)),
			'###TITLE_FILEFORMAT###' => htmlspecialchars($this->pi_getLL('search_fileformatTitle', 'File format', true)),
			'###TITLE_LICENCES###' => htmlspecialchars($this->pi_getLL('search_licencesTitle', 'Licences', true)),
			'###TITLE_UPDATE###' => htmlspecialchars($this->pi_getLL('search_update', 'Update from', true)),
			'###UPDATE_DAY_LABEL###' => htmlspecialchars($this->pi_getLL('search_update_day', 'Update from a day', true)),
			'###CHECKED_UPDATE_DAY###' => $this->list_criteria['update']=='day'? 'checked="checked"': '',
			'###UPDATE_WEEK_LABEL###' => htmlspecialchars($this->pi_getLL('search_update_week', 'Update from a week', true)),
			'###CHECKED_UPDATE_WEEK###' => $this->list_criteria['update']=='week'? 'checked="checked"': '',
			'###UPDATE_MONTH_LABEL###' => htmlspecialchars($this->pi_getLL('search_update_month', 'Update from a month', true)),
			'###CHECKED_UPDATE_MONTH###' => $this->list_criteria['update']=='month'? 'checked="checked"': '',
			'###UPDATE_NONE_LABEL###' => htmlspecialchars($this->pi_getLL('search_update_none', 'None', true)),
			'###CHECKED_UPDATE_NONE###' => !in_array($this->list_criteria['update'], array('day', 'week', 'month'))? 'checked="checked"': '',
		);

		$fileformatItems = $this->renderFileformatItems($template, $this->getFileformats(true));
		$agenciesItems = $this->renderAgenciesItems($template, $this->getTiersAgencies());
		$licenceItems = $this->renderLicenceItems($template, $this->getLicences());
		$ownerItems = $this->renderOwnerItems($template, $this->getOwners());

		$subpartArray = array();
		$subpartArray['###FILEFORMAT_ITEM###'] = $fileformatItems;
		$subpartArray['###AGENCIES_ITEM###'] = $agenciesItems;
		//-- Compatibilité with previous version (from revision #75453)
		$subpartArray['###TIERS_ITEM###'] = $agenciesItems;
		//--
		$subpartArray['###LICENCES_ITEM###'] = $licenceItems;
		$subpartArray['###OWNERS_ITEM###'] = $ownerItems;

		// Hook for add fields markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalFieldsSearchMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalFieldsSearchMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalFieldsSearchMarkers($markers, $subpartArray, $template, $this->conf, $this);
			}
		}
		$content .= $this->cObj->substituteMarkerArrayCached($template, $markers, $subpartArray);
		return $content;
	}

	/**
	 * Render seleceted Criteria
	 *
	 * @return	string		Selected criteria HTML content
	 */
	function renderSelectedCriteria() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SELECTED_CRITERIA###');

		$subpartArray = array();
		$subpartArray['###SUBPART_SC_KEYWORDS###'] = $this->renderSC_keywords($template);
		$subpartArray['###SUBPART_SC_AGENCIES###'] = $this->renderSC_agencies($template);
		//-- Compatibilité with previous version (from revision #75453)
		$subpartArray['###SUBPART_SC_TIERS###'] = $subpartArray['###SUBPART_SC_AGENCIES###'];
		//--
		$subpartArray['###SUBPART_SC_OWNERS###'] = $this->renderSC_owners($template);
		$subpartArray['###SUBPART_SC_FORMATS###'] = $this->renderSC_formats($template);
		$subpartArray['###SUBPART_SC_LICENCES###'] = $this->renderSC_licences($template);

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
	function renderSC_keywords($template) {
		if (!$this->list_criteria['keywords'])
			return '';

		$template = $this->cObj->getSubpart($template, '###SUBPART_SC_KEYWORDS###');
		$markers = array(
			'###SC_KEYWORDS_NAME###' => $this->prefixId.'[deleted][keywords]',
			'###SC_KEYWORDS_LABEL###' => $this->pi_getLL('sc_keywords', 'Keywords', true),
			'###SC_KEYWORDS_VALUE###' => $this->list_criteria['keywords'],
		);
		return $this->cObj->substituteMarkerArray($template, $markers);
	}
	
	/**
	 * Renders selected criteria agencies
	 *
	 * @param	string	$template
	 * @return	string	The HTML content
	 */
	function renderSC_agencies($template) {
		$content = '';
		if (is_array($this->list_criteria['agencies']) && !empty($this->list_criteria['agencies'])) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'uid, name',
				$this->tables['tiers'],
				'1' .  $this->cObj->enableFields($this->tables['tiers']) .  ' AND uid in (' . implode(',', $this->list_criteria['agencies']) . ')',
				'',
				'name',
				'',
				'name'
			);
		}
		if (is_array($rows) && !empty($rows)) {
			$template = $this->cObj->getSubpart($template, '###SUBPART_SC_AGENCIES###');
			$markers = array();
			$itemTemplate = $this->cObj->getSubpart($template, '###SC_AGENCY_ITEM###');
			$itemContent = '';
			foreach ($rows as $row) {
				$itemMarkers = array(
					'###AGENCY_LABEL###' => $row['name'],
					'###AGENCY_VALUE###' => $row['uid'],
					'###AGENCY_NAME###' => $this->prefixId.'[deleted][agencies][]',
				);
				$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers);
			}
			$template = $this->cObj->substituteSubpart($template, '###SC_AGENCY_ITEM###', $itemContent);
			$markers = array(
				'###TITLE_AGENCIES###' => $this->pi_getLL('sc_agencies', 'Agencies', true),
				//-- Compatibilité with previous version (from revision #75453)
				'###SC_TIERS_LABEL###' => $this->pi_getLL('sc_tiers', 'Tiers', true),
				'###SC_TIERS_VALUE###' => $this->cObj->stdWrap(implode(',', array_keys($rows)), $this->conf['displaySearch.']['tiers.']),
				// --
			);
			$content = $this->cObj->substituteMarkerArray($template, $markers);
		}
		return $content;
	}

	/**
	 * Renders selected criteria owner
	 *
	 * @param	string	$template
	 * @return	string	The HTML content
	 */
	function renderSC_owners($template) {
		$content = '';
		if (is_array($this->list_criteria['owners']) && !empty($this->list_criteria['owners'])) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'uid, name',
				$this->tables['tiers'],
				'1' .  $this->cObj->enableFields($this->tables['tiers']) .  ' AND uid in (' . implode(',', $this->list_criteria['owners']) . ')',
				'',
				'name'
			);
		}
		if (is_array($rows) && !empty($rows)) {
			$template = $this->cObj->getSubpart($template, '###SUBPART_SC_OWNERS###');
			$markers = array();
			$itemTemplate = $this->cObj->getSubpart($template, '###SC_OWNER_ITEM###');
			$itemContent = '';
			foreach ($rows as $row) {
				$itemMarkers = array(
					'###OWNER_LABEL###' => $row['name'],
					'###OWNER_VALUE###' => $row['uid'],
					'###OWNER_NAME###' => $this->prefixId.'[deleted][owners][]',
				);
				$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers);
			}
			$template = $this->cObj->substituteSubpart($template, '###SC_OWNER_ITEM###', $itemContent);
			$markers = array('###TITLE_OWNERS###' => $this->pi_getLL('sc_owners', 'Owners', true));
			$content = $this->cObj->substituteMarkerArray($template, $markers);
		}
		return $content;
	}

	/**
	 * Renders selected criteria formats
	 *
	 * @param	string	$template
	 * @return	string	The HTML content
	 */
	function renderSC_formats($template) {
		$content = '';
		if (is_array($this->list_criteria['fileformat']) && !empty($this->list_criteria['fileformat'])) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'uid, name',
				$this->tables['fileformats'],
				'1' .  $this->cObj->enableFields($this->tables['fileformats']) .  ' AND uid in (' . implode(',', $this->list_criteria['fileformat']) . ')',
				'',
				'name',
				'',
				'name'
			);
		}
		if (is_array($rows) && !empty($rows)) {
			$template = $this->cObj->getSubpart($template, '###SUBPART_SC_FORMATS###');
			$markers = array();
			$itemTemplate = $this->cObj->getSubpart($template, '###SC_FORMAT_ITEM###');
			$itemContent = '';
			foreach ($rows as $row) {
				$itemMarkers = array(
					'###FILEFORMAT_LABEL###' => $row['name'],
					'###FILEFORMAT_VALUE###' => $row['uid'],
					'###FILEFORMAT_NAME###' => $this->prefixId.'[deleted][fileformat][]',
				);
				$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers);
			}
			$template = $this->cObj->substituteSubpart($template, '###SC_FORMAT_ITEM###', $itemContent);
			$markers = array(
				'###TITLE_FORMATS###' => $this->pi_getLL('sc_formats', 'Formats', true),
				//-- Compatibilité with previous version (from revision #75453)
				'###SC_FORMATS_LABEL###' => $this->pi_getLL('sc_formats', 'Formats', true),
				'###SC_FORMATS_VALUE###' => $this->cObj->stdWrap(implode(',', array_keys($rows)), $this->conf['displaySearch.']['formats.']),
				//--
			);
			$content = $this->cObj->substituteMarkerArray($template, $markers);
		}
		return $content;
	}
	
	/**
	 * Renders selected criteria licences
	 *
	 * @param	string	$template
	 * @return	string	The HTML content
	 */
	function renderSC_licences($template) {
		$content = '';
		if (is_array($this->list_criteria['licences']) && !empty($this->list_criteria['licences'])) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'uid, name',
				$this->tables['licences'],
				'1' .  $this->cObj->enableFields($this->tables['licences']) .  ' AND uid in (' . implode(',', $this->list_criteria['licences']) . ')',
				'',
				'name',
				'',
				'name'
			);
		}
		if (is_array($rows) && !empty($rows)) {
			$template = $this->cObj->getSubpart($template, '###SUBPART_SC_LICENCES###');
			$markers = array();
			$itemTemplate = $this->cObj->getSubpart($template, '###SC_LICENCE_ITEM###');
			$itemContent = '';
			foreach ($rows as $row) {
				$itemMarkers = array(
					'###LICENCE_LABEL###' => $row['name'],
					'###LICENCE_VALUE###' => $row['uid'],
					'###LICENCE_NAME###' => $this->prefixId.'[deleted][licences][]',
				);
				$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers);
			}
			$template = $this->cObj->substituteSubpart($template, '###SC_LICENCE_ITEM###', $itemContent);
			$markers = array(
				'###TITLE_LICENCES###' => $this->pi_getLL('sc_licences', 'Licences', true),
				//-- Compatibilité with previous version (from revision #75453)
				'###SC_LICENCES_LABEL###' => $this->pi_getLL('sc_licences', 'Licences', true),
				'###SC_LICENCES_VALUE###' => $this->cObj->stdWrap(implode(',', array_keys($rows)), $this->conf['displaySearch.']['licences.']),
				//--
			);
			$content = $this->cObj->substituteMarkerArray($template, $markers);
		}
		return $content;
	}
	
	/**
	 * Render sorting
	 *
	 * @return	string		The sorting HTML code
	 */
	function renderSorting() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SORTING###');

		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###SORTING_TITLE###' => $this->pi_getLL('sort_title', 'Sort By', true),
		);

		$sortNames = t3lib_div::trimExplode(',', $this->conf['displaySorting.']['sortNames'], true);
		$itemTemplate = $this->cObj->getSubpart($template, '###SORTING_ITEM###');

		foreach ($sortNames as $sortName) {
			$locMarkers = array();

			$data = array(
				'sortName' => $sortName,
				'active' => $this->list_criteria['sort']['column']? $this->list_criteria['sort']['column']: $this->conf['sorting.']['name'],
			);
			$cObj = t3lib_div::makeInstance('tslib_cObj');
			$cObj->start($data, 'Sorting');
			$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
			$locMarkers['###SORTING_NAME###'] = $cObj->stdWrap('', $this->conf['displaySorting.'][$sortName . '.']);

			$items .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers);
		}
		$template = $this->cObj->substituteSubpart($template, '###SORTING_ITEM###', $items);
		return $this->cObj->substituteMarkerArray($template, $markers);
	}

	/**
	 * Render file formats
	 *
	 * @param	string		$template: The template for file formats
	 * @param	array		$aFileformats: The file formats
	 * @return	$item
	 */
	function renderFileformatItems($template, $aFileformats) {
		if (is_array($aFileformats) && count($aFileformats)) {
			foreach ($aFileformats as $fileformat) {
				if (!empty($fileformat['extension'])) {
					$fileformatValue = strtoupper($fileformat['extension']);
				}
				else {
					$fileformatValue = $this->pi_getLL('search_fileformatOther', 'Other format', true);
				}
				$markers = array(
					'###PREFIXID###' => $this->prefixId,
					'###FILEFORMAT_LABEL###' => htmlspecialchars($fileformatValue),
					'###FILEFORMAT_VALUE###' => $fileformat['uid'],
					'###CHECKED###' => '',
				);
				if (is_array($this->list_criteria['fileformat']) && t3lib_div::inArray($this->list_criteria['fileformat'],$fileformat['uid'])) {
					$markers['###CHECKED###'] = 'checked';
				}
				$fileformatItem = $this->cObj->getSubpart($template, '###FILEFORMAT_ITEM###');
				$item .= $this->cObj->substituteMarkerArray($fileformatItem, $markers);
			}
		}
		return $item;
	}

	/**
	 * Render tiers agencies
	 *
	 * @param	string		$template: The template for tiers
	 * @param	array		$agencies: The tiers
	 * @return	$item
	 */
	function renderAgenciesItems($template, $agencies) {
		if (is_array($agencies) && count($agencies)) {
			$agencies = t3lib_div::trimExplode(',', $this->conf['select.']['agencies'], true);
			foreach ($agencies as $agency) {
				$markers = array(
					'###PREFIXID###' => $this->prefixId,
					'###AGENCY_LABEL###' => htmlspecialchars($agency['name']),
					'###AGENCY_NAME###' => $this->prefixId.'[agencies][]',
					'###AGENCY_VALUE###' => intval($agency['uid']),
					'###CHECKED###' => in_array($agency['uid'], $agencies)? 'checked = "checked"': '',
				);
				if (is_array($this->list_criteria['agencies']) && t3lib_div::inArray($this->list_criteria['agencies'],$agency['uid'])) {
					$markers['###CHECKED###'] = 'checked';
				}
				$agenciesItem = $this->cObj->getSubpart($template, '###AGENCIES_ITEM###');
				$item .= $this->cObj->substituteMarkerArray($agenciesItem, $markers);
			}
		}
		return $item;
	}
	
	/**
	 * Render owners
	 *
	 * @param	string		$template: The template for owners
	 * @param	array		$owners: The owners
	 * @return	The HTML content
	 */
	function renderOwnerItems($template, $owners) {
		$content = '';
		if (!is_array($owners) || empty($owners))
			return $content;

		foreach ($owners as $owner) {
			$markers = array(
				'###PREFIXID###' => $this->prefixId,
				'###OWNER_LABEL###' => htmlspecialchars($owner['name']),
				'###OWNER_NAME###' => $this->prefixId.'[owners][]',
				'###OWNER_VALUE###' => intval($owner['uid']),
				'###CHECKED###' => '',
			);
			if (is_array($this->list_criteria['owners']) && t3lib_div::inArray($this->list_criteria['owners'],$owner['uid'])) {
				$markers['###CHECKED###'] = 'checked="checked"';
			}
			$ownerItem = $this->cObj->getSubpart($template, '###OWNERS_ITEM###');
			$content .= $this->cObj->substituteMarkerArray($ownerItem, $markers);
		}
		return $content;
	}

	/**
	 * Render licences
	 *
	 * @param	string		$template: The template for licences
	 * @param	array		$aTiers: The licences
	 * @return	$item
	 */
	function renderLicenceItems($template, array $aLicences) {
		$select_licences = t3lib_div::trimExplode(',', $this->conf['select.']['licences'], true);
		$licencesItem = $this->cObj->getSubpart($template, '###LICENCES_ITEM###');
		foreach ($aLicences as $licence) {
			$markers = array(
				'###PREFIXID###' => $this->prefixId,
				'###LICENCE_LABEL###' => htmlspecialchars($licence['name']),
				'###LICENCE_NAME###' => $this->prefixId.'[licences][]',
				'###LICENCE_VALUE###' => intval($licence['uid']),
				'###CHECKED###' => '',
			);
			if (is_array($this->list_criteria['licences']) && t3lib_div::inArray($this->list_criteria['licences'],$licence['uid'])) {
				$markers['###CHECKED###'] = 'checked';
			}
			$item .= $this->cObj->substituteMarkerArray($licencesItem, $markers);
		}
		return $item;
	}

	/**
	 * Render list view
	 *
	 * @return	$content
	 */
	function renderList() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_LIST###');

		$headerItems = $this->renderListHeader($this->cObj->getSubpart($template, '###HEADER_ITEM###'));
		$rowItems = $this->renderListRows($template);

		$markers = array(
			'###CAPTION###' => htmlspecialchars($this->pi_getLL('list_caption', 'List', true)),
			'###UNIQID###' => uniqid($this->prefixId),
			'###PREFIXID###' => $this->prefixId,
			'###PAGE_BROWSER###' => $this->getListGetPageBrowser(intval(ceil($this->nbFileGroup/$this->nbFileGroupByPage))),
		);

		$subpartArray = array();

		if (!$this->nbFileGroup) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_EMPTY_LIST###');
			$markers = array(
				'###PREFIXID###' => $this->prefixId,
				'###ANY_DATASET###' => $this->pi_getLL('any_dataset', 'There is any dataset', true),
			);
		}
		else {
			$subpartArray['###HEADER_ITEM###'] = $headerItems;
			$subpartArray['###GROUP_ROW_CONTENT###'] = $rowItems;

			// Hook for add fields markers to list view
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListFieldsMarkers'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListFieldsMarkers'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$_procObj->additionalListFieldsMarkers($markers, $subpartArray, $template, $this);
				}
			}
		}

		$content .= $this->cObj->substituteMarkerArrayCached($template, $markers, $subpartArray);
		return $content;
	}

	/**
	 * Render list headers
	 *
	 * @param	$template
	 * @return	$content
	 */
	function renderListHeader($template) {
		foreach ($this->listFields as $field) {
			$markers['###HEADERID' . strtoupper($field) . '###'] = $this->headersId[$field];
			$markers['###HEADER' . strtoupper($field) . '###'] = htmlspecialchars($this->pi_getLL('th_' . $field, $field, true));
			$markers['###SORT' . strtoupper($field) . '_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '?id=' . $GLOBALS['TSFE']->id
				. '&' . $this->prefixId . '[sort][column]=' . $field
				. '&' . $this->prefixId . '[sort][order]=' . (( ($this->list_criteria['sort']['column'] == $field) &&  ($this->list_criteria['sort']['order'] == 'ASC'))? 'DESC': 'ASC');
			$markers['###SORT' . strtoupper($field) . '_LINK_TITLE###'] = htmlspecialchars($this->pi_getLL('sort_' . $field . '_link_title', 'Sort link on ' . $field, true));
			$markers['###SORT' . strtoupper($field) . '_ALT###'] = htmlspecialchars($this->pi_getLL('sort_' . $field . '_alt', 'Sort on ' . $field, true));
			$markers['###SORT' . strtoupper($field) . '_TITLE###'] = htmlspecialchars($this->pi_getLL('sort_' . $field . '_title', 'Sort on ' . $field, true));
			if ($this->list_criteria['sort']['column'] == $field) {
				if ($this->list_criteria['sort']['order'] == 'ASC') {
					$markers['###SORT' . strtoupper($field) . '_IMG###'] = $this->conf['displayList.']['sort.']['sortImg.']['asc'];
				} else {
					$markers['###SORT' . strtoupper($field) . '_IMG###'] = $this->conf['displayList.']['sort.']['sortImg.']['desc'];
				}
			} else {
					$markers['###SORT' . strtoupper($field) . '_IMG###'] = $this->conf['displayList.']['sort.']['sortImg.']['inactive'];
			}
		}

		$content .= $this->cObj->substituteMarkerArray($template, $markers);
		return $content;
	}

	/**
	 * Render list rows
	 *
	 * @param	$template
	 * @return	$content
	 */
	function renderListRows($template) {
		$queryJoin = '';
		$whereClause = '';
		$groupBy = '';

		// Set where clause with junture
		if (isset($this->list_criteria['keywords']) && !empty($this->list_criteria['keywords'])) {
			$whereClause .= ' AND (
				LOCATE("' . strtoupper($this->list_criteria['keywords']) . '", UPPER(`'.$this->tables['filegroups'].'`.`title`))
				OR LOCATE("' . strtoupper($this->list_criteria['keywords']) . '", UPPER(`'.$this->tables['filegroups'].'`.`description`)))
			';
		}
		if ((isset($this->list_criteria['agencies']) && count($this->list_criteria['agencies'])) || $this->conf['select.']['agencies']) {
			$agencies = array();
			if (is_array($this->list_criteria['agencies']))
				$agencies = $this->list_criteria['agencies'];
			if ($select_agencies = t3lib_div::trimExplode(',', $this->conf['select.']['agencies'], true))
				$agencies = array_merge($agencies, $select_agencies);
			if (!empty($agencies))
				$whereClause .= ' AND ( `' . $this->tables['filegroups'] . '`.`agency` IN (' . implode(',', $agencies) . '))';
		}
		if (isset($this->list_criteria['owners']) && count($this->list_criteria['owners'])) {
			$owners = implode(',', $this->list_criteria['owners']);
			$owners = t3lib_div::intExplode(',', $owners, true);
			$whereClause .= ' AND ( `' . $this->tables['filegroups'] . '`.`owner` IN (' . implode(',', $owners) . '))';
		}
		if (isset($this->list_criteria['fileformat']) && count($this->list_criteria['fileformat'])){
			$queryJoin .= '
				INNER JOIN ' . $this->tables['file_filegroup_mm'] . '
					ON uid_foreign = ' . $this->tables['filegroups'] . '.`uid`
				INNER JOIN ' . $this->tables['files'] . '
					ON uid_local = ' . $this->tables['files'] . '.`uid`
			';
			$whereClause .= ' AND `' . $this->tables['files'] . '`.`format` IN (' . implode(',', $this->list_criteria['fileformat']) . ')' . $this->cObj->enableFields($this->tables['files']);
		}
		if (isset($this->list_criteria['licences']) && count($this->list_criteria['licences'])) {
			$whereClause .= ' AND ( `' . $this->tables['filegroups'] . '`.`licence` IN (' . implode(',', $this->list_criteria['licences']) . '))';
		}
		if ($this->list_criteria['update'] && in_array($this->list_criteria['update'], array('day','week','month'))) {
			switch ($this->list_criteria['update']) {
				case 'day':
					$period = 1;
					break;
				case 'week':
					$period = 7;
					break;
				case 'month':
					$period = date('t', mktime(0,0,0,date('n')-1));
					break;
			}
			$whereClause .= ' AND DATEDIFF(CURDATE(), FROM_UNIXTIME(update_date, \'%Y-%m-%d\'))<=' . $period;
		}
		$whereClause .= $this->cObj->enableFields($this->tables['filegroups']);

		// Hook for add fields markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['addSearchRestriction'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['addSearchRestriction'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->addSearchRestriction($whereClause, $queryJoin, $this->conf, $this);
			}
		}

		// Get all filegroups
		$filegroups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'DISTINCT `' . $this->tables['filegroups'] . '`.`uid`',
			$this->tables['filegroups'] . $queryJoin,
			'`' . $this->tables['filegroups'] . '`.`pid` = ' . $this->storage . $whereClause  . $this->cObj->enableFields($this->tables['filegroups'])
		);
		if ( is_array($filegroups) && !empty($filegroups) )
			$this->nbFileGroup = count($filegroups);

		// Set sort and order and get filegroups for a page number
		t3lib_div::loadTCA($this->tables['filegroups']);
		$columns = $GLOBALS['TCA'][$this->tables['filegroups']]['columns'];
		$columns = array_keys($columns);
		if ($GLOBALS['TCA'][$this->tables['filegroups']]['ctrl']['crdate'])
			$columns[] = 'crdate';
		if ($GLOBALS['TCA'][$this->tables['filegroups']]['ctrl']['tstamp'])
			$columns[] = 'tstamp';

		$sorting['column'] = $this->conf['sorting.']['name'];
		$sorting['order'] = $this->conf['sorting.']['order'];
		if ($this->list_criteria['sort']['column'])
			$sorting = $this->list_criteria['sort'];

		if ( in_array($sorting['column'], $columns) && ($sorting['column'] != 'files') ) {
			$tiers = array(
				'agency',
				'contact',
				'publisher',
				'creator',
				'manager',
				'owner',
			);
			$order = ($sorting['order'])? $sorting['order']: 'ASC';
			if ( ($sorting['column'] == 'tstamp') && ($this->conf['displayList.']['sort.']['tstamp.']['day']) ) {
				$orderBy = 'FROM_UNIXTIME(`' . $this->tables['filegroups'] . '`.`' . $sorting['column'] . '`, "%Y%m%d") ' . $order . ', `' . $this->tables['filegroups'] . '`.`title` ASC';
			}
			elseif ( in_array($sorting['column'], $tiers) ) {
				$queryJoin .= ' LEFT OUTER JOIN `' . $this->tables['tiers'] . '` ON `' . $this->tables['tiers'] . '`.`uid` = `' . $this->tables['filegroups'] . '`.`' . $sorting['column'] . '`';
				$orderBy = '`' . $this->tables['tiers'] . '`.`name` ' . $order;
			}
			else	{
				$orderBy = '`' . $this->tables['filegroups'] . '`.`' . $sorting['column'] . '` ' . $order;
			}

			if ( $sorting['column'] != 'title')
				$orderBy .= ', `' . $this->tables['filegroups'] . '`.`title` ASC';
		}

		if ( empty($this->piVars['page']) ) {
			$start = 0;
		}	else	{
			$start = intval($this->piVars['page']) * $this->nbFileGroupByPage;
		}
		$fields = $this->listFields;
		foreach ($fields as $idx=>$field) {
			$fields[$idx] = '`' . $this->tables['filegroups'] . '`.`' . $field . '`';
		}

		$groupBy = '`'.$this->tables['filegroups'] . '`.`uid`, ' . implode(',', $fields);

		// Hook on extra column and sorting query
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['selectQuery_extraColumnSorting'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['selectQuery_extraColumnSorting'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->selectQuery_extraColumnSorting($sorting['column'], $order, $fields, $orderBy, $this->conf, $this);
			}
		}

		// Hook on extra group by query
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['selectQuery_extraGroupBy'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['selectQuery_extraGroupBy'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->selectQuery_extraGroupBy($sorting['column'], $groupBy, $this->conf, $this);
			}
		}

		$selectFields =  '`'.$this->tables['filegroups'] . '`.`uid`, ' . implode(',', $fields);

		// Executing query
		$filegroups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			$selectFields,
			$this->tables['filegroups'] . $queryJoin,
			'`' . $this->tables['filegroups'] . '`.`pid` = ' . $this->storage . $whereClause  . $this->cObj->enableFields($this->tables['filegroups']),
			$groupBy,
			$orderBy,
			$start . ',' . $this->nbFileGroupByPage
		);

		$i=0;
		foreach ($filegroups as $filegroup) {
			if ($i%2 == 0) {
				$templateGroup = $this->cObj->getSubpart($template, '###GROUP_ROW###');
			}	else	{
				$templateGroup = $this->cObj->getSubpart($template, '###GROUP_ROW_ALT###');
			}
			$content .= $this->cObj->substituteSubpart(
				$templateGroup,
				'###ROW_ITEM###',
				$this->renderListRow($this->cObj->getSubpart($templateGroup, '###ROW_ITEM###'), $filegroup)
			);

			$i++;
		}

		return $content;
	}

	/**
	 * Render list row
	 *
	 * @param	$template
	 * @param	$row
	 * @return	$content
	 */
	function renderListRow($template, $row) {
		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###URL###' => $this->pi_getPageLink(
				$this->conf['singlePid'],
				'',
				array_merge(
					$this->list_criteriaNav,
					array(
						$this->prefixId . '[uid]' => $row['uid'],
						// $this->prefixId . '[returnID]' => $GLOBALS['TSFE']->id,
					)
				)
			),
		);

		foreach ($this->listFields as $field) {
			$markers['###HEADERID' . strtoupper($field) . '###'] = $this->headersId[$field];
			switch ($field) {
				case 'creator':
				case 'manager':
				case 'owner':
				case 'publisher':
					$publisher = t3lib_BEfunc::getRecord($this->tables['tiers'], $row[$field]);
					$markers['###' . strtoupper($field) . '###'] = $this->cObj->stdWrap($publisher['name'], $this->conf['displayList.'][$field . '_stdWrap.']);
					break;
				case $this->fileField:
					$filesContent = $this->renderFiles('LIST', $row['uid'], $this->cObj->getSubpart($template, '###SECTION_FILE###'));
					$template = $this->cObj->substituteSubpart($template, '###SECTION_FILE###', $filesContent);
					break;
				case 'tstamp':
				case 'update_date':
				case 'release_date':
				case 'crdate':
					if (!empty($row[$field])&& $row[$field])
						$markers['###' . strtoupper($field) . '###'] = $this->cObj->stdWrap($row[$field], $this->conf['displayList.'][$field . '_stdWrap.']);
					else
						$markers['###' . strtoupper($field) . '###'] = '';
					break;
				case 'licence':
					$licence = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
						'name, link, logo',
						'tx_icsoddatastore_licences',
						'uid=' . $row['licence'],
						'',
						'',
						'1'
					);
					if (!empty($licence['logo'])) {
						$logo = $this->getImgResource($licence['logo'], $licence['name'], $this->conf['licences']['logo.']['maxW'], $this->conf['licences']['logo.']['maxH'], true);
					}
					$licence_value = $logo . $licence['name'];
					if (!empty($licence['link'])) {
						$licence_value = '<a href="' . $licence['link'] . '" target="_blank">' . $licence_value . '</a>';
					}
					$markers['###' . strtoupper($field) . '###'] = $this->cObj->stdWrap($licence_value, $this->conf['displayList.'][$field . '_stdWrap.']);
					break;
				default:
					$markers['###' . strtoupper($field) . '###'] = $this->cObj->stdWrap($row[$field], $this->conf['displayList.'][$field . '_stdWrap.']);
			}
		}

		$subpartArray = array();
		// Hook for add fields markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalFieldsMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalFieldsMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalFieldsMarkers($markers, $subpartArray, $template, $row, $this->conf['displayList.'], $this);
			}
		}
		$content .= $this->cObj->substituteMarkerArrayCached($template, $markers, $subpartArray);
		return $content;
	}

	/**
	 * Render files
	 *
	 * @param	string		$view		The view 'LIST' or 'SINGLE' to render files
	 * @param	int		$filegroup		The filegroup's uid
	 * @param	string		$template		The file's template to substitute
	 * @return	content		The content of filegroup files template substituted
	 */
	function renderFiles($view, $filegroup, $template) {
		$lConf = ($view == 'LIST')? $this->conf['displayList.']: $this->conf['displaySingle.'];
		t3lib_div::loadTCA($this->tables['files']);
		$uploadPaths['file'] = '';
		if ($GLOBALS['TCA'][$this->tables['files']]['columns']['file']['config']['uploadfolder'])
			$uploadPaths['file'] = $GLOBALS['TCA'][$this->tables['files']]['columns']['file']['config']['uploadfolder'] . '/';
		t3lib_div::loadTCA($this->tables['fileformats']);
		$uploadPaths['fileformat'] = $GLOBALS['TCA'][$this->tables['fileformats']]['columns']['picto']['config']['uploadfolder'] . '/';
		if ($view == 'LIST') {
			$filetypes = array();
			foreach ($this->fileLinks as $fileLink) {
				$filetypes[$fileLink] = t3lib_BEfunc::getRecord($this->tables['filetypes'], $fileLink);
			}
		}	else	{
			$filetypes = $this->getFiletypes();
		}
		$fields = array(
			'record_type',
			'file',
			'url',
			'md5',
			'type',
			'format',
			'size',
		);
		foreach ($fields as $idx=>$field) {
			$fields[$idx] = '`' . $this->tables['files'] . '`.`' . $field . '` as ' . $field;
		}
		if (!in_array($this->tables['files'].'.uid', $fields))
			$fields[] = $this->tables['files'].'.uid';
		foreach ($filetypes as $type) {
			$where = array(
				'1' . $this->cObj->enableFields($this->tables['files']) . $this->cObj->enableFields($this->tables['filegroups']),
				'`' . $this->tables['filegroups'] . '`.`uid` = ' . $filegroup,
				'`' . $this->tables['files'] . '`.`type` = ' . $type['uid'],
				'(`' . $this->tables['files'] . '`.`file` NOT LIKE "" OR `' . $this->tables['files'] . '`.`url` NOT LIKE "")',
			);
			if (isset($this->list_criteria['fileformat']) && ($view == 'LIST') && $this->conf['displayList.']['renderOnlySearchedFileFormats']) {
				$where[] = '`' . $this->tables['files'] . '`.`format` IN (' . implode(',',$this->list_criteria['fileformat']) . ')';
			}
			$pictoItems = '';

			if ($lConf['groupByFormat']) {
				$files = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'format, COUNT(*) as count_format',
					'`' . $this->tables['filegroups'] . '`' .
					' LEFT OUTER JOIN `' . $this->tables['file_filegroup_mm'] . '` ON `' . $this->tables['file_filegroup_mm'] . '`.`uid_foreign` = `' . $this->tables['filegroups'] . '`.`uid`' .
					' JOIN `' . $this->tables['files'] . '` ON `' . $this->tables['files'] . '`.`uid` = `' . $this->tables['file_filegroup_mm'] . '`.`uid_local`' . $this->cObj->enableFields($this->tables['files']),
					implode(' AND ', $where) .$this->cObj->enableFields($this->tables['filegroups']),
					'format'
				);
				$pictoItems = $this->renderFiles_byFormat($files, $template, array('uploadPaths' => $uploadPaths, 'lConf' => $lConf));
			}
			else {
				$files = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					implode(', ', $fields),
					'`' . $this->tables['filegroups'] . '`' .
					' LEFT OUTER JOIN `' . $this->tables['file_filegroup_mm'] . '` ON `' . $this->tables['file_filegroup_mm'] . '`.`uid_foreign` = `' . $this->tables['filegroups'] . '`.`uid`' .
					' JOIN `' . $this->tables['files'] . '` ON `' . $this->tables['files'] . '`.`uid` = `' . $this->tables['file_filegroup_mm'] . '`.`uid_local`' . $this->cObj->enableFields($this->tables['files']),
					implode(' AND ', $where) .$this->cObj->enableFields($this->tables['filegroups'])
				);
				$pictoItems = $this->renderFiles_byRow($files, $template, array('uploadPaths' => $uploadPaths, 'lConf' => $lConf));
			}
			
			$sectionContent = $this->cObj->substituteSubpart($template, '###PICTO_ITEM###', $pictoItems);
			if (!empty($files)) {
				$cObj = t3lib_div::makeInstance('tslib_cObj');
				$cObj->start($type, $this->tables['filetypes']);
				$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
				$content .= $this->cObj->substituteMarkerArray(
					$sectionContent,
					array('###SECTION_NAME###' => $cObj->stdWrap('', $this->conf['dataset.']['fileSectionTitle.']))
				);
			}
		}
		return $content;
	}
	
	/**
	 * Renders files by row
	 *
	 * @param	array	$files: Array of files rows
	 * @param	string	$template: The HTML template
	 * @param	array	$PA: An array with configuration options.
	 */
	function renderFiles_byRow($files, $template, $PA) {
		$uploadPaths = $PA['uploadPaths'];
		$lConf = $PA['lConf'];
		
		foreach ($files as $file) {
			$markers = array(
				'###FILEUID###' => $file['uid'],
				'###FILESIZE###' => '',
				'###FILEMD5###' => '',
				'###FILENAME###' => '',
			);
			if ($file['record_type'] == 0) {
				if ($file['size']) {
					$markers['###FILESIZE###'] = $this->getFileSize(intval($file['size']));
				}
				else {
					$markers['###FILESIZE###'] = $this->getFileSize(filesize($uploadPaths['file'] . $file['file']));
				}
				$markers['###FILEMD5###'] = $file['md5'];
				$filedata = t3lib_div::trimExplode('.', basename($file['file']));
				$fileext = $filedata[count($filedata) -1];
				unset($filedata[count($filedata) -1]);
				$filename = implode('.', $filedata);
				$file['filename'] = $filename . '.' . $fileext;
				$markers['###FILENAME###'] = $this->cObj->stdWrap($filename, $lConf['files.']['filename.']) . '.' . $fileext;
			} else {
				$markers['###FILEMD5###'] = $file['md5'];
				$markers['###FILENAME###'] = $this->cObj->stdWrap($file['url'], $lConf['files.']['filename.']);
			}
			$pictoItems .= $this->renderFiles_item($file, $template, $markers, $lConf);
		}
		return $pictoItems;
	}
	
	/**
	 * Renders files by format
	 *
	 * @param	array	$files: Array of files rows
	 * @param	string	$template: The HTML template
	 * @param	array	$PA: An array with configuration options.
	 */
	function renderFiles_byFormat($files, $template, $PA) {
		$uploadPaths = $PA['uploadPaths'];
		$lConf = $PA['lConf'];
		foreach ($files as $file) {
			$markers = array();
			$pictoItems .= $this->renderFiles_item($file, $template, $markers, $lConf);
		}
		return $pictoItems;
	}
	
	/**
	 * Renders files item
	 *
	 * @param	array	$file: The file row
	 * @param	string	$template: The HTML template
	 * @param	array	$markers: The markers array
	 * @param	array	$lConf: The conf
	 */
	function renderFiles_item($file, $template, $markers, $lConf) {
		$formats = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'name, picto',
			$this->tables['fileformats'],
			'1' . $this->cObj->enableFields($this->tables['fileformats']) . ' AND uid=' . $file['format'] ,
			'',
			'',
			'1'
		);
		if (is_array($formats) && !empty($formats)) {
			$format = $formats[0];
			$file['picto'] = $format['picto'];
			$file['format_title'] = $format['name'];
		}
		
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($file, $this->tables['files']);
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		$markers['###PICTO###'] =  $cObj->stdWrap('', $this->conf['datasetFile.']);
		$markers['###FILEEXT###'] = htmlspecialchars($file['format_title']);
		$markers['###COUNT_BYFORMAT###'] = htmlspecialchars($file['count_format']);
		
		$pictoTemplate = $this->cObj->getSubpart($template, '###PICTO_ITEM###');
		$linkTemplate = $this->cObj->getSubpart($pictoTemplate, '###LINK_ITEM###');
		$linkItem = $this->cObj->substituteMarkerArray($linkTemplate, $markers);
		$pictoItem = $this->cObj->substituteSubpart(
			$pictoTemplate, 
			'###LINK_ITEM###', 
			$cObj->stdWrap($linkItem, ($lConf['datasetFile.']? $lConf['datasetFile.']: $this->conf['datasetFile.']))
		);
		$pictoItem = $this->cObj->substituteMarkerArray($pictoItem, $markers);
		
		return $pictoItem;
	}
	
	/**
	 * Render single view
	 *
	 * @param	int		$id: filegroup uid
	 * @return	string		content
	 */
	function renderSingle($id) {
		$tiersIds = array();
		$tiersFields = array('publisher', 'agency', 'contact', 'creator', 'manager', 'owner');

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE###');

		$filegroups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'`' . $this->tables['filegroups'] . '`',
			'`uid` = ' . $id . $this->cObj->enableFields($this->tables['filegroups']),
			'',
			'',
			'1'
		);
		$filegroup = $filegroups[0];

		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###INTRO###' => $this->cObj->stdWrap($this->pi_getLL('detail_intro', 'Introduction', true), $this->conf['displaySingle.']['intro_stdWrap.']),
			'###FORMAT###' => $this->cObj->stdWrap($this->pi_getLL('detail_formatavailable', 'Available format', true), $this->conf['displaySingle.']['formatavaillable_stdWrap.']),
			'###BACKLINK###' => $this->pi_linkTP($this->pi_getLL('back', 'Back', true), $this->list_criteriaNav, 0, $this->piVars['returnID']),
			'###OTHER_DATA###' =>  $this->cObj->stdWrap($this->pi_getLL('detail_other_data', 'Other data', true), $this->conf['displaySingle.']['other_data_stdWrap.']),
		);

		foreach ($this->detailFields as $field) {
			$markers['###' . strtoupper($field) . '_LABEL###'] = $this->cObj->stdWrap($this->pi_getLL('detail_' . $field, $field, true), $this->conf['displaySingle.'][$field . '_label_stdWrap.']);

			/** Ne pas afficher les champs non remplis **/
			$tmp_subpart = '';
			$tmp_subpart = $this->cObj->getSubpart($template, '###SUBPART_'. strtoupper($field) .'###');
			if (!$filegroup[$field])
				$tmp_subpart = '';

			$template = $this->cObj->substituteSubpart($template, '###SUBPART_'. strtoupper($field) .'###', $tmp_subpart);

			if (in_array($field, $tiersFields) && $filegroup[$field])
				$tiersIds[] = $filegroup[$field];

			switch($field) {
				case 'publisher':
					$tiers = t3lib_BEfunc::getRecord($this->tables['tiers'], $filegroup[$field], '`name`', ' and `hidden`=0');
					$markers['###' . strtoupper($field) . '_VALUE###'] = $this->cObj->stdWrap($tiers['name'], $this->conf['displaySingle.'][$field . '_stdWrap.']);
					break;
				case 'agency' :
					$tiers = t3lib_BEfunc::getRecord($this->tables['tiers'], $filegroup[$field], '`name`', ' and `hidden`=0');
					$markers['###' . strtoupper($field) . '_VALUE###'] = $this->cObj->stdWrap($tiers['name'], $this->conf['displaySingle.'][$field . '_stdWrap.']);
					break;
				case 'contact' :
					$tiers = t3lib_BEfunc::getRecord($this->tables['tiers'], $filegroup[$field], '`name`', ' and `hidden`=0');
					$markers['###' . strtoupper($field) . '_VALUE###'] = $this->cObj->stdWrap($tiers['name'], $this->conf['displaySingle.'][$field . '_stdWrap.']);
					break;
				case 'creator' :
					$tiers = t3lib_BEfunc::getRecord($this->tables['tiers'], $filegroup[$field], '`name`', ' and `hidden`=0');
					$markers['###' . strtoupper($field) . '_VALUE###'] = $this->cObj->stdWrap($tiers['name'], $this->conf['displaySingle.'][$field . '_stdWrap.']);
					break;
				case 'manager' :
					$tiers = t3lib_BEfunc::getRecord($this->tables['tiers'], $filegroup[$field], '`name`', ' and `hidden`=0');
					$markers['###' . strtoupper($field) . '_VALUE###'] = $this->cObj->stdWrap($tiers['name'], $this->conf['displaySingle.'][$field . '_stdWrap.']);
					break;
				case 'owner' :
					$tiers = t3lib_BEfunc::getRecord($this->tables['tiers'], $filegroup[$field], '`name`', ' and `hidden`=0');
					$markers['###' . strtoupper($field) . '_VALUE###'] = $this->cObj->stdWrap($tiers['name'], $this->conf['displaySingle.'][$field . '_stdWrap.']);
					break;
				case 'licence' :
					$licence = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'`name`, `link`, `logo`',
						'`' . $this->tables['licences'] . '`',
						'`uid` = ' . $filegroup[$field] . $this->cObj->enableFields($this->tables['licences'])
					);

					if (!empty($licence[0]['logo'])) {
						$logo = $this->getImgResource($licence[0]['logo'], $licence[0]['name'], $this->conf['licences']['logo.']['maxW'], $this->conf['licences']['logo.']['maxH'], true);
					}
					$licence_value = $logo . $licence[0]['name'];
					if (!empty($licence[0]['link'])) {
						$licence_value = '<a href="' . $licence[0]['link'] . '" target="_blank">' . $licence_value . '</a>';
					}
					$markers['###' . strtoupper($field) . '_VALUE###'] = $this->cObj->stdWrap($licence_value, $this->conf['displaySingle.'][$field . '_stdWrap.']);
				break;
				default :
					$markers['###' . strtoupper($field) . '_VALUE###'] = $this->cObj->stdWrap($filegroup[$field], $this->conf['displaySingle.'][$field . '_stdWrap.']);
			}
		}
		// Render files content
		$filesContent = $this->renderFiles('SINGLE', $id, $this->cObj->getSubpart($template, '###SECTION_FILE###'));
		$template = $this->cObj->substituteSubpart($template, '###SECTION_FILE###', $filesContent);

		// Render providers icons
		$tiersIds = array_unique($tiersIds);
		if (empty($tiersIds)) {
			$markers['###PROVIDER_IMG###'] = '';
		}
		else {
			$resProvider = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				'uid_local, uid_foreign, fe_users.image as providerImg',
				'tx_icsoddatastore_tiers',
				'tx_icsoddatastore_feusers_tiers_mm',
				'fe_users',
				' AND uid_local IN(' . implode(',', $tiersIds) . ') AND fe_users.image!=\'\'',
				'',
				'tx_icsoddatastore_feusers_tiers_mm.sorting ASC'
			);
			$GLOBALS['TSFE']->includeTCA(0);
			$imagePath = $GLOBALS['TCA']['fe_users']['columns']['image']['config']['uploadfolder'] . '/';
			$providerImg = array();
			while ($provider = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resProvider))
			{
				$providerImg[] = $imagePath.$provider['providerImg'];
			}
			$markers['###PROVIDER_IMG###'] = $this->cObj->stdWrap(implode(',', $providerImg), $this->conf['displaySingle.']['providerImg.']);
		}

		$subpartArray = array();
		// Hook for add fields markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalFieldsMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalFieldsMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalFieldsMarkers($markers, $subpartArray, $template, $filegroup, $this->conf['displaySingle.'], $this);
			}
		}
		$content .= $this->cObj->substituteMarkerArrayCached($template, $markers, $subpartArray);
		return $content;
	}

	/**
	 * Rendering img resource
	 *
	 * @param	string		$resource: The path to img resource
	 * @param	string		$desc: The resource description
	 * @param	int		$width
	 * @param	int		$height
	 * @param	boolean		$ext: Render external resource "true", otherwise "false"
	 * @return	img		resource
	 */
	function getImgResource($resource, $desc, $width = 62, $height = 20, $external = false) {
		$imgPicto['file'] = $resource;
		$imgPicto['file.']['maxH'] = $height;
		$imgPicto['file.']['maxW'] = $width;
		$titleImg = $desc;
		$altImg = $desc;

		if (!empty($imgPicto)) {
			if ($external) {
				return '<img src="' . $resource . '" height="' . $imgPicto['file.']['maxH'] . '" width="' . $imgPicto['file.']['maxW'] . '" title="' . $titleImg . '" alt="' . $altImg . '" />';
			}	else	{
				return '<img src="' . $this->cObj->IMG_RESOURCE($imgPicto) . '" height="' . $imgPicto['file.']['maxH'] . '" width="' . $imgPicto['file.']['maxW'] . '" title="' . $titleImg . '" alt="' . $altImg . '" />';
			}
		}
	}

	/**
	 * Retrieves filegroup files
	 *
	 * @param	int		$filegroup: filegroup uid
	 * @return	$file_filegroup_mm		The MM relations or null
	 */
	function getFiles_mm($filegroup) {
		$file_filegroup_mm = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`uid_local`',
			$this->tables['file_filegroup_mm'],
			'`uid_foreign` = ' . $filegroup
		);

		if (is_array($file_filegroup_mm) && count($file_filegroup_mm)) {
			return $file_filegroup_mm;
		}
	}

	/**
	 * Get filesize . Display correct format
	 *
	 * @param	string		$file
	 * @return	document		size with unit
	 */
	function getFileSize($size) {
		if ($size>(1024*1024))
			return round($size/(1024*1024),1) . ' ' . $this->pi_getLL('megabytes', 'Mb', true);
		if ($size>(1024))
			return round($size/(1024)) . ' ' . $this->pi_getLL('kilobytes', 'kb', true);
		else
			return round($size) . ' ' . $this->pi_getLL('bytes', 'b', true);
	}

	/**
	 * Retrieves fileformats
	 *
	 * @param	boolean		$searchable
	 * @return	$fileformats		File formats
	 */
	function getFileformats($searchable = false) {
		$where = array('`pid` = ' . $this->storage);
		if ($searchable)
			$where[] = 	'searchable = 1';
		$fileformats = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`uid`, `name`, `extension`',
			$this->tables['fileformats'],
			implode(' AND ', $where) . $this->cObj->enableFields($this->tables['fileformats']),
			'',
			'`sorting` ASC'
		);

		if (is_array($fileformats) && count($fileformats)) {
			return $fileformats;
		}
		return false;
	}

	/**
	 * Retrieves filetypes
	 *
	 * @return	$filetypes		File types
	 */
	function getFiletypes() {
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, name',
			$this->tables['filetypes'],
			'1' . $this->cObj->enableFields($this->tables['filetypes']),
			'',
			'',
			'',
			'uid'
		);
	}

	/**
	 * Retrieves tiers
	 *
	 * @return	tiers
	 */
	function getTiersAgencies() {
		$agencies =  $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`' . $this->tables['tiers'] . '`.`uid`, `' . $this->tables['tiers'] . '`.`name`',
			$this->tables['filegroups'] . ' JOIN ' . $this->tables['tiers'] . ' ON `' . $this->tables['tiers'] . '`.`uid` = `' . $this->tables['filegroups']  . '`.`agency` ' . $this->cObj->enableFields($this->tables['tiers']),
			'1 ' . $this->cObj->enableFields($this->tables['filegroups']),
			'`' . $this->tables['filegroups']  . '`.`agency`',
			'`' . $this->tables['tiers'] . '`.`name` ASC'
		);
		return $agencies;
	}

	/**
	 * Retrieves owners
	 *
	 * @return	owners
	 */
	function getOwners() {
		$rows =  $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`' . $this->tables['tiers'] . '`.`uid`, `' . $this->tables['tiers'] . '`.`name`',
			$this->tables['filegroups'] . ' JOIN ' . $this->tables['tiers'] . ' ON `' . $this->tables['tiers'] . '`.`uid` = `' . $this->tables['filegroups']  . '`.`owner` ' . $this->cObj->enableFields($this->tables['tiers']),
			'1 ' . $this->cObj->enableFields($this->tables['filegroups']),
			'`' . $this->tables['filegroups']  . '`.`owner`',
			'`' . $this->tables['tiers'] . '`.`name` ASC'
		);
		return $rows;
	}

	/**
	 * Retrieves licences
	 *
	 * @return	mixed		Licences
	 */
	function getLicences() {
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, name',
			$this->tables['licences'],
			'1' .  $this->cObj->enableFields($this->tables['licences'])
		);
	}

	/**
	 * Get page bowser
	 *
	 * @param	int		$numberOfPages number of pages
	 * @return	void
	 */
	protected function getListGetPageBrowser($numberOfPages) {
		$conf = $this->conf['displayList.']['pagebrowse.'];
		// $conf += array(
			// 'pageParameterName' => $this->prefixId . '|page',
			// 'numberOfPages' => $numberOfPages,
		// );
		$conf2 = array(
			'pageParameterName' => $this->prefixId . '|page',
			'numberOfPages' => $numberOfPages,
		);
		
		$conf = array_merge($conf, $conf2);

		// Get page browser
		$cObj = t3lib_div::makeInstance('tslib_cObj');

		/* @var $cObj tslib_cObj */
		$cObj->start(array(), '');
		return $cObj->cObjGetSingle('USER', $conf);
	}

	/**
	 * Render RSS content
	 *
	 * @param	string		$rssLink: The RSS link
	 * @param	string		$imgSrc: The img resource
	 * @return	string		The RSS link content
	 */
	function renderRSS($rssLink, $imgSrc) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RSS###');
		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###URL###' => $rssLink,
			'###LINK_IMAGE###' => $imgSrc,
			'###LINK_ALT###' => htmlspecialchars($this->pi_getLL('rss_alt', 'rss', true)),
			'###LINK_TITLE###' => htmlspecialchars($this->pi_getLL('rss_title', 'rss', true)),
			'###LINK_TEXT###' => htmlspecialchars($this->pi_getLL('rss_text', 'rss feed', true)),
		);
		return $this->cObj->substituteMarkerArray($template, $markers);
	}

	/**
	 * @return	string		The additionalParams variable in getPageLink from tx_pagebrowse_pi1
	 * @author	GOYER Frederic <frederic.goyer@in-cite.net>
	 */
    function getExtraQueryString() {
        $gp = t3lib_div::_GP($this->prefixId);
        foreach($gp as $key => $val) {
            if(is_array($val)) {
                $ind = 0;
                foreach($val as $v)
                    if(!empty($v))
                        $str[] = $this->prefixId . '['. $key .'][' . $ind++ . ']=' . $v;
            }else
                if(!empty($val) && $key != 'page')
                    $str[] = $this->prefixId . '['. $key .']=' . $val;
        }
        $str = '&' . implode('&', $str);
        return $str;
    }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_datastore/pi1/class.tx_icsoddatastore_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_datastore/pi1/class.tx_icsoddatastore_pi1.php']);
}

?>