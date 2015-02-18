<?php

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
