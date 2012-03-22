<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 author name <author@mail.com>
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
 *   56: class tx_icsodappstore_getapplications_command extends tx_icsodcoreapi_command
 *  111:     function execute(array $params, XMLWriter $xmlwriter)
 *  209:     protected function transformResultsForOutput(array $applications)
 *  302:     protected function writeOutput(XMLWriter $xmlwriter, array $elements)
 *  416:     private function writeOutputScreenshots(XMLWriter $xmlwriter, array $elements)
 *  435:     private function writeOutputPlatforms(XMLWriter $xmlwriter, array $elements)
 *  454:     private function writeOutputCategories(XMLWriter $xmlwriter, array $elements)
 *  473:     private function writeOutputdatasets(XMLWriter $xmlwriter, array $elements)
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
/**
 * @file class.tx_icsodappstore_getapplications_command.php
 *
 * Short description of the class getapplications
 *
 * @author    author name <author@mail.com>
 * @package    TYPO3.ics_od_appstore
 */

require_once(t3lib_extMgm::extPath('ics_od_core_api') . 'api/class.tx_icsodcoreapi_command.php');

class tx_icsodappstore_getapplications_command extends tx_icsodcoreapi_command
{

	const EMPTY_IDS_CODE = 100;
	const EMPTY_IDS_TEXT = "ids should be not empty.";
	const INVALID_IDS_CODE = 101;
	const INVALID_IDS_TEXT = "The specified value is not valid for ids.";
	const EMPTY_PAGELIMIT_CODE = 102;
	const EMPTY_PAGELIMIT_TEXT = "pagelimit should be not empty.";
	const INVALID_PAGELIMIT_CODE = 103;
	const INVALID_PAGELIMIT_TEXT = "The specified value is not valid for pagelimit.";
	const EMPTY_PAGE_CODE = 104;
	const EMPTY_PAGE_TEXT = "page should be not empty.";
	const INVALID_PAGE_CODE = 105;
	const INVALID_PAGE_TEXT = "The specified value is not valid for page.";
	const EMPTY_SORT_CODE = 106;
	const EMPTY_SORT_TEXT = "sort should be not empty.";
	const INVALID_SORT_CODE = 107;
	const INVALID_SORT_TEXT = "The specified value is not valid for sort.";

	var $params = array(
		'pagelimit' => '50',
		'page' => '1',
		'sort' => 'title|asc',
	);

	// *************************
	// * User inclusions 0
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...
	var $pageLimitMax = 50;
	var $sortTypes = array(
		'TITLE',
		'TITLE|ASC',
		'TITLE|DESC',
		'RELEASE_DATE',
		'RELEASE_DATE|ASC',
		'RELEASE_DATE|DESC',
		'UPDATE_DATE',
		'UPDATE_DATE|ASC',
		'UPDATE_DATE|DESC',
	);
	// * End user inclusions 0



	/**
	 * Executes the command.
	 *
	 * @param	array		$params The command parameters.
	 * @param	XMLWriter		$xmlwriter The XML Writer for output.
	 * @return	[type]		...
	 */
	function execute(array $params, XMLWriter $xmlwriter)
	{
		$params = array_merge($this->params, $params);


		// *************************
		// * User inclusions 1
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...
		if (!$params['pagelimit'])
			$params['pagelimit'] = $this->pageLimitMax;
		if ($params['pagelimit']>$this->pageLimitMax || !is_numeric($params['pagelimit']))
		{
			makeError($xmlwriter, tx_icsodappstore_getapplications_command::INVALID_PAGELIMIT_CODE, tx_icsodappstore_getapplications_command::INVALID_PAGELIMIT_TEXT);
			return;
		}
		$params['pagelimit'] = intval($params['pagelimit']);

		if (!$params['page'] || $params['page']<=0)
			$params['page'] = 1;
		if (!is_numeric($params['page']))
		{
			makeError($xmlwriter, tx_icsodappstore_getapplications_command::INVALID_PAGE_CODE, tx_icsodappstore_getapplications_command::INVALID_PAGE_TEXT);
			return;
		}
		$params['page'] = intval($params['page']);

		if (!empty($params['sort']))
		{
			$sortParams = t3lib_div::trimExplode(',', $params['sort'], true);
			foreach ($sortParams as $sort)
			{
				if (!in_array(strtoupper($sort), $this->sortTypes))
				{
					makeError($xmlwriter, tx_icsodappstore_getapplications_command::INVALID_SORT_CODE, tx_icsodappstore_getapplications_command::INVALID_SORT_TEXT);
					return;
				}
			}
		}
		// * End user inclusions 1


		// Create a datasource object for retrieving applications
		$datasource = t3lib_div::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['datasource']['application']);

		$applications = array();

		if( !isset($params['ids']) ) {
			$applications = $datasource->getApplicationsAll($params);
		}
		else {
			$applications = $datasource->getApplicationsByIds($params);
		}

		// *************************
		// * User inclusions 2
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		$count = $applications['count'];
		$applications = $applications['records'];
		if (!is_array($applications))
			$applications = array();
		// * End user inclusions 2


		$elements = $this->transformResultsForOutput($applications);
		makeError($xmlwriter, SUCCESS_CODE, SUCCESS_TEXT);

		// *************************
		// * User inclusions 3
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		$pages = $count / $params['pagelimit'];
		$pages = is_float($pages)? intval($pages +1) : $pages;
		$elements = array(
			'count' => $count,
			'pages' => $pages,
			'page' =>  $params['page'],
			'limit' => $params['pagelimit'],
			'records' => $elements,
		);
		// * End user inclusions 3

		$this->writeOutput($xmlwriter, $elements);
	}

	/**
	 * Transforms results for output
	 *
	 * @param	array		$applications A collection of applications
	 * @return	Elements		array
	 */
	protected function transformResultsForOutput(array $applications)
	{
		// *************************
		// * User inclusions 4
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions 4

		$elements = array();
		foreach ($applications as $application)
		{
			$element = array();

			$element['id'] = $application['id'];
			$element['author'] = $application['author'];
			$element['title'] = $application['title'];
			$element['description'] = $application['description'];
			$element['release_date'] = (string)$application['release_date'];
			$element['logo'] = $application['logo'];
			$element['screenshot'] = $application['screenshot'];
			$element['link'] = $application['link'];
			$element['update_date'] = (string)$application['update_date'];
			$element['lock_publication'] = $application['lock_publication'];
			$element['publish'] = $application['publish'];
			$element['platforms'] = $application['platforms'];
			$element['tstamp'] = (string)$application['tstamp'];
			$element['crdate'] = (string)$application['crdate'];

			// *************************
			// * User inclusions 5
			// * DO NOT DELETE OR CHANGE THOSE COMMENTS
			// *************************

			// ... (Add additional operations here) ...

			// update_date
			if (!$application['update_date'])
				$element['update_date'] = date('c', $application['crdate']);
			unset($element['crdate']);
			unset($element['tstamp']);

			// Release_date
			$element['release_date'] = date('c', $application['release_date']);
			if (!$application['publish'] || $element['lock_publication'])
			{
				$element['release_date'] = null;
			}
			unset($element['lock_publication']);
			unset($element['publish']);

			// Screenshots
			$screenshots = t3lib_div::trimExplode(',', $application['screenshot'], 'true');
			$element['screenshots'] = $screenshots;
			unset($element['screenshot']);

			// Platforms
			$element['platforms'] = t3lib_div::trimExplode(',', $application['platforms'], true);

			// Categories
			$element['categories'] = t3lib_div::trimExplode(',', $application['categories'], true);

			// Datasets
			$element['datasets'] = t3lib_div::trimExplode(',', $application['datasets'], true);

			// * End user inclusions 5


			$elements[] = $element;
		}

		// *************************
		// * User inclusions 6
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions 6


		return $elements;
	}

	/**
	 * Writes output
	 *
	 * @param	XMLWriter		$xmlwriter the writer
	 * @param	array		$elements
	 * @return	void
	 */
	 protected function writeOutput(XMLWriter $xmlwriter, array $elements)
	{
		// *************************
		// * User inclusions 7
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...
		$count = $elements['count'];
		$pages = $elements['pages'];
		$page = $elements['page'];
		$limit = $elements['limit'];
		$elements = $elements['records'];
		// * End user inclusions 7

		$xmlwriter->startElement('data');
		// *************************
		// * User inclusions 8
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...
		if ($page && $limit)
		{
			$xmlwriter->writeAttribute('items', $count);
			$xmlwriter->writeAttribute('pages', $pages);
			$xmlwriter->writeAttribute('limit', $limit);
		}
		// * End user inclusions 8

		foreach ($elements as $element)
		{
			$xmlwriter->startElement('application');
			// *************************
			// * User inclusions 9
			// * DO NOT DELETE OR CHANGE THOSE COMMENTS
			// *************************

			// ... (Add additional operations here) ...

			// * End user inclusions 9

			foreach ($element as $key => $value)
			{
				// *************************
				// * User inclusions 10
				// * DO NOT DELETE OR CHANGE THOSE COMMENTS
				// *************************

				// ... (Add additional operations here) ...
				if (false)
				{
				// * End user inclusions 10

				$xmlwriter->startElement($key);
				$xmlwriter->text($value);
				$xmlwriter->endElement();
				// *************************
				// * User inclusions 11
				// * DO NOT DELETE OR CHANGE THOSE COMMENTS
				// *************************

				// ... (Add additional operations here) ...
				}

				switch ($key)
				{
					case 'screenshots':
						$this->writeOutputScreenshots($xmlwriter, $value);
						break;
					case 'categories':
						$this->writeOutputCategories($xmlwriter, $value);
						break;
					case 'datasets':
						$this->writeOutputDatasets($xmlwriter, $value);
						break;
					case 'platforms':
						$this->writeOutputPlatforms($xmlwriter, $value);
						break;
					default :
						$xmlwriter->startElement($key);
						$xmlwriter->text($value);
						$xmlwriter->endElement();
				}
				// * End user inclusions 11

			}
			// *************************
			// * User inclusions 12
			// * DO NOT DELETE OR CHANGE THOSE COMMENTS
			// *************************

			// ... (Add additional operations here) ...

			// * End user inclusions 12
			$xmlwriter->endElement();
		}
		$xmlwriter->endElement();
	}

	// *************************
	// * User inclusions 13
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...

	/**
	 * Writes output screenshots
	 *
	 * @param	XMLWriter		$xmlwriter the writer
	 * @param	array		$elements
	 * @return	[type]		...
	 */
	private function writeOutputScreenshots(XMLWriter $xmlwriter, array $elements)
	{
		$xmlwriter->startElement('screenshots');
		foreach ($elements as $element)
		{
			$xmlwriter->startElement('screenshot');
			$xmlwriter->text($element);
			$xmlwriter->endElement();
		}
		$xmlwriter->endElement();
	}

	/**
	 * Writes output platforms
	 *
	 * @param	XMLWriter		$xmlwriter the writer
	 * @param	array		$elements
	 * @return	[type]		...
	 */
	private function writeOutputPlatforms(XMLWriter $xmlwriter, array $elements)
	{
		$xmlwriter->startElement('platforms');
		foreach ($elements as $element)
		{
			$xmlwriter->startElement('platform');
			$xmlwriter->text($element);
			$xmlwriter->endElement();
		}
		$xmlwriter->endElement();
	}

	/**
	 * Writes output categories
	 *
	 * @param	XMLWriter		$xmlwriter the writer
	 * @param	array		$elements
	 * @return	[type]		...
	 */
	private function writeOutputCategories(XMLWriter $xmlwriter, array $elements)
	{
		$xmlwriter->startElement('categories');
		foreach ($elements as $element)
		{
			$xmlwriter->startElement('category');
			$xmlwriter->text($element);
			$xmlwriter->endElement();
		}
		$xmlwriter->endElement();
	}

	/**
	 * Writes output datasets
	 *
	 * @param	XMLWriter		$xmlwriter the writer
	 * @param	array		$elements
	 * @return	[type]		...
	 */
	private function writeOutputdatasets(XMLWriter $xmlwriter, array $elements)
	{
		$xmlwriter->startElement('datasets');
		foreach ($elements as $element)
		{
			$xmlwriter->startElement('dataset');
			$xmlwriter->text($element);
			$xmlwriter->endElement();
		}
		$xmlwriter->endElement();
	}
	// * End user inclusions 13


} // End of class tx_icsodappstore_getapplications_command
