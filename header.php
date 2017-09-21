<!DOCTYPE html>
<!--[if IE 8]> 		   <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />	
  	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	
	<div class="fixed">
	<?php
	
	//Add our Top Bar Nav: http://foundation.zurb.com/docs/components/top-bar.html 
	tome_topnav();
	
	?>
	</div>