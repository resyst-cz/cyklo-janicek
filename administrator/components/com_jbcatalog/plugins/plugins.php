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

class ParsePlugins {
    private $plugins = array();
  
    public function __construct( $type = '', $id = 0, $published = 1) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('a.id AS value, a.name AS text')
                ->from('#__jbcatalog_plugins AS a');
                
        if($published){
            $query->where('a.published = "1"');
        }        
        if($type){
            $query->where('a.type = "'.$type.'"');
        }
        if($id){
            $query->where('a.id = "'.$id.'"');
        }
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
        
        if(count($options)){
            foreach($options as $plug){
                if(is_file(JPATH_COMPONENT_ADMINISTRATOR."/plugins/{$plug->text}/{$plug->text}.php")){
                    require_once JPATH_COMPONENT_ADMINISTRATOR."/plugins/{$plug->text}/{$plug->text}.php";
                    $classname = ucfirst($plug->text).'Plugin';
                   
                    if(class_exists($classname)){
                        $this->plugins[] = $classname;
                    }
                    
                }
            }
        }
        
        
    }
    
    public function getData($view, $params = array()){
        $arr = array();
        if(count($this->plugins)){
            foreach($this->plugins as $plug){
                $class = new $plug();
                $value = $class->{$view}($params);
               //var_dump($value);
                if(is_array($value)){
                    foreach ($value as $key=>$val){
                        if($key){
                            $arr[$key] = $val;
                        }else{
                            $arr[] = $val;
                        }
                    }
                }elseif($value){
                    return $value;
                }
                
            }
        }
        return $arr;
    }
    
}