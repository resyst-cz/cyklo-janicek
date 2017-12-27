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

class JBCatalogControllerAdfs extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unsetDefault',	'setDefault');
	}

	/**
	 * Proxy for getModel
	 * @since   1.6
	 */
	public function getModel($name = 'Adf', $prefix = 'JBCatalogModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	public function saveorder()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the arrays from the Request
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder))
		{
			parent::saveorder();
		}
		else
		{
			// Nothing to reorder
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return true;
		}
	}

	public function saveOrderAjax()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder))
		{
			// Get the model
			$model = $this->getModel();
			// Save the ordering
			$return = $model->saveorder($pks, $order);
			if ($return)
			{
				echo "1";
			}
		}
		// Close the application
		JFactory::getApplication()->close();
	}
        
        public function getAdfGroupDetails(){
            $groupid = $this->input->getInt('groupid', 0);
            $catid = $this->input->getInt('catid', 0);
            if(!$groupid){
                return '';
            }
            
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('a.id, a.name')
                    ->from('#__jbcatalog_adfgroup AS a')
                    ->where('a.published = "1"')
                    ->where('a.id = '.$groupid);

            $db->setQuery($query);
            $group = $db->loadObject();
            
            $query = $db->getQuery(true)
                    ->select('a.id, a.name, c.listview')
                    ->from('#__jbcatalog_adf AS a')
                    ->innerjoin('#__jbcatalog_adf_ingroups AS i ON a.id = i.adfid')
                    ->leftjoin('#__jbcatalog_category_adfs as c ON (c.adfs_id = a.id AND c.cat_id = '.intval($catid).')')    
                 
                    //->where('a.id = i.adfid')
                    ->where('i.groupid = '.$groupid)
                    ->where('a.published = "1"');
            $db->setQuery($query);
            $adfs = $db->loadObjectList();
            
            $catfilt[] = JHTML::_('select.option',  "0", JText::_('JGLOBAL_USE_GLOBAL'), 'id', 'name' );
            $catfilt[] = JHTML::_('select.option',  "1", JText::_('JHIDE'), 'id', 'name' );
            $catfilt[] = JHTML::_('select.option',  "2", JText::_('JSHOW'), 'id', 'name' );
            
            
            echo '<tr class="jbgroup'.$group->id.'" style="background-color:#eee;font-size:130%;">';
            echo '<td><a href="javascript:void(0);" title="'.JText::_('COM_JBCATALOG_REMOVE').'" onClick="javascript:jsRemoveFieldGroup(\''.$group->id.'\', \''.$group->name.'\');"><img src="'.JURI::base().'components/com_jbcatalog/images/publish_x.png" title="'.JText::_('COM_JBCATALOG_REMOVE').'" /></a></td>';
            echo '<td colspan="3"><input type="hidden" name="jbgroups[]" value="'.$group->id.'" />'.$group->name.'</td>';
            echo '</tr>';
            for($i=0; $i < count($adfs); $i++){
                echo '<tr class="jbgroup'.$group->id.'" style="background-color:#fefefe;font-style:italic;">';
                echo '<td><input type="hidden" name="jbgroups_adf[]" value="'.$group->id.'" /><input type="hidden" name="jbgrfields[]" value="'.$adfs[$i]->id.'" /></td>';
                echo '<td>'.$adfs[$i]->name.'</td>';
                echo '<td><input type="checkbox" value="1" name="showlist['.$adfs[$i]->id.']" '.($adfs[$i]->listview?' checked="checked"':'').' /></td>';
                echo '<td>';
                echo JHTML::_('select.genericlist',   $catfilt, 'catfilter[]', 'class="inputboxsel" size="1"', 'id', 'name', 0 );
                echo '</td>';
                
                echo '</tr>';
            }
            exit();
            
            //return array("id" => $group->id, "name" => $group->name, "adf" => $adfs);
            
        }
        
        
}
