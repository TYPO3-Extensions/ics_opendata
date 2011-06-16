<?php

########################################################################
# Extension Manager/Repository config file for ext "ics_od_datastore".
#
# Auto generated 09-06-2011 10:03
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Opendata Datastore',
	'description' => 'Opendata plugins and module for Data Store. Support public store, RSS and BE module for dataset.',
	'category' => 'plugin',
	'author' => 'In Cite Solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:92:{s:9:"ChangeLog";s:4:"3e20";s:10:"README.txt";s:4:"ee2d";s:16:"ext_autoload.php";s:4:"1d91";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"d3f1";s:14:"ext_tables.php";s:4:"51f2";s:14:"ext_tables.sql";s:4:"d0de";s:19:"flexform_ds_pi1.xml";s:4:"9058";s:35:"icon_tx_icsoddatastore_agencies.gif";s:4:"475a";s:36:"icon_tx_icsoddatastore_downloads.gif";s:4:"475a";s:38:"icon_tx_icsoddatastore_fileformats.gif";s:4:"475a";s:37:"icon_tx_icsoddatastore_filegroups.gif";s:4:"b02b";s:32:"icon_tx_icsoddatastore_files.gif";s:4:"475a";s:36:"icon_tx_icsoddatastore_filetypes.gif";s:4:"475a";s:35:"icon_tx_icsoddatastore_licences.gif";s:4:"475a";s:32:"icon_tx_icsoddatastore_tiers.gif";s:4:"475a";s:13:"locallang.xml";s:4:"9e05";s:16:"locallang_db.xml";s:4:"8c23";s:26:"locallang_flexform_pi1.xml";s:4:"fcff";s:7:"tca.php";s:4:"83ef";s:14:"doc/manual.sxw";s:4:"b5bc";s:19:"doc/wizard_form.dat";s:4:"8ac8";s:20:"doc/wizard_form.html";s:4:"000b";s:43:"lib/class.tx_icsoddatastore_filecontrol.php";s:4:"6f96";s:43:"lib/class.tx_icsoddatastore_filemanager.php";s:4:"1cc9";s:42:"lib/class.tx_icsoddatastore_recordlist.php";s:4:"8c77";s:41:"lib/class.tx_icsoddatastore_sysfolder.php";s:4:"849d";s:37:"lib/class.tx_icsoddatastore_title.php";s:4:"aa66";s:13:"mod1/conf.php";s:4:"fc36";s:14:"mod1/index.php";s:4:"b2a3";s:18:"mod1/locallang.xml";s:4:"2ac9";s:22:"mod1/locallang_mod.xml";s:4:"045c";s:22:"mod1/mod_navframe.html";s:4:"7605";s:22:"mod1/mod_template.html";s:4:"39b6";s:23:"mod1/moduleicon-old.gif";s:4:"8074";s:19:"mod1/moduleicon.gif";s:4:"2a4d";s:35:"mod1/tx_icsoddatastore_navframe.php";s:4:"7f40";s:66:"opendata/class.tx_icsoddatastore_datastore_getagencies_command.php";s:4:"276b";s:66:"opendata/class.tx_icsoddatastore_datastore_getdatasets_command.php";s:4:"c841";s:69:"opendata/class.tx_icsoddatastore_datastore_getfileformats_command.php";s:4:"49dc";s:66:"opendata/class.tx_icsoddatastore_datastore_getlicences_command.php";s:4:"781c";s:69:"opendata/class.tx_icsoddatastore_datastore_searchdatasets_command.php";s:4:"b0d3";s:65:"opendata/datasource/class.tx_icsoddatastore_agency_datasource.php";s:4:"dd34";s:66:"opendata/datasource/class.tx_icsoddatastore_dataset_datasource.php";s:4:"f003";s:69:"opendata/datasource/class.tx_icsoddatastore_fileformat_datasource.php";s:4:"0495";s:66:"opendata/datasource/class.tx_icsoddatastore_licence_datasource.php";s:4:"7551";s:64:"opendata/datasource/class.tx_icsoddatastore_tiers_datasource.php";s:4:"f118";s:57:"opendata/datasource/tx_icsoddatastore_sourceconnexion.php";s:4:"af75";s:40:"opendata/xml_cmddoc/documentationapi.css";s:4:"3a7e";s:40:"opendata/xml_cmddoc/documentationapi.xsd";s:4:"b01d";s:40:"opendata/xml_cmddoc/documentationapi.xsl";s:4:"0261";s:35:"opendata/xml_cmddoc/getagencies.inc";s:4:"600e";s:35:"opendata/xml_cmddoc/getagencies.php";s:4:"0b94";s:35:"opendata/xml_cmddoc/getagencies.xml";s:4:"034e";s:35:"opendata/xml_cmddoc/getdatasets.inc";s:4:"4d93";s:35:"opendata/xml_cmddoc/getdatasets.php";s:4:"fc7e";s:35:"opendata/xml_cmddoc/getdatasets.xml";s:4:"fac2";s:38:"opendata/xml_cmddoc/getfileformats.inc";s:4:"3c48";s:38:"opendata/xml_cmddoc/getfileformats.php";s:4:"ac78";s:38:"opendata/xml_cmddoc/getfileformats.xml";s:4:"b177";s:35:"opendata/xml_cmddoc/getlicences.inc";s:4:"e04a";s:35:"opendata/xml_cmddoc/getlicences.php";s:4:"9a52";s:35:"opendata/xml_cmddoc/getlicences.xml";s:4:"605e";s:38:"opendata/xml_cmddoc/searchdatasets.inc";s:4:"f95a";s:38:"opendata/xml_cmddoc/searchdatasets.php";s:4:"f627";s:38:"opendata/xml_cmddoc/searchdatasets.xml";s:4:"04ed";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:35:"pi1/class.tx_icsoddatastore_pi1.php";s:4:"1609";s:43:"pi1/class.tx_icsoddatastore_pi1_wizicon.php";s:4:"e221";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"9bc1";s:24:"pi1/static/constants.txt";s:4:"efea";s:20:"pi1/static/setup.txt";s:4:"4200";s:14:"pi2/ce_wiz.gif";s:4:"02b6";s:35:"pi2/class.tx_icsoddatastore_pi2.php";s:4:"6fd8";s:43:"pi2/class.tx_icsoddatastore_pi2_wizicon.php";s:4:"39ca";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"290f";s:24:"pi2/static/constants.txt";s:4:"b9ea";s:20:"pi2/static/setup.txt";s:4:"6871";s:14:"res/editer.png";s:4:"bd42";s:20:"res/editer_icone.png";s:4:"a605";s:15:"res/img_rss.jpg";s:4:"ae38";s:15:"res/img_rss.png";s:4:"c335";s:19:"res/img_sortAsc.gif";s:4:"c229";s:20:"res/img_sortDesc.gif";s:4:"a2ee";s:24:"res/img_sortInactive.gif";s:4:"95e4";s:22:"res/rss2_tmplFile.tmpl";s:4:"2cba";s:17:"res/template.html";s:4:"0b25";s:12:"res/test.png";s:4:"a51d";s:16:"res/uploader.png";s:4:"38dc";s:22:"res/uploader_icone.png";s:4:"ac72";}',
	'suggests' => array(
	),
);

?>