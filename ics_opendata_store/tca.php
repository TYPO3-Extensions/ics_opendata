<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_icsopendatastore_filegroups'] = array (
	'ctrl' => $TCA['tx_icsopendatastore_filegroups']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden,title,description,technical_data,files,agency,categories,contact,licence,release_date,update_date,time_period,update_frequency,publisher,creator,manager,owner'
    ),
	'feInterface' => $TCA['tx_icsopendatastore_filegroups']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),	
		'technical_data' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.technical_data',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),	
		/*'files' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.files',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_icsopendatastore_files',	
				'foreign_table_where' => 'AND !EXISTS(SELECT uid_local FROM tx_icsopendatastore_files_filegroup_mm WHERE uid_local = tx_icsopendatastore_files.uid AND uid_foreign <> ###THIS_UID###) ORDER BY tx_icsopendatastore_files.uid',	
				'size' => 5,	
				'minitems' => 1,
				'maxitems' => 20,	
				"MM" => "tx_icsopendatastore_files_filegroup_mm",	
				'MM_opposite_field' => 'filegroup',
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_icsopendatastore_files',
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
							'table' => 'tx_icsopendatastore_files',
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
			)
		),*/
		'agency' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.agency',		
            'config' => array (
                'type' => 'select',
                'items' => array (
                    array('',0),
                ),
                'foreign_table' => 'tx_icsopendatastore_tiers',    
                'foreign_table_where' => 'ORDER BY tx_icsopendatastore_tiers.uid',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_icsopendatastore_tiers',
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
							'table' => 'tx_icsopendatastore_tiers',
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
            )
		),
		'categories' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.categories',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_icsopendatastore_categories',	
				'foreign_table_where' => 'ORDER BY tx_icsopendatastore_categories.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 10,	
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
			)
		),
		'contact' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.contact',		
            'config' => array (
                'type' => 'select',
                'items' => array (
                    array('',0),
                ),
                'foreign_table' => 'tx_icsopendatastore_tiers',    
                'foreign_table_where' => 'ORDER BY tx_icsopendatastore_tiers.uid',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_icsopendatastore_tiers',
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
							'table' => 'tx_icsopendatastore_tiers',
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
            )
		),
		'licence' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.licence',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_icsopendatastore_licences',	
				'foreign_table_where' => 'ORDER BY tx_icsopendatastore_licences.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_icsopendatastore_licences',
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
							'table' => 'tx_icsopendatastore_licences',
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
			)
		),
		'release_date' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.release_date',		
			'config' => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'update_date' => array (
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.update_date',		
			'config' => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'time_period' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.time_period',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'update_frequency' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.update_frequency',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
        'publisher' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.publisher',        
            'config' => array (
                'type' => 'select',
                'items' => array (
                    array('',0),
                ),
                'foreign_table' => 'tx_icsopendatastore_tiers',    
                'foreign_table_where' => 'ORDER BY tx_icsopendatastore_tiers.uid',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_icsopendatastore_tiers',
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
							'table' => 'tx_icsopendatastore_tiers',
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
            )
        ),
        'creator' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.creator',        
            'config' => array (
                'type' => 'select',
                'items' => array (
                    array('',0),
                ),
                'foreign_table' => 'tx_icsopendatastore_tiers',    
                'foreign_table_where' => 'ORDER BY tx_icsopendatastore_tiers.uid',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_icsopendatastore_tiers',
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
							'table' => 'tx_icsopendatastore_tiers',
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
            )
        ),
        'manager' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.manager',        
            'config' => array (
                'type' => 'select',
                'items' => array (
                    array('',0),
                ),
                'foreign_table' => 'tx_icsopendatastore_tiers',    
                'foreign_table_where' => 'ORDER BY tx_icsopendatastore_tiers.uid',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_icsopendatastore_tiers',
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
							'table' => 'tx_icsopendatastore_tiers',
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
            )
        ),
        'owner' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filegroups.owner',        
            'config' => array (
                'type' => 'select', 
                'items' => array (
                    array('',0),
                ),
                'foreign_table' => 'tx_icsopendatastore_tiers',    
                'foreign_table_where' => 'ORDER BY tx_icsopendatastore_tiers.uid',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_icsopendatastore_tiers',
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
							'table' => 'tx_icsopendatastore_tiers',
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
            )
        ),
	),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, description;;;;3-3-3,technical_data, files, agency, categories, contact, licence, release_date, update_date, time_period, update_frequency, publisher, creator, manager, owner')
    ),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_icsopendatastore_fileformats'] = array (
	'ctrl' => $TCA['tx_icsopendatastore_fileformats']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,description,mimetype,extension,searchable,picto'
	),
	'feInterface' => $TCA['tx_icsopendatastore_fileformats']['feInterface'],
	'columns' => array (
		'hidden' => array (        
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (        
			'exclude' => 0,        
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_fileformats.name',        
			'config' => array (
				'type' => 'input',    
				'size' => '30',    
				'eval' => 'required,trim',
			)
		),
		'description' => array (        
			'exclude' => 0,        
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_fileformats.description',        
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
				'eval' => 'trim',
			)
		),
		'mimetype' => array (        
			'exclude' => 0,        
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_fileformats.mimetype',        
			'config' => array (
				'type' => 'input',    
				'size' => '30',    
				'max' => '100',    
				'eval' => 'trim',
			)
		),
		'extension' => array (        
			'exclude' => 0,        
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_fileformats.extension',        
			'config' => array (
				'type' => 'input',    
				'size' => '30',    
				'max' => '20',    
				'eval' => 'trim',
			)
		),
		'picto' => array (        
			'exclude' => 0,        
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_fileformats.picto',        
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],    
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],    
				'uploadfolder' => 'uploads/tx_icsopendatastore',
				'size' => 1,    
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
        'searchable' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_fileformats.searchable',        
            'config' => array (
                'type' => 'check',
				'default' => '0',
            )
        ),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, description, mimetype, extension, searchable, picto')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_icsopendatastore_categories'] = array (
	'ctrl' => $TCA['tx_icsopendatastore_categories']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,description,parent'
	),
	'feInterface' => $TCA['tx_icsopendatastore_categories']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_categories.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_categories.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
				'eval' => 'trim',
			)
		),
		'parent' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_categories.parent',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_icsopendatastore_categories',	
				'foreign_table_where' => 'ORDER BY tx_icsopendatastore_categories.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,	
				"MM" => "tx_icsopendatastore_categories_parent_mm",	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
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
				),
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, description, parent')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_icsopendatastore_licences'] = array (
	'ctrl' => $TCA['tx_icsopendatastore_licences']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,link,logo'
	),
	'feInterface' => $TCA['tx_icsopendatastore_licences']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_licences.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
       'link' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_licences.link',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'trim',
            )
        ),
       'logo' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_licences.logo',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'trim',
            )
        ),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name,link,logo')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



/*$TCA['tx_icsopendatastore_agencies'] = array (
	'ctrl' => $TCA['tx_icsopendatastore_agencies']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name'
	),
	'feInterface' => $TCA['tx_icsopendatastore_agencies']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_agencies.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
*/


$TCA['tx_icsopendatastore_downloads'] = array (
	'ctrl' => $TCA['tx_icsopendatastore_downloads']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,filegroup,ip,file'
	),
	'feInterface' => $TCA['tx_icsopendatastore_downloads']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'filegroup' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_downloads.filegroup',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_icsopendatastore_filegroups',	
				'foreign_table_where' => 'ORDER BY tx_icsopendatastore_filegroups.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'ip' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_downloads.ip',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'max' => '40',	
				'eval' => 'trim',
			)
		),
		'file' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_downloads.file',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_icsopendatastore_files',	
				'foreign_table_where' => 'ORDER BY tx_icsopendatastore_files.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, filegroup, ip, file')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_icsopendatastore_tiers'] = array (
    'ctrl' => $TCA['tx_icsopendatastore_tiers']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden,name,description,email,website,logo,address,zipcode,city,country,latitude,longitude'
    ),
    'feInterface' => $TCA['tx_icsopendatastore_tiers']['feInterface'],
    'columns' => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.name',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'required,trim',
            )
        ),
        'description' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.description',        
            'config' => array (
                'type' => 'text',
                'cols' => '30',    
                'rows' => '5',
            )
        ),
        'email' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.email',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',
            )
        ),
		'website' => array(
            'exclude' => 1,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.website',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'trim',
			)
		),
		'logo' => array(
            'exclude' => 1,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.logo',    
			'config' => array(
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'trim',
			)
		),
		'address' => array(
            'exclude' => 1,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.address',        
			'config' => array(
                'type' => 'text',    
                'cols' => '20',
				'rows' => '3',
            )
		),
		'zipcode' => array(
            'exclude' => 1,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.zipcode',    
			'config' => array(
                'type' => 'input',    
                'size' => '10',    
                'eval' => 'trim',
			)
		),
		'city' => array(
            'exclude' => 1,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.city',    
			'config' => array(
                'type' => 'input',    
                'size' => '20',    
                'eval' => 'trim',
			)
		),
		'country' => array(
            'exclude' => 1,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.country',    
			'config' => array(
                'type' => 'input',    
                'size' => '20',    
                'eval' => 'trim',
			)
		),
		'latitude' => array(
            'exclude' => 1,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.latitude',    
			'config' => array(
                'type' => 'input',    
                'size' => '20', 
                'eval' => 'trim',
			)
		),
		'longitude' => array(
            'exclude' => 1,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_tiers.longitude',    
			'config' => array(
                'type' => 'input',    
                'size' => '20',    
                'eval' => 'trim',
			)
		),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, name, description, email, website,logo,address,zipcode,city,country,latitude,longitude')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);



$TCA['tx_icsopendatastore_files'] = array (
	'ctrl' => $TCA['tx_icsopendatastore_files']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,record_type,file,url,type,format,filegroup,md5'
	),
	'feInterface' => $TCA['tx_icsopendatastore_files']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'record_type' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.record_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.record_type.0', 0),
					array('LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.record_type.1', 1),
				),
			)
		),
		/*'file' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.file',
			'config' => array (
				'type' => 'user',
				'userFunc' => 'tx_icsopendatastore_filecontrol->makeControl',
				'internal_type' => 'file_reference',
				'allowed' => '',	
				'disallowed' => 'php,php3',	
				'uploadfolder' => 'uploads/tx_icsopendatastore',
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'show_thumbs' => 1,	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
				'disable_controls' => 'browser,list',
			)
		),*/
        'file' => array (        
			//'displayCond' => 'FIELD:record_type:=:0',
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.file',
            'config' => array (
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => '',    
                'disallowed' => 'php,php3',    
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],    
                'uploadfolder' => 'uploads/tx_icsopendatastore',
                'size' => 1,    
                'minitems' => 1,
                'maxitems' => 1,
            )
        ),
        'url' => array (        
			//'displayCond' => 'FIELD:record_type:=:1',
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.url',        
            'config' => array (
                'type' => 'input',
                'cols' => '100',    
                'rows' => '1',
				'eval' => 'required',
            )
        ),
		'format' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.format',		
			'config' => array (
				'type' => 'select',	
					'items' => array (
					//array('',0),
				),
				'foreign_table' => 'tx_icsopendatastore_fileformats',	
				'foreign_table_where' => 'ORDER BY tx_icsopendatastore_fileformats.uid',	
				'size' => 1,	
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'type' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.type',		
			'config' => array (
				'type' => 'select',	
					'items' => array (
					//array('',0),
				),
				'foreign_table' => 'tx_icsopendatastore_filetypes',	
				'foreign_table_where' => 'ORDER BY tx_icsopendatastore_filetypes.uid',	
				'size' => 1,	
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'filegroup' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.filegroup',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_icsopendatastore_filegroups',	
				'foreign_table_where' => 'ORDER BY tx_icsopendatastore_filegroups.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,	
				"MM" => "tx_icsopendatastore_files_filegroup_mm",	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'list' => array(
						'type'   => 'script',
						'title'  => 'List',
						'icon'   => 'list.gif',
						'params' => array(
							'table' => 'tx_icsopendatastore_filegroups',
							'pid'   => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
				),
			)
		),
        'md5' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_files.md5',        
            'config' => array (
                'type' => 'input',
                'size' => '30',    
            )
        ),
	),
	'types' => array (
		//'0' => array('showitem' => 'hidden;;1;;1-1-1, record_type, file, url, type, format, filegroup')
		'0' => array('showitem' => 'hidden;;;;1-1-1, record_type;;;;2-2-2, file, type, format, filegroup, md5'),
		'1' => array('showitem' => 'hidden;;;;1-1-1, record_type;;;;2-2-2, url, type, format, filegroup, md5')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_icsopendatastore_filetypes'] = array (
    'ctrl' => $TCA['tx_icsopendatastore_filetypes']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden,name,description'
    ),
    'feInterface' => $TCA['tx_icsopendatastore_filetypes']['feInterface'],
    'columns' => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filetypes.name',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'required,trim',
            )
        ),
        'description' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_opendata_store/locallang_db.xml:tx_icsopendatastore_filetypes.description',        
            'config' => array (
                'type' => 'text',
                'cols' => '30',    
                'rows' => '5',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, name, description')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);
?>