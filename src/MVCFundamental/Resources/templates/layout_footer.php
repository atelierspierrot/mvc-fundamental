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
 * @var     string  $content        The main content of the footer
 * @var     string  $content_left   The content of footer's left block
 * @var     string  $content_eight  The content of footer's right block
 */
if (!isset($content))        $content         = '';
if (!isset($content_left))   $content_left    = '';
if (!isset($content_right))  $content_right   = '';

?>
<footer class="footer">

<?php if (!empty($content)) : ?>
    <div class="footer-content">
        <?php echo $content; ?>
    </div>
<?php endif; ?>

<?php if (!empty($content_left)) : ?>
    <div class="footer-content pull-left text-muted">
        <?php echo $content_left; ?>
    </div>
<?php endif; ?>

<?php if (!empty($content_right)) : ?>
    <div class="footer-content pull-right text-muted">
        <?php echo $content_right; ?>
    </div>
<?php endif; ?>

    <div class="clearfix"></div>
</footer>
