<?php
/**
 * This file is part of the MVC-Fundamental package.
 *
 * Copyright (c) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
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

if (!isset($title))     $title      = '';
if (!isset($page_title))$page_title = $title;
if (!isset($params))    $params     = array();

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<style>
    html {
        position: relative;
        min-height: 100%;
    }
    body {
        margin-bottom: 60px;
    }
    .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 60px;
        background-color: #f5f5f5;
        padding: 1em 5em;
    }
    .column {
        margin: 1em;
        padding: 1em;
        max-width: 30%;
    }
    .sidebar-module {
        background-color: #f5f5f5;
        border-radius: 4px;
    }
</style>
</head>
<body>
    <div class="container">

        <?php echo $this->renderChild('header'); ?>

        <div class="container" role="main">

            <div class="row">
                <div class="col-sm-8 main">
                    <?php echo $this->renderChild('aside'); ?>
                    <?php echo $this->renderChild('content'); ?>
                </div>
                <div class="col-sm-3 col-sm-offset-1 sidebar">
                    <?php echo $this->renderChild('extra'); ?>
                </div>
            </div>

        </div>

    </div>
    <?php echo $this->renderChild('footer'); ?>
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>
