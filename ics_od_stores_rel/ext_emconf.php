<?php

########################################################################
# Extension Manager/Repository config file for ext "ics_od_stores_rel".
#
# Auto generated 13-05-2011 15:31
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Appstore datastore relation',
	'description' => 'The relation between open data datastore and appstore',
	'category' => 'misc',
	'author' => 'In Cité Solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => 'ics_od_appstore,ics_od_datastore',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
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
	'_md5_values_when_last_written' => 'a:8:{s:9:"ChangeLog";s:4:"8955";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"ca3d";s:14:"ext_tables.sql";s:4:"00a6";s:16:"locallang_db.xml";s:4:"7da2";s:19:"doc/wizard_form.dat";s:4:"dba7";s:20:"doc/wizard_form.html";s:4:"cd20";}',
);

?>