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
 * Data elementxml : represent selected items. Xml element can be linked to a fields with a DataLink
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_DataElementXML
{
    
	// === ATTRIBUTS ============================================================================== //

    private $_name = null;
	private $_link = null;
	private $_xmlparent = null;
	private $_xmlchildren = array();

	private $_table = null;
	
    // === OPERATIONS ============================================================================= //

    /**
     * Constructor
     *
     * @param  String $Name
     * @return mixed
     */
    public function initXML($Name)
    {
        $this->_name = $Name;
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
     * Short description of method getLink
     *
     * @return DataLink
     */
    public function getLink()
    {
        if(isset($this->_link))
			return $this->_link;
			
		return null;
    }
	
	/**
     * Short description of method getTable
     *
     * @return DataTable
     */
    public function getTable()
    {
        if(isset($this->_table))
			return $this->_table;
			
		return null;
    }
	
	/**
     * Short description of method getXmlParent
     *
     * @return DataElementXml
     */
    public function getXmlParent()
    {
        if(isset($this->_xmlparent))
			return $this->_xmlparent;
			
		return null;
    }
	
	/**
     * Short description of method getXmlChild
     *
	 * @param String $Key
     * @return DataElementXml
     */
    public function &getXmlChild($Key)
    {
        if(isset($this->_xmlchildren[$Key]))
			return $this->_xmlchildren[$Key];
			
		return null;
    }
	
	/**
     * Short description of method getAllChilds
     *
     * @return Array DataElementXml
     */
    public function getAllChildren()
    {
		return $this->_xmlchildren;
    }

    /**
     * Short description of method setName
     *
     * @param  String $Name
     * @return mixed
     */
    public function setName($Name)
    {
        $this->_name = $Name;
    }

    /**
     * Short description of method setLink
     *
     * @param  DataLink $Link
     * @return mixed
     */
    public function setLink($Link)
    {
        $this->_link = $Link;
    }
	
	/**
     * Short description of method setTable
     *
     * @param  DataTable : $Table
     * @return mixed
     */
    public function setTable($Table)
    {
        $this->_table = $Table;
    }
	
	/**
     * Short description of method setXmlParent
     *
     * @param  DataElementXml $Parent
     * @return mixed
     */
    public function setXmlParent($Parent)
    {
        $this->_xmlparent = $Parent;
    }
	
	/**
     * add a child to the xml element. if $Child is null, unset $key element
     *
	 * @param  String $Key
     * @param  DataElementXml $Child
     * @return mixed
     */
    public function setXmlChild($Key,$Child)
    {
		if($Child == null){
			if( isset($this->_xmlchildren[$Key]) )
				$this->_xmlchildren[$Key]->setXmlParent(null);
			unset($this->_xmlchildren[$Key]);
		}
		else{
			$this->_xmlchildren[$Key] = $Child;
		}
    }
	
	/**
	* Short description of method print
	*
	* @param String : tabulation
	* @return String
	*/
	public function printElement($Prefix){
		if(!isset($Prefix))
			$Prefix = '';
			
		$content = '';
		$link = $this->getLink();
		if($link != null){
			$field = $link->getField();
			$content .= '<br />' . $Prefix . ' ' .  $this->getName() . $link->printLink();
		}
		else{
			$content .= '<br />' . $Prefix . ' ' .  $this->getName() . " -> NO LINK SET";
		}
		
		$prefix = $Prefix . "---";
		foreach($this->_xmlchildren as $child){
				$content .= $child->printElement($prefix);
		}
		
		return $content;
	}
	
	public function __destruct()
	{
		if( isset($this->_link) ) {
			$this->_link->setField(null);
			$this->_link = null;
			unset($this->_link);
		}
		foreach($this->xmlchildren as $key=>$child) {
			$child = null;
			unset($child);
		}
		unset($this->_xmlchildren);
	}
	
	
} /* end of class tx_icsopendata_DataElementXML */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_elementxml.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/data/class.data_elementxml.php']);
}

?>