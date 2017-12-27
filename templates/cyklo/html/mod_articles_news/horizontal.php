<?php
/**
 * upraveny modul pro zobrazeni znacek vyrobcu s popisky
 * @author ReSyst.cz <admin@resyst.cz>
 */
defined('_JEXEC') or die;
?>
<div class="newsflash-horiz <?php echo $params->get('moduleclass_sfx'); ?>">
    <?php
    for ($i = 0, $n = count($list); $i < $n; $i ++) {
        $item = $list[$i];
        $article_images = json_decode($item->images);
        ?>
        <div class="span4">
            <div class="znacka-wrapper">
                <div class="znacka-logo"
                <?php if (!empty($article_images->image_intro)) { ?>
                         style="background: transparent url(<?php echo JURI::base() . $article_images->image_intro; ?>) no-repeat 50% 50%;"<?php }
                ?>>
                    <div class="znacka-popis">
                        <?php require JModuleHelper::getLayoutPath('mod_articles_news', '_item'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="clearfix"></div>
</div>
