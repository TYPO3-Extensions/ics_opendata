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
 * Data field : a field with a type and an associated table
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_DataField
{
    
	// === ATTRIBUTS ============================================================================== //

    private $_name = null;
    private $_type = null;
    private $_tablename = null;

    // === OPERATIONS ============================================================================= //

    /**
     * Constructor
     *
     * @param  String $Name
     * @return mixed
     */
    public function __construct($Name, $Type, $TableName)
    {
        $this->_name = $Name;
		$this->_type = $Type;
		$this->_tablename = $TableName;
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
     * Short description of method getType
     *
     * @return String
     */
    public function getType()
    {
        if(isset($this->_type))
			return $this->_type;
			
		return null;
    }
	
	/**
     * Short description of method getTableName
     *
     * @return String
     */
    public function getTableName()
    {
		if(isset($this->_tablename))
			return $this->_tablename;
			
		return null;
    }
	
	/**
     * Short description of method printField
     *
     * @return String
     */
    public function printField()
    {
		$content = '';
        if(isset($this->_name)){
			$content .= $this->_name . " (" . $this->_type . ") : ";
			if(isset($this->_table)){
				$content .= $this->_table->getName();
			}
			else{
				$content .= "NO TABLE";
			}
		}
		return $content;;
    }

} /* end of class tx_icsopendata_DataField */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_field.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_field.php']);
}

?>