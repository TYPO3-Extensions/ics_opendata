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
 * filter test : test filter 
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_mysql_filter_test
	implements tx_icsopendata_filter_generator
{
	public function generateFilter($filter, $nbindent)
	{
		$returnarray = array();
		$indent = str_repeat(chr(9), $nbindent);
		$param1 = $filter->getParam(0);
		$field1 = $filter->getLink(0)->getField();
		$fieldname1 = $field1->getName();
		$tablename1 = $field1->getTableName();
		
		$param2 = $filter->getParam(1);
		$field2 = $filter->getLink(1)->getField();
		$fieldname2 = $field2->getName();
		$tablename2 = $field2->getTableName();

		$returnarray['WHERE'] = $indent . '\'`' . $tablename1 . '`.`' . $fieldname1 . '` = \' . $GLOBALS[\'TYPO3_DB\']->fullQuoteStr($params[\'' . $param1 . '\'], \'' . $tablename1 . '\') .' . chr(10);
		$returnarray['WHERE'] .= $indent . '\'AND `' . $tablename2 . '`.`' . $fieldname2 . '` = \' . $GLOBALS[\'TYPO3_DB\']->fullQuoteStr($params[\'' . $param2 . '\'], \'' . $tablename2 . '\')';
		return $returnarray;
	}

} /* end of class tx_icsopendata_mysql_filter_test */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/filters/class.mysql_filter_test.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/filters/class.mysql_filter_test.php']);
}

?>