<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

//--- API commands ---


// --- datastore_getagencies
$TYPO3_CONF_VARS['EXTCONF']['ics_opendata_api']['command']['1.0']['datastore_getagencies'] = 'EXT:user_datastore/opendata/class.tx_userdatastore_datastore_getagencies_command.php:tx_userdatastore_datastore_getagencies_command';
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasource']['agency'] = 'EXT:user_datastore/opendata/datasource/class.tx_userdatastore_agency_datasource.php:tx_userdatastore_agency_datasource';
// --- datastore_getlicences
$TYPO3_CONF_VARS['EXTCONF']['ics_opendata_api']['command']['1.0']['datastore_getlicences'] = 'EXT:user_datastore/opendata/class.tx_userdatastore_datastore_getlicences_command.php:tx_userdatastore_datastore_getlicences_command';
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasource']['licence'] = 'EXT:user_datastore/opendata/datasource/class.tx_userdatastore_licence_datasource.php:tx_userdatastore_licence_datasource';
// --- datastore_getcategories
$TYPO3_CONF_VARS['EXTCONF']['ics_opendata_api']['command']['1.0']['datastore_getcategories'] = 'EXT:user_datastore/opendata/class.tx_userdatastore_datastore_getcategories_command.php:tx_userdatastore_datastore_getcategories_command';
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasource']['category'] = 'EXT:user_datastore/opendata/datasource/class.tx_userdatastore_category_datasource.php:tx_userdatastore_category_datasource';
// --- datastore_getfileformats
$TYPO3_CONF_VARS['EXTCONF']['ics_opendata_api']['command']['1.0']['datastore_getfileformats'] = 'EXT:user_datastore/opendata/class.tx_userdatastore_datastore_getfileformats_command.php:tx_userdatastore_datastore_getfileformats_command';
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasource']['fileformat'] = 'EXT:user_datastore/opendata/datasource/class.tx_userdatastore_fileformat_datasource.php:tx_userdatastore_fileformat_datasource';


// --- Datasource connexions for commands datastore_getagencies, datastore_getlicences, datastore_getcategories, datastore_getdatasets, datastore_searchdatasets
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasourceconnect']['typo3db_opendatapkg']['host'] = 'localhost';
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasourceconnect']['typo3db_opendatapkg']['login'] = 'opendatapkg';
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasourceconnect']['typo3db_opendatapkg']['password'] = 'opendatapkg';
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasourceconnect']['typo3db_opendatapkg']['base'] = 'opendatapkg';

// *************************
// * User inclusions typo3db_opendatapkg connexion
// * DO NOT DELETE OR CHANGE THOSE COMMENTS
// *************************

// ... (Add additional operations here) ...
// --- datastore_getdatasets, datastore_searchdatasets
$TYPO3_CONF_VARS['EXTCONF']['ics_opendata_api']['command']['1.0']['datastore_getdatasets'] = 'EXT:user_datastore/opendata/class.tx_userdatastore_datastore_getdatasets_command.php:tx_userdatastore_datastore_getdatasets_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_opendata_api']['command']['1.0']['datastore_searchdatasets'] = 'EXT:user_datastore/opendata/class.tx_userdatastore_datastore_searchdatasets_command.php:tx_userdatastore_datastore_searchdatasets_command';
$TYPO3_CONF_VARS['EXTCONF']['user_datastore']['datasource']['dataset'] = 'EXT:user_datastore/opendata/datasource/class.tx_userdatastore_dataset_datasource.php:tx_userdatastore_dataset_datasource';

// * End user inclusions typo3db_opendatapkg connexion

// --- Hook AddMarkers Categories

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_opendata_api']['applicationFieldsRenderControls'][] = 'EXT:user_datastore/hook/class.user_datastore_applicationCategories.php:user_datastore_applicationCategories';
// --- Hook AddMarkers Categories
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_opendata_api']['applicationFieldsRenderControls'][] = 'EXT:user_datastore/hook/class.user_datastore_applicationDatasets.php:user_datastore_applicationDatasets';


