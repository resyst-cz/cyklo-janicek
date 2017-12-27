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



/**
 * View to edit a banner.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.5
 */
class jbcatalogViewItems extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

    protected $categories;


	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.

        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');
        $this->categories   = $this->get("Cats");


		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
            require_once JPATH_COMPONENT.'/helpers/catalog.php';
         CatalogHelper::addSubmenu('items');
        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');

        JToolbarHelper::title(JText::_('COM_JBCATALOG_ITEMS_TITLE'), 'menumgr.png');

        JToolbarHelper::addNew('item.add');

        JToolbarHelper::editList('item.edit');


        JToolbarHelper::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
        JToolbarHelper::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);


        JToolbarHelper::trash('items.trash');

        JHtmlSidebar::setAction('index.php?option=com_jbcatalog&view=items');

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_published',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
        );

        JHtmlSidebar::addFilter(
            JText::_('COM_JBCATALOG_FILTER_CAT'),
            'filter_catid',
            JHtml::_('select.options', $this->categories, 'value', 'text', $this->state->get('filter.catid'), true)
        );

	}
    protected function getSortFields()
    {
        return array(
            'a.lft' => JText::_('JGRID_HEADING_ORDERING'),
            'a.published' => JText::_('JSTATUS'),
            'a.title' => JText::_('JGLOBAL_TITLE'),
            'a.id' => JText::_('JGRID_HEADING_ID')
        );
    }
}
