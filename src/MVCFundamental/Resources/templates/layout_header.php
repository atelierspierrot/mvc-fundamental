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

/**
 * @var     string  $title      The main title of the page
 * @var     string  $hat        The main hat (slogan) of page's title
 * @var     string  $logo       The main logo of the page, displayed before the main title
 * @var     string  $home_link  The URL of a clickable title
 * @var     array   $breadcrumb The page breadcrumb with items like `name => url`
 */
if (!isset($title))     $title      = '';
if (!isset($hat))       $hat        = '';
if (!isset($logo))      $logo       = '';
if (!isset($home_link)) $home_link  = '';
if (!isset($breadcrumb))$breadcrumb = array();

?>
<header id="banner" class="body">

<?php if (!empty($title)) : ?>
    <div class="page-header">
        <h1>
    <?php if (!empty($home_link)) : ?>
        <a href="<?php echo $home_link; ?>">
    <?php endif; ?>
    <?php if (!empty($logo)) : ?>
            <img src="<?php echo $logo; ?>" alt="logo" class="page-logo">
    <?php endif; ?>
            <?php echo $title; ?>
    <?php if (!empty($hat)) : ?>
            &nbsp;<small><?php echo $hat; ?></small>
    <?php endif; ?>
    <?php if (!empty($home_link)) : ?>
        </a>
    <?php endif; ?>
        </h1>
    </div>
<?php endif; ?>

<?php if (!empty($breadcrumb)) : ?>
    <ol class="breadcrumb">
        <?php $counter=1; foreach ($breadcrumb as $name=>$url) : ?>
            <li
                <?php if ($counter<count($breadcrumb)) : ?>
                >
                <a href="<?php echo $url; ?>">
                    <?php else : ?>
                        class="active">
                    <?php endif; ?>
                    <?php echo $name; ?>
                    <?php if ($counter<count($breadcrumb)) : ?>
                </a>
                <?php endif; ?>
            </li>
            <?php $counter++; endforeach; ?>
    </ol>
<?php endif; ?>

</header>
