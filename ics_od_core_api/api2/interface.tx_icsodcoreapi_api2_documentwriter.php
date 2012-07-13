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

/**
 * Interface for document part of output writing.
 * @author		Pierrick Caillon <pierrick@in-cite.net>
 * @package		TYPO3
 * @subpackage	ics_od_core_api
 */
interface tx_icsodcoreapi_api2_DocumentWriter {
	/**
	 * Defines the source charset.
	 * @param	string		$charset: The source charset.
	 * @return	void
	 */
	public function setSourceCharset($charset);
	/**
	 * Starts the document with the specified root element name.
	 * @param	string		$rootElementName: The name of the root element.
	 * @return	void
	 */
	public function start($rootElementName);
	/**
	 * Ends the document.
	 * Also flush the output.
	 * @return	void
	 */
	public function end();
}
