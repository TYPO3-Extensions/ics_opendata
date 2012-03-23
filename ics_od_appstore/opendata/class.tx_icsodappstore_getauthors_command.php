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
 *   53: class tx_icsodappstore_getauthors_command extends tx_icsodcoreapi_command
 *   75:     function execute(array $params, XMLWriter $xmlwriter)
 *  128:     protected function transformResultsForOutput(array $authors)
 *  180:     protected function writeOutput(XMLWriter $xmlwriter, array $elements)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * @file class.tx_icsodappstore_getauthors_command.php
 *
 * Short description of the class getapplications
 *
 * @author    author name <author@mail.com>
 * @package    TYPO3.ics_od_appstore
 */

class tx_icsodappstore_getauthors_command extends tx_icsodcoreapi_command
{
	var $params = array();

	// *************************
	// * User inclusions 0
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...

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

		// * End user inclusions 1


		// Create a datasource object for retrieving authors
		$datasource = t3lib_div::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['datasource']['author']);

		$authors = array();

		$authors = $datasource->getAuthorsAll($params);

		// *************************
		// * User inclusions 2
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions 2


		$elements = $this->transformResultsForOutput($authors);
		makeError($xmlwriter, SUCCESS_CODE, SUCCESS_TEXT);

		// *************************
		// * User inclusions 3
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions 3


		$this->writeOutput($xmlwriter, $elements);
	}

	/**
	 * Transforms results for output
	 *
	 * @param	array		$authors A collection of authors
	 * @return	Elements		array
	 */
	protected function transformResultsForOutput(array $authors)
	{
		// *************************
		// * User inclusions 4
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions 4

		$elements = array();
		foreach ($authors as $author)
		{
			$element = array();

			$element['id'] = $author['id'];
			$element['name'] = $author['name'];

			// *************************
			// * User inclusions 5
			// * DO NOT DELETE OR CHANGE THOSE COMMENTS
			// *************************

			// ... (Add additional operations here) ...

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

		// * End user inclusions 7

		$xmlwriter->startElement('data');
		// *************************
		// * User inclusions 8
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions 8

		foreach ($elements as $element)
		{
			$xmlwriter->startElement('author');
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


} // End of class tx_icsodappstore_getauthors_command
