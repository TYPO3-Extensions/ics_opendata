<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

// --- Hook AddMarkers Datasets
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderLabel'][] = 'EXT:ics_od_stores_rel/hook/class.tx_icsodstoresrel_appsDatasets.php:tx_icsodstoresrel_appsDatasets';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderData'][] = 'EXT:ics_od_stores_rel/hook/class.tx_icsodstoresrel_appsDatasets.php:tx_icsodstoresrel_appsDatasets';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderForm'][] = 'EXT:ics_od_stores_rel/hook/class.tx_icsodstoresrel_appsDatasets.php:tx_icsodstoresrel_appsDatasets';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderControls'][] = 'EXT:ics_od_stores_rel/hook/class.tx_icsodstoresrel_appsDatasets.php:tx_icsodstoresrel_appsDatasets';


