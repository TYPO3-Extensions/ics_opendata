<?php defined('TYPO3_MODE') || die('Access denied.');

$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['smile_icsoddatastore_license']);
if ($confArr['enable.']['appstore']) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderForm'][] = 'EXT:smile_icsoddatastore_license/hooks/class.tx_smileicsoddatastorelicense_applicationFieldsRenderForm.php:tx_smileicsoddatastorelicense_applicationFieldsRenderForm';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_appstore']['applicationFieldsRenderControls'][] = 'EXT:smile_icsoddatastore_license/hooks/class.tx_smileicsoddatastorelicense_applicationFieldsRenderControls.php:tx_smileicsoddatastorelicense_applicationFieldsRenderControls';
}
if ($confArr['enable.']['datastore']) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ics_od_datastore']['additionalFieldsMarkers'][] = 'EXT:smile_icsoddatastore_license/hooks/class.tx_smileicsoddatastorelicense_additionalFieldsMarkers.php:tx_smileicsoddatastorelicense_additionalFieldsMarkers';
	
	$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/tx_smileicsoddatastorelicense_filelink.php';
}
?>