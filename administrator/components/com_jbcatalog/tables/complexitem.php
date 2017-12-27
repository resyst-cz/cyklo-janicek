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

class JBCatalogTableComplexItem extends JTable
{

    public function __construct(&$db)
    {
        parent::__construct('#__jbcatalog_complex_item', 'id', $db);

    }

}
