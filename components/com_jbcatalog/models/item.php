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


class JBCatalogModelItem extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jbcatalog.item';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_jbcatalog';

	protected $_item = null;

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

		$pk = $app->input->getInt('id');
		$this->setState('item.id', $pk);
                
                $pk = $app->input->getInt('catid');
		$this->setState('catid', $pk);

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
	public function getItem()
	{
		if (!count($this->_item))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;
			if ($active)
			{
				$params->loadString($active->params);
			}
                        $id = (int) $this->getState('item.id');
			$this->_item = $this->_getItem($id);
                        $this->_item->adf = $this->_getAdfGroups($id, 0);
                        $this->_item->adftab = $this->_getAdfGroups($id, 1);
                        $this->_item->adf_singles = $this->_getAdfSingles($id);
                        $this->_getItemImg($id);
		}
                
                
		return $this->_item;
	}
        
        private function _getItem($id){
            $db = $this->getDbo();
            
            $query = "SELECT * " 
                    ." FROM #__jbcatalog_items as a"
                    ." WHERE id = {$id}";
            $db->setQuery($query);
            return $db->loadObject();
                
        }
        private function _getItemImg($id){
            $db = $this->getDbo();
           
            $query = "SELECT name FROM #__jbcatalog_files"
            ." WHERE catid = 2 AND itemid = ".intval($id)." AND ftype = 1"
            ." ORDER BY ordering";
            $db->setQuery($query);
            $this->_item->images = $db->loadColumn();
            
        }
        private function _getAdfGroups($id, $tab){
            $db = $this->getDbo();
            $query = "SELECT g.*"
                ." FROM #__jbcatalog_items_cat as a"
                ." JOIN #__jbcatalog_category as b"
                ." ON a.cat_id = b.id"
                ." JOIN #__jbcatalog_category_adfs_group as c"
                ." ON c.cat_id = b.id"
                ." JOIN #__jbcatalog_adfgroup as g"
                ." ON g.id = c.group_id AND g.published = '1'"    
                ." WHERE a.item_id = {$id}"
                .($tab?" AND g.displayopt = '2'":" AND g.displayopt != '2'")
                ." GROUP BY g.id"
                ." ORDER BY g.ordering";

            $db->setQuery($query);
            $adfs_groups = $db->loadObjectList();
            
            for($i = 0; $i < count($adfs_groups); $i++){
                $query = "SELECT d.* "
                    ." FROM #__jbcatalog_adf as d"
                    ." JOIN #__jbcatalog_adf_ingroups as i"
                    ." ON i.adfid = d.id"    
                    ." WHERE i.groupid = ".$adfs_groups[$i]->id
                    ." AND d.published = '1'"    
                    ." ORDER BY d.ordering, d.id";
                $db->setQuery($query);
                $adfs = $db->loadObjectList();
                
                for($j=0; $j<count($adfs); $j++){
                    ///array_push($array,$this->_getAdfValue($adfs[$j], $id));
                    $adf_show = array($adfs[$j]->name, $adfs[$j]->adf_tooltip, $this->_getAdfValue($adfs[$j], $id));
                    $adfs_groups[$i]->adf[] = $adf_show;
                }
                
            }
            
            return $adfs_groups;
        }
        
        private function _getAdfSingles($id){
            $db = $this->getDbo();
            
                $query = "SELECT d.* "
                    ." FROM #__jbcatalog_adf as d"
                    ." JOIN #__jbcatalog_category_adfs as i"
                    ." ON i.adfs_id = d.id"    
                    ." WHERE i.group_id = 0"
                    ." AND d.published = '1'"
                    ." AND i.cat_id = ".intval($this->getState('catid'))   
                    ." ORDER BY d.ordering, d.id";
                $db->setQuery($query);
                $adfs = $db->loadObjectList();
                
                for($j=0; $j<count($adfs); $j++){
                    ///array_push($array,$this->_getAdfValue($adfs[$j], $id));
                    $adf_show = array($adfs[$j]->name, $adfs[$j]->adf_tooltip, $this->_getAdfValue($adfs[$j], $id));
                    $adfs[$j]->adf[] = $adf_show;
                }

            
            return $adfs;
        }
        
        private function _getAdfValue($adf, $id){
            $db = $this->getDbo();
            
            
            
            
            $db->setQuery(
                    "SELECT adf_value FROM #__jbcatalog_adf_values"
                    ." WHERE adf_id = ".$adf->id
                    ." AND item_id = ".intval($id)
                    );
            $value = $db->loadResult();
            
            
            
            //plugin get adf value
            require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
                        
            $pl = new ParsePlugins('adf', $adf->field_type);
            $value = $pl->getData('getItemFEAdfValue', array("adf" => $adf, "value" => $value, 'itemid' => $id));

            if($value == array()){
                $value = '';
            }
            /*
            switch ($adf->field_type){
                //text
                case '0':

                    $value = htmlspecialchars($value);
                    if($adf->adf_numeric){
                        $value = number_format(floatval($value), 0, '.', ' ');
                    }
                    break;
                //radio
                case '1':
                    $value = $value?JText::_("Yes"):JText::_("No");
                    break;
                //editor
                case '2':


                    $db->setQuery(
                        "SELECT adf_text FROM #__jbcatalog_adf_values"
                        ." WHERE adf_id = ".$adf->id
                        ." AND item_id = ".intval($id)
                    );
                    $value = $db->loadResult();
                    

                    break;
                //select
                case '3':
                    $db->setQuery(
                        "SELECT b.name FROM #__jbcatalog_adf_values as a"
                        ." JOIN #__jbcatalog_adf_select as b"
                        ." ON b.id = a.adf_value"    
                        ." WHERE a.adf_id = ".$adf->id
                        ." AND a.item_id = ".intval($id)
                    );
                    $value = $db->loadResult();
                    break;
                //multi select
                case '4':
                    $db->setQuery(
                        "SELECT GROUP_CONCAT(b.name) FROM #__jbcatalog_adf_values as a"
                        ." JOIN #__jbcatalog_adf_select as b"
                        ." ON b.id = a.adf_value"    
                        ." WHERE a.adf_id = ".$adf->id
                        ." AND a.item_id = ".intval($id)
                    );
                    $value = $db->loadResult();

                    
                    break;
                //link
                case '5':
                    $value_link = htmlspecialchars($value);
                    if(substr($value, 0, 4) != 'http'){
                        $value_link = 'http://'.$value_link;
                    }
                    $value = '<a href="'.$value_link.'" target="_blank">'.$value.'</a>';
                    break;
                //date
                case '6':
                    $value = htmlspecialchars($value);
                    break;
                //ranking
                case '7':
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
                    break;
                case 8:
                    //echo $value;
                    if($value){
                        $db->setQuery(
                            "SELECT b.title FROM #__jbcatalog_adf_values as a"
                            ." JOIN #__jbcatalog_complex_item as b"
                            ." ON b.id = a.adf_value"    
                            ." WHERE  b.published != -2 AND a.adf_id = ".$value

                        );
                        $value1 = $db->loadResult();

                        $val = $value;

                        $vals = array();

                        while ($val){


                            if($val){
                                $query = 'SELECT title FROM #__jbcatalog_complex_item'
                                .' WHERE id = '.$val;
                                $db->setQuery($query);
                                $title = $db->loadResult();
                                if($title){
                                    $vals[] = $title;
                                }


                                $query = 'SELECT parent_id FROM #__jbcatalog_complex_item'
                                    .' WHERE id = '.$val;
                                $db->setQuery($query);
                                $val = $db->loadResult();
                            }

                        }
                        $value = '';
                        if(count($vals) > 1){
                            for($j=count($vals)-1;$j>=0;$j--){
                                if($j != count($vals)-1){
                                    $value .= ' / ';
                                }
                                $value .= $vals[$j];
                            }
                        }else{
                            $value = $value1;
                        }
                    
                    }
                    
                    
                    
                    break;
            }
            */
            if($value != ''){
                $postf = str_replace('{sup}', '<sup>', $adf->adf_postfix);
                $postf = str_replace('{/sup}', '</sup>', $postf);
                $value = $adf->adf_prefix.$value.$postf;
            }
            
            return $value;
        }

        
        public function rate_item($id, $adf_id, $value){
            $user = JFactory::getUser();
            $db = $this->getDbo();
            if($user->get('id') && $value < 6  && $value > 0){
                $db->setQuery(
                    "INSERT INTO #__jbcatalog_adf_rating(rating_id,item_id,usr_id,value)"
                    ." VALUES({$adf_id},{$id},{$user->get('id')},{$value})"
                    ." ON DUPLICATE KEY UPDATE value = '".$value."'"
                    );
                $db->execute();
            }else if($user->get('id') && $value == '0'){
                $db->setQuery(
                        "DELETE FROM #__jbcatalog_adf_rating "
                        ." WHERE rating_id = {$adf_id} AND item_id = {$id} AND usr_id = ".$user->get('id')
                        );
                $db->execute();        
            }    
        }
        
        public function getIsAdmin(){
            $user = JFactory::getUser();
            $isAdmin = $user->get('isRoot');
            
            return ($isAdmin);
        }
        
        public function getItemPosition(){
            $db = JFactory::getDbo();
            
            $db->setQuery('SELECT data FROM #__jbcatalog_options WHERE name="item_position"');
            return $db->loadResult();
            
            return (isset($_POST['edit']) && $isAdmin);
        }
}
