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
class tx_icsoddatastore_processCmdmap_deleteAction {

	function processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted, $pObj)
	{

		global $TYPO3_DB;
		$solrClient = SolrTools::initSolrClient();
		
		if ($table === 'tx_icsoddatastore_filegroups')
		{
			$oldDoc = SolrTools::getOldDoc($id, $solrClient);
			
			if(isset($oldDoc) && is_object($oldDoc))
			{
				//Convert old doc to input doc
				$doc = SolrTools::solrDocToSolrInputDoc($oldDoc);
			
				if($doc->fieldExists('deleted'))
				{
					$doc->deleteField('deleted');
				}
				$doc->addField('deleted', '1');
					
				$updateResponse = $solrClient->addDocument($doc);
				
				try 
				{
					if ($updateResponse->success())
					{
						$solrClient->commit();
					}
					else
					{
						$updateResponse = $solrClient->addDocument($doc);
						if ($updateResponse->success())
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
					t3lib_div::sysLog($e->getMessage(), 'ics_od_datastore');
				}
			}
		}
		
		if ($table === 'tx_icsoddatastore_files')
		{
			//get old doc id
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid_foreign',         // SELECT ...
					'tx_icsoddatastore_files_filegroup_mm',    // FROM ...
					'uid_local="'. $id .'"'	// WHERE
			);
			$row = $TYPO3_DB->sql_fetch_assoc($res);

			//get old doc
			$oldDoc = SolrTools::getOldDoc($row['uid_foreign'], $solrClient);
			
			if(isset($oldDoc) && is_object($oldDoc))
			{	
			//Convert old doc to input doc
			$doc = SolrTools::solrDocToSolrInputDoc($oldDoc);
				
				//delete selected file format
				$formats = $doc->getField('files_types_id')->values;
				$doc->deleteField('files_types_id');
				unset($formats[array_search($recordToDelete[format], $formats)]);
				foreach ($formats as $format)
				{
					$doc->addField('files_types_id',$format);
				}
				
				//update doc
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
					t3lib_div::sysLog($e->getMessage(), 'ics_od_datastore');
				}
			}	
		}
	}
}