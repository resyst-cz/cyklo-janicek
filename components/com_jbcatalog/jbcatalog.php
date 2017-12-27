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
$document = JFactory::getDocument();

$document->addStyleSheet(JURI::root(true).'/components/com_jbcatalog/css/jbcatalog.css');

$controller	= JControllerLegacy::getInstance('JBCatalog');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
