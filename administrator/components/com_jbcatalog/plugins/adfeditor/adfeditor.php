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

class AdfEditorPlugin {
    
    public function getItemEdit($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        $item_id = $params["itemid"];
        
        $db = JFactory::getDbo();
        
        $db->setQuery(
        "SELECT adf_text FROM #__jbcatalog_adf_values"
        ." WHERE adf_id = ".$adf->id
        ." AND item_id = ".intval($item_id)
        );
        $value = $db->loadResult();


        $editor = JFactory::getEditor();

        return '<textarea name="extraf['.$adf->id.']" id="extraf['.$adf->id.']" cols="60" rows="20" style="width: 550px; height:300px;" class="mceEditors">'.$value.'</textarea>';
        //return $editor->display('extraf['.$adf->id.']',$value,'550', '300', '60', '20');
                      
                    
    }
    public function saveItemEdit($params = array()){
        $db = JFactory::getDbo();
        $adfid = $params["adfid"];
        $val = $params["value"];
        $itemid = $params["itemid"];
        
        $db->setQuery("INSERT INTO #__jbcatalog_adf_values(item_id,adf_id,adf_value,adf_text)"
        ." VALUES({$itemid},{$adfid},'','".addslashes($val)."')");
        $db->execute();        
                    
    }
    
    public function getItemFEAdfValue($params = array()){
        $db = JFactory::getDbo();
        $adf = $params["adf"];
        $id = $params["itemid"];
        
        $db->setQuery(
            "SELECT adf_text FROM #__jbcatalog_adf_values"
            ." WHERE adf_id = ".$adf->id
            ." AND item_id = ".intval($id)
        );
        $value = $db->loadResult();
        return $value;
                    
    }
    
    public function getAdfFilter($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        
        return '<input class="inpfilter" type="text" name="adf_filter['.$adf->id.']" value="'.htmlspecialchars($value).'" />';
                             
                    
    }
    
    public function getAdfFilterSQL($params = array()){
        
        $tbl_pref = $params["adfid"];
        $value = $params["value"];
        $key = $tbl_pref;
        
        $filter['tbl'] = " LEFT JOIN #__jbcatalog_adf_values as v{$tbl_pref} ON a.id=v{$tbl_pref}.item_id"                                               
        ." JOIN #__jbcatalog_adf as e{$tbl_pref} ON e{$tbl_pref}.id=v{$tbl_pref}.adf_id AND e{$tbl_pref}.published='1'";

        $filter['sql'] = " AND (v{$tbl_pref}.adf_text LIKE '%{$value}%' AND v{$tbl_pref}.adf_id = {$key})";
        
        return $filter;
    }
    
    function __call($method, $args) {
        return null;
    }
    public function install(){
        
    }
    
    public function uninstall(){
        
    }
    
    public function getPluginItem(){
        return null;
    }
    public function getPluginItemSave(){
        return null;
    }
    
}
