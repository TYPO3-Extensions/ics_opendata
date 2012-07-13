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
 * Interface for output writing.
 * @author		Pierrick Caillon <pierrick@in-cite.net>
 * @package		TYPO3
 * @subpackage	ics_od_core_api
 */
interface tx_icsodcoreapi_api2_writer {
	/**
	 * Writes a full element tag.
	 * The only child is the specified text content if not empty.
	 * @param	string		$name: The name of the element.
	 * @param	string		$content: The content of the element.
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function writeElement($name, $content = '');
	/**
	 * Starts an element.
	 * @param	string		$name: The name of the element.
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function startElement($name);
	/**
	 * Ends the current element.
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function endElement();
	/**
	 * Writes a full attribute.
	 * @param	string		$name: The name of the attribute.
	 * @param	string		$value: The value of the attribute.
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function writeAttribute($name, $value);
	/**
	 * Starts an attribute.
	 * @param	string		$name: The name of the attribute.
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function startAttribute($name);
	/**
	 * Ends the current attribute.
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function endAttribute();
	/**
	 * Writes a text.
	 * @param	string		$content: The text content
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function text($content);
	/**
	 * Writes a full CDATA.
	 * @param	string		$content: The content of the CDATA.
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function writeCData($content);
	/**
	 * Starts a CDATA.
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function startCData();
	/**
	 * Ends the current CDATA;
	 * @return	boolean		<b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function endCData();
}
