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

require_once('solrTools.php');

/**
 * Hook of the core for the 'ics_od_datastore' extension.
 *
 * @author	Giraudeau Étienne <contact@smile.net>
 * @package	TYPO3
 * @subpackage	tx_icsoddatastore
 */
class tx_icsoddatastore_processDatamap_afterDatabaseOperations {
	
	function processDatamap_afterDatabaseOperations($status, $table, $id, $tabChamps, $pObj) {
		
		global $TYPO3_DB;
	
		$createFieldLabelArray = array('hidden', 'keywords', 'spatial_cover', 'language', 
				'quality', 'granularity', 'linked_references', 'taxonomy', 'illustration',
				'html_from_csv_display', 'has_dynamic_display', 'param_dynamic_display', 'title', 'description', 
				'technical_data', 'licence', 'time_period', 'update_frequency');
		$updateFieldLabelArray = array('hidden', 'deleted', 'keywords', 'spatial_cover', 'language', 
				'quality', 'granularity', 'linked_references', 'taxonomy', 'illustration',
				'html_from_csv_display', 'has_dynamic_display', 'param_dynamic_display', 'title', 'description', 
				'technical_data', 'licence', 'time_period', 'update_frequency');
		
		if ($table === 'tx_icsoddatastore_filegroups')
		{
			//creation of solr client
			$solrClient = SolrTools::initSolrClient();
			
			if ($status === 'new')
			{
				$doc = new SolrInputDocument();
				
				foreach ($createFieldLabelArray as $fieldLabel)
				{
					$doc->addField($fieldLabel, $tabChamps[$fieldLabel]);
				}
				
				$doc->addField('deleted', '0');
				
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
				
				try 
				{
					if ($addDocResponse->success())
					{
						$solrClient->commit();
					}
					else
					{
						$addDocResponse = $solrClient->addDocument($doc);
						
						if ($addDocResponse->success())
						{
							$solrClient->commit();
						}
						else
						{
							$solrClient->rollback();
						}
					}
				}
				catch (SolrException $e)
				{
					$e->getMessage();
				}
				
			}
			elseif ($status === 'update')
			{
				//get old doc
				$oldDoc = SolrTools::getOldDoc($pObj->checkValue_currentRecord[uid], $solrClient);
				
				//Convert old doc to input doc
				$doc = SolrTools::solrDocToSolrInputDoc($oldDoc);

				// update fields
				foreach ($updateFieldLabelArray as $fieldLabel)
				{
					if (isset($tabChamps[$fieldLabel]))
					{
						$doc->deleteField($fieldLabel);
						$doc->addField($fieldLabel, $tabChamps[$fieldLabel]);
					}
				}
				
				if (isset($tabChamps['release_date']))
				{
					if($doc->fieldExists('release_date'))
					{
						$doc->deleteField('release_date');
					}
					$doc->addField('release_date', date("Y-m-d",$tabChamps[release_date]) . 'T' . date("H:i:s", $tabChamps[release_date]) . 'Z');
				}

				if (isset($tabChamps['update_date']))
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
					foreach (explode(',', $pObj->datamap[tx_icsoddatastore_filegroups][$pObj->checkValue_currentRecord[uid]][tx_icsodcategories_categories]) as $categorieId)
					{
						if ($categorieId !== '')
						{
							$whereclause .= ' OR uid=' . $categorieId;
						}
					}
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
				}
				else
				{
					$addDocResponse = $solrClient->addDocument($doc);
				
					if ($addDocResponse->success())
					{
						$solrClient->commit();
					}
					else
					{
						$solrClient->rollback();
					}
				}
			}
		}
	
		if ($table === 'tx_icsoddatastore_files')
		{
			//creation of solr client
			$solrClient = SolrTools::initSolrClient();
			
			if ($status === 'new')
			{
				//get old doc
				$oldDoc = SolrTools::getOldDoc($pObj->checkValue_currentRecord[filegroup], $solrClient);
				
				//Convert old doc to input doc
				$doc = SolrTools::solrDocToSolrInputDoc($oldDoc);
				
				// add format to doc
				$doc->addField('files_types_id',$tabChamps[format]);
				
				//update doc
				$addDocResponse = $solrClient->addDocument($doc);
				
				
				if ($addDocResponse->success())
				{
					$solrClient->commit();
				}
				else
				{
					$addDocResponse = $solrClient->addDocument($doc);
						
					if ($addDocResponse->success())
					{
						$solrClient->commit();
					}
					else
					{
						$solrClient->rollback();
					}
				}
				
			}
			elseif ($status === 'update' && isset($tabChamps[format]) && $tabChamps[format] !== '')
			{
				//get old doc
				$oldDoc = SolrTools::getOldDoc($pObj->datamap[tx_icsoddatastore_files][$pObj->checkValue_currentRecord[uid]][filegroup], $solrClient);
				
				//Convert old doc to input doc
				$doc = SolrTools::solrDocToSolrInputDoc($oldDoc);
			
			
				//delete old file format
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
				}
				else
				{
					$addDocResponse = $solrClient->addDocument($doc);
				
					if ($addDocResponse->success())
					{
						$solrClient->commit();
					}
					else
					{
						$solrClient->rollback();
					}
				}
			}
		}
	}
}