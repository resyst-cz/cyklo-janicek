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

class AdfRatingPlugin {
    
    public function getItemEdit($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];

        return ' ';
                
                    
    }
    
    public function getItemFEAdfValue($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        $id = $params["itemid"];
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        if($user->get('id')){
            $db->setQuery(" SELECT value FROM #__jbcatalog_adf_rating WHERE rating_id = ".$adf->id." AND item_id = ".$id." AND usr_id = ".$user->get('id'));

            $value_sel = $db->loadResult();
            $db->setQuery(" SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = ".$adf->id." AND item_id = ".$id);

            $value_avg = $db->loadResult();
            $value = '<span style="float:left; margin-right:10px;">'.sprintf("%01.2f", $value_avg).'</span>';
            $value .= '
            <input name="star2-'.$adf->id.'" value="1" '.($value_sel == '1'?'checked="checked"':"").' type="radio" class="starsik id'.$adf->id.'"/>
            <input name="star2-'.$adf->id.'" value="2" '.($value_sel == '2'?'checked="checked"':"").' type="radio" class="starsik id'.$adf->id.'"/>
            <input name="star2-'.$adf->id.'" value="3" '.($value_sel == '3'?'checked="checked"':"").' type="radio" class="starsik id'.$adf->id.'"/>
            <input name="star2-'.$adf->id.'" value="4" '.($value_sel == '4'?'checked="checked"':"").' type="radio" class="starsik id'.$adf->id.'"/>
            <input name="star2-'.$adf->id.'" value="5" '.($value_sel == '5'?'checked="checked"':"").' type="radio" class="starsik id'.$adf->id.'"/>
                <script>
                //var jsBase = juri::base();?>
                jQuery("input.id'.$adf->id.'").rating({
                callback: function(value, link){ 

                    jQuery.ajax({
                        url: "'.juri::base().'" + "index.php?option=com_jbcatalog&task=rating&tmpl=component&rat="+value+"&id='.$id.'&adfid='.$adf->id.'",

                      });
                }
                });

                </script>
            ';
        }else{
            $db->setQuery(" SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = ".$adf->id." AND item_id = ".$id);

            $value = $db->loadResult();
            if(!$value){
                $value = JText::_('COM_JBCATALOG_FE_NOT_RATED');
            }else{
                $value = sprintf("%01.2f", $value);
            }
        }
        return $value;
                    
    }
    
    public function getAdfFilter($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        
        $selarr = array();
        $selarr[] = JHTML::_('select.option',  0, 0, 'id', 'name' );
        $selarr[] = JHTML::_('select.option',  1, 1, 'id', 'name' );
        $selarr[] = JHTML::_('select.option',  2, 2, 'id', 'name' );
        $selarr[] = JHTML::_('select.option',  3, 3, 'id', 'name' );
        $selarr[] = JHTML::_('select.option',  4, 4, 'id', 'name' );
        $selarr[] = JHTML::_('select.option',  5, 5, 'id', 'name' );
        
        $html = '<div><div>&nbsp;'.JText::_('COM_JBCATALOG_FROM').':&nbsp;</div>';
        $html .= JHTML::_('select.genericlist',   $selarr, 'adf_filter['.$adf->id.'][]', 'class="inputboxsel" size="1"', 'id', 'name', isset($value[0])?$value[0]:0, 'adf_filter_'.$adf->id.'_0' );
        $html .= '</div><div><div>&nbsp;'.JText::_('COM_JBCATALOG_TO').':&nbsp;</div>';
        $html .= JHTML::_('select.genericlist',   $selarr, 'adf_filter['.$adf->id.'][]', 'class="inputboxsel" size="1"', 'id', 'name', isset($value[1])?$value[1]:5, 'adf_filter_'.$adf->id.'_1' );
        $html .= '</div>';                        
        return $html;            
    }
    
    public function getAdfFilterSQL($params = array()){
        
        $tbl_pref = $params["adfid"];
        $value = $params["value"];
        $key = $tbl_pref;
        
        if($value[0] == 0){
            $sq = ' (SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = '.$key.' AND item_id = a.id) is NULL OR ';
        }else{
            $sq = "{$value[0]} <= (SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = {$key} AND item_id = a.id) AND ";
        }
        $filter["sql"] = " AND (  {$sq} {$value[1]} >= (SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = {$key} AND item_id = a.id))";

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
