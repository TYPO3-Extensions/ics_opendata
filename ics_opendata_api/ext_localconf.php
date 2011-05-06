<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_icsopendataapi_pi1.php', '_pi1', 'list_type', 0);


t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_icsopendataapi_pi2.php', '_pi2', 'list_type', 0);


t3lib_extMgm::addPItoST43($_EXTKEY, 'pi3/class.tx_icsopendataapi_pi3.php', '_pi3', 'list_type', 0);


t3lib_extMgm::addPItoST43($_EXTKEY, 'pi4/class.tx_icsopendataapi_pi4.php', '_pi4', 'list_type', 0);


$TYPO3_CONF_VARS['FE']['eID_include']['ics_opendata_api'] = 'EXT:ics_opendata_api/api/tx_icsopendataapi_client.php';
?>