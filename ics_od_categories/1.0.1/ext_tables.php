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
?>