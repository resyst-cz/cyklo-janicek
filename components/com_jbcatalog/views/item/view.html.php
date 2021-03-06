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


class JBCatalogViewItem extends JViewLegacy
{
	protected $state = null;

	protected $item = null;

        protected $data = null;
	/**
	 * Display the view
	 *
	 * @return  mixed  False on error, null otherwise.
	 */
	public function display($tpl = null)
	{
		
                $items		= $this->get('Item');
                $this->pdata		= $this->get('ItemPosition');

                $this->isAdmin = $this->get('IsAdmin');
                $this->state = $this->get('State');
                $this->params = $this->state->params;
		$this->item = &$items;
            
		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;
                require_once JPATH_COMPONENT.'/helpers/path.php';
               
                // Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->def('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_CONTACT_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
                
                if ($this->item->title)
                {
                        $title = $this->item->title;
                }
                
		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
                

                $pathway = $app->getPathway();
                $path = JBCatalogPath::getCatsPath($this->state->catid, 1);
                $path_item[] = array('title' => $this->item->title, 'link' => '');
                $path = array_merge($path_item, $path);
                $path = array_reverse($path);
                foreach ($path as $item)
                {
                        $pathway->addItem($item['title'], $item['link']);
                }
                
	}
}
