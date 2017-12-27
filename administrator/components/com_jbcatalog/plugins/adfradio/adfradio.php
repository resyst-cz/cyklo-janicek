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

class AdfRadioPlugin {
    
    public function getItemEdit($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];

        return JHTML::_('select.booleanlist',  'extraf['.$adf->id.']', 'class="btn-group"', $value, JText::_('JYES'), JText::_('JNO'),'extrf'.$adf->id );
                       
                    
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
        
        $value = $params["value"];
        
        $value = $value?JText::_("Yes"):JText::_("No");
        
        return $value;
                    
    }
    public function getAdfFilter($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        
        return '<input type="checkbox" name="adf_filter['.$adf->id.']" value="1" '.($value?"checked":"").' />';
                         
                    
    }
    
    public function getAdfFilterSQL($params = array()){
        
        $tbl_pref = $params["adfid"];
        $value = $params["value"];
        $key = $tbl_pref;
        
        $filter['tbl'] = " LEFT JOIN #__jbcatalog_adf_values as v{$tbl_pref} ON a.id=v{$tbl_pref}.item_id"
        ." JOIN #__jbcatalog_adf as e{$tbl_pref} ON e{$tbl_pref}.id=v{$tbl_pref}.adf_id AND e{$tbl_pref}.published='1'";

        $filter['sql'] = " AND (v{$tbl_pref}.adf_value = '{$value}' AND v{$tbl_pref}.adf_id = {$key})";
        
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
