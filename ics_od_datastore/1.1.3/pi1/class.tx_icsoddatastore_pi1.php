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
 *   74: class tx_icsoddatastore_pi1 extends tslib_pibase
 *
 *              SECTION: < Default search criteria 
 *  100:     function main($content, $conf)
 *  150:     function controlVars(&$content)
 *  164:     function init()
 *  281:     function renderSearch()
 *  319:     function renderFileformatItems($template, $aFileformats)
 *  351:     function renderTiersItems($template, $aTiers)
 *  376:     function renderList()
 *  401:     function renderListHeader($template)
 *  432:     function renderListRows($template)
 *  555:     function renderListRow($template, $row)
 *  619:     function renderFiles($view, $filegroup, $template)
 *  710:     function renderSingle($id)
 *  809:     function getImgResource($resource, $desc, $width = 62, $height = 20, $external = false)
 *  831:     function getFiles_mm($filegroup)
 *  849:     function getFileSize($file)
 *  864:     function getFileformats($searchable = false)
 *  887:     function getFiletypes()
 *  904:     function getTiersAgencies()
 *  922:     protected function getListGetPageBrowser($numberOfPages)
 *  944:     function renderRSS($rssLink, $imgSrc)
 *
 * TOTAL FUNCTIONS: 20
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
					case 'SOLR':
						if (!isset($this->piVars['uid'])) {
							$content .= $this->renderSolr();
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
			$this->conf['sorting.']['name'] = 'tstamp';
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
	 * Render the search view
	 *
	 * @return	string		$content The search view content
	 */
	function renderSearch() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH###');
		$locMarkers = array('###SELECTED_CRITERIA###' => $this->renderSelectedCriteria());
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers);
		
		$markers = array(
			'###FORM_ACTION###' => '#',
			'###PREFIXID###' => $this->prefixId,
			'###KEYWORDS_LABEL###' => htmlspecialchars($this->pi_getLL('search_keywords', 'Keywords', true)),
			'###KEYWORDS_VALUE###' => htmlspecialchars($this->piVars['keywords']),
			'###SEARCHBUTTON_VALUE###' => htmlspecialchars($this->pi_getLL('search_submit', 'Submit', true)),
			'###FORM_ACTION###' => $this->pi_getPageLink($this->conf['resultsSearchPid']),
			'###TITLE_TIERS###' => htmlspecialchars($this->pi_getLL('search_tiersTitle', 'Tiers', true)),
			'###TITLE_FILEFORMAT###' => htmlspecialchars($this->pi_getLL('search_fileformatTitle', 'File format', true)),
		);

		$fileformatItems = $this->renderFileformatItems($template, $this->getFileformats(true));
		$tiersItems = $this->renderTiersItems($template, $this->getTiersAgencies());

		$subpartArray = array();
		$subpartArray['###FILEFORMAT_ITEM###'] = $fileformatItems;
		$subpartArray['###TIERS_ITEM###'] = $tiersItems;

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
	
	function renderSelectedCriteria() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SELECTED_CRITERIA###');
		
		$tiers = '';
		if (is_array($this->list_criteria['tiers']) && !empty($this->list_criteria['tiers'])) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'name',
				$this->tables['tiers'],
				'1' .  $this->cObj->enableFields($this->tables['tiers']) .  ' AND uid in (' . implode(',', $this->list_criteria['tiers']) . ')',
				'',
				'name',
				'',
				'name'
			);
			$tiers = array_keys($rows);
		}
		
		$formats = '';
		if (is_array($this->list_criteria['fileformat']) && !empty($this->list_criteria['fileformat'])) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'name',
				$this->tables['fileformats'],
				'1' .  $this->cObj->enableFields($this->tables['fileformats']) .  ' AND uid in (' . implode(',', $this->list_criteria['fileformat']) . ')',
				'',
				'name',
				'',
				'name'
			);
			$formats = array_keys($rows);
		}
		
		$markers = array(
			'###SC_TITLE###' => $this->pi_getLL('selectedCriteria', 'Selected Criteria', true),
			'###SC_KEYWORDS_LABEL###' => $this->pi_getLL('sc_keywords', 'Keywords', true),
			'###SC_KEYWORDS_VALUE###' => $this->list_criteria['keywords'],
			'###SC_TIERS_LABEL###' => $this->pi_getLL('sc_tiers', 'Tiers', true),
			'###SC_TIERS_VALUE###' => $this->cObj->stdWrap(implode(',', $tiers), $this->conf['displaySearch.']['tiers.']),
			'###SC_FORMATS_LABEL###' => $this->pi_getLL('sc_formats', 'Formats', true),
			'###SC_FORMATS_VALUE###' => $this->cObj->stdWrap(implode(',', $formats), $this->conf['displaySearch.']['formats.']),
		);
		$subpartArray = array();
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
				if (is_array($this->piVars['fileformat']) && t3lib_div::inArray($this->piVars['fileformat'],$fileformat['uid'])) {
					$markers['###CHECKED###'] = 'checked';
				}
				$fileformatItem = $this->cObj->getSubpart($template, '###FILEFORMAT_ITEM###');
				$item .= $this->cObj->substituteMarkerArray($fileformatItem, $markers);
			}
		}
		return $item;
	}

	/**
	 * Render tiers
	 *
	 * @param	string		$template: The template for tiers
	 * @param	array		$aTiers: The tiers
	 * @return	$item
	 */
	function renderTiersItems($template, $aTiers) {
		if (is_array($aTiers) && count($aTiers)) {
			$agencies = t3lib_div::trimExplode(',', $this->conf['select.']['agencies'], true);
			foreach ($aTiers as $tiers) {
				$markers = array(
					'###PREFIXID###' => $this->prefixId,
					'###TIERS_LABEL###' => htmlspecialchars($tiers['name']),
					'###TIERS_VALUE###' => htmlspecialchars($tiers['uid']),
					'###CHECKED###' => in_array($tiers['uid'], $agencies)? 'checked = "checked"': '',
				);
				if (is_array($this->piVars['tiers']) && t3lib_div::inArray($this->piVars['tiers'],$tiers['uid'])) {
					$markers['###CHECKED###'] = 'checked';
				}
				$tiersItem = $this->cObj->getSubpart($template, '###TIERS_ITEM###');
				$item .= $this->cObj->substituteMarkerArray($tiersItem, $markers);
			}
		}
		return $item;
	}

	/**
	 * Render solr view
	 *
	 * @return	$content
	 */
	function renderSolr()
	{
		$content ='';
		
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SOLR###');
		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###H3_TITLE###' => $this->pi_getLL('Precise your research:', 'Affiner votre recherche part : ', true),
			'###SUBMIT_VALUE###' => $this->pi_getLL('Submit', 'Valider', true),
			'###DATA_SUGGESTION###' => $this->pi_getLL('Suggest a new dataset', 'Suggérer un nouveau jeu de données', true),
			'###KEYWORDS_LABEL###' => $this->pi_getLL('Keywords:', 'Mots clés : ', true),
			'###SORT_LABEL###' => $this->pi_getLL('Sort by:', 'Trier par : ', true),
			'###SORT_PERTINENCE_LABEL###' => $this->pi_getLL('pertinence', 'pertinence', true),
			'###SORT_RANK_LABEL###' => $this->pi_getLL('rank', 'note', true),
			'###SORT_DATE_LABEL###' => $this->pi_getLL('date', 'date de publication / mise à jour', true),
		);
		
		
		
		$request = "*:*";	//default request, return all results
		$currentGetParam =t3lib_div::_GP('tx_icsoddatastore_pi1');
		if (isset($currentGetParam[page]))
		{
// 			t3lib_div::debug($this->list_criteriaNav);
			$first_item_place = $currentGetParam[page] * $this->nbFileGroupByPage;
		}
		else 
		{
			$first_item_place = 0;
		}
		
		$serializedResult = file_get_contents('http://localhost:8983/solr/select?q='.$request.'&start=' . $first_item_place . '&rows='.$this->nbFileGroupByPage.'&wt=phps');
		$result = unserialize($serializedResult);
		
		$this->nbFileGroup = $result[response][numFound];
		$markers['###PAGE_BROWSER###'] = $this->getListGetPageBrowser(intval(ceil($this->nbFileGroup/$this->nbFileGroupByPage)));

		$last_item_place = $first_item_place + $this->nbFileGroupByPage;
		$last_item_place = min($last_item_place, $this->nbFileGroup);
		$markers['###RESULTS_RANGE###'] = ($first_item_place + 1) . ' ' . $this->pi_getLL('to', 'à', true) . ' ' . ($last_item_place + 1) . ' ' . $this->pi_getLL('on', 'sur', true) . ' ' . ($this->nbFileGroup + 1) . ' ' . $this->pi_getLL('results', 'résultats', true) . ' ';
		
		$subpartArray = array();
		if ($result[response][numFound] > 0)
		{
			$subpartArray['###RESULT_LIST###'] = $this->renderSolrItems($template, $result);
		}
		else 
		{
			$subpartArray['###RESULT_LIST###'] = "";
		}
		
		/*$headerItems = $this->renderListHeader($this->cObj->getSubpart($template, '###HEADER_ITEM###'));
		$rowItems = $this->renderListRows($template);

		$subpartArray = array();
		$subpartArray['###HEADER_ITEM###'] = $headerItems;
		$subpartArray['###GROUP_ROW_CONTENT###'] = $rowItems;
		*/
		
		// Hook for add fields markers to solr view
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSolrFieldsMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSolrFieldsMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalSolrFieldsMarkers($markers, $subpartArray, $template, $this);
			}
		}
		$content .= $this->cObj->substituteMarkerArrayCached($template, $markers, $subpartArray);
		return $content;
	}
	
	
	
	/**
	 * Render solr items list
	 *
 	 * @param	string		$template	The template of the view
	 * @param	array		$result		The result from solr
	 * @return	$content
	 */
	function renderSolrItems($template, $result)
	{
		$content ='';
			foreach ($result[response][docs] as $document)
			{
				$templateItem = $this->cObj->getSubpart($template, '###RESULT_LIST_ITEM###');

				$markers = array(
					'###RANK###' => '',
					'###DATE_LABEL###' => $this->pi_getLL('Published:', 'Publié le : ', true),
					'###CATEGORIE_LABEL###' => $this->pi_getLL('Categories:', 'Thématiques : ', true),
					'###MANAGER_LABEL###' => $this->pi_getLL('Manager:', 'Gestionnaire : ', true),
					'###OWNER_LABEL###' => $this->pi_getLL('Owner:', 'Propriétaire : ', true),
					'###DESCRIPTION###' => $document[description],
					'###TITLE###' => $document[title],
					'###CATEGORIE###' => implode(', ',array_slice($document[categories],1)),
					'###MANAGER###' => $document[manager],
					'###OWNER###' => $document[owner],
				);
					 
				$url_to_detail_page = $this->pi_getPageLink(
					$this->conf['singlePid'],
					'',
					array_merge(
						$this->list_criteriaNav,
						array(
							$this->prefixId . '[uid]' => $document[id],
						)
					)
				);
				
				$markers['###URL###'] = $url_to_detail_page;
				$markers['###DETAILS_LINK###'] = '<a href="'.$url_to_detail_page.'">'.$this->pi_getLL('Go to details card', 'Consulter la fiche détaillée', true).'</a>';
				
				if (isset($document[html_from_csv]) && $document[html_from_csv])
				{
					$url_to_visualization_page = $this->pi_getPageLink(
						$this->conf['singlePid'],
						'',
						array_merge(
							$this->list_criteriaNav,
							array(
								$this->prefixId . '[uid]' => $document[id],
								'visualization' => '1',
							)
						)
					);
					$markers['###VISUALIZATION###'] = '<div class="background"><span class="green_circle"></span><a href="'.$url_to_visualization_page.'">' . $this->pi_getLL('Visualize', 'Visualiser le jeu de données', true) . '</a></div>';
				}
				else
				{
					$markers['###VISUALIZATION###'] = '';
				}
				$release_date = new DateTime($document[release_date]);
				$markers['###DATE###'] = $release_date->format('d/m/Y');
				
				$picto_part = '';
				foreach ($document[files_types_picto] as $file_type_picto)
				{
					$picto_part .= '<img src="'.$this->conf['displaySolr.']['pictoBaseURL'] . $file_type_picto . '" width="36" height="13" alt="' . substr($file_type_picto, 0, 3) . '">';
				}
				
				$markers['###PICTO###'] = $picto_part;
				
				
				$content .= $this->cObj->substituteMarkerArrayCached($templateItem, $markers, $subpartArray);
				
			}
		
		return $content;
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
		$subpartArray['###HEADER_ITEM###'] = $headerItems;
		$subpartArray['###GROUP_ROW_CONTENT###'] = $rowItems;
		
		// Hook for add fields markers to list view
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListFieldsMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListFieldsMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalListFieldsMarkers($markers, $subpartArray, $template, $this);
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

		if ($this->conf['sorting.']['name']) {
			if (($this->conf['sorting.']['name'] == 'tstamp') && $this->conf['displayList.']['sort.']['tstamp.']['day']) {
				// Group dataset by day: retrieves dataset order by day, dataset updated or created the same day are sorted by title
				$orderBy = 'FROM_UNIXTIME(`' . $this->tables['filegroups'] . '`.`tstamp`, "%Y%m%d") ' . $this->conf['sorting.']['order'] . ', `' . $this->tables['filegroups'] . '`.`title` ASC';
			} else {
				$orderBy = '`' . $this->tables['filegroups'] . '`.`' . $this->conf['sorting.']['name'] . '` ' . $this->conf['sorting.']['order'];
			}
		}
		// Set where clause with junture
		if (isset($this->list_criteria['keywords']) && !empty($this->list_criteria['keywords'])) {
			$whereClause .= ' AND (
				LOCATE("' . strtoupper($this->list_criteria['keywords']) . '", UPPER(`'.$this->tables['filegroups'].'`.`title`))
				OR LOCATE("' . strtoupper($this->list_criteria['keywords']) . '", UPPER(`'.$this->tables['filegroups'].'`.`description`)))
			';
		}
		if ((isset($this->list_criteria['tiers']) && count($this->list_criteria['tiers'])) || $this->conf['select.']['agencies']) {
			$agencies = array();
			if (is_array($this->list_criteria['tiers']))
				$agencies = $this->list_criteria['tiers'];
			if ($select_agencies = t3lib_div::trimExplode(',', $this->conf['select.']['agencies'], true))
				$agencies = array_merge($agencies, $select_agencies);
			if (!empty($agencies))
				$whereClause .= ' AND ( `' . $this->tables['filegroups'] . '`.`agency` IN (' . implode(',', $agencies) . '))';
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
		if ( in_array($this->list_criteria['sort']['column'], $this->listFields) && ($this->list_criteria['sort']['column'] != 'files') ) {
			$tiers = array(
				'agency',
				'contact',
				'publisher',
				'creator',
				'manager',
				'owner',
			);
			$order = ($this->list_criteria['sort']['order'])? $this->list_criteria['sort']['order']: 'ASC';
			if ( ($this->list_criteria['sort']['column'] == 'tstamp') && ($this->conf['displayList.']['sort.']['tstamp.']['day']) ) {
				$orderBy = 'FROM_UNIXTIME(`' . $this->tables['filegroups'] . '`.`' . $this->list_criteria['sort']['column'] . '`, "%Y%m%d") ' . $order;
			}	elseif ( in_array($this->list_criteria['sort']['column'], $tiers) ) {
				$queryJoin .= ' LEFT OUTER JOIN `' . $this->tables['tiers'] . '` ON `' . $this->tables['tiers'] . '`.`uid` = `' . $this->tables['filegroups'] . '`.`' . $this->list_criteria['sort']['column'] . '`';
				$orderBy = '`' . $this->tables['tiers'] . '`.`name` ' . $order;
			}	else	{
				$orderBy = '`' . $this->tables['filegroups'] . '`.`' . $this->list_criteria['sort']['column'] . '` ' . $order;
			}

			if ( $this->list_criteria['sort']['column'] != 'title')
				$orderBy .= ', `' . $this->tables['filegroups'] . '`.`title` ASC';
		}

		if ( empty($this->piVars['page']) ) {
			$start = 0;
		}	else	{
			$start = intval($this->piVars['page']) * $this->nbFileGroupByPage;
		}
		$fields = $this->listFields;
		foreach ($fields as $idx=>$field) {
			$fields[$idx] = '`' . $this->tables['filegroups'] . '`.`' . $field . '` as ' . $field;
		}
		$filegroups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
			'DISTINCT `' . $this->tables['filegroups'] . '`.`uid`, ' . implode(',', $fields),
			$this->tables['filegroups'] . $queryJoin,
			'`' . $this->tables['filegroups'] . '`.`pid` = ' . $this->storage . $whereClause  . $this->cObj->enableFields($this->tables['filegroups']),
			'',
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
						$this->prefixId . '[returnID]' => $GLOBALS['TSFE']->id,
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
			$files = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				implode(', ', $fields),
				'`' . $this->tables['filegroups'] . '`' .
				' LEFT OUTER JOIN `' . $this->tables['file_filegroup_mm'] . '` ON `' . $this->tables['file_filegroup_mm'] . '`.`uid_foreign` = `' . $this->tables['filegroups'] . '`.`uid`' .
				' JOIN `' . $this->tables['files'] . '` ON `' . $this->tables['files'] . '`.`uid` = `' . $this->tables['file_filegroup_mm'] . '`.`uid_local`' . $this->cObj->enableFields($this->tables['files']),
				implode(' AND ', $where) .$this->cObj->enableFields($this->tables['filegroups'])
			);
			$pictoItems = '';
			foreach ($files as $file) {
				$markers = array(
					'###FILESIZE###' => '',
					'###FILEMD5###' => '',
				);
				if ($file['record_type'] == 0) {
					$markers['###FILESIZE###'] = $this->getFileSize($uploadPaths['file'] . $file['file']);
					$markers['###FILEMD5###'] = md5_file($uploadPaths['file'] . $file['file']);
				} else {
					$markers['###FILEMD5###'] = md5_file($file['url']);
				}
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
				}
				$cObj = t3lib_div::makeInstance('tslib_cObj');
				$cObj->start($file, $this->tables['files']);
				$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
				$markers['###PICTO###'] =  $cObj->stdWrap('', $this->conf['datasetFile.']);
				$markers['###FILEEXT###'] = htmlspecialchars($format['name']);

				$pictoItem = $this->cObj->substituteMarkerArray($this->cObj->getSubpart($template, '###PICTO_ITEM###'), $markers);
				$pictoItems .= $pictoItem;
			}
			$sectionContent = $this->cObj->substituteSubpart($template, '###PICTO_ITEM###', $pictoItems);
			if (!empty($files))
				$content .= $this->cObj->substituteMarkerArray($sectionContent, array('###SECTION_NAME###' => $type['name']));
		}
		return $content;
	}

	/**
	 * Render single view
	 *
	 * @param	int		$id: filegroup uid
	 * @return	string		content
	 */
	function renderSingle($id) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE###');

		$filegroups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'`' . $this->tables['filegroups'] . '`',
			'`uid` = ' . $id . $this->cObj->enableFields($this->tables['filegroups'])
		);
		$filegroup = $filegroups[0];

		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###INTRO###' => $this->cObj->stdWrap($this->pi_getLL('detail_intro', 'Introduction', true), $this->conf['displaySingle.']['intro_stdWrap.']),
			'###FORMAT###' => $this->cObj->stdWrap($this->pi_getLL('detail_formatavailable', 'Available format', true), $this->conf['displaySingle.']['formatavaillable_stdWrap.']),
			'###BACKLINK###' => $this->pi_linkTP($this->pi_getLL('back'), $this->list_criteriaNav, 0, $this->conf['searchPid']),
			'###OTHER_DATA###' =>  $this->cObj->stdWrap($this->pi_getLL('detail_other_data', 'Other data', true), $this->conf['displaySingle.']['other_data_stdWrap.']),
			'###UPDATE_TITLE###' => $this->cObj->stdWrap($this->pi_getLL('detail_update_title', 'Updates', true), $this->conf['displaySingle.']['update_title_stdWrap.']),
			'###INSTITUTION_TITLE###' =>  $this->cObj->stdWrap($this->pi_getLL('detail_institution_title', 'Institutions', true), $this->conf['displaySingle.']['institution_title_stdWrap.']),
				
			);
		
		if (t3lib_div::_GP('visualization') === '1')
		{
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_VISUALIZATION###');
			$markers['###BACKLINK###'] = $this->pi_linkTP($this->pi_getLL('back_to_detail_view'), $this->list_criteriaNav, 0, $this->conf['searchPid']);
			$markers['###BACKLINKLABEL###'] = $this->pi_getLL('back_to_detail_view');
			$template = $this->cObj->substituteSubpart($template, '###HTML_FROM_CSV_DISPLAY###', $filegroup['html_from_csv_display']);
		}
		else
		{
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE###');
		}
		
		
		if (isset($filegroup['html_from_csv_display']) && $filegroup['html_from_csv_display'] !== '')
		{
			$url_visualization = $this->pi_getPageLink(
				$this->conf['singlePid'],
				'',
				array_merge(
					$this->list_criteriaNav,
					array(
						$this->prefixId . '[uid]' => $id,
						'visualization' => '1',
					)
				)
			);
			$markers['###DYNAMIC_DISPLAY_LINK###'] = $this->cObj->stdWrap('<a title="'.$this->pi_getLL('groupfile_dynamic_display').'" href="'.$url_visualization.'">'.$this->pi_getLL('groupfile_dynamic_display').'</a>', $this->conf['displaySingle.']['dynamic_display_link_stdWrap.']);
		} 
		else
		{
			$markers['###DYNAMIC_DISPLAY_LINK###'] = '';
		}

		
		foreach ($this->detailFields as $field) {
			$markers['###' . strtoupper($field) . '_LABEL###'] = $this->cObj->stdWrap($this->pi_getLL('detail_' . $field, $field, true), $this->conf['displaySingle.'][$field . '_label_stdWrap.']);

			/** Ne pas afficher les champs non remplis **/
			$tmp_subpart = '';
			$tmp_subpart = $this->cObj->getSubpart($template, '###SUBPART_'. strtoupper($field) .'###');
			if (!$filegroup[$field])
				$tmp_subpart = '';

			$template = $this->cObj->substituteSubpart($template, '###SUBPART_'. strtoupper($field) .'###', $tmp_subpart);

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
		$filesContent = $this->renderFiles('SINGLE', $id, $this->cObj->getSubpart($template, '###SECTION_FILE###'));
		$template = $this->cObj->substituteSubpart($template, '###SECTION_FILE###', $filesContent);

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
	function getFileSize($file) {
		if (filesize($file)>(1024*1024))
			return round(filesize($file)/(1024*1024),1) . ' M';
		if (filesize($file)>(1024))
			return round(filesize($file)/(1024)) . ' kb';
		else
			return round(filesize($file)) . ' octets';
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
	 * Get page bowser
	 *
	 * @param	int		$numberOfPages number of pages
	 * @return	void
	 */
	protected function getListGetPageBrowser($numberOfPages) {
		$conf = $this->conf['displayList.']['pagebrowse.'] ? $this->conf['displayList.']['pagebrowse.'] : $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_pagebrowse_pi1.'];
		$conf += array(
			'pageParameterName' => $this->prefixId . '|page',
			'numberOfPages' => $numberOfPages,
		);

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
     * @return    string        The additionalParams variable in getPageLink from tx_pagebrowse_pi1
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