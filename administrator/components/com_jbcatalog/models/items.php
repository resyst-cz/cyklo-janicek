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

class JBCatalogModelItems extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'menutype', 'a.menutype',
				'title', 'a.title',
				'alias', 'a.alias',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'level', 'a.level',
				'path', 'a.path',
				'client_id', 'a.client_id',
				'home', 'a.home',
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
					array('a.id', 'a.title', 'a.alias','a.published','a.descr','a.ordering'),
					array(null, null, null, null, null, null)
				)
			)
		);
                $query->from($db->quoteName('#__jbcatalog_items') . ' AS a');
                $query->leftJoin($db->quoteName('#__jbcatalog_items_cat') .' AS b ON b.item_id = a.id');
                $query->group("a.id");
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
                
                $catid = $this->getState('filter.catid');
		if (is_numeric($catid))
		{
			$query->where('b.cat_id = ' . (int) $catid);
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
                $query->where('(' . 'a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
            }
        }



		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',(string)$query)).'<hr/>';
		return $query;

	}
    protected function populateState($ordering = null, $direction = null)
    {
        $published = $this->getUserStateFromRequest($this->context . '.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $search = $this->getUserStateFromRequest($this->context . '.search', 'filter_search');
        $this->setState('filter.search', $search);

        $catid = $this->getUserStateFromRequest($this->context . '.catid', 'filter_catid');
        $this->setState('filter.catid', $catid);

        // List state information.
        parent::populateState('a.ordering', 'asc');
    }

    public function getCats(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.id AS value, a.title AS text, a.level')
            ->from('#__jbcatalog_category AS a')
            ->join('LEFT', $db->quoteName('#__jbcatalog_category') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');



        // Prevent parenting to children of this item.
       /* if ($id = $this->form->getValue('id'))
        {
            $query->join('LEFT', $db->quoteName('#__jbcatalog_category') . ' AS p ON p.id = ' . (int) $id)
                ->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
        }*/

        $query->where('a.published != -2 AND a.parent_id != 0')
            ->group('a.id, a.title, a.level, a.lft, a.rgt, a.parent_id, a.published')
            ->order('a.lft ASC');

        // Get the options.
        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        // Pad the option text with spaces using depth level as a multiplier.
        for ($i = 0, $n = count($options); $i < $n; $i++)
        {
            $options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
        }

        return $options;
    }

}
