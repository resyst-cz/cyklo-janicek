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


class JBCatalogControllerCategory extends JControllerForm
{
    protected $text_prefix = 'COM_JBCATALOG_CATEGORY';
    
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model	= $this->getModel('Category', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_jbcatalog&view=category' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }
    public function trash(){

            $cid  = $this->input->post->get('cid', array(), 'array');

            $model	= $this->getModel('Category', '', array());
            $model->trash($cid);
            $this->setRedirect(JRoute::_('index.php?option=com_jbcatalog&view=categories' . $this->getRedirectToListAppend(), false));


        }
}
