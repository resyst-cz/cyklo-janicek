<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.cyklo
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
define('VERZE', '1.2');

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$user = JFactory::getUser();
$this->language = $doc->language;
$this->direction = $doc->direction;
// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$layout = $app->input->getCmd('layout', '');
$task = $app->input->getCmd('task', '');
$itemid = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if ($task == "edit" || $layout == "form") {
    $fullWidth = 1;
} else {
    $fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
JHTML::_('behavior.modal');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/template.js?v=' . VERZE);
$doc->addScript('components/com_jbcatalog/libraries/fancybox/source/jquery.fancybox.js?v=2.1.5');
$doc->addScript('components/com_jbcatalog/libraries/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7');

// Add Stylesheets
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template.css?v=' . VERZE);
$doc->addStyleSheet('components/com_jbcatalog/libraries/fancybox/source/jquery.fancybox.css?v=2.1.5');
$doc->addStyleSheet('components/com_jbcatalog/libraries/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Adjusting content width
if ($this->countModules('sidebar-right') && $this->countModules('sidebar-left')) {
    $span = "span6";
} else if ($this->countModules('sidebar-right') && !$this->countModules('sidebar-left')) {
    $span = "span9";
} else if (!$this->countModules('sidebar-right') && $this->countModules('sidebar-left')) {
    $span = "span9";
} else {
    $span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile')) {
    $logo = '<div class="site-logo"><img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" /></div>
                <span class="site-title">' . htmlspecialchars($this->params->get('sitetitle')) . '</span><br />' .
        ($params->get('siteslogan') && $params->get('siteslogan-2') ? '<small>' . $params->get('siteslogan') . ' <span class="zelena">' . $params->get('siteslogan-2') . '</span></small>' : '');
} else if ($this->params->get('sitetitle')) {
    $logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle')) . '</span>';
} else {
    $logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}

$komponenta = $app->input->get('option');
$show_page_header = $show_page_header_bg = false;
$add_class = $header_title = '';
$show_page_header_bg = JURI::base() . 'images/clanky_header_images/vychozi_bg.png';

if ($komponenta == 'com_content') {
    $article_id = $app->input->get('id');
    $add_class = ($article_id == '6' ? ' kontakt' : '');
    if ($article_id !== null) {
        $article = JTable::getInstance("content");
        $article->load($article_id); // Get Article ID
        $article_params = json_decode($article->get("attribs"));
        $article_images = json_decode($article->get("images"));
        if ($article_params->show_title == '1') {
            $show_page_header = true;
        }
        if (!empty($article_images->image_intro)) {
            $show_page_header_bg = JURI::base() . $article_images->image_intro;
        }
        $header_title = $article->title;
    }
} else if ($komponenta == 'com_jbcatalog') {
    $show_page_header = true;
    $menu = $app->getMenu(); // alternativa - vezme nazev z menu
    $header_title = $menu->getActive()->title; // aktivni polozka menu
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>"
      lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0" /> -->
    <jdoc:include type="head"/>
    <!--[if lt IE 9]>
    <script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/style.css"
          type="text/css"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic&subset=latin,latin-ext' rel='stylesheet'
          type='text/css'/>
    <link href='http://fonts.googleapis.com/css?family=Kaushan+Script&subset=latin,latin-ext' rel='stylesheet'
          type='text/css'/>

    <link rel="apple-touch-icon" sizes="57x57"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-57x57.png"/>
    <link rel="apple-touch-icon" sizes="60x60"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-60x60.png"/>
    <link rel="apple-touch-icon" sizes="72x72"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-72x72.png"/>
    <link rel="apple-touch-icon" sizes="76x76"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-76x76.png"/>
    <link rel="apple-touch-icon" sizes="114x114"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-114x114.png"/>
    <link rel="apple-touch-icon" sizes="120x120"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-120x120.png"/>
    <link rel="apple-touch-icon" sizes="144x144"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-144x144.png"/>
    <link rel="apple-touch-icon" sizes="152x152"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-152x152.png"/>
    <link rel="apple-touch-icon" sizes="180x180"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/apple-touch-icon-180x180.png"/>
    <link rel="icon" type="image/png"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/favicon-32x32.png"
          sizes="32x32"/>
    <link rel="icon" type="image/png"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/android-chrome-192x192.png"
          sizes="192x192"/>
    <link rel="icon" type="image/png"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/favicon-96x96.png"
          sizes="96x96"/>
    <link rel="icon" type="image/png"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/favicon-16x16.png"
          sizes="16x16"/>
    <meta name="application-name" content="Cyklo Janíček"/>
    <meta name="msapplication-TileColor" content="#7da90f"/>
    <meta name="msapplication-TileImage"
          content="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/mstile-144x144.png"/>
    <link rel="mask-icon"
          href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/safari-pinned-tab.svg"
          color="#ffffff"/>
    <meta name="msapplication-TileColor" content="#ffffff"/>
    <meta name="msapplication-TileImage"
          content="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/mstile-144x144.png"/>
    <meta name="msapplication-config"
          content="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicons/browserconfig.xml"/>
    <meta name="theme-color" content="#7da90f"/>

    <?php if ($this->countModules('mapka')) : ?>
        <!--
            You need to include this script tag on any page that has a Google Map.

            The following script tag will work when opening this example locally on your computer.
            But if you use this on a localhost server or a live website you will need to include an API key.
            Sign up for one here (it's free for small usage):
                https://developers.google.com/maps/documentation/javascript/tutorial#api_key

            After you sign up, use the following script tag with YOUR_GOOGLE_API_KEY replaced with your actual key.
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqUF8pj9IVHkcu0WETKytFwaMfcQP19q4&sensor=false"></script>
        -->
        <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/shady-map.js"></script>
    <?php endif; ?>
    <?php if ($this->countModules('instagram')) : ?>
        <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/instafeed.min.js"></script>
    <?php endif; ?>
</head>

<body class="site <?php

echo $option
    . ' view-' . $view
    . ($layout ? ' layout-' . $layout : ' no-layout')
    . ($task ? ' task-' . $task : ' no-task')
    . ($itemid ? ' itemid-' . $itemid : '')
    . ($params->get('fluidContainer') ? ' fluid' : '')
    . $add_class;
?>">
<div id="fb-root"></div>
<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/cs_CZ/sdk.js#xfbml=1&version=v2.3&appId=188936964535953";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<!-- Google Tag Manager -->
<noscript>
    <iframe src="//www.googletagmanager.com/ns.html?id=GTM-NNSQV8"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<script>(function (w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start':
                new Date().getTime(), event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            '//www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-NNSQV8');</script>
<!-- End Google Tag Manager -->
<!-- Body -->
<div class="body">
    <div class="fixed-top">
        <div class="body-top">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <div class="row-fluid">
                    <div class="top-info span10 text-right">
                        <?php echo($params->get('siteemail') ? '<span class="email"><a href="mailto:' . $params->get('siteemail') . '">' . $params->get('siteemail') . '</a></span>' : ''); ?>
                        <?php echo($params->get('sitephone') ? '<span class="phone">' . $params->get('sitephone') . '</span>' : ''); ?>
                    </div>
                    <div class="fb-like span2" data-href="https://www.facebook.com/cyklo.janicek"
                         data-layout="button_count"
                         data-action="like" data-show-faces="false" data-share="false"></div>
                </div>
            </div>
        </div>
        <div class="header-logo-menu">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <div class="row-fluid">
                    <div class="span4">
                        <header class="header" role="banner">
                            <div class="header-inner clearfix">
                                <a class="brand pull-left"
                                   href="<?php echo $this->baseurl; ?>/"><?php echo $logo; ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="span8">
                        <?php if ($this->countModules('top-nav')) : ?>
                            <nav class="navigation topmenu" role="navigation">
                                <jdoc:include type="modules" name="top-nav" style="none"/>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($show_page_header) { ?>
        <div <?php echo($show_page_header_bg !== false ? 'class="clanek-main-bg" style="background: rgba(0, 0, 0, 0) url(\'' . $show_page_header_bg . '\') no-repeat scroll 50% 0;"' : ''); ?>>
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <?php if ($this->countModules('breadcrumbs')) { ?>
                    <jdoc:include type="modules" name="breadcrumbs" style="none"/>
                <?php } ?>
                <h1 class="page-header on-slideshow"><?php echo $header_title; ?></h1>
            </div>
        </div>
    <?php } ?>
    <?php if ($this->countModules('slideshow')) : ?>
        <div class="slideshow-full">
            <jdoc:include type="modules" name="slideshow" style="none"/>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('sortiment-nav')) : ?>
        <div class="sortiment-menu">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <nav class="navigation sortiment" role="navigation">
                    <jdoc:include type="modules" name="sortiment-nav" style="none"/>
                </nav>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('banner-1') || $this->countModules('banner-2')) : ?>
        <div class="banner1-bg">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <div class="row banner1-content">
                    <div class="span6 banner1-left">
                        <jdoc:include type="modules" name="banner-1" style="none"/>
                    </div>
                    <div class="span6 banner1-right">
                        <jdoc:include type="modules" name="banner-2" style="none"/>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('mini-kolo')) : ?>
        <div class="mini-kolo"></div>
    <?php endif; ?>
    <?php if ($this->countModules('banner-3')) : ?>
        <div class="banner1-bg banner3-bg">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <div class="row banner1-content">
                    <div class="span6 banner1-left">
                        <jdoc:include type="modules" name="banner-3" style="none"/>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('reference')) : ?>
        <div class="banner2-bg">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <jdoc:include type="modules" name="reference" style="xhtml"/>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('certifikace')) : ?>
        <div class="banner-white-bg certifikace">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <jdoc:include type="modules" name="certifikace" style="xhtml"/>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('testovaci-dny')) : ?>
        <div class="banner-grey-bg testovaci-dny">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <jdoc:include type="modules" name="testovaci-dny" style="xhtml"/>
            </div>
        </div>
    <?php endif; ?>
    <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
        <jdoc:include type="modules" name="banner" style="xhtml"/>
        <div class="row-fluid">
            <?php if ($this->countModules('sidebar-left')) : ?>
                <!-- Begin Left Sidebar -->
                <div id="sidebar" class="span3">
                    <div class="sidebar-nav">
                        <jdoc:include type="modules" name="sidebar-left" style="xhtml"/>
                    </div>
                </div>
                <!-- End Left Sidebar -->
            <?php endif; ?>
            <main id="content" role="main" class="<?php echo $span; ?>">
                <jdoc:include type="modules" name="position-3" style="xhtml"/>
                <jdoc:include type="message"/>
                <jdoc:include type="component"/>
                <?php if ($this->countModules('znacky')) { ?>
                    <jdoc:include type="modules" name="znacky" style="none"/>
                <?php } ?>
            </main>
            <?php if ($this->countModules('sidebar-right')) : ?>
                <div id="aside" class="span3">
                    <!-- Begin Right Sidebar -->
                    <jdoc:include type="modules" name="sidebar-right" style="well"/>
                    <!-- End Right Sidebar -->
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($this->countModules('sponzorujeme')) : ?>
        <div class="banner-white-bg sponzorujeme">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <jdoc:include type="modules" name="sponzorujeme" style="xhtml"/>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('mapka')) : ?>
        <div class="mapka-full">
            <?php if ($this->countModules('mapka-oteviraci-doba')) : ?>
                <div class="mapka-oteviraci-doba">
                    <jdoc:include type="modules" name="mapka-oteviraci-doba" style="xhtml"/>
                </div>
            <?php endif; ?>
            <h2>Kde nás najdete</h2>
            <div id="map"></div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('contact-form')) : ?>
        <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
            <div class="contact-form">
                <jdoc:include type="modules" name="contact-form" style="xhtml"/>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('bazar-form')) : ?>
        <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
            <div id="bazarform" class="contact-form bazar-form">
                <jdoc:include type="modules" name="bazar-form-title" style="none"/>
                <jdoc:include type="modules" name="bazar-form" style="xhtml"/>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('nad-spodni-listou')) : ?>
        <div class="nad-spodni-listou">
            <jdoc:include type="modules" name="nad-spodni-listou" style="xhtml"/>
            <div class="greenline">
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->countModules('instagram')) : ?>
        <div class="instagram">
            <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                <jdoc:include type="modules" name="instagram" style="xhtml"/>
                <div id="instafeed"></div>
                <script type="text/javascript">
                    var feed = new Instafeed({
                        get: 'user',
                        userId: 2141789648,
                        accessToken: '2141789648.467ede5.98d624315fb24d6e908b1877041a200c'
                    });
                    feed.run();
                </script>
            </div>
        </div>
    <?php endif; ?>
    <div class="spodni-lista">
        <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
            <div class="row">
                <div class="span12 lista-icon-phone">
                    <div class="lista-title">Zákaznická podpora</div>
                    <div class="lista-text">
                        <span class="phone"><?php echo $params->get('sitephone'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer -->
<footer class="footer" role="contentinfo">
    <div class="container<?php echo($params->get('fluidContainer') ? '-fluid' : ''); ?>">
        <div class="row">
            <div class="footer-module span4">
                <jdoc:include type="modules" name="footer-left" style="xhtml"/>
            </div>
            <div class="footer-module span4">
                <jdoc:include type="modules" name="footer-middle" style="xhtml"/>
            </div>
            <div class="footer-module span4">
                <h3>Sledujte nás</h3>
                <div class="fb-page" data-href="https://www.facebook.com/cyklo.janicek" data-width="288"
                     data-height="227"
                     data-hide-cover="true" data-show-facepile="true" data-show-posts="false">
                    <div class="fb-xfbml-parse-ignore">
                        <blockquote cite="https://www.facebook.com/cyklo.janicek">
                            <a href="https://www.facebook.com/cyklo.janicek">Cyklo Janíček</a>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
        <div class="row margin-top">
            <p class="span8 text-right platebni_metody">
                <img src="images/footer_ikonky/logo_visa.png" alt="VISA" title="VISA"/>
                <img src="images/footer_ikonky/logo_maestro.png" alt="Maestro" title="Maestro"/>
                <img src="images/footer_ikonky/logo_mastercard.png" alt="MasterCard" title="MasterCard"/>
            </p>
            <p class="span4">
                <a class="fb-icon" href="https://www.facebook.com/cyklo.janicek" data-placement="top"
                   data-toggle="tooltip"
                   data-original-title="Cyklo Janíček na Facebooku"><img src="images/footer_ikonky/icon_fb.png"
                                                                         alt="Cyklo Janíček na Facebooku"/></a>
                <span class="autor bondon">Webdesign by <a href="http://www.bondon-webdesign.cz/">Bondon</a></span>
                <span class="autor resyst">Created by <a href="http://www.resyst.cz/">ReSyst.cz</a></span>
            </p>
        </div>
    </div>
</footer>
<jdoc:include type="modules" name="debug" style="none"/>
</body>
</html>
