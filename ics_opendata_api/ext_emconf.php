<?php

########################################################################
# Extension Manager/Repository config file for ext "ics_opendata_api".
#
# Auto generated 18-10-2010 09:28
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'OpenData API',
	'description' => 'A simple API implementation for data requests.',
	'category' => 'plugin',
	'author' => 'In Cité Solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:84:{s:9:"ChangeLog";s:4:"7d73";s:10:"README.txt";s:4:"ee2d";s:21:"ext_conf_template.txt";s:4:"bbdb";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"03ef";s:14:"ext_tables.php";s:4:"e77a";s:14:"ext_tables.sql";s:4:"d1c8";s:39:"icon_tx_icsopendataapi_applications.gif";s:4:"475a";s:31:"icon_tx_icsopendataapi_logs.gif";s:4:"475a";s:37:"icon_tx_icsopendataapi_month_logs.gif";s:4:"475a";s:37:"icon_tx_icsopendataapi_statistics.gif";s:4:"475a";s:13:"locallang.xml";s:4:"3e29";s:16:"locallang_db.xml";s:4:"64f8";s:7:"tca.php";s:4:"400d";s:53:"api/class.tx_icsopendataapi_abstract_file_command.php";s:4:"e5a0";s:39:"api/class.tx_icsopendataapi_command.php";s:4:"f5d7";s:39:"api/class.tx_icsopendataapi_factory.php";s:4:"9747";s:38:"api/class.tx_icsopendataapi_logger.php";s:4:"aa00";s:39:"api/class.tx_icsopendataapi_service.php";s:4:"3a39";s:19:"api/error_codes.php";s:4:"8e93";s:23:"api/error_functions.php";s:4:"5d16";s:32:"api/tx_icsopendataapi_client.php";s:4:"f819";s:19:"doc/wizard_form.dat";s:4:"6932";s:20:"doc/wizard_form.html";s:4:"52f9";s:39:"lib/class.tx_icsopendataapi_command.php";s:4:"4cd4";s:41:"lib/class.tx_icsopendataapi_parameter.php";s:4:"3e0f";s:37:"lib/class.tx_icsopendataapi_value.php";s:4:"238e";s:23:"lib/randomGenerator.php";s:4:"c4d6";s:22:"lib/xml2json/test1.xml";s:4:"3fe5";s:22:"lib/xml2json/test2.xml";s:4:"7fff";s:22:"lib/xml2json/test3.xml";s:4:"988f";s:22:"lib/xml2json/test4.xml";s:4:"2581";s:25:"lib/xml2json/xml2json.php";s:4:"2e28";s:30:"lib/xml2json/xml2json_test.php";s:4:"595d";s:26:"lib/xml2json/json/JSON.php";s:4:"21a4";s:25:"lib/xml2json/json/LICENSE";s:4:"f572";s:48:"mod1/class.tx_icsopendataapi_module1_command.php";s:4:"6c81";s:45:"mod1/class.tx_icsopendataapi_module1_menu.php";s:4:"db33";s:50:"mod1/class.tx_icsopendataapi_module1_parameter.php";s:4:"0c95";s:46:"mod1/class.tx_icsopendataapi_module1_value.php";s:4:"c04d";s:13:"mod1/conf.php";s:4:"a6fe";s:14:"mod1/index.php";s:4:"4b01";s:18:"mod1/locallang.xml";s:4:"ebb9";s:22:"mod1/locallang_mod.xml";s:4:"0a0e";s:22:"mod1/mod_template.html";s:4:"09c0";s:19:"mod1/moduleicon.gif";s:4:"8074";s:14:"mod1/script.js";s:4:"5f6a";s:15:"mod1/styles.css";s:4:"2844";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:35:"pi1/class.tx_icsopendataapi_pi1.php";s:4:"3320";s:43:"pi1/class.tx_icsopendataapi_pi1_wizicon.php";s:4:"e320";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"43f6";s:20:"pi1/static/setup.txt";s:4:"5b8b";s:14:"pi2/ce_wiz.gif";s:4:"02b6";s:35:"pi2/class.tx_icsopendataapi_pi2.php";s:4:"9dd5";s:43:"pi2/class.tx_icsopendataapi_pi2_wizicon.php";s:4:"4907";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"141b";s:14:"pi3/ce_wiz.gif";s:4:"02b6";s:35:"pi3/class.tx_icsopendataapi_pi3.php";s:4:"0af4";s:43:"pi3/class.tx_icsopendataapi_pi3_wizicon.php";s:4:"12e4";s:13:"pi3/clear.gif";s:4:"cc11";s:17:"pi3/locallang.xml";s:4:"2e50";s:14:"pi4/ce_wiz.gif";s:4:"02b6";s:35:"pi4/class.tx_icsopendataapi_pi4.php";s:4:"6c42";s:43:"pi4/class.tx_icsopendataapi_pi4_wizicon.php";s:4:"b0ed";s:13:"pi4/clear.gif";s:4:"cc11";s:17:"pi4/locallang.xml";s:4:"453c";s:14:"pi5/ce_wiz.gif";s:4:"02b6";s:35:"pi5/class.tx_icsopendataapi_pi5.php";s:4:"963c";s:43:"pi5/class.tx_icsopendataapi_pi5_wizicon.php";s:4:"87b2";s:13:"pi5/clear.gif";s:4:"cc11";s:17:"pi5/locallang.xml";s:4:"3fcc";s:14:"pi6/ce_wiz.gif";s:4:"02b6";s:35:"pi6/class.tx_icsopendataapi_pi6.php";s:4:"12a1";s:43:"pi6/class.tx_icsopendataapi_pi6_wizicon.php";s:4:"2443";s:13:"pi6/clear.gif";s:4:"cc11";s:17:"pi6/locallang.xml";s:4:"37f9";s:20:"pi6/static/setup.txt";s:4:"88e3";s:13:"res/script.js";s:4:"d3be";s:18:"res/tablefilter.js";s:4:"8586";s:17:"res/template.html";s:4:"21e1";s:18:"res/verifPasswd.js";s:4:"a8ac";}',
);

?>