<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


// --- Source list and associated file
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['sources'] = array(
	'csv' => array(
					'formsourceid' => 'csvsource', 
					'dataclass' => 'EXT:ics_opendata/lib/sources/csv/class.csv_data.php:tx_icsopendata_CsvData',
					'formsource' => 'EXT:ics_opendata/lib/sources/csv/class.csv_formsource.php:&tx_icsopendata_CsvFormSource',
					//'generation' => notice here the datasource class to generate,
					//'connexiongeneration' => notice here the class to generate the connexion to csv file,
				),
	'mysql' => array(
					'formsourceid' => 'mysqlsource', 
					'dataclass' => 'EXT:ics_opendata/lib/sources/mysql/class.mysql_data.php:tx_icsopendata_MysqlData',
					'formsource' => 'EXT:ics_opendata/lib/sources/mysql/class.mysql_formsource.php:&tx_icsopendata_MysqlFormSource',
					'generation' => 'EXT:ics_opendata/lib/sources/mysql/class.mysql_generation.php:&tx_icsopendata_Mysqlgeneration',
					'connexiongeneration' => 'EXT:ics_opendata/lib/sources/mysql/class.mysql_generation.php:&tx_icsopendata_Mysqlgeneration_connexion',
				),
	// typo3db inherite from mysql
	'typo3db' => array(
					'formsourceid' => 'typo3dbsource', 
					'dataclass' => 'EXT:ics_opendata/lib/sources/typo3db/class.typo3db_data.php:tx_icsopendata_Typo3dbData',
					'formsource' => 'EXT:ics_opendata/lib/sources/typo3db/class.typo3db_formsource.php:&tx_icsopendata_Typo3dbFormSource',
					'generation' => 'EXT:ics_opendata/lib/sources/typo3db/class.typo3db_generation.php:&tx_icsopendata_Typo3dbGeneration',
					'connexiongeneration' => 'EXT:ics_opendata/lib/sources/mysql/class.mysql_generation.php:&tx_icsopendata_Mysqlgeneration_connexion',
					// 
					// 'current' => array(
						// 'typo_db_username' => 'typo3db_current_username',
						// 'typo_db_password' => 'typo3db_current_password',
						// 'typo_db_host' => 'typo3db_current_host',
						// 'typo_db' => 'typo3db_current_db',
					// )
				)
);

// --- Filter list
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['filters'] = array(
	'simpleselect' => array(
					'filterid' => 'Simple selection filter',
					'fieldslabel' => array('link1'),
					'paramslabel' => array('param1'),
					'filtersources' => array(
						'mysql' => 'EXT:ics_opendata/lib/sources/mysql/filters/class.mysql_filter_simpleselect.php:tx_icsopendata_mysql_filter_simpleselect',
						'typo3db' => 'EXT:ics_opendata/lib/sources/mysql/filters/class.mysql_filter_simpleselect.php:tx_icsopendata_mysql_filter_simpleselect'
					)
				),
	'limit' => array(
					'filterid' => 'Simple limit',
					'fieldslabel' => array(),
					'paramslabel' => array('limit'),
					'filtersources' => array(
						'mysql' => 'EXT:ics_opendata/lib/sources/mysql/filters/class.mysql_limit.php:tx_icsopendata_mysql_limit'
					)
				),
	'proximity22' => array(
					'filterid' => 'Proximity filter 2-2',
					'fieldslabel' => array('latitude', 'longitude'),
					'paramslabel' => array('latitude', 'longitude'),
					'filtersources' => array(
						'mysql' => 'EXT:ics_opendata/lib/sources/mysql/filters/class.mysql_filter_proximity22.php:tx_icsopendata_mysql_filter_proximity22'
					)
				),
	'proximity22_limit' => array(
					'filterid' => 'Proximity filter 2-2 and limit',
					'fieldslabel' => array('latitude', 'longitude'),
					'paramslabel' => array('latitude', 'longitude', 'limit'),
					'filtersources' => array(
						'mysql' => 'EXT:ics_opendata/lib/sources/mysql/filters/class.mysql_filter_proximity22_limit.php:tx_icsopendata_mysql_filter_proximity22_limit'
					)
				)
);

// --- Type conversion
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['typesalias'] = array(
	'String' => array('string', 'char', 'varchar', 'text', 'blob'),
	'Int' => array('int', 'tinyint'),
	'Double' => array('double', 'float'),
	'Timestamp' => array('timestamp'),
	'Date(String)' => array()
);

$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['types'] = array(
	'String' => array(
					'String' => 'EXT:ics_opendata/lib/data/casting/class.cast_none.php:tx_icsopendata_cast_none',
					'Int' => 'EXT:ics_opendata/lib/data/casting/class.cast_string_toint.php:tx_icsopendata_cast_string_toint',
					'Double' => 'EXT:ics_opendata/lib/data/casting/class.cast_string_todouble.php:tx_icsopendata_cast_string_todouble',
					'Timestamp' => 'EXT:ics_opendata/lib/data/casting/class.cast_string_totimestamp.php:tx_icsopendata_cast_string_totimestamp'
				),
	'Int' => array(
					'String' => 'EXT:ics_opendata/lib/data/casting/class.cast_tostring.php:tx_icsopendata_cast_tostring',
					'Int' => 'EXT:ics_opendata/lib/data/casting/class.cast_none.php:tx_icsopendata_cast_none',
					'Double' => 'EXT:ics_opendata/lib/data/casting/class.cast_string_todouble.php:tx_icsopendata_cast_string_todouble',
					'Timestamp' => 'EXT:ics_opendata/lib/data/casting/class.cast_none.php:tx_icsopendata_cast_none',
				),
	'Double' => array(
					'String' => 'EXT:ics_opendata/lib/data/casting/class.cast_tostring.php:tx_icsopendata_cast_tostring',
					'Int' => 'EXT:ics_opendata/lib/data/casting/class.cast_string_toint.php:tx_icsopendata_cast_string_toint',
					'Double' => 'EXT:ics_opendata/lib/data/casting/class.cast_none.php:tx_icsopendata_cast_none'
				),
	'Timestamp' => array(
					'String' => 'EXT:ics_opendata/lib/data/casting/class.cast_tostring.php:tx_icsopendata_cast_tostring',
					'Int' => 'EXT:ics_opendata/lib/data/casting/class.cast_none.php:tx_icsopendata_cast_none',
					'Date_yyyy/mm/dd' => 'EXT:ics_opendata/lib/data/casting/class.cast_timestamp_todateyyyymmdd.php:tx_icsopendata_cast_timestamp_todateyyyymmdd',
					'Date_dd/mm/yyyy' => 'EXT:ics_opendata/lib/data/casting/class.cast_timestamp_todateddmmyyyy.php:tx_icsopendata_cast_timestamp_todateddmmyyyy',
					'Date_mm/dd/yyyy' => 'EXT:ics_opendata/lib/data/casting/class.cast_timestamp_todatemmddyyyy.php:tx_icsopendata_cast_timestamp_todatemmddyyyy',
					'Date_ISO8601' => 'EXT:ics_opendata/lib/data/casting/class.cast_timestamp_todateiso8601.php:tx_icsopendata_cast_timestamp_todateiso8601'
				),
	'Date(String)' => array(
					'Timestamp' => 'EXT:ics_opendata/lib/data/casting/class.cast_string_totimestamp.php:tx_icsopendata_cast_string_totimestamp'
				)
);

// --- Templates
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['templates']['command'] = array(
	'command' => 'lib/templates/tx_icsopendata_template_command.php'
);

$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['templates']['datasource'] = array(
	'datasource' => 'lib/templates/tx_icsopendata_template_datasource.php',
	'connexion' => 'lib/templates/tx_icsopendata_template_datasourceconnexion.php'
);

$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['templates']['extlocalconf'] = array(
	'extlocalconf' => 'lib/templates/tx_icsopendata_template_ext_localconf.php'
);

// --- Icons
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['icons']['extension'] = array(
	'default' => 'res/ext_icon.gif',
);
			
?>