<?php

########################################################################
# Extension Manager/Repository config file for ext "ics_od_appstore".
#
# Auto generated 24-04-2012 11:18
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Open Data applications store',
    'description' => 'Opendata plugins for Applications Store. Support public store and plugins for applications owner to manage applications.',
    'category' => 'plugin',
    'author' => 'In Cite Solution',
    'author_email' => 'technique@in-cite.net',
    'shy' => '',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'module' => '',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => 1,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'author_company' => '',
    'version' => '1.1.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '4.3.0-0.0.0',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
    '_md5_values_when_last_written' => 'a:89:{s:9:"ChangeLog";s:4:"9287";s:10:"README.txt";s:4:"ee2d";s:16:"ext_autoload.php";s:4:"5117";s:12:"ext_icon.gif";s:4:"2505";s:17:"ext_localconf.php";s:4:"d9cb";s:14:"ext_tables.php";s:4:"d1fa";s:14:"ext_tables.sql";s:4:"7e76";s:25:"ext_tables_static+adt.sql";s:4:"f7da";s:28:"ext_typoscript_constants.txt";s:4:"d8ce";s:24:"ext_typoscript_setup.txt";s:4:"1bb2";s:38:"icon_tx_icsodappstore_applications.gif";s:4:"475a";s:30:"icon_tx_icsodappstore_logs.gif";s:4:"475a";s:36:"icon_tx_icsodappstore_month_logs.gif";s:4:"475a";s:35:"icon_tx_icsodappstore_platforms.gif";s:4:"475a";s:36:"icon_tx_icsodappstore_statistics.gif";s:4:"475a";s:13:"locallang.xml";s:4:"21f6";s:16:"locallang_db.xml";s:4:"cb33";s:7:"tca.php";s:4:"51d3";s:25:"doc/ics_od_repository.dat";s:4:"65da";s:14:"doc/manual.sxw";s:4:"8f05";s:19:"doc/wizard_form.dat";s:4:"63ff";s:20:"doc/wizard_form.html";s:4:"9ca5";s:37:"lib/class.tx_icsodappstore_common.php";s:4:"e546";s:23:"lib/randomGenerator.php";s:4:"fb55";s:59:"opendata/class.tx_icsodappstore_getapplications_command.php";s:4:"f625";s:54:"opendata/class.tx_icsodappstore_getauthors_command.php";s:4:"62fb";s:56:"opendata/class.tx_icsodappstore_getplatforms_command.php";s:4:"5aa4";s:62:"opendata/class.tx_icsodappstore_searchapplications_command.php";s:4:"5c61";s:37:"opendata/ext_typoscript_constants.txt";s:4:"8928";s:33:"opendata/ext_typoscript_setup.txt";s:4:"d9a7";s:73:"opendata/datasource/class.tx_icsodappstore_application_datasource.old.php";s:4:"809d";s:69:"opendata/datasource/class.tx_icsodappstore_application_datasource.php";s:4:"73d5";s:64:"opendata/datasource/class.tx_icsodappstore_author_datasource.php";s:4:"9c6c";s:66:"opendata/datasource/class.tx_icsodappstore_platform_datasource.php";s:4:"3890";s:56:"opendata/datasource/tx_icsodappstore_sourceconnexion.php";s:4:"0098";s:40:"opendata/xml_cmddoc/documentationapi.css";s:4:"1588";s:40:"opendata/xml_cmddoc/documentationapi.xsd";s:4:"b01d";s:40:"opendata/xml_cmddoc/documentationapi.xsl";s:4:"0261";s:39:"opendata/xml_cmddoc/getapplications.inc";s:4:"eb1e";s:39:"opendata/xml_cmddoc/getapplications.php";s:4:"7472";s:39:"opendata/xml_cmddoc/getapplications.xml";s:4:"6c7b";s:34:"opendata/xml_cmddoc/getauthors.inc";s:4:"4221";s:34:"opendata/xml_cmddoc/getauthors.php";s:4:"566b";s:34:"opendata/xml_cmddoc/getauthors.xml";s:4:"9064";s:36:"opendata/xml_cmddoc/getplatforms.inc";s:4:"7047";s:36:"opendata/xml_cmddoc/getplatforms.php";s:4:"a42b";s:36:"opendata/xml_cmddoc/getplatforms.xml";s:4:"caaa";s:42:"opendata/xml_cmddoc/searchapplications.inc";s:4:"423a";s:42:"opendata/xml_cmddoc/searchapplications.php";s:4:"27a3";s:42:"opendata/xml_cmddoc/searchapplications.xml";s:4:"38d5";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:34:"pi1/class.tx_icsodappstore_pi1.php";s:4:"ef65";s:42:"pi1/class.tx_icsodappstore_pi1_wizicon.php";s:4:"a9d9";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"a7ca";s:14:"pi2/ce_wiz.gif";s:4:"02b6";s:34:"pi2/class.tx_icsodappstore_pi2.php";s:4:"612b";s:42:"pi2/class.tx_icsodappstore_pi2_wizicon.php";s:4:"7606";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"cf3c";s:14:"pi3/ce_wiz.gif";s:4:"02b6";s:34:"pi3/class.tx_icsodappstore_pi3.php";s:4:"c6d8";s:42:"pi3/class.tx_icsodappstore_pi3_wizicon.php";s:4:"c009";s:13:"pi3/clear.gif";s:4:"cc11";s:17:"pi3/locallang.xml";s:4:"56cb";s:14:"pi4/ce_wiz.gif";s:4:"02b6";s:34:"pi4/class.tx_icsodappstore_pi4.php";s:4:"c1ed";s:42:"pi4/class.tx_icsodappstore_pi4_wizicon.php";s:4:"1b34";s:13:"pi4/clear.gif";s:4:"cc11";s:17:"pi4/locallang.xml";s:4:"8d76";s:15:"res/default.jpg";s:4:"d894";s:15:"res/default.png";s:4:"71b2";s:13:"res/script.js";s:4:"b38c";s:18:"res/tablefilter.js";s:4:"d345";s:17:"res/template.html";s:4:"b658";s:18:"res/css/styles.css";s:4:"6410";s:23:"res/css/img/default.jpg";s:4:"d894";s:23:"res/css/img/default.png";s:4:"71b2";s:24:"res/css/img/download.jpg";s:4:"48d2";s:26:"res/css/img/masque-min.png";s:4:"4718";s:23:"res/css/img/masque1.png";s:4:"0152";s:23:"res/css/img/masque2.png";s:4:"5908";s:19:"res/img/default.jpg";s:4:"d894";s:19:"res/img/default.png";s:4:"71b2";s:41:"scheduler/class.tx_icsodappstore_task.php";s:4:"9e99";s:19:"sqlres/makestat.sql";s:4:"1546";s:30:"sqlres/putbackzeroapicount.sql";s:4:"defe";s:20:"static/constants.txt";s:4:"c279";s:16:"static/setup.txt";s:4:"7229";}',
    'suggests' => array(
    ),
);

?>