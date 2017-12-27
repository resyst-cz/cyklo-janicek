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


class jbcatalogViewAdfs extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;


	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.

        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');

        $this->ordering = array();

        // Preprocess the list of items to find ordering divisions.
        if(count($this->items)){
            foreach ($this->items as $item)
            {
                $this->ordering[] = $item->id;
            }
        }
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
         CatalogHelper::addSubmenu('adfs');
        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');

        JToolbarHelper::title(JText::_('COM_JBCATALOG_ADFS_TITLE'), 'menumgr.png');

        JToolbarHelper::addNew('adf.add');

        JToolbarHelper::editList('adf.edit');


            JToolbarHelper::publish('adfs.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('adfs.unpublish', 'JTOOLBAR_UNPUBLISH', true);


            JToolbarHelper::trash('adf.trash');
            JToolbarHelper::divider();
            

	}
    protected function getSortFields()
    {
        return array(

            'a.published' => JText::_('JSTATUS'),
            'a.name' => JText::_('JGLOBAL_TITLE'),
            'a.id' => JText::_('JGRID_HEADING_ID')
        );
    }
}
