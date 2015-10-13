<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cit� Solution <technique@in-cite.net>
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
 * @file class.tx_icsoddatastore_datastore_gettiers_command.php
 *
 * Short description of the class datastore_gettiers
 *
 * @author    Plan.Net <typo3@plan-net.fr>
 * @package    TYPO3.ics_od_datastore
 */


class tx_icsoddatastore_datastore_gettiers_command extends tx_icsodcoreapi_command
{


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
	 * @return	void
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


		// Create a datasource object for retrieving agencys
		$datasource = t3lib_div::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['datasource']['tiers']);

		$tiers = array();

		$tiers = $datasource->getTiersAll($params);

		// *************************
		// * User inclusions 2
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions 2


		$elements = $this->transformResultsForOutput($tiers);
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
	 * @param	array		$tiers A collection of tiers
	 * @return	array
	 */
	protected function transformResultsForOutput(array $tiers)
	{
		$elements = array();
		foreach ($tiers as $tier)
		{
			$element = array();

			$element['id'] = $tier['id'];
			$element['name'] = $tier['name'];
			$element['description'] = $tier['description'];
			$element['email'] = $tier['email'];
			$element['website'] = $tier['website'];
			$element['logo'] = $tier['logo'];
			$element['address'] = $tier['address'];
			$element['zipcode'] = $tier['zipcode'];
			$element['city'] = $tier['city'];
			$element['country'] = $tier['country'];
			$element['latitude'] = $tier['latitude'];
			$element['longitude'] = $tier['longitude'];

			// *************************
			// * User inclusions 4
			// * DO NOT DELETE OR CHANGE THOSE COMMENTS
			// *************************

			// ... (Add additional operations here) ...
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['tiers_output'])) {
				$_params = array(
					'tier' => $tier,
					'output' => &$element,
				);
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['tiers_output'] as $funcRef) {
					t3lib_div::callUserFunction($funcRef, $_params, $this);
				}
			}

			// * End user inclusions 4


			$elements[] = $element;
		}

		// *************************
		// * User inclusions 5
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions 5


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
		$xmlwriter->startElement('data');
		foreach ($elements as $element)
		{
			$xmlwriter->startElement('tiers');
			foreach ($element as $key => $value)
			{
				$xmlwriter->startElement($key);
				$xmlwriter->text($value);
				$xmlwriter->endElement();
			}
			$xmlwriter->endElement();
		}
		$xmlwriter->endElement();
	}
} // End of class tx_icsoddatastore_datastore_gettiers_command
