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
 * Mysql Data : Analyze a mysql database and contain all base informations about the source
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_MysqlData
        implements tx_icsopendata_SourceData
{

    // === ATTRIBUTS ============================================================================== //
	
	protected $_type = 'mysql';
	protected $_id = null;
    protected $_name = null;
	protected $_tables = Array();
	
    protected $_host = null;
    protected $_base = null;
    protected $_login = null;
    protected $_password = null;

    // === OPERATIONS ============================================================================= //

	/**
     * Initialize data structure
     *
     * @param  String $Name
     * @param  String $Host
	 * @param  String $Base
	 * @param  String $Login
	 * @param  String $Pass
     * @return mixed
     */
    public function initData($Name, $Host, $Login, $Pass, $Base)
    {
        $this->_name = $Name;
        $this->_host = $Host;
        $this->_base = $Base;
        $this->_login = $Login;
		$this->_password = $Pass;
		$this->_id = md5($Name . $Host . $Login . $Pass . $Base);
		
		$newdb = t3lib_div::makeInstance('t3lib_DB');
		
		try {
			$newdb->connectDB($Host, $Login, $Pass, $Base);
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
		
		$basetables = $newdb->admin_get_tables();
		foreach($basetables as $tablename => $tableinfo){
			$fields = $newdb->admin_get_fields($tablename);
			$fieldarray = Array();
			foreach($fields as $fieldname=>$fieldrow){
				$field = new tx_icsopendata_DataField($fieldname, $fieldrow['Type'], $tablename);
				$fieldarray[] = $field;
			}
			$this->_tables[] = new tx_icsopendata_DataTable($tablename, $fieldarray, $this);
		}
		return null;
    }
	
	// --- HERITED OPERATIONS --------------------------------------
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
     * Short description of method getTable
     *
     * @param  Integer $i
     * @return SourceTable
     */
    public function getTable($i)
    {
		if(isset($this->_tables[$i]))
			return $this->_tables[$i];
			
		return null;
    }
	
	public function getTableByName($TableName)
	{
		foreach($this->_tables as $table)
			if($table->getName() == $TableName)
				return $table;
		
		return null;
	}
	
	public function getSourceId()
	{
		return $this->_id;
	}
	
	public function getType()
	{
		return $this->_type;
	}

    /**
     * Short description of method countTable
     *
     * @return Integer
     */
    public function countTables()
    {
		if(isset($this->_tables))
			return sizeof($this->_tables);
			
		return 0;
    }
	
	/**
     * Short description of method printParams
     *
     * @return String : Parameters of the source
     */
    public function printParams()
    {
		$content = '<p>HOST = ' . $this->_host . '</p>
					<p>LOGIN = ' . $this->_login . '</p>
					<p>PASS = ' . $this->_password . '</p>
					<p>BASE = ' . $this->_base . '</p>';
		return $content;
    }

	// --- OTHER OPERATIONS -----------------------------------------
    /**
     * Short description of method getHost
     *
     * @return String
     */
    public function getHost()
    {
		if(isset($this->_host))
			return $this->_host;
		
		return null;
	}

    /**
     * Short description of method getBase
     *
     * @return String
     */
    public function getBase()
    {
		if(isset($this->_base))
			return $this->_base;
			
		return null;
    }

    /**
     * Short description of method getLogin
     *
     * @return String
     */
    public function getLogin()
    {
        if(isset($this->_login))
			return $this->_login;
			
		return null;
    }

    /**
     * Short description of method getPassword
     *
     * @return String
     */
    public function getPassword()
    {
        if(isset($this->_password))
			return $this->_password;
			
		return null;
    }

} /* end of class tx_icsopendata_MysqlData */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/class.mysql_data.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/class.mysql_data.php']);
}

?>