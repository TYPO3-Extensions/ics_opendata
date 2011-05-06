<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ics_opendata_api/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icsopendataapi_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_icsopendataapi_pi1_wizicon.php';
}


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ics_opendata_api/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icsopendataapi_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_icsopendataapi_pi2_wizicon.php';
}


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ics_opendata_api/locallang_db.xml:tt_content.list_type_pi3',
	$_EXTKEY . '_pi3',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icsopendataapi_pi3_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi3/class.tx_icsopendataapi_pi3_wizicon.php';
}


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi4']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ics_opendata_api/locallang_db.xml:tt_content.list_type_pi4',
	$_EXTKEY . '_pi4',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icsopendataapi_pi4_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi4/class.tx_icsopendataapi_pi4_wizicon.php';
}


$TCA['tx_icsopendataapi_applications'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications',		
		'label'     => 'application',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'fe_cruser_id' => 'fe_cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsopendataapi_applications.gif',
	),
);

$TCA['tx_icsopendataapi_logs'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_logs',		
		'label'     => 'crdate',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsopendataapi_logs.gif',
	),
);

$TCA['tx_icsopendataapi_month_logs'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_month_logs',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsopendataapi_month_logs.gif',
	),
);

$TCA['tx_icsopendataapi_statistics'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_statistics',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsopendataapi_statistics.gif',
	),
);


if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('tools_txicsopendataapiM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		
	t3lib_extMgm::addModule('tools', 'txicsopendataapiM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}
?>
