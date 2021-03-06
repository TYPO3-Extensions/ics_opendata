<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "ics_od_datastore".
 *
 * Auto generated 31-05-2013 12:39
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Opendata Datastore',
	'description' => 'Opendata plugins and module for Data Store. Support public store, RSS and BE module for dataset.',
	'category' => 'plugin',
	'author' => 'Plan.Net',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => 'pagebrowse',
	'conflicts' => '',
	'suggests' => array(
	),
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
	'version' => '1.2.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-0.0.0',
			'pagebrowse' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'ics_tcafe_admin' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:120:{s:9:"ChangeLog";s:4:"3909";s:16:"ext_autoload.php";s:4:"a238";s:21:"ext_conf_template.txt";s:4:"03f1";s:12:"ext_icon.gif";s:4:"2505";s:17:"ext_localconf.php";s:4:"7aa8";s:14:"ext_tables.php";s:4:"e486";s:14:"ext_tables.sql";s:4:"3820";s:19:"flexform_ds_pi1.xml";s:4:"b944";s:19:"flexform_ds_pi3.xml";s:4:"20c8";s:35:"icon_tx_icsoddatastore_agencies.gif";s:4:"475a";s:36:"icon_tx_icsoddatastore_downloads.gif";s:4:"475a";s:38:"icon_tx_icsoddatastore_fileformats.gif";s:4:"475a";s:37:"icon_tx_icsoddatastore_filegroups.gif";s:4:"b02b";s:32:"icon_tx_icsoddatastore_files.gif";s:4:"475a";s:36:"icon_tx_icsoddatastore_filetypes.gif";s:4:"475a";s:35:"icon_tx_icsoddatastore_licences.gif";s:4:"475a";s:41:"icon_tx_icsoddatastore_monthdownloads.gif";s:4:"475a";s:37:"icon_tx_icsoddatastore_statistics.gif";s:4:"475a";s:32:"icon_tx_icsoddatastore_tiers.gif";s:4:"475a";s:13:"locallang.xml";s:4:"5f56";s:16:"locallang_db.xml";s:4:"3f88";s:26:"locallang_flexform_pi1.xml";s:4:"c0a7";s:26:"locallang_flexform_pi3.xml";s:4:"3f95";s:10:"README.txt";s:4:"ee2d";s:7:"tca.php";s:4:"1381";s:14:"doc/manual.sxw";s:4:"1dbf";s:19:"doc/wizard_form.dat";s:4:"8ac8";s:20:"doc/wizard_form.html";s:4:"000b";s:47:"downloadfile/class.tx_icsoddatastore_dlfile.php";s:4:"ae49";s:49:"downloadfile/class.tx_icsoddatastore_download.php";s:4:"861f";s:51:"downloadfile/class.tx_icsoddatastore_hitcounter.php";s:4:"3464";s:49:"downloadfile/class.tx_icsoddatastore_makelink.php";s:4:"d6b6";s:43:"hook/class.tx_icsoddatastore_stats_hook.php";s:4:"5757";s:43:"hook/class.tx_icsoddatastore_TCAFEAdmin.php";s:4:"abb1";s:18:"hook/locallang.xml";s:4:"cc9c";s:21:"hook/static/setup.txt";s:4:"7871";s:39:"lib/class.tx_icsoddatastore_dynflex.php";s:4:"35b4";s:43:"lib/class.tx_icsoddatastore_filecontrol.php";s:4:"8750";s:43:"lib/class.tx_icsoddatastore_filemanager.php";s:4:"ccef";s:42:"lib/class.tx_icsoddatastore_recordlist.php";s:4:"7abd";s:41:"lib/class.tx_icsoddatastore_sysfolder.php";s:4:"d52d";s:41:"lib/class.tx_icsoddatastore_tcahelper.php";s:4:"ed14";s:37:"lib/class.tx_icsoddatastore_title.php";s:4:"ea08";s:13:"mod1/conf.php";s:4:"ffd7";s:14:"mod1/index.php";s:4:"0acd";s:18:"mod1/locallang.xml";s:4:"629e";s:22:"mod1/locallang_mod.xml";s:4:"334f";s:22:"mod1/mod_navframe.html";s:4:"0acc";s:22:"mod1/mod_template.html";s:4:"1502";s:23:"mod1/moduleicon-old.gif";s:4:"8074";s:19:"mod1/moduleicon.gif";s:4:"2a4d";s:35:"mod1/tx_icsoddatastore_navframe.php";s:4:"26ed";s:66:"opendata/class.tx_icsoddatastore_datastore_getagencies_command.php";s:4:"4d58";s:66:"opendata/class.tx_icsoddatastore_datastore_getdatasets_command.php";s:4:"bc62";s:69:"opendata/class.tx_icsoddatastore_datastore_getfileformats_command.php";s:4:"de5a";s:66:"opendata/class.tx_icsoddatastore_datastore_getlicences_command.php";s:4:"6aad";s:69:"opendata/class.tx_icsoddatastore_datastore_searchdatasets_command.php";s:4:"e24d";s:65:"opendata/datasource/class.tx_icsoddatastore_agency_datasource.php";s:4:"e14a";s:66:"opendata/datasource/class.tx_icsoddatastore_dataset_datasource.php";s:4:"d6ad";s:69:"opendata/datasource/class.tx_icsoddatastore_fileformat_datasource.php";s:4:"c68f";s:66:"opendata/datasource/class.tx_icsoddatastore_licence_datasource.php";s:4:"33d1";s:64:"opendata/datasource/class.tx_icsoddatastore_tiers_datasource.php";s:4:"8e3c";s:57:"opendata/datasource/tx_icsoddatastore_sourceconnexion.php";s:4:"4525";s:40:"opendata/xml_cmddoc/documentationapi.css";s:4:"beb3";s:40:"opendata/xml_cmddoc/documentationapi.xsd";s:4:"b01d";s:40:"opendata/xml_cmddoc/documentationapi.xsl";s:4:"0261";s:35:"opendata/xml_cmddoc/getagencies.inc";s:4:"600e";s:35:"opendata/xml_cmddoc/getagencies.php";s:4:"6591";s:35:"opendata/xml_cmddoc/getagencies.xml";s:4:"7ec0";s:35:"opendata/xml_cmddoc/getdatasets.inc";s:4:"4d93";s:35:"opendata/xml_cmddoc/getdatasets.php";s:4:"d3ab";s:35:"opendata/xml_cmddoc/getdatasets.xml";s:4:"5df1";s:38:"opendata/xml_cmddoc/getfileformats.inc";s:4:"3c48";s:38:"opendata/xml_cmddoc/getfileformats.php";s:4:"e9c2";s:38:"opendata/xml_cmddoc/getfileformats.xml";s:4:"975f";s:35:"opendata/xml_cmddoc/getlicences.inc";s:4:"e04a";s:35:"opendata/xml_cmddoc/getlicences.php";s:4:"c85b";s:35:"opendata/xml_cmddoc/getlicences.xml";s:4:"75cf";s:38:"opendata/xml_cmddoc/searchdatasets.inc";s:4:"f95a";s:38:"opendata/xml_cmddoc/searchdatasets.php";s:4:"b7bd";s:38:"opendata/xml_cmddoc/searchdatasets.xml";s:4:"42ad";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:35:"pi1/class.tx_icsoddatastore_pi1.php";s:4:"5388";s:43:"pi1/class.tx_icsoddatastore_pi1_wizicon.php";s:4:"d93d";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"cd80";s:24:"pi1/static/constants.txt";s:4:"ac62";s:20:"pi1/static/setup.txt";s:4:"e377";s:14:"pi2/ce_wiz.gif";s:4:"02b6";s:35:"pi2/class.tx_icsoddatastore_pi2.php";s:4:"f780";s:43:"pi2/class.tx_icsoddatastore_pi2_wizicon.php";s:4:"ade1";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"90e2";s:24:"pi2/static/constants.txt";s:4:"6d78";s:20:"pi2/static/setup.txt";s:4:"993b";s:14:"pi3/ce_wiz.gif";s:4:"02b6";s:35:"pi3/class.tx_icsoddatastore_pi3.php";s:4:"373d";s:43:"pi3/class.tx_icsoddatastore_pi3_wizicon.php";s:4:"37bd";s:13:"pi3/clear.gif";s:4:"cc11";s:17:"pi3/locallang.xml";s:4:"8acb";s:20:"pi3/static/setup.txt";s:4:"69eb";s:16:"res/datastore.js";s:4:"2157";s:14:"res/editer.png";s:4:"bd42";s:20:"res/editer_icone.png";s:4:"a605";s:15:"res/img_rss.jpg";s:4:"ae38";s:15:"res/img_rss.png";s:4:"c335";s:19:"res/img_sortAsc.gif";s:4:"c229";s:20:"res/img_sortDesc.gif";s:4:"a2ee";s:24:"res/img_sortInactive.gif";s:4:"95e4";s:24:"res/jquery-ui.custom.css";s:4:"6bea";s:28:"res/lastupdate_tmplFile.html";s:4:"882d";s:22:"res/rss2_tmplFile.tmpl";s:4:"2cba";s:17:"res/template.html";s:4:"dcb7";s:29:"res/template_TCAFE_Admin.html";s:4:"da5e";s:38:"res/template_TCAFE_Admin_datasets.html";s:4:"f8bd";s:35:"res/template_TCAFE_Admin_files.html";s:4:"bc20";s:12:"res/test.png";s:4:"a51d";s:16:"res/uploader.png";s:4:"38dc";s:22:"res/uploader_icone.png";s:4:"ac72";s:42:"scheduler/class.tx_icsoddatastore_task.php";s:4:"c2c6";}',
);

?>