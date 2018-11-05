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

class JBCatalogImages
{

//    const  path = 'components/com_jbcatalog/libraries/jsupload/server/php/files';
    const  path = 'images/files';

    public static function getClickableImg($img)
    {
        return "<a class='fancybox-thumbs' data-fancybox-group='thumb' href='" . JURI::base() . "/" . self::path . "/" . $img . "'><img src='" . JURI::base() . "/" . self::path . "/thumbnail/" . $img . "'></a>";
    }

    public static function getImg($img, $thumb = false)
    {
        return JURI::base() . (self::path . "/" . ($thumb ? "thumbnail/" : "") . $img);
    }

    public static function includeFancy($doc)
    {
        JHtml::_('bootstrap.framework');
        $doc->addScript('components/com_jbcatalog/libraries/fancybox/source/jquery.fancybox.js?v=2.1.5');
        $doc->addStyleSheet('components/com_jbcatalog/libraries/fancybox/source/jquery.fancybox.css?v=2.1.5');
        $doc->addScript('components/com_jbcatalog/libraries/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7');
        $doc->addStyleSheet('components/com_jbcatalog/libraries/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7');
        $doc->addScript('components/com_jbcatalog/libraries/rating/js/jquery.rating.js');

        $doc->addStyleSheet('components/com_jbcatalog/libraries/rating/styles/jquery.rating.css');

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                /*
                 *  Simple image gallery. Uses default settings
                 */

                jQuery('.fancybox').fancybox();


                /*
                 *  Thumbnail helper. Disable animations, hide close button, arrows and slide to next gallery item if clicked
                 */

                jQuery('.fancybox-thumbs').fancybox({
                    prevEffect: 'none',
                    nextEffect: 'none',

                    closeBtn: true,
                    arrows: false,
                    nextClick: true,

                    helpers: {
                        thumbs: {
                            width: 50,
                            height: 50
                        }
                    }
                });


            });
        </script>
        <?php

    }

    public static function isImg($img, $thumb = false)
    {
    }
}
 
