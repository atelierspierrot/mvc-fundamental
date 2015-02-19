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
if (!isset($content))   $content    = '';
if (!isset($contents))  $contents   = array();

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <style>
body { margin: 1em; }
pre { white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word; }
.jumbotron hr { border-color: #ddd; }
    </style>
</head>
<body>
<div class="container">

    <div class="jumbotron">

<?php if (!empty($title)) : ?>
        <h1><?php echo $title; ?></h1>
<?php endif; ?>

<?php if (!empty($content)) : ?>
        <div class="content"><?php echo $content; ?></div>
<?php endif; ?>

    </div>

<?php if (!empty($contents)) : ?>
    <?php foreach ($contents as $_name=>$_content) : ?>
        <article id="<?php echo $_name; ?>">
            <?php echo $_content; ?>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

</div>
</body>
</html>
