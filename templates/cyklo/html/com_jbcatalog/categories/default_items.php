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
?>
<div class="catmain_div">
    <?php
    for($i=0;$i<count($this->items);$i++){
        echo "<div class='catimg_div'>";
        if(isset($this->items[$i]->images) && is_file(JPATH_ROOT.'/'.$this->items[$i]->images)){
            echo "<a href='".JRoute::_("index.php?option=com_jbcatalog&view=category&id=".$this->items[$i]->id)."'><img src='".JURI::base().($this->items[$i]->images)."'></a>";
        }
        echo "</div>";
        echo "<div class='catinfo_div'><a href='".JRoute::_("index.php?option=com_jbcatalog&view=category&id=".$this->items[$i]->id)."'>".$this->items[$i]->title."</a>";
        echo "<div class='catdescr_div'>".$this->items[$i]->descr."</div></div>";
        echo "<div style='clear:both;margin-bottom:3px;'></div>";
    }
    ?>
</div>
<div style="padding-top:15px; text-align: center;">
    <form method="post">
        Zobrazit <?php echo $this->pagination->getLimitBox(); ?> kategorií na stránce
    </form>
</div>
<p class="counter"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>