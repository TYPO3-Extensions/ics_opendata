<?php
/***************************************************************
*  Copyright notice
*
*  (c) %%%YEAR%%% %%%AUTHOR%%% <%%%EMAIL%%%>
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
 * @file class.%%%COMMANDCLASSNAME%%%.php
 *
 * %%%DESCRIPTION%%%
 * 
 * @author    %%%AUTHOR%%% <%%%EMAIL%%%>
 * @package    TYPO3.%%%EXTENSIONKEY%%%
 */

require_once(t3lib_extMgm::extPath('ics_opendata_api') . 'api/class.tx_icsopendataapi_command.php');

class %%%COMMANDCLASSNAME%%% extends tx_icsopendataapi_command
{
	%%%ERRORCODE%%%%%%DEFAULTVALUES%%%%%%ALLOWEDVALUES%%%
	%%%USERCODE%%%
	/**
	 * Executes the command.
	 *
	 * @param	array	$params The command parameters.
	 * @param	XMLWriter	$xmlwriter The XML Writer for output.
	 */
	function execute(array $params, XMLWriter $xmlwriter)
	{
		$params = array_merge($this->params, $params);
		%%%TESTPARAM%%%
		%%%USERCODE%%%
		
		// Create a datasource object for retrieving %%%ITEMS%%%
		$datasource = t3lib_div::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['%%%EXTENSIONKEY%%%']['datasource']['%%%ITEM%%%']);
		
		$%%%ITEMS%%% = array();
		%%%DATASOURCEQUERY%%%
		
		%%%USERCODE%%%
		
		$elements = $this->transformResultsForOutput($%%%ITEMS%%%);
		makeError($xmlwriter, SUCCESS_CODE, SUCCESS_TEXT);
		
		%%%USERCODE%%%
		
		$this->writeOutput($xmlwriter, $elements);
	}
	
	/**
	 * Transforms results for output
	 *
	 * @param	array	$%%%ITEMS%%% A collection of %%%ITEMS%%%
	 *
	 * @return	Elements array
	 */	
	protected function transformResultsForOutput(array $%%%ITEMS%%%)
	{
		$elements = array();
		foreach ($%%%ITEMS%%% as $%%%ITEM%%%)
		{
			$element = array();
			%%%TYPECONVERSION%%%
			%%%USERCODE%%%
			
			$elements[] = $element;
		}
		
		%%%USERCODE%%%
		
		return $elements;
	}
	
	/**
	 * Writes output
	 *
	 * @param	XMLWriter	$xmlwriter the writer
	 * @param	array	$elements
	 *
	 * @return void
	 */
	 protected function writeOutput(XMLWriter $xmlwriter, array $elements)
	{
		$xmlwriter->startElement('data'); 
		foreach ($elements as $element)
		{
			$xmlwriter->startElement('%%%ITEM%%%');
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
} // End of class %%%COMMANDCLASSNAME%%%
