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

class JBCatalogModelPlugin extends JModelAdmin
{
    protected $text_prefix = 'COM_JBCATALOG_PLUGIN';
    
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_jbcatalog.plugin', 'plugin', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
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
    public function getTable($type = 'Plugin', $prefix = 'JBCatalogTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    public function save($data)
    {
        $pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('plugin.id');
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
                $associations = JLanguageAssociations::getAssociations('com_jbcatalog', '#__jbcatalog_plugin', 'com_jbcatalog.plugin', $item->id);

                foreach ($associations as $tag => $association)
                {
                    $item->associations[$tag] = $association->id;
                }
            }
        }

        
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
        $data = JFactory::getApplication()->getUserState('com_jbcatalog.edit.plugin.data', array());

        if (empty($data))
        {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('plugin.id') == 0)
            {
                $app = JFactory::getApplication();
               // $data->set('catid', $app->input->get('catid', $app->getUserState('com_jbcatalog.category.filter.category_id'), 'int'));
            }
        }

        $this->preprocessData('com_jbcatalog.plugin', $data);

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
    
    public function getPlugins($pk = null){
        $plugin_id = $this->getState('plugin.id');
        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
        $pl = new ParsePlugins('adf', $plugin_id, 0);

        $plugin['settings'] = $pl->getData('getPluginSettings');
        
        return $plugin;
    }
    
    
}
