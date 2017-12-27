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


class jbcatalogViewAdfgroup extends JViewLegacy
{
    protected $form;

    protected $item;

    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        // Initialiase variables.
        $this->form		= $this->get('Form');
        $this->item		= $this->get('Item');
        $this->state	= $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
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
         CatalogHelper::addSubmenu('adfgroups');
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user		= JFactory::getUser();
        $userId		= $user->get('id');
        $isNew		= ($this->item->id == 0);
        //$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        // Since we don't track these assets at the item level, use the category id.


        JToolbarHelper::title($isNew ? JText::_('COM_JBCATALOG_ADFGROUP_NEW') : JText::_('COM_JBCATALOG_ADFGROUP_EDIT'), '1.png');

        // If not checked out, can save the item.
        //if (!$checkedOut || count($user->getAuthorisedCategories('com_jbcatalog', 'core.create')) > 0)
        {
            JToolbarHelper::apply('adfgroup.apply');
            JToolbarHelper::save('adfgroup.save');

                JToolbarHelper::save2new('adfgroup.save2new');

        }

        // If an existing item, can save to a copy.
        if (!$isNew)
        {
            JToolbarHelper::save2copy('adfgroup.save2copy');
        }

        if (empty($this->item->id))
        {
            JToolbarHelper::cancel('adfgroup.cancel');
        }
        else
        {
            JToolbarHelper::cancel('adfgroup.cancel', 'JTOOLBAR_CLOSE');
        }


	}
}
