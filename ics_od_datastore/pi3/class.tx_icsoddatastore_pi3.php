<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cité Solution <technique@in-cite.net>
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

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Stats files downloads' for the 'ics_od_datastore' extension.
 *
 * @author	YANG Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */
class tx_icsoddatastore_pi3 extends tslib_pibase {
	var $prefixId      = 'tx_icsoddatastore_pi3';		// Same as class name
	var $scriptRelPath = 'pi3/class.tx_icsoddatastore_pi3.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_od_datastore';	// The extension key.

	private static $defaultSize = 10;	// Default size of rank
	private static $defaultType = 'dataset';	// Default type of rank

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

		$content = 'stat toto';

		return $this->pi_wrapInBaseClass($content);
	}
	
	function init() {
		$this->conf['size'] = self::$defaultSize;
		$this->conf['type'] = self::$defaultType;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_datastore/pi3/class.tx_icsoddatastore_pi3.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_datastore/pi3/class.tx_icsoddatastore_pi3.php']);
}

?>