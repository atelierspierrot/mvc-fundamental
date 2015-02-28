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
 * @var     string  $content    The main content
 * @var     string  $title      The main content's title
 * @var     string  $header     The main content's header
 * @var     string  $footer     The main content's footer
 */
if (!isset($title))     $title      = '';
if (!isset($content))   $content    = '';
if (!isset($header))    $header     = '';
if (!isset($footer))    $footer     = '';

/**
 * @var     array  $contents    Table of contents defined as array items
 *
 * The main content is added at the beginning of the array.
 *
 * Each "content" item is constructed like:
 *
 *      array(
 *          'title'     => string ,
 *          'content'   => string ,
 *          'header'    => string ,
 *          'footer'    => string ,
 *      )
 */
if (!isset($contents))  $contents   = array();

$tmp_ctt = array_filter(array(
    'title'     => $title,
    'content'   => $content,
    'footer'    => $footer,
    'header'    => $header,
));
if (!in_array($tmp_ctt, $contents)) array_unshift($contents, $tmp_ctt);

?>
<?php foreach ($contents as $_content) :
    if (is_string($_content)) $_content = array('content'=>$_content);
?>
<section>

    <article class="hentry">
    <?php if (isset($_content['title']) && !empty($_content['title'])) : ?>
        <header>
            <h2><?php echo $_content['title']; ?></h2>
        </header>
    <?php endif; ?>
    <?php if (isset($_content['header']) && !empty($_content['header'])) : ?>
        <header>
            <?php echo $_content['header']; ?>
        </header>
    <?php endif; ?>
    <?php if (isset($_content['content']) && !empty($_content['content'])) : ?>
        <?php echo $_content['content']; ?>
    <?php endif; ?>
    <?php if (isset($_content['footer']) && !empty($_content['footer'])) : ?>
        <footer>
            <?php echo $_content['footer']; ?>
        </footer>
    <?php endif; ?>
    </article>

</section>
<?php endforeach; ?>
