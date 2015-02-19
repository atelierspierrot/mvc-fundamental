<?php
/**
 * @see <http://github.com/atelierspierrot/mvc-fundamental>.
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
.column { max-width: 30%; margin: 1em; padding: 1em; border: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="container">

<?php if (!empty($title)) : ?>
    <div class="page-header">
        <h1><?php echo $title; ?></h1>
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

<?php if (!empty($left_block)) : ?>
    <div class="column pull-left"><?php echo $left_block; ?></div>
<?php endif; ?>

<?php if (!empty($right_block)) : ?>
    <div class="column pull-right"><?php echo $right_block; ?></div>
<?php endif; ?>

<?php if (!empty($content)) : ?>
    <div class="content"><?php echo $content; ?></div>
<?php endif; ?>

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
