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


require_once(t3lib_extMgm::extPath('ics_opendata') . 'lib/sources/mysql/class.mysql_generation.php');

/**
 * Generate the datasource file associated to the command file
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_Typo3dbGeneration extends tx_icsopendata_Mysqlgeneration
{

	// === OPERATIONS ============================================================================= //
	
	protected function generateQueryFilter($filters, $tablename, $nbindent)
	{
		$filterscontent = parent::generateQueryFilter($filters, $tablename, $nbindent);

		$indent = str_repeat(chr(9), $nbindent);		
		$restriction = chr(10) . $indent . '\'1\' . t3lib_BEfunc::deleteClause(\'' . $tablename . '\') . t3lib_BEfunc::BEenableFields(\'' . $tablename . '\')';		
		if ($filterscontent['WHERE'] == chr(10) . $indent . '\'\'')
			$filterscontent['WHERE'] = $restriction;
		else
			$filterscontent['WHERE'] = $restriction . chr(10) . $indent . '. \' AND \' .' . $filterscontent['WHERE'];
		
		return $filterscontent;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/typo3db/class.typo3db_generation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/typo3db/class.typo3db_generation.php']);
}