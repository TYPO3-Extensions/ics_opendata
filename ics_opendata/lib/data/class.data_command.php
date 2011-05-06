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
 * Data : command
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_DataCommand
{

    // === ATTRIBUTS ============================================================================== //
	
	private $_id = null;
	private $_name = null;
    private $_params = Array();
	private $_defaultvalue = Array();
	private $_required = Array();
	private $_filters = Array();
	private $_tablexml = null;

    // === OPERATIONS ============================================================================= //

    /**
     * Constructor
     *
     * @param  String $Name
     * @return mixed
     */
    public function initCommand($Name, $Id)
    {
        $this->_name = $Name;
        $this->_id = $Id;
    }

    /**
     * Retrieve command name
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
     * Retrieve command param
     *
     * @return String
     */
    public function getParam($i)
    {
        if(isset($this->_params[$i]))
			return $this->_params[$i];
		return null;
    }
	
	/**
     * Return default value of the $i param
     *
     * @return mixed
     */
    public function getDefaultValue($i)
    {
		$Param = $this->getParam($i);
        if(isset($this->_defaultvalue[$Param]))
			return $this->_defaultvalue[$Param];
		return null;
    }
	
	/**
	* Return boolean : if true, param $i is required
	*
	* @return boolean
	*/
	public function isParamRequired($i)
	{
		$Param = $this->getParam($i);
		if( $Param ) {
			if( isset($this->_required[$Param]) )
				return true;
		}
		return false;
	}
	
	/**
	* Return the number of params
	*
	* @return Int
	*/
	public function countParams()
	{
		return sizeof($this->_params);
	}
	
	/**
     * Retrieve command links
     *
     * @return tx_icsopendata_DataLink
     */
    public function getFilter($i)
    {
        if(isset($this->_filters[$i]))
			return $this->_filters[$i];
		return null;
    }
		
	/**
	* Return the number of filters
	*
	* @return Int
	*/
	public function countFilters()
	{
		return sizeof($this->_filters);
    }
	
	/**
     * Retrieve the xml table associated to the commande
     *
     * @return tx_icsopendata_elementxml
     */
    public function getTableXml()
    {
        if( isset($this->_tablexml) )
			return $this->_tablexml;
		return null;
    }	
	
	/**
	* set a new name for the command
	*
	* return -
	*/
	public function setName($Name)
	{
		if( !empty($Name) )
			$this->_name = $Name;
	}
	
	/**
	* set the Xml table associated to the command
	*
	* return -
	*/
	public function setTableXml($TableXml)
	{
		$this->_tablexml = $TableXml;
	}
	
	/**
	* add a filter for the command
	*
	* return -
	*/
	public function addFilter($Filter)
	{
		array_push($this->_filters, $Filter);
	}
	
	/**
	* delete a filter of the command
	*
	* return -
	*/
	public function deleteFilter($i)
	{
		unset( $this->_filters[$i] );
		$this->_filters = array_merge( $this->_filters );
	}
	
	/**
	* add a new parameter to the command
	*
	* return -
	*/
	public function addParam($Param, $Required, $HasDefaultValue, $DefaultValue)
	{
		array_push($this->_params, $Param);
		if( $HasDefaultValue ){
			$this->_defaultvalue[$Param] = $DefaultValue;
		}
		if( $Required ){
			$this->_required[$Param] = !empty($Required);
		}
	}
	
	/**
	* delete a parameter of the command
	*
	* return -
	*/
	public function deleteParam($i)
	{
		$Param = $this->getParam($i);
		unset( $this->_params[$i] );
		unset( $this->_required[$Param] );
		unset( $this->_defaultvalue[$Param] );
		$this->_params = array_merge( $this->_params );
	}
	
	/**
	* Order parameters of the command
	*
	* @param $order : String 'up' or 'down'
	* @param $i : num of the param
	*/
	public function orderParam($i, $order)
	{
		switch($order) {
			case 'up' : 
				if($i > 0) {
					$temp = $this->_params[$i - 1];
					$this->_params[$i - 1] = $this->_params[$i];
					$this->_params[$i] = $temp;
				}
				break;
			case 'down' : 
				var_dump($this->_params);
				if( isset($this->_params[$i + 1]) ) {
					$temp = $this->_params[$i + 1];
					$this->_params[$i + 1] = $this->_params[$i];
					$this->_params[$i] = $temp;
				}
				break;
			default : 
				break;
		}
	}
	
	/**
	* Return boolean : true if the command can be generate
	*
	* @return boolean
	*/
	public function isActive()
	{
		$warnings = $this->getWarning();
		if( in_array("WARNING_COMMAND_TABLE_NOT_SET", $warnings) )
			return false;
			
		return true;
	}
	
	/**
	* Retrieve warning information about the command
	*
	* return String[]
	*/
	public function getWarning()
	{
		$warning = Array();
		// --- command name
		if( empty($this->_name) )
			$warning[] = "WARNING_EMPTY_NAME";
		if ( str_replace(' ', '', $this->_name) != $this->_name )
			$warning[] = "WARNING_SPACE_IN_COMMAND_NAME";
		// --- command table
		if ( empty($this->_tablexml) ) {
			$warning[] = "WARNING_COMMAND_TABLE_NOT_SET";
		}
		else {
			$sourcexml = $this->_tablexml->getXmlParent();
			if( empty($sourcexml) )
				$warning[] = "WARNING_COMMAND_TABLE_NOT_SET";
		}
		// --- parameters
		// --- filters
		foreach($this->_filters as $i=>$filter) {
			for($j=0 ; $j<$filter->getParamCount() ; $j++) {
				$param = $filter->getParam($j);
				if( empty($param) ) {
					$warning[] = "WARNING_" . $filter->getName() . "_PARAMETER" . $j . "_NOT_SET";
					break;
				}
			}
			for($j=0 ; $j<$filter->getLinkCount() ; $j++) {
				$link = $filter->getLink($j);
				if( empty($link) ) {
					$warning[] = "WARNING_" . $filter->getName() . "_LINK" . $j . "_NOT_SET";
					break;
				}
				else{
					$commandsfields = $GLOBALS['repository']->get('commandsfields');
					$basexml = $GLOBALS['repository']->get('basexml');
					$filterinputs = $commandsfields[$this->_id][$i];
					foreach( $filterinputs as $input ) {
						$sourcexml = $basexml->getXmlChild($input['source']);
						if( empty($sourcexml) ) {
							$warning[] = "WARNING_" . $filter->getName() . "_SOURCES_HAD_CHANGED";
							break;
						}
						$tablexml = $sourcexml->getXmlChild($input['table']);
						if( empty($tablexml) ) {
							$warning[] = "WARNING_" . $filter->getName() . "_TABLES_HAD_CHANGED";
							break;
						}
						$fieldxml = $tablexml->getXmlChild($input['field']);
						if( empty($fieldxml) ) {
							$warning[] = "WARNING_" . $filter->getName() . "_FIELDS_HAD_CHANGED";
							break;
						}
					}
				}
			}
		}
		return $warning;
	}
	
} /* end of class tx_icsopendata_DataCommand */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_command.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_command.php']);
}

?>