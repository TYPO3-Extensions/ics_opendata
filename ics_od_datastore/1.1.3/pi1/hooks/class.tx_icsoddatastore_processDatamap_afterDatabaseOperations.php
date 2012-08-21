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
 * Hook of the core for the 'ics_od_datastore' extension.
 *
 * @author	Giraudeau Étienne <contact@smile.net>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */
class tx_icsoddatastore_processDatamap_afterDatabaseOperations {
	
	function processDatamap_afterDatabaseOperations($status, $table, $id, $tabChamps, $pObj) {
		
		t3lib_div::debug('solr php client hook start');
		global $TYPO3_DB;
	
		$createFieldLabelArray = array('keywords', 'spatial_cover', 'language', 
				'quality', 'granularity', 'linked_references', 'taxonomy', 'illustration',
				'html_from_csv_display', 'has_dynamic_display', 'param_dynamic_display', 'title', 'description', 
				'technical_data', 'licence', 'time_period', 'update_frequency');
		$updateFieldLabelArray = array('keywords', 'spatial_cover', 'language', 
				'quality', 'granularity', 'linked_references', 'taxonomy', 'illustration',
				'html_from_csv_display', 'has_dynamic_display', 'param_dynamic_display',
				'technical_data', 'licence', 'time_period', 'update_frequency');
		
		if ($table === 'tx_icsoddatastore_filegroups')
		{
			t3lib_div::debug('filegroups start');
			t3lib_div::debug($tabChamps);
			t3lib_div::debug($pObj);
			//creation of solr client
			$solrClient = $this->initSolrClient();
			
			if ($status === 'new' && !$tabChamps[hidden])
			{
				t3lib_div::debug('new filegroups start');
				$doc = new SolrInputDocument();
				
				foreach ($createFieldLabelArray as $fieldLabel)
				{
					$doc->addField($fieldLabel, $tabChamps[$fieldLabel]);
				}
				
				$doc->addField('release_date', date("Y-m-d",$tabChamps[release_date]) . 'T' . date("H:i:s", $tabChamps[release_date]) . 'Z');
				$doc->addField('update_date', date("Y-m-d",$tabChamps[update_date]) . 'T' . date("H:i:s", $tabChamps[update_date]) . 'Z');
				
				// id field
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid',         // SELECT ...
						'tx_icsoddatastore_filegroups',    // FROM ...
						'title="'.$tabChamps[title].'"'	// WHERE
				);
				$row = $TYPO3_DB->sql_fetch_assoc($res);
				$doc->addField('id', $row['uid']);
				$filegroup_id = $row['uid'];
				
				if(isset($tabChamps[manager]))
				{
					// manager field
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'name manager',         // SELECT ...
							'tx_icsoddatastore_tiers',    // FROM ...
							'uid="'.$tabChamps[manager].'"'	// WHERE
					);
					$row = $TYPO3_DB->sql_fetch_assoc($res);
					$doc->addField('manager', $row['manager']);
				}
				
				if(isset($tabChamps[owner]))
				{
					// owner field
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'name owner',         // SELECT ...
							'tx_icsoddatastore_tiers',    // FROM ...
							'uid="'.$tabChamps[owner].'"'	// WHERE
					);
					$row = $TYPO3_DB->sql_fetch_assoc($res);
					$doc->addField('owner', $row['owner']);
				}
				
				if(intval($tabChamps[tx_icsodcategories_categories]) > 0)
				{
					// categories field
					$whereclause = '1=0';
					foreach ($pObj->dbAnalysisStore[0][0]->tableArray[tx_icsodcategories_categories] as $categorieId)
					{
						$whereclause .= ' OR uid=' . $categorieId;
					}
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'name categories',         // SELECT ...
							'tx_icsodcategories_categories',   // FROM ...
							$whereclause	// WHERE
					);
					while($row = $TYPO3_DB->sql_fetch_assoc($res))
						$doc->addField('categories', $row['categories']);
				}

				$addDocResponse = $solrClient->addDocument($doc);
				
				if ($addDocResponse->success())
				{
					$solrClient->commit();
					t3lib_div::debug('new filegroups end');
				}
				else
				{
					$addDocResponse = $solrClient->addDocument($doc);
					
					if ($addDocResponse->success())
					{
						$solrClient->commit();
						t3lib_div::debug('new filegroups end');
					}
					else
					{
						$solrClient->rollback();
						t3lib_div::debug('new filegroups end error');
					}
				}
				
			}
			elseif ($status === 'update')
			{
				t3lib_div::debug('update filegroups start');
				//get old doc
				$oldDoc = $this->getOldDoc($pObj->checkValue_currentRecord[uid], $solrClient);
				
				//Convert old doc to input doc
				$doc = $this->solrDocToSolrInputDoc($oldDoc);

				// add new fields to doc
				foreach ($updateFieldLabelArray as $fieldLabel)
				{
					if($doc->fieldExists($fieldLabel))
					{
// 						t3lib_div::debug('delete '.$fieldLabel);
						$doc->deleteField($fieldLabel);
					}
// 					t3lib_div::debug('add '.$fieldLabel);
					$doc->addField($fieldLabel, $tabChamps[$fieldLabel]);
				}
				
				if (isset($tabChamps['release_date']))
				{
					if($doc->fieldExists('release_date'))
					{
// 						t3lib_div::debug('delete release_date');
						
						$doc->deleteField('release_date');
					}
					$doc->addField('release_date', date("Y-m-d",$tabChamps[release_date]) . 'T' . date("H:i:s", $tabChamps[release_date]) . 'Z');
// 					t3lib_div::debug('add release_date');
				}

				if (isset($tabChamps['release_date']))
				{
					if($doc->fieldExists('update_date'))
					{
						$doc->deleteField('update_date');
					}
					$doc->addField('update_date', date("Y-m-d",$tabChamps[update_date]) . 'T' . date("H:i:s", $tabChamps[update_date]) . 'Z');
				}
				
				// manager field
				if(isset($tabChamps[manager]))
				{
					if($doc->fieldExists('manager'))
					{
						$doc->deleteField('manager');
					}
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'name manager',         // SELECT ...
							'tx_icsoddatastore_tiers',    // FROM ...
							'uid="'.$tabChamps[manager].'"'	// WHERE
					);
					$row = $TYPO3_DB->sql_fetch_assoc($res);
					$doc->addField('manager', $row['manager']);
				}
				
				// owner field
				if(isset($tabChamps[owner]))
				{
					if($doc->fieldExists('owner'))
					{
						$doc->deleteField('owner');
					}
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'name owner',         // SELECT ...
							'tx_icsoddatastore_tiers',    // FROM ...
							'uid="'.$tabChamps[owner].'"'	// WHERE
					);
					$row = $TYPO3_DB->sql_fetch_assoc($res);
					$doc->addField('owner', $row['owner']);
				}
				
				// categories field
				if(intval($tabChamps[tx_icsodcategories_categories]) > 0)
				{
					if($doc->fieldExists('categories'))
					{
						$doc->deleteField('categories');
					}
					$whereclause = '1=0';
// 					t3lib_div::debug(explode(',', $pObj->datamap[tx_icsoddatastore_filegroups][$pObj->checkValue_currentRecord[uid]][tx_icsodcategories_categories]));
					foreach (explode(',', $pObj->datamap[tx_icsoddatastore_filegroups][$pObj->checkValue_currentRecord[uid]][tx_icsodcategories_categories]) as $categorieId)
					{
						if ($categorieId !== '')
						{
							$whereclause .= ' OR uid=' . $categorieId;
						}
					}
// 					t3lib_div::debug($whereclause);
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'name categories',         // SELECT ...
							'tx_icsodcategories_categories',   // FROM ...
							$whereclause	// WHERE
					);
					while($row = $TYPO3_DB->sql_fetch_assoc($res))
					{
						$doc->addField('categories', $row['categories']);
					}
				}
				
				
				//update doc
				$addDocResponse = $solrClient->addDocument($doc);
				
				if ($addDocResponse->success())
				{
					$solrClient->commit();
					t3lib_div::debug('update filegroups end');
				}
				else
				{
					$addDocResponse = $solrClient->addDocument($doc);
				
					if ($addDocResponse->success())
					{
						$solrClient->commit();
						t3lib_div::debug('update filegroups end');
					}
					else
					{
						$solrClient->rollback();
						t3lib_div::debug('update filegroups end error');
					}
				}
			}
// 			hook à trouver
// 			elseif ($status === 'delete')
// 			{
// 				t3lib_div::debug('delete filegroups');
// // 			function processCmdmap_preProcess($command, $table, $id, $value, $pObj) {
// // 			processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $pObj)
				
// 			}
		}
	
		if ($table === 'tx_icsoddatastore_files')
		{
			t3lib_div::debug('files start');
			//creation of solr client
			$solrClient = $this->initSolrClient();
			
			t3lib_div::debug($tabChamps);
			t3lib_div::debug($pObj);
			
			if ($status === 'new')
			{
				t3lib_div::debug('new files start');
				//get old doc
				$oldDoc = $this->getOldDoc($pObj->checkValue_currentRecord[filegroup], $solrClient);
				
				//Convert old doc to input doc
				$doc = $this->solrDocToSolrInputDoc($oldDoc);
				
				// add format to doc
				$doc->addField('files_types_id',$tabChamps[format]);
				
				//update doc
				$addDocResponse = $solrClient->addDocument($doc);
				
				
				if ($addDocResponse->success())
				{
					$solrClient->commit();
					t3lib_div::debug('new files end');
				}
				else
				{
					$addDocResponse = $solrClient->addDocument($doc);
						
					if ($addDocResponse->success())
					{
						$solrClient->commit();
						t3lib_div::debug('new files end');
					}
					else
					{
						$solrClient->rollback();
						t3lib_div::debug('new files end error');
					}
				}
				
			}
			elseif ($status === 'update')
			{
				t3lib_div::debug('update files start');
// 				t3lib_div::debug($pObj->datamap[tx_icsoddatastore_files][$pObj->checkValue_currentRecord[uid]][filegroup]);
				//get old doc
				$oldDoc = $this->getOldDoc($pObj->datamap[tx_icsoddatastore_files][$pObj->checkValue_currentRecord[uid]][filegroup], $solrClient);
				
				//Convert old doc to input doc
				$doc = $this->solrDocToSolrInputDoc($oldDoc);
			
			
				//delete old file format
				t3lib_div::debug($tabChamps[format]);
				t3lib_div::debug($doc->getField('files_types_id'));
				
				if( in_array( $pObj->historyRecords['tx_icsoddatastore_files:' . $pObj->checkValue_currentRecord[uid]][oldRecord][format], $doc->getField('files_types_id')->values ) )
				{
					$formats = $doc->getField('files_types_id')->values;
					$doc->deleteField('files_types_id');
					unset($formats[array_search($pObj->historyRecords['tx_icsoddatastore_files:' . $pObj->checkValue_currentRecord[uid]][oldRecord][format], $formats)]);
					foreach ($formats as $format)
					{
						$doc->addField('files_types_id',$format);
					}
				}
				// add new format to doc
				$doc->addField('files_types_id',$tabChamps[format]);
				
				//update doc
				$addDocResponse = $solrClient->addDocument($doc);
				
				
				if ($addDocResponse->success())
				{
					$solrClient->commit();
				t3lib_div::debug('update files end');
				}
				else
				{
					$addDocResponse = $solrClient->addDocument($doc);
				
					if ($addDocResponse->success())
					{
						$solrClient->commit();
						t3lib_div::debug('update files end');
					}
					else
					{
						$solrClient->rollback();
					t3lib_div::debug('update files end error');
					}
				}
		}

// 			elseif ($status === 'delete')
// 			{
// 				$file = fopen('/tmp/solrdebug', "a+");
// 				fwrite($file, "supprime le fichier\n");
// 			}
		}
		t3lib_div::debug('solr php client hook end');
	}
	
	function initSolrClient()
	{
		t3lib_div::debug('init solr start');
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

		t3lib_div::debug('init solr end');
		return new SolrClient($options);;
	}
	
	function getOldDoc($oldDocId, $solrClient)
	{
		t3lib_div::debug('solr get old doc start');
		
		$query = new SolrQuery();
		$query->setQuery("id:" . $oldDocId);
		$query_response = $solrClient->query($query);
		$query_response->setParseMode(SolrQueryResponse::PARSE_SOLR_DOC);
		$response = $query_response->getResponse();
		t3lib_div::debug($response);
		$oldDocResponse = $response->offsetGet('response')->offsetGet('docs');
		$oldDoc = $oldDocResponse[0];
		
		t3lib_div::debug('solr get old doc end');
		t3lib_div::debug($oldDoc);
		return $oldDoc;
	}
	
	
	function solrDocToSolrInputDoc($solrDoc)
	{
		t3lib_div::debug('solr convert doc start');
		$doc = new SolrInputDocument();
		
		$solrDocFieldNames = $solrDoc->getFieldNames();
		foreach ($solrDocFieldNames as $field)
		{
			foreach ($solrDoc->getField($field)->values as $value)
			{
				$doc->addField($field,$value);
// 				t3lib_div::debug($field . ":" . $value);
			}
		}

		t3lib_div::debug('solr convert doc end');
		t3lib_div::debug($doc);
		return $doc;
	}
}