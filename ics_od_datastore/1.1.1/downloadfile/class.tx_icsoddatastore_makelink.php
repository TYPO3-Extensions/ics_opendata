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

/** 
 * Make link
 * Generate file's url to hit download
 *
 * @author    Tsi Yang <tsi@in-cite.net>
 * @package    TYPO3
 */
class tx_icsoddatastore_makelink {
	/**
	 * Generate url
	 *
	 * @param	array	$row: File's record
	 * @return generated url
	 */
	function generateUrl(array $row) {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_od_datastore']);
		switch (intval($extConf['stats'])) {
			case 1:	// Stats method 302
			case 2:	// Stats method readfile
				$url = $row['record_type'] ? $row['url'] : t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '?eID=ics_od_datastoredownload&file=' . $row['uid'];
				break;
			default:
				$url = $row['record_type'] ? $row['url'] : t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $row['file'];
		}
		return $url;
	}
	
	/**
	 * Makelink main function
	 *
	 * @param	string		$content: The content to process
	 * @param	array		$conf: Typoscript configuration
	 * @return
	 */
	function main($content='', array $conf) {
		return $this->generateUrl($this->cObj->data);
	}

}
