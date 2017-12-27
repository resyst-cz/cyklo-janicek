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
class jbcatalogViewItem extends JViewLegacy
{
    protected $form;

    protected $item;

    protected $state;
    
    protected $plugins;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        // Initialiase variables.
        $this->form		= $this->get('Form');
        $this->item		= $this->get('Item');
        $this->state	= $this->get('State');
        $this->plugins	= $this->get('Plugins');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
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
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user		= JFactory::getUser();
        $userId		= $user->get('id');
        $isNew		= ($this->item->id == 0);
        //$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        // Since we don't track these assets at the item level, use the category id.


        JToolbarHelper::title($isNew ? JText::_('COM_JBCATALOG_ITEM_NEW') : JText::_('COM_JBCATALOG_ITEM_EDIT'), '1.png');

        // If not checked out, can save the item.
        //if ( count($user->getAuthorisedCategories('com_jbcatalog', 'core.create')) > 0)
        {
            JToolbarHelper::apply('item.apply');
            JToolbarHelper::save('item.save');

                JToolbarHelper::save2new('item.save2new');

        }

        // If an existing item, can save to a copy.
        if (!$isNew)
        {
            JToolbarHelper::save2copy('item.save2copy');
        }

        if (empty($this->item->id))
        {
            JToolbarHelper::cancel('item.cancel');
        }
        else
        {
            JToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');
        }


	}
}
