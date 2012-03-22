<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_icsodcategories_categories'] = array (
	'ctrl' => $TCA['tx_icsodcategories_categories']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,description,parent'
	),
	'feInterface' => $TCA['tx_icsodcategories_categories']['feInterface'],
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
			'label' => 'LLL:EXT:ics_od_categories/locallang_db.xml:tx_icsodcategories_categories.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_od_categories/locallang_db.xml:tx_icsodcategories_categories.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'parent' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_od_categories/locallang_db.xml:tx_icsodcategories_categories.parent',		
			'config' => array (
				'type' => 'select',	
                'items' => array (
                    array('',0),
                ),
				'foreign_table' => 'tx_icsodcategories_categories',	
				'foreign_table_where' => 'AND tx_icsodcategories_categories.pid=###CURRENT_PID### ORDER BY tx_icsodcategories_categories.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'relation' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:ics_od_categories/locallang_db.xml:tx_icsodcategories_categories.relation',        
            'config' => array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'tx_icsodappstore_applications,tx_icsoddatastore_filegroups',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 100,    
                "MM" => "tx_icsodcategories_categories_relation_mm",
            )
        ),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, description, parent,relation')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>