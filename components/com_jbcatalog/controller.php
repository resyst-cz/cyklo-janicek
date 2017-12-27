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

class JBCatalogController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', 'categories');
		$this->input->set('view', $vName);

		$user = JFactory::getUser();

		$safeurlparams = array('catid' => 'INT', 'id' => 'INT', 'cid' => 'ARRAY', 'year' => 'INT', 'month' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT',
			'showall' => 'INT', 'return' => 'BASE64', 'filter' => 'STRING', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'filter-search' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD');

		parent::display($cachable, $safeurlparams);

		return $this;
	}
        public function rating($cachable = false, $urlparams = false)
	{
            $model = $this->getModel_es('Item');
            $value = $this->input->getInt('rat', 0);
            $id = $this->input->getInt('id', 0);
            $adf_id = $this->input->getInt('adfid', 0);
            $model->rate_item($id, $adf_id, $value);
            
        }
        
        public function getModel_es($name = 'form', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
        
        public function save_position(){
            $db = JFactory::getDbo();
            
            $db->setQuery("UPDATE #__jbcatalog_options SET data = '".$db->escape($_POST['data'])."' WHERE name='item_position'");
            $db->execute();
            die();
        }
        
        
}
