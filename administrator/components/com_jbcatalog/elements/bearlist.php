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

defined( '_JEXEC' ) or die( 'Restricted access' );



class JFormFieldbearlist extends JFormField
{
    protected $type = 'bearlist';

    public function getInput() 
    {

        $value=$this->value;

        $db =  JFactory::getDBO();
        $db->setQuery($this->element['sql']);   
        $key = ($this->element['key_field']? $this->element['key_field'] : 'value'); 
        $val = ($this->element['value_field'] ?$this->element['value_field'] : 'name');
        $options = array();    

        $rows = $db->loadObjectList(); 


        if($this->element['default'] == ''){
            $options = $rows;
        }else{
            $options[] = JHTML::_('select.option',  "", $this->element['default'], $key, $val );
            if(count($rows)){
                $options = array_merge($options,$rows);
            } 
        }
        

        if($options)
        {    
            return JHTML::_('select.genericlist',$options, $this->name,'', $key, $val, $value, $this->name);                

        }	
    }
                   
 }	   

	?>