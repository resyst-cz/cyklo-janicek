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


class CatalogHelper
{
	/**
	 * Defines the valid request variables for the reverse lookup.
	 */
	protected static $_filter = array('option', 'view', 'layout');

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string    The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
                JHtmlSidebar::addEntry(
			JText::_('COM_JBCATALOG_SUB_CATEGORIES'),
			'index.php?option=com_jbcatalog&view=categories',
			$vName == 'categories'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_JBCATALOG_SUB_ITEMS'),
			'index.php?option=com_jbcatalog&view=items',
			$vName == 'items'
		);
                JHtmlSidebar::addEntry(
			JText::_('COM_JBCATALOG_SUB_ADFGROUPS'),
			'index.php?option=com_jbcatalog&view=adfgroups',
			$vName == 'adfgroups'
		);
                JHtmlSidebar::addEntry(
			JText::_('COM_JBCATALOG_SUB_ADFS'),
			'index.php?option=com_jbcatalog&view=adfs',
			$vName == 'adfs'
		);
                JHtmlSidebar::addEntry(
			JText::_('COM_JBCATALOG_SUB_PLUGINS'),
			'index.php?option=com_jbcatalog&view=plugins',
			$vName == 'plugins'
		);
                
	}

	
}
