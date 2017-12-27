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

class JBCatalogModelCategory extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jbcatalog.category';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_jbcatalog';


	private $_items = null;

        private $adf_filter = null;

        private $filtr_query = null;

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
		$this->setState('category.id', $pk);

                $params = $app->getParams();
		$this->setState('params', $params);


                $this->adf_filter = $app->input->get('adf_filter', $app->getUserState('adf_filter_'.$pk, null), 'array');

                $app->setUserState('adf_filter_'.$pk, $this->adf_filter);

                //clear filter
                if(isset($_POST['clear_filter'])){
                    $app->setUserState('adf_filter_'.$pk, null);
                    $this->adf_filter = array();
                }



                if(isset($_GET['bcatfilters'])){
                    $bcatfilters = $_GET['bcatfilters'];
                    if(count($bcatfilters)){
                        if($bcatfilters[count($bcatfilters) - 1]){
                            $this->setState('category.id', $bcatfilters[count($bcatfilters) - 1]);
                        }elseif(isset($bcatfilters[count($bcatfilters) - 2]) && $bcatfilters[count($bcatfilters) - 2]){
                            $this->setState('category.id', $bcatfilters[count($bcatfilters) - 2]);
                        }
                    }
                }
                //$this->adf_filter = $this->getState('adf_filter');
		$params = $app->getParams();
		$this->setState('params', $params);

                //$s = $this->getState('list.limit');
                $ll = $app->getUserState('list_limit'.$pk, $app->getCfg('list_limit', 0));

                $value = $app->input->get('limit', $ll, 'uint');
		$this->setState('list.limit', $value);
                $app->setUserState('list_limit'.$pk, $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

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
            $id = (int) $this->getState('category.id');
            $this->_items = $this->_getAItems($id);

            $this->_getItemsImg();
            $this->getResystItemsInfo();
            $this->getResystItemsPrice();
            $this->getResystItemsOriginalPrice();
            $this->getResystItemsBikeCategory();
		}
		return $this->_items;
	}

        public function getItem(){
            $catid = $this->getState('category.id');
            $db = $this->getDbo();
            $query = "SELECT * "
                    ." FROM #__jbcatalog_category"
                    ." WHERE id = ".intval($catid)
                    ." AND published = 1";
            $db->setQuery($query);
            return $db->loadObject();
        }

        public function getCategories(){
            $catid = $this->getState('category.id');
            $db = $this->getDbo();

            $query = "SELECT * "
                    ." FROM #__jbcatalog_category"
                    ." WHERE parent_id = ".intval($catid)
                    ." AND published = 1"
                    ." ORDER BY lft ASC";
            $db->setQuery($query);
            $categories = $db->loadObjectList();
            for($i=0; $i<count($categories); $i++){

                $categories[$i]->images = null;

                if($categories[$i]->image && is_file(JPATH_ROOT.DIRECTORY_SEPARATOR.$categories[$i]->image)){
                    $categories[$i]->images = $categories[$i]->image;
                    $info = pathinfo($categories[$i]->image);
                    $path_to_thumbs_directory = 'components'.DIRECTORY_SEPARATOR.'com_jbcatalog'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'jsupload'.DIRECTORY_SEPARATOR.'server'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'thumbnail';

                    if(is_file(JPATH_ROOT.DIRECTORY_SEPARATOR.$path_to_thumbs_directory.DIRECTORY_SEPARATOR.$categories[$i]->id.'_thumb.'.$info['extension'])){
                        $categories[$i]->images = $path_to_thumbs_directory.DIRECTORY_SEPARATOR.$categories[$i]->id.'_thumb.'.$info['extension'];
                    }

                   // $file_name_thumb = $id.'_thumb.'.$info['extension'];
                }
            }

            return $categories;

        }

        protected function getListQuery()
	{
            if($this->filtr_query){
                return $this->filtr_query;
            }


            $db = $this->getDbo();


            $filter_sql = '';
            $filter_tbl = '';
            $sql = array();
            $tbl_pref = 1;
            //var_dump($this->adf_filter);//die();
            if(count($this->adf_filter)){
                foreach ($this->adf_filter as $key=>$value){
                    $query = "SELECT published FROM #__jbcatalog_adf WHERE id=".intval($key);
                        $db->setQuery($query);
                        $published = $db->loadResult();

                    if($value && $published){
                        $query = "SELECT field_type FROM #__jbcatalog_adf WHERE id=".intval($key);
                        $db->setQuery($query);
                        $adftype = $db->loadResult();

                        //plugin get adf value
                        require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
                        $pl = new ParsePlugins('adf', $adftype);
                        $sql[] = $pl->getData('getAdfFilterSQL', array("adfid" => $key, "value" => $value));

                    }

                    /*
                    if(!is_array($value)){
                        if($value){
                            if(!$filter_sql){
                                $filter_sql .= " AND (( IF((e.field_type = 0 OR e.field_type = 2  OR e.field_type = 5),IF(e.field_type = 2, v.adf_text LIKE '%{$value}%', v.adf_value LIKE '%{$value}%'),v.adf_value='{$value}') AND v.adf_id = {$key})";
                            }else{
                                $filter_tbl .= " LEFT JOIN #__jbcatalog_adf_values as v{$tbl_pref} ON a.id=v{$tbl_pref}.item_id"
                                               ." JOIN #__jbcatalog_adf as e{$tbl_pref} ON e{$tbl_pref}.id=v{$tbl_pref}.adf_id";

                                $filter_sql .= " AND (IF((e{$tbl_pref}.field_type = 0 OR e{$tbl_pref}.field_type = 2 OR e{$tbl_pref}.field_type = 5),IF(e{$tbl_pref}.field_type = 2,v{$tbl_pref}.adf_text LIKE '%{$value}%',v{$tbl_pref}.adf_value LIKE '%{$value}%'),v{$tbl_pref}.adf_value='{$value}') AND v{$tbl_pref}.adf_id = {$key})";

                                $tbl_pref++;
                            }
                        }
                    }else{
                        $query = "SELECT field_type FROM #__jbcatalog_adf WHERE id=".intval($key);
                        $db->setQuery($query);
                        $adftype = $db->loadResult();

                        //complex fields

                        if($adftype == 8 && count($value)){
                           $value_par = $value[count($value) - 1];
                           for($j=0;$j<count($value);$j++){

                               if($value[count($value) - 1 - $j]){
                                   $value_par = $value[count($value) - 1 - $j];
                                   break;
                               }
                           }
                           if($value_par){
                               $complex_items = array($value_par);
                               $wh = true;
                               while($wh){

                                   $db->setQuery(
                                           "SELECT id FROM #__jbcatalog_complex_item as i"
                                           ." WHERE parent_id IN ({$value_par})"
                                           );
                                   $ids = $db->loadColumn();

                                   if(!count($ids)){
                                       $wh = false;
                                   }else{
                                       $value_par = implode(',', $ids);
                                       $complex_items = array_merge($complex_items, $ids);
                                   }
                               }

                               if(count($complex_items)){
                                   if(!$filter_sql){
                                       $filter_sql .= ' AND (';
                                   }else{
                                       $filter_sql .= ' AND ';
                                   }
                                   $filter_tbl .= " LEFT JOIN #__jbcatalog_adf_values as v{$tbl_pref} ON a.id=v{$tbl_pref}.item_id"
                                               ." JOIN #__jbcatalog_adf as e{$tbl_pref} ON e{$tbl_pref}.id=v{$tbl_pref}.adf_id";

                                   $filter_sql .= " ( v{$tbl_pref}.adf_value IN (".implode(',',$complex_items).") AND v{$tbl_pref}.adf_id = {$key})";

                                   $tbl_pref++;
                               }
                               //var_dump($complex_items);


                           }
                        }elseif($adftype == 6 && count($value)){
                            if($value[0] || $value[1]){
                                if(!$filter_sql){
                                       $filter_sql .= ' AND (';
                                   }else{
                                       $filter_sql .= ' AND ';
                                   }
                                   $filter_tbl .= " LEFT JOIN #__jbcatalog_adf_values as v{$tbl_pref} ON a.id=v{$tbl_pref}.item_id"
                                               ." JOIN #__jbcatalog_adf as e{$tbl_pref} ON e{$tbl_pref}.id=v{$tbl_pref}.adf_id";

                                   $filter_sql .= " ( ".($value[0]?"v{$tbl_pref}.adf_value >= '".$value[0]." 00:00:00' AND":'')." ".($value[1]?" v{$tbl_pref}.adf_value <= '".$value[1]." 00:00:00' AND":'')." v{$tbl_pref}.adf_id = {$key})";
                                   $tbl_pref++;
                            }
                        }elseif($adftype == 7 && count($value)){

                            //$db->setQuery("SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = {$key} AND i");
                            //die();
                            if(!$filter_sql){
                                       $filter_sql .= ' AND (';
                                   }else{
                                       $filter_sql .= ' AND ';
                                   }
                                   if($value[0] == 0){
                                       $sq = ' (SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = '.$key.' AND item_id = a.id) is NULL OR ';
                                   }else{
                                       $sq = "{$value[0]} <= (SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = {$key} AND item_id = a.id) AND ";
                                   }
                                   $filter_sql .= " (  {$sq} {$value[1]} >= (SELECT AVG(value) FROM #__jbcatalog_adf_rating WHERE rating_id = {$key} AND item_id = a.id))";
                                   //$tbl_pref++;
                        }

                    }
                    */
                }

                /*if($filter_sql){
                    $filter_sql .= ")";
                }*/
                if(count($sql)){
                    foreach($sql as $sq){
                        if(isset($sq['tbl']) && $sq['tbl']){
                            $filter_tbl .= $sq['tbl'];
                        }
                        if(isset($sq['sql']) && $sq['sql']){
                            $filter_sql .= $sq['sql'];
                        }
                    }
                }

            }

            $query = "SELECT a.*"
                    ." FROM #__jbcatalog_items as a"
                    ." JOIN #__jbcatalog_items_cat as b"
                    ." ON a.id = b.item_id"
                    .($this->getState('category.id')?" AND b.cat_id = ".(int) $this->getState('category.id'):"")
                    ." LEFT JOIN #__jbcatalog_adf_values as v ON a.id=v.item_id"
                    ." LEFT JOIN #__jbcatalog_adf as e ON e.id=v.adf_id"
                    .$filter_tbl
                    ." WHERE a.published = '1' "
                    .$filter_sql
                    ." GROUP BY a.id"
                    ." ORDER BY a.ordering";
             $this->filtr_query = $query;//die();
             return $query;
        }
        private function _getAItems($id){
            $db = $this->getDbo();
            $query = $this->getListQuery();
            $db->setQuery($query, $this->getState('list.start'), $this->getState('list.limit'));
            $items = $db->loadObjectList();

            $query = 'SELECT e.* FROM #__jbcatalog_category_adfs as c'
                    .' JOIN #__jbcatalog_adf as e ON e.id=c.adfs_id'
                    .' WHERE c.cat_id = '.$id.' AND c.listview = "1" AND e.published="1"';
            $db->setQuery($query);

            $fields = $db->loadObjectList();

            if(count($fields)){
                if (!empty($items)) {
                    $items[0]->fieldsname = $fields;
                    for($i = 0; $i < count($items); $i++){
                        foreach($fields as $fieldid){
                            $items[$i]->fields[] = $this->_getAdfValue($fieldid, $items[$i]->id);
                        }
                    }
                }
            }

            return $items;

        }
        private function _getItemsImg(){
            $db = $this->getDbo();
            for($i=0; $i<count($this->_items); $i++){
                $query = "SELECT name FROM #__jbcatalog_files"
                ." WHERE catid = 2 AND itemid = ".intval($this->_items[$i]->id)." AND ftype = 1"
                ." ORDER BY ordering";
                $db->setQuery($query);
                $this->_items[$i]->images = $db->loadColumn();
            }
        }

        public function getFilters(){
            $id = (int) $this->getState('category.id');
            $db = $this->getDbo();


            $query = "(SELECT d.*"
                ." FROM #__jbcatalog_items_cat as a"
                ." JOIN #__jbcatalog_category as b"
                ." ON a.cat_id = b.id"
                ." JOIN #__jbcatalog_category_adfs_group as c"
                ." ON c.cat_id = b.id"
                ." JOIN #__jbcatalog_adfgroup as g"
                ." ON g.id = c.group_id AND g.published = '1'"
                ." JOIN #__jbcatalog_adf_ingroups as i"
                ." ON i.groupid = g.id"
                ." JOIN #__jbcatalog_adf as d"
                ." ON i.adfid = d.id"
                ." LEFT JOIN #__jbcatalog_category_adfs as j"
                ." ON j.adfs_id = d.id AND b.id = j.cat_id AND j.group_id != '0'"
                ." WHERE b.id = ".$id
                ." AND d.published = '1'"
                ." AND ((d.filters = '1' AND j.filtered != '1') OR (j.filtered = '2'))"
                ." GROUP BY d.id"
                ." ORDER BY g.id, d.ordering, d.id)"
                ." UNION "
                ." (SELECT d1.*"
                ." FROM #__jbcatalog_category_adfs as ad"
                ." JOIN #__jbcatalog_adf as d1"
                ." ON d1.id = ad.adfs_id AND ad.group_id = 0 AND ad.cat_id = ".$id
                ." WHERE d1.published = '1'"
                ." AND ((d1.filters = '1' AND ad.filtered != '1') OR (ad.filtered = '2'))"
                ." )"
                    ;

            $db->setQuery($query);
            $adfs = $db->loadObjectList();
            $html = array();
            for($j=0; $j<count($adfs); $j++){
                $adf_show = array($adfs[$j]->name, $adfs[$j]->adf_tooltip, $this->getAdfByType($adfs[$j]), $adfs[$j]->field_type);
                $html[] = $adf_show;
                //$html[$adfs[$j]->name]= $this->getAdfByType($adfs[$j]);
            }
            return $html;
        }
        public function getAdfByType($adf){
            $db = $this->getDbo();



            $value = isset($this->adf_filter[$adf->id])?$this->adf_filter[$adf->id]:null;

            //plugin for adf
            require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";

            $pl = new ParsePlugins('adf', $adf->field_type);
            $html = $pl->getData('getAdfFilter', array("adf" => $adf, "value" => $value));

            /*
            $html = '';
            switch ($adf->field_type){
                //text
                case '0':

                    $html = '<input class="inpfilter" type="text" name="adf_filter['.$adf->id.']" value="'.htmlspecialchars($value).'" />';
                    break;
                //radio
                case '1':
                    $html = '<input type="checkbox" name="adf_filter['.$adf->id.']" value="1" '.($value?"checked":"").' />';
                    break;
                //editor
                case '2':

                    $html = '<input class="inpfilter" type="text" name="adf_filter['.$adf->id.']" value="'.htmlspecialchars($value).'" />';


                    break;
                //select & multi select
                case '3':

                case '4':

                    //if no options
                        if(!count($selarr)){
                            return '';
                        }else{
                            $null_arr[] = JHTML::_('select.option',  "", JText::_('COM_JBCATALOG_SELOPTION'), 'id', 'name' );

                            $selarr = array_merge($null_arr,$selarr);

                        }
                    $html = JHTML::_('select.genericlist',   $selarr, 'adf_filter['.$adf->id.']', 'class="inputboxsel" size="1"', 'id', 'name', $value );

                    break;
                //link
                case '5':
                    $html = '<input class="inpfilter" type="text" name="adf_filter['.$adf->id.']" value="'.htmlspecialchars($value).'" />';

                    break;
                //date
                case '6':
                    $html = JHtml::_('calendar', isset($value[0])?$value[0]:'', 'adf_filter['.$adf->id.'][]', 'extraf_'.$adf->id, "%Y-%m-%d", ' class="inpfilter" readonly = "readonly"');
                    $html .= '<div>&nbsp;'.JText::_('COM_JBCATALOG_TO').':&nbsp;</div>';
                    $html .= JHtml::_('calendar', isset($value[1])?$value[1]:'', 'adf_filter['.$adf->id.'][]', 'extraf_'.$adf->id.'_1', "%Y-%m-%d", ' class="inpfilter" readonly = "readonly');

                    break;
                //rating
                case '7':
                    $selarr = array();
                    $selarr[] = JHTML::_('select.option',  0, 0, 'id', 'name' );
                    $selarr[] = JHTML::_('select.option',  1, 1, 'id', 'name' );
                    $selarr[] = JHTML::_('select.option',  2, 2, 'id', 'name' );
                    $selarr[] = JHTML::_('select.option',  3, 3, 'id', 'name' );
                    $selarr[] = JHTML::_('select.option',  4, 4, 'id', 'name' );
                    $selarr[] = JHTML::_('select.option',  5, 5, 'id', 'name' );

                    $html = JHTML::_('select.genericlist',   $selarr, 'adf_filter['.$adf->id.'][]', 'class="inputboxsel" size="1"', 'id', 'name', isset($value[0])?$value[0]:0, 'adf_filter_'.$adf->id.'_0' );
                    $html .= JHTML::_('select.genericlist',   $selarr, 'adf_filter['.$adf->id.'][]', 'class="inputboxsel" size="1"', 'id', 'name', isset($value[1])?$value[1]:5, 'adf_filter_'.$adf->id.'_1' );
                    break;
                case '8':
                if($adf->adf_complex){

                        $db->setQuery(
                            "SELECT b.title FROM #__jbcatalog_adf_values as a"
                            ." JOIN #__jbcatalog_complex_item as b"
                            ." ON b.id = a.adf_value"
                            ." WHERE  b.published != -2 AND a.adf_id = ".$adf->adf_complex

                        );
                        $value1 = $db->loadResult();
                        if($value1){
                            $query = $db->getQuery(true)
                            ->select('a.id , a.title as name')
                            ->from('#__jbcatalog_complex_item AS a')
                            ->where('a.catid = '.intval($adf->adf_complex))
                            ->order('a.ordering');
                            $db->setQuery($query);


                            $options_all[] = JHTML::_('select.option',  "", JText::_('COM_JBCATALOG_SELOPTION'), 'id', 'name' );

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
                            if(count($value) > 1 && $value[1] != 0){

                                $html .= $this->getComplexTree($value, $adf->id);

                            }else{
                                $html .= JHTML::_('select.genericlist',   $options_all, 'adf_filter['.$adf->id.'][]', 'class="inputboxsel" size="1" onchange="js_BCAT_cmp_filter('.$adf->id.',1,this.value)"', 'id', 'name', $value[0] );
                                $html .= '<div id="bzdiv_cmp_'.$adf->id.'"></div>';
                            }
                        }
                }

                break;
            }
            */
            return $html;
        }
        public function getCatID(){
            return (int) $this->getState('category.id');
        }

        public function getFilterVar(){
            return $this->adf_filter;
        }



        public function getPlugins($pk = null){

            require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";

            $pl = new ParsePlugins('adf');

            $plugin['js'] = $pl->getData('getCategoryFEViewJS');

            return $plugin;
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

            if($value != ''){
                $postf = str_replace('{sup}', '<sup>', $adf->adf_postfix);
                $postf = str_replace('{/sup}', '</sup>', $postf);
                $value = $adf->adf_prefix.$value.$postf;
            }

            return $value;
        }

        public function getCustomPlugins(){
            $id = (int) $this->getState('category.id');
            require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";

            $arr = array();
            foreach($this->_items as $items){
                $arr[] = $items->id;
            }

            $pl = new ParsePlugins('custom');
            $value = $pl->getData('getCompareFields', array("c_id" => $id, "items" => $arr));

            return $value;
        }

        /**
         * 2015-08-02 ReSyst.cz
         * Prida k polozkam detaily
         */
        private function getResystItemsInfo() {
            $db = $this->getDbo();
            for($i=0; $i<count($this->_items); $i++){
                // adf_id = 9 -> cena, adf_id = 10 -> kategorie kola
                $query = "
                    SELECT
                        a.name AS extra,
                        v.adf_value AS hodnota,
                        v.adf_id AS hodnota_id
                    FROM #__jbcatalog_adf_values AS v
                    INNER JOIN #__jbcatalog_adf AS a ON (v.adf_id = a.id)
                    WHERE v.item_id = " . intval($this->_items[$i]->id) . "
                    AND v.adf_id NOT IN (9, 10, 11)
                    ORDER BY v.adf_id
                ";
                $db->setQuery($query);
                $this->_items[$i]->resyst_info = $db->loadAssocList();
            }
        }

        /**
         * 2015-08-02 ReSyst.cz
         * Prida k polozkam cenu
         */
        private function getResystItemsPrice() {
            $db = $this->getDbo();
            for($i=0; $i<count($this->_items); $i++){
                $query = "
                    SELECT
                        v.adf_value AS cena
                    FROM #__jbcatalog_adf_values AS v
                    INNER JOIN #__jbcatalog_adf AS a ON (v.adf_id = a.id)
                    WHERE v.item_id = " . intval($this->_items[$i]->id) . "
                    AND v.adf_id = 9
                ";
                $db->setQuery($query);
                $this->_items[$i]->resyst_cena = $db->loadResult();
            }
        }

        /**
         * 2016-03-05 ReSyst.cz
         * Prida k polozkam puvodni cenu
         */
        private function getResystItemsOriginalPrice() {
            $db = $this->getDbo();
            for($i=0; $i<count($this->_items); $i++){
                $query = "
                    SELECT
                        v.adf_value AS puvodni_cena
                    FROM #__jbcatalog_adf_values AS v
                    INNER JOIN #__jbcatalog_adf AS a ON (v.adf_id = a.id)
                    WHERE v.item_id = " . intval($this->_items[$i]->id) . "
                    AND v.adf_id = 11
                ";
                $db->setQuery($query);
                $this->_items[$i]->resyst_puvodni_cena = $db->loadResult();
            }
        }

        /**
         * 2015-08-17 ReSyst.cz
         * Prida k polozkam kategorii kola
         */
        private function getResystItemsBikeCategory() {
            $db = $this->getDbo();
            for($i=0; $i<count($this->_items); $i++){
                $query = "
                    SELECT
                        s.id, s.name AS kategorie
                    FROM  #__jbcatalog_adf_select AS s
                    INNER JOIN #__jbcatalog_adf_values AS v ON (s.id = v.adf_value)
                    INNER JOIN #__jbcatalog_adf AS a ON (v.adf_id = a.id)
                    WHERE v.item_id = " . intval($this->_items[$i]->id) . "
                    AND v.adf_id = 10
                ";
                $db->setQuery($query);
                $this->_items[$i]->resyst_kategorie = $db->loadAssoc();
            }
        }
}
