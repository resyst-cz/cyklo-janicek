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

class JBCatalogModelAdf extends JModelAdmin
{
    protected $text_prefix = 'COM_JBCATALOG_ADF';
    
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_jbcatalog.adf', 'adf', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }


        // Determine correct permissions to check.
        if ($this->getState('adf.id'))
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
    public function getTable($type = 'Adf', $prefix = 'JBCatalogTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    public function save($data)
    {
        $pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('adf.id');
        $isNew = true;
        $table = $this->getTable();

        // Load the row if saving an existing item.
        if ($pk > 0)
        {
            $table->load($pk);
            $isNew = false;
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

        
        $this->setState('adf.id', $table->id);
        $db = JFactory::getDbo();
        //adf groups
        $db->setQuery("DELETE FROM #__jbcatalog_adf_ingroups WHERE adfid={$table->id}");
        $db->execute();
        
        if(isset($data['group_id']) && count($data['group_id'])){
            foreach($data['group_id'] as $groupid){
                $db->setQuery("INSERT INTO #__jbcatalog_adf_ingroups(adfid,groupid)"
                        ." VALUES({$table->id}, ".intval($groupid).")");
                $db->execute();
            }
        }
        
        //plugin save
        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
        $pl = new ParsePlugins('adf', $table->field_type);
        $pl->getData('saveAdfData', array("adfid" => $table->id));
        
        

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
                ->where('context=' . $db->quote('com_jbcatalog.adf'))
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
                    $query->values($id . ',' . $db->quote('com_jbcatalog.adf') . ',' . $db->quote($key));
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
            /*$registry = new JRegistry;
            $registry->loadString($item->metadata);
            $item->metadata = $registry->toArray();*/
        }

        // Load associated contact items
        $app = JFactory::getApplication();
        $assoc = isset($app->item_associations) ? $app->item_associations : 0;

        if ($assoc)
        {
            $item->associations = array();

            if ($item->id != null)
            {
                $associations = JLanguageAssociations::getAssociations('com_jbcatalog', '#__jbcatalog_adf', 'com_jbcatalog.adf', $item->id);

                foreach ($associations as $tag => $association)
                {
                    $item->associations[$tag] = $association->id;
                }
            }
        }
        $db = JFactory::getDbo();
        $query = "SELECT a.id"
            ." FROM #__jbcatalog_adfgroup as a"
            ." JOIN #__jbcatalog_adf_ingroups as b"
            ." ON b.groupid = a.id"
            ." WHERE b.adfid = ".intval($item->id)
            ." ORDER BY a.name";

        $db->setQuery($query);
        $item->group_id = $db->loadColumn();
        
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
        $data = JFactory::getApplication()->getUserState('com_jbcatalog.edit.adf.data', array());

        if (empty($data))
        {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('adf.id') == 0)
            {
                $app = JFactory::getApplication();
               // $data->set('catid', $app->input->get('catid', $app->getUserState('com_jbcatalog.category.filter.category_id'), 'int'));
            }
        }

        $this->preprocessData('com_jbcatalog.adf', $data);

        return $data;
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
    
    public function getSelVars($pk = null){
        $adf_id = (int) $this->getState('adf.id');
        $db = JFactory::getDbo();
        $db->setQuery(
                "SELECT * FROM #__jbcatalog_adf_select"
                ." WHERE field_id = ".$adf_id
                ." ORDER BY ordering"
                );
        return $db->loadObjectList();
    }
    public function getComplexVars($pk = null){
        //$adf_id = (int) $this->getState('adf.id');
        $db = JFactory::getDbo();
        $db->setQuery(
                "SELECT id,name FROM #__jbcatalog_complex"
                ." WHERE level = 1"
                ." AND published != -2"
                ." ORDER BY lft"
                );
        return $db->loadObjectList();
    }
    
    function trash($cid){
        
        $db = JFactory::getDbo();
        if(count($cid)){
            foreach ($cid as $id){
                $db->setQuery(
                "DELETE FROM #__jbcatalog_adf"
                ." WHERE id = ".intval($id)
                );
                $db->execute();
                
                $db->setQuery(
                "DELETE FROM #__jbcatalog_adf_values"
                ." WHERE adf_id = ".intval($id)
                );
                $db->execute();
                
                $db->setQuery(
                "DELETE FROM #__jbcatalog_adf_select"
                ." WHERE field_id = ".intval($id)
                );
                $db->execute();

            }
        }
    }
    
    public function getPlugins($pk = null){
        $adf_id = (int) $this->getState('adf.id');
        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
        $pl = new ParsePlugins('adf');
        $plugin['js_selected'] = $pl->getData('getAdfViewJSSelected');
        $plugin['js'] = $pl->getData('getAdfViewJS');
        $plugin['js_selected_pre'] = $pl->getData('getAdfViewJSSelectedPre');
        
        $plugin['adf_extra'] = $pl->getData('getAdfViewExtra', array("adfid" => $adf_id));
        
        return $plugin;
    }
    
    
}
