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

    protected $default_view = 'categories';

	public function display($cachable = false, $urlparams = false)
	{

		$view   = $this->input->get('view', 'categories');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'category' && $layout == 'edit' && !$this->checkEditId('com_jbcatalog.edit.category', $id)) {

			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_jbcatalog&view=categories', false));

			return false;
		}


		parent::display();
                //
                require_once JPATH_COMPONENT.'/helpers/footer.php';
                FooterCHelper::footHTML();
                
		return $this;
	}
}
