<?php
global $tourState; 
$cookie = $_COOKIE['tour'];
if (is_null($cookie)){ 
	$tourState = 'show'; 
	setcookie('tour', 'seen');
}else{
	$tourState = 'hide'; 
}  
?>
<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />

  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />

  <title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
  
  <!-- Included CSS Files (Uncompressed) -->
  <!--
  <link rel="stylesheet" href="stylesheets/foundation.css">
  -->
  
  <!-- Included CSS Files (Compressed) -->
  <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/stylesheets/foundation.min.css">
  <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/stylesheets/app.css">
  <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css">
  <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/stylesheets/flat-ui.css">
  <link href='http://fonts.googleapis.com/css?family=Lato:400,700,900,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
  <script src="<?php bloginfo('template_directory'); ?>/javascripts/modernizr.foundation.js"></script>
  <?php
  echo "<script type='text/javascript'>var currentURL = '".get_permalink(get_the_ID())."'</script>";
  ?>
  <?php wp_head(); ?>
</head>
<body>
	<div class="row">
	</div>
  <div class="row">
    <div class="twelve columns">
      <img src="<?php bloginfo('template_directory'); ?>/images/engage.png" />
      <hr />
    </div>
  </div>
  
  <?php

 		
  ?>
