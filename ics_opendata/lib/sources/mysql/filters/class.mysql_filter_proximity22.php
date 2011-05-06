<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 In Cité Solution <technique@in-cite.net>
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
 * filter proximity22 : add a where to the mysql query
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_mysql_filter_proximity22
	implements tx_icsopendata_filter_generator
{
	public function generateFilter($filter, $nbindent)
	{
		$returnarray = array();
		$indent = str_repeat(chr(9), $nbindent);
		$latitude = $filter->getParam(0);
		$longitude = $filter->getParam(1);
		
		$latfield = $filter->getLink(0)->getField();
		$lngfield = $filter->getLink(1)->getField();
		
		$latfieldname = $latfield->getName();
		$lattablename = $latfield->getTableName();
		$lngfieldname = $lngfield->getName();
		$lngtablename = $lngfield->getTableName();
		
		$latcontent = 'deg2rad(floatval($params[\'' . $latitude . '\']))';
		$lngcontent = 'deg2rad(floatval($params[\'' . $longitude . '\']))';
		$orthodromie = $indent . '\'(6378.137 * ACOS(COS(RADIANS(`' . $lattablename . '`.`' . $latfieldname . '`)) * COS(\' . ' . $latcontent . ' . \') * COS(RADIANS(`' . $lngtablename . '`.`' . $lngfieldname . '`) - ' . chr(10) . 
						$indent . '\' . ' . $lngcontent . ' . \') + SIN(RADIANS(`' . $lattablename . '`.`' . $latfieldname . '`)) * SIN(\' . ' . $latcontent . ' . \')))';
		$order = $orthodromie . ' ASC\'';

		$returnarray['ORDER'] = $order;
		
		return $returnarray;
	}

} /* end of class tx_icsopendata_mysql_filter_proximity22 */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/filters/class.mysql_filter_proximity22.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/filters/class.mysql_filter_proximity22.php']);
}

?>