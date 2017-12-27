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
require_once JPATH_COMPONENT.'/helpers/images.php';

if(!empty($this->plugins['js'])){
    foreach($this->plugins['js'] as $js){
        echo $js;
    }
}
?>
<?php if(!empty($this->items)) {
    $filtr = array();
    foreach ($this->items as $produkt) {
        if (isset($produkt->resyst_kategorie)) {
            $filtr[$produkt->resyst_kategorie['id']] = $produkt->resyst_kategorie['kategorie'];
        }
    }
    if (!empty($filtr)) {
    ?>
        <div class="filtr-box row">
            <div class="span8">
                <div class="filtr-label">Kategorie</div>
                <div class="filtr-tags">
                    <span class="kategorie_tag" data-filtr_kategorie_id="-1">Vše</span>
                    <?php foreach ($filtr as $key=>$kat) { ?>
                        <span class="kategorie_tag" data-filtr_kategorie_id="<?php echo $key; ?>"><?php echo $kat; ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="span4 text-right">
                <input type="text" id="produkty-search" placeholder="Hledaný výraz" />
                <img id="produkty-search-btn" src="images/icon_search.png" alt="Hledat" data-toggle="tooltip" data-placement="right" title="Hledat" />
            </div>
        </div>
    <?php
    }
}
?>
<div class="catmain_div">
    <?php echo $this->loadTemplate('items_table'); ?>
</div>
<?php if(!empty($this->items)){ ?>
    <div class="products-footer">
        <form method="post">
            Zobrazit <?php echo $this->pagination->getLimitBox(); ?> produktů na stránce
        </form>
        <p class="counter"><?php echo $this->pagination->getPagesCounter(); ?> </p>
        <div class="pagination">
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    </div>
<?php } ?>