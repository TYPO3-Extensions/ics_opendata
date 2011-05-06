<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 In-Cite Solution <technique@in-cite.net>
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
 * Cast : timestamp to date ISO8601 (string)
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_cast_timestamp_todateiso8601
		implements tx_icsopendata_Casting
{

	// === OPERATIONS ============================================================================= //

	/*
	* Retrieve content corresponding to the transformation between input and output types
	*
	* @return String
	*/	
	public function generateCast($nbindent)
	{
		$indent = str_repeat(chr(9), $nbindent);
		$content = $indent . '%%%XMLDATA%%% = date(\'c\', %%%SOURCEDATA%%%);';
		return $content;
	}

} /* end of class tx_icsopendata_cast_timestamp_todateiso8601 */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/casting/class.cast_timestamp_todateiso8601.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/casting/class.cast_timestamp_todateiso8601.php']);
}

?>