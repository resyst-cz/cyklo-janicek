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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$catfilt[] = JHTML::_('select.option',  "0", JText::_('JGLOBAL_USE_GLOBAL'), 'id', 'name' );
$catfilt[] = JHTML::_('select.option',  "1", JText::_('JHIDE'), 'id', 'name' );
$catfilt[] = JHTML::_('select.option',  "2", JText::_('JSHOW'), 'id', 'name' );
$catjs = JHTML::_('select.genericlist',   $catfilt, 'catfilter[]', 'class="inputboxsel" size="1"', 'id', 'name', 0 );
$catjs  = preg_replace('~[\r\n]+~', '', $catjs);
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'category.cancel' || document.formvalidator.isValid(document.id('fileupload')))
        {
            <?php echo $this->form->getField('descr')->save(); ?>
            Joomla.submitform(task, document.getElementById('fileupload'));
        }
    }
    
    function jsAddFieldGroup(){
        var groupid = jQuery('#grouplist').val();
        if(groupid == 0){
            return false;
        }
        jQuery.ajax({
            url: "<?php echo juri::base()?>" + "index.php?option=com_jbcatalog&task=adfs.getAdfGroupDetails&tmpl=component&groupid="+groupid+"&catid=<?php echo $this->item->id?>",
            data: {'<?php echo JSession::getFormToken()?>':'1'}
        }).done(function(data) {
                jQuery('.jbgroup'+groupid).remove();
                jQuery("#jb_addgroupcat > tbody").append(data);
                jQuery("#grouplist option[value='"+groupid+"']").remove();
                jQuery('#grouplist').chosen();
                jQuery('#grouplist').trigger("liszt:updated");
        });
    }
    
    function jsRemoveFieldGroup(gid, gname){
        jQuery('.jbgroup'+gid).remove();
        jQuery("#grouplist").append('<option value="'+gid+'">'+gname+'</option>');
        jQuery('#grouplist').chosen();
        jQuery('#grouplist').trigger("liszt:updated");
    }
    
    function jsAddADField(){
        var adfid = jQuery('#adflist').val();
        if(adfid == 0){
            return false;
        }    
        var adfname = jQuery("#adflist option:selected").text();
        var data = '<tr id="jbadftr_'+adfid+'" style="background-color:#eee;font-style:italic;"><td><input type="hidden" name="jbgroups_adf[]" value="0" /><input type="hidden" name="jbgrfields[]" value="'+adfid+'" /><a href="javascript:void(0);" title="<?php echo JText::_('COM_JBCATALOG_REMOVE')?>" onClick="javascript:jsRemoveADf(\''+adfid+'\',\''+adfname+'\');"><img src="<?php echo JURI::base()?>components/com_jbcatalog/images/publish_x.png" title="<?php echo JText::_('COM_JBCATALOG_REMOVE')?>" /></a></td>';
        data += '<td>'+adfname+'</td><td><input type="checkbox" value="1" name="showlist['+adfid+']"  /></td>';
        data += '<td><?php echo $catjs;?></td></tr>';
        jQuery("#jb_addadfcat > tbody").append(data);
        
        jQuery("#adflist option[value='"+adfid+"']").remove();
        jQuery('#adflist').chosen();
        jQuery('#adflist').trigger("liszt:updated");

        
    }    
    
    function jsRemoveADf(gid, gname){

        jQuery("#adflist").append('<option value="'+gid+'">'+gname+'</option>');
        jQuery('#adflist').chosen();
        jQuery('#adflist').trigger("liszt:updated");
        jQuery('#jbadftr_'+gid).remove();
    }
    
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jbcatalog&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="fileupload" class="form-validate form-horizontal" enctype="multipart/form-data">

    <div class="span10 form-horizontal">

        <fieldset>
            <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JBCATALOG_CATEGORY_DETAILS', true)); ?>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('title'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('title'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('alias'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('alias'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('parent_id'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('parent_id'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('image'); ?></div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('descr'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('descr'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('id'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('id'); ?>
                </div>
            </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'fields', JText::_('COM_JBCATALOG_ADDFIELDS', true)); ?>
            <div>
                <table id="jb_addgroupcat" class="table" style="width:auto;">
                    <thead>
                        <tr>
                            <th colspan="2"><?php echo JText::_('COM_JBCATALOG_CAT_ADF_GROUPNAME');?></th>
                            <th><?php echo JText::_('COM_JBCATALOG_CAT_SHOWONLIST');?></th>
                            <th><?php echo JText::_('COM_JBCATALOG_DISPLAY_FILTER');?></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($this->grval)){
                            foreach ($this->grval as $gval){
                                echo '<tr class="jbgroup'.$gval->id.'" style="background-color:#eee;font-size:130%;">';
                                echo '<td><a href="javascript:void(0);" title="'.JText::_('COM_JBCATALOG_REMOVE').'" onClick="javascript:jsRemoveFieldGroup(\''.$gval->id.'\', \''.$gval->name.'\');"><img src="'.JURI::base().'components/com_jbcatalog/images/publish_x.png" title="'.JText::_('COM_JBCATALOG_REMOVE').'" /></a></td>';
                                echo '<td colspan="3"><input type="hidden" name="jbgroups[]" value="'.$gval->id.'" />'.$gval->name.'</td>';
                                echo '</tr>';
                                for($i=0; $i < count($gval->adfs); $i++){
                                    echo '<tr class="jbgroup'.$gval->id.'" style="background-color:#fefefe;font-style:italic;">';
                                    echo '<td><input type="hidden" name="jbgroups_adf[]" value="'.$gval->id.'" /><input type="hidden" name="jbgrfields[]" value="'.$gval->adfs[$i]->id.'" /></td>';
                                    echo '<td>'.$gval->adfs[$i]->name.'</td>';
                                    echo '<td><input type="checkbox" value="1" name="showlist['.$gval->adfs[$i]->id.']" '.($gval->adfs[$i]->listview?' checked="checked"':'').' /></td>';
                                    echo '<td>';
                                    echo JHTML::_('select.genericlist',   $catfilt, 'catfilter[]', 'class="inputboxsel" size="1"', 'id', 'name', $gval->adfs[$i]->filtered, 'catfilter_'.$gval->adfs[$i]->id );
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        
                        <tr>
                            <td colspan="2">
                                <?php echo $this->grouplist;?>
                            </td>
                            <td colspan="2">
                                <input type="button" value="<?php echo JText::_('COM_JBCATALOG_ADDNEW_ITEMS');?>" onclick="jsAddFieldGroup();">
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <br />
                <hr />
                <br >
                <table id="jb_addadfcat" class="table" style="width:auto;">
                    <thead>
                        <tr>
                            <th colspan="2"><?php echo JText::_('COM_JBCATALOG_CAT_ADF_FIELDNAME');?></th>
                            <th><?php echo JText::_('COM_JBCATALOG_CAT_SHOWONLIST');?></th>
                            <th><?php echo JText::_('COM_JBCATALOG_DISPLAY_FILTER');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($this->adfval)){
                            foreach ($this->adfval as $val){
                                echo '<tr id="jbadftr_'.$val->id.'" style="background-color:#eee;font-style:italic;">';
                                echo '<td><input type="hidden" name="jbgroups_adf[]" value="0" /><input type="hidden" name="jbgrfields[]" value="'.$val->id.'" /><a href="javascript:void(0);" title="'.JText::_('COM_JBCATALOG_REMOVE').'" onClick="javascript:jsRemoveADf(\''.$val->id.'\',\''.$val->name.'\');"><img src="'.JURI::base().'components/com_jbcatalog/images/publish_x.png" title="'.JText::_('COM_JBCATALOG_REMOVE').'" /></a></td>';
                                echo '<td>'.$val->name.'</td>';
                                echo '<td><input type="checkbox" value="1" name="showlist['.$val->id.']" '.($val->listview?' checked="checked"':'').'  /></td>';
                                echo '<td>';
                                echo JHTML::_('select.genericlist',   $catfilt, 'catfilter[]', 'class="inputboxsel" size="1" id="catfilter_'.$val->id.'"', 'id', 'name', $val->filtered );
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>    

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <?php echo $this->adflist;?>
                            </td>
                            <td colspan="2">
                                <input type="button" value="<?php echo JText::_('COM_JBCATALOG_ADDNEW_ITEMS');?>" onclick="jsAddADField();">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
                
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php echo JHtml::_('bootstrap.endTabSet'); ?>




        </fieldset>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
    <!-- End Newsfeed -->
    <!-- Begin Sidebar -->
    <div class="span2">
        <h4><?php echo JText::_('JDETAILS');?></h4>
        <hr />
        <fieldset class="form-vertical">

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('published'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('published'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('language'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('language'); ?>
                </div>
            </div>
        </fieldset>
    </div>
    <!-- End Sidebar -->
</form>


