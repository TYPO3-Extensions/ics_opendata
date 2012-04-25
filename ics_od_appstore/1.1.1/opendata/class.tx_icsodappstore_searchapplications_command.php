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
 *   53: class tx_icsodappstore_searchapplications_command extends tx_icsodcoreapi_command
 *  122:     function execute(array $params, XMLWriter $xmlwriter)
 *  231:     protected function transformResultsForOutput(array $applications)
 *  308:     protected function writeOutput(XMLWriter $xmlwriter, array $elements)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * @file class.tx_icsodappstore_searchapplications_command.php
 *
 * Short description of the class getapplications
 *
 * @author    author name <author@mail.com>
 * @package    TYPO3.ics_od_appstore
 */

class tx_icsodappstore_searchapplications_command extends tx_icsodcoreapi_command
{

	const EMPTY_TITLE_CODE = 100;
	const EMPTY_TITLE_TEXT = "title should be not empty.";
	const INVALID_TITLE_CODE = 101;
	const INVALID_TITLE_TEXT = "The specified value is not valid for title.";
	const EMPTY_RELEASED_START_CODE = 102;
	const EMPTY_RELEASED_START_TEXT = "released_start should be not empty.";
	const INVALID_RELEASED_START_CODE = 103;
	const INVALID_RELEASED_START_TEXT = "The specified value is not valid for released_start.";
	const EMPTY_RELEASED_END_CODE = 104;
	const EMPTY_RELEASED_END_TEXT = "released_end should be not empty.";
	const INVALID_RELEASED_END_CODE = 105;
	const INVALID_RELEASED_END_TEXT = "The specified value is not valid for released_end.";
	const EMPTY_UPDATED_START_CODE = 106;
	const EMPTY_UPDATED_START_TEXT = "updated_start should be not empty.";
	const INVALID_UPDATED_START_CODE = 107;
	const INVALID_UPDATED_START_TEXT = "The specified value is not valid for updated_start.";
	const EMPTY_UPDATED_END_CODE = 108;
	const EMPTY_UPDATED_END_TEXT = "updated_end should be not empty.";
	const INVALID_UPDATED_END_CODE = 109;
	const INVALID_UPDATED_END_TEXT = "The specified value is not valid for updated_end.";
	const EMPTY_PLATFORMS_CODE = 110;
	const EMPTY_PLATFORMS_TEXT = "platforms should be not empty.";
	const INVALID_PLATFORMS_CODE = 111;
	const INVALID_PLATFORMS_TEXT = "The specified value is not valid for platforms.";
	const EMPTY_CATEGORIES_CODE = 112;
	const EMPTY_CATEGORIES_TEXT = "categories should be not empty.";
	const INVALID_CATEGORIES_CODE = 113;
	const INVALID_CATEGORIES_TEXT = "The specified value is not valid for categories.";
	const EMPTY_DATASETS_CODE = 114;
	const EMPTY_DATASETS_TEXT = "datasets should be not empty.";
	const INVALID_DATASETS_CODE = 115;
	const INVALID_DATASETS_TEXT = "The specified value is not valid for datasets.";
	const EMPTY_AUTHOR_CODE = 116;
	const EMPTY_AUTHOR_TEXT = "author should be not empty.";
	const INVALID_AUTHOR_CODE = 117;
	const INVALID_AUTHOR_TEXT = "The specified value is not valid for author.";
	const EMPTY_SEARCH_CODE = 118;
	const EMPTY_SEARCH_TEXT = "search should be not empty.";
	const INVALID_SEARCH_CODE = 119;
	const INVALID_SEARCH_TEXT = "The specified value is not valid for search.";

	var $params = array(
		'search' => '1',
	);

	// *************************
	// * User inclusions 0
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...
	const EMPTY_PARAMS_CODE = 120;
	const EMPTY_PARAMS_TEXT = "A parameter should be given at least.";

	var $pageLimitMax = 50;
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

		if (empty($params['search']))
		{
			makeError($xmlwriter, tx_icsodappstore_searchapplications_command::EMPTY_SEARCH_CODE, tx_icsodappstore_searchapplications_command::EMPTY_SEARCH_TEXT);
			return;
		}

		// *************************
		// * User inclusions 1
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...
		if (empty($params['title'])
			&& empty($params['updated_start'])
			&& empty($params['updated_end'])
			&& empty($params['released_start'])
			&& empty($params['released_end'])
			&& empty($params['platforms'])
			&& empty($params['categories'])
			&& empty($params['datasets'])
			&& empty($params['author'])) {

			makeError($xmlwriter, tx_icsodappstore_searchapplications_command::EMPTY_PARAMS_CODE, tx_icsodappstore_searchapplications_command::EMPTY_PARAMS_TEXT);
			return;
		}

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

		// $params['search'] : Always true and call of "$datasource->getApplicationsAll($params);" is never reached
		$params['search'] = 1;

		// * End user inclusions 1


		// Create a datasource object for retrieving applications
		$datasource = t3lib_div::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['datasource']['application']);

		$applications = array();

		if( !isset($params['search']) ) {
			$applications = $datasource->getApplicationsAll($params);
		}
		else {
			$applications = $datasource->getApplicationsFilter($params);
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
			unset($element['author']);
			unset($element['title']);
			unset($element['description']);
			unset($element['release_date']);
			unset($element['logo']);
			unset($element['screenshot']);
			unset($element['link']);
			unset($element['update_date']);
			unset($element['lock_publication']);
			unset($element['publish']);
			unset($element['platforms']);
			unset($element['tstamp']);
			unset($element['crdate']);

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

				// * End user inclusions 10

				$xmlwriter->startElement($key);
				$xmlwriter->text($value);
				$xmlwriter->endElement();
				// *************************
				// * User inclusions 11
				// * DO NOT DELETE OR CHANGE THOSE COMMENTS
				// *************************

				// ... (Add additional operations here) ...

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

	// * End user inclusions 13


} // End of class tx_icsodappstore_searchapplications_command
