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
 * Form : list of command filter and command parameter
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormCommandProfile
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
		if( empty($post['ics_opendata']['menuCmd']) ) {
			if( empty($post['commandname']) )
				$errors[] = 'ERROR_EMPTY_COMMAND_NAME';
		}
		
		if( !empty($post['addparam']) && empty($post['newparam']) )
			$errors[] = 'ERROR_NEW_PARAMETER_NAME_NOT_SET';
		
		$commandtable = $post['commandtable'];
		if( $post['filterselected'] != null ) {
			if( empty($commandtable['source'] ) ) {
				$errors[] = 'ERROR_SOURCE_NOT_SET';
			}
			elseif( empty($commandtable['table']) ) {
				$errors[] = 'ERROR_TABLE_NOT_SET';
			}
		}
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
		if( empty($commands) )
			$commands = array();
		$command = $commands[$commandselected];
		
		if( empty($command) ){
			// Create a new command
			$command = t3lib_div::makeInstance('tx_icsopendata_DataCommand');
			if( empty($commands) ) {
				$commandselected = 0;
			}
			else {
				$commandselected = max(array_keys($commands)) + 1;
			}
			$command->initCommand('newcommand', $commandselected);
			$commands[$commandselected] = $command;
		}
		
		// Update command
		if( isset($post['commandname'] ) )
			$command->setName(htmlspecialchars($this->cleanStr($post['commandname'])));
		
		$this->updateCommand($command, $commandselected);
		
		$GLOBALS['repository']->set('commands', $commands);

		// Main content header
		$content .= '
				<h2>' . $GLOBALS['LANG']->getLL('commandprofile.title') . '</h2>
				<input type="hidden" name="action" value="next"/>
				<input type="hidden" id="' . $this->_extkey . 'addparam" name="addparam"/>
				<input type="hidden" id="' . $this->_extkey . 'deleteparam" name="deleteparam"/>
				<input type="hidden" id="' . $this->_extkey . 'orderparamup" name="orderparamup"/>
				<input type="hidden" id="' . $this->_extkey . 'orderparamdown" name="orderparamdown"/>
				<input type="hidden" id="' . $this->_extkey . 'gotofilter" name="gotofilter"/>
				<input type="hidden" id="' . $this->_extkey . 'commandselected" name="commandselected" value="' . $commandselected . '">
				<input type="hidden" id="' . $this->_extkey . 'commanddeleted" name="commanddeleted">
				<input type="hidden" id="' . $this->_extkey . 'filterselected" name="filterselected">
				<input type="hidden" id="' . $this->_extkey . 'deletefilter" name="deletefilter"/>';
		
		// Display Error
		$errors = $GLOBALS['repository']->get('errors');
		if(isset($errors) && !empty($errors)){
			$content .= '
						<div class="error">';
			foreach($errors as $err){
				$content .= "<p> ERROR : " . $err . "</p>";
			}
			$content .= '
						</div>';
		}
		
		$content .= "
				<h3>" . $GLOBALS['LANG']->getLL('commandprofile.help') . "</h3>";

		// Display Command Warning
		$warnings = $command->getWarning();
		if( !empty($warnings) ){
			$content .= '
						<div class="warning">';
			foreach($warnings as $war){
				$content .= '
							<p>' . t3lib_iconWorks::getSpriteIcon('status-dialog-warning'). ' ' . $war . '</p>';
			}
			$content .= '
						</div>';
		}
		
		$content .= '
				<fieldset>
					<legend>
						' . $GLOBALS['LANG']->getLL('commandprofile.command') . ' : <input type="text" name="commandname" value="' . $command->getName() . '">
					</legend>';
					
		// --- table selection
		$basexml = $GLOBALS['repository']->get('basexml');
		if( !empty($basexml) ) {
			$tablexml = $command->getTableXml();
			if( isset($post['commandtable']) ) {
				$commandtable = $post['commandtable'];
				$sourcexml = $basexml->getXmlChild($commandtable['source']);
			}
			else {
				if( !empty($tablexml) ) {
					$sourcexml = $tablexml->getXmlParent();
				}
			}
		}
		$content .= '
					<label for=' . $this->extkey . '_table" >' . $GLOBALS['LANG']->getLL('commandprofile.tableselection') . ' : </label>
					<select name="commandtable[source]" onchange="' . htmlspecialchars('
								document.getElementById("' . $this->_extkey . 'commandselected").value="' . $commandselected . '";
								document.getElementById("opendataform").submit();') . '
							">
						<option value=""></option>';
		if( !empty($basexml) ) {
			$sourcesxml = $basexml->getAllChildren();
			foreach($sourcesxml as $sourceid=>$source) {
				$content .= '
						<option value="' . $sourceid . '" ';
				if( (isset($commandtable['source']) && ($commandtable['source'] == $sourceid)) ||  (!empty($tablexml) && $sourcexml === $source) )
					$content .= 'selected="selected"';
				$content .= '>' . $source->getName() . '</option>';
			}
		}
		$content .= '
					</select>
					<select name="commandtable[table]" onchange="' . htmlspecialchars('
							document.getElementById("' . $this->_extkey . 'commandselected").value="' . $commandselected . '";
							document.getElementById("opendataform").submit();') . '">
						<option value=""></option>';
		if( !empty($basexml) ) {
			if (!empty($sourcexml) ) {
				$tablesxml = $sourcexml->getAllChildren();
				foreach($tablesxml as $tableid=>$table) {
					$content .= '
						<option value="' . $tableid . '" ';
					if( (isset($commandtable['table']) && ($commandtable['table'] == $tableid)) ||  (!empty($tablexml) && $tablexml === $table))
						$content .= 'selected="selected"';
					$content .= '>' . $table->getName() . '</option>';
				}
			}
		}
		$content .= '
					</select>
				</fieldset>';
		
		// Display parameters
		$content .= '
				<fieldset>
					<legend>
						' . $GLOBALS['LANG']->getLL('commandprofile.parameters') . '
					</legend>';
	
		// --- add new parameter
		$content .= '
					<div>' . $command->getName() . ' ( ';
		$content .= '
						<table>
							<thead>
								<th>Parameter</th>
								<th>Default value</th>
								<th>Required</th>
							</thead>
							<tbody>';
		for($i=0 ; $i<$command->countParams() ; $i++) {
			$content .= '
								<tr>
									<td>
										<a href="#" onclick="' . htmlspecialchars('
											document.getElementById("' . $this->_extkey . 'deleteparam").value = "' . $i . '"; 
											document.getElementById("opendataform").submit();') . '">
											' . t3lib_iconWorks::getSpriteIcon('actions-edit-delete')  . '
										</a>
										<a href="#" onclick="' . htmlspecialchars('
											document.getElementById("' . $this->_extkey . 'orderparamup").value = "' . $i . '"; 
											document.getElementById("opendataform").submit();') . '">
											' . t3lib_iconWorks::getSpriteIcon('actions-move-up')  . '
										</a>
										<a href="#" onclick="' . htmlspecialchars('
											document.getElementById("' . $this->_extkey . 'orderparamdown").value = "' . $i . '"; 
											document.getElementById("opendataform").submit();') . '">
											' . t3lib_iconWorks::getSpriteIcon('actions-move-down')  . '
										</a>
										' . $command->getParam($i) . '
									</td>
									<td>
										' . $command->getDefaultValue($i) . '
									</td>
									<td>
										' . $command->isParamRequired($i) . '
									</td>
								</tr>';
		}
		$content .= '
								<tr>
									<td>
										<a href="#" onclick="' . htmlspecialchars('
											document.getElementById("' . $this->_extkey . 'addparam").value = 1; 
											document.getElementById("' . $this->_extkey . 'commandselected").value = "' . $commandselected . '";
											document.getElementById("opendataform").submit();') . '">
											' . t3lib_iconWorks::getSpriteIcon('actions-edit-add')  . '
										</a>
										<input type="text" name="newparam" value="NewParam" onfocus="' . htmlspecialchars("
											if(this.value == this.defaultValue) {this.value = '';}") . '">
									</td>
									<td>
										<input type="checkbox" name="newparamhasdefault" value="true">
										<input type="text" name="newparamdefaultvalue" value="Default Value" onfocus="' . htmlspecialchars("
											if(this.value == this.defaultValue) {this.value = '';}") . '">
									</td>
									<td>
										<input type="checkbox" name="newparamrequired" value="true">
										Required ?
									</td>
								</tr>
								<tr><td>)</td></tr>
							</tbody>
						</table>
					</div>
				</fieldset>';
		
		// Display filters
		$content .= '
				<fieldset>
					<legend>' . $GLOBALS['LANG']->getLL('commandprofile.filters') . '</legend>
					<div class ="filter">';
		$xmltable = $command->getTableXml();
		if( !empty($command) && $command->countFilters()>0 ) {
			// --- foreach filter
			for($i=0 ; $i<$command->countFilters() ; $i++) {
				$filter = $command->getFilter($i);
				// --- delete filter
				$content .= '
						<a href="#" onclick="' . htmlspecialchars('
							document.getElementById("' . $this->_extkey . 'deletefilter").value = "' . $i . '"; 
							document.getElementById("opendataform").submit();') . '">
							' . t3lib_iconWorks::getSpriteIcon('actions-edit-delete')  . '
						</a>';
				// --- edit filter 
				if( !empty($xmltable) ) {
					$content .= '
						<a href="#" onclick="' . htmlspecialchars('
							document.getElementById("' . $this->_extkey . 'filterselected").value = "' . $i . '";
							document.getElementById("' . $this->_extkey . 'gotofilter").value = "1";
							document.getElementById("opendataform").submit();') . '">
							' . t3lib_iconWorks::getSpriteIcon('actions-document-open')  . '
						</a>';
				}
				$content .= '
						<strong>' . $filter->getName() . '</strong> (' . $filter->getFilterType() . ')';
				
				$activation = $filter->getActivationParam();
				if( !empty($activation) ) {
					$content .= '
						<p>
							' . $GLOBALS['LANG']->getLL('commandprofile.activationparam') . ' : ' . $filter->getActivationParam() . ' (==' . $filter->getActivationValue() . ')
						</p>';
				}
				// --- column
				$content .= '
						<table>
							<thead>
								<th>Links</th>
								<th>Links Labels</th>
								<th>Params Labels</th>
								<th>Params</th>
							</thead>
							<tbody>';
				// --- links and params 
				$nbit = max( $filter->getParamCount(), $filter->getLinkCount() );
				for($j = 0; $j<$nbit ; $j++) {
					$content .= '
								<tr>';
					// --- link
					$link = $filter->getLink($j);
					$content .= '
									<td>';
					if( !empty($link) ) {
						$field = $link->getField();
						if( !empty($field) )
							$content .= $field->getName() . ' (' . $link->getInputType() . ')';
					}
					$content .= '
									</td>';
					// --- linklabel
					$linklabel = $filter->getLinkLabel($j);
					$content .= '
									<td>';
					if( !empty($linklabel) ) {
						$content .= $linklabel;
					}
					$content .= '
									</td>';
					// --- paramlabel
					$paramlabel = $filter->getParamLabel($j);
					$content .= '
									<td>';
					if( !empty($paramlabel) ) {
						$content .= $paramlabel;
					}
					$content .= '
									</td>';
					// --- param
					$param = $filter->getParam($j);
					$content .= '
									<td>';
					if( !empty($param) ) {
						$content .= $param;
					}
					$content .= '
									</td>
								</tr>';
				}
				$content .= '
							</tbody>
						</table>';
			}
			
		}
		else {
			$content .= 'No filter set';
		}
			
		$content .= '
				</div>';
		
		if( !empty($xmltable) ) {
			// -- add new filter
			$data = $xmltable->getTable()->getSource();
			$sourcetype = $data->getType();
			$filterlist = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['filters'];
			$content .= '
					<div class="addnewfilter">' . 
						$GLOBALS['LANG']->getLL('commandprofile.addnewfilter') . '
						<select name="filtertype">';
			foreach($filterlist as $filtertype=>$filterinfos){
				if( isset($filterinfos['filtersources'][$sourcetype]) ) {
					$content .= '
							<option value=' . $filtertype . '>' . $filterinfos['filterid'] . '</option>';
				}
			}
			$content .= '
						</select>
						<a href="#" onclick="' . htmlspecialchars('
								document.getElementById("' . $this->_extkey . 'commandselected").value = "' . $commandselected . '";
								document.getElementById("' . $this->_extkey . 'filterselected").value = "newfilter";
								document.getElementById("' . $this->_extkey . 'gotofilter").value = "1";
								document.getElementById("opendataform").submit();') . '">
								' . t3lib_iconWorks::getSpriteIcon('actions-edit-add')  . '
						</a>
					</div>';
		}
		else {
			$content .= '
					Command table not set : you cannot add or edit filters';
		}
		$content .= '
				</fieldset>';
				
		$content .= '
				<input type="submit"
						name="updatecommand" 
						onclick="' . htmlspecialchars('document.getElementById("' . $this->_extkey . 'commandselected").value = "' . $commandselected . '";') . '" 
						value="Update">';
						
        return $content;
		
    } // End Render form
	
	/**
	* Update command attributs : Add/delete parameters, add/delete filters, edit filters
	*
	* @param $command : reference to the command
	* @param commandselected : id of the command
	*/
	private function updateCommand(&$command, $commandselected)
	{
		$post = t3lib_div::_POST();
		$basexml = $GLOBALS['repository']->get('basexml');

		// Add parameter
		if( !empty($post['addparam'])) {
			if( !empty($post['newparam']) ) {
				if( isset($post['newparamhasdefault']) ) {
					$command->addParam(htmlspecialchars($this->cleanStr($post['newparam'])), $post['newparamrequired'], true, htmlspecialchars($post['newparamdefaultvalue']));
				}
				else {
					$command->addParam(htmlspecialchars($this->cleanStr($post['newparam'])), $post['newparamrequired'], false, '');
				}
			}
		}
		
		// Delete parameter
		if( $post['deleteparam'] != null ) {
			$command->deleteParam( $post['deleteparam'] );
		}
		
		// Order parameter
		if( $post['orderparamup'] != null ) {
			$command->orderParam($post['orderparamup'], 'up');
		}
		if( $post['orderparamdown'] != null ) {
			$command->orderParam($post['orderparamdown'], 'down');
		}
		
		
		$commandsfields = $GLOBALS['repository']->get('commandsfields');
		
		// Delete filter
		if( $post['deletefilter'] != null ) {
			$command->deleteFilter( $post['deletefilter'] );
			unset( $commandsfields[$commandselected][$post['deletefilter']] );
			$commandsfields[$commandselected] = array_merge($commandsfields[$commandselected]);
		}
		
		// Update table
		if( !empty($basexml)) {
			if( isset($post['commandtable']) ) {
				$commandtable = $post['commandtable'];
				if( $sourcexml = $basexml->getXmlChild($commandtable['source']) ) 
					$tablexml = $sourcexml->getXmlChild($commandtable['table']);
				$command->setTableXml($tablexml);
			}
		}
		// Edit filter
		if( isset($post['updatefilter']) ) {
			$filter = $command->getFilter($post['editfilter']);
			// --- name
			$name = htmlspecialchars($this->cleanStr($post['newfiltername']));
			if( !empty($name) )
				$filter->setName($name);
			// --- parameters
			$params = $post['params'];
			foreach($params as $i=>$value) {
				$filter->setParam($i, $value);
			}
			// --- fields
			$fields = $post['fields'];
			foreach($fields as $i=>$field) {
				$tablexml = $command->getTableXml()->getXmlParent()->getXmlChild($field['table']);
				if( !empty($tablexml) ) {
					$fieldxml = $tablexml->getXmlChild($field['field']);
					if( !empty($fieldxml) )
						$filter->setLink($i, $fieldxml->getLink());
				}
			}
			$commandsfields[$commandselected][$post['editfilter']] = $fields;
			// --- activation
			if( isset($post['activate']) ) {
				$filter->setActivation($post['activationparam'], htmlspecialchars($this->cleanStr($post['activationvalue'])));
			}
			else {
				$filter->setActivation(null, null);
			}
		}
		$GLOBALS['repository']->set('commandsfields', $commandsfields);
	}
	
	private function cleanStr($in) {
		$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ ]@i','@[^a-zA-Z0-9_]@');
		$replace = array ('e','a','i','u','o','c','_','');
		return preg_replace($search, $replace, $in);
	}
	
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
			return 'commandprofile';
		}
		
		if( $post['gotofilter'] != null )
			return 'filter';
			
		return 'commandprofile';
    }


} /* end of class tx_icsopendata_FormCommandProfile */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_commandprofile.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_commandprofile.php']);
}

?>