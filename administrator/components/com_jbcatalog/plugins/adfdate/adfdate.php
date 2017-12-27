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

class AdfDatePlugin {
    
    function __call($method, $args) {
        return null;
    }
    
    
    public function getItemEdit($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        
        $html  = JHtml::_('calendar', $value, 'extraf['.$adf->id.']', 'extraf_'.$adf->id, "Y-m-d", 'readonly = "readonly"');
        $html .= '<input type="hidden" name="caldr[]" value="'.$adf->id.'" />';
                        
        return $html;            
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
        
        $value = htmlspecialchars($value);
        
        return $value;
                    
    }
    public function getAdfFilter($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        $html = '<div>&nbsp;'.JText::_('COM_JBCATALOG_FROM').':&nbsp;</div>';
        $html .= JHtml::_('calendar', isset($value[0])?$value[0]:'', 'adf_filter['.$adf->id.'][]', 'extraf_'.$adf->id, "%Y-%m-%d", ' class="inpfilter" readonly = "readonly"');
        $html .= '<div>&nbsp;'.JText::_('COM_JBCATALOG_TO').':&nbsp;</div>';
        $html .= JHtml::_('calendar', isset($value[1])?$value[1]:'', 'adf_filter['.$adf->id.'][]', 'extraf_'.$adf->id.'_1', "%Y-%m-%d", ' class="inpfilter" readonly = "readonly"');
        
        return $html;                         
    }
    
    public function getAdfFilterSQL($params = array()){
        
        $tbl_pref = $params["adfid"];
        $value = $params["value"];
        $key = $tbl_pref;
        
        $filter = array();
        
        if($value[0] || $value[1]){

                $filter['tbl'] = " LEFT JOIN #__jbcatalog_adf_values as v{$tbl_pref} ON a.id=v{$tbl_pref}.item_id"
                           ." JOIN #__jbcatalog_adf as e{$tbl_pref} ON e{$tbl_pref}.id=v{$tbl_pref}.adf_id AND e{$tbl_pref}.published='1'";

                $filter['sql'] = " AND ( ".($value[0]?"v{$tbl_pref}.adf_value >= '".$value[0]." 00:00:00' AND":'')." ".($value[1]?" v{$tbl_pref}.adf_value <= '".$value[1]." 00:00:00' AND":'')." v{$tbl_pref}.adf_id = {$key})";

        }     
        
        return $filter;
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
