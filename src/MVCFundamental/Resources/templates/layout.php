<?php
/**
 * This file is part of the MVC-Fundamental package.
 *
 * Copyright (c) 2013-2016 Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * The source code of this package is available online at
 * <http://github.com/atelierspierrot/mvc-fundamental>.
 */

/**
 * This layout is built with the help of [jQuery](http://jquery.com/)
 * and [Bootstrap](http://getbootstrap.com/) loaded from distant CDN.
 *
 * The page is separated in sub-parts defined themselves as template files. The
 * following parts will be used by default:
 *
 *      'content'   => 'layout_content.php'
 *      'header'    => 'layout_header.php'
 *      'footer'    => 'layout_footer.php'
 *      'extra'     => 'layout_extra.php'
 *      'aside'     => 'layout_aside.php'
 *      'system'    => 'layout_system.php'
 *
 *      +--------------------------------+
 *      |             header             |
 *      +--------------------------------+
 *      |   system messages    | extra   |
 *      |   ---------------    |         |
 *      |           content    |         |
 *      | -------              |         |
 *      | aside |              |         |
 *      |       |              |         |
 *      |       |              |         |
 *      | -------              |         |
 *      |                      |         |
 *      +--------------------------------+
 *      |             footer             |
 *      +--------------------------------+
 *
 * @see     \MVCFundamental\Commons\DefaultLayout
 * @link    http://jquery.com/
 * @link    http://getbootstrap.com/
 */

/**
 * @var     string  $page_title     The header "title" meta-tag
 * @var     string  $page_headers   Additional raw meta-tags and header information
 * @var     string  $page_styles    Additional CSS styles
 * @var     string  $page_scripts   Additional JS scripts
 */
if (!isset($page_title)) {
    $page_title     = isset($title) ? $title : '';
}
if (!isset($page_headers)) {
    $page_headers   = '';
}
if (!isset($page_styles)) {
    $page_styles    = '';
}
if (!isset($page_scripts)) {
    $page_scripts   = '';
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <title><?php echo $page_title; ?></title>
    <?php echo $page_headers; ?>
<style>
html            { position: relative; min-height: 100%; }
body            { margin-bottom: 100px; }
.jumbotron hr   { border-color: #ddd; }
.footer         { position: absolute; bottom: 0; width: 100%; height: auto; min-height: 100px; background-color: #f5f5f5; padding: 1em 5em; }
.column         { margin: 1em 1em 1em 0; max-width: 30%; }
.sidebar-module { padding: 1em; background-color: #f5f5f5; border-radius: 4px; margin-bottom: 1em; }
.main section, section nav { margin-bottom: 1em; }
img.page-logo   { max-height: 180px; max-width: 180px; border-radius: 10%; vertical-align: middle; }
<?php echo $page_styles; ?>
</style>
</head>
<body>
    <a href="#content" class="sr-only sr-only-focusable">Skip to main content</a>
    <div class="container">

        <?php echo $this->renderChild('header'); ?>

        <div class="container" role="main">
            <div class="row">
                <div class="col-sm-8 main" id="content">
                    <?php echo $this->renderChild('system'); ?>
                    <?php echo $this->renderChild('aside'); ?>
                    <?php echo $this->renderChild('content'); ?>
                </div>
                <div class="col-sm-3 col-sm-offset-1">
                    <?php echo $this->renderChild('extra'); ?>
                </div>
            </div>

        </div>

    </div>
    <?php echo $this->renderChild('footer'); ?>
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script>
        <?php echo $page_scripts; ?>
    </script>
</body>
</html>
