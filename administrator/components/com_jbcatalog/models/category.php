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


class JBCatalogModelCategory extends JModelAdmin
{
    protected $text_prefix = 'COM_JBCATALOG_CATEGORY';
    
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_jbcatalog.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }

        // Determine correct permissions to check.
        if ($this->getState('category.id'))
        {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');
        }
        else
        {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        // Modify the form based on access controls.
        if (!$this->canEditState((object) $data))
        {
            // Disable fields for display.
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');
            $form->setFieldAttribute('sticky', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');
            $form->setFieldAttribute('sticky', 'filter', 'unset');
        }

        return $form;
    }
    public function getTable($type = 'Category', $prefix = 'JBCatalogTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    public function save($data)
    {

        $pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('category.id');
        $isNew = true;
        $table = $this->getTable();

        // Load the row if saving an existing item.
        if ($pk > 0)
        {
            $table->load($pk);
            $isNew = false;
        }
        if (!$isNew)
        {
            if ($table->parent_id == $data['parent_id'])
            {


                $table->setLocation($data['parent_id'], 'last-child');


            }
            // Set the new parent id if parent id not matched and put in last position
            else
            {
                $table->setLocation($data['parent_id'], 'last-child');
            }
        }
        // We have a new item, so it is not a change.
        elseif ($isNew)
        {
            $table->setLocation($data['parent_id'], 'last-child');
        }
        // The menu type has changed so we need to just put this at the bottom
        // of the root level.
        else
        {
            $table->setLocation(1, 'last-child');
        }

        // Bind the data.
        if (!$table->bind($data))
        {
            $this->setError($table->getError());
            return false;
        }

        // Alter the title & alias for save as copy.  Also, unset the home record.
        if (!$isNew && $data['id'] == 0)
        {
            list($title, $alias) = $this->generateNewTitle($table->parent_id, $table->alias, $table->title);
            $table->title = $title;
            $table->alias = $alias;
            $table->published = 0;

        }

        // Check the data.
        if (!$table->check())
        {
            $this->setError($table->getError());
            return false;
        }

        // Store the data.
        if (!$table->store())
        {
            $this->setError($table->getError());
            return false;
        }

        // Rebuild the tree path.
        if (!$table->rebuildPath($table->id))
        {
            $this->setError($table->getError());
            return false;
        }

        $this->setState('category.id', $table->id);
        
        
        //thumbnails
        $this->createThumbnail($table->image, $table->id);
        
        //
        $db = JFactory::getDbo();
        
        
        $db->setQuery("DELETE FROM #__jbcatalog_category_adfs WHERE cat_id={$table->id}");
        $db->execute();
        if(isset($_POST["jbgrfields"]) && $_POST["jbgrfields"]){
            $i=0;
            foreach($_POST["jbgrfields"] as $asdf ){
                $listview = isset($_POST['showlist'][$asdf])?1:0;
                $db->setQuery("INSERT IGNORE INTO #__jbcatalog_category_adfs(adfs_id,cat_id,group_id,listview,filtered) VALUES({$asdf},{$table->id},".intval($_POST['jbgroups_adf'][$i]).",{$listview}, ".intval($_POST['catfilter'][$i]).")");
                $db->execute();
                $i++;
            }

        }
        
        $db->setQuery("DELETE FROM #__jbcatalog_category_adfs_group WHERE cat_id={$table->id}");
        $db->execute();
        if(isset($_POST["jbgroups"]) && $_POST["jbgroups"]){
            $i=0;
            foreach($_POST["jbgroups"] as $asdf ){
                $db->setQuery("INSERT INTO #__jbcatalog_category_adfs_group(cat_id,group_id) VALUES({$table->id}, {$asdf})");
                $db->execute();
            }

        }
        
        

        // Load associated menu items
        $app = JFactory::getApplication();
        $assoc = isset($app->item_associations) ? $app->item_associations : 0;
        if ($assoc)
        {
            // Adding self to the association
            $associations = $data['associations'];
            foreach ($associations as $tag => $id)
            {
                if (empty($id))
                {
                    unset($associations[$tag]);
                }
            }

            // Detecting all item menus
            $all_language = $table->language == '*';
            if ($all_language && !empty($associations))
            {
                JError::raiseNotice(403, JText::_('COM_MENUS_ERROR_ALL_LANGUAGE_ASSOCIATED'));
            }

            $associations[$table->language] = $table->id;

            // Deleting old association for these items
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->delete('#__associations')
                ->where('context=' . $db->quote('com_jbcatalog.category'))
                ->where('id IN (' . implode(',', $associations) . ')');
            $db->setQuery($query);

            try
            {
                $db->execute();
            }
            catch (RuntimeException $e)
            {
                $this->setError($e->getMessage());
                return false;
            }

            if (!$all_language && count($associations) > 1)
            {
                // Adding new association for these items
                $key = md5(json_encode($associations));
                $query->clear()
                    ->insert('#__associations');
                foreach ($associations as $tag => $id)
                {
                    $query->values($id . ',' . $db->quote('com_jbcatalog.category') . ',' . $db->quote($key));
                }
                $db->setQuery($query);

                try
                {
                    $db->execute();
                }
                catch (RuntimeException $e)
                {
                    $this->setError($e->getMessage());
                    return false;
                }
            }
        }

        // Clean the cache
        $this->cleanCache();


        return true;
    }
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            // Convert the metadata field to an array.
            //$registry = new JRegistry;
           // $registry->loadString($item->metadata);
            //$item->metadata = $registry->toArray();
        }

        // Load associated contact items
        $app = JFactory::getApplication();
        $assoc = isset($app->item_associations) ? $app->item_associations : 0;

        if ($assoc)
        {
            $item->associations = array();

            if ($item->id != null)
            {
                $associations = JLanguageAssociations::getAssociations('com_jbcatalog', '#__jbcatalog_category', 'com_jbcatalog.category', $item->id);

                foreach ($associations as $tag => $association)
                {
                    $item->associations[$tag] = $association->id;
                }
            }
        }
        $db = $this->getDbo();
        $query = "SELECT b.id"
            ." FROM #__jbcatalog_category_adfs as a"
            ." JOIN #__jbcatalog_adfgroup as b"
            ." ON a.adfs_id = b.id"
            ." WHERE a.cat_id = ".intval($item->id)
            ." ORDER BY b.name";

        $db->setQuery($query);
        $item->adfsgroup = $db->loadColumn();
        
        
        return $item;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_jbcatalog.edit.category.data', array());

        if (empty($data))
        {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('category.id') == 0)
            {
                $app = JFactory::getApplication();
               // $data->set('catid', $app->input->get('catid', $app->getUserState('com_jbcatalog.category.filter.category_id'), 'int'));
            }
        }

        $this->preprocessData('com_jbcatalog.category', $data);

        return $data;
    }
    public function saveorder($idArray = null, $lft_array = null)
    {
        // Get an instance of the table object.
        $table = $this->getTable();

        if (!$table->saveorder($idArray, $lft_array))
        {
            $this->setError($table->getError());
            return false;
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }

    public function publish(&$pks, $value = 1)
    {
        $table = $this->getTable();
        $pks = (array) $pks;


        // Clean the cache
        $this->cleanCache();

        // Ensure that previous checks doesn't empty the array
        if (empty($pks))
        {
            return true;
        }

        return parent::publish($pks, $value);
    }

    function trash($cid){
        $this->publish($cid, -2);
        $db = JFactory::getDbo();
        if(count($cid)){
            foreach ($cid as $id){
                $db->setQuery(
                "SELECT item_id FROM #__jbcatalog_items_cat"
                ." WHERE cat_id = ".intval($id)
                );
                $ids = $db->loadColumn();
                
                if(count($ids)){
                    foreach($ids as $id){
                        $db->setQuery(
                        "SELECT COUNT(c.id) FROM #__jbcatalog_items_cat as i"
                        ." JOIN #__jbcatalog_category as c ON i.cat_id = c.id"        
                        ." WHERE i.item_id = ".intval($id)
                        ." AND c.published = 1"        
                        );
                        $count = $db->loadResult();
                        if(!$count){
                            $db->setQuery(
                            "UPDATE #__jbcatalog_items"
                            ." SET published = -2"        
                            ." WHERE id = ".intval($id)
                            );
                            $db->execute();
                        }
                    }
                    
                }

            }
        }
    }
    
    
    function createThumbnail($filename, $id) {  

        
        if(!$filename || !is_file(JPATH_ROOT.DIRECTORY_SEPARATOR.$filename)){
            return false;
        }
        $final_width_of_image = 150; 
        $path_to_thumbs_directory = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jbcatalog'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'jsupload'.DIRECTORY_SEPARATOR.'server'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'thumbnail';
        
        $info = pathinfo($filename);
        
        $file_name_thumb = $id.'_thumb.'.$info['extension'];
        //echo $path_to_thumbs_directory.DIRECTORY_SEPARATOR.$file_name_thumb;die();
        /*if(is_file($path_to_thumbs_directory.DIRECTORY_SEPARATOR.$file_name_thumb)){
            return '';
        }*/

        if(preg_match('/[.](jpg)$/i', $filename)) {  
            $im = imagecreatefromjpeg(JPATH_ROOT.DIRECTORY_SEPARATOR.$filename);  
        } else if (preg_match('/[.](gif)$/i', $filename)) {  
            $im = imagecreatefromgif(JPATH_ROOT.DIRECTORY_SEPARATOR.$filename);  
        } else if (preg_match('/[.](png)$/i', $filename)) {  
            $im = imagecreatefrompng(JPATH_ROOT.DIRECTORY_SEPARATOR.$filename);  
        }  

        $ox = imagesx($im);  
        $oy = imagesy($im);  

        $nx = $final_width_of_image;  
        $ny = floor($oy * ($final_width_of_image / $ox));  

        $nm = imagecreatetruecolor($nx, $ny);  

        imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);  

        if(!file_exists($path_to_thumbs_directory)) {  
          if(!mkdir($path_to_thumbs_directory)) {  
               die("There was a problem. Please try again!");  
          }   
           }  

        imagejpeg($nm, $path_to_thumbs_directory .DIRECTORY_SEPARATOR. $file_name_thumb);  
        
        
    }  
    
    function getGroupList(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('a.id, a.name')
                ->from('#__jbcatalog_adfgroup AS a')
                ->where('a.published = "1"')
                ->where('a.id NOT IN (SELECT group_id FROM #__jbcatalog_category_adfs_group WHERE cat_id = '.intval($this->getState('category.id')).')');

        
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


        $null_arr[] = JHTML::_('select.option',  "", JText::_('COM_JBCATALOG_ADF_FIELDTYPE_SELECT'), 'id', 'name' );


        $options = array_merge($null_arr, $options);
        
        return JHTML::_('select.genericlist',   $options, 'grouplist', 'class="inputboxsel" size="1"', 'id', 'name', 0 );
     

    }
    
    function getAdfList(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('a.id, a.name')
                ->from('#__jbcatalog_adf AS a')
                ->where('a.published = "1"')
                ->where('a.id NOT IN (SELECT adfs_id FROM #__jbcatalog_category_adfs WHERE group_id=0 AND cat_id = '.intval($this->getState('category.id')).')');
               


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


        $null_arr[] = JHTML::_('select.option',  "", JText::_('COM_JBCATALOG_ADF_FIELDTYPE_SELECT'), 'id', 'name' );


        $options = array_merge($null_arr, $options);
        
        return JHTML::_('select.genericlist',   $options, 'adflist', 'class="inputboxsel" size="1"', 'id', 'name', 0 );
     

    }
    
    function getGroupVals(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('a.id, a.name')
                ->from('#__jbcatalog_adfgroup AS a')
                ->innerjoin('#__jbcatalog_category_adfs_group as g ON g.group_id = a.id')
                ->where('g.cat_id = '.intval($this->getState('category.id')))
                ->where('a.published = "1"');

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
            $i = 0;
            foreach($options as $option){
                $query = $db->getQuery(true)
                ->select('a.id, a.name, c.listview, c.filtered')
                ->from('#__jbcatalog_adf AS a')
                ->innerjoin('#__jbcatalog_adf_ingroups as g ON g.adfid = a.id')
                ->leftjoin('#__jbcatalog_category_adfs as c ON (c.adfs_id = a.id AND c.cat_id = '.intval($this->getState('category.id')).')')           
                ->where('g.groupid = '.intval($option->id))
                ->where('a.published = "1"');
                $db->setQuery($query);
                $options[$i]->adfs = $db->loadObjectList();
                $i++;
            }
        }
        
        return $options;
    }  
    
    
    function getAdfVals(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('a.id, a.name, g.listview, g.filtered')
                ->from('#__jbcatalog_adf AS a')
                ->innerjoin('#__jbcatalog_category_adfs as g ON g.adfs_id = a.id')
                ->where('g.cat_id = '.intval($this->getState('category.id')))
                ->where('g.group_id = 0')
                ->where('a.published = "1"');



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

        return $options;
    }
    
}
