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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');

echo JLayoutHelper::render('joomla.jbcatalog.categories_default', $this);

?>
<div class="catmain_div">
    <?php
    for($i=0;$i<count($this->categories);$i++){
        echo "<div class='catimg_div'>";
        if(isset($this->categories[$i]->images) && is_file(JPATH_ROOT.'/'.$this->categories[$i]->images)){
            echo "<a href='".JRoute::_("index.php?option=com_jbcatalog&view=category&id=".$this->categories[$i]->id)."'><img src='".JURI::base().($this->categories[$i]->images)."'></a>";
        }
        echo "</div>";
        echo "<div class='catinfo_div'><a href='".JRoute::_("index.php?option=com_jbcatalog&view=category&id=".$this->categories[$i]->id)."'>".$this->categories[$i]->title."</a>";
        echo "<div class='catdescr_div'>".$this->categories[$i]->descr."</div></div>";
        echo "<div style='clear:both;margin-bottom:3px;'></div>";
    }
    ?>
</div>
<?php
echo $this->loadTemplate('items');
?>

