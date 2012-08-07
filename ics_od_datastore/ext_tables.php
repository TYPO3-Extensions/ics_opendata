<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('web_txicsoddatastoreM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		
	t3lib_extMgm::addModule('web', 'txicsoddatastoreM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	if (t3lib_div::int_from_ver(TYPO3_version) >= '4005000') {
		t3lib_extMgm::addNavigationComponent('web_txicsoddatastoreM1', '');
	}
}

include_once(t3lib_extMgm::extPath('ics_od_datastore') . 'lib/class.tx_icsoddatastore_title.php');

$TCA['tx_icsoddatastore_filegroups'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_filegroups',		
		'label'     => 'title',
		'label_userFunc' => 'tx_icsoddatastore_tcahelper->getRecordTitle',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_filegroups.gif',
	),
);

$TCA['tx_icsoddatastore_fileformats'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_fileformats',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		//'default_sortby' => 'ORDER BY name',	
		'delete' => 'deleted',
		'sortby' => 'sorting',
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_fileformats.gif',
	),
);


$TCA['tx_icsoddatastore_licences'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_licences',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_licences.gif',
	),
);

$TCA['tx_icsoddatastore_downloads'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_downloads',		
		'label'     => 'filegroup',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY filegroup',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_downloads.gif',
	),
);

$TCA['tx_icsoddatastore_files'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_files',		
		'label'     => 'file',
		'label_alt' => 'url',
		'label_userFunc' => 'tx_icsoddatastore_title->getRecordTitle',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'record_type',
		'default_sortby' => 'ORDER BY format',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_files.gif',
	),
);

$TCA['tx_icsoddatastore_tiers'] = array (
    'ctrl' => array (
        'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_tiers',        
        'label'     => 'name',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY name',    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_tiers.gif',
    ),
);

$TCA['tx_icsoddatastore_filetypes'] = array(
	'ctrl'	=> array(
        'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_filetypes',        
        'label'     => 'name',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY name',    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_filetypes.gif',
	)
);

$TCA['tx_icsoddatastore_monthdownloads'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_monthdownloads',		
		'label'     => 'filegroup',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY filegroup',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_monthdownloads.gif',
	),
);

$TCA['tx_icsoddatastore_statistics'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_statistics',		
		'label'     => 'filegroup',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY filegroup',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsoddatastore_statistics.gif',
	),
);


// Add fe_groups filemount field
$filemountCol = array(
	'tx_icsoddatastore_filemount' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:ics_od_datastore/locallang_db.xml:tx_icsoddatastore_filemount',
		'config' => array(
			'type' => 'input',
			'size' => '50',
			'eval' => 'trim',
		),
	),
);
t3lib_div::loadTCA('fe_groups');
t3lib_extMgm::addTCAcolumns('fe_groups',$filemountCol,1);
t3lib_extMgm::addToAllTCAtypes('fe_groups','tx_icsoddatastore_filemount');

// Add fe_users tiers relation
$tiersCol = array(
	'tx_icsoddatastore_tiers' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:ics_od_datastore/locallang_db.xml:fe_users.tx_icsoddatastore_tiers',
		'config' => array (
			'type' => 'select',
			'foreign_table' => 'tx_icsoddatastore_tiers',
			'foreign_table_where' => 'ORDER BY tx_icsoddatastore_tiers.uid',
			'size' => 10,	
			'minitems' => 0,
			'maxitems' => 100,
			'MM' => 'tx_icsoddatastore_feusers_tiers_mm',
			'MM_opposite_field' => 'fe_edit',
		)
	),
);
t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tiersCol,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','--div--;LLL:EXT:ics_od_datastore/locallang_db.xml:tab_datastore, tx_icsoddatastore_tiers');


t3lib_div::loadTCA('tt_content');
$TCA['pages']['columns']['module']['config']['items'][] = array('LLL:EXT:ics_od_datastore/locallang.xml:sysfolder', 'datastore');

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';

$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem']='CType;;4;button;1-1-1, header;;3;;2-2-2,pi_flexform;;;;1-1-1'; // new! flexform

// you add pi_flexform to be renderd when your plugin is shown
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';                  // new! flexform
 
 // NOTE: Be sure to change ''sampleflex'' to the correct directory name of your extension!                   // new! flexform
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:ics_od_datastore/flexform_ds_pi1.xml');             // new! flexform

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ics_od_datastore/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Data Store");

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icsoddatastore_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_icsoddatastore_pi1_wizicon.php';
}


// Plugin RSS
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ics_od_datastore/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icsoddatastore_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_icsoddatastore_pi2_wizicon.php';
}
t3lib_extMgm::addStaticFile($_EXTKEY,"pi2/static/","Data Store RSS");


// Stats download
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ics_od_datastore/locallang_db.xml:tt_content.list_type_pi3',
	$_EXTKEY . '_pi3',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icsoddatastore_pi3_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi3/class.tx_icsoddatastore_pi3_wizicon.php';
}
t3lib_extMgm::addStaticFile($_EXTKEY,"pi3/static/","Datastore statistics");

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi3']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi3', 'FILE:EXT:ics_od_datastore/flexform_ds_pi3.xml');




if (t3lib_extMgm::isLoaded('ics_tcafe_admin'))
	t3lib_extMgm::addStaticFile($_EXTKEY,"hook/static/","Datastore hook");

?>