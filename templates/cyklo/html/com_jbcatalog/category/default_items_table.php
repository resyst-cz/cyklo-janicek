<?php

/**
 * zobrazeni kategorie nyni zobrazuje detaily produktu
 * 2015-08-02 ReSyst.cz
 * Websites: http://www.resyst.cz
 */
defined('_JEXEC') or die;
/* plugins */
JHTML::_('behavior.modal');
require_once JPATH_COMPONENT . '/helpers/images.php';
// echo JBCatalogImages::includeFancy($this->document); // neni potreba - je includovano v sablone

if (isset($this->customplugins['script']) && $this->customplugins['script']) {
    echo $this->customplugins['script'];
}

/* plugins */
if (isset($this->customplugins['head']) && $this->customplugins['head']) {
    echo $this->customplugins['head'];
}
if (!empty($this->items)) {
    for ($i = 0; $i < count($this->items); $i++) {
        ?>
        <div class="row-fluid">
            <div class="product-card span12"
                 data-id="<?php echo $i; ?>" <?php echo (isset($this->items[$i]->resyst_kategorie)) ? "data-kategorie_id='" . $this->items[$i]->resyst_kategorie['id'] . "'" : ""; ?>>
                <?php

                /* plugins */
                if (isset($this->customplugins['checkbox'][$i]) && $this->customplugins['checkbox'][$i]) {
                    echo $this->customplugins['checkbox'][$i];
                }
                if (isset($this->items[$i]->images) && count($this->items[$i]->images)) {
                    ?>
                    <div class='product-image'>
                        <a href="<?php echo JBCatalogImages::getImg($this->items[$i]->images[0], false); ?>"
                           data-fancybox-group="thumb-<?php echo $i; ?>" class="fancybox-thumbs">
                            <img src="<?php echo JBCatalogImages::getImg($this->items[$i]->images[0], false); ?>"
                                 alt="<?php echo $this->items[$i]->title; ?>"/>
                        </a>
                        <?php if (isset($this->items[$i]->resyst_kategorie)) { ?>
                            <div class='product-kategorie'>
                                <span
                                    class="kategorie_tag"><?php echo $this->items[$i]->resyst_kategorie['kategorie']; ?></span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div class='product-info'>
                    <div class="product-header">
                        <h3><?php echo $this->items[$i]->title; ?></h3>
                        <?php if (isset($this->items[$i]->resyst_cena)) {
                            $cena = number_format(floatval($this->items[$i]->resyst_cena), 2, ',', ' ');
                            ?>
                            <div class="cena">
                                Cena: <span><?php echo $cena; ?> Kč</span>
                            </div>
                        <?php } ?>
                        <?php if (!empty($this->items[$i]->resyst_puvodni_cena)) {
                            $puv_cena = number_format(floatval($this->items[$i]->resyst_puvodni_cena), 2, ',', ' ');
                            ?>
                            <div class="cena puvodni">
                                Původní cena: <span><?php echo $puv_cena; ?> Kč</span>
                            </div>
                        <?php } ?>
                    </div>
                    <ul class="nav nav-tabs">
                        <?php if (isset($this->items[$i]->resyst_info)) {
                            ?>
                            <li><a data-toggle="tab" href="#informace_<?php echo $i; ?>">Specifikace</a></li>
                            <?php

                        }
                        if (isset($this->items[$i]->descr)) {
                            ?>
                            <li><a data-toggle="tab" href="#popis_<?php echo $i; ?>">Popis produktu</a></li>
                            <?php

                        }
                        ?>
                    </ul>
                    <div class="tab-content">
                        <?php if (isset($this->items[$i]->resyst_info)) { ?>
                            <div class="tab-pane" id="informace_<?php echo $i; ?>">
                                <ul>
                                    <?php foreach ($this->items[$i]->resyst_info as $info) { ?>
                                        <?php if (!empty($info['hodnota'])) { ?>
                                            <li class="icon_bg_<?php echo $info['hodnota_id'] ?>" data-toggle="tooltip"
                                                data-placement="left" title="<?php echo $info['extra']; ?>">
                                                <span class="icon"></span><?php echo $info['hodnota']; ?>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php

                        }
                        if (isset($this->items[$i]->descr)) {
                            ?>
                            <div class="tab-pane" id="popis_<?php echo $i; ?>">
                                <?php echo $this->items[$i]->descr; ?>
                            </div>
                            <?php

                        }
                        ?>
                    </div>
                </div>
                <?php

                if ($this->state->{'category.id'} === 10) {
                    ?>
                    <div class="zeptat-se">
                        <a href="#bazarform" data-id="<?php echo $i; ?>" class="modal btn-email"><span>Chci další informace</span></a>
                    </div>
                    <?php

                }
                ?>
            </div>
        </div>
        <?php

    }
} else {
    ?>
    <div class="row-fluid">
        <p>Nenalezeny žádné produkty</p>
    </div>
    <?php

}
/* plugins */
if (isset($this->customplugins['bottom']) && $this->customplugins['bottom']) {
    echo $this->customplugins['bottom'];
}
