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
 * Data link : link an element xml to a fields. Contain input and output type
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_DataLink
{

    // === ATTRIBUTS ============================================================================== //

    private $_inputtype = null;
    private $_outputtype = null;
	private $_field = null;
	private $_name = null;

    // === OPERATIONS ============================================================================= //

    /**
     * Short description of method DataLink
     *
     * @param  DataField $Field
	 * @param  String $Type : type de transformation
     * @return mixed
     */
    public function initLink($Field, $InputType, $OutputType)
    {
        $this->_field = $Field;
		$this->_inputtype = $InputType;
		$this->_outputtype = $OutputType;
    }

    /**
     * Short description of method getField
     *
     * @return DataField
     */
    public function getField()
    {
        if(isset($this->_field))
			return $this->_field;
			
		return null;
    }

    /**
     * Short description of method getType
     *
     * @return String
     */
    public function getInputType()
    {
        if(isset($this->_inputtype))
			return $this->_inputtype;
			
		return null;
    }
	
	/**
     * Short description of method getType
     *
     * @return String
     */
    public function getOutputType()
    {
        if(isset($this->_outputtype))
			return $this->_outputtype;
			
		return null;
    }

    /**
     * Short description of method setType
     *
     * @param  String $Type
     * @return mixed
     */
    public function setInputType($Type)
    {
        $this->_inputtype = $Type;
    }
	
	/**
     * Short description of method setField
     *
     * @param  DataField $Field
     * @return mixed
     */
    public function setField($Field)
    {
        $this->_field = $Field;
    }
	
	/**
     * Short description of method setName
     *
     * @param  String : New name
     * @return mixed
     */
    public function setName($Name)
    {
        $this->_name = $Name;
    }
	 
	 /**
	 * return a string representing the link
	 *
	 * @return String
	 */
	 public function printLink() 
	 {
		$content = '-> ( ' . $this->_inputtype . ' ) LINKED TO ( ' . $this->_outputtype . ' ): ' . $this->_field->getName();
		return $content;
	 }
	 
} /* end of class tx_icsopendata_DataLink */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_link.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_link.php']);
}

?>