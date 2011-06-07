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
 * Repository : used to store and manage session data
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_Repository
{
	
	// === ATTRIBUTS ============================================================================== //
	
	private $_extkey = 'ics_opendata';
	
	// --- Extension Data
	private $_data = Array();
	private $_sources = Array();
	
	private $_sourcelist = Array();
	private $_sourceclass = Array();
	
	// === OPERATIONS ============================================================================= //
	
	/**
	* set the value associate to the key
	* 
	* @param Key : String
	* @param Value : mixed
	*/
	public function set($Key, $Value)
	{
		if( $Value == null ) {
			unset( $this->_data[$Key] );
		}
		else {
			$this->_data[$Key] = $Value;
		}
	}
	
	/**
	* return the value of the associated key
	*
	* @param String
	* @return mixed
	*/
	public function get($Key)
	{
		if(isset($this->_data[$Key])){
			return $this->_data[$Key];
		}
		return null;
	}
	
	// =================================
	// === Source management === === ===
	
	public function getSources()
	{
		if( isset($this->_sources) )
			return $this->_sources;
		return null;
	}
	
	/**
	* return informations about the sources (selected items, other ..)
	*
	* @param String : Id of the source
	* @return mixed
	*/
	public function getSourceInfos($Id)
	{
		if(isset($this->_sources[$Id])){
			return $this->_sources[$Id]['infos'];
		}
		return null;
	}
	
	/**
	* return informations about the sources (selected items, other ..)
	*
	* @param String : Id of the source
	* @return mixed
	*/
	public function getSourceData($Id)
	{
		if(isset($this->_sources[$Id])){
			return $this->_sources[$Id]['data'];
		}
		return null;
	}
	
	/**
	* return informations about the sources (selected items, other ..)
	*
	* @param String : Id of the source
	* @return mixed
	*/
	public function setSourceInfos($Id, $infos)
	{
		if( $infos == null) {
			unset( $this->_sources[$Id] );
		}
		else {
			if( !isset($this->_sources[$Id]) ) {
				$this->_sources[$Id] = Array();
			}
			$this->_sources[$Id]['infos'] = $infos;
		}
	}
	
	/**
	* return informations about the sources (selected items, other ..)
	*
	* @param String : Id of the source
	* @return mixed
	*/
	public function setSourceData($Id, $data)
	{
		if( $data == null) {
			unset( $this->_sources[$Id] );
		}
		else {
			if( !isset($this->_sources[$Id]) ) {
				$this->_sources[$Id] = Array();
			}
			$this->_sources[$Id]['data'] = $data;
		}
	}
	
} /* end of the class tx_icsopendata_Repository */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/class.repository.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/class.repository.php']);
}

?>