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


class JBCatalogModelCategories extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jbcatalog.categories';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_jbcatalog';

	private $_parent = null;

	private $_items = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);
                // List state information
		$ll = $app->getUserState('list_limit_cat', $app->getCfg('list_limit', 0));
                
                $value = $app->input->get('limit', $ll, 'uint');
		$this->setState('list.limit', $value);
                $app->setUserState('list_limit_cat', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);
		// Get the parent id if defined.
		$parentId = $app->input->getInt('id');
		$this->setState('filter.parentId', $parentId);

		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id	A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * redefine the function an add some properties to make the styling more easy
	 *
	 * @return mixed An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if (!count($this->_items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;
			if ($active)
			{
				$params->loadString($active->params);
			}
			$this->_items = $this->_getCats();
                        $this->_getCatsImg();
		}

		return $this->_items;
	}
        protected function getListQuery()
	{
            $db = $this->getDbo();
		$query = "SELECT * " 
                    ." FROM #__jbcatalog_category"
                    ." WHERE parent_id = 1"
                    ." AND published = 1"    
                    ." ORDER BY lft ASC";
                return $query;
        }
        private function _getCats(){
            $db = $this->getDbo();
            
            $query = "SELECT * " 
                    ." FROM #__jbcatalog_category"
                    ." WHERE parent_id = 1"
                    ." AND published = 1"  
                    ." ORDER BY lft ASC";
            $db->setQuery($query,$this->getState('list.start'), $this->getState('list.limit'));
            return $db->loadObjectList();
                
        }
        private function _getCatsImg(){
            $db = $this->getDbo();
            for($i=0; $i<count($this->_items); $i++){

                $this->_items[$i]->images = null;
                
                if($this->_items[$i]->image && is_file(JPATH_ROOT.DIRECTORY_SEPARATOR.$this->_items[$i]->image)){
                    $this->_items[$i]->images = $this->_items[$i]->image;
                    $info = pathinfo($this->_items[$i]->image);
                    $path_to_thumbs_directory = 'components'.DIRECTORY_SEPARATOR.'com_jbcatalog'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'jsupload'.DIRECTORY_SEPARATOR.'server'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'thumbnail';
                    
                    if(is_file(JPATH_ROOT.DIRECTORY_SEPARATOR.$path_to_thumbs_directory.DIRECTORY_SEPARATOR.$this->_items[$i]->id.'_thumb.'.$info['extension'])){
                        $this->_items[$i]->images = $path_to_thumbs_directory.DIRECTORY_SEPARATOR.$this->_items[$i]->id.'_thumb.'.$info['extension'];
                    }
        
                   // $file_name_thumb = $id.'_thumb.'.$info['extension'];
                }
            }
        }

}
