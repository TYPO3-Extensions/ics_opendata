<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 plan.net france <technique@in-cite.net>
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
 * $Id$
 */

require_once(t3lib_extMgm::extPath('ics_od_core_api') . 'api2/error_codes.php');

/** 
 * Abstract command class.
 * Defines the contract for a command.
 *
 * @author		Pierrick Caillon <pierrick@in-cite.net>
 * @package		TYPO3
 * @subpackage	ics_od_core_api
 */ 
abstract class tx_icsodcoreapi_api2_Command {
	/**
	 * Retrieves the enumerator on the command result.
	 * @param	array		$params: The command parameters.
	 * @return	tx_icsodcoreapi_api2_Enumerator		The enumerator on the result.
	 */
	public function getResultEnumerator(array $params) {
		throw new tx_icsodcoreapi_api2_Exception('ERROR_COMMAND');
	}
	/**
	 * Starts the result output.
	 * @param	tx_icsodcoreapi_api2_Writer		$writer: The output writer.
	 * @return	void
	 */
	public function start(tx_icsodcoreapi_api2_Writer $writer) {
		$writer->startElement('data');
	}
	/**
	 * Ends the result output.
	 * @param	tx_icsodcoreapi_api2_Writer		$writer: The output writer.
	 * @return	void
	 */
	public function end(tx_icsodcoreapi_api2_Writer $writer) {
		$writer->endElement();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_core_api/api2/class.tx_icsodcoreapi_api2_command.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_core_api/api2/class.tx_icsodcoreapi_api2_command.php']);
}
