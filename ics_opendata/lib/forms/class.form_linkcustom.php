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
 * Form : choose inputs and outputs type for each selected fields
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormLinkCustom
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
		$errors = array();
		$post = t3lib_div::_POST();
		
		$fieldsselected = $post['fieldsselected'];
		foreach( $fieldsselected as $fieldname=>$fieldinfo ) {
			if( empty($fieldinfo['newname']) )
				$errors[] = $fieldname . ' xml name cannot be empty.';
		}
		$GLOBALS['repository']->set('errors', $errors);
		
		if( !empty($errors) )
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
        $content = $this->jsUpdateList();
		
		// Get data
		$post = t3lib_div::_POST();
		$sourceselected = $post['sourceselected'];
		$tableselected = $post['tableselected'];
		$fieldsselected = $post['fieldsselected'];
		
		$sourceinfos = $GLOBALS['repository']->getSourceInfos($sourceselected);
		$dataselected = $sourceinfo['selecteditems'];

		$tableinfos = $sourceinfos['selecteditems'][$tableselected];
		
		// Main content header
		$content .= '
					<h2>' . $GLOBALS['LANG']->getLL('linkcustom.title') . '</h2>
					<input type="hidden" name="action" value="link"/>
					<input type="hidden" id="' . $this->_extkey . 'sourceselected" name="sourceselected" value="' . $sourceselected . '">
					<input type="hidden" id="' . $this->_extkey . 'tableselected" name="tableselected" value="' . $tableselected . '">
					<input type="hidden" name="fieldsselected" value=' . serialize($post['fieldsselected']) . '/>';
		
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
		
		// Form content
		$content .= '
					<h3>' . $GLOBALS['LANG']->getLL('linkcustom.help') . '</h3>';
		
		// Type specific parameters
		if( empty($tableinfos)) {
			$prefix = '';
			$suffix = '';
		}
		else {
			$prefix = $tableinfos['prefix'];
			$suffix = $tableinfos['suffix'];
		}
		
		// --- prefix and suffix
		$content .= '
					<fieldset>
						<legend>' . $GLOBALS['LANG']->getLL('linkcustom.specificparams') . '</legend>
						
						<label for="' . $this->_extkey . 'prefix">' . $GLOBALS['LANG']->getLL('linkcustom.prefix') . ' : </label>
						<input id="' . $this->_extkey . 'prefix" type="text" name="prefix" value="' . $prefix . '">
						
						<label for="' . $this->_extkey . 'suffix">' . $GLOBALS['LANG']->getLL('linkcustom.suffix') . ' : </label>
						<input id="' . $this->_extkey . 'suffix" type="text" name="suffix" value="' . $suffix . '">
					</fieldset>';
		
		// Table
		$content .= '
					<fieldset>
						<legend>' . $GLOBALS['LANG']->getLL('tableprofile.table') . ' : ' . $tableselected . '</legend>';
			
		// --- table xml name
		$defaultvalue = $tableselected;
		if( !empty($tableinfos) )
			$defaultvalue = $tableinfos['tablexmlname'];
		$content .= '
						<label for="' . $this->_extkey . $tableselected . '">' . $GLOBALS['LANG']->getLL('linkcustom.tablexmlname') . ' : </label>
						<input id="' . $this->_extkey . $tableselected . '" type="text" name="tablexmlname" value="' . $defaultvalue . '">';
		
		$content .= '
						<table>
							<thead>
								<tr>
									<th>' . $GLOBALS['LANG']->getLL('linkcustom.fieldname') . '</th>
									<th>' . $GLOBALS['LANG']->getLL('linkcustom.inputtype') . '</th>
									<th>' . $GLOBALS['LANG']->getLL('linkcustom.outputtype') . '</th>
									<th>' . $GLOBALS['LANG']->getLL('linkcustom.newname') . '</th>
								</tr>
							</thead>
							<tbody>';
					
		$fields = $tableinfos['fields'];
		$sourcedata = $GLOBALS['repository']->getSourceData($sourceselected);
		$table = $sourcedata->getTableByName($tableselected);
		
		$inputtypelist = array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['types']);
		foreach($fieldsselected as $fieldname=>$info){
			$field = $table->getFieldByName($fieldname);
			if( empty($field) )
				continue;
				
			$fieldtype = $field->getType();
			$content .= '
								<tr>
									<td>' . $fieldname . ' (' . $fieldtype . ')</td>';
			// --- Select input type
			$content .= '
									<td>
										<select name="fieldsselected[' . $fieldname . '][inputtype]" onchange="' . 
												htmlspecialchars('
													updateTypeList(this,"' . $this->_extkey . $fieldname . '_selectoutput");
												') . '"
										>';
			foreach($inputtypelist as $type){
				$content .= '
											<option value="' . $type . '"';
				if( !isset($fields[$fieldname]) ) {
					$typealias = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['typesalias'][$type];
					foreach( $typealias as $alias ) {
						if( stristr($fieldtype, $alias ) ) {
							$selectedinput = $type;
							$content .= 'selected="selected"';
							break;
						}
					}
				}
				else {
					if( empty($select) && ($fields[$fieldname]['inputtype'] == $type ) )
						$content .= 'selected="selected"';
				}
				$content .= '>' . $type . '</option>';
			}
			$content .= '
										</select>
									</td>';
			// --- Select output type
			$outputtypelist = array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['types'][$selectedinput]);
			if( empty($outputtypelist) ) {
				reset($inputtypelist);
				$outputtypelist = array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['types'][current($inputtypelist)]);
			}
			$content .= '
									<td>
										<select id="' . $this->_extkey . $fieldname . '_selectoutput" name="fieldsselected[' . $fieldname . '][outputtype]">';
			foreach($outputtypelist as $type){
				$content .= '
											<option value="' . $type . '"';
				if( $fields[$fieldname]['outputtype'] == $type )
					$content .= 'selected="selected"';
				$content .= '>' . $type . '</option>';
			}
			$content .= '
										</select>
									</td>';
			// --- New name
			$defaultvalue = $fieldname;
			if( isset($fields[$fieldname]['newname']) )
				$defaultvalue = $fields[$fieldname]['newname'];
			$content .= '
									<td>
										<input id="' . $this->_extkey . $fieldname . '" type="text" name="fieldsselected[' . $fieldname . '][newname]" value="' . $defaultvalue . '">
									</td>
								</tr>';
		}
		$content .= '
							</tbody>
						</table>
					</fieldset>';
		
		$content .= '
					<input type="submit" name="cancel" value="' . $GLOBALS['LANG']->getLL('cancel') . '" onclick="' . htmlspecialchars('
										document.getElementById("' . $this->_extkey . 'menuCmd").value = "tableprofile"; 
										document.getElementById("' . $this->_extkey . 'menusourceselected").value = "' . $sourceselected . '";
										document.getElementById("' . $this->_extkey . 'menutableselected").value = "' . $tableselected . '";
										document.getElementById("opendatamenu").submit();') . '">
					<input type="submit" value="' .$GLOBALS['LANG']->getLL('linkcustom.generatelink') . '"/>';

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
		
		if(!empty($errors))
			return 'linkcustom';

        return 'tableprofile';
    }

	/**
	* link fields selected to new xml elements
	*
	* 
	*/
	public function link() {
		// Get data
		$post = t3lib_div::_POST();
		
		if( isset($post['cancel']) )
			return '';
			
		$sourceselected = $post['sourceselected'];
		$tableselected = $post['tableselected'];
		$fieldsselected = $post['fieldsselected'];
		$prefix = $this->cleanStr($post['prefix']);
		$suffix = $this->cleanStr($post['suffix']);
		$tablexmlname = $this->cleanStr($post['tablexmlname']);
		
		$data = $GLOBALS['repository']->getSourceData($sourceselected);
		$basexml = $GLOBALS['repository']->get('basexml');

		if( empty($basexml)) {
			$basexml = t3lib_div::makeInstance('tx_icsopendata_DataElementXML');
			$basexml->initXML('ics_opendata_BaseXml');
		}
		
		// --- create xml element for the source
		$sourcexml = $basexml->getXmlChild($data->getSourceId());
		if ( empty($sourcexml) ) {
			$sourcexml = t3lib_div::makeInstance('tx_icsopendata_DataElementXML');
			$sourcexml->initXML($data->getName());
			$sourcexml->setXmlParent($basexml);
			$basexml->setXmlChild($data->getSourceId(),null);
			$basexml->setXmlChild($data->getSourceId(),$sourcexml);
		}

		$table = $data->getTableByName($tableselected);
		
		// --- create xml element for the table or update if the table already exist
		$tablexml = $sourcexml->getXmlChild($tableselected);
		if( empty($tablexml) ) {
			$tablexml = t3lib_div::makeInstance('tx_icsopendata_DataElementXML');
			$tablexml->setXmlParent($sourcexml);
			$tablexml->setTable($table);
			$sourcexml->setXmlChild($tableselected, null);
			$sourcexml->setXmlChild($tableselected, $tablexml);
		}
		$tablexml->initXML($prefix . $tablexmlname . $suffix);

		
		// --- browse fields which are already selected in the table
		$sourcefieldsselected = array();
		$fieldsxml = $tablexml->getAllChildren();
		if( !empty($fieldsxml) ) {
			foreach($fieldsxml as $fieldname=>$fieldxml) {
				if( isset($fieldsselected[$fieldname]) ) {
					// --- update field if selected
					$fieldsselected[$fieldname]['newname'] = $this->cleanStr($fieldsselected[$fieldname]['newname']);
					$fieldxml->initXML($prefix . $fieldsselected[$fieldname]['newname'] . $suffix);
					$link = $fieldxml->getLink();
					if( !empty($link) ) {
						$field = $table->getFieldByName($fieldname);
						$link->initLink($field, $fieldsselected[$fieldname]['inputtype'], $fieldsselected[$fieldname]['outputtype']);
						$link->setName($prefix . $fieldname . $suffix);
					}
					$sourcefieldsselected[$fieldname] = $fieldsselected[$fieldname];
					unset($fieldsselected[$fieldname]);
				}
				else {
					// --- delete field if not selected
					$tablexml->setXmlChild($fieldname, null);
				}
			}
		}
		// --- add new selected fields
		foreach($fieldsselected as $fieldname=>$infos) {
			// --- create xml element for each fields
			$fieldxml = t3lib_div::makeInstance('tx_icsopendata_DataElementXML');
			$fieldsselected[$fieldname]['newname'] = $this->cleanStr($fieldsselected[$fieldname]['newname']);
			$fieldxml->initXML($prefix . $fieldsselected[$fieldname]['newname'] . $suffix);
			$fieldxml->setXmlParent($tablexml);
			$tablexml->setXmlChild($fieldname, $fieldxml);
			
			// --- link xml element
			$field = $table->getFieldByName($fieldname);
			$link = t3lib_div::makeInstance('tx_icsopendata_DataLink');
			$link->initLink($field, $infos['inputtype'], $infos['outputtype']);
			$link->setName($prefix . $fieldsselected[$fieldname]['newname'] . $suffix);

			$fieldxml->setLink($link);
			
			$sourcefieldsselected[$fieldname] = $fieldsselected[$fieldname];
		}
		
		// Add new selected table 
		$sourceinfos = $GLOBALS['repository']->getSourceInfos($sourceselected);
		$sourceinfos['selecteditems'][$tableselected]['suffix'] = $suffix;
		$sourceinfos['selecteditems'][$tableselected]['prefix'] = $prefix;
		$sourceinfos['selecteditems'][$tableselected]['tablexmlname'] = $tablexmlname;
		$sourceinfos['selecteditems'][$tableselected]['fields'] = $sourcefieldsselected;
		
		// Save data
		$GLOBALS['repository']->setSourceInfos($sourceselected,$sourceinfos);
		$GLOBALS['repository']->set('basexml',$basexml);
		
	} // End link()
	
	/**
	* javascript used to change type list dynamically
	*
	* @return String
	*/
	private function jsUpdateList() {
	
		$typelist = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['types'];
		$typelistcontent = '
	var typelist = new Array();';
		foreach( $typelist as $typename=>$outputtypes ){
			$typelistcontent .= '
	typelist[\'' . $typename . '\'] = new Array(';
			$i = 0;
			foreach( $outputtypes as $type=>$file ) {
				if( $i < (sizeof($outputtypes) - 1 ) ) {
					$typelistcontent .= '
			\'' . $type . '\',';
				}
				else {
					$typelistcontent .= '
			\'' . $type . '\'';
				}
				$i++;
			}
			$typelistcontent .= '
		);';
		}
		
		$content = '
<script type="text/javascript" language="javascript">
function updateTypeList(inputselect, outputselectid) {
' . $typelistcontent . '
';
		$content .= <<<EOJS
	var type = inputselect.value;
	var outputtypelist = typelist[type];
	var outputselect = document.getElementById(outputselectid);
	outputselect.options.length = 0;
	for(i=0 ; i<outputtypelist.length ; i++) {
		outputselect.options[outputselect.options.length] = new Option(outputtypelist[i], outputtypelist[i]);
	}
}
</script>
EOJS;
		return $content;
	}
	
	private function cleanStr($in) {
		$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ ]@i','@[^a-zA-Z0-9_]@');
		$replace = array ('e','a','i','u','o','c','_','');
		return Preg_replace($search, $replace, htmlspecialchars($in));
	}

} /* end of class tx_icsopendata_FormLinkCustom */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_linkcustom.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_linkcustom.php']);
}

?>