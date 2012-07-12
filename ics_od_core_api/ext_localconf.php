<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

$TYPO3_CONF_VARS['FE']['eID_include']['ics_od_api'] = 'EXT:ics_od_core_api/api/tx_icsodcoreapi_client.php';


/// TEST
// t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_icsodcoreapi_pi1.php', '_pi1', 'list_type', 0);
$TYPO3_CONF_VARS['FE']['eID_include']['ics_od_api_xmldoc'] = 'EXT:ics_od_core_api/lib/tx_icsodcoreapi_xmldocFE.php';
?>