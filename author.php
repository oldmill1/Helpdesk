<?php include 'security.php'; ?>
<?php if (!is_user_logged_in()) header('Location: /wp-admin/'); ?>
<?php get_header(); ?> 
  <div class="row">
    <div class="small-8 columns" id="content">
  		<?php get_template_part('partials/navigation', 'breadcrumbs'); ?>
			<div class="ticket">
        <?php 
        if (!$_REQUEST['ticket']) { 
          get_template_part('partials/data', 'tickets');
        } else { 
          get_template_part('partials/data', 'ticket');	
        }
        ?>
      </div>
    </div>
    <div class="small-4 columns">
    	<?php get_sidebar(); ?>
    </div>
  </div>
<?php get_footer(); ?>