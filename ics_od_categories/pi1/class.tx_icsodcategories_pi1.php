<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Plan.Net France <typo3@plan-net.fr>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Plugin 'Directory categories' for the 'ics_od_categories' extension.
 *	
 * @author	Tsi YANG <tsi.yang@plan-net.fr>
 * @package	TYPO3
 * @subpackage	ics_od_categories
 */
class tx_icsodcategories_pi1 extends tslib_pibase {
    public $prefixId      = 'tx_icsodcategories_pi1';        // Same as class name
    public $scriptRelPath = 'pi1/class.tx_icsodcategories_pi1.php';    // Path to this script relative to the extension dir.
    public $extKey        = 'ics_od_categories';    // The extension key.
    
    /**
     * The main method of the Plugin.
     *
     * @param string $content The Plugin content
     * @param array $conf The Plugin configuration
     * @return string The content that is displayed on the website
     */
    public function main($content, array $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj = 1;    // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		
		$this->pi_initPIflexForm();
	
		// Get template file
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'templateFile', 'general');
		$this->template = $this->cObj->fileResource($templateFile ? $templateFile : $this->conf['templateFile']);
		
		$template = $this->cObj->getSubpart($this->template, '###TEMPLATE_DIRECTORY_CATEGORIES###');
		
		//TODO: récupérer les catégories de niveau 1 uniquement utliseés => cf tools
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_icsodcategories_categories',
			'parent=0' . $this->cObj->enableFields('tx_icsodcategories_categories')
		);
		// TODO : pour chaque catégorie afficher le titre + lien vers page de résultats
		$markers = array();
		$subpartArray['###CATEGORIES_GROUP###'] = $this->renderCategories($rows, $this->cObj->getSubpart($template, '###CATEGORIES_GROUP###'));
		
		$template = $this->cObj->substituteSubpartArray($template, $subpartArray);
		$content = $this->cObj->substituteMarkerArray($template, $markers);

 
        return $this->pi_wrapInBaseClass($content);
    }
	
	function renderCategories($categories, $template) {
		// t3lib_div::debug($template, 'template');
		$itemTemplate = $this->cObj->getSubpart($template, '###CATEGORIES_ITEM###');
		$itemContent = '';
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		foreach ($categories as $cat) {
			$cObj->start($cat, 'Category');
			$cObj->setParent($this->data, $this->currentRecord);
			$markers = array(
				'NAME' => $cObj->stdWrap('', $this->conf['dataset.']['category.']['name.'])
			);
			$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $markers, '###|###');
		}
		return $this->cObj->substituteSubpartArray($template, array('###CATEGORIES_ITEM###' => $itemContent));
	}

}



if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ics_od_categories/pi1/class.tx_icsodcategories_pi1.php'])) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ics_od_categories/pi1/class.tx_icsodcategories_pi1.php']);
}
?>