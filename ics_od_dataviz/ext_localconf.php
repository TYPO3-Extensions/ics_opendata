<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['additionalFieldsMarkers'][] = 'EXT:ics_od_dataviz/hook/class.tx_icsoddataviz_datastore.php:tx_icsoddataviz_datastore';

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/tx_icsoddataviz_getdata.php';
	
// Register cache 'myext_mycache'
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache'] = array();
}
// Define string frontend as default frontend, this must be set with TYPO3 4.5 and below
// and overrides the default variable frontend of 4.6
if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['frontend'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['frontend'] = 't3lib_cache_frontend_StringFrontend';
}
if (t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) < '4006000') {
    // Define database backend as backend for 4.5 and below (default in 4.6)
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['backend'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['backend'] = 't3lib_cache_backend_DbBackend';
    }
    // Define data and tags table for 4.5 and below (obsolete in 4.6)
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['options'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['options'] = array();
    }
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['options']['cacheTable'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['options']['cacheTable'] = 'tx_icsoddataviz_cache';
    }
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['options']['tagsTable'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY . '_cache']['options']['tagsTable'] = 'tx_icsoddataviz_cache_tags';
    }
}

?>