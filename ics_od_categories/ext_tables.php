<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$TCA['tx_icsodcategories_categories'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ics_od_categories/locallang_db.xml:tx_icsodcategories_categories',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'sortby' => 'sorting',
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_icsodcategories_categories.gif',
	),
);


$tempColumns = array (
	'tx_icsodcategories_categories' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:ics_od_categories/locallang_db.xml:tx_icsodcategories_categories.relation_opposite',		
		'config' => array (
			'type' => 'select',		
			'foreign_table' => 'tx_icsodcategories_categories',	
			'foreign_table_where' => 'ORDER BY tx_icsodcategories_categories.name',
			'size' => 10,	
			'minitems' => 0,
			'maxitems' => 100,	
			'MM' => 'tx_icsodcategories_categories_relation_mm',
			'MM_opposite_field' => 'relation',
		)
	),
);

t3lib_extMgm::addStaticFile($_EXTKEY,"static/","OpenData Categories");
if (t3lib_extMgm::isLoaded('ics_od_datastore')) {
	t3lib_div::loadTCA('tx_icsoddatastore_filegroups');
	t3lib_extMgm::addTCAcolumns('tx_icsoddatastore_filegroups',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('tx_icsoddatastore_filegroups','tx_icsodcategories_categories;;;;1-1-1');
	
}

if (t3lib_extMgm::isLoaded('ics_od_appstore')) { 
	t3lib_div::loadTCA('tx_icsodappstore_applications');
	t3lib_extMgm::addTCAcolumns('tx_icsodappstore_applications',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('tx_icsodappstore_applications','tx_icsodcategories_categories;;;;1-1-1');
}



t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
    'LLL:EXT:ics_od_categories/locallang_db.xml:tt_content.list_type_pi1',
    $_EXTKEY . '_pi1',
    t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE === 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icsodcategories_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'pi1/class.tx_icsodcategories_pi1_wizicon.php';
}
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:ics_od_categories/flexform_ds_pi1.xml');

?>