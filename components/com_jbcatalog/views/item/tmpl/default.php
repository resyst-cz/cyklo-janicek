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

//JHtml::_('behavior.caption');
//JHtml::_('bootstrap.tooltip');
$doc = JFactory::getDocument();
require_once JPATH_COMPONENT.'/helpers/images.php';
echo JBCatalogImages::includeFancy($this->document);
/*dropbox*/

$doc->addStyleSheet('components/com_jbcatalog/css/smoothness/jquery-ui-1.10.3.custom.css');
$doc->addStyleSheet('components/com_jbcatalog/css/jquery.qtip.css');
$doc->addStyleSheet('components/com_jbcatalog/css/newproperties.css');
?>

<script type='text/javascript'>
    var adminMode = '<?php echo $this->isAdmin?true:false;?>';
    var dataFromServer = <?php echo $this->pdata;?>;//data from dropbox

</script>
<script>
    function js_AdfGr(divid){
        var blstat = jQuery('#'+divid).css('display');
        if(blstat == 'block'){
            jQuery('#'+divid).hide();
        }else{
            jQuery('#'+divid).show();
        }
    }
    
</script>    
<div>
    <div class="successDIV"><?php echo JText::_("COM_JBCATALOG_SAVEDSUCC");?></div>
<!--<div>
	<h3><?php echo $this->item->title?></h3>
</div>-->
<div class="bc_header drop dropHeader">
    <div class="dragText" data-index="index1"><h3><?php echo $this->item->title?></h3></div>
</div>
<?php
if(isset($this->item->adftab) && count($this->item->adftab)){
    echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details'));

    echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JBCATALOG_ITEM_TAB', true));
}
?>
           
<div class="bc_mainbl">
    <div class="bc_mainbl_left drop dropLeft">
        <div class='bc_img_block dragImage' data-index='index2'>
        <?php
        if(isset($this->item->images) && count($this->item->images)){
            echo "<div id='img_t'>".JBCatalogImages::getClickableImg($this->item->images[0])."</div>";
        }
        for($i=1; $i < count($this->item->images);$i++){
             echo "<div class='item_miniimg_div'>".JBCatalogImages::getClickableImg($this->item->images[$i])."</div>";
        }
        ?>
        <div style='clear:both;margin-bottom:3px;'></div>
        </div>
    </div>    

    <div class="bc_maibl_right drop dropRight">
       <div class="bc_descr_block dragText" data-index="index3">
            <?php echo $this->item->descr;?>
        </div>
	</div>
</div>
	<div class="bc_footer drop dropFooter">
		
                        <?php
                        for($i = 0; $i < count($this->item->adf); $i++){
                            $show_adftitle = false;
                            if(isset($this->item->adf[$i]->adf) && count($this->item->adf[$i]->adf)){
                                echo '<div id="adf_view_head_'.($this->item->adf[$i]->id).'" class="bc_adf dragParam" data-index="index'.(4+$i).'">';     
                                echo '<h3 class="adfgroup_name_h3" onclick="javascritp:js_AdfGr(\'adf_view_'.($this->item->adf[$i]->id).'\');">'.$this->item->adf[$i]->name.'</h3>';
                                echo '<div class="adf_view_div" id="adf_view_'.($this->item->adf[$i]->id).'">';
                                for($j = 0; $j < count($this->item->adf[$i]->adf); $j++){
                                    $adf = $this->item->adf[$i]->adf[$j];
                                    if($adf[2]){
                                        $show_adftitle = true;
                                        $adcc = '';
                                        if($adf[1]){
                                            $adcc = 'class="hasTooltip" title="'.htmlspecialchars($adf[1]).'"';
                                        }
                                        echo "<div class='adf_clearfx_div'><label ".$adcc.">".$adf[0].":</label><div>".$adf[2]."</div></div>";
                                    }
                                    
                                }

                                echo '</div>';
                                if($this->item->adf[$i]->displayopt == '0'){
                                    echo '<script>jQuery("#adf_view_'.$this->item->adf[$i]->id.'").hide();</script>';
                                }
                                
                                echo "</div>";
                                if(!$show_adftitle){
                                    echo '<script>jQuery("#adf_view_head_'.$this->item->adf[$i]->id.'").hide();</script>';
                                }
                            }
                        }
                        $index = 4+$i+1;
                        ?>
            
            <?php
                        for($i = 0; $i < count($this->item->adf_singles); $i++){
                            $show_adftitle = false;
                            if(isset($this->item->adf_singles[$i]->adf) && count($this->item->adf_singles[$i]->adf)){
                                echo '<div id="adfsingle_view_head_'.($this->item->adf_singles[$i]->id).'" class="bc_adf dragParam" data-index="index'.($index).'">';     
                                echo '<div class="adf_view_div" id="adfsingle_view_'.($this->item->adf_singles[$i]->id).'">';
                                for($j = 0; $j < count($this->item->adf_singles[$i]->adf); $j++){
                                    $adf = $this->item->adf_singles[$i]->adf[$j];
                                    if($adf[2]){
                                        $show_adftitle = true;
                                        $adcc = '';
                                        if($adf[1]){
                                            $adcc = 'class="hasTooltip" title="'.htmlspecialchars($adf[1]).'"';
                                        }
                                        echo "<div class='adf_clearfx_div'><label ".$adcc.">".$adf[0].":</label><div>".$adf[2]."</div></div>";
                                    }
                                    
                                }

                                echo '</div>';
                                
                                
                                echo "</div>";
                                $index ++;
                            }
                        }
                        
                        ?>
		
		
    </div>    
     <?php 
     if(isset($this->item->adftab) && count($this->item->adftab)){
        echo JHtml::_('bootstrap.endTab');
     }
     ?>

<?php
if(isset($this->item->adftab) && count($this->item->adftab)){
    for($i = 0; $i < count($this->item->adftab); $i++){
        
        if(isset($this->item->adftab[$i]->adf) && count($this->item->adftab[$i]->adf)){
            echo JHtml::_('bootstrap.addTab', 'myTab', 'tab'.$this->item->adftab[$i]->id, $this->item->adftab[$i]->name);
           
            echo '<div class="adf_view_div dragParam" data-index="index'.($index+$i).'">';
            for($j = 0; $j < count($this->item->adftab[$i]->adf); $j++){
                $adf = $this->item->adftab[$i]->adf[$j];
                if($adf[2]){
                    $adcc = '';
                    if($adf[1]){
                        $adcc = 'class="hasTooltip" title="'.$adf[1].'"';
                    }
                    echo "<div class='adf_clearfx_div'><label ".$adcc.">".$adf[0].":</label><div>".$adf[2]."</div></div>";
                }     
                    
            }
            
            echo '</div>';
            echo JHtml::_('bootstrap.endTab');
        }
    }
}
?>



 <?php 
 if(isset($this->item->adftab) && count($this->item->adftab)){
    echo JHtml::_('bootstrap.endTabSet');
 }   
 ?>
<?php
if($this->isAdmin){
?>
 <!---START dropbox-->
    <div class="parameters">
        <div>
            <a href="#drag">Start drag</a>
        </div>
        <div>
            <a href="#showParams">Show params</a>
        </div>
    </div>
    <input type="button" name="save" value="Save" id="save"/>
    <input type="button" name="cancel" value="Cancel" id="cancel"/>
    <div id="modal" title="Edit options">
    </div>
    <!-- underscore parts -->
    <script type="text/template" id="modalTemplate">
        <div id="tabs">
            <ul class="tabs"><%= tabs%></ul>
            <%= content%>
        </div>
    </script>
    <script type="text/template" id="inputTemplate">
        <tr>
            <td><label for="<%= name%>"><%= labelText%></label></td>
            <td><%= element%></td>
        </tr>
    </script>
<?php } ?>    

<script src="components/com_jbcatalog/libraries/dragbox/js/jquery-ui-1.10.3.custom.js"></script>
<script src="components/com_jbcatalog/libraries/dragbox/js/underscore.js"></script>
<script src="components/com_jbcatalog/libraries/dragbox/js/jquery.qtip.min.js"></script>
<script src="components/com_jbcatalog/libraries/dragbox/js/drag.js"></script>

</div>


