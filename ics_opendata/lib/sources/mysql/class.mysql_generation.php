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
 * Generate the datasource file associated to the command file
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_Mysqlgeneration implements tx_icsopendata_source_generation
{
	
	// === OPERATIONS ============================================================================= //
	public function __construct()
	{
		$this->indent = chr(9);
		$this->indentbloc = $this->indent . chr(9);
	}

	/**
	* Return the content of the datasource file associated to the command
	*
	* @param command : DataCommand - command associated to the datasource
	* @param nbindent : int - number of indentation of the first level
	* @param queryfilters : array (queryname=>filters)
	* @return String
	*/
	public function generateQueries($command, $nbindent, $queriesfilters)
	{				
		$xmltable = $command->getTableXml();
		$xmlfields = $xmltable->getAllChildren();
		reset($xmlfields);
		$table = $xmltable->getTable();
		
		// attributs
		$content .= $this->generateQueries_attributes();
		
		// constructor : instanciate a new database
		$content .= $this->generateQueries_constructor($table->getSource());
		
		// sql query operation
		$content .= $this->generateQueries_queryOperations($queriesfilters, $table->getName(), $xmlfields, $nbindent);
		
		$content .= chr(10) . $this->indent . '%%%USERCODEother processing%%%' . chr(10);
		
		return $content;
	}
	
	/**
	 * Generate datasource attributes content
	 *
	 * @return	string	Attributes content
	 */
	protected function generateQueries_attributes()
	{
		return	chr(10) . 
			$this->indent . 'private $_datasourceDB = null;' . chr(10);
	}
	
	/**
	 * Generate datasource constructor
	 *
	 * @param	DataSource	$source	The datasource
	 *
	 * @return	string	Constructor's content
	 */
	protected function generateQueries_constructor($source)
	{
		return chr(10) . 
			$this->indent . '/**' . chr(10) . 
			$this->indent . ' * Constructor' . chr(10) . 
			$this->indent . ' *' . chr(10) . 
			$this->indent . ' */' . chr(10) . 
			$this->indent . 'public function __construct()' . chr(10) . 
			$this->indent . '{' . chr(10) . 
			$this->indentbloc . '$this->_datasourceDB = ' . $source->getType() . '_' . $source->getName() . '_connect();' . chr(10) . 
			$this->indentbloc . '%%%USERCODEconstructor%%%' . chr(10) . 
			$this->indent . '}' . chr(10);
	}
	
	/**
	 * Generate datasource query operations
	 *
	 * @param	array	$queriesfilters	Queries filters
	 * @param	string	$tablename		The tablename of the datasource
	 * @param	array	$xmlfields		The tablename fields alias
	 * @param	int		$nbindent		The content's indent
	 *
	 * @return	string	The content of query operations on the datasource
	 */
	protected function generateQueries_queryOperations($queriesfilters, $tablename, $xmlfields, $nbindent)
	{
		$content .= 
			chr(10) . 
			$this->indent . '/**' . chr(10) . 
			$this->indent . ' * Retrieves datasource\'s records' . chr(10) . 
			$this->indent . ' *' . chr(10) . 
			$this->indent . ' * @param	array	$queryarray	The query array to query on database' . chr(10) . 
			$this->indent . ' *' . chr(10) . 
			$this->indent . ' * @return	array	Array of records' . chr(10) . 
			$this->indent . ' */' . chr(10) . 
			$this->indent . 'public function get($queryarray)' . chr(10) .
			$this->indent . '{' . chr(10) . 
			$this->indentbloc . '%%%USERCODEget%%%' . chr(10) .
			$this->indentbloc . 'return $this->_datasourceDB->exec_SELECTgetRows(' . chr(10) . 
			$this->indentbloc . chr(9) . '$queryarray[\'fields\'],' . chr(10) . 
			$this->indentbloc . chr(9) . '$queryarray[\'fromtable\'],' . chr(10) .
			$this->indentbloc . chr(9) . '$queryarray[\'where\'],' . chr(10) . 
			$this->indentbloc . chr(9) . '$queryarray[\'groupby\'],' . chr(10) . 
			$this->indentbloc . chr(9) . '$queryarray[\'order\'],' . chr(10) . 
			$this->indentbloc . chr(9) . '$queryarray[\'limit\']' . chr(10) . 
			$this->indentbloc . ');' . chr(10) . 
			$this->indent . '} // End get' . chr(10);
		
		foreach( $queriesfilters as $queryname=>$filters ) {
			$filterscontent = array();
			$filterscontent = $this->generateQueryFilter($filters, $tablename, $nbindent + 1);
			
			$functioncontent = 
				$this->indent . '/**' . chr(10) . 
				$this->indent . ' * Retrieves datasource\'s records' . chr(10) . 
				$this->indent . ' *' . chr(10) . 
				$this->indent . ' * @param	array	$params	The parameters to query on database' . chr(10) . 
				$this->indent . ' *' . chr(10) . 
				$this->indent . ' * @return	array	Array of records' . chr(10) . 
				$this->indent . ' */' . chr(10) . 
				$this->indent . 'public function %%%QUERYNAME%%%' . $queryname . '($params)' . chr(10) . 
				$this->indent . '{' . chr(10) . 
				$this->indentbloc . '$queryarray = array();' . chr(10) .
				$this->indentbloc . '$queryarray[\'fields\'] = ' . $this->generateQueryFields($tablename, $xmlfields, $nbindent + 1) . ';' . chr(10) .
				$this->indentbloc . '$queryarray[\'fromtable\'] = ' . $this->generateQueryTable($tablename, $nbindent + 1) . ';' . chr(10) .
				$this->indentbloc . '$queryarray[\'where\'] = ' . $filterscontent['WHERE'] . ';' . chr(10) . 
				$this->indentbloc . '$queryarray[\'groupby\'] = ' . $filterscontent['GROUPEDBY'] . ';' . chr(10) . 
				$this->indentbloc . '$queryarray[\'order\'] = ' . $filterscontent['ORDER'] . ';' . chr(10) . 
				$this->indentbloc . '$queryarray[\'limit\'] = ' . $filterscontent['LIMIT'] . ';' . chr(10) . 
				$this->indentbloc . '%%%USERCODE' . $queryname . '%%%' . chr(10) . 
				$this->indentbloc . 'return $this->get($queryarray);' . chr(10) . 
				$this->indent . '} // End %%%QUERYNAME%%%' . $queryname . chr(10);
						
			$content .= chr(10) . $functioncontent;
		}
		return $content;
	}
	
	/**
	 * Generate query fields
	 *
	 * @param	string	$tablename		The tablename of the datasource
	 * @param	array	$xmlfields		The tablename fields alias
	 * @param	int		$nbindent		The content's indent
	 *
	 * @return string	The content of  query fields list for datasource table
	 */
	protected function generateQueryFields($Tablename, $XmlFields, $nbindent)
	{
		$indent = str_repeat(chr(9), $nbindent);
		if( empty($XmlFields) )
			return $indent . '\'\'';
		$i = 0;
		foreach( $XmlFields as $fieldname=>$xmlelement ) {
			if( $i < (sizeof($XmlFields) - 1) ) {
				$content .= $indent . '\'`' . $Tablename . '`.`' . $fieldname . '` AS `' . $xmlelement->getName() . '`, \' . ' . chr(10);
			}
			else {
				$content .= $indent . '\'`' . $Tablename . '`.`' . $fieldname . '` AS `' . $xmlelement->getName() . '`\'';
			}
			$i++;
		}
		return chr(10) . $content;
	}
	
	/**
	 * Generate query table
	 *
	 * @param	string	$tablename		The tablename of the datasource
	 * @param	int		$nbindent		The content's indent
	 *
	 * @return	string	The content of query table for datasource table
	 */	
	protected function generateQueryTable($Tablename, $nbindent)
	{
		$indent = str_repeat(chr(9), $nbindent);
		$content .= $indent . '\'`' . $Tablename . '`\'';
		
		return chr(10) . $content;
	}
	
	
	/**
	 * Generate query table
	 *
	 * @param	array	$filters		Query filters
	 * @param	string	$tablename		The tablename of the datasource
	 * @param	int		$nbindent		The content's indent
	 *
	 * @return	string	The content of query filter for datasource table
	 */	
	protected function generateQueryFilter($filters, $tablename, $nbindent)
	{
		$indent = str_repeat(chr(9), $nbindent);
		
		$content = '';
		$queryarray = array();
		$queryarray['WHERE'] = array();
		$queryarray['GROUPEDBY'] = array();
		$queryarray['ORDER'] = array();
		$queryarray['LIMIT'] = array();
		
		foreach( $filters as $filter ) {
			if( $filter->isActive() ) {
				$code = $filter->generateFilterCode('mysql', $nbindent);
				$queryarray = array_merge_recursive($queryarray, $code);
			}
		}
		
		// WHERE
		$wherecontent = array();
		$i = 0;
		foreach( $queryarray['WHERE'] as $where ) {
			if( $i < (sizeof($queryarray['WHERE']) -1) ) {
				$wherecontent[] = $where;
			}
			else {
				$wherecontent[] = $where;
			}
			$i++;
		}
		//GROUPEDBY
		$i = 0;
		foreach( $queryarray['GROUPEDBY'] as $groupedby ) {
			if( $i < (sizeof($queryarray['GROUPEDBY']) -1) ) {
				$groupedbycontent .= $groupedby . ' .' . chr(10);
			}
			else {
				$groupedbycontent .= $groupedby;
			}
			$i++;
		}
		// ORDER
		$i = 0;
		foreach( $queryarray['ORDER'] as $order ) {
			if( $i < (sizeof($queryarray['ORDER']) -1) ) {
				$ordercontent .= $order . ' .' . chr(10);
			}
			else {
				$ordercontent .= $order;
			}
			$i++;
		}
		// LIMIT
		foreach( $queryarray['LIMIT'] as $limit ) {
			$limitcontent = $limit;
		}
		
		if( empty($limitcontent) )
			$limitcontent = $indent . '\'\'';
		if( empty($ordercontent) )
			$ordercontent = $indent . '\'\'';
		if( empty($groupedbycontent) )
			$groupedbycontent = $indent . '\'\'';
		if (empty($wherecontent))
			$wherecontent = $indent . '\'\'';
		else
			$wherecontent = implode(chr(10) . $indent . '. \' AND \' .' . chr(10), $wherecontent);
		
		$filterscontent = array();
		$filterscontent['WHERE'] = chr(10) . $wherecontent;
		$filterscontent['GROUPEDBY'] = chr(10) . $groupedbycontent;
		$filterscontent['ORDER'] = chr(10) . $ordercontent;
		$filterscontent['LIMIT'] = chr(10) . $limitcontent;
		
		return $filterscontent;
	}

}

class tx_icsopendata_Mysqlgeneration_connexion implements tx_icsopendata_source_generation_connexion
{
	public function __construct()
	{
		$this->indent = '';
		$this->indentbloc = $this->indent . chr(9);
	}

	public function generateFunction($functionName, $source)
	{
		return chr(10) .
			$this->indent . '/**' . chr(10) . 
			$this->indent . ' * Connect to the datasource ' . $source->getName() . ' type ' . $source->getType() . chr(10) . 
			$this->indent . ' *' . chr(10) . 
			$this->indent . ' * @return	object	The connexion to the datasource' . chr(10) . 
			$this->indent . ' */' . chr(10) . 
			$this->indent . 'function ' . $functionName . '_connect()' . chr(10) . 
			$this->indent . '{' . chr(10) . 
			$this->indentbloc . '$host = $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'user_datatest\'][\'datasourceconnect\'][\'' . $source->getType() . '_' . $source->getName() . '\'][\'host\'];' . chr(10) . 
			$this->indentbloc . '$login = $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'user_datatest\'][\'datasourceconnect\'][\'' . $source->getType() . '_' . $source->getName() . '\'][\'login\'];' . chr(10) . 
			$this->indentbloc . '$password = $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'user_datatest\'][\'datasourceconnect\'][\'' . $source->getType() . '_' . $source->getName() . '\'][\'password\'];' . chr(10) . 
			$this->indentbloc . '$base = $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'user_datatest\'][\'datasourceconnect\'][\'' . $source->getType() . '_' . $source->getName() . '\'][\'base\'];' . chr(10) . chr(10) . 
			$this->indentbloc . '$_datasourceDB = t3lib_div::makeInstance(\'t3lib_DB\');' . chr(10) . 
			$this->indentbloc . 'try {' . chr(10) . 
			$this->indentbloc . chr(9) . '$_datasourceDB->connectDB($host, $login, $password, $base);' . chr(10) . 
			$this->indentbloc . '}' . chr(10) . 
			$this->indentbloc . 'catch (Exception $e)' . chr(10) . 
			$this->indentbloc . '{' . chr(10) . 
			$this->indentbloc . chr(9) . 'return $e->getMessage();' . chr(10) . 
			$this->indentbloc . '}' . chr(10) .  chr(10) . 
			$this->indentbloc . '%%%USERCODE' . $functionName . '_connect%%%' . chr(10) . chr(10) . 
			$this->indentbloc . 'return $_datasourceDB;' . chr(10) . 
			$this->indent . '}' . chr(10);
	}

	public function generateExtConf($source)
	{
		return chr(10) . '// --- Datasource connexions for commands %%%CONNEXIONS%%%' . chr(10) . 
			$this->indent . '$TYPO3_CONF_VARS[\'EXTCONF\'][\'%%%EXTENSIONKEY%%%\'][\'datasourceconnect\'][\'' . $source->getType() . '_' . $source->getName() . '\'][\'host\'] = \'' . $source->getHost() . '\';' . chr(10) . 
			$this->indent . '$TYPO3_CONF_VARS[\'EXTCONF\'][\'%%%EXTENSIONKEY%%%\'][\'datasourceconnect\'][\'' . $source->getType() . '_' . $source->getName() . '\'][\'login\'] = \'' . $source->getLogin() . '\';' . chr(10) . 
			$this->indent . '$TYPO3_CONF_VARS[\'EXTCONF\'][\'%%%EXTENSIONKEY%%%\'][\'datasourceconnect\'][\'' . $source->getType() . '_' . $source->getName() . '\'][\'password\'] = \'' . $source->getPassword() . '\';' . chr(10) . 
			$this->indent . '$TYPO3_CONF_VARS[\'EXTCONF\'][\'%%%EXTENSIONKEY%%%\'][\'datasourceconnect\'][\'' . $source->getType() . '_' . $source->getName() . '\'][\'base\'] = \'' . $source->getBase() . '\';' . chr(10) . chr(10) . 		
			$this->indent . '%%%USERCODE' . $source->getType() . '_' . $source->getName() . ' connexion%%%' . chr(10) . chr(10);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/class.mysql_generation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/sources/mysql/class.mysql_generation.php']);
}