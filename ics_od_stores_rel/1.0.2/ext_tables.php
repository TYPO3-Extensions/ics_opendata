<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$tempColumns = array (
	'tx_icsodstoresrel_filegroup' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:ics_od_stores_rel/locallang_db.xml:tx_icsodappstore_applications.tx_icsodstoresrel_datastore',		
		'config' => array (
			'type' => 'select',	
			'foreign_table' => 'tx_icsoddatastore_filegroups',	
			'foreign_table_where' => 'ORDER BY tx_icsoddatastore_filegroups.uid',	
			'size' => 5,	
			'minitems' => 1,
			'maxitems' => 100,	
			"MM" => "tx_icsodstoresrel_apps_filegroups_mm",
		)
	),
);


t3lib_div::loadTCA('tx_icsodappstore_applications');
t3lib_extMgm::addTCAcolumns('tx_icsodappstore_applications',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tx_icsodappstore_applications','tx_icsodstoresrel_filegroup;;;;1-1-1');
?>