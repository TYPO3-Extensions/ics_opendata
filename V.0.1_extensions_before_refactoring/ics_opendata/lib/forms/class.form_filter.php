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
 * Form : set parameters for the selected filter
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormFilter
        implements tx_icsopendata_Form
{
    // === ATTRIBUTS ============================================================================== //
	
	private $_extkey = 'ics_opendata';
	
    // === OPERATIONS ============================================================================= //

    /**
     * Short description of method validInput
     *
     * @return Integer
     */
    public function validInput()
    {
		$post = t3lib_div::_POST();
		$errors = Array();

		$GLOBALS['repository']->set('errors',$errors);
		
		if(!empty($errors))
			return false;
			 
        return true;
    }

    /**
     * Short description of method renderForm
     *
     * @param  $FormData
     * @return String
     */
    public function renderForm($FormData, $pObj)
    {
		// Get data
		$post = t3lib_div::_POST();
		$commandselected = $post['commandselected'];
		$commands = $GLOBALS['repository']->get('commands');
		$command = $commands[$commandselected];

		$filterselected = $post['filterselected'];
		if( $filterselected == 'newfilter' ) {
			$filterselected = $command->countFilters();
			// Add a new filter
			$commandsfields = $GLOBALS['repository']->get('commandsfields');
			$filter = t3lib_div::makeInstance('tx_icsopendata_DataFilter');
			$filter->initFilter($post['filtertype'], $command);
			$filter->setName('filter' . $command->countFilters());
			$command->addFilter($filter);
			if( empty($commandsfields) )
				$commandsfields = Array();

			if( !isset($commandsfields[$commandselected]) )
					$commandsfields[$commandselected] = Array();	
					
			$GLOBALS['repository']->set('commandsfields', $commandsfields);
			
		}
		else {
			$filter = $command->getFilter($post['filterselected']);
		}

		$basexml = $GLOBALS['repository']->get('basexml');
		
		if( isset($post['commandtable']) ) {
			$commandsourceid = $post['commandtable']['source'];
		}
		else {
			$commandsourceid = $post['commandsourceid'];
		}
		
		// Main content header
		$content .= '
					<h2>' . $GLOBALS['LANG']->getLL('filter.title') . '</h2>
					<input type="hidden" name="action" value="next"/>
					<input type="hidden" name="editfilter" value="' . $filterselected . '"/>
					<input type="hidden" name="commandsourceid" value="' . $commandsourceid . '"/>
					<input type="hidden" id="' . $this->_extkey . 'commandselected" name="commandselected" value="' . $commandselected . '">
					<input type="hidden" id="' . $this->_extkey . 'filterselected" name="filterselected" value="' . $filterselected . '"/>';
		
		// Display Error
		$errors = $GLOBALS['repository']->get('errors');
		if(isset($errors) && !empty($errors)){
			$content .= '
					<div class="error">';
			foreach($errors as $err){
				$content .= '
						<p> ERROR : ' . $err . '</p>';
			}
			$content .= '
					</div>';
		}
		
		$content .= '
					<h3>' . $GLOBALS['LANG']->getLL('filter.help') . '</h3>
					<div class="commandname">' . $GLOBALS['LANG']->getLL('filter.command') . ' : ' . $command->getName() . '</div>';

		// Filter name
		$filtername = $filter->getName();
		if( isset($post['newfiltername']) )
			$filtername = $post['newfiltername'];
		$content .= '
					<div class="newfiltername">
						' . $GLOBALS['LANG']->getLL('filter.newname') . ' : 
						<input type="text" name="newfiltername" value="' . $filtername  . '">
					</div>';
		
		// Parameters
		$content .= '
					<fieldset>
						<legend>' . $GLOBALS['LANG']->getLL('filter.parameters') . '</legend>
						<table>
							<thead>
								<th>Filter label</th>
								<th>Command parameter<th>
							</thead>
							<tbody>';
		for($i=0 ; $i<$filter->getParamCount() ; $i++) {
			$content .= '
								<tr>
									<td><label for="' . $this->extkey . '_param_' . $i . '" >' . $filter->getParamLabel($i) . '</label></td>
									<td>
										<select name="params[' . $i . ']">';
			for($j=0 ; $j<$command->countParams() ; $j++) {
				$content .= '
											<option value="' . $command->getParam($j) . '" ';
				if( ($filter->getParam($i) == $command->getParam($j)) || ($post['params'][$i] == $command->getParam($j)))
					$content .= 'selected="selected"';
				$content .= '>' . $command->getParam($j) . '</option>';
			}
			$content .= '
										</select>
									</td>
								</tr>';
		}
		$content .= '
							</tbody>
						</table>
					</fieldset>';
		
		// Fields
		$basexml = $GLOBALS['repository']->get('basexml');
		$content .= '
					<fieldset>
						<legend>' . $GLOBALS['LANG']->getLL('filter.fields') . '</legend>';
						
						
		$content .= '
						' . $GLOBALS['LANG']->getLL('filter.commandtable') . ' : ' . $command->getTableXml()->getName() . '
						<table>
							<thead>
								<th>' . $GLOBALS['LANG']->getLL('filter.filterlabel') . '</th>
								<th>' . $GLOBALS['LANG']->getLL('table') . '</th>
								<th>' . $GLOBALS['LANG']->getLL('field') . '</th>
							</thead>
							<tbody>';
		$commandsfields= $GLOBALS['repository']->get('commandsfields');
		
		$filterfields = $post['fields'];
		if( empty($filterfields) ) {
			$filterfields = $commandsfields[$commandselected][$post['filterselected']];
		}

		for($i=0 ; $i<$filter->getLinkCount() ; $i++) {
			$filterfield = $filterfields[$i];
			$content .= '
								<tr>
									<td>
										<label for=' . $this->extkey . '_param_' . $i . '" >' . $filter->getLinkLabel($i) . '</label>
										<input type="hidden" name="fields[' . $i . '][source]" value="' . $commandsourceid . '">
									</td>
									<td>
										<select name="fields[' . $i . '][table]" onchange="' . htmlspecialchars('
											document.getElementById("' . $this->_extkey . 'filterselected").value = "' . $filterselected . '";
											document.getElementById("opendataform").submit();') . '">
											<option value=""></option>';
				
			$sourcexml = $basexml->getXmlChild($commandsourceid);;
			if(	!empty($sourcexml) ) {
				$tablesxml = $sourcexml->getAllChildren();
				foreach($tablesxml as $tableid=>$tablexml) {
					$content .= '
										<option value="' . $tableid . '" ';
					if( isset($filterfield['table']) && ($filterfield['table'] == $tableid) )
						$content .= 'selected="selected"';
					$content .= '>' . $tablexml->getName() . '</option>';
				}
			}
			$content .= '
										</select>
									</td>
									<td>
										<select name="fields[' . $i . '][field]">
											<option value=""></option>';
			if(	!empty($sourcexml) ) {
				$tablexml = $sourcexml->getXmlChild($filterfield['table']);
				if(	!empty($tablexml) ) {
					$fieldsxml = $tablexml->getAllChildren();
					foreach($fieldsxml as $fieldid=>$fieldxml) {
						$content .= '
										<option value="' . $fieldid . '" ';
						if( isset($filterfield['field']) && ($filterfield['field'] == $fieldid) )
							$content .= 'selected="selected"';
						$content .= '>' . $fieldxml->getName() . '</option>';
					}
				}
			}
			$content .= '
										</select>
									</td>
								</tr>';
		}
		$content .= '
							</tbody>
						</table>
					</fieldset>';
		
		// Activation
		$content .= '
					<fieldset>
						<legend><input type="checkbox" name="activate" ';
		if( ($filter->getActivationParam() != null) || isset($post['activate']))
			$content .= 'checked="checked"';
		$content .= '> ' . $GLOBALS['LANG']->getLL('filter.activation') . '</legend>';
		
		$content .= '
						<div class="activation">
							<label for="' . $this->extkey . 'activation" >' . $GLOBALS['LANG']->getLL('filter.activationparam') . ' : </label>
							<select id="' . $this->extkey . 'activation" name="activationparam">';
							
		$activationparam = $filter->getActivationParam();
		if( isset($post['activationparam']) )
			$activationparam = $post['activationparam'];
		$activationvalue = $filter->getActivationValue();
		if( isset($post['activationvalue']) )
			$activationvalue = $post['activationvalue'];
			
		for($j=0 ; $j<$command->countParams() ; $j++) {
			$content .= '
								<option value="' . $command->getParam($j) . '" ';
			if( $activationparam == $command->getParam($j) )
				$content .= 'selected="selected"';
			$content .= '>' . $command->getParam($j) . '</option>';
		}
		$content .= '
							</select>
							<label for="' . $this->extkey . 'activationvalue" >' . $GLOBALS['LANG']->getLL('filter.activationvalue') . ' : </label>
							<input id="' . $this->extkey . 'activationvalue" type="text" name="activationvalue" value="' . $activationvalue . '">
						</div>
					</fieldset>';
	
		$content .= '
					<input type="submit" name="cancel" value="' . $GLOBALS['LANG']->getLL('cancel') . '">
					<input type="submit" name="updatefilter" value="' . $GLOBALS['LANG']->getLL('update') . '">';
		
        return $content;
    } // End renderForm
	
    /**
     * Short description of method getNextForm
     *
     * @return Integer
     */
    public function getNextFormId()
    {
        $errors = $GLOBALS['repository']->get('errors');
		$post = t3lib_div::_POST();
		
		if(!empty($errors)){
			return 'filter';
		}
		
		if( isset($post['updatefilter']) || isset($post['cancel']) )
			return 'commandprofile';
			
		return 'filter';
    }

} /* end of class tx_icsopendata_FormFilter */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_filter.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_filter.php']);
}

?>