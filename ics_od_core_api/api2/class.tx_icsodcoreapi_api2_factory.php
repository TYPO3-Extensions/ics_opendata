<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 plan.net france <technique@in-cite.net>
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
 * $Id$
 */

/** 
 * Command factory.
 *
 * @author		Pierrick Caillon <pierrick@in-cite.net>
 * @package		TYPO3
 * @subpackage	ics_od_core_api
 */
class tx_icsodcoreapi_api2_Factory {

	private $version; /**< Version of API */
	private $command; /**< The action */
	private $patternCommand; /**< pattern command */
	
	/**
	 * Initializes the factory.
	 *
	 * @param	string		$version: The version of API.
	 * @return	void
	 */
	function __construct($version) {
		global $TYPO3_CONF_VARS;
		
		$this->version = $version;
		if ((!isset($TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api2']['command'][$version]) &&
			!isset($TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api2']['patterncommand'][$version])) ||
			(empty($TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api2']['command'][$version]) &&
			empty($TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api2']['patterncommand'][$version])))
			throw new tx_icsodcoreapi_api2_Exception('ERROR_VERSION');
		$this->command = $TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api2']['command'][$version];
		$this->patternCommand = $TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api2']['patterncommand'][$version];
	}
	
	/**
	 * Retrieves a new command for the specified action.
	 *
	 * @param	string		$command: The action.
	 * @return	tx_icsodcoreapi_api2_Command		The instance of the requested action's command. <code>Null</code> if the action is not found.
	 */
	function getCommand($command) {
		if (is_array($this->patternCommand)) {
			foreach ($this->patternCommand as $commandRef) {
				list($pattern, $classRef) = $commandRef;
				if (preg_match($pattern, $command) == 1) {
					$className = $this->getUserObjClass($classRef);
					$commandObj = t3lib_div::makeInstance($className, $command);
					if ($commandObj && is_a($commandObj, 'tx_icsodcoreapi_api2_PatternCommand') && $commandObj->isValid()) {
						return $commandObj;
					}
				}
			}
		}
		$commandObj = t3lib_div::getUserObj($this->command[$command], 'user_', true);
		if ($commandObj && is_a($commandObj, 'tx_icsodcoreapi_api2_Command'))
			return $commandObj;
		return null;
	}

	/**
	 * Get class name
	 *
	 * @param	string		$classRef: path class
	 * @return	string		class name
	 */
	private function getUserObjClass($classRef) {
		if (strpos($classRef, ':') !== false) {
			list($file, $class) = t3lib_div::revExplode(':', $classRef, 2);
			$requireFile = t3lib_div::getFileAbsFileName($file);
			if ($requireFile) t3lib_div::requireOnce($requireFile);
		}
		else {
			$class = $classRef;
		}
		return $class;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_core_api/api2/class.tx_icsodcoreapi_api2_factory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_core_api/api2/class.tx_icsodcoreapi_api2_factory.php']);
}
