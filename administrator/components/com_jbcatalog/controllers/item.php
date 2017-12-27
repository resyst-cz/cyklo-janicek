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

class JBCatalogControllerItem extends JControllerForm
{

    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model	= $this->getModel('Item', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_jbcatalog&view=item' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }
    
    public function showAdfAjax(){
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        // Set the model
        $model	= $this->getModel('Item', '', array());
        //$cats = $_POST['catssel'];
        $item_id = intval($_POST['item_id']);
        header('Content-type: text/html; charset=UTF-8');
        if(!empty($_POST['catssel'])){
            echo $model->getItemAdfs($_POST['catssel'], $item_id);
        }
        // Close the application
        JFactory::getApplication()->close();
    }
    
}
