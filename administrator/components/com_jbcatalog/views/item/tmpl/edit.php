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
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

if(count($this->plugins['js'])){
    foreach($this->plugins['js'] as $js){
        echo $js;
    }
}
?>
<script>
     Joomla.submitbutton = function(task)
                {
                    if (task == 'item.cancel' || document.formvalidator.isValid(document.id('fileupload')))
                    {
                        <?php echo $this->form->getField('descr')->save(); ?>
                        Joomla.submitform(task, document.getElementById('fileupload'));
                    }
                }
</script>    


<form action="<?php echo JRoute::_('index.php?option=com_jbcatalog&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="fileupload" class="form-validate form-horizontal" enctype="multipart/form-data">

    <div class="span10 form-horizontal">

        <fieldset>
            <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JBCATALOG_ITEM_DETAILS', true)); ?>
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
                    <?php echo $this->form->getLabel('catid'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('catid'); ?>
                </div>
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

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'options', JText::_('COM_JBCATALOG_ADF_LABEL', true)); ?>
            
            <script>
                function getItemAdfs(){
                    
                
                    jQuery.ajax({
                        type: "POST",
                        url: "index.php?option=com_jbcatalog&task=item.showAdfAjax&tmpl=component",
                        data: {'<?php echo JSession::getFormToken()?>':'1',
                            'catssel':jQuery('#jform_catid').val(),
                            'item_id':'<?php echo (int) $this->item->id;?>'
                            },
                        dataType: "html"
                      }).done(function(data) {
                        jQuery('#items_adf_div').html(data);
                        jQuery('#items_adf_div .inputboxsel').chosen();
                        jQuery('#items_adf_div .inputboxsel').trigger("liszt:updated");
                        
                        
                        ///
                        jQuery('#items_adf_div .radio').addClass('btn');
		jQuery("#items_adf_div label.radio:not(.active)").click(function()
		{
			var label = jQuery(this);
                        
			var input = jQuery('#' + label.attr('for'));

			if (!input.prop('checked')) {
				label.closest('#items_adf_div').find("label.radio").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-primary');
				} else if (input.val() == 0) {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		jQuery("#items_adf_div label.radio input[checked=checked]").each(function(el,th)
		{
			
                        if (th.value == '') {
				jQuery("label[for=" + th.id + "]").addClass('active btn-primary');
			} else if (th.value == 0) {
				jQuery("label[for=" + th.id + "]").addClass('active btn-danger');
			} else {
				jQuery("label[for=" + th.id + "]").addClass('active btn-success');
			}
		});
                jQuery('#items_adf_div .btn-group').hide();
                jQuery('#items_adf_div .controls div.controls').css("margin","0px");
                
                        ///
                        
                        if (typeof WFEditor !== 'undefined') {
                                    WFEditor.init(WFEditor.settings);
                                } else if (typeof tinyMCE !== 'undefined') {
                                   tinyMCE.init({mode : "specific_textareas",editor_selector : "mceEditors"});
                                } else if (typeof CKEDITOR !== 'undefined'){
                                    //CKEDITOR.remove('extraf_1');
                                    CKEDITOR.replace( 'extraf_1');
                                }
                        jQuery( "input[name^='caldr[]']" ).each(function(){
                            Calendar.setup({
				inputField: 'extraf_'+jQuery(this).val(),
				ifFormat: "%Y-%m-%d",
				button: 'extraf_' + jQuery(this).val() + '_img',
				align: "Tl",
				singleClick: true,
				firstDay: ''
				});
                        });
                        jQuery(".numericOnly").keypress(function (e) {
                            if (String.fromCharCode(e.keyCode).match(/[^0-9\.]/g)) return false;
                        });
                      });
                }
                getItemAdfs();
            </script>    
            <div id="items_adf_div">
                
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
    <?php
    require_once JPATH_COMPONENT.'/helpers/images.php';
    echo ImagesHelper::loaderUI($this->item->images);
    ?>
</form>
