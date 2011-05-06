<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 In-Cite Solution <technique@in-cite.net>
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
 * Form : list of fields for the selected table
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_FormTableProfile
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
		
		if(!isset($post['fieldsselected']))
			$errors[] = 'ERROR_NO_FIELDS_SELECTED';
			
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
		$content = $this->jsCheckAll();
		// Get data
		$post = t3lib_div::_POST();
		$sourceselected = $post['sourceselected'];
		$tableselected = $post['tableselected'];

		$data = $GLOBALS['repository']->getSourceData($sourceselected);
		$sourceinfos = $GLOBALS['repository']->getSourceInfos($sourceselected);
		$tableinfos = $sourceinfos['selecteditems'][$tableselected];
		$basexml = $GLOBALS['repository']->get('basexml');
		
		
		// Main content header
		$content .= '
					<h2>' . $GLOBALS['LANG']->getLL('tableprofile.title') . '</h2>
					<input type="hidden" name="action" value="next"/>
					<input type="hidden" id="' . $this->_extkey . 'sourceselected" name="sourceselected" value="' . $sourceselected . '">
					<input type="hidden" id="' . $this->_extkey . 'tableselected" name="tableselected" value="' . $tableselected . '">';
		
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
		
		$content .= '<h3>' . $GLOBALS['LANG']->getLL('tableprofile.help') . '</h3>';
		
		// Other informations
		if(isset($sourceinfos['selecteditems'][$tableselected]))
			$content .= '
					<fieldset>
						<legend>' . $GLOBALS['LANG']->getLL('tableprofile.other') . '</legend>
						<div class="informations">' . 
							$GLOBALS['LANG']->getLL('linkcustom.prefix') . ' : ' . $tableinfos['prefix'] . '<br />' . 
							$GLOBALS['LANG']->getLL('linkcustom.suffix') . ' : ' . $tableinfos['suffix'] . '<br />
						</div>
					</fieldset>';
		// Display fields
		$content .= '
					<fieldset>
						<legend>' . $GLOBALS['LANG']->getLL('tableprofile.table') . ' : ' . $tableselected . '</legend>';
		// --- select all fields
		$content .= '
						<table>
							<thead>
								<tr>
									<th><input id="' . $this->_extkey . 'all" type="checkbox" onclick="checkAll(this)" /></th>
									<th>' . $GLOBALS['LANG']->getLL('linkcustom.fieldname') . '</th>
									<th>' . $GLOBALS['LANG']->getLL('linkcustom.inputtype') . '</th>
									<th>' . $GLOBALS['LANG']->getLL('linkcustom.outputtype') . '</th>
									<th>' . $GLOBALS['LANG']->getLL('linkcustom.newname') . '</th>
								</tr>
							</thead>
							<tbody>';

		// --- field 
		$table = $data->getTableByName($tableselected);
		for($i = 0; $i<$table->countFields();$i++) {
			$fields = $tableinfos['fields'];
			$content .= '
								<tr>';
			$field = $table->getField($i);
			$fieldname = $field->getName();
			$content .= '
									<td>
										<input id="' . $this->_extkey . $fieldname . '" type="checkbox" name="fieldsselected[' . $fieldname . ']"'; 
			if(isset($fields[$fieldname]))
				$content .='checked="checked"';
			$content .= '/>
									</td>
									<td>
										<label for="' . $this->_extkey . $fieldname . '" >' . $fieldname . '</label>
									</td>';
			if( isset($fields[$fieldname]) )
				$content .= '
									<td>' . $fields[$fieldname]['inputtype'] . '</td>
									<td>' . $fields[$fieldname]['outputtype'] . '</td>
									<td>' . $fields[$fieldname]['newname'] . '</td>';
			$content .= '
								</tr>';
		}
		$content .= '
							</tbody>
						</table>
					</fieldset>';
		

		$content .= '<input type="submit" name="editlink" value="' . $GLOBALS['LANG']->getLL('tableprofile.addedit') . '"/>';
		
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
			return 'field';
		}

		if(isset($post['deletetable']))
			return 'table';
			
		return 'linkcustom';
    }

	/**
	* javascript used to check all boxes of a table
	*
	* @return String
	*/
	private function jsCheckAll() {
		$content = <<<EOJS
<script type="text/javascript" language="javascript">
function checkAll(element) {
	var nodes = document.getElementsByTagName('input');
	for (i = 0; i < nodes.length; i++) {
		var input = nodes.item(i);
		if (input.type != 'checkbox')
				continue;
		input.checked = element.checked;
	}
}
</script>
EOJS;
		return $content;
	}

} /* end of class tx_icsopendata_FormTableProfile */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_tableprofile.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/forms/class.form_tableprofile.php']);
}

?>