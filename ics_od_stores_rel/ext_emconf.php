<?php

########################################################################
# Extension Manager/Repository config file for ext "ics_od_stores_rel".
#
# Auto generated 16-06-2011 17:39
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Opendata Appstore Datastore Relation',
	'description' => 'The relation between opendata datastore and appstore',
	'category' => 'plugin',
	'author' => 'In Cite Solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => 'ics_od_appstore,ics_od_datastore',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.0.1',
	'constraints' => array(
		'depends' => array(
			'ics_od_appstore' => '',
			'ics_od_datastore' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:15:{s:9:"ChangeLog";s:4:"8955";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"2505";s:17:"ext_localconf.php";s:4:"2a3b";s:14:"ext_tables.php";s:4:"00a3";s:14:"ext_tables.sql";s:4:"a8da";s:24:"ext_typoscript_setup.txt";s:4:"7942";s:13:"locallang.xml";s:4:"23d9";s:16:"locallang_db.xml";s:4:"7da2";s:14:"doc/manual.sxw";s:4:"1f52";s:19:"doc/wizard_form.dat";s:4:"dba7";s:20:"doc/wizard_form.html";s:4:"cd20";s:45:"hook/class.tx_icsodstoresrel_appsDatasets.php";s:4:"cc94";s:18:"hook/locallang.xml";s:4:"23d9";s:17:"res/template.html";s:4:"64bd";}',
	'suggests' => array(
	),
);

?>