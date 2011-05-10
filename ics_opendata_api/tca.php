<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_icsopendataapi_applications'] = array (
	'ctrl' => $TCA['tx_icsopendataapi_applications']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,key_appli,fe_cruser_id,application,description,platform,count_use,max,publication_date,logo,screenshot,link,update_date,lock_publication'
	),
	'feInterface' => $TCA['tx_icsopendataapi_applications']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'key_appli' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.key_appli',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'max' => '15',	
				'eval' => 'required,trim',
			)
		),
		'application' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.application',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '15',
			)
		),
		'platform' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.platform',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'count_use' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.count_use',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'int',
			)
		),
		'max' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.max',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'int',
			)
		),
		'publication_date' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.publication_date',		
			'config' => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'publish' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.publish',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'logo' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.logo',		
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg',	
				'max_size' => 20,	
				'uploadfolder' => 'uploads/tx_icsopendataapi',
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'screenshot' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.screenshot',		
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg',	
				'max_size' => 400,	
				'uploadfolder' => 'uploads/tx_icsopendataapi',
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 3,
			)
		),
		'link' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.link',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'update_date' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.update_date',		
			'config' => array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'lock_publication' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.lock_publication',		
			'config' => array (
				'type' => 'radio',
				'items' => array (
					array('LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.lock_publication.I.0', '0'),
					array('LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_applications.lock_publication.I.1', '1'),
				),
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, key_appli, fe_cruser_id, application, description, platform, count_use, max, publication_date, publish, logo, screenshot, link, update_date, lock_publication')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_icsopendataapi_logs'] = array (
	'ctrl' => $TCA['tx_icsopendataapi_logs']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,tx_icsopendataapi_application,ip,cmd'
	),
	'feInterface' => $TCA['tx_icsopendataapi_logs']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'tx_icsopendataapi_application' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_logs.tx_icsopendataapi_application',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'int',
			)
		),
		'ip' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_logs.ip',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'cmd' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_logs.cmd',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, tx_icsopendataapi_application, ip, cmd')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_icsopendataapi_month_logs'] = array (
	'ctrl' => $TCA['tx_icsopendataapi_month_logs']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,tx_icsopendataapi_application,ip,cmd'
	),
	'feInterface' => $TCA['tx_icsopendataapi_month_logs']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'tx_icsopendataapi_application' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_month_logs.tx_icsopendataapi_application',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'int',
			)
		),
		'ip' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_logs.ip',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'cmd' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_month_logs.cmd',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, tx_icsopendataapi_application, ip, cmd')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_icsopendataapi_statistics'] = array (
	'ctrl' => $TCA['tx_icsopendataapi_statistics']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,tx_icsopendataapi_application,cmd,count,date'
	),
	'feInterface' => $TCA['tx_icsopendataapi_statistics']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'tx_icsopendataapi_application' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_statistics.tx_icsopendataapi_application',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'int',
			)
		),
		'cmd' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_statistics.cmd',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'count' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_statistics.count',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'int',
			)
		),
		'date' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_opendata_api/locallang_db.xml:tx_icsopendataapi_statistics.date',		
			'config' => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, tx_icsopendataapi_application, cmd, count, date')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

?>