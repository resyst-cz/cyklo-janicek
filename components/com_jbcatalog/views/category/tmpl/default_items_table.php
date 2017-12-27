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
/*plugins*/
JHTML::_('behavior.modal');
if(isset($this->customplugins['script']) && $this->customplugins['script']){
    echo $this->customplugins['script'];
}
/*-*/

echo '<table class="jb_catlist">';
    if(count($this->items)){
        /*plugins
        */
        if(isset($this->customplugins['head']) && $this->customplugins['head']){
            echo $this->customplugins['head'];
        }
       
        /*-*/
        echo '<th>&nbsp;</th>';
        echo '<th>'.JText::_('COM_JBCATALOG_TITLE').'</th>';
        if(isset($this->items[0]->fieldsname) && count($this->items[0]->fieldsname)){
            foreach($this->items[0]->fieldsname as $fld){
                echo "<th>".$fld->name."</th>";
            }
        }
        echo '</tr>';
    }
    for($i=0;$i<count($this->items);$i++){
        
		echo "<tr class='jbitemlist'>";//"<div class='jbitemlist'>";
        /*plugins*/
        if(isset($this->customplugins['checkbox'][$i]) && $this->customplugins['checkbox'][$i]){
            echo $this->customplugins['checkbox'][$i];
        }
        /*-*/
        echo "<td style='padding:10px 0px;width:60px;text'><div class='catimg_div'>";//"<div class='catimg_div'>";
        //echo "<div><a href='index.php?option=com_jbcatalog&view=item&id=".$this->items[$i]->id."'>".$this->items[$i]->title."</a></div>";
		if(isset($this->items[$i]->images) && count($this->items[$i]->images)){
            echo "<a href='".JRoute::_("index.php?option=com_jbcatalog&view=item&catid=".$this->catid."&id=".$this->items[$i]->id)."'><img src='".  JBCatalogImages::getImg($this->items[$i]->images[0], true)."'></a>";
        }
        echo "</div></td>";
        echo "<td>";//"<div class='catinfo_div'>";
        echo "<div><a href='".JRoute::_("index.php?option=com_jbcatalog&view=item&catid=".$this->catid."&id=".$this->items[$i]->id)."'>".$this->items[$i]->title."</a></div>";
        echo "</td>";
        if(isset($this->items[$i]->fields) && count($this->items[$i]->fields)){
            foreach($this->items[$i]->fields as $fld){
                echo "<td>".$fld."</td>";
            }
        }
        
        echo "</tr>";

        

    }
/*plugins*/
    if(isset($this->customplugins['bottom']) && $this->customplugins['bottom']){
        echo $this->customplugins['bottom'];
    }
/*-*/
    echo '</table>';

?>