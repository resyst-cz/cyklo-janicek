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

class JBCatalogControllerPlugin extends JControllerForm
{
    protected $text_prefix = 'COM_JBCATALOG_PLUGIN';
    
    public function trash(){
        $cid  = $this->input->post->get('cid', array(), 'array');

        $model	= $this->getModel('Plugin', '', array());
        $model->trash($cid);
       $this->setRedirect(JRoute::_('index.php?option=com_jbcatalog&view=plugin' . $this->getRedirectToListAppend(), false));

        
    }
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model	= $this->getModel('Plugin', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_jbcatalog&view=plugin' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }
}
