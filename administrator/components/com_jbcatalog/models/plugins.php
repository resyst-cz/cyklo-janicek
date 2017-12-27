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

class JBCatalogModelPlugins extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'menutype', 'a.menutype',
				'title', 'a.title',
				'alias', 'a.alias',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'level', 'a.level',
				'path', 'a.path',
				'client_id', 'a.client_id',
				'home', 'a.home',
			);

			$app = JFactory::getApplication();
			$assoc = isset($app->item_associations) ? $app->item_associations : 0;
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}




	/**
	 * Builds an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery    A query object.
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		// Select all fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				$db->quoteName(
					array('a.id', 'a.title', 'a.name','a.published','a.description','a.ordering', 'a.version', 'a.type'),
					array(null, null, null, null, null, null, null, null)
				)
			)
		);
                $query->from($db->quoteName('#__jbcatalog_plugins') . ' AS a');
                //$query->leftJoin($db->quoteName('#__jbcatalog_items_cat') .' AS b ON b.item_id = a.id');
                //$query->group("a.id");
		// Exclude the root category.
		//$query->where('a.id > 1');

		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}
                
                $catid = $this->getState('filter.catid');
		if (is_numeric($catid))
		{
			$query->where('b.cat_id = ' . (int) $catid);
		}
		


                // Filter by search in title, alias or id
                if ($search = trim($this->getState('filter.search')))
                {
                    if (stripos($search, 'id:') === 0)
                    {
                        $query->where('a.id = ' . (int) substr($search, 3));
                    }

                    else
                    {
                        $search = $db->quote('%' . $db->escape($search, true) . '%');
                        $query->where('(' . 'a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
                    }
                }



		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
                $query->order('a.id');

		//echo nl2br(str_replace('#__','jos_',(string)$query)).'<hr/>';
		return $query;

	}
    protected function populateState($ordering = null, $direction = null)
    {
        $published = $this->getUserStateFromRequest($this->context . '.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $search = $this->getUserStateFromRequest($this->context . '.search', 'filter_search');
        $this->setState('filter.search', $search);

        $catid = $this->getUserStateFromRequest($this->context . '.catid', 'filter_catid');
        $this->setState('filter.catid', $catid);

        // List state information.
        parent::populateState('a.ordering', 'asc');
    }

    
    
        public function pluginInstall(){
		jimport('joomla.installer.helper');
		jimport('joomla.filesystem.path');
		$filename = $_FILES['plugin_installer']['name'];
		$baseDir = JPATH_ROOT."/tmp/";;
		if (file_exists( $baseDir )) {
			if (is_writable( $baseDir )) {
				if (move_uploaded_file( $_FILES['plugin_installer']['tmp_name'], $baseDir . $filename )) {
				
					if (JPath::setPermissions( $baseDir . $filename )) {
						$msg = '';
					} else {
						$msg = JText::_("BLBE_UPL_PERM");
					}
				} else {
					$msg = JText::_("BLBE_UPL_MOVE");
				}
			} else {
				$msg = JText::_("BLBE_UPL_TMP");
			}
		} else {
			$msg = JText::_("BLBE_UPL_TMPEX");
		}
		if($msg != ''){
			JError::raiseError(500, $msg );
			//return false;
		} 
                
		$retval = JInstallerHelper::unpack($baseDir . $filename);
                
		if(count($retval)){
			
			
                        
                        
			$xml = JFactory::getXML($retval['dir']."/plugin.xml");
			
			
			
			if($xml){
				if(is_dir($retval['dir'])){
                                    jimport('joomla.filesystem.file');
                                    jimport('joomla.filesystem.folder');
                                    JFolder::create(JPATH_ROOT."/administrator/components/com_jbcatalog/plugins/{$xml->name}", 0755);
                                    $uploaded = JFolder::copy($retval['dir'],JPATH_ROOT."/administrator/components/com_jbcatalog/plugins/{$xml->name}", '', true);

                                    
                                    if(is_dir($retval['dir'].'/BE')){
                                        
                                        $uploaded = JFolder::copy($retval['dir'].'/BE/',JPATH_ROOT."/administrator/components/com_jbcatalog/", '', true);
                                        //var_dump($uploaded);
                                    }

                                    if(is_dir($retval['dir'].'/FE')){
                                        $uploaded = JFolder::copy($retval['dir'].'/FE/',JPATH_ROOT."/components/com_jbcatalog/", '', true);
                                    }
                                    
                                    
                                    
                                    
                                    $db = $this->getDbo();
                                    $query = "INSERT IGNORE INTO #__jbcatalog_plugins(name,title,description,version,published,type) VALUES('{$xml->name}','{$xml->title}','{$xml->description}','{$xml->version}','0','{$xml->type}')";
                                    $db->setQuery($query);
                                    $db->query();
                                    $plugin_id = $db->insertid();
                                    
                                    
                                    require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
                                    $pl = new ParsePlugins('', $plugin_id, 0);
                                    
                                    
                                    $pl->getData('install');
                                    //die();
                                }
                                
                                
			}else{
                            $msg = JText::_("BCAT_XML_FILE_NOT_FOUND");
                            JError::raiseError(500, $msg );
                        }
			
		}
	}
        
        public function pluginUninstall($cid){
            if(count($cid)){
                require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."plugins.php";
                $db = $this->getDbo();
                foreach($cid as $pid){
                    
                    $pl = new ParsePlugins('', $pid, 0);

                    $pl->getData('uninstall');
                    
                    $db->setQuery(
                            'SELECT type FROM #__jbcatalog_plugins WHERE id='.intval($pid)
                            );

                    //delete additional fields values
                    if($db->loadResult() == 'adf'){
                        $db->setQuery(
                                "DELETE v,a FROM #__jbcatalog_adf as a"
                                ." LEFT JOIN #__jbcatalog_adf_values as v"
                                ." ON a.id = v.adf_id"
                                ." WHERE a.field_type = ".intval($pid)
                                );
                        $db->execute();

                    }
                    
                    jimport('joomla.filesystem.folder');
                    //delete plugins file and sql
                    $db->setQuery("SELECT name FROM #__jbcatalog_plugins WHERE id = ".intval($pid));
                    $name = $db->loadResult();
                    if($name){
                        JFolder::delete((JPATH_ROOT."/administrator/components/com_jbcatalog/plugins/{$name}"));

                        $db->setQuery(
                                "DELETE FROM #__jbcatalog_plugins WHERE id = ".intval($pid)
                                );
                        $db->execute();
                    }
                }

            }
        }
        
        
        public function publish($cid, $value = 1)
        {
            if(count($cid)){
                $db = $this->getDbo();
                foreach($cid as $pid){
                    $db->setQuery("UPDATE #__jbcatalog_plugins SET published = '".$value."' WHERE id=".intval($pid));
                    $db->query();
                }
            }
        }
        
    
}
