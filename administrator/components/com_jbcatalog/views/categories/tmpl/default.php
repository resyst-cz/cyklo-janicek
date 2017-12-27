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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$user		= JFactory::getUser();
$app		= JFactory::getApplication();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$canOrder	= $user->authorise('core.edit.state',	'com_menus');
$saveOrder 	= ($listOrder == 'a.lft' && $listDirn == 'asc');
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_jbcatalog&task=categories.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
$sortFields = $this->getSortFields();
$assoc		= isset($app->item_associations) ? $app->item_associations : 0;
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jbcatalog');?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_JBCATALOG_FILTER_SEARCH_DESC');?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_JBCATALOG_CATS_SEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_JBCATALOG_CATS_SEARCH_FILTER'); ?>" />
            </div>
            <div class="btn-group pull-left hidden-phone">
                <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button class="btn hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                    <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                    <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                    <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                </select>
            </div>
        </div>
		<div class="clearfix"> </div>

		<table class="table table-striped" id="itemList">
            <thead>
            <tr>
                <th width="1%" class="hidden-phone">
                    <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                </th>
                <th width="1%" class="hidden-phone">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>
                <th width="1%" class="nowrap center">
                    <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                </th>
                <th class="title">
                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th class="title center" width="15%"> 
                    <?php echo JText::_('COM_JBCATALOG_CATEGORY_ITEMS');?>
                </th>        
                <th width="1%" class="nowrap hidden-phone">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="15">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>

            <tbody>
            <?php
            $originalOrders = array();

            foreach ($this->items as $i => $item) :
                $orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);

                // Get the parents of item for sorting
                if ($item->level > 1)
                {
                    $parentsStr = "";
                    $_currentParentId = $item->parent_id;
                    $parentsStr = " ".$_currentParentId;
                    for ($j = 0; $j < $item->level; $j++)
                    {
                        foreach ($this->ordering as $k => $v)
                        {
                            $v = implode("-", $v);
                            $v = "-" . $v . "-";
                            if (strpos($v, "-" . $_currentParentId . "-") !== false)
                            {
                                $parentsStr .= " " . $k;
                                $_currentParentId = $k;
                                break;
                            }
                        }
                    }
                }
                else
                {
                    $parentsStr = "";
                }
                ?>
            <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent_id;?>" item-id="<?php echo $item->id?>" parents="<?php echo $parentsStr?>" level="<?php echo $item->level?>">
                <td class="order nowrap center hidden-phone">
                    <?php //if ($canChange) :
                    $disableClassName = '';
                    $disabledLabel	  = '';
                    if (!$saveOrder) :
                        $disabledLabel    = JText::_('JORDERINGDISABLED');
                        $disableClassName = 'inactive tip-top';
                    //endif; ?>
                    <span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
							<i class="icon-menu"></i>
						</span>
                    <?php else : ?>
                    <span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
                    <?php endif; ?>
                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" />
                </td>
                <td class="center hidden-phone">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td class="center">
                    <?php //echo JHtml::_('MenusHtml.Menus.state', $item->published, $i, $canChange, 'cb');
                    $states	= array(
                        1	=> array(
                            'unpublish',
                            '',
                            'JTOOLBAR_UNPUBLISH',
                            '',
                            false,
                            'publish',
                            'publish'
                        ),
                        0	=> array(
                            'publish',
                            '',
                            'JTOOLBAR_PUBLISH',
                            '',
                            false,
                            'unpublish',
                            'unpublish'
                        ));
                    echo JHtml::_('jgrid.state', $states, $item->published, $i, 'categories.', true, true, 'cb');
                    ?>
                </td>
                <td>
                    <?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>


                    <a href="<?php echo JRoute::_('index.php?option=com_jbcatalog&task=category.edit&id='.(int) $item->id);?>">
                    <?php echo $this->escape($item->title); ?>
                    </a>



                </td>
                <td class="center">
                    <a class="badge badge-success" href="index.php?option=com_jbcatalog&view=items&filter_catid=<?php echo (int) $item->id;?>">
                        <?php echo $item->cnt_items;?>
                    </a>
                </td>    
                <td class="center hidden-phone">
						<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
							<?php echo (int) $item->id; ?></span>
                </td>
            </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>