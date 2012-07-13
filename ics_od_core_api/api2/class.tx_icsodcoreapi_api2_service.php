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

require_once(t3lib_extMgm::extPath('ics_od_core_api') . 'api2/error_codes.php');

/** 
 * Handles an API query call.
 * This service dispatches the call to the requested command.
 * It checks the call parameters, retrieves the requested command instance,
 * retrieve the command's enumerator, starts the output, walk through the
 * enumerator to output items and ends the output.
 * Errors are supported to append before walking the command's enumerator.
 *
 * @author		Pierrick Caillon <pierrick@in-cite.net>
 * @package		TYPO3
 * @subpackage	ics_od_core_api
 */
class tx_icsodcoreapi_api2_Service {
	/**
	 * The request parameters.
	 */
	private $params = array(
		'key' => '', // key of developpers application
		'version' => '', // version of command
		'cmd' => '', // command to execute
		'output' => '', // output format
		'param' => array(), // array where key/value pairs are "param of command" => "param value"
	);
	/**
	 * The writer for output.
	 */
	private $writer;

	/**
	 * Initializes the service.
	 * Initializes the eID subsystem.
	 * Uses the HTTP GET/POST parameters to initializes the service parameters.
	 */
	public function init() {
		$this->feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object     
		tslib_eidtools::connectDB(); //Connect to database
		tslib_fe::includeTCA();
		foreach ($this->params as $gp => $var) {
			if (!is_null(t3lib_div::_GP($gp))) {
				$this->params[$gp] = t3lib_div::_GP($gp);
			}
		}
	}

	/**
	 * Checks the call of API and writes the document to standard output.
	 *
	 * @return void
	 */
	public function main() {
		$this->writeAnswerContent();
		$this->finishWriter();
	}
	
	private function initWriter() {
		$writer = $this->getWriter();
		$writer->setSourceCharset(($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset']) ? ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset']) : ('iso-8859-1'));
		$writer->start('opendata');
		$this->writer = $writer;
		$this->writeRequest();
		$writer->startElement('answer');
	}
	
	private function finishWriter() {
		$this->writer->endElement();
		$this->writer->end();
	}
	
	/**
	 * Writes the request element to the output.
	 *
	 * @param	tx_icsodcoreapi_api2_Writer		$writer: The output writer.
	 * @return	void
	 */
	private function writeRequest(tx_icsodcoreapi_api2_Writer $writer) {
		$writer->startElement('request');
		$writer->text(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
		$writer->endElement();
	}
	
	/**
	 * Writes the answer element to the output.
	 *
	 * @param	tx_icsodcoreapi_api2_Writer		$writer: The output writer.
	 * @return	void
	 */
	private function writeAnswerContent(tx_icsodcoreapi_api2_Writer $writer) {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_od_core_api']);
		$enumerator = null;
		try
		{
			if ($extConf && intval($extConf['shutdown']))
				throw new tx_icsodcoreapi_api2_Exception('ALERT_DISABLED');
			if ($extConf && intval($extConf['maintenance']))
				throw new tx_icsodcoreapi_api2_Exception('ALERT_MAINTENANCE');
			$this->checkCall());
			if (!$this->checkKey()) {
				throw new tx_icsodcoreapi_api2_Exception('ERROR_KEY');
			}
			if ($this->isCallLimitReached()) {
				throw new tx_icsodcoreapi_api2_Exception('ERROR_MAX');
			}
			$factory = t3lib_div::makeInstance('tx_icsodcoreapi_api2_factory', $this->params['version']);
			$command = $factory->getCommand($this->params['cmd']);

			if (!is_object($command)) {
				throw new tx_icsodcoreapi_api2_Exception('ERROR_COMMAND');
			}
			else {
				$enumerator = $command->getResultEnumerator($this->params['param']);
				$this->logCall();
			}
			if (!($enumerator instanceof tx_icsodcoreapi_api2_Enumerator)) {
				$enumerator = null;
				throw new tx_icsodcoreapi_api2_Exception('ERROR_COMMAND_RESULT');
			}
		}
		catch (tx_icsodcoreapi_api2_Exception $e) {
			switch ($e->getCode()) {
				case 0:
					break;
				case -1:
				case 9:
					header('HTTP/1.0 500 Internal Server Error', true, 500);
					break;
				case 8:
					header('HTTP/1.0 509 Quota Exceeded', true, 509);
					break;
				case 98:
				case 99:
					header('HTTP/1.0 503 Service Unavailable', true, 503);
					break;
				default:
					header('HTTP/1.0 400 Bad request', true, 400);
					break;
			}
			$this->initWriter();
			$e->writeTo($this->writer);
			return;
		}
		$this->initWriter();
		(new tx_icsodcoreapi_api2_Exception('SUCCESS'))->writeTo($this->writer);
		if ($enumerator != null) {
			$command->start($writer);
			while ($enumerator->next()) {
				$current = $enumerator->current();
				if ($current instanceof tx_icsodcoreapi_api2_Writeable) {
					$current->writeTo($writer);
				}
			}
			$command->end($writer);
		}
	}
	/**
	 * Checks if the required parameters are specified.
	 * @throws tx_icsodcoreapi_api2_Exception		When an exception when an invalid parameter is encountered.
	 * @return	void
	 */
	private function checkCall(){
		if (empty($this->params['key'])) {
			throw new tx_icsodcoreapi_api2_Exception('ERROR_KEY_EMPTY');
		}
		if (empty($this->params['version'])) {
			throw new tx_icsodcoreapi_api2_Exception('ERROR_VERSION_EMPTY');
		}
		if (empty($this->params['cmd'])) {
			throw new tx_icsodcoreapi_api2_Exception('ERROR_COMMAND_EMPTY');
		}	
	}

	/**
	 * Checks if the API Key is valid.
	 *
	 * @return Boolean <code>True</code> if the API Key is valid otherwise <code>false</code>.
	 */
	private function checkKey() {
		global $TYPO3_DB;
		$rows = $TYPO3_DB->exec_SELECTgetRows(
			'uid',
			'tx_icsodappstore_applications',
			'key_appli = ' . $TYPO3_DB->fullQuoteStr($this->params['key'], $table) . ' ' .
			'AND hidden = 0 AND deleted = 0'
		);
		return (!empty($rows));
	}
		
	/**
	 * Retrieves the output Writer.
	 *
	 * @return	tx_icsodcoreapi_api2_Writer		The created writer.
	 */
	private function getWriter() {
		$writerClass = 'tx_icsodcoreapi_api2_XmlWriter';
		switch (strtoupper($this->params['output'])) {
			case 'JSON':
				$type = 'application/json';
				$writerClass = 'tx_icsodcoreapi_api2_JSonWriter';
				break;
			default:
				$type = 'application/xml';
		}
        header('Content-Type: ' . $type . '; charset=utf-8');
		header('Access-Control-Allow-Origin: *');
		return t3lib_div::makeInstance($writerClass, 'php://output');
	}
	
	/**
	 * Logs the call.
	 */
	private function logCall() {
		$logger = t3lib_div::makeInstance('tx_icsodcoreapi_logger');
		$logger->init($this->params);
		$logger->logCall();
	}
	
	/**
	 * Checks if the period call limit is reached.
	 *
	 * @return Boolean <code>True</code> if the limit is reached otherwise <code>false</code>.
	 */
	private function isCallLimitReached() {
		global $TYPO3_DB;
		$rows = $TYPO3_DB->exec_SELECTgetRows(
			'count_use, max',
			'tx_icsodappstore_applications',
			'key_appli = ' . $TYPO3_DB->fullQuoteStr($this->params['key'], $table) . ' ' .
			'AND hidden = 0 AND deleted = 0'
		);
		return (intval($rows[0]['count_use']) >= intval($rows[0]['max']));
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_core_api/api2/class.tx_icsodcoreapi_api2_service.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_core_api/api2/class.tx_icsodcoreapi_api2_service.php']);
}
