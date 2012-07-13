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
 * A writer for XML output.
 * @author		Pierrick Caillon <pierrick@in-cite.net>
 * @package		TYPO3
 * @subpackage	ics_od_core_api
 */
class tx_icsodcoreapi_api2_XmlWriter implements tx_icsodcoreapi_api2_Writer, tx_icsodcoreapi_api2_DocumentWriter {
	private $writer = null;	/**< The internal XML writer object. */
	public $indent = false;	/**< Activate indentation for human reading. */
	private $charset = 'utf-8';	/**< The current input charset. */
	
	/**
	 * Initializes the writer on the specified source URI.
	 * @param	string		$uri: The source URI.
	 * @return	void
	 */
	public function __construct($uri) {
		$this->writer = XMLWriter::openURI($uri);
	}
	
	public function setSourceCharset($charset) {
		$this->charset = $charset;
	}
	
	public function start($rootElementName) {
		$this->writer->startDocument('1.0', 'utf-8', 'yes');
		$this->writer->setIndent($this->indent);
		if ($this->indent) {
			$this->writer->setIndentString("\t");
		}
		$this->writer->startElement($rootElementName);
	}
	
	public function end() {
		$this->writer->endElement();
		$this->writer->endDocument();
		$this->wrtier->flush();
	}

	public function writeElement($name, $content = '') {
		if ($content && ($this->charset != 'utf-8')) $content = iconv($this->charset, 'utf-8', $content);
		return $this->writer->writeElement($name, $content);
	}
	
	public function startElement($name) {
		return $this->writer->startElement($name);
	}
	
	public function endElement() {
		return $this->writer->endElement();
	}
	
	public function writeAttribute($name, $value) {
		if ($value && ($this->charset != 'utf-8')) $value = iconv($this->charset, 'utf-8', $value);
		return $this->writer->writeAttribute($name, $value);
	}
	
	public function startAttribute($name) {
		return $this->writer->startAttribute($name);
	}
	
	public function endAttribute() {
		return $this->writer->endAttribute();
	}
	
	public function text($content) {
		if ($content && ($this->charset != 'utf-8')) $content = iconv($this->charset, 'utf-8', $content);
		return $this->writer->text($content);
	}
	
	public function writeCData($content) {
		if ($content && ($this->charset != 'utf-8')) $content = iconv($this->charset, 'utf-8', $content);
		return $this->writer->writeCData($content);
	}
	
	public function startCData() {
		return $this->writer->startCData();
	}
	
	public function endCData() {
		return $this->writer->endCData();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_core_api/api2/class.tx_icsodcoreapi_api2_xmlwriter.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_od_core_api/api2/class.tx_icsodcoreapi_api2_xmlwriter.php']);
}
