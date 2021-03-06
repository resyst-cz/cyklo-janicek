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

class AdfLinkPlugin {
    
    public function getItemEdit($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        
        
        return '<input type="text" name="extraf['.$adf->id.']" value="'.htmlspecialchars($value).'" />';
                
                    
    }
    public function saveItemEdit($params = array()){
        $db = JFactory::getDbo();
        $adfid = $params["adfid"];
        $val = $params["value"];
        $itemid = $params["itemid"];
        
        $db->setQuery("INSERT INTO #__jbcatalog_adf_values(item_id,adf_id,adf_value,adf_text)"
        ." VALUES({$itemid},{$adfid},'".addslashes($val)."','')");
        $db->execute();        
                    
    }
    
    public function getItemFEAdfValue($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        
        $value_link = htmlspecialchars($value);
        if($value && substr($value, 0, 4) != 'http'){
            $value_link = 'http://'.$value_link;
        }
        if($value){
            $value = '<a href="'.$value_link.'" target="_blank">'.$value.'</a>';
        }
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

        $filter['sql'] = " AND (v{$tbl_pref}.adf_value LIKE '%{$value}%' AND v{$tbl_pref}.adf_id = {$key})";
        
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
