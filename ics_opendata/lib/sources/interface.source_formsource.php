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
 * Interface Source Form Source : All parameter source form must implements this interface !
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
interface tx_icsopendata_SourceFormSource
	extends tx_icsopendata_Form
{

	// === OPERATIONS ============================================================================= //

    /**
     * Analyze the database and instanciate a data object
     *
     * @return boolean
     */
    public function analyze();

} /* end of interface tx_icsopendata_SourceFormSource */

?>