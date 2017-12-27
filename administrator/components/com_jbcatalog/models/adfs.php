<?php
/*------------------------------------------------------------------------
# JBCatalog
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2014 JBCatalog.com. All Rights Reserved.
# @license - http://jbcatalog.com/catalog-license/site-articles/license.html GNU/GPL
# Websites: http://www.jbcatalog.com
# Technical Support:  Forum - http://jbcatalog.com/forum/
-------------------------------------------------------------------------*/
defined('_JEXEC') or die;


class JBCatalogModelAdfs extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'published', 'a.published',
				'g.name', 'g.name',
                                'field_type', 'a.field_type'
			);

			$app = JFactory::getApplication();
			$assoc = isset($app->item_associations) ? $app->item_associations : 0;
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}




	/**
	 * Builds an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery    A query object.
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		// Select all fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				$db->quoteName(
					array('a.id', 'a.name', 'a.published', 'a.field_type', 'a.filters', 'a.ordering', 'b.title'),
					array(null, null, null, null, null, null, null)
				)
			)
		);
                $query->select('GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ", ") as adfsgroup');
                $query->from($db->quoteName('#__jbcatalog_adf') . ' AS a');
                $query->leftJoin($db->quoteName('#__jbcatalog_plugins') .' AS b ON b.id = a.field_type AND b.published="1" AND b.type="adf"');
		$query->leftJoin($db->quoteName('#__jbcatalog_adf_ingroups') .' AS i ON a.id = i.adfid');
                $query->leftJoin($db->quoteName('#__jbcatalog_adfgroup') .' AS g ON g.id = i.groupid AND g.published != "-2"');
                $query->group('a.id');
                // Exclude the root category.
		//$query->where('a.id > 1');

		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}
                
                // Filter by search in title, alias or id
                if ($search = trim($this->getState('filter.search')))
                {
                    if (stripos($search, 'id:') === 0)
                    {
                        $query->where('a.id = ' . (int) substr($search, 3));
                    }

                    else
                    {
                        $search = $db->quote('%' . $db->escape($search, true) . '%');
                        $query->where('(' . 'a.name LIKE ' . $search . ')');
                    }
                }

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',(string)$query)).'<hr/>';
		return $query;

	}
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.search', 'filter_search');
        $this->setState('filter.search', $search);

        // List state information.
        parent::populateState('a.ordering', 'asc');
    }

}
