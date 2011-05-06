<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

/*
 * Extends tx_icsopendataapi_applications table
 */
$addColumnArray =array(
	'tx_userdatastore_categories' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:user_datastore/locallang_db.php:tx_icsopendataapi_applications.tx_userdatastore_categories',
		'config'  => array( 		
			'type' => 'select',
			'foreign_table' => 'tx_icsopendatastore_categories',	
			'foreign_table_where' => 'ORDER BY tx_icsopendatastore_categories.uid',
			'size' => 5,	
			'minitems' => 0,
			'maxitems' => 10,
			'MM' => 'tx_userdatastore_categories_applications_mm',
			'wizards' => array(
				'_PADDING'  => 2,
				'_VERTICAL' => 1,
				'add' => array(
					'type'   => 'script',
					'title'  => 'Create new record',
					'icon'   => 'add.gif',
					'params' => array(
						'table'    => 'tx_icsopendatastore_categories',
						'pid'      => '###CURRENT_PID###',
						'setValue' => 'prepend'
					),
					'script' => 'wizard_add.php',
				),
				'list' => array(
					'type'   => 'script',
					'title'  => 'List',
					'icon'   => 'list.gif',
					'params' => array(
						'table' => 'tx_icsopendatastore_categories',
						'pid'   => '###CURRENT_PID###',
					),
					'script' => 'wizard_list.php',
				),
				'edit' => array(
					'type'                     => 'popup',
					'title'                    => 'Edit',
					'script'                   => 'wizard_edit.php',
					'popup_onlyOpenIfSelected' => 1,
					'icon'                     => 'edit2.gif',
					'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
				),
			),
		),
	),
	'tx_userdatastore_filegroups' => array(
		'label'   => 'LLL:EXT:user_datastore/locallang_db.php:tx_icsopendataapi_applications.tx_userdatastore_filegroups',
		'config'  => array( 		
			'type' => 'select',
			'foreign_table' => 'tx_icsopendatastore_filegroups',	
			'foreign_table_where' => 'ORDER BY tx_icsopendatastore_filegroups.uid',	
			'size' => 5,	
			'minitems' => 0,
			'maxitems' => 200,
			'MM' => 'tx_userdatastore_filegroups_applications_mm',
		),
	),
);

t3lib_div::loadTCA('tx_icsopendataapi_applications');
t3lib_extMgm::addTCAcolumns('tx_icsopendataapi_applications', $addColumnArray);
t3lib_extMgm::addToAllTCAtypes('tx_icsopendataapi_applications', 'tx_userdatastore_categories, tx_userdatastore_filegroups');

?>