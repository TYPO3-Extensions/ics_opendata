<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}
$tempColumns = array (
    'tx_smileicsoddatastorelicense_acceptcgu' => array (
        'exclude' => 0, 
        'label' => 'LLL:EXT:smile_icsoddatastore_license/locallang_db.xml:tx_icsoddatastore_licences.tx_smileicsoddatastorelicense_acceptcgu',
        'config' => array (
            'type' => 'check',
        )
    ),
);


t3lib_div::loadTCA('tx_icsoddatastore_licences');
t3lib_extMgm::addTCAcolumns('tx_icsoddatastore_licences',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tx_icsoddatastore_licences','tx_smileicsoddatastorelicense_acceptcgu;;;;1-1-1');
?>