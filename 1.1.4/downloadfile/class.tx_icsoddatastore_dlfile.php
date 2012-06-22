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
 * Download file
 *
 * @author    Tsi Yang <tsi@in-cite.net>
 * @package    TYPO3
 */
class tx_icsoddatastore_dlfile {
	/**
	 * Execute download
	 *
	 * @param	array		$file: File's record
	 * @return void
	 */
	function main(array $file) {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_od_datastore']);
		switch (intval($extConf['stats'])) {
			case 1:	
				$this->redirect($file['file']);
				break;
			case 2:	
				$this->readfile($file);
				break;
			default:
				trigger_error('Download file method is not defined.', E_USER_ERROR);
				header("HTTP/1.0 404 Not Found");
				echo "<html><body><h1>Page not found</h1></body></html>";
				die();
		}
	}
	
	/**
	 * Redirect download
	 *
	 * @param	string		$filePath: The file's path
	 * @return void
	 */
	private function redirect($filePath='') {
		header('Location: ' . t3lib_div::locationHeaderUrl($filePath));
		die();
	}
	
	/** 
	 * Download with PHP readfile
	 *
	 * @param	array		$file: File's record
	 * @return void
	 */
	private function readfile(array $file) {
		$formats = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, mimetype',
			'tx_icsoddatastore_fileformats',
			'deleted = 0 AND uid = ' . $file['format'],
			'',
			'',
			'1'
		);
		if (is_array($formats) && !empty($formats)) {
			$filePath = t3lib_div::getFileAbsFileName($file['file']);
			header('Pragma: private');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Type: ' . $formats[0]['mimetype']);
			header('Content-Length: ' . filesize($filePath));

			header('Content-Disposition: inline; filename="' .  basename($filePath) . '"');

			readfile($filePath);
			
			ob_flush();
			flush();
			die();
		}
	}
}