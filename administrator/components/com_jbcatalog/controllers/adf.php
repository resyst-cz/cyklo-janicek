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

class JBCatalogControllerAdf extends JControllerForm
{
    protected $text_prefix = 'COM_JBCATALOG_ADF';
    
    public function trash(){
        $cid  = $this->input->post->get('cid', array(), 'array');

        $model	= $this->getModel('Adf', '', array());
        $model->trash($cid);
       $this->setRedirect(JRoute::_('index.php?option=com_jbcatalog&view=adfs' . $this->getRedirectToListAppend(), false));

        
    }
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model	= $this->getModel('Adf', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_jbcatalog&view=adf' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }
}
