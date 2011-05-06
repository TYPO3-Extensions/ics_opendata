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
 * Data filter : 
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_DataFilter
{

	// === ATTRIBUTS ============================================================================== //
	
	private $_extkey = 'ics_opendata';
	private $_type = null;
	private $_linkslabels = array();
	private $_paramslabels = array();
	
	private $_command = null;
	
	private $_name = null;
	private $_links = Array();
	private $_params = Array();
	private $_activationparam = null;
	private $_activationvalue = '';
	
	// === OPERATIONS ============================================================================= //
	
	public function initFilter($filtertype, & $command)
	{
		$this->_type = $filtertype;
		$this->_linkslabels = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['filters'][$filtertype]['fieldslabel'];
		$this->_paramslabels = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['filters'][$filtertype]['paramslabel'];
		$this->_command = $command;
	}
	
	/**
	* Return the name of the filter
	*
	* return String
	*/
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	* Return the type of the filter
	*
	* return String
	*/
	public function getFilterType()
	{
		return $this->_type;
	}
	
	/**
	* Return the operator of the filter
	*
	* return String
	*/
	public function getOperator(){
		return $this->_operator;
	}
	
	/**
	* retrieve activation param
	*
	* @return : String
	*/
	public function getActivationParam()
	{
		return $this->_activationparam;
	}
	
	/**
	* retrieve activation value
	*
	* @return mixed
	*/
	public function getActivationValue()
	{
		return $this->_activationvalue;
	}

	/**
	* Return the number of links to set
	*
	* return Int : number of links to set
	*/
	public function getLinkCount() 
	{
		return sizeof($this->_linkslabels);
	}
	
	/**
	* return the $i link
	*
	* @param $i : Int
	* @return DataLink
	*/
	public function getLink($i)
	{
		if( isset($this->_links[$i]) ) 
			return $this->_links[$i];
		return null;
	}
	/**
	* return the label associate to the link
	*
	* @param $i : Int
	* @return String
	*/
	public function getLinkLabel($i)
	{
		if( isset($this->_linkslabels[$i]) ) 
			return $this->_linkslabels[$i];
		return null;
	}
	
	/**
	* Return the number of param to set
	*
	* @return Int : number of params to set
	*/
	public function getParamCount()
	{
		return sizeof($this->_paramslabels);
	}
	
	/**
	* get the param $i
	*
	* @param $i : Int
	* @return String
	*/
	public function getParam($i)
	{
		if( isset($this->_params[$i]) ) 
			return $this->_params[$i];
		return null;
	}
	
	/**
	* get the label associate to the param $i
	*
	* @param $i : Int
	* @return String
	*/
	public function getParamLabel($i)
	{
		if( isset($this->_paramslabels[$i]) ) 
			return $this->_paramslabels[$i];
		return null;
	}

	/**
	* set the Name of the filter
	*
	* @param $Name : String
	* @return -
	*/
	public function setName($Name) {
		if( isset($Name) ) {
			$this->_name = $Name;
		}
	}
	
	/**
	* set the $i link
	*
	* @param $i : Int
	* @param $link : DataLink
	* @return -
	*/
	public function setLink($i, $link)
	{
		if( isset($this->_linkslabels[$i]) ) 
			$this->_links[$i] = $link;
	}
	
	/**
	* set the Operator of the filter
	*
	* @param $Operator : String (boolean operator like '=', '<' ...)
	* @return -
	*/
	public function setOperator($Operator)
	{
		$this->_operator = $Operator;
	}
	
	/**
	* set the $i param
	*
	* @param $i : Int
	* @param $param : String
	* @return -
	*/
	public function setParam($i, $param)
	{
		if( isset($this->_paramslabels[$i]) ) 
			$this->_params[$i] = $param;
	}
	
	/**
	* Set activation parameter
	*
	* @param : Param : String
	* @param : Value : mixed
	*/
	public function setActivation($Param, $Value) {
		$this->_activationparam = $Param;
		$this->_activationvalue = $Value;
	}	
	
	/**
	* Return true if the filter can be generate
	*
	* @return boolean
	*/
	public function isActive()
	{
		for( $i=0 ; $i<$this->getParamCount() ; $i++ ) {
			if( $this->getParam($i) == null )
				return false;

		}
		for( $i=0 ; $i<$this->getLinkCount() ; $i++ ) {
			$link = $this->getLink($i);
			if( empty($link) )
				return false;
			if( $link->getField() == null )
				return false;
		}
		
		return true;
	}
	
	/**
	* Generate filter code, depending on filter type and source type
	*
	* @param String : $sourcetype
	* @return array
	*/
	public function generateFilterCode($sourcetype, $nbindent)
	{
		$filtersources = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['filters'][$this->_type]['filtersources'];

		if( !isset($filtersources[$sourcetype]) )
			return array();
			
		$class = $filtersources[$sourcetype];
		$generator = t3lib_div::getUserObj($class);
		$content = $generator->generateFilter($this, $nbindent);
		return $content;
	}
	
} /* end of class Abstract tx_icsopendata_DataFilter */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_filter.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_filter.php']);
}

?>