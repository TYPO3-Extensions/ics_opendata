<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 author name <author@mail.com>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   66: class tx_icsodappstore_application_datasource
 *   85:     public function __construct()
 *  105:     public function get($queryarray)
 *  142:     public function getApplicationsAll($params)
 *  222:     public function getApplicationsByIds($params)
 *  320:     public function getApplicationsFilter($params)
 *  513:     private function addExtraQueryFields($queryFields)
 *  529:     private function buildQueryOrder($sortOrder)
 *  544:     private function buildQueryLimit($page, $limit, $count_apps)
 *  563:     private function processAppData($application)
 *  609:     private function getAppAuthorName($author = null)
 *  624:     private function getLogoPath($logo)
 *  639:     private function getScreenshotsPath($screenshots)
 *  659:     private function getAppPlatforms ($application = null)
 *  688:     private function getAppCategories($application = null)
 *  716:     private function getAppdatasets($application = null)
 *  744:     private function getPlatformTitle2IDs($platform)
 *  766:     private function getCatName2IDs($category)
 *  791:     private function getDatasetTitle2IDs($dataset)
 *  816:     private function getAuthorName2IDs($author)
 *
 * TOTAL FUNCTIONS: 19
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
require_once(t3lib_extMgm::extPath('ics_od_appstore') . 'opendata/datasource/tx_icsodappstore_sourceconnexion.php');

/**
 * Short description of the command
 *
 * @file class.tx_icsodappstore_application_datasource.php
 * @author    author name <author@mail.com>
 * @package    TYPO3.ics_od_appstore
 */
class tx_icsodappstore_application_datasource
{
	// *************************
	// * User inclusions 0
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...

	// * End user inclusions 0


	private $_datasourceDB = null;

	/**
	 * Constructor
	 *
	 * @return	[type]		...
	 */
	public function __construct()
	{
		$this->_datasourceDB = typo3db_opendatapkg_connect();
		// *************************
		// * User inclusions constructor
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions constructor

	}

	/**
	 * Retrieves datasource's records
	 *
	 * @param	array		$queryarray	The query array to query on database
	 * @return	array		Array of records
	 */
	public function get($queryarray)
	{
		// *************************
		// * User inclusions get 0
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions get 0
		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			$queryarray['fields'],
			$queryarray['fromtable'],
			$queryarray['where'],
			$queryarray['groupby'],
			$queryarray['order'],
			$queryarray['limit'],
			$queryarray['uidIndexField']
		);
		// *************************
		// * User inclusions get 1
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions get 1

		return $rows;
	} // End get

	/**
	 * Retrieves datasource's records
	 *
	 * @param	array		$params	The parameters to query on database
	 * @return	array		Array of records
	 */
	public function getApplicationsAll($params)
	{
		// *************************
		// * User inclusions All 0
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************
		// ... (Add additional operations here) ...

		// * End user inclusions All 0

		$queryarray = array();
		$queryarray['fields'] =
			'`tx_icsodappstore_applications`.`uid` AS `id`, ' .
			'`tx_icsodappstore_applications`.`fe_cruser_id` AS `author`, ' .
			'`tx_icsodappstore_applications`.`title` AS `title`, ' .
			'`tx_icsodappstore_applications`.`description` AS `description`, ' .
			'`tx_icsodappstore_applications`.`release_date` AS `release_date`, ' .
			'`tx_icsodappstore_applications`.`logo` AS `logo`, ' .
			'`tx_icsodappstore_applications`.`screenshot` AS `screenshot`, ' .
			'`tx_icsodappstore_applications`.`link` AS `link`, ' .
			'`tx_icsodappstore_applications`.`update_date` AS `update_date`, ' .
			'`tx_icsodappstore_applications`.`lock_publication` AS `lock_publication`, ' .
			'`tx_icsodappstore_applications`.`publish` AS `publish`, ' .
			'`tx_icsodappstore_applications`.`platforms` AS `platforms`, ' .
			'`tx_icsodappstore_applications`.`tstamp` AS `tstamp`, ' .
			'`tx_icsodappstore_applications`.`crdate` AS `crdate`';
		$queryarray['fromtable'] =
			'`tx_icsodappstore_applications`';
		$queryarray['where'] =
			'1' . t3lib_BEfunc::deleteClause('tx_icsodappstore_applications') . t3lib_BEfunc::BEenableFields('tx_icsodappstore_applications');
		$queryarray['groupby'] =
			'';
		$queryarray['order'] =
			'';
		$queryarray['limit'] =
			'';
		$queryarray['uidIndexField'] =
			'';
		// *************************
		// * User inclusions All 1
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...
		$queryarray['fields'] = $this->addExtraQueryFields($queryarray['fields']);

		// Builds query order
		if ($params['sort'])
			$queryarray['order'] = $this->buildQueryOrder($params['sort']);

		// Builds query limit
		if ($params['page'] && $params['pagelimit'])
		{
			$count_apps = count($this->get($queryarray));
			$queryarray['limit'] = $this->buildQueryLimit($params['page'], $params['pagelimit'], $count_apps);
		}
		$applications = $this->get($queryarray);

		// Process data
		foreach ($applications as $idx=>$application)
		{
			$applications[$idx] = $this->processAppData($application);
		}

		// Last "return $this->get($queryarray);" is never reached
		return array(
			'count' => ($count_apps? $count_apps : count($applications)),
			'records' => $applications,
		);
		// * End user inclusions All 1

		return $this->get($queryarray);
	} // End getApplicationsAll

	/**
	 * Retrieves datasource's records
	 *
	 * @param	array		$params	The parameters to query on database
	 * @return	array		Array of records
	 */
	public function getApplicationsByIds($params)
	{
		// *************************
		// * User inclusions ByIds 0
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// * End user inclusions ByIds 0

		$queryarray = array();
		$queryarray['fields'] =
			'`tx_icsodappstore_applications`.`uid` AS `id`, ' .
			'`tx_icsodappstore_applications`.`fe_cruser_id` AS `author`, ' .
			'`tx_icsodappstore_applications`.`title` AS `title`, ' .
			'`tx_icsodappstore_applications`.`description` AS `description`, ' .
			'`tx_icsodappstore_applications`.`release_date` AS `release_date`, ' .
			'`tx_icsodappstore_applications`.`logo` AS `logo`, ' .
			'`tx_icsodappstore_applications`.`screenshot` AS `screenshot`, ' .
			'`tx_icsodappstore_applications`.`link` AS `link`, ' .
			'`tx_icsodappstore_applications`.`update_date` AS `update_date`, ' .
			'`tx_icsodappstore_applications`.`lock_publication` AS `lock_publication`, ' .
			'`tx_icsodappstore_applications`.`publish` AS `publish`, ' .
			'`tx_icsodappstore_applications`.`platforms` AS `platforms`, ' .
			'`tx_icsodappstore_applications`.`tstamp` AS `tstamp`, ' .
			'`tx_icsodappstore_applications`.`crdate` AS `crdate`';
		$queryarray['fromtable'] =
			'`tx_icsodappstore_applications`';
		$queryarray['where'] =
			'1' . t3lib_BEfunc::deleteClause('tx_icsodappstore_applications') . t3lib_BEfunc::BEenableFields('tx_icsodappstore_applications');
		$queryarray['groupby'] =
			'';
		$queryarray['order'] =
			'';
		$queryarray['limit'] =
			'';
		$queryarray['uidIndexField'] =
			'';
		// *************************
		// * User inclusions ByIds 1
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		$queryarray['fields'] = $this->addExtraQueryFields($queryarray['fields']);

		// Builds query order
		if ($params['sort'])
			$queryarray['order'] = $this->buildQueryOrder($params['sort']);

		// Builds query where
		$where = array();
		if ($params['ids'])
		{
			$ids = t3lib_div::trimExplode(',', $params['ids']);
			foreach ($ids as $idx=>$id)
			{
				$ids[$idx] = $this->_datasourceDB->fullQuoteStr($id, 'tx_icsodappstore_applications');
			}
			$where[] = '`tx_icsodappstore_applications`.`uid` IN (' . implode(',', $ids) . ')';
		}
		$where = implode(' AND ', $where);
		$queryarray['where'] .= ' AND ' . $where;

		// Builds query limit
		if ($params['page'] && $params['pagelimit'])
		{
			$count_apps = count($this->get($queryarray));
			$queryarray['limit'] = $this->buildQueryLimit($params['page'], $params['pagelimit'], $count_apps);
		}

		$applications = $this->get($queryarray);

		// Process data
		foreach ($applications as $idx=>$application)
		{
			$applications[$idx] = $this->processAppData($application);
		}

		// Last "return $this->get($queryarray);" is never reached
		return array(
			'count' => ($count_apps? $count_apps : count($applications)),
			'records' => $applications,
		);
		// * End user inclusions ByIds 1

		return $this->get($queryarray);
	} // End getApplicationsByIds


	/**
	 * Retrieves datasource's records
	 *
	 * @param	array		$params	The parameters to query on database
	 * @return	array		Array of records
	 */
	public function getApplicationsFilter($params)
	{
		// *************************
		// * User inclusions Filter 0
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...
		if (empty($params['title'])
			&& empty($params['updated_start'])
			&& empty($params['updated_end'])
			&& empty($params['released_start'])
			&& empty($params['released_end'])
			&& empty($params['platforms'])
			&& empty($params['categories'])
			&& empty($params['datasets'])
			&& empty($params['author'])) {

			return null;
		}
		// * End user inclusions Filter 0

		$queryarray = array();
		$queryarray['fields'] =
			'`tx_icsodappstore_applications`.`uid` AS `id`, ' .
			'`tx_icsodappstore_applications`.`fe_cruser_id` AS `author`, ' .
			'`tx_icsodappstore_applications`.`title` AS `title`, ' .
			'`tx_icsodappstore_applications`.`description` AS `description`, ' .
			'`tx_icsodappstore_applications`.`release_date` AS `release_date`, ' .
			'`tx_icsodappstore_applications`.`logo` AS `logo`, ' .
			'`tx_icsodappstore_applications`.`screenshot` AS `screenshot`, ' .
			'`tx_icsodappstore_applications`.`link` AS `link`, ' .
			'`tx_icsodappstore_applications`.`update_date` AS `update_date`, ' .
			'`tx_icsodappstore_applications`.`lock_publication` AS `lock_publication`, ' .
			'`tx_icsodappstore_applications`.`publish` AS `publish`, ' .
			'`tx_icsodappstore_applications`.`platforms` AS `platforms`, ' .
			'`tx_icsodappstore_applications`.`tstamp` AS `tstamp`, ' .
			'`tx_icsodappstore_applications`.`crdate` AS `crdate`';
		$queryarray['fromtable'] =
			'`tx_icsodappstore_applications`';
		$queryarray['where'] =
			'1' . t3lib_BEfunc::deleteClause('tx_icsodappstore_applications') . t3lib_BEfunc::BEenableFields('tx_icsodappstore_applications');
		$queryarray['groupby'] =
			'';
		$queryarray['order'] =
			'';
		$queryarray['limit'] =
			'';
		$queryarray['uidIndexField'] =
			'';
		// *************************
		// * User inclusions Filter 1
		// * DO NOT DELETE OR CHANGE THOSE COMMENTS
		// *************************

		// ... (Add additional operations here) ...

		// Builds query where
		$where = array();

			// title
		if (!empty($params['title']))
			$where[] = '`title` LIKE \'' . htmlspecialchars($params['title']) . '%\'';

			// update_date
		if (!empty($params['updated_start']) && !empty($params['updated_end']))
			$where[] = 'update_date BETWEEN UNIX_TIMESTAMP(\'' . $params['updated_start'] . '\') AND UNIX_TIMESTAMP(\'' . $params['updated_end'] . '\')';
		if (!empty($params['updated_start']) && empty($params['updated_end']))
			$where[] = 'update_date >= UNIX_TIMESTAMP(\'' . $params['updated_start'] . '\')';
		if (empty($params['updated_start']) && !empty($params['updated_end']))
			$where[] = 'update_date <= UNIX_TIMESTAMP(\'' . $params['updated_end'] . '\')';

			// release_date
		if (!empty($params['released_start']) && !empty($params['released_end']))
			$where[] = 'release_date BETWEEN UNIX_TIMESTAMP(\'' . $params['released_start'] . '\') AND UNIX_TIMESTAMP(\'' . $params['released_end'] . '\')';
		if (!empty($params['released_start']) && empty($params['released_end']))
			$where[] = 'release_date >= UNIX_TIMESTAMP(\'' . $params['released_start'] . '\')';
		if (empty($params['released_start']) && !empty($params['released_end']))
			$where[] = 'release_date <= UNIX_TIMESTAMP(\'' . $params['released_end'] . '\')';

			// platforms
		if (!empty($params['platforms']))
		{
			$platforms = t3lib_div::trimExplode(',', $params['platforms'], true);
			foreach ($platforms as $index=>$platform)
			{
				if (!is_numeric($platform))
				{
					if($ids = $this->getPlatformTitle2IDs($platform))
						$platforms[$index] = implode(',', $ids);
					else
						$platforms[$index] = '\'' . $platform . '\'';
				}
			}
			$queryarray['fromtable'] .= ' JOIN tx_icsodappstore_apps_platforms_mm ON tx_icsodappstore_applications.uid = tx_icsodappstore_apps_platforms_mm.uid_local
				JOIN tx_icsodappstore_platforms ON tx_icsodappstore_apps_platforms_mm.uid_foreign = tx_icsodappstore_platforms.uid
			';
			$where[] = '`tx_icsodappstore_platforms`.`uid` IN (' . implode(',', $platforms) . ')';
		}

			// categories
		if (t3lib_extMgm::isLoaded('ics_od_categories') && !empty($params['categories']))
		{
			$categories = t3lib_div::trimExplode(',', $params['categories'], true);
			foreach ($categories as $index=>$category)
			{
				if (!is_numeric($category))
				{
					if ($ids = $this->getCatName2IDs($category))
						$categories[$index] = implode(',', $ids);
					else
						$categories[$index] = '\'' . $category . '\'';
				}
			}
			$queryarray['fromtable'] .= ' JOIN tx_icsodcategories_categories_relation_mm ON tx_icsodappstore_applications.uid = tx_icsodcategories_categories_relation_mm.uid_foreign
				JOIN tx_icsodcategories_categories ON tx_icsodcategories_categories_relation_mm.uid_local = tx_icsodcategories_categories.uid
			';
			$where[] = '`tx_icsodcategories_categories`.`uid` IN (' . implode(',', $categories) . ')';
		}

			// datasets
		if (t3lib_extMgm::isLoaded('ics_od_datastore') && !empty($params['datasets']))
		{
			$datasets = t3lib_div::trimExplode(',', $params['datasets'], true);
			foreach ($datasets as $index=>$dataset)
			{
				if (!is_numeric($dataset))
				{
					if ($ids = $this->getDatasetTitle2IDs($dataset))
						$datasets[$index] = implode(',', $ids);
					else
						$datasets[$index] = '\'' . $dataset . '\'';
				}
			}
			$queryarray['fromtable'] .= ' JOIN tx_icsodstoresrel_apps_filegroups_mm ON tx_icsodappstore_applications.uid = tx_icsodstoresrel_apps_filegroups_mm.uid_local
				JOIN tx_icsoddatastore_filegroups ON tx_icsodstoresrel_apps_filegroups_mm.uid_foreign = tx_icsoddatastore_filegroups.uid
			';
			$where[] = '`tx_icsoddatastore_filegroups`.`uid` IN (' . implode(',', $datasets) . ')';
		}

			// author
		if (!empty($params['author']))
		{
			$authors = $params['author'];
			if (!is_numeric($params['author']))
			{
				if ($ids = $this->getAuthorName2IDs($params['author']))
					$authors = implode(',', $ids);
				else
					$authors = '\'' . $params['author'] . '\'';
			}
			$where[] = '`fe_cruser_id` IN (' . $authors . ')';
		}

		$queryarray['where'] .= ' AND ' . implode(' AND ', $where);

		// Builds query limit
		if ($params['page'] && $params['pagelimit'])
		{
			$count_apps = count($this->get($queryarray));
			$queryarray['limit'] = $this->buildQueryLimit($params['page'], $params['pagelimit'], $count_apps);
		}

		$applications = $this->get($queryarray);


		// Builds  query uidIndexField
		$queryarray['uidIndexField'] = 'id';

		// Last "return $this->get($queryarray);" is never reached
		return array(
			'count' => ($count_apps? $count_apps : count($applications)),
			'records' => $applications,
		);

		// * End user inclusions Filter 1

		return $this->get($queryarray);
	} // End getApplicationsFilter

	// *************************
	// * User inclusions other processing
	// * DO NOT DELETE OR CHANGE THOSE COMMENTS
	// *************************

	// ... (Add additional operations here) ...

	/**
	 * Add query fields for the query fields string
	 *
	 * @param	string		$queryFields
	 * @return	string		The query fields with fields added
	 */
	private function addExtraQueryFields($queryFields)
	{
		if (t3lib_extMgm::isLoaded('ics_od_categories'))
			$queryFields .= ', `tx_icsodappstore_applications`.`tx_icsodcategories_categories` AS `categories`';
		if (t3lib_extMgm::isLoaded('ics_od_datastore'))
			$queryFields .= ', `tx_icsodappstore_applications`.`tx_icsodstoresrel_filegroup` AS `datasets`';

		return $queryFields;
	}

	/**
	 * Builds query order
	 *
	 * @param	string		$sortOrder
	 * @return	string
	 */
	private function buildQueryOrder($sortOrder)
	{
		$sortOrder = str_replace('|asc', ' ASC', $sortOrder);
		$sortOrder = str_replace('|desc', ' DESC', $sortOrder);
		return $sortOrder;
	}

	/**
	 * Builds query limit
	 *
	 * @param	int		$page	The number of requested page
	 * @param	int		$limit	The limitation of results
	 * @param	int		$count_apps	Number of results
	 * @return	mixed		The query limit
	 */
	private function buildQueryLimit($page, $limit, $count_apps)
	{
		if (($page * $limit) > $count_apps)
		{
			$page = $count_apps / $limit;
			if (is_float($page))
				$page = intval($page) +1;
		}
		$queryLimit = ($page* $limit) - $limit . ', ' . $limit;
		return $queryLimit;
	}


	/**
	 * Process application 's data
	 *
	 * @param	array		$application	The application to process
	 * @return	mixed		The application processing
	 */
	private function processAppData($application)
	{
		$application['author'] = $this->getAppAuthorName($application['author']);
		$application['logo'] = $this->getLogoPath($application['logo']);
		$application['screenshot'] = $this->getScreenshotsPath($application['screenshot']);
		if ($application['platforms'])
		{
			if ($platforms = $this->getAppPlatforms($application['id']));
				$application['platforms'] = implode(',', array_keys($platforms));
		}
		else {
			$application['platforms'] = null;
		}
		if (t3lib_extMgm::isLoaded('ics_od_categories'))
		{

			if ($application['categories'])
			{
				if ($categories = $this->getAppCategories($application['id']));
					$application['categories'] = implode(',', array_keys($categories));
			}
			else {
				$application['categories'] = null;
			}
		}
		if (t3lib_extMgm::isLoaded('ics_od_datastore'))
		{

			if ($application['datasets'])
			{
				if ($datasets = $this->getAppDatasets($application['id']));
					$application['datasets'] = implode(',', array_keys($datasets));
			}
			else {
				$application['datasets'] = null;
			}
		}
		return $application;
	}

	/**
	 * Retrieve application author's name
	 *
	 * @param	array		$application
	 * @return	mixed
	 */
	private function getAppAuthorName($author = null)
	{
		if (!isset($author))
			return null;

		$row = t3lib_BEfunc::getRecord('fe_users', $author, 'name');
		return $row['name'];
	}

	/**
	 * Retrieve the link to the logo
	 *
	 * @param	string		$logo	The logo
	 * @return	string		The link
	 */
	private function getLogoPath($logo)
	{
		t3lib_div::loadTCA('tx_icsodappstore_applications');
		if (!$GLOBALS['TCA']['tx_icsodappstore_applications']['columns']['logo']['config']['uploadfolder'])
			return $logo;

		return t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '/' . $GLOBALS['TCA']['tx_icsodappstore_applications']['columns']['logo']['config']['uploadfolder'] . '/' . $logo;
	}

	/**
	 * Retrieve links to the screenshots
	 *
	 * @param	string		$screenshots	The screenshots
	 * @return	string
	 */
	private function getScreenshotsPath($screenshots)
	{
		t3lib_div::loadTCA('tx_icsodappstore_applications');
		if (!$GLOBALS['TCA']['tx_icsodappstore_applications']['columns']['screenshot']['config']['uploadfolder'])
			return $screenshots;

		$screenshots = t3lib_div::trimExplode(',', $screenshots, true);
		foreach($screenshots as $idx=>$screenshot)
		{
			$screenshots[$idx] = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '/' . $GLOBALS['TCA']['tx_icsodappstore_applications']['columns']['screenshot']['config']['uploadfolder'] . '/' . $screenshot;
		}
		return implode(',', $screenshots);
	}

	/**
	 * Retrieve application's platforms
	 *
	 * @param	int		$application	Aplication's id
	 * @return	mixed		Platforms
	 */
	private function getAppPlatforms ($application = null)
	{
		if (!isset($application))
			return null;

		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			'tx_icsodappstore_platforms.uid as id, tx_icsodappstore_platforms.title as title',
			'tx_icsodappstore_applications
			JOIN tx_icsodappstore_apps_platforms_mm ON tx_icsodappstore_applications.uid = tx_icsodappstore_apps_platforms_mm.uid_local
			JOIN tx_icsodappstore_platforms ON tx_icsodappstore_apps_platforms_mm.uid_foreign = tx_icsodappstore_platforms.uid',
			'1 AND tx_icsodappstore_applications.uid = ' . $application,
			'',
			'',
			'',
			'id'
		);

		if (empty($rows) || !is_array($rows))
			return null;

		return $rows;
	}

	/**
	 * Retrieve categories
	 *
	 * @param	int		$application	Application's id
	 * @return	mixed		Categories
	 */
	private function getAppCategories($application = null)
	{
		if (!isset($application) || !t3lib_extMgm::isLoaded('ics_od_categories'))
			return null;

		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			'tx_icsodcategories_categories.uid as id, tx_icsodcategories_categories.name as name',
			'tx_icsodappstore_applications
			JOIN tx_icsodcategories_categories_relation_mm ON tx_icsodappstore_applications.uid = tx_icsodcategories_categories_relation_mm.uid_foreign
			JOIN tx_icsodcategories_categories ON tx_icsodcategories_categories_relation_mm.uid_local = tx_icsodcategories_categories.uid',
			'1 AND tx_icsodcategories_categories_relation_mm.tablenames = "tx_icsodappstore_applications" AND tx_icsodappstore_applications.uid = ' . $application,
			'',
			'',
			'',
			'id'
		);
		if (empty($rows) || !is_array($rows))
			return null;

		return $rows;
	}

	/**
	 * Retrieve datasets
	 *
	 * @param	int		$application	Application's id
	 * @return	mixed		datasets
	 */
	private function getAppdatasets($application = null)
	{
		if (!isset($application) || !t3lib_extMgm::isLoaded('ics_od_datastore'))
			return null;

		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			'tx_icsoddatastore_filegroups.uid as id, tx_icsoddatastore_filegroups.title as title',
			'tx_icsodappstore_applications
			JOIN tx_icsodstoresrel_apps_filegroups_mm ON tx_icsodappstore_applications.uid = tx_icsodstoresrel_apps_filegroups_mm.uid_local
			JOIN tx_icsoddatastore_filegroups ON tx_icsodstoresrel_apps_filegroups_mm.uid_foreign = tx_icsoddatastore_filegroups.uid',
			'1 AND tx_icsodappstore_applications.uid = ' . $application,
			'',
			'',
			'',
			'id'
		);
		if (empty($rows) || !is_array($rows))
			return null;

		return $rows;
	}

	/**
	 * Retrieve platform's id
	 *
	 * @param	string		$platform	Platform's title
	 * @return	mixed		Ids for the title
	 */
	private function getPlatformTitle2IDs($platform)
	{
		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			'uid',
			'tx_icsodappstore_platforms',
			'1 AND title LIKE \'' . $platform . '\''
		);
		if (is_array($rows) && !empty($rows))
		{
			$ids = array();
			foreach ($rows as $row)
				$ids[] = $row['uid'];
		}
		return $ids;
	}

	/**
	 * Retrieve category's id
	 *
	 * @param	string		$category	Category's name
	 * @return	mixed		Ids for Name
	 */
	private function getCatName2IDs($category)
	{
		if (!t3lib_extMgm::isLoaded('ics_od_categories'))
			return null;

		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			'uid',
			'tx_icsodcategories_categories',
			'1 AND name LIKE \'' . $category . '\''
		);
		if (is_array($rows) && !empty($rows))
		{
			$ids = array();
			foreach ($rows as $row)
				$ids[] = $row['uid'];
		}
		return $ids;
	}

	/**
	 * Retrieve dataset's id
	 *
	 * @param	string		$dataset	dataset's title
	 * @return	mixed		Ids for title
	 */
	private function getDatasetTitle2IDs($dataset)
	{
		if (!t3lib_extMgm::isLoaded('ics_od_datastore'))
			return null;

		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			'uid',
			'tx_icsoddatastore_filegroups',
			'1 AND title LIKE \'' . $dataset . '\''
		);
		if (is_array($rows) && !empty($rows))
		{
			$ids = array();
			foreach ($rows as $row)
				$ids[] = $row['uid'];
		}
		return $ids;
	}

	/**
	 * Retrieve author's id
	 *
	 * @param	string		$author	author's name
	 * @return	mixed		Ids for name
	 */
	private function getAuthorName2IDs($author)
	{
		$rows = $this->_datasourceDB->exec_SELECTgetRows(
			'uid',
			'fe_users',
			'1 AND name LIKE \'' . $author . '\''
		);
		if (is_array($rows) && !empty($rows))
		{
			$ids = array();
			foreach ($rows as $row)
				$ids[] = $row['uid'];
		}
		return $ids;
	}

	// * End user inclusions other processing


} // End of class tx_icsodappstore_application_datasource
