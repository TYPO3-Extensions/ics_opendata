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
 * Csv Data : Analyze a csv file and contain all base informations about the source
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_CsvData 
		implements tx_icsopendata_SourceData
{

    // === ATTRIBUTS ============================================================================== //
	
	private $_type = 'csv';
	private $_id = null;
    private $_name = null;
	private $_table = null;
	
	private $_path = null;
    private $_length = null;
    private $_delimiter = null;
    private $_enclosure = null;
    private $_escape = null;
	
    // === OPERATIONS ============================================================================= //

    /**
     * Default constructor
     *
     * @param  String $Name
     * @param  String $Path
	 * @param  Integer $Length : max length of a line
	 * @param  String $Delimiter
	 * @param  String $Enclosure
	 * @param  String $Escape
     * @return mixed
     */
    public function initData($Name, $Path, $Length, $Delimiter, $Enclosure, $Escape)
    {
        $this->_name = $Name;
        $this->_path = $Path;
        $this->_length = $Length;
        $this->_delimiter = $Delimiter;
        $this->_enclosure = $Enclosure;
        $this->_escape = $Escape;
		$this->_id = md5($Name . $Path . $Length . $Delimiter . $Enclosure . $Escape);
		
		// --- Open csv file
		try{
					$csvfile = fopen($Path, 'r+');
					$csvfields = fgetcsv($csvfile,$Length,$Delimiter);
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
		
		// --- Fill base and Table with fields
		$fields = array();
		foreach($csvfields as $csvfield){
			$field = new tx_icsopendata_DataField($csvfield, 'String', $this->_name . '_table');
			$fields[] = $field;
		}
		$this->_table = new tx_icsopendata_DataTable($this->_name . '_table', $fields, $this);
		
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
		if($i==0 && isset($this->_table))
			return $this->_table;
			
		return null;
    }
	
	public function getTableByName($TableName)
	{
		if($this->_table->getName() == $TableName)
			return $this->_table;
	
		return null;
	}
	
	/**
	* return the unique id of the source
	*
	* return String
	*/
	public function getSourceId()
	{
		return $this->_id;
	}
	
	/**
	* return the source type
	*
	* return String : source type
	*/
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
		if(isset($this->_table))
			return 1;
			
		return 0;
    }
	
	/**
     * Short description of method printParams
     *
     * @return String : Parameters of the source
     */
    public function printParams()
    {
		$content = '<p>PATH = ' . $this->_path . '</p>
					<p>DELIMITER = ' . $this->_delimiter . '</p>
					<p>ENCLOSURE = ' . $this->_enclosure . '</p>
					<p>ESCAPE = ' . $this->_escape . '</p>';
		return $content;
    }
	
	// --- OTHER OPERATIONS -----------------------------------------
	/**
     * Short description of method getPath
     *
     * @return String
     */
    public function getPath()
    {
		if(isset($this->_path))
			return $this->_path;
			
		return null;
    }
	
	/**
     * Short description of method getDelimiter
     *
     * @return String
     */
    public function getDelimiter()
    {
		if(isset($this->_delimiter))
			return $this->_delimiter;
			
		return null;
    }
	
	/**
     * Short description of method getEnclosure
     *
     * @return String
     */
    public function getEnclosure()
    {
		if(isset($this->_enclosure))
			return $this->_enclosure;
			
		return null;
    }
	
	/**
     * Short description of method getEscape
     *
     * @return String
     */
    public function getEscape()
    {
		if(isset($this->_escape))
			return $this->_escape;
			
		return null;
    }

} /* end of class tx_icsopendata_CsvData */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/csv/class.csv_data.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/csv/class.csv_data.php']);
}

?>