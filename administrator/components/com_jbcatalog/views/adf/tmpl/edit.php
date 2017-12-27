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

if(count($this->plugins['js'])){
    foreach($this->plugins['js'] as $js){
        echo $js;
    }
}
$js_sel = '';

if(count($this->plugins['js_selected_pre'])){
    foreach($this->plugins['js_selected_pre'] as $js){
        $js_sel .= $js."\n";
    }
}

if(count($this->plugins['js_selected'])){
    foreach($this->plugins['js_selected'] as $js){
        $js_sel .= $js."\n";
    }
}
?>
<script type="text/javascript">
                	
                
                function getSelDiv(obj){
                    <?php echo $js_sel;?>  
                    
                }   
                
                Joomla.submitbutton = function(task)
                {
                    if (task == 'adf.cancel' || document.formvalidator.isValid(document.id('bcat-form')))
                    {
                        Joomla.submitform(task, document.getElementById('bcat-form'));
                    }
                }
		
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jbcatalog&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="bcat-form" class="form-validate form-horizontal">

    <div class="span10 form-horizontal">

        <fieldset>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('name'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('name'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('field_type'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('field_type'); ?>
                </div>
            </div>
            
            <?php
            if(count($this->plugins["adf_extra"])){
                foreach ($this->plugins["adf_extra"] as $extr){
                    echo $extr;
                }
            }
            ?>
            
            
            <div class="control-group" id="complexdiv">
                <div class="control-label">
                    <label>
                        <?php echo $this->form->getLabel('adf_complex'); ?>
                    </label>
                </div>
                <div class="controls">
                        <?php echo $this->form->getInput('adf_complex'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('filters'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('filters'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('group_id'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('group_id'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('adf_prefix'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('adf_prefix'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('adf_postfix'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('adf_postfix'); ?>
                </div>
            </div>
            
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('adf_tooltip'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('adf_tooltip'); ?>
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
<script>
    getSelDiv(document.getElementById('bcat-form').jform_field_type);
</script>    