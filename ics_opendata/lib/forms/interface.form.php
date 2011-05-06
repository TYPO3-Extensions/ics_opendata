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
 * Interface Form : All form must implements this interface !
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
interface tx_icsopendata_Form
{

    // === OPERATIONS ============================================================================= //

    /**
     * Inputs validation for the current form
	 * set $errors array if inputs are not valid
     *
     * @return Boolean
     */
    public function validInput();

    /**
     * Retrieve the content of the current form
     *
     * @param  $FormData : Data POST saved
     * @return String
     */
    public function renderForm($FormData, $pObj);

    /**
     * Retrieve the id of the next form, depending on inputs
     *
     * @return String
     */
    public function getNextFormId();

} /* end of interface tx_icsopendata_Form */

?>