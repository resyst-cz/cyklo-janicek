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


class JBCatalogModelItem extends JModelAdmin
{
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_jbcatalog.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }

        // Determine correct permissions to check.
        if ($this->getState('item.id'))
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
    public function getTable($type = 'Item', $prefix = 'JBCatalogTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    public function save($data)
    {
        
        $pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('item.id');
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



        $this->setState('item.id', $table->id);

        $db = JFactory::getDbo();
        $db->setQuery("DELETE FROM #__jbcatalog_items_cat WHERE item_id={$table->id}");
        $db->execute();
        if(isset($data["catid"]) && $data["catid"]){
            foreach($data["catid"] as $asdf ){
                $db->setQuery("INSERT INTO #__jbcatalog_items_cat(item_id,cat_id) VALUES({$table->id},{$asdf})");
                $db->execute();
            }

        }
        $db->setQuery("DELETE FROM #__jbcatalog_adf_values WHERE item_id={$table->id}");
        $db->execute();
        $z = 0;
        if(isset($_POST['adfids']) && count($_POST['adfids'])){
            foreach($_POST['adfids'] as $adfid){
                if(isset($_POST['extraf'][$adfid])){
                    $val = $_POST['extraf'][$adfid];
                    //$val_text = (intval($_POST['adfids_type'][$z]) == 2)?$_POST['extraf'][$adfid]:'';
                    
                    
                    //plugin adf fill
                    require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
                    $pl = new ParsePlugins('adf', intval($_POST['adfids_type'][$z]));
                    $pl->getData('saveItemEdit', array("adfid" => $adfid, "value" => $val, 'itemid' => $table->id));

                   
                    
                }
                $z++;
            }
            
        }
        
        //images
        $ord = 0;
        $db->setQuery("DELETE FROM #__jbcatalog_files WHERE catid=2 AND ftype=1 AND itemid={$table->id}");
        $db->execute();
        if(isset($_POST["filnm"]) && count($_POST["filnm"])){
            foreach ($_POST["filnm"] as $filnm){
                $db->setQuery("INSERT INTO #__jbcatalog_files(catid,name,ftype,ordering,itemid) VALUES(2,'".$filnm."',1,{$ord},{$table->id})");
                $db->execute();
                $ord++;
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
                ->where('context=' . $db->quote('com_jbcatalog.item'))
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
                    $query->values($id . ',' . $db->quote('com_jbcatalog.item') . ',' . $db->quote($key));
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
           // $registry = new JRegistry;
           // $registry->loadString($item->metadata);
           // $item->metadata = $registry->toArray();
        }

        // Load associated contact items
        $app = JFactory::getApplication();
        $assoc = isset($app->item_associations) ? $app->item_associations : 0;

        if ($assoc)
        {
            $item->associations = array();

            if ($item->id != null)
            {
                $associations = JLanguageAssociations::getAssociations('com_jbcatalog', '#__jbcatalog_items', 'com_jbcatalog.item', $item->id);

                foreach ($associations as $tag => $association)
                {
                    $item->associations[$tag] = $association->id;
                }
            }
        }

        $db = $this->getDbo();
        $query = "SELECT b.id"
            ." FROM #__jbcatalog_items_cat as a"
            ." JOIN #__jbcatalog_category as b"
            ." ON a.cat_id = b.id"
            ." WHERE a.item_id = ".(int) $item->id
            ." ORDER BY b.title";

        $db->setQuery($query);
        $item->catid = $db->loadColumn();
        
        $query = "SELECT name FROM #__jbcatalog_files"
                ." WHERE catid = 2 AND itemid = ".intval($item->id)." AND ftype = 1"
                ." ORDER BY ordering";
        $db->setQuery($query);
        $item->images = $db->loadColumn();

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
        $data = JFactory::getApplication()->getUserState('com_jbcatalog.edit.item.data', array());

        if (empty($data))
        {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('item.id') == 0)
            {
                $app = JFactory::getApplication();
               // $data->set('catid', $app->input->get('catid', $app->getUserState('com_jbcatalog.category.filter.category_id'), 'int'));
            }
        }

        $this->preprocessData('com_jbcatalog.item', $data);

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

    public function getItemAdfs($cats, $item_id){
        if(!count($cats)){
            return '';
        }
        $cat_id = implode(',', $cats);
        $db = $this->getDbo();
        
        //$id = $this->getState('item.id');
        $query = "(SELECT d.*, g.name as catname"
            ." FROM #__jbcatalog_category as b"

            ." JOIN #__jbcatalog_category_adfs_group as c"
            ." ON c.cat_id = b.id"
            ." JOIN #__jbcatalog_adfgroup as g"
            ." ON g.id = c.group_id AND g.published = '1'"  
            ." JOIN #__jbcatalog_adf_ingroups as i"
            ." ON i.groupid = g.id"    
            ." JOIN #__jbcatalog_adf as d"
            ." ON i.adfid = d.id"
            ." JOIN #__jbcatalog_plugins as p"
            ." ON p.id = d.field_type AND p.published = '1' AND p.type = 'adf'"    
            ." WHERE b.id IN (".$cat_id.")"
            ." AND d.published = '1'"    
            ." GROUP BY d.id"
            ." ORDER BY g.id, d.ordering, d.id)"
                ." UNION "
                ." (SELECT d1.*, '' as catname"    
                ." FROM #__jbcatalog_category_adfs as ad"
                ." JOIN #__jbcatalog_adf as d1"  
                ." ON d1.id = ad.adfs_id AND ad.group_id = 0 AND ad.cat_id IN (".$cat_id.")"
                ." WHERE d1.published = '1')"    ;

        $db->setQuery($query);
        $adfs = $db->loadObjectList();
        //var_dump($adfs);
        $html = '';
        for($j=0; $j<count($adfs); $j++){
            $html .= $this->getAdfByType($adfs[$j], $item_id);
        }
        return $html;
    }
    
    public function getAdfByType($adf, $item_id){
        $db = $this->getDbo();
        $html  = '<div class="control-group">'
                .'<div class="control-label">'
                    .$adf->name
                .'</div>'
                .'<div class="controls">';
        
        $db->setQuery(
                "SELECT adf_value FROM #__jbcatalog_adf_values"
                ." WHERE adf_id = ".$adf->id
                ." AND item_id = ".intval($item_id)
                );
        $value = $db->loadResult();
        
        //plugin adf fill
        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
        $pl = new ParsePlugins('adf', $adf->field_type);
        $html .= $pl->getData('getItemEdit', array("adf" => $adf, "value" => $value, 'itemid' => $item_id));

        
        
        /*
        switch ($adf->field_type){
            //text
            case '0':
                $class = $adf->adf_numeric?'class="numericOnly"':'';
                $html .= '<input type="text" name="extraf['.$adf->id.']" '.$class.' value="'.htmlspecialchars($value).'" />';
                break;
            //radio
            case '1':
                $html .= JHTML::_('select.booleanlist',  'extraf['.$adf->id.']', 'class="btn-group"', $value );
                break;
            //editor
            case '2':
                
                
                $db->setQuery(
                "SELECT adf_text FROM #__jbcatalog_adf_values"
                ." WHERE adf_id = ".$adf->id
                ." AND item_id = ".intval($item_id)
                );
                $value = $db->loadResult();
                
                
                $editor = JFactory::getEditor();

                
                $html .= $editor->display('extraf['.$adf->id.']',$value,'550', '300', '60', '20');
               
                
                break;
            //select
            case '3':
                //if no options
                    if(!count($selarr)){
                        return '';
                    }else{
                        $null_arr[] = JHTML::_('select.option',  "", JText::_('COM_JBCATALOG_ADF_FIELDTYPE_SELECT'), 'id', 'name' );
			
                            $selarr = array_merge($null_arr,$selarr);
                    }
                $html .= JHTML::_('select.genericlist',   $selarr, 'extraf['.$adf->id.']', 'class="inputboxsel" size="1"', 'id', 'name', $value );
                break;
            //multi select
            case '4':
                $db->setQuery(
                "SELECT adf_value FROM #__jbcatalog_adf_values"
                ." WHERE adf_id = ".$adf->id
                ." AND item_id = ".intval($item_id)
                );
                $value = $db->loadColumn();

                //if no options
                    if(!count($selarr)){
                        return '';
                    }
                $html .= JHTML::_('select.genericlist',   $selarr, 'extraf['.$adf->id.'][]', 'class="inputboxsel" multiple size="1"', 'id', 'name', $value );
               
                break;
            //link
            case '5':
                $html .= '<input type="text" name="extraf['.$adf->id.']" value="'.htmlspecialchars($value).'" />';
                break;
            //date
            case '6':
                $html .= JHtml::_('calendar', $value, 'extraf['.$adf->id.']', 'extraf_'.$adf->id, "Y-m-d", 'readonly = "readonly"');
                $html .= '<input type="hidden" name="caldr[]" value="'.$adf->id.'" />';
                break;
            //ranking
            case '7':
                break;
            case '8':
                
                
                
                if($adf->adf_complex){
                    
                    $db->setQuery(
                            'SELECT id'
                            .' FROM #__jbcatalog_complex'
                            .' WHERE id = '.$adf->adf_complex
                            .' AND published != -2'
                            );
                    if($db->loadResult()){
                    
                        $query = $db->getQuery(true)
			->select('a.id , a.title as name')
			->from('#__jbcatalog_complex_item AS a')
                        ->where('a.catid = '.intval($adf->adf_complex))
                        ->order('a.ordering');
                        $db->setQuery($query);
                        
                        
                        $options_all[] = JHTML::_('select.option',  "", JText::_('COM_JBCATALOG_SEL_OPTION'), 'id', 'name' );
			
                        try
                        {
                                $options = $db->loadObjectList();
                                if($options){
                                    $options_all = array_merge($options_all, $options);
                                }
                        }
                        catch (RuntimeException $e)
                        {
                                JError::raiseWarning(500, $e->getMessage());
                        }
                        
                        if($value){
                            $html .= $this->getComplexTree($value, $adf->id);

                        }else{
                            $html .= JHTML::_('select.genericlist',   $options_all, 'extraf['.$adf->id.']', 'class="inputboxsel" size="1" onchange="js_BCAT_cmp_filter('.$adf->id.',1,this.value)"', 'id', 'name', $value );
                            $html .= '<div id="bzdiv_cmp_'.$adf->id.'"></div>';
                        }
                    }    
                }
                
                break;
        }*/
        $html .= '<input type="hidden" name="adfids[]" value="'.$adf->id.'" />';
        $html .= '<input type="hidden" name="adfids_type[]" value="'.$adf->field_type.'" />';
        $html .= '</div></div>';
        return $html;
    }
    
    public function getPlugins($pk = null){
        $adf_id = (int) $this->getState('adf.id');
        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
        
        $pl = new ParsePlugins('adf');

        $plugin['js'] = $pl->getData('getItemViewJS');
        
        return $plugin;
    }
    
}
