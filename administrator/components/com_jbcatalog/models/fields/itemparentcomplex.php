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

class JFormFieldItemParentComplex extends JFormFieldList
{

	protected $type = 'ItemParentComplex';

	protected function getOptions()
	{
		$options = array();
                $db = JFactory::getDbo();
                $catid = 0;
                if(isset($_SESSION['complexitem.catid'])){
                    $catid = $_SESSION['complexitem.catid'];
                    $db->setQuery(
                        'SELECT level FROM #__jbcatalog_complex WHERE id = '.$catid
                        );
                    $level = $db->loadResult();
                }
                if($this->value){
                    $db->setQuery(
                        'SELECT catid FROM #__jbcatalog_complex_item WHERE id = '.$this->value
                        );
                    $catid = $db->loadResult();
                }
		
		
		if(!$catid){
                    return JText::_("COM_JBCATALOG_NO_PARENT");
                }
            
                $db->setQuery(
                        'SELECT level FROM #__jbcatalog_complex WHERE id = '.$catid
                        );
                $level = $db->loadResult();

		if($level > 1){
                    if($this->value){
                        $db->setQuery(
                        'SELECT title as text, id as value FROM #__jbcatalog_complex_item WHERE catid = '.$catid
                            .' ORDER BY ordering'
                        );
                        
                    }else{
                        $db->setQuery(
                        'SELECT i.title as text, i.id as value'
                            .' FROM #__jbcatalog_complex_item as i'
                            .' JOIN #__jbcatalog_complex as c ON i.catid = c.parent_id AND c.id ='.$catid
                            .' ORDER BY i.ordering'
                        );
                    } 
                }else{
                    if($this->value){
                        $db->setQuery(
                        'SELECT title as text, id as value FROM #__jbcatalog_complex_item WHERE catid = '.$catid
                            .' ORDER BY ordering'
                        );
                    }else{
                        return JText::_("COM_JBCATALOG_NO_PARENT");
                    }
                }

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
