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

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');


class JFormFieldAdfscomplex extends JFormFieldList
{

	protected $type = 'Adfscomplex';

	protected function getOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
                
                $db->setQuery("SELECT id FROM #__jbcatalog_plugins WHERE name = 'adfcomplex' AND published = '1'");
                if(!$db->loadResult()){
                    return null;
                }
		$query = $db->getQuery(true)
			->select('a.id AS value, a.name AS text')
			->from('#__jbcatalog_complex AS a')
                        ->where('a.level = "1"')
                        ->where("published != '-2'")
                        ->order('a.lft');


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


		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
