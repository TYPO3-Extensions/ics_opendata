<?php
class tx_smileicsoddatastorelicense_additionalFieldsMarkers{
	function additionalFieldsMarkers(&$markers, &$subpartArray, &$template, &$row, &$conf, &$pObj){
		$this->pObj = $pObj;
		
		// Get data licence
		$licence = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
			'`name`, `tx_smileicsoddatastorelicense_acceptcgu`, `link`',
			'`tx_icsoddatastore_licences`',
			'`uid` = ' . $row['licence']
		);
		if (is_array($licence) && !empty($licence) && $licence['tx_smileicsoddatastorelicense_acceptcgu']) {
			$GLOBALS['TSFE']->additionalHeaderData[$this->pObj->extKey]='<script src="typo3conf/ext/smile_icsoddatastore_license/res/script.js" type="text/javascript"></script>';
			if($GLOBALS["TSFE"]->fe_user->user){
				$sesType = 'user';
			}else{
				$sesType = 'ses';
			}
			$GP = t3lib_div::_GP($this->pObj->prefixId) ;
			if(isset($GP['cgu'])){
				$this->storeSessionValues($sesType, 'cgu_accepted', $GP['cgu']);
			}

			if($this->getSessionValues($sesType, 'cgu_accepted') != 'on'){
				
				// Hide data files linked
				$pictoItem = htmlspecialchars($this->pObj->pi_getLL('accept_license_to_download')) ;
				$subpartArray['###SECTION_FILE_HIDE###'] = $this->pObj->cObj->substituteMarkerArray($pictoItem, $markers);

				// CGU Subpart
				$cguField = $this->pObj->cObj->getSubpart($template, '###CGU_FIELD###');
				
				$configurations['parameter'] = $licence['link'];
				$fileSize = filesize('fileadmin/tpl_opendata/ext/ics_od_appstore/res/SOMMAIRE_ANNUEL_version_web.pdf');
				$fileSize = t3lib_div::formatSize($fileSize, ' o| ko| Mo| Go');
				$typolink = $this->pObj->cObj->typolink(sprintf($this->pObj->pi_getLL('cgu_link_label'),$fileSize), $configurations);
				
				// Render data files not linked
				$confPicto = $pObj->conf['datasetFile.'];
				$pObj->conf['datasetFile.'] = $pObj->conf['datasetFileNotLinked.'];
				$filesContent = $this->pObj->renderFiles('SINGLE', $row['uid'], $this->pObj->cObj->getSubpart($template, '###SECTION_FILE_NOTLINKED###'));
				$pObj->conf['datasetFile.'] = $confPicto;
				
				$markersCGU = array(
					'###URL_LICENSE###' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),
					'###BTN_REGISTRATION###' => $this->pObj->prefixId.'[btn_registration]',
					'###BTN_REGISTRATION_VALUE###' => htmlspecialchars($this->pObj->pi_getLL('btn_registration')),
					'###CGU###' => $this->pObj->prefixId.'[cgu]' ,
					'###CGU_LABEL###' => $this->pObj->pi_getLL('cgu_label'),
					'###CGU_LINK###' => $typolink,
					'###UID_VALUE###' => $row['uid'],
				);
				$subpartsCGU = array('###SECTION_FILE_NOTLINKED###' => $filesContent);
				
				$subpartArray['###CGU_FIELD###'] = $this->pObj->cObj->substituteMarkerArrayCached($cguField, $markersCGU, $subpartsCGU);
			}
		} else {
			$subpartArray['###CGU_FIELD###'] = '';
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$type: ...
	 * @param	[type]		$var: ...
	 * @param	[type]		$content: ...
	 * @return	[type]		...
	 */
	function storeSessionValues($type, $var, $content) {
		$GLOBALS['TSFE']->fe_user->setKey($type, $var, $content);
		$GLOBALS['TSFE']->storeSessionData();
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$type: ...
	 * @param	[type]		$var: ...
	 * @return	[type]		...
	 */
	function getSessionValues($type, $var) {
        return $GLOBALS["TSFE"]->fe_user->getKey($type, $var);
    }
}
?>