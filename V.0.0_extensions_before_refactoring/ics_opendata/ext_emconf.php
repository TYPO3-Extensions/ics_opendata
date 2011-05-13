<?php

########################################################################
# Extension Manager/Repository config file for ext "ics_opendata".
#
# Auto generated 15-02-2011 15:11
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'OpenData starter',
	'description' => 'Create and edit opendata extension',
	'category' => 'be',
	'author' => 'In Cité solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => 'taskcenter',
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
			'taskcenter' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:62:{s:9:"ChangeLog";s:4:"25e1";s:10:"README.txt";s:4:"9fa9";s:16:"ext_autoload.php";s:4:"617f";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"90c4";s:14:"ext_tables.php";s:4:"c78a";s:16:"locallang_db.xml";s:4:"a35d";s:14:"doc/ics_od.dat";s:4:"e736";s:19:"doc/wizard_form.dat";s:4:"7bc6";s:20:"doc/wizard_form.html";s:4:"09c6";s:29:"lib/class.code_generation.php";s:4:"1efa";s:24:"lib/class.repository.php";s:4:"16ae";s:31:"lib/data/class.data_command.php";s:4:"b92b";s:34:"lib/data/class.data_elementxml.php";s:4:"98c6";s:29:"lib/data/class.data_field.php";s:4:"f36c";s:30:"lib/data/class.data_filter.php";s:4:"25a7";s:28:"lib/data/class.data_link.php";s:4:"660a";s:29:"lib/data/class.data_table.php";s:4:"7120";s:35:"lib/data/interface.data_casting.php";s:4:"27d1";s:36:"lib/data/casting/class.cast_none.php";s:4:"e95a";s:47:"lib/data/casting/class.cast_string_todouble.php";s:4:"24ad";s:44:"lib/data/casting/class.cast_string_toint.php";s:4:"edc4";s:50:"lib/data/casting/class.cast_string_totimestamp.php";s:4:"aedd";s:56:"lib/data/casting/class.cast_timestamp_todateddmmyyyy.php";s:4:"b345";s:55:"lib/data/casting/class.cast_timestamp_todateiso8601.php";s:4:"a933";s:56:"lib/data/casting/class.cast_timestamp_todatemmddyyyy.php";s:4:"35be";s:56:"lib/data/casting/class.cast_timestamp_todateyyyymmdd.php";s:4:"8bb5";s:40:"lib/data/casting/class.cast_tostring.php";s:4:"00df";s:39:"lib/forms/class.form_commandprofile.php";s:4:"355b";s:31:"lib/forms/class.form_filter.php";s:4:"24ae";s:32:"lib/forms/class.form_general.php";s:4:"1cb3";s:35:"lib/forms/class.form_linkcustom.php";s:4:"8f21";s:38:"lib/forms/class.form_loadextension.php";s:4:"72d2";s:32:"lib/forms/class.form_manager.php";s:4:"04cd";s:29:"lib/forms/class.form_menu.php";s:4:"02d0";s:31:"lib/forms/class.form_result.php";s:4:"f1b7";s:31:"lib/forms/class.form_source.php";s:4:"8d40";s:38:"lib/forms/class.form_sourceprofile.php";s:4:"27ef";s:30:"lib/forms/class.form_sumup.php";s:4:"0de0";s:37:"lib/forms/class.form_tableprofile.php";s:4:"3a76";s:28:"lib/forms/interface.form.php";s:4:"a5f4";s:42:"lib/sources/interface.filter_generator.php";s:4:"a1c5";s:37:"lib/sources/interface.source_data.php";s:4:"51ea";s:43:"lib/sources/interface.source_formsource.php";s:4:"c8e0";s:34:"lib/sources/csv/class.csv_data.php";s:4:"edd9";s:40:"lib/sources/csv/class.csv_formsource.php";s:4:"331c";s:38:"lib/sources/mysql/class.mysql_data.php";s:4:"05e1";s:44:"lib/sources/mysql/class.mysql_formsource.php";s:4:"d795";s:44:"lib/sources/mysql/class.mysql_generation.php";s:4:"8664";s:60:"lib/sources/mysql/filters/class.mysql_filter_proximity22.php";s:4:"3f62";s:66:"lib/sources/mysql/filters/class.mysql_filter_proximity22_limit.php";s:4:"f725";s:61:"lib/sources/mysql/filters/class.mysql_filter_simpleselect.php";s:4:"35ea";s:53:"lib/sources/mysql/filters/class.mysql_filter_test.php";s:4:"4fc3";s:47:"lib/sources/mysql/filters/class.mysql_limit.php";s:4:"af23";s:49:"lib/templates/tx_icsopendata_template_command.php";s:4:"f166";s:52:"lib/templates/tx_icsopendata_template_datasource.php";s:4:"f064";s:55:"lib/templates/tx_icsopendata_template_ext_localconf.php";s:4:"a2e1";s:42:"modfunc1/class.tx_icsopendata_modfunc1.php";s:4:"bbd3";s:22:"modfunc1/locallang.xml";s:4:"4109";s:42:"modfunc2/class.tx_icsopendata_modfunc2.php";s:4:"1a98";s:22:"modfunc2/locallang.xml";s:4:"fc71";s:16:"res/ext_icon.gif";s:4:"1bdc";}',
	'suggests' => array(
	),
);

?>