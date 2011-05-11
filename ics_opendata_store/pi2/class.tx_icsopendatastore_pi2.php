<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Popy (popy.dev@gmail.com)
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
 * 'pp_rsslatestcontent' extension.
 *
 * @author	Popy <popy.dev@gmail.com>
 *
 * Adapted by In Cité Solution <technique@in-cite.net> for plugin 'Datastore RSS' for the 'ics_opendata_store' extension.
 *
 */



require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_icsopendatastore_pi2 extends tslib_pibase{
	var $prefixId      = 'tx_icsopendatastore_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_icsopendatastore_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_opendata_store';	// The extension key.

	/**
	 * Render datastore rss feed
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The	rss feed content that is displayed on the website
	 */
	function renderSingleRssPage($content,$conf) 	{
		$this->conf = $conf;

		/* Get the template */
		$this->templateFile = $this->conf['templateFile'];
		if (!$this->templateFile)
			$this->templateFile = 'typo3conf/ext/ics_opendata_store/res/rss2_tmplFile.tmpl';

		/* Declarations */
		$rssId = intval(t3lib_div::_GP('rssFeed'));
		$this->config = $GLOBALS['TSFE']->config['config']['datastore_rss.'];
		$this->feed = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_icsopendatastore_filegroups',
			'1' . t3lib_BEfunc::deleteClause('tx_icsopendatastore_filegroups') . t3lib_BEfunc::BEenableFields('tx_icsopendatastore_filegroups'),
			'',
			'tstamp DESC',
			'10'
		);

		$pubDate = $this->feed[0]['tstamp'];

		$this->siteUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		$this->charset = trim($GLOBALS['TSFE']->config['config']['renderCharset'])? $GLOBALS['TSFE']->config['config']['renderCharset'] : $GLOBALS['TSFE']->config['config']['metaCharset'];

		$tmpl = $this->cObj->fileResource($this->templateFile);
		$template['TEMPLATE_RSS2'] = $this->cObj->getSubpart($tmpl, '###TEMPLATE_RSS2###');
		$markers = array();

		/* RSS feed header */
		$template['HEADER'] = $this->cObj->getSubpart($template['TEMPLATE_RSS2'], '###HEADER###');
		$markers = array(
			'###RSS_DECLARATION###' => ($this->conf['displayRSS.']['rssDeclaration'])? $this->conf['displayRSS.']['rssDeclaration'] : '<rss version="2.0">',
			'###SITE_TITLE###' => htmlspecialchars($GLOBALS['TSFE']->tmpl->setup['sitetitle']),
			'###SITE_LINK###' => htmlspecialchars($this->siteUrl),
			'###SITE_DESCRIPTION###' => ($this->conf['displayRSS.']['description'])? htmlspecialchars($this->conf['displayRSS.']['description']) : '',
			'###LANGUAGE###' => ($this->conf['displayRSS.']['language'])? '<language>' . htmlspecialchars($this->conf['displayRSS.']['language']) . '</language>' : '',
			'###COPYRIGHT###' => ($this->conf['displayRSS.']['copyright'])? '<copyright>' . htmlspecialchars($this->conf['displayRSS.']['copyright']) . '</copyright>' : '',
			'###MANAGINGEDITOR###' => ($this->conf['displayRSS.']['managingeditor'])? '<managingEditor>' . htmlspecialchars($this->conf['displayRSS.']['managingeditor']) . '</managingEditor>' : '',
			'###WEBMASTER###' => ($this->conf['displayRSS.']['webMaster'])?  '<webMaster>' . htmlspecialchars($this->conf['displayRSS.']['webMaster']) . '</webMaster>' : '',
			'###PUBDATE###' => '<pubDate>' . htmlspecialchars(date('r', $pubDate)) . '</pubDate>',
			'###LASTBUILDDATE###' => '',
			'###CATEGORY###' => '',
			'###GENERATOR###' => ($this->conf['displayRSS.']['generator'])? '<generator>' . htmlspecialchars($this->conf['displayRSS.']['generator']) . '</generator>' : '<generator>TYPO3 - get.content.right</generator>',
			'###DOCS###' => ($this->conf['displayRSS.']['docs'])? '<docs>' . htmlspecialchars($this->conf['displayRSS.']['docs']) . '</docs>' : '<docs>http://blogs.law.harvard.edu/tech/rss</docs>',
			'###CLOUD###' => ($this->conf['displayRSS.']['cloud'])? $this->conf['displayRSS.']['cloud'] : '', // like <cloud domain="rpc.sys.com" port="80" path="/RPC2" registerProcedure="myCloud.rssPleaseNotify" protocol="xml-rpc" />
			'###TTL###' => ($this->conf['displayRSS.']['ttl'])?  '<ttl>' . htmlspecialchars($this->conf['displayRSS.']['ttl']) . '</ttl>' : '',
			'###RATING###' => ($this->conf['displayRSS.']['rating'])?  '<rating>' . htmlspecialchars($this->conf['displayRSS.']['rating']) . '</rating>' : '',
			'###SKIPHOURS###' => '',
			'###SKIPDAYS###' => '',
		);

		/* RSS feed header's image */
		$template['IMAGE'] = '';
		if ($this->config['siteLogo'] || $this->config['siteLogo.']) {
			$imgPath='';
			if ($this->config['siteLogo.']['relativeUrl']) {
				$imgPath=$this->siteUrl;
			}
			$imgPath.=$this->cObj->stdWrap(
				$this->config['siteLogo'],
				$this->config['siteLogo.']
				);

			$template['IMAGE'] = $this->cObj->getSubpart($template['TEMPLATE_RSS2'], '###IMAGE###');
			$markersImage = array(
				'###SITE_TITLE###' => htmlspecialchars($GLOBALS['TSFE']->tmpl->setup['sitetitle']),
				'###IMGPATH###' => htmlspecialchars($imgPath),
				'###SITE_LINK###' => htmlspecialchars($this->siteUrl),
				'###IMG_WIDTH###' => '',
				'###IMG_HEIGHT###' => '',
				'###SITE_DESCRIPTION###' => ($this->conf['displayRSS.']['description'])? htmlspecialchars($this->conf['displayRSS.']['description']) : '',
			);
			$template['IMAGE'] = $this->cObj->substituteMarkerArray($template['IMAGE'], $markersImage);
		}
		$template['HEADER'] = $this->cObj->substituteSubpart($template['HEADER'], '###IMAGE###', $template['IMAGE']);


		/* RSS feed header's textInput */
		$template['TEXTINPUT'] = '';
		if ($this->conf['displayRSS.']['textInput.'])	{
			$template['TEXTINPUT'] = $this->cObj->getSubpart($template['TEMPLATE_RSS2'], '###TEXTINPUT###');
			$markersTextInput = array(
				'###TEXTINPUT_TITLE###' => htmlspecialchars($this->conf['displayRSS.']['textInput.']['title']),
				'###TEXTINPUT_DESCRIPTION###' => htmlspecialchars($this->conf['displayRSS.']['textInput.']['description']),
				'###TEXTINPUT_NAME###' => htmlspecialchars($this->conf['displayRSS.']['textInput.']['name']),
				'###TEXTINPUT_LINK###' => htmlspecialchars($this->conf['displayRSS.']['textInput.']['link']),
			);
			$template['TEXTINPUT'] =  $this->cObj->substituteMarkerArray($template['TEXTINPUT'], $markersTextInput);
		}
		$template['HEADER'] = $this->cObj->substituteSubpart($template['HEADER'], '###TEXTINPUT###', $template['TEXTINPUT']);

		/* RSS feed header skipHours & skipDays */
		if ($this->conf['displayRSS.']['skipHours.'])	{
			$skipHours = t3lib_div::trimExplode(',', $this->conf['displayRSS.']['skipHours.'], true);
			foreach ($skipHours as $hour)	{
				$skipHours_content .= '<hour>' . htmlspecialchars($hour) . '</hour>';
			}
			$markers['###SKIPHOURS###'] = $skipHours_content;
		}
		if ($this->conf['displayRSS.']['skipDays.'])	{
			$skipDays = t3lib_div::trimExplode(',', $this->conf['displayRSS.']['skipDays.'], true);
			foreach ($skipDays as $day)	{
				$skipDays_content .= '<day>' . htmlspecialchars($day) . '</day>';
			}
			$markers['###SKIPDAYS###'] = $skipDays_content;
		}

		$template['HEADER'] = $this->cObj->substituteMarkerArray($template['HEADER'], $markers);

		/* RSS feed content */
		$template['CONTENT'] = $this->cObj->getSubpart($template['TEMPLATE_RSS2'], '###CONTENT###');
		$template['DATASET'] = $this->cObj->getSubpart($template['CONTENT'], '###DATASET###');
		$datasets = '';
		foreach ($this->feed as $item) {
			$linkArray = array();
			if ($this->conf['displayRSS.']['items.']['link.'])	{
				foreach ($this->conf['displayRSS.']['items.']['link.'] as $key=>$param)	{
					if ($key == 'item.')
						$linkArray[$param['key']] = $item['uid'];
					else
						$linkArray[$param['key']] = $param['value'];
				}
			}
			$author = $this->getDatastoreAuthor($item['creator']);
			$categories = $this->getDatastoreCategories(t3lib_div::trimExplode(',', $item['categories'], true));
			$markersDataset = array(
				'###DATASET_TITLE###' => htmlspecialchars($item['title']),
				'###DATASET_LINK###' => htmlspecialchars(t3lib_div::linkThisUrl($this->siteUrl, $linkArray)),
				'###DATASET_DESCRIPTION###' => htmlspecialchars($this->utf8_csConv($item['description'])),
				'###DATASET_AUTHOR###' => $author? '<author>' .htmlspecialchars($this->utf8_csConv($author)) . '</author>' : '',
				'###DATASET_CATEGORY###' => (!empty($categories) && is_array($categories))? '<category>' . htmlspecialchars($this->utf8_csConv(implode(',', $categories))) . '</category>' : '',
				'###DATASET_COMMENTS###' => '',
				'###DATASET_ENCLOSURE###' => '',
				'###DATASET_GUID###' => '',
				'###DATASET_PUBDATE###' => '<pubDate>' . htmlspecialchars(date('r', $item['tstamp'])) . '</pubDate>' ,
				'###DATASET_SOURCE###' => '',
			);
			$datasets .= $this->cObj->substituteMarkerArray($template['DATASET'], $markersDataset);
		}
		$template['CONTENT'] = $this->cObj->substituteSubpart($template['CONTENT'], '###DATASET###', $datasets);

		//--
		return $this->cObj->substituteMarkerArrayCached(
			$template['TEMPLATE_RSS2'],
			$markers,
			array(
				'###HEADER###' => $template['HEADER'],
				'###CONTENT###' => $template['CONTENT'],
			)
		);
	}

	/**
	 * Retrieves datastore dataset's author
	 *
	 * @param	int		$uid: The author uid
	 * @return	string		The author's name
	 */
	function getDatastoreAuthor($uid)	{
		$author = t3lib_BEfunc::getrecord(
			'tx_icsopendatastore_tiers',
			$uid,
			'name',
			t3lib_BEfunc::BEenableFields('tx_icsopendatastore_tiers')
		);
		return $author['name'];
	}

	/**
	 * Retrieves datastore dataset's categories
	 *
	 * @param	array		$uids: Categories uid
	 * @return	array		Categories name
	 */
	function getDatastoreCategories($uids)	{
		$categories = array();
		foreach ($uids as $uid)	{
			$category = t3lib_BEfunc::getrecord(
				'tx_icsopendatastore_categories',
				$uid,
				'name',
				t3lib_BEfunc::BEenableFields('tx_icsopendatastore_categories')
			);
			$categories[] = $category['name'];
		}
		return $categories;
	}

	/**
	 * Convert from utf-8 to charset
	 *
	 * @param	string		$content: The content to convert
	 * @return	The		content converted
	 */
	function utf8_csConv($content)	{
		if (strtoupper($this->charset) != 'UTF-8')
			return $GLOBALS['TSFE']->csConvObj->conv($content, 'utf-8', $this->charset);
		return $content;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata_store/class.tx_icsopendatastore_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata_store/class.tx_icsopendatastore_pi2.php']);
}

?>