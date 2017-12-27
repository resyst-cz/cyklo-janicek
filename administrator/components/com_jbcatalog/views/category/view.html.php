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



class jbcatalogViewCategory extends JViewLegacy
{
    protected $form;

    protected $item;

    protected $state;
    
    protected $grouplist;
    
    protected $adflist;
    
    protected $grval;
    
    protected $adfval;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        // Initialiase variables.
        $this->form		= $this->get('Form');
        $this->item		= $this->get('Item');
        $this->state	= $this->get('State');
        $this->grouplist = $this->get('GroupList');
        $this->adflist = $this->get('AdfList');
        $this->grval = $this->get('GroupVals');
        $this->adfval = $this->get('AdfVals');


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
         CatalogHelper::addSubmenu('categories');
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user		= JFactory::getUser();
        $userId		= $user->get('id');
        $isNew		= ($this->item->id == 0);
        //$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        // Since we don't track these assets at the item level, use the category id.


        JToolbarHelper::title($isNew ? JText::_('COM_JBCATALOG_CATEGORY_NEW') : JText::_('COM_JBCATALOG_CATEGORY_EDIT'), '1.png');

        // If not checked out, can save the item.
       // if (count($user->getAuthorisedCategories('com_jbcatalog', 'core.create')) > 0)
        {
            JToolbarHelper::apply('category.apply');
            JToolbarHelper::save('category.save');

                JToolbarHelper::save2new('category.save2new');

        }

        // If an existing item, can save to a copy.
        if (!$isNew)
        {
            JToolbarHelper::save2copy('category.save2copy');
        }

        if (empty($this->item->id))
        {
            JToolbarHelper::cancel('category.cancel');
        }
        else
        {
            JToolbarHelper::cancel('category.cancel', 'JTOOLBAR_CLOSE');
        }


	}
}
