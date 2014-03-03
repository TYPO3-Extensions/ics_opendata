<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Plan.Net France <typo3@plan-net.fr>
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
 * Eid to return data of filegroups for the 'ics_od_dataviz' extension.
 *
 * @author	Emilie SAGNIEZ <emilie.sagniez@plan-net.fr>
 * @package	TYPO3
 * @subpackage	tx_icsoddataviz
 */
class tx_icsoddataviz_getdata {
	var $prefixId      = 'tx_icsoddataviz_getdata';		// Same as class name
	var $scriptRelPath = 'Classes/class.tx_icsoddataviz_getdata.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_od_dataviz';	// The extension key.
	var $folderObj	= null;
	var $commonObj	= null;
	var $conf = array();
	
	 /**
     * @var t3lib_cache_frontend_AbstractFrontend
     */
    protected $cacheInstance;
	protected $cacheExtKey = 'ics_od_dataviz_cache';
	
	/**
	 *	_contruct
	 *
	 */
	function tx_icsoddataviz_getdata() {
		$content = '';
		$this->initialize();
		if ($this->piVars['file']) {
			$files = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'`tx_icsoddatastore_files`.uid,
					`tx_icsoddatastore_files`.tstamp,
					`tx_icsoddatastore_files`.file',
				'`tx_icsoddatastore_files`',
				'`tx_icsoddatastore_files`.`file` like \'%.csv\' AND `tx_icsoddatastore_files`.`uid` = ' . $this->piVars['file'] . ' ' .  $this->cObj->enableFields('tx_icsoddatastore_files'),
				'',
				'',
				1
			);
			if (is_array($files) && !empty($files)) {
				$file = $files[0];
				$content = $this->getContentFile($file);
			}
		}
		header('Content-Type: application/json');
		echo json_encode($content);
		exit();
	}
	
	/**
	 *	initialize
	 *
	 */
	function initialize() {
		$pageUid = $_GET['id'] ? $_GET['id'] : 1;
		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 0);
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->checkAlternativeIdMethods();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->getCompressedTCarray();
		
		$this->piVars = t3lib_div::_GP($this->prefixId);
		$this->cObj = t3lib_div::makeInstance("tslib_cObj");
		
		$this->initializeCache();
	}	
	
	/**
     * Initialize cache instance to be ready to use
     *
     * @return void
     */
    protected function initializeCache() {
        t3lib_cache::initializeCachingFramework();		
        try {
            $this->cacheInstance = $GLOBALS['typo3CacheManager']->getCache($this->cacheExtKey);
        } catch (t3lib_cache_exception_NoSuchCache $e) {
            $this->cacheInstance = $GLOBALS['typo3CacheFactory']->create(
                $this->cacheExtKey,
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->cacheExtKey]['frontend'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->cacheExtKey]['backend'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->cacheExtKey]['options']
            );
        }
    }
	
	function getCacheID($file) {
		return $file['uid'] . $file['tstamp'] . ($this->piVars['next'] ?: 0);
	}
	
	function getContentFile($file) {
		$cacheID = $this->getCacheID($file);
		header('X-FromChache: True');
		 // If $entry is null, it hasn't been cached. Calculate the value and store it in the cache:
        if (false === ($entry = $GLOBALS['typo3CacheManager']->getCache($this->cacheExtKey)->get($cacheID))) {
			header('X-FromChache: False');
			$filepath = $file['file'];
			$entry = $this->readfile($filepath, $this->piVars['next'] ?: 0);
			$tags = array(); // default
			$lifetime = null; // default
            // Save value in cache
            $GLOBALS['typo3CacheManager']->getCache($this->cacheExtKey)->set($cacheID, serialize($entry), $tags, $lifetime);
        } else {
			$entry = unserialize($entry);
		}
        return $entry;
	}
	
	// ID: $uid . $tstamp . $position
	
	function readfile($filepath, $position = 0) {
		$handle = fopen($filepath, "a+");
		$counterLine = 0;
		$header = false;
		$data = array();
		$limitMax = 20;
		fseek($handle, $position);
		while (($counterLine < $limitMax) && ($line = fgetcsv($handle, 1024, ';'))) {
			if (!$header && ($position == 0)) {
				$header = $line;
				continue;
			}
			$counterLine++;
			// if (implode('', $line)) {
				$data[] = $line;
			//}
		}
		$cs = t3lib_div::makeInstance('t3lib_cs');
		$cs->convArray($data, 'ISO8859-1', 'UTF8');
		$cs->convArray($header, 'ISO8859-1', 'UTF8');
		$result = array(
			'title' => basename($filepath),
			'header' => $header,
			'data' => $data,
			'next' => !feof($handle) ? ftell($handle) : false,
			'size' => filesize($filepath),
		);
		fclose($handle);
		return $result;
	}
	
}

?>