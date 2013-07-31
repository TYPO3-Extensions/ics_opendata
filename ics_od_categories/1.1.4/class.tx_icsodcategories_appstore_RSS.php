<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Smile <contact@smile.fr>
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

class tx_icsodcategories_appstore_RSS {

	
	function additionalFieldsRSSMarkers(&$markerArrayItem, $subpartItem, $tplContentItem, $data, $conf, $pObj)
	{
// 		var_dump('toto');
		
		$tools = t3lib_div::makeInstance('tx_icsodcategories_tools');
		$tools->init($pObj->tables['applications'], $pObj->cObj);
		
		$categories = $tools->getCategoriesElement($data['uid']);
		
		if (is_array($categories) && !empty($categories)) {
			$categoriesName =array();
			foreach ($categories as $category) {
				$categoriesName[] = trim($category['name']);
			}
			$vCategories = implode(', ', $categoriesName);
			$markerArrayItem['###ITEM_THEMATIQUES###'] = $pObj->cObj->stdWrap(htmlspecialchars($vCategories), $conf['categories_stdWrap.']);
		}
		else 
		{
			$markerArrayItem['###ITEM_THEMATIQUES###'] = '';
		}
	}

}

?>