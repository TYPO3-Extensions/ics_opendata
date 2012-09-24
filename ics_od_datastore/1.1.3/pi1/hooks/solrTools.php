<?php
/***************************************************************
 *  Copyright notice
*
*  (c) 2012 Smile <contact@smile.fr>
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
 * Tools to used solr client
 *
 * @author	Giraudeau Étienne <contact@smile.net>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */

class SolrTools {
	
	public static function initSolrClient()
	{
		/* Nom de domaine du serveur Solr */
		define('SOLR_SERVER_HOSTNAME', 'localhost');
			
		/* Si l'on doit exécuter en mode sécurisé ou non */
		define('SOLR_SECURE', false);
			
		/* Port HTTP de connexion */
		define('SOLR_SERVER_PORT', ((SOLR_SECURE) ? 8443 : 8983));
			
		$options = array
		(
				'hostname' => SOLR_SERVER_HOSTNAME,
// 					'login'    => SOLR_SERVER_USERNAME,
// 					'password' => SOLR_SERVER_PASSWORD,
				'port'     => SOLR_SERVER_PORT,
		);

		return new SolrClient($options);
	}
	
	public static function getOldDoc($oldDocId, $solrClient)
	{
		
		$query = new SolrQuery();
		$query->setQuery("id:" . $oldDocId);
		$query_response = $solrClient->query($query);
		$query_response->setParseMode(SolrQueryResponse::PARSE_SOLR_DOC);
		$response = $query_response->getResponse();
		$oldDocResponse = $response->offsetGet('response')->offsetGet('docs');
		$oldDoc = $oldDocResponse[0];
		
		return $oldDoc;
	}
	
	public static function getSimilarDocs($refDocId, $solrClient, $nbOfDoc = 5, $searchField = 'keywords')
	{
		$query = new SolrQuery();
		$query->setQuery("id:" . $refDocId);
		$query->setMlt(TRUE);
		$query->setMltCount($nbOfDoc);
		$query->addMltField($searchField);
		$query->setMltMinTermFrequency(1);
		$query->setMltMinDocFrequency(1);
		$query_response = $solrClient->query($query);
		$query_response->setParseMode(SolrQueryResponse::PARSE_SOLR_OBJ);
		$response = $query_response->getResponse();
		foreach ($response['moreLikeThis'] as $value)
		{
			foreach ($value['docs'] as $doc)
			{
				if($doc['deleted'] === 0 && $doc['hidden'] === 0)
				{
					$docsArray[] =  $doc;
				}
			}
		}
		return $docsArray;
	}
	
	public static function solrDocToSolrInputDoc($solrDoc)
	{
		$doc = new SolrInputDocument();
		
		$solrDocFieldNames = $solrDoc->getFieldNames();
		foreach ($solrDocFieldNames as $field)
		{
			foreach ($solrDoc->getField($field)->values as $value)
			{
				$doc->addField($field,$value);
			}
		}
		return $doc;
	}
}