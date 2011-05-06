<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 In Cité Solution <technique@in-cite.net>
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
/*
 * $Id$
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

$LANG->includeLLFile("EXT:ics_opendata/modfunc1/locallang.xml");

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

// Get repository
$rep = t3lib_div::_GP('repository');
if( !empty($rep) ){
	$repository = unserialize(gzuncompress(base64_decode(t3lib_div::_GP('repository'))));
}
else{
	$repository = t3lib_div::makeInstance('tx_icsopendata_Repository');
}

/**
 * Module extension (addition to function menu) 'Create a new opendata extension' for the 'ics_opendata' extension.
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_modfunc1 extends t3lib_extobjbase {
		
	// === ATTRIBUTS ============================================================================== //
	
	private $_manager = null;
	private $_extkey = 'ics_opendata';
	
	// === OPERATIONS ============================================================================= //
	
	/**					
	 * Makes the content for the overview frame...
	 *
	 * @return	HTML
	 */
	public function overview_main()	{
		$icon = '<img src="'.$this->backPath.t3lib_extMgm::extRelPath("ics_opendata").'ext_icon.gif" width=18 height=16 class="absmiddle">';
		$content = $this->mkMenuConfig($icon.$this->headLink(tx_icsopendata_modfunc1,1),'',$this->overviewContent());

		return $content;
	}

	/**
	 * Main method
	 *
	 * @return	HTML
	 */
	public function main() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
		// Initialization
		$this->_manager = t3lib_div::makeInstance('tx_icsopendata_FormManager');
		
		// Retrieve main content 
		$content = $this->mainContent();

		return $content;
	}

	/**
	 * Returns content in overview frame
	 *
	 * @return	Content for overview frame
	 */
	public function overviewContent()	{
		$content = 'Content in overview frame...';
		return '<a href="index.php?SET[function]=tx_icsopendata_modfunc2"  onClick="this.blur();"><img src="'.$this->backPath.'gfx/edit2.gif" style="float: left;"></a><div><a href="index.php?SET[function]=tx_icsopendata_modfunc2"  onClick="this.blur();">'.$content.'</a></div>';
	}

	/**
	 * Return main content
	 *
	 * @return	Main content for the module
	 */
	public function mainContent()	{
		
		$post = t3lib_div::_POST();
		
		// Get current formid
		$formid = $post['formid'];				
		
		// Render form
		$action = $post['action'];

		/**
		*	switch(action)
		*	'next'       : return the next $form depending on _POST data and current $formid
		*	'analyze'    : analyse the source and return the next form
		*	'generate'   : generate html code and return the next form
		*	'default'    : return the first form
		*/
		switch ($action){
			case 'next' : 
				$formid = $this->_manager->nextAction($formid,'next');
				break;
			case 'analyze' :
				$formid = $this->_manager->nextAction($formid,'analyze');
				break;
			case 'link' :
				$formid = $this->_manager->nextAction($formid,'link');
				break;
			default : 
				$formid = $this->_manager->nextAction($formid,'next');
				break;
		}
		$form = $this->_manager->getForm($formid);
		
		// Render form content
		if( !empty($form) ){
			$formdata = $this->_manager->getFormData($formid);
			$formcontent = $form->renderForm($formdata, $this->pObj);
		}
		else{
			$formcontent = '';
		}

		$maincontent .= '
								<div class="opendataform">
									' . $formcontent . '
									<input type="hidden" name="formid" value=' . $formid . '>
								</div>';
		// Render menu
		$menu = $this->_manager->getFormMenu();
		if( !empty($menu) ) {
			$menucontent = '
								<div class="opendatamenu">' . $menu->renderForm(null, $this->pObj) . '
								</div>';		
		}
		else {
			$menucontent = 'Menu error';
		}
		
		// Send repository
		$rep = htmlspecialchars(base64_encode(gzcompress(serialize($GLOBALS['repository']))));
		
		// Build main content
		$content = '
			<h2>' . $GLOBALS['LANG']->getLL('extensiontitle') . '</h2>
			<h3>' . $GLOBALS['LANG']->getLL('extensionsubtitle') . '</h3>
			<table>
				<tbody>
					<tr>
						<td>
							<form action="" method="POST" name="opendatamenu" id="opendatamenu" onsubmit="return true;" enctype="multipart/form-data">
								<input type="hidden" name="repository" value="' . $rep . '"/>
								' . $menucontent . '
							</form>
						</td>
						<td>   </td>
						<td>
							<form action="" method="POST" name="opendataform" id="opendataform" onsubmit="return true;" enctype="multipart/form-data">
								<input type="hidden" name="repository" value="' . $rep . '"/>
								' . $maincontent . '
							</form>
						</td>
					</tr>
				</tbody>
			</table>';

		return $content;
	}
	
} /* End of class tx_icsopendata_modfunc1 */



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/modfunc1/class.tx_icsopendata_modfunc1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/modfunc1/class.tx_icsopendata_modfunc1.php']);
}

?>