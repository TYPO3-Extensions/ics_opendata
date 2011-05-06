<?php
/*
 * $Id$
 */
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE')	{
	t3lib_extMgm::insertModuleFunction(
		'tools_em',		
		'tx_icsopendata_modfunc1',
		t3lib_extMgm::extPath($_EXTKEY).'modfunc1/class.tx_icsopendata_modfunc1.php',
		'LLL:EXT:ics_opendata/locallang_db.xml:moduleFunction.tx_icsopendata_modfunc1'
	);
}


if (TYPO3_MODE == 'BE')	{
	t3lib_extMgm::insertModuleFunction(
		'tools_em',		
		'tx_icsopendata_modfunc2',
		t3lib_extMgm::extPath($_EXTKEY).'modfunc2/class.tx_icsopendata_modfunc2.php',
		'LLL:EXT:ics_opendata/locallang_db.xml:moduleFunction.tx_icsopendata_modfunc2'
	);
}
?>