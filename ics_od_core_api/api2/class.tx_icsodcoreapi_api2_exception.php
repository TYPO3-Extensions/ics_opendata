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
 * Represents an API version 2 exception.
 * It can be directly outputed.
 *
 * @author		Pierrick Caillon <pierrick@in-cite.net>
 * @package		TYPO3
 * @subpackage	ics_od_core_api
 */
class tx_icsodcoreapi_api2_Exception extends Exception implements tx_icsopendata_writeable {
	public function __construct($constantPrefix, Exception $previous = null) {
		$text = defined($constantPrefix . '_TEXT') ? constant($constantPrefix . '_TEXT') : ('Text for ' . $constantPrefix);
		$code = defined($constantPrefix . '_CODE') ? constant($constantPrefix . '_CODE') : -1;
		parent::__construct($text, $code, $previous);
	}
	
	public function writeTo(tx_icsodcoreapi_api2_Writer $writer) {
		$writer->startElement('status');
		$writer->writeAttribute('code', $this->getCode());
		$writer->writeAttribute('message', $this->getMessage());
		$writer->endElement();
	}
}
