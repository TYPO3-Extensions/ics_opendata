<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Tsi <tsi@in-cite.net>
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

require_once( t3lib_extMgm::extPath('ics_opendata_api') . 'lib/class.tx_icsopendataapi_parameter.php' );

/**
 * Objet 'value' for the 'ics_opendata_api' extension.
 *
 * @author	Tsi <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsopendataapi
 */
class tx_icsopendataapi_value{

	private $value = '', $description = '';
	private $parameters = array();
	
	/**
	 * Loads values in a document xml
	 * @param $writer XMLWriter
	 */
	function loadXML(XMLreader $xmlreader){		
		// Check the node name.
		if ($xmlreader->name != 'value')
			throw new Exception('value element expected. ' . $xmlreader->name . ' found.');
		// Load attributes.
		$this->setValue($xmlreader->getAttribute('value'));
		if ($xmlreader->isEmptyElement)
			return;
		if (!$xmlreader->read())
			throw new Exception('Unable to read the value node sub elements.');
		while( $xmlreader->nodeType != XMLReader::END_ELEMENT ){
			if( $xmlreader->nodeType == XMLReader::ELEMENT ){
				switch( $xmlreader->name ){
					case 'description':
						// Load description
						if (!$xmlreader->isEmptyElement){
							$this->setDescription($xmlreader->readString());
							while (($xmlreader->nodeType != XMLReader::END_ELEMENT) || ($xmlreader->name != 'description'))
								if (!$xmlreader->read())
									throw new Exception('Unable to read the value description node sub elements.');
						}
						break;
					case 'parameters':
						// Load parameters
						if ($xmlreader->isEmptyElement)
							break;
						if (!$xmlreader->read())
							throw new Exception('Unable to read the value parameters node sub elements.');
						while( $xmlreader->nodeType != XMLReader::END_ELEMENT ){
							if( $xmlreader->nodeType == XMLReader::ELEMENT ){
								$parameter = new tx_icsopendataapi_parameter();
								$parameter->loadXML($xmlreader);
								$this->addParameter($parameter);
							}
							if (!$xmlreader->read())
								throw new Exception('Unable to read the value parameters node sub elements.');
						}
						break;
					default:
						throw new Exception('description or parameters expected. ' . $xmlreader->name . ' found.');
				}
			}
			if (!$xmlreader->read())
				throw new Exception('Unable to read the value node sub elements.');
		}
	}
	
	/**
	 * Put value in a doculent xml
	 * @param $xmlwriter XMLWriter
	 */
	function saveXML(XMLWriter $xmlwriter){
		$xmlwriter->startElement('value');
		$xmlwriter->writeAttribute( 'value' , $this->getValue() );
		$xmlwriter->startElement('description');
		$xmlwriter->text( $this->getDescription() );
		$xmlwriter->endElement();
		if( $this->getParametersCount() > 0 ){
			$xmlwriter->startElement('parameters');
			foreach( $this->parameters as $parameter ){
				$parameter->saveXML($xmlwriter);
			}
			$xmlwriter->endElement();
		}
		$xmlwriter->endElement();
	}

	/**
	 * Loads _POST variables
	 */
	function loadPOST($post){
		// Load value sub element
		foreach( $post as $postvar=>$value){
			switch( $postvar ){
				case 'value':
					$this->setValue($value);
					break;
				case 'description':
					$this->setDescription($value);
					break;
				case 'parameters':
					// Load parameters
					foreach( $value as $post_parameter){
						$parameter = new tx_icsopendataapi_parameter();
						$parameter->loadPOST($post_parameter);
						$this->addParameter($parameter);
					}
					break;
				default:
					throw new Exception('value or description element expected. ' . $postvar . ' found.');
			}
		}
	}

	/**
	 * Retrieves value of value
	 * @return string
	 */
	function getValue(){
		return $this->value;
	}
	
	/**
	 * Defines value of value
	 * @param $value string
	 */
	function setValue($value){
		$this->value = $value;
	}
	
	/**
	 * Retrieves value's description
	 * @return string
	 */	
	function getDescription(){
		return $this->description;
	}
	
	/**
	 * Defines value's description
	 * @param $description string
	 */
	function setDescription( $description ){
		$this->description = $description;
	}
	
	/**
	 * Count value parameters
	 * @return integer The number parameters
	 */
	function getParametersCount(){
		return count($this->parameters);
	}
	
	/**
	 * Retrieves parameter
	 * @param $i integer The indice of parameter
	 * @return parameter
	 */
	function getParameter($i){
		return $this->parameters[$i];
	}
	
	/**
	 * Delete parameter
	 * @param $obj parameter
	 */
	function removeParameter($obj){
		$keys = array_keys($this->parameters, $obj, true);
		rsort($keys);
		foreach ($keys as $key)
			array_splice($this->parameters, $key, 1);
	}
	
	/**
	 * Insert parameter in parameters
	 * @param $obj parameter
	 */
	function addParameter($obj){
		$this->parameters[] = $obj;
	}	
}