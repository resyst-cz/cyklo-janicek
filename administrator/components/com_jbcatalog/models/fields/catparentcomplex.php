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

class JFormFieldCatParentComplex extends JFormFieldList
{

	protected $type = 'CatParentComplex';

	protected function getOptions()
	{
		$options = array();
                
                $catid = 0;
                if(isset($_GET['catid'])){
                   $catid = intval($_GET['catid']);
                   $_SESSION['complexitem.catid'] = $catid;
                }elseif(isset($_SESSION['complexitem.catid'])){
                    $catid = $_SESSION['complexitem.catid'];
                }
                if(!$this->value){
                   $this->value = $catid; 
                }
                
                
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.name AS text, a.level')
			->from('#__jbcatalog_complex AS a')
			->join('LEFT', $db->quoteName('#__jbcatalog_complex') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');


                $query->where('a.published != -2');
                
                if($this->name == 'jform[catid]'){
                    $query->where('a.level != 0');
                }
		$query->group('a.id, a.name, a.level, a.lft, a.rgt, a.parent_id')
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

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
