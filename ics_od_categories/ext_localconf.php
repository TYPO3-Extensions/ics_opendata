<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

//--- API commands ---


// --- datastore_getagencies
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['od_getcategories'] = 'EXT:ics_od_categories/opendata/class.tx_icsodcategories_od_getcategories_command.php:tx_icsodcategories_od_getcategories_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_categories']['datasource']['category'] = 'EXT:ics_od_categories/opendata/datasource/class.tx_icsodcategories_category_datasource.php:tx_icsodcategories_category_datasource';


// --- Datasource connexions for commands od_getcategories
$TYPO3_CONF_VARS['EXTCONF']['ics_od_categories']['datasourceconnect']['typo3db_opendatapkg']['host'] = TYPO3_db_host;
$TYPO3_CONF_VARS['EXTCONF']['ics_od_categories']['datasourceconnect']['typo3db_opendatapkg']['login'] = TYPO3_db_username;
$TYPO3_CONF_VARS['EXTCONF']['ics_od_categories']['datasourceconnect']['typo3db_opendatapkg']['password'] = TYPO3_db_password;
$TYPO3_CONF_VARS['EXTCONF']['ics_od_categories']['datasourceconnect']['typo3db_opendatapkg']['base'] = TYPO3_db;

// *************************
// * User inclusions typo3db_opendatapkg connexion
// * DO NOT DELETE OR CHANGE THOSE COMMENTS
// *************************

// ... (Add additional operations here) ...
// --- datastore_getdatasets, datastore_searchdatasets

// * End user inclusions typo3db_opendatapkg connexion

// --- Hook AddMarkers Categories

if (t3lib_extMgm::isLoaded('ics_od_datastore')) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['additionalFieldsMarkers'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_datastore.php:tx_icsodcategories_datastore';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['additionalFieldsSearchMarkers'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_datastore.php:tx_icsodcategories_datastore';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['additionalSelectedCriteriaMarkers'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_datastore.php:tx_icsodcategories_datastore';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['addSearchRestriction'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_datastore.php:tx_icsodcategories_datastore';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['additionalFieldsRSSMarkers'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_datastore.php:tx_icsodcategories_datastore';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['renderFilegroupExtraFields'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_datastore.php:tx_icsodcategories_datastore';
}

if (t3lib_extMgm::isLoaded('ics_od_appstore')) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderForm'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_appstore.php:tx_icsodcategories_appstore';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderData'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_appstore.php:tx_icsodcategories_appstore';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderLabel'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_appstore.php:tx_icsodcategories_appstore';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderControls'][] = 'EXT:ics_od_categories/class.tx_icsodcategories_appstore.php:tx_icsodcategories_appstore';
}
?>