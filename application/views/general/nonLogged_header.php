<?php
/**
 * Project: Selfology
 * User: Hector Ordonez
 * Description:
 * View chunk for the non-logged users header.
 * Date: 27/07/13 19:15
 */
?>
<!doctype html>
<html>
<head>
    <!-- Title -->
    <title>
        <?php echo $this->_getTitle(); ?>

    </title>

    <!-- Meta Tags -->
    <?php foreach ($this->_getMeta() as $meta) : ?>
        <meta <?php foreach ($meta as $tagName => $tagValue) : echo "{$tagName}='{$tagValue}' "; endforeach; ?>/>
    <?php endforeach; ?>

    <!-- CSS Stylesheets -->
    <?php foreach ($this->_getCss() as $css) : ?>
        <link rel='stylesheet' href='<?php echo $css; ?>'/>
    <?php endforeach; ?>

    <!-- JS Libraries -->
    <?php foreach ($this->_getJs() as $js) : ?>
        <script src='<?php echo $js; ?>'></script>
    <?php endforeach; ?>

</head>

<body>

<div id='header'>
    <div id='logo'></div>
</div>

<!-- Opening Content -->
<div id='nonLogged_content'>