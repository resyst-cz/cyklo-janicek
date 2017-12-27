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

if(count($this->plugins['js'])){
    foreach($this->plugins['js'] as $js){
        echo $js;
    }
}
?>
<script>
    function hideFltrs(){
        if(jQuery('#divfiltr').css('display') == 'block'){
            jQuery('#divfiltr').hide();
        }else{
            jQuery('#divfiltr').show();
        }
        
    }
    
</script>   

<?php
$sp = ((count($this->items) || $this->filtervar != null) && count($this->filters))?'':' style="display:none;"';
?>
<div <?php echo $sp;?> class="filtershead" onclick="javascript:hideFltrs();"><h5><?php echo JText::_("COM_JBCATALOG_FILTERS");?></h5></div>
<div <?php echo $sp;?> id="divfiltr">
    <?php
    if(count($this->filters)){
    ?>
    <div id="divfiltr_area">
    <form action="<?php echo JRoute::_("index.php?option=com_jbcatalog&view=category&id=".$this->catid);?>" method="post">
        <?php

                foreach($this->filters as $filtr){
                    $adcc = '';
                    if($filtr[1]){
                        $adcc = 'class="hasTooltip" title="'.htmlspecialchars($filtr[1]).'"';
                    }
                        echo '<div class="bcfilters"><label '.$adcc.'>'.$filtr[0].":</label><div>".$filtr[2]."</div></div>";
                    }
                ?>
                
                <div class="bcfilters">
                    <input type="submit" name="apply_filter" value="<?php echo JText::_("COM_JBCATALOG_FILTER_APPLY");?>" />
                    <input type="submit" name="clear_filter" value="<?php echo JText::_("COM_JBCATALOG_FILTER_CLEAR");?>" />
                </div>
                <?php
            
        ?>
        
    </form>
    </div>    
    <?php
    }
    ?>
</div>
<div class="catmain_div">
    <?php
    echo $this->loadTemplate('items_table');
    ?>
    
</div>
<div style="padding-top:15px; text-align: center;">
    <form method="post">
        
        <?php 
        if(count($this->items)){
            echo $this->pagination->getLimitBox();
        }
        ?>
        
    </form>
    
</div>
<p class="counter"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
<div class="pagination">

	
	<?php echo $this->pagination->getPagesLinks(); ?>
  
</div>
    