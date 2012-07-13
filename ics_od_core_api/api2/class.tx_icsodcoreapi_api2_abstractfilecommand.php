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
 * Abstract file command class.
 * Defines the generic way of providing files via API.
 *
 * @remarks Parameters :<dl>
 * <dt>mode</dt><dd>The command query mode. Accepted values: update, content. Mandatory.</dd>
 * <dt>filename</dt><dd>The name of the file to retrieve. Mandatory for mode update and content.</dd>
 * <dt>return</dt><dd>The type of content return. Defined only in when mode=content. Accepted values: url, inline. Inline returns the content in base64. Default value is url.</dt>
 * </dl>
 *
 * @author		Pierrick Caillon <pierrick@in-cite.net>
 * @package		TYPO3
 * @subpackage	ics_od_core_api
 */ 
abstract class tx_icsodcoreapi_api2_AbstractFileCommand extends tx_icsodcoreapi_api2_Command
{
	const EMPTY_MODE_CODE = 100;// ERROR_COMMAND_FIRST_CODE;
	const EMPTY_MODE_TEXT = "The mode should be not empty.";
	const INVALID_MODE_CODE = 101;// ERROR_COMMAND_FIRST_CODE + 1;
	const INVALID_MODE_TEXT = "The specified mode is not recognized.";
	const EMPTY_FILENAME_CODE = 102;// ERROR_COMMAND_FIRST_CODE + 2;
	const EMPTY_FILENAME_TEXT = "Please, provide a filename.";
	const FILENOTFOUND_CODE = 103; //ERROR_COMMAND_FIRST_CODE + 3;
	const FILENOTFOUND_TEXT = 'The specified file was not found.';
	const EMPTY_RETURN_CODE = 104;// ERROR_COMMAND_FIRST_CODE + 4;
	const EMPTY_RETURN_TEXT = "The specified return type should be not empty or unspecified.";
	const INVALID_RETURN_CODE = 105;// ERROR_COMMAND_FIRST_CODE + 5;
	const INVALID_RETURN_TEXT = "The specified return type is not recognized.";
	const NOHANDLER_CODE = 106;// ERROR_COMMAND_FIRST_CODE + 6;
	const NOHANDLER_TEXT = "The mode is valid but no handling has been done.";

	/**
	 * The default parameters values.
	 */
	var $params = array(
		'mode' => '',
		'filename' => '',
		'return' => 'url',
	);
	
	/**
	 * The valid modes values.
	 */
	var $modes = array(
		'update',
		'content',
	);
	
	/**
	 * The modes that requires the filename parameter.
	 */
	var $filenameModes = array(
		'update',
		'content',
	);

	/**
	 * The valid return types.
	 */
	static $returns = array(
		'url',
		'inline',
	);
	
	protected $startValues = array();
	
	/**
	 * Retrieves the enumerator on the command result.
	 * @param	array		$params: The command parameters.
	 * @return	tx_icsodcoreapi_api2_Enumerator		The enumerator on the result.
	 */
	public function getResultEnumerator(array $params) {
		$this->checkParameters($params);
		switch ($params['mode']) {
			case 'update':
				$this->startValues['attributes'] = array(
					'update' => date('c', $this->getLastUpdate($params['filename']))
				);
				return new tx_icsodcoreapi_api2_EmptyEnumerator();
			case 'content':
				$content = $this->getFile($params['filename'], $params['return']);
				if ($params['return'] == 'url') {
					$this->startValues['attributes'] = array(
						'url' => $content
					);
				}
				else {
					$this->startValues['content'] = base64_encode($content);
				}
				return new tx_icsodcoreapi_api2_EmptyEnumerator();
			default:
				$enumerator = $this->getCustomResultEnumerator($params);
				if (!$enumerator || !is_a($enumerator, 'tx_icsodcoreapi_api2_Enumerator')) {
					throw new tx_icsodcoreapi_api2_Exception('tx_icsopenddataapi_abstract_file_command2::NOHANDLER');
				}
				return $enumerator;
		}
	}
	
	protected function checkParameters(array &$params) {
		foreach ($this->params as $key => $value) {
			if (!isset($params[$key])) {
				$params[$key] = $value;
			}
		}
		if (empty($params['mode'])) throw new tx_icsodcoreapi_api2_Exception('tx_icsopenddataapi_abstract_file_command2::EMPTY_MODE');
		if (!in_array($params['mode'], $this->modes)) throw new tx_icsodcoreapi_api2_Exception('tx_icsopenddataapi_abstract_file_command2::INVALID_MODE');
		if (in_array($params['mode'], $this->filenameModes)) {
			if (empty($params['filename'])) throw new tx_icsodcoreapi_api2_Exception('tx_icsopenddataapi_abstract_file_command2::EMPTY_FILENAME');
			if (!$this->isFile($params['filename'])) throw new tx_icsodcoreapi_api2_Exception('tx_icsopenddataapi_abstract_file_command2::FILENOTFOUND');
		}
		if ($params['mode'] == 'content') {
			if (empty($params['return'])) throw new tx_icsodcoreapi_api2_Exception('tx_icsopenddataapi_abstract_file_command2::EMPTY_RETURN');
			if (!in_array($params['return'], tx_icsopenddataapi_abstract_file_command2::$returns)) throw new tx_icsodcoreapi_api2_Exception('tx_icsopenddataapi_abstract_file_command2::INVALID_RETURN');
		}
	}

	/**
	 * Starts the result output.
	 * Includes attributes and content defined in $startValues.
	 * @param	tx_icsodcoreapi_api2_Writer		$writer: The output writer.
	 * @return	void
	 */
	public function start(tx_icsodcoreapi_api2_Writer $writer) {
		$writer->startElement('data');
		if (isset($this->startValues['attributes'])) {
			foreach ($this->startValues['attributes'] as $name => $value) {
				$writer->writeAttribute($name, $value);
			}
		}
		if (isset($this->startValues['content'])) {
			$writer->text($this->startValues['content']);
		}
	}

	/**
	 * Checks if the specified filename maps to a know file.
	 *
	 * @param $filename string The name of the file.
	 * @return boolean Whether filename is valid or not.
	 */
	protected abstract function isFile($filename);

	/**
	 * Retrieves the last modification time of the file.
	 *
	 * @param $filename string The name of the file.
	 * @return int The unix timestamp of the date of the last modification of the file.
	 */
	protected abstract function getLastUpdate($filename);
	
	/**
	 * Retrieves the content or the url to content of the file.
	 *
	 * @param $filename string The name of the file.
	 * @param $return string The type of the return value.
	 * @return string The content or the url to the content of the file.
	 * @see tx_icsopenddataapi_abstract_file_command2::$returns
	 */
	protected abstract function getFile($filename, $return);
	
	/**
	 * Retrieves the enumerator on the command result for the child class custom modes.
	 *
	 * @param $params array The command parameters.
	 * @return	tx_icsodcoreapi_api2_Enumerator		The enumerator on the result. Or <code>null</code> if not valid.
	 */
	protected function getCustomResultEnumerator(array $params) {
		return null;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_openddata_api/api2/class.tx_icsodcoreapi_api2_abstractfilecommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_openddata_api/api2/class.tx_icsodcoreapi_api2_abstractfilecommand.php']);
}
