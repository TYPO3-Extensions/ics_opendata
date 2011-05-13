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
 * Data table : a table containing fields
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_DataTable
{

    // === ATTRIBUTS ============================================================================== //

    private $_name = null;
	private $_fields = array();
	private $_source = null;

     // === OPERATIONS ============================================================================= //

    /**
     * Constructor
     *
     * @param  String $Name
     * @param  Field [] $Fields
     * @return mixed
     */
    public function __construct($Name, $Fields, $Source)
    {
        $this->_name = $Name;
		$this->_fields = $Fields;
		$this->_source = $Source;
    }

    /**
     * Short description of method getName
     *
     * @return String
     */
    public function getName()
    {
		if(isset($this->_name))
			return $this->_name;
			
		return null;
    }

    /**
     * Short description of method getField
     *
     * @param  Integer $i
     * @return DataField
     */
    public function getField($i)
    {
		if(isset($this->_fields[$i]))
			return $this->_fields[$i];
			
		return null;
    }
	
	public function getFieldByName($FieldName)
	{
		foreach($this->_fields as $field)
			if($field->getName() == $FieldName)
				return $field;
		
		return null;
	}
	
	/**
     * Short description of method getSource
     *
     * @return DataSource
     */
    public function getSource()
    {
		if(isset($this->_source))
			return $this->_source;
			
		return null;
    }
	
	/**
     * Short description of method countFields
     *
     * @return Integer
     */
    public function countFields()
    {
		if(isset($this->_fields))
			return sizeof($this->_fields);
			
		return 0;
    }
	
	/**
     * Short description of method printTable
     *
     * @return String
     */
    public function printTable()
    {
		$content = '';
		if(isset($this->_fields)){
			$content .= $this->_name;
			foreach($this->_fields as $field){
				$content .= "<br />|__ " . $field->printField();
			}
		}	
		return $content;
    }

} /* end of class tx_icsopendata_DataTable */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_table.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_table.php']);
}

?>