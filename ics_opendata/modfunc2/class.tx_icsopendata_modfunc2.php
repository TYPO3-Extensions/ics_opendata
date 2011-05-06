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

 // Include LL file
$LANG->includeLLFile("EXT:ics_opendata/modfunc1/locallang.xml");

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
 
// Get repository (used yo store session data)
$rep = t3lib_div::_GP('repository');
if( !empty($rep) ){
	$repository = unserialize(gzuncompress(base64_decode(t3lib_div::_GP('repository'))));
}
else{
	$repository = t3lib_div::makeInstance('tx_icsopendata_Repository');
}


/**
 * Module extension (addition to function menu) 'Edit opendata extension' for the 'ics_opendata' extension.
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_modfunc2 extends t3lib_extobjbase 
{
	// === ATTRIBUTS ============================================================================== //
	
	private $_manager = null;
	private $_extkey = 'ics_opendata';
	
	// === OPERATIONS ============================================================================= //
	
	/**
	 * Returns the module menu
	 *
	 * @return	Array with menuitems
	 */
	function modMenu()	{
		global $LANG;

		return Array (
			"tx_icsopendata_modfunc2_check" => "",
		);
	}

	/**
	 * Main method of the module
	 *
	 * @return	HTML
	 */
	function main()	{
			// Initializes the module. Done in this function because we may need to re-initialize if data is submitted!
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Initialization
		$this->_manager = t3lib_div::makeInstance('tx_icsopendata_FormManager');
		
		// Get main content 
		$content = $this->mainContent();

		return $content;
	}
	
	function mainContent()
	{
		$post = t3lib_div::_POST();
		// Get current formid
		$formid = $post['formid'];	
		$rep = $post['repository'];
		
		if( isset($post['loadrepository']) ) {
			$loadform = $this->_manager->getForm('loadext');
			if( !empty($loadform) ) {
				if( $loadform->validInput() ) {
					$repcontent = file_get_contents($post['loadedrepository'] . '/doc/ics_od_repository.dat');
					$repository = unserialize(gzuncompress(base64_decode($repcontent)));
					if( is_a($repository, 'tx_icsopendata_Repository') ) {
						$GLOBALS['repository'] = $repository;
						$rep = true;
					}
				}
			}
		}

		if( empty($rep) ) {
			
			$loadform = $this->_manager->getForm('loadext');
			if( !empty($loadform) ) {
				$content = '
						<form action="" method="POST" name="opendataform" id="opendataform" onsubmit="return true;" enctype="multipart/form-data">
								' . $loadform->renderForm(null, $this->pObj) . '
						</form>';
			}
		}
		else {

			// Render form
			$action = $post['action'];

			/**
			*	switch(action)
			*	'next'       : return the next $form depending on _POST data and current $formid
			*	'analyze'    : analyse the source and return the next form
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
			
			if( empty($formid) )
				$formid = 'menu';
			
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
		}
		
		return $content;
	}
} /* End of class tx_icsopendata_modfunc2 */


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/modfunc2/class.tx_icsopendata_modfunc2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/modfunc2/class.tx_icsopendata_modfunc2.php']);
}

?>