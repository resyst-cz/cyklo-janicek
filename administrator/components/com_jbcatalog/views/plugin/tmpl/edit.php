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

?>
<script type="text/javascript">
                	
                
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
                    <?php echo $this->form->getLabel('id'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('id'); ?>
                </div>
            </div>
            
            <?php

                if(count($this->plugins['settings'])){
                    foreach($this->plugins['settings'] as $pl){
                        ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $pl['label']; ?>
                            </div>
                            <div class="controls">
                                <?php echo $pl['data']; ?>
                            </div>
                        </div>
                        <?php
                    }
                }
            ?>


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

        </fieldset>
    </div>
    <!-- End Sidebar -->
</form>
 