<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Popy (popy.dev@gmail.com)
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

require_once(PATH_t3lib . 'class.t3lib_tstemplate.php');
require_once(PATH_tslib . 'class.tslib_fe.php');
require_once(PATH_tslib . 'class.tslib_content.php');

/**
 * EDI for the 'ics_od_core_api' extension
 *
 * @author In Cité Solution <technique@in-cite.net>
 * @package	TYPO3
 * @subpackage	ics_feldap
 *
 * Display xml cmd doc
 *
 */
class tx_icsodcoreapi_xmldocFE	{

	var $params = array(
		'cmd' => '',
	);
	
	function init() {
		$this->feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object     
		tslib_eidtools::connectDB(); //Connect to database
		tslib_fe::includeTCA();
		foreach ($this->params as $gp => $var) {
			if (!is_null(t3lib_div::_GP($gp))) {
				$this->params[$gp] = t3lib_div::_GP($gp);
			}
		}
	}
	
	function main()	{
		$doc = new DOMDocument();
		$xsl = new XSLTProcessor();
		$xsl_path = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . 'typo3conf/ext/ics_od_appstore/opendata/xml_cmddoc/';
		$doc->load( $xsl_path . 'documentationapi.xsl');
		$xsl->importStyleSheet($doc);
		$cmd_path = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . 'typo3conf/ext/ics_od_appstore/opendata/xml_cmddoc/';
		$doc->load($cmd_path . 'searchapplications.xml');
		return $xsl->transformToXML($doc);
	}
	
	function printOutput($output)	{
		echo $output; 	
	}

}

$fob =t3lib_div::makeInstance('tx_icsodcoreapi_xmldocFE');
$fob->init();
$output = $fob->main();
$fob->printOutput($output);
?>