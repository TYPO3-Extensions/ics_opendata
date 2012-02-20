<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
// *************************
// * User inclusions 0
// * DO NOT DELETE OR CHANGE THOSE COMMENTS
// *************************

// ... (Add additional operations here) ...

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_icsodappstore_pi1.php', '_pi1', 'list_type', 0);


t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_icsodappstore_pi2.php', '_pi2', 'list_type', 0);


t3lib_extMgm::addPItoST43($_EXTKEY, 'pi3/class.tx_icsodappstore_pi3.php', '_pi3', 'list_type', 0);


t3lib_extMgm::addPItoST43($_EXTKEY, 'pi4/class.tx_icsodappstore_pi4.php', '_pi4', 'list_type', 0);

// * End user inclusions 0


//--- API commands start---


// --- getapplications
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['getapplications'] = 'EXT:ics_od_appstore/opendata/class.tx_icsodappstore_getapplications_command.php:tx_icsodappstore_getapplications_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_appstore']['datasource']['application'] = 'EXT:ics_od_appstore/opendata/datasource/class.tx_icsodappstore_application_datasource.php:tx_icsodappstore_application_datasource';
// --- searchapplications
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['searchapplications'] = 'EXT:ics_od_appstore/opendata/class.tx_icsodappstore_searchapplications_command.php:tx_icsodappstore_searchapplications_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_appstore']['datasource']['application'] = 'EXT:ics_od_appstore/opendata/datasource/class.tx_icsodappstore_application_datasource.php:tx_icsodappstore_application_datasource';
// --- getplatforms
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['getplatforms'] = 'EXT:ics_od_appstore/opendata/class.tx_icsodappstore_getplatforms_command.php:tx_icsodappstore_getplatforms_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_appstore']['datasource']['platform'] = 'EXT:ics_od_appstore/opendata/datasource/class.tx_icsodappstore_platform_datasource.php:tx_icsodappstore_platform_datasource';
// --- getauthors
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['getauthors'] = 'EXT:ics_od_appstore/opendata/class.tx_icsodappstore_getauthors_command.php:tx_icsodappstore_getauthors_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_appstore']['datasource']['author'] = 'EXT:ics_od_appstore/opendata/datasource/class.tx_icsodappstore_author_datasource.php:tx_icsodappstore_author_datasource';


//--- API commands end---

// *************************
// * User inclusions 1
// * DO NOT DELETE OR CHANGE THOSE COMMENTS
// *************************

// ... (Add additional operations here) ...
unset($TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['getapplications']);
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['appstore_getapplications'] = 'EXT:ics_od_appstore/opendata/class.tx_icsodappstore_getapplications_command.php:tx_icsodappstore_getapplications_command';
unset($TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['searchapplications']);
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['appstore_searchapplications'] = 'EXT:ics_od_appstore/opendata/class.tx_icsodappstore_searchapplications_command.php:tx_icsodappstore_searchapplications_command';
unset($TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['getplatforms']);
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['appstore_getplatforms'] = 'EXT:ics_od_appstore/opendata/class.tx_icsodappstore_getplatforms_command.php:tx_icsodappstore_getplatforms_command';
unset($TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['getauthors']);
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['appstore_getauthors'] = 'EXT:ics_od_appstore/opendata/class.tx_icsodappstore_getauthors_command.php:tx_icsodappstore_getauthors_command';

// * End user inclusions 1
