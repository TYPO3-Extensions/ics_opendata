<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 author name <author@mail.com>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *   53: function typo3db_opendatapkg_connect()
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * @file tx_icsodappstore_sourceconnexion.php
 *
 * Connexion functions to datasource
 *
 * @author    author name <author@mail.com>
 * @package    TYPO3.ics_od_appstore
 */


/**
 * Connect to the datasource opendatapkg type typo3db
 *
 * @return	object		The connexion to the datasource
 */
function typo3db_opendatapkg_connect()
{
	// *************************
	// * User inclusions typo3db_opendatapkg_connect
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...

	// * End user inclusions typo3db_opendatapkg_connect


	return $GLOBALS['TYPO3_DB'];
}

