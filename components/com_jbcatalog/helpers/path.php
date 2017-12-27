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


class JBCatalogPath
{

    public static function getCatsPath($catid, $link = 0){
        $db = JFactory::getDbo();
        $path = array();
        $db->setQuery(
                "SELECT * FROM #__jbcatalog_category"
                ." WHERE id = ".intval($catid)
                );
        $cat = $db->loadObject();
        if(!empty($cat)){
            $path[] = array('title' => $cat->title, 'link' => $link?JRoute::_("index.php?option=com_jbcatalog&view=category&id=".$cat->id):''); 
            $parent = $cat->parent_id;
            while($parent > 1){
                $db->setQuery(
                "SELECT * FROM #__jbcatalog_category"
                ." WHERE id = ".intval($parent)
                );
                $parent_obj = $db->loadObject();
                //if($parent_obj->parent_id > 1){
                    $path[] = array('title' => $parent_obj->title, 'link' => JRoute::_("index.php?option=com_jbcatalog&view=category&id=".$parent_obj->id)); 
                //}
                $parent = $parent_obj->parent_id;
            }
            
        }
        return $path;
    }

    
}
 
