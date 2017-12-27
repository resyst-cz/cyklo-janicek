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

class JBCatalogControllerPlugins extends JControllerAdmin
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
	public function getModel($name = 'Plugin', $prefix = 'JBCatalogModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

        public function install()
	{
		$model = $this->getModel($name = 'Plugins', $prefix = 'JBCatalogModel');
                $model->pluginInstall();
                $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	
	}
        public function uninstall()
	{
		$model = $this->getModel($name = 'Plugins', $prefix = 'JBCatalogModel');
                $cid = $this->input->post->get('cid', null, 'array');
                $model->pluginUninstall($cid);
                $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	
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
        
        public function publish(){

            $model = $this->getModel($name = 'Plugins', $prefix = 'JBCatalogModel');
            $cid = $this->input->post->get('cid', null, 'array');
            $task = $this->input->post->get('task', null, 'string');
            $value = ($task == 'plugins.publish')?1:0;
            $model->publish($cid, $value);
            $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			
        }
}
