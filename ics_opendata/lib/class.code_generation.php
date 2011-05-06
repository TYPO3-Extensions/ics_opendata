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
 * Generate content for all files needed for a new opendata extension
 *
 * @author	Mathias Cocheri <mcocheri@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendata
 */
class tx_icsopendata_codegeneration
{

	// === ATTRIBUTS ============================================================================== //
	
	private $_extkey = 'ics_opendata';
	
	// === OPERATIONS ============================================================================= //
	
	/**
	* generate files for the new opendata extension
	*
	*
	* @return array('command', 'datasource', 'extlocalconf', 'errors')
	*/
	public function generateCode()
	{		
		$commandfiles = Array();
		$sourcedatafiles = Array();
		$extlocalconf = '';
		
		$files = array();
		
		$errors = array();
		
		// Retrieve template path
		$command_path = t3lib_extMgm::extPath( "ics_opendata", $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['templates']['command']['command'] );
		$datasource_path = t3lib_extMgm::extPath( "ics_opendata", $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['templates']['datasource']['datasource'] );
		$datasourceconnexion_path = t3lib_extMgm::extPath( "ics_opendata", $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['templates']['datasource']['connexion'] );
		$extlocalconf_path = t3lib_extMgm::extPath("ics_opendata", $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['templates']['extlocalconf']['extlocalconf'] );
		
		// Load templates
		if( !$command_template = file_get_contents($command_path) )
			$errors[] = "ERROR : COMMAND TEMPLATE NOT FOUND";
		if( !$datasource_template = file_get_contents($datasource_path) )
			$errors[] = "ERROR : DATASOURCE TEMPLATE NOT FOUND";
		if( !$datasourceconnexion_template = file_get_contents($datasourceconnexion_path) )
			$errors[] = "ERROR : DATASOURCE TEMPLATE NOT FOUND";
		if( !$extlocalconf_template = file_get_contents($extlocalconf_path) )
			$errors[] = "ERROR : EXTCONF TEMPLATE NOT FOUND";
		
		if( !empty($errors) ) {
			$files['errors'] = $errors;
			return $files;
		}
		
		// Generate command, datasource and ext_localconf
		$extkey = $GLOBALS['repository']->get('extensionkey');
		$commands = $GLOBALS['repository']->get('commands');
		$version = $GLOBALS['repository']->get('packageversion');
		$sourceconnexion_filename = 'tx_' . str_replace('_', '', $extkey) . '_sourceconnexion';
		$sourceconnexions = array();
		$commandconf = '';
		foreach($commands as $command){
			
			if( !$command->isActive() )
				continue;
			
			if( isset($post['extensionkey'] ) ) {
				$extkey = htmlspecialchars($this->cleanStr($post['extensionkey']));
			}
			$classname = 'tx_' . str_replace('_', '', $extkey ) . '_' . strtolower($command->getName()) . '_command';
			$datasource = $command->getTableXml()->getName();
			$sourceclassname = 'tx_' . str_replace('_', '', $extkey ) . '_' . strtolower($datasource) . '_datasource';
			
			
			// --- Generate command file
			$command_content = $this->generateCommand($command, $command_template);
			$command_content = str_replace('%%%COMMANDCLASSNAME%%%', $classname, $command_content);
			
			
			// --- Generate datasource file
			$datasource_content = $this->generateDatasource($command, $datasource_template);
			$datasource_content = str_replace('%%%SOURCECLASSNAME%%%', $sourceclassname, $datasource_content);			
			$datasource_content = str_replace('%%%SOURCECONNEXION%%%', 'opendata/datasource/' . $sourceconnexion_filename . '.php', $datasource_content);
			
			
			// --- general informations
			if( empty($extensiondescription) )
				$extensiondescription = 'Short description of the class ' . $command->getName();
			$command_content = $this->generateGeneralInfos($command_content, array('DESCRIPTION'), array('DESCRIPTION' => $extensiondescription));
			$datasource_content = $this->generateGeneralInfos($datasource_content, array('DESCRIPTION'), array('DESCRIPTION' => $extensiondescription));
			
			$item = str_replace('_', '', strtolower($command->getTableXml()->getName()));
			$command_content = str_replace('%%%QUERYNAME%%%', get . ucfirst($item) . 's', $command_content);
			$datasource_content = str_replace('%%%QUERYNAME%%%', get . ucfirst($item) . 's', $datasource_content);
			
			// --- Add the command to the ext_localconf
			$commandconf_content = $this->generateCommandExtConf($command, $version);
			$commandconf_content = str_replace('%%%COMMANDCLASSNAME%%%', $classname, $commandconf_content);
			$commandconf_content = str_replace('%%%SOURCECLASSNAME%%%', $sourceclassname, $commandconf_content);

			
			// --- Add the functions to connect to datasource
			$functionName = str_replace('_', '', strtolower($command->getTableXml()->getTable()->getSource()->getType())) . '_' . str_replace('_', '', strtolower($command->getTableXml()->getTable()->getSource()->getName()));
			if (!isset($sourceconnexions[$functionName]))
			{
				$sourceconnexions[$functionName] = $this->generateDatasourceConnexion($functionName, $command->getTableXml()->getTable()->getSource());
			}
			if (!is_array($sourceconnexions[$functionName]['connexion']))
				$sourceconnexions[$functionName]['connexion'] = array();
			$sourceconnexions[$functionName]['connexion'][] = $command->getName();
			
			// --- save files infos
			$files[$classname] = array();
			$files[$classname]['path'] = '/opendata/';
			$files[$classname]['filename'] = 'class.' . $classname . '.php';
			$files[$classname]['content'] = $command_content;
			$files[$sourceclassname] = array();
			$files[$sourceclassname]['path'] = '/opendata/datasource/';
			$files[$sourceclassname]['filename'] = 'class.' . $sourceclassname . '.php';
			$files[$sourceclassname]['content'] = $datasource_content;
			$commandconf .= $commandconf_content;
			
			// --- user comments
			$datapath = t3lib_extMgm::extPath( $this->_extkey, "doc/ics_od.dat");
			$extensionlist = file_get_contents($datapath);
			if( $extensionlist ) {
				$extensionlist = unserialize($extensionlist);
			}
			else {
				$extensionlist = array();
			}
			if( isset($extensionlist[$extkey]) ) {
				if( is_dir($extensionlist[$extkey]) ) {
					$extpath = $extensionlist[$extkey];
				}
			}
			$command_content = $this->generateUserCode($classname, $files[$classname], $extpath);
			$datasource_content = $this->generateUserCode($sourceclassname, $files[$sourceclassname], $extpath);
		}
		
		if (!empty($sourceconnexions) && is_array($sourceconnexions))
		{
			$sourceconnexion_functions = array();
			$sourceconnexion_extConfs = array();
			$sourceconnexion_conns = array();
			foreach ($sourceconnexions as $connexion)
			{
				$sourceconnexion_functions[] = $connexion['function'];
				$sourceconnexion_extConfs[] = $connexion['extConf'];
				$sourceconnexion_conns[] = implode(', ', $connexion['connexion']);
			}
		}
			
		// --- Generate ext_localconf
		if( !empty($commandconf) ) {
			$extlocalconf = str_replace('%%%COMMANDS%%%', $commandconf, $extlocalconf_template);
			if (!empty($sourceconnexion_extConfs) && is_array($sourceconnexion_extConfs))
			{
				$extlocalconf = str_replace('%%%DATASOURCECONNEXIONS%%%', implode(chr(10), $sourceconnexion_extConfs), $extlocalconf);
				if (!empty($sourceconnexion_conns) && is_array($sourceconnexion_conns))
				{
					$extlocalconf = str_replace('%%%CONNEXIONS%%%', implode(', ', $sourceconnexion_conns), $extlocalconf);
				}
				else
				{
					$extlocalconf = str_replace('%%%CONNEXIONS%%%', '', $extlocalconf);					
				}
			}
			else
			{
				$extlocalconf = str_replace('%%%DATASOURCECONNEXION%%%', '', $extlocalconf);
			}			
			$extlocalconf = str_replace('%%%EXTENSIONKEY%%%', $extkey , $extlocalconf);
			$files['ext_localconf'] = array();
			$files['ext_localconf']['path'] = '/';
			$files['ext_localconf']['filename'] = 'ext_localconf.php';
			$files['ext_localconf']['content'] = $extlocalconf;
			$this->generateUserCode('ext_localconf', $files['ext_localconf'], $extpath);
		}
		
		// --- Generate ext_emconf
		$extemconf = $this->generateEmConf();
		$files['ext_emconf'] = array();
		$files['ext_emconf']['path'] = '/';
		$files['ext_emconf']['filename'] = 'ext_emconf.php';
		$files['ext_emconf']['content'] = $extemconf;
		
		// --- Generate ext_icon
		$exticon = $this->generateExtIcon('default');
		$files['ext_icon'] = array();
		$files['ext_icon']['path'] = '/';
		$files['ext_icon']['filename'] = 'ext_icon.gif';
		$files['ext_icon']['content'] = $exticon;
		
		// --- Generate datasource connexion file
		if (!empty($sourceconnexion_functions) && is_array($sourceconnexion_functions))
		{
			$sourceconnexion = str_replace('%%%FUNCTIONS%%%', implode(chr(10), $sourceconnexion_functions), $datasourceconnexion_template );
			$sourceconnexion = str_replace('%%%SOURCECONNEXION%%%', $sourceconnexion_filename , $sourceconnexion);
			$sourceconnexion = $this->generateGeneralInfos($sourceconnexion);
			$files[$sourceconnexion_filename] = array();
			$files[$sourceconnexion_filename]['path'] = '/opendata/datasource/';
			$files[$sourceconnexion_filename]['filename'] = $sourceconnexion_filename . '.php';
			$files[$sourceconnexion_filename]['content'] = $sourceconnexion;
			$sourceconnexion = $this->generateUserCode($sourceconnexion_filename, $files[$sourceconnexion_filename], $extpath);
		}
		
		return $files;
	} // End generation
	
	/**
	 *
	 * @param String $content The content stream
	 * @param Array $markerArray
	 * @param Array $values	Associative array marker => value
	 *
	 */
	private function generateGeneralInfos($content, $markerArray, $values)
	{
		$markers = array('YEAR', 'AUTHOR', 'EMAIL', 'EXTENSIONKEY');
		if (!empty($markerArray))
			$markers = array_merge($markers, $markerArray);

		foreach ($markers as $marker)
		{
			switch(strtoupper($marker))
			{
				case 'YEAR':
					$date = date('Y');
					if (isset($values[$marker]))
						$date = $values[$marker];
					$content = str_replace('%%%YEAR%%%', $date, $content);
					break;
				case 'AUTHOR':
					$authorname = $GLOBALS['repository']->get('authorname');
					if( empty($authorname) )
						$authorname = 'author name';
					if (isset($values[$marker]))
						$authorname = $values[$marker];
					$content = str_replace('%%%AUTHOR%%%', $authorname, $content);
					break;
				case 'EMAIL':
					$authoremail = $GLOBALS['repository']->get('authoremail');
					if( empty($authoremail) )
						$authoremail = 'author@mail.com';
					$content = str_replace('%%%EMAIL%%%', $authoremail, $content);
					break;
				case 'EXTENSIONKEY':
					$extkey = $GLOBALS['repository']->get('extensionkey');
					if (isset($values[$marker]))
						$extkey = $values[$marker];
					$content = str_replace('%%%EXTENSIONKEY%%%', $extkey, $content);
					break;
				default :
					$content = str_replace('%%%' . $marker . '%%%', $values[$marker], $content);
			}
		}
		return $content;
	}
	
	/**
	* generate exticon content
	*
	* @param String : exticon type (default = 'default')
	* @return String
	*/
	private function generateExtIcon($exticontype)
	{
		if( empty($exticontype) )
			$exticontype = 'default';
		$icons = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['icons']['extension'];
		if( in_array($exticontype, array_keys($icons)) ) {
			$iconpath = t3lib_extMgm::extPath( "ics_opendata", $icons[$exticontype]);
		}
		else {
			$iconpath = t3lib_extMgm::extPath( "ics_opendata", $icons['default']);
		}
		
		if( !$icon = file_get_contents($iconpath) ) {
			$errors[] = "ERROR : EXTENSION ICON NOT FOUND";
			return '';
		}
		
		return $icon;
	}
	
	/**
	* generate ext_localconf content
	*
	* @param DataCommand : command selected
	* @return String
	*/
	private function generateCommandExtConf($command, $version)
	{
		$commandname = $command->getName();
		$items = strtolower($command->getTableXml()->getName());
		$classcommandname = str_replace('_', '', strtolower($command->getTableXml()->getName()));
	
		$content = chr(10) . '// --- ' . $commandname . chr(10) . 
		'$TYPO3_CONF_VARS[\'EXTCONF\'][\'ics_opendata_api\'][\'command\'][\'' . $version . '\'][\'' . $commandname . '\'] = \'EXT:%%%EXTENSIONKEY%%%/opendata/class.%%%COMMANDCLASSNAME%%%.php:%%%COMMANDCLASSNAME%%%\';' . chr(10) . 
		'$TYPO3_CONF_VARS[\'EXTCONF\'][\'%%%EXTENSIONKEY%%%\'][\'datasource\'][\'' . $items . '\'] = \'EXT:%%%EXTENSIONKEY%%%/opendata/datasource/class.%%%SOURCECLASSNAME%%%.php:%%%SOURCECLASSNAME%%%\';';
		
		return $content;
	}
	
	/**
	* generate ext_emconf content
	*
	* @return String
	*/
	private function generateEmConf()
	{
		$EM_CONF = array(
			'title' => addslashes($GLOBALS['repository']->get('extensiontitle')),
			'description' => addslashes($GLOBALS['repository']->get('extensiondescription')),
			'category' => 'misc',
			'author' => addslashes($GLOBALS['repository']->get('authorname')),
			'author_email' => addslashes($GLOBALS['repository']->get('authoremail')),
		);
			
		
		$filecontent = '<?php
########################################################################
# Extension Manager/Repository config file for ext: "' . $GLOBALS['repository']->get('extensionkey') . '"
#
# Auto generated ' . date('j-m-Y G:i') . '
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(' . chr(10);
		foreach( $EM_CONF as $key=>$value ) {
			$filecontent .= chr(9) . '\'' . $key . '\' => \'' . $value . '\',' . chr(10);
		}
		
		$filecontent .= chr(9) . '\'shy\' => \'\',
	\'dependencies\' => \'ics_opendata_api\',
	\'conflicts\' => \'\',
	\'priority\' => \'\',
	\'module\' => \'\',
	\'state\' => \'alpha\',
	\'internal\' => \'\',
	\'uploadfolder\' => 0,
	\'createDirs\' => \'\',
	\'modify_tables\' => \'\',
	\'clearCacheOnLoad\' => 0,
	\'lockType\' => \'\',
	\'author_company\' => \'\',
	\'version\' => \'0.0.0\',
	\'constraints\' => array(
		\'depends\' => array(
			\'ics_opendata_api\' => \'\',
		),
		\'conflicts\' => array(
		),
		\'suggests\' => array(
		),
	),
	\'_md5_values_when_last_written\' => \'\',
);';
		
		return $filecontent;
	}
	
	/**
	* generate datasource content
	*
	* @param DataCommand : command selected
	* @return String
	*/
	private function generateDatasource($command, $datasourcetemplate)
	{
		$sourcetype = $command->getTableXml()->getTable()->getSource()->getType();
		$class = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources'][$sourcetype]['generation'];
		if( empty($class) )
			return 'ERROR : GENERATION CLASS NOT FOUND';
		$gen = t3lib_div::getUserObj($class);
		
		$queriesfilters = $GLOBALS['repository']->get('queryfilters');
		$GLOBALS['repository']->set('queryfilters', null);
		
		$queries = $gen->generateQueries($command, 2, $queriesfilters);
		$datasourcecontent = str_replace('%%%DATASOURCEQUERIES%%%', $queries, $datasourcetemplate);
		
		return $datasourcecontent;
	}
	
	/**
	 * Generate datasource connexion function
	 *
	 * @param	string		$functionName	The name of the function
	 * @param	SourceData	$source			The datasource
	 *
	 * @return	mixed	The array contained function's content and extconf
	 */
	private function generateDatasourceConnexion($functionName, $source)
	{
		$class = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['sources'][$source->getType()]['connexiongeneration'];
		if( empty($class) )
			return 'ERROR : GENERATION CLASS NOT FOUND';
		$gen = t3lib_div::getUserObj($class);
		
		$function = $gen->generateFunction($functionName, $source);
		$extConf = $gen->generateExtConf($source);
		return array('function' => $function, 'extConf' => $extConf);
	}	
	
	
	// --- command generation ---------------------------------------------------------- //
	/**
	* generate command content
	*
	* @param DataCommand : command selected
	* @return String
	*/
	private function generateCommand($command, $commandtemplate)
	{
		// error codes
		$errorcodes = $this->generateErrorCodes($command);
		$commandcontent = str_replace('%%%ERRORCODE%%%', $errorcodes, $commandtemplate);
		
		// default value
		$defaultvalue = $this->generateDefaultValue($command);
		$commandcontent = str_replace('%%%DEFAULTVALUES%%%', $defaultvalue, $commandcontent);
		
		// allowed value
		$allowedvalue = $this->generateAllowedValue($command);
		$commandcontent = str_replace('%%%ALLOWEDVALUES%%%', $allowedvalue, $commandcontent);
		
		// test params
		$test = $this->generateTest($command);
		$commandcontent = str_replace('%%%TESTPARAM%%%', $test, $commandcontent);
		
		// test params
		$switchquery = $this->generateQueryChoice($command);
		$commandcontent = str_replace('%%%DATASOURCEQUERY%%%', $switchquery, $commandcontent);
		
		// type conversion
		$typeconversion = $this->generateTypeConversion($command, 3);
		$commandcontent = str_replace('%%%TYPECONVERSION%%%', $typeconversion, $commandcontent);
		
		$item = str_replace('_', '', strtolower($command->getTableXml()->getName()));
		$commandcontent = str_replace('%%%ITEM%%%', $item, $commandcontent);
		$commandcontent = str_replace('%%%ITEMS%%%', $item . 's', $commandcontent);
		
		return $commandcontent;
	}
	
	private function generateErrorCodes($command)
	{
		$indent = str_repeat(chr(9), 1);
		$errorcontent = $indent . 'const EMPTY_%%%PARAMETERUP%%%_CODE = %%%ERRORCODEEMPTY%%%;' . chr(10) . 
						$indent . 'const EMPTY_%%%PARAMETERUP%%%_TEXT = "%%%PARAMETERLO%%% should be not empty.";' . chr(10) . 
						$indent . 'const INVALID_%%%PARAMETERUP%%%_CODE = %%%ERRORCODEINVALID%%%;' . chr(10) . 
						$indent . 'const INVALID_%%%PARAMETERUP%%%_TEXT = "The specified value is not valid for %%%PARAMETERLO%%%.";';
		
		if( $command->countParams() == 0 )
			return '';
		
		$errorcode = 100;
		$errorsconst = chr(10);
		for($i=0 ; $i<$command->countParams() ; $i++) {
			$errorconst = str_replace('%%%PARAMETERUP%%%', strtoupper($command->getParam($i)), $errorcontent);
			$errorconst = str_replace('%%%PARAMETERLO%%%', strtolower($command->getParam($i)), $errorconst);
			$errorconst = str_replace('%%%ERRORCODEEMPTY%%%', $errorcode, $errorconst);
			$errorcode++;
			$errorconst = str_replace('%%%ERRORCODEINVALID%%%', $errorcode, $errorconst);
			$errorcode++;
			$errorsconst .= $errorconst . chr(10);
		}
		
		return $errorsconst;
	}
	
	private function generateDefaultValue($command)
	{
		if( $command->countParams() == 0 )
			return '';

		$defaultvaluecontent = chr (10) . chr(9) . 'var $params = array(' . chr(10);
		for($i=0 ; $i<$command->countParams() ; $i++) {
			if( $command->getDefaultValue($i) != null)
				$defaultvaluecontent .= chr(9) . chr(9) . '\'' . $command->getParam($i) . '\' => \'' . addslashes($command->getDefaultValue($i)) . '\',' . chr(10);
		}
		$defaultvaluecontent .= chr(9) . ');';
			
		return $defaultvaluecontent;
	}
	
	private function generateAllowedValue($command)
	{
		return chr(10);
	}
	
	private function generateTest($command)
	{
		if( $command->countParams() == 0 )
			return '';
		
		$indent = str_repeat(chr(9), 2);
		$indentbloc = $indent . chr(9);

		$classname = 'tx_' . str_replace('_', '', $GLOBALS['repository']->get('extensionkey')) . '_' . strtolower($command->getName()) . '_command';
		$testcontent = chr(10);
		
		// empty test
		$emptytemplate = $indent . 'if (empty($params[\'%%%PARAMETERLO%%%\']))' . chr(10) . 
							$indent . '{' . chr(10) . 
							$indentbloc . 'makeError($xmlwriter, %%%CLASSNAME%%%::EMPTY_%%%PARAMETERUP%%%_CODE, %%%CLASSNAME%%%::EMPTY_%%%PARAMETERUP%%%_TEXT);' . chr(10) .
							$indentbloc . 'return;' . chr(10) .
							$indent . '}';
							
		for($i=0 ; $i<$command->countParams() ; $i++) {
			if( $command->isParamRequired($i) ) {
				$emptytestcontent = str_replace('%%%PARAMETERLO%%%', strtolower($command->getParam($i)), $emptytemplate);
				$emptytestcontent = str_replace('%%%PARAMETERUP%%%', strtoupper($command->getParam($i)), $emptytestcontent);
				$emptytestcontent = str_replace('%%%CLASSNAME%%%', $classname, $emptytestcontent);
				$testcontent .= $emptytestcontent . chr(10);
			}
		}
		
		// valid test
			
		
							
		// filter params test
		$filtertemplate = $indent . 'if (($params[\'%%%ACTIVATION_PARAMETERLO%%%\'] == \'%%%ACTIVATION_VALUE%%%\') && empty($params[\'%%%PARAMETERLO%%%\']))' . chr(10) . 
							$indent . '{' . chr(10) . 
							$indentbloc . 'makeError($xmlwriter, %%%CLASSNAME%%%::EMPTY_%%%PARAMETERUP%%%_CODE, %%%CLASSNAME%%%::EMPTY_%%%PARAMETERUP%%%_TEXT);' . chr(10) . 
							$indentbloc . 'return;' . chr(10) . 
							$indent . '}';
							
		for($i=0 ; $i<$command->countFilters() ; $i++) {
			$filter = $command->getFilter($i);
			for($j=0 ; $j<$filter->getParamCount() ; $j++) {
				$activationparam = $filter->getActivationParam();
				if( !empty($activationparam) ) {
					$filtertestcontent = str_replace('%%%ACTIVATION_PARAMETERLO%%%', strtolower($activationparam), $filtertemplate);
					$filtertestcontent = str_replace('%%%ACTIVATION_VALUE%%%', $filter->getActivationValue(), $filtertestcontent);
					$filtertestcontent = str_replace('%%%PARAMETERLO%%%', strtolower($filter->getParam($j)), $filtertestcontent);
					$filtertestcontent = str_replace('%%%PARAMETERUP%%%', strtoupper($filter->getParam($j)), $filtertestcontent);
					$filtertestcontent = str_replace('%%%CLASSNAME%%%', $classname, $filtertestcontent);
					$testcontent .= $filtertestcontent . chr(10);
				}
			}
		}
		
		return $testcontent;
	}
	
	private function generateQueryChoice($command)
	{
		// Generate the switch array, used to build switch php structure
		$switcharray = Array();
		$issetarray = Array();
		for($i=0 ; $i<$command->countFilters() ; $i++) {
			$filter = $command->getFilter($i);
			$activationparam = $filter->getActivationParam();
			$activationvalue = $filter->getActivationValue();
			if( $activationparam != null ) {
				if( !isset($switcharray[$activationparam]) ) {
					$switcharray[$activationparam] = Array();
				}
				if( !isset($switcharray[$activationparam][$activationvalue]) ){
					$switcharray[$activationparam][$activationvalue] = Array();
				}
				$switcharray[$activationparam][$activationvalue][$filter->getName()] = $filter;
			}
			else {
				for( $j=0 ; $j<$filter->getParamCount() ; $j++ ) {
					$param = $filter->getParam($j);
					if( !isset($issetarray[$param]) ) {
						$issetarray[$param] = Array();
					}
					$issetarray[$param][$filter->getName()] = $filter;
				}
			}
		}
		return $this->buildSwitch($switcharray, $issetarray, array(), array(), 2);
	}
	
	private function buildSwitch(array $switcharray, array $issetarray, array $activefilters, array $inactivefilters, $nbindent) 
	{
		$indent = str_repeat(chr(9), $nbindent);
		if( empty($switcharray) ) {
			return $this->buildIf($issetarray, $activefilters, $inactivefilters, $nbindent);
		}
		
		$indentcase = $indent . chr(9);
		$indentbloc = $indentcase . chr(9);
		
		reset($switcharray);
		$switcharraycopie = $switcharray;
		$param = key($switcharray);
		
		
		if( isset($issetarray[$param]) ) {
			$filters = $this->deleteAndCleanArray($issetarray, $param);
			$content =  chr(10) . 
				$indent . 'if( !isset($params[\'' . $param . '\']) ) {' . $this->buildSwitch($switcharray, $issetarray, $activefilters, array_merge($inactivefilters, $filters),  $nbindent + 1) . chr(10) . 
				$indent . '}' . chr(10) . 
				$indent . 'else {' . $this->buildSwitch($switcharraycopie, $issetarray, array_merge($filters, $activefilters), $inactivefilters, $nbindent + 1) . chr(10) . 
				$indent . '}';
			return $content;
		}
		
		$filters = array_shift($switcharray);
		
		$content = chr(10) . 
			$indent . 'switch($params[\'' . $param . '\']) {';
		foreach($filters as $key=>$filterlist) {
			$content .= chr(10) . 
				$indentcase . 'case \'' . addslashes($key) . '\' :' . $this->buildSwitch($switcharray, $issetarray, array_merge($filterlist, $activefilters), $inactivefilters, $nbindent + 2) . chr(10) . 
				$indentbloc . 'break;';
			$querynum++;
		}
		$content .= chr(10) . 
			$indentcase . 'default:' . $this->buildSwitch($switcharray, $issetarray, $activefilters, $inactivefilters, $nbindent + 2) . chr(10) . 
			$indentbloc . 'break;' . chr(10) .
			$indent . '}';
			
		return $content;
	}
	
	private function buildIf(array $issetarray, array $activefilters, array $inactivefilters, $nbindent) 
	{
		$indent = str_repeat(chr(9), $nbindent);
		if( !empty($issetarray) ) {
			reset($issetarray);
			$param = key($issetarray);
			$copyarray = $issetarray;
			array_shift($copyarray);
			$filters = $this->deleteAndCleanArray($issetarray, $param);
			$content = chr(10) . 
				$indent . 'if( !isset($params[\'' . $param . '\']) ) {' . $this->buildIf($issetarray, $activefilters, array_merge($inactivefilters, $filters), $nbindent + 1) . chr(10) . 
				$indent . '}' . chr(10) . 
				$indent . 'else {' . $this->buildIf($copyarray, array_merge($filters, $activefilters), $inactivefilters, $nbindent + 1) . chr(10) . 
				$indent . '}';
			return $content;
		}
		foreach( $activefilters as $filtername=>$filter ) {
			if( !isset($inactivefilters[$filtername]) ) {
				$name .= $filtername;
			}
			else {
				unset( $activefilters[$filtername] );
			}
		}
		if( empty($name) )
				$name = 'All';
				
		// Save query name for datasource generation
		$queryfilters = $GLOBALS['repository']->get('queryfilters');
		if( empty($queryfilters) )
			$queryfilters = array();
		$queryfilters[$name] = $activefilters;
		$GLOBALS['repository']->set('queryfilters', $queryfilters);
		
		return chr(10) . $indent . '$%%%ITEMS%%% = $datasource->%%%QUERYNAME%%%' . $name . '($params);';
		
		
	}
	
	private function deleteAndCleanArray(& $issetarray, $keyparam)
	{
		if( empty($issetarray) )
			return array();
			
		$filtertodelete = $issetarray[$keyparam];
		unset($issetarray[$keyparam]);
		
		foreach( $issetarray as $param=>$filters ) {
			$issetarray[$param] = array_diff_key($filters, $filtertodelete);
			if( empty($issetarray[$param]) )
				unset( $issetarray[$param] );
		}
		return $filtertodelete;
	}
	
	private function generateTypeConversion($command, $nbindent)
	{
		$typeconversioncontent = chr(10);
		$tablexml = $command->getTableXml();
		$fieldsxml = $tablexml->getAllChildren();
		
		foreach($fieldsxml as $fieldxml) {
			$link = $fieldxml->getLink();
			$inputtype = $link->getInputType();
			$outputtype = $link->getOutputType();
			$conversionclass = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->_extkey]['types'][$inputtype][$outputtype];
			$typeconverter = t3lib_div::getUserObj($conversionclass);
			
			$elementcontent = $typeconverter->generateCast($nbindent);
			$elementcontent = str_replace('%%%XMLDATA%%%', '$element[\'%%%XMLFIELDNAME%%%\']', $elementcontent);
			$elementcontent = str_replace('%%%SOURCEDATA%%%', '$%%%ITEM%%%[\'%%%XMLFIELDNAME%%%\']', $elementcontent);
			
			$elementcontent = str_replace('%%%XMLFIELDNAME%%%', $fieldxml->getName(), $elementcontent);

			$typeconversioncontent .= $elementcontent . chr(10);
		}
		
		return $typeconversioncontent;
	}
	

	// --- End command generation -------------------------------------------------------- //

	// --- User code generation ---------------------------------------------------------- //	
	private function generateUserCode($classalias, & $classinfos, $extpath)
	{
		$deletearray = t3lib_div::_GP('deleteusercode');

		$test = array();
		$nbusercode = preg_match_all('#(' . chr(9) . '*)%%%USERCODE(.*)%%%#', $classinfos['content'], $tags);

		for( $i=0 ; $i<$nbusercode ; $i++ ) {
			$indent = $tags[1][$i];
			$queryname = $tags[2][$i];
			if( empty($queryname) )
				$queryname = $i;
			
			$tagtop = $indent . '// *************************' . chr(10) . 
				$indent . '// * User inclusions ' . $queryname . chr(10) . 
				$indent . '// * DO NOT DELETE OR CHANGE THOSE COMMENTS' . chr(10) .
				$indent . '// *************************';
			
			$tagmid = chr(10) . $indent . chr(10) . 
				$indent . '// ... (Add additional operations here) ...' . chr(10) . 
				$indent . chr(10);
			
			$tagbot = $indent . '// * End user inclusions ' . $queryname;
				
			if( !empty($extpath) ) {
				$path = $extpath . $classinfos['path'] . $classinfos['filename'];
				$usercode = $this->retrieveUserCode($path, $tagtop, $tagbot);
				var_dump($usercode);
			}
			if( !empty($usercode) && !in_array($classalias, $deletearray) ) {
				$mid = $usercode;
			}
			else {
				$mid = $tagmid;
			}
			$classinfos['content'] = preg_replace('#(' . chr(9) . '*)%%%USERCODE(.*)%%%#', $tagtop . $mid . $tagbot . chr(10), $classinfos['content'], 1);
			
		}
	}
	
	private function retrieveUserCode($path, $tagtop, $tagbot)
	{
		$code = array();
		$toplines = explode(chr(10), $tagtop);
		$botlines = explode(chr(10), $tagbot);
		
		foreach( $toplines as $i=>$line ) {
			if( $i < (sizeof($toplines) - 1) ) {
				$toppattern .= '^' . preg_quote($line) . '$.';
			}
			else {
				$toppattern .= '^' . preg_quote($line) . '$';
			}
		}
		foreach( $botlines as $i=>$line ) {
			if( $i < (sizeof($botlines) - 1) ) {
				$botpattern .= '^' . preg_quote($line) . '$.';
			}
			else {
				$botpattern .= '^' . preg_quote($line) . '$';
			}
		}
		
		$pattern = '#' . $toppattern . '(.*)' . $botpattern . '#ms';
		
		$filecontent = str_replace(chr(13), '', file_get_contents($path));
		if( !empty($filecontent) ) {
			$nbresult = preg_match($pattern, $filecontent, $code);
		}
		return $code[1];
	}
	
	// --- end user code generation -------------------------------------------------------- //
	
	private function cleanStr($in) {
		$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ ]@i','@[^a-zA-Z0-9_]@');
		$replace = array ('e','a','i','u','o','c','_','');
		return preg_replace($search, $replace, $in);
	}
	
} /* end of class tx_icsopendata_codegeneration */

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/class.code_generation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_opendata/lib/class.code_generation.php']);
}
