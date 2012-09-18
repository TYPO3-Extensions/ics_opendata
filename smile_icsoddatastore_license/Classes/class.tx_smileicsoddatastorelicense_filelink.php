<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Plan Net <technique@in-cite.net>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Eid to get download file link
 *
 * @author	Emilie SAGNIEZ <emilie@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_smileicsoddatastorelicense
 */
class tx_smileicsoddatastorelicense_filelink {
	var $extKey        = 'ics_bc_folders';	// The extension key.
	var $prefixId = 'tx_icsoddatastore_pi1';
	/**
	 *	_contruct
	 *
	 */
	function tx_smileicsoddatastorelicense_filelink() {
		$this->initialize();
		$content = '';
		$files = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`tx_icsoddatastore_files`.*,
				`tx_icsoddatastore_fileformats`.`uid` as format_uid,
				`tx_icsoddatastore_fileformats`.`name` as format_name',
			'`tx_icsoddatastore_files`
				INNER JOIN `tx_icsoddatastore_fileformats`
				ON `tx_icsoddatastore_fileformats`.`uid` = `tx_icsoddatastore_files`.`format` ' . $this->cObj->enableFields('tx_icsoddatastore_fileformats'),
			'`tx_icsoddatastore_files`.`uid` = ' . $this->piVars['file'] . ' ' . $this->cObj->enableFields('tx_icsoddatastore_files')
		);
		if (is_array($files) && !empty($files)) {
			$file = $files[0];
			$cObj = t3lib_div::makeInstance('tslib_cObj');
			$cObj->start($file, 'tx_icsoddatastore_files');
			$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
			$link = $cObj->stdWrap('', $this->conf['datasetFileLink.']);			
			$content .= '<div class="download"><div class="download_link"><a href="' . $link . '">Téléchargez au format ' . $file['format_name'] . '</a></div></div>';
		}
		echo $content;
		exit();
	}
	
	/**
	 *	initialize
	 *
	 */
	function initialize() {
		$this->feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object     
		tslib_eidtools::connectDB(); //Connect to database
		tslib_fe::includeTCA();
		
		$pageUid = $_GET['id'] ? $_GET['id'] : 1;
		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 0);
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();
		
		$this->piVars = t3lib_div::_GET($this->prefixId);
		$this->cObj = t3lib_div::makeInstance("tslib_cObj");
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->prefixId . '.'];
	}	
	
}

?>