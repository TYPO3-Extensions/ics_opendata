<?php

########################################################################
# Extension Manager/Repository config file for ext "ics_od_core_api".
#
# Auto generated 23-06-2011 12:03
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Opendata API',
	'description' => 'Main controller for XML/JSON access. Can use any specific command handlers.',
	'category' => 'misc',
	'author' => 'In Cite Solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => 'ics_od_appstore',
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
	'version' => '1.0.2',
	'constraints' => array(
		'depends' => array(
			'ics_od_appstore' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:41:{s:9:"ChangeLog";s:4:"8955";s:10:"README.txt";s:4:"ee2d";s:21:"ext_conf_template.txt";s:4:"d038";s:12:"ext_icon.gif";s:4:"2505";s:17:"ext_localconf.php";s:4:"8061";s:14:"ext_tables.php";s:4:"5ce8";s:51:"api/class.tx_icsodcoreapi_abstract_file_command.php";s:4:"5452";s:37:"api/class.tx_icsodcoreapi_command.php";s:4:"3b6e";s:37:"api/class.tx_icsodcoreapi_factory.php";s:4:"7c7e";s:36:"api/class.tx_icsodcoreapi_logger.php";s:4:"3de0";s:45:"api/class.tx_icsodcoreapi_pattern_command.php";s:4:"6ec8";s:37:"api/class.tx_icsodcoreapi_service.php";s:4:"07c0";s:19:"api/error_codes.php";s:4:"f31f";s:23:"api/error_functions.php";s:4:"1973";s:30:"api/tx_icsodcoreapi_client.php";s:4:"e14d";s:14:"doc/manual.sxw";s:4:"f89e";s:19:"doc/wizard_form.dat";s:4:"1769";s:20:"doc/wizard_form.html";s:4:"9aa4";s:37:"lib/class.tx_icsodcoreapi_command.php";s:4:"10ee";s:39:"lib/class.tx_icsodcoreapi_parameter.php";s:4:"5b02";s:35:"lib/class.tx_icsodcoreapi_value.php";s:4:"b485";s:22:"lib/xml2json/test1.xml";s:4:"f846";s:22:"lib/xml2json/test2.xml";s:4:"8f71";s:22:"lib/xml2json/test3.xml";s:4:"f1bc";s:22:"lib/xml2json/test4.xml";s:4:"27e1";s:25:"lib/xml2json/xml2json.php";s:4:"5865";s:30:"lib/xml2json/xml2json_test.php";s:4:"4a45";s:26:"lib/xml2json/json/JSON.php";s:4:"4d2b";s:25:"lib/xml2json/json/LICENSE";s:4:"f572";s:46:"mod1/class.tx_icsodcoreapi_module1_command.php";s:4:"2cd9";s:43:"mod1/class.tx_icsodcoreapi_module1_menu.php";s:4:"95cd";s:48:"mod1/class.tx_icsodcoreapi_module1_parameter.php";s:4:"61ed";s:44:"mod1/class.tx_icsodcoreapi_module1_value.php";s:4:"f864";s:13:"mod1/conf.php";s:4:"008e";s:14:"mod1/index.php";s:4:"68c1";s:18:"mod1/locallang.xml";s:4:"60cf";s:22:"mod1/locallang_mod.xml";s:4:"5654";s:22:"mod1/mod_template.html";s:4:"4168";s:19:"mod1/moduleicon.gif";s:4:"8074";s:14:"mod1/script.js";s:4:"0e5e";s:15:"mod1/styles.css";s:4:"4f56";}',
	'suggests' => array(
	),
);

?>