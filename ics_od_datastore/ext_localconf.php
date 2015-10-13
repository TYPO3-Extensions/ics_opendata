<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_icsoddatastore_pi1.php', '_pi1', 'list_type', 0);

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_icsoddatastore_pi2.php', '_pi2', 'list_type', 0);

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi3/class.tx_icsoddatastore_pi3.php', '_pi3', 'list_type', 0);


$TYPO3_CONF_VARS['FE']['eID_include']['ics_od_datastoredownload'] = 'EXT:ics_od_datastore/downloadfile/class.tx_icsoddatastore_download.php';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_icsoddatastore_task'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'Logs file download',
    'description'      => 'Logs file download',
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_icsoddatastore_synchroTask'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'Synchronize the update files',
    'description'      => 'Synchronize the update files',
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_icsoddatastore_processFilesTask'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'Synchronize md5 and file size',
    'description'      => 'Synchronize md5 and file size',
);

// --- Hook

if (t3lib_extMgm::isLoaded('ics_tcafe_admin')) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['startOff'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['process_afterInit'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['user_TCAFEAdmin'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['renderValue'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['renderEntries'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['handleFormField'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['process_valueToDB'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['formRenderer_additionnalMarkers'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['handleFormField_typeSelect'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['getSelectItemArray'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['actions_additionnalDataArray'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['single_additionnalMarkers'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['extra_controlEntries'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['controlEntry'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['deleteRecord'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_tcafe_admin']['getRecords'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_TCAFEAdmin.php:tx_icsoddatastore_TCAFEAdmin';
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['additionalFieldsMarkers'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_stats_hook.php:tx_icsoddatastore_stats_hook';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['addSearchRestriction'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_stats_hook.php:tx_icsoddatastore_stats_hook';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['selectQuery_extraColumnSorting'][] = 'EXT:ics_od_datastore/hook/class.tx_icsoddatastore_stats_hook.php:tx_icsoddatastore_stats_hook';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:ics_od_datastore/lib/class.tx_icsoddatastore_tcemain_hook.php:tx_icsoddatastore_tcemain_hook';

//--- API commands ---


// --- datastore_getagencies
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['datastore_getagencies'] = 'EXT:ics_od_datastore/opendata/class.tx_icsoddatastore_datastore_getagencies_command.php:tx_icsoddatastore_datastore_getagencies_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasource']['agency'] = 'EXT:ics_od_datastore/opendata/datasource/class.tx_icsoddatastore_agency_datasource.php:tx_icsoddatastore_agency_datasource';
// --- datastore_getlicences
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['datastore_getlicences'] = 'EXT:ics_od_datastore/opendata/class.tx_icsoddatastore_datastore_getlicences_command.php:tx_icsoddatastore_datastore_getlicences_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasource']['licence'] = 'EXT:ics_od_datastore/opendata/datasource/class.tx_icsoddatastore_licence_datasource.php:tx_icsoddatastore_licence_datasource';
// --- datastore_getfileformats
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['datastore_getfileformats'] = 'EXT:ics_od_datastore/opendata/class.tx_icsoddatastore_datastore_getfileformats_command.php:tx_icsoddatastore_datastore_getfileformats_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasource']['fileformat'] = 'EXT:ics_od_datastore/opendata/datasource/class.tx_icsoddatastore_fileformat_datasource.php:tx_icsoddatastore_fileformat_datasource';
// --- datastore_gettiers
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['datastore_gettiers'] = 'EXT:ics_od_datastore/opendata/class.tx_icsoddatastore_datastore_gettiers_command.php:tx_icsoddatastore_datastore_gettiers_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasource']['tiers'] = 'EXT:ics_od_datastore/opendata/datasource/class.tx_icsoddatastore_tiers_datasource.php:tx_icsoddatastore_tiers_datasource';


// --- Datasource connexions for commands datastore_getagencies, datastore_getlicences, datastore_getdatasets, datastore_searchdatasets
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasourceconnect']['typo3db_opendatapkg']['host'] = TYPO3_db_host;
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasourceconnect']['typo3db_opendatapkg']['login'] = TYPO3_db_username;
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasourceconnect']['typo3db_opendatapkg']['password'] = TYPO3_db_password;
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasourceconnect']['typo3db_opendatapkg']['base'] = TYPO3_db;

// *************************
// * User inclusions typo3db_opendatapkg connexion
// * DO NOT DELETE OR CHANGE THOSE COMMENTS
// *************************

// ... (Add additional operations here) ...
// --- datastore_getdatasets, datastore_searchdatasets
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['datastore_getdatasets'] = 'EXT:ics_od_datastore/opendata/class.tx_icsoddatastore_datastore_getdatasets_command.php:tx_icsoddatastore_datastore_getdatasets_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_core_api']['command']['1.0']['datastore_searchdatasets'] = 'EXT:ics_od_datastore/opendata/class.tx_icsoddatastore_datastore_searchdatasets_command.php:tx_icsoddatastore_datastore_searchdatasets_command';
$TYPO3_CONF_VARS['EXTCONF']['ics_od_datastore']['datasource']['dataset'] = 'EXT:ics_od_datastore/opendata/datasource/class.tx_icsoddatastore_dataset_datasource.php:tx_icsoddatastore_dataset_datasource';

// * End user inclusions typo3db_opendatapkg connexion


?>
