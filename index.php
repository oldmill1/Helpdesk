<?php include 'security.php'; ?>
<?php if (!is_user_logged_in()) header('Location: ' . wp_login_url()); ?>
<?php get_header(); ?> 
<?php $filter = $_REQUEST['show']; ?> 
<div class="row" id="page">
 	<!-- Nav Sidebar -->
    <!-- This is source ordered to be pulled to the left on larger screens -->
    <div class="large-3 columns clouds">
      <div class="panel" id="you">
        <a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/share.png" /></a>
        <h5 class="user subheader"><a href="#"><?php global $user; echo ucwords($user->user_login); ?></a></h5>
      </div>
      <div id="inbox">
      <h3>Inbox</h3>
    	<span class="radius secondary label">No messages</span>
    	</div>
    </div><!-- end large-3 --> 
    <!-- Main Feed -->
    <!-- This has been source ordered to come first in the markup (and on small devices) but to be to the right of the nav on larger screens -->
    <div class="large-6 columns feed-place ticket">
    	<?php
    	$ticket = $_REQUEST['ticket']; 
    	if ($ticket){
    		get_template_part('partials/view', 'ticket');
    	}else{?>
    	<div id="new-ticket" class="btn btn-large btn-block btn-primary"><a href="javascript:void(0);" data-reveal-id="myModal" class="">Create New Ticket</a></div>
	    <div class="feed-place-inner">
				<?php 
				global $wpdb; 
				global $user; 
				$userID = $user->ID; 
				if ($filter=='open'){
				$tickets = $wpdb->get_results("select * from `helpdesk_tickets` WHERE `user_id` = '$userID' AND `state` != 'closed' ORDER BY `dateCreated` DESC;");				
				}elseif($filter=='closed'){
					$tickets = $wpdb->get_results("select * from `helpdesk_tickets` WHERE `user_id` = '$userID' AND `state` = 'closed' ORDER BY `dateCreated` DESC;");
				}elseif($filter=='all'){
					$tickets = $wpdb->get_results("select * from `helpdesk_tickets` WHERE `user_id` = '$userID' AND `state` != 'closed' ORDER BY `dateCreated` DESC;");
				}else {
					$tickets = $wpdb->get_results("select * from `helpdesk_tickets` WHERE `user_id` = '$userID' ORDER BY `dateCreated` DESC;");	
				}
				$wpdb->show_errors();
				if ($tickets){
				foreach($tickets as $ticket){
				?>
				<!-- Feed Entry -->
	      <div class="row">
	        <div class="large-2 columns small-3"><img src="http://placehold.it/80x80&text=[img]" /></div>
	        <div class="large-10 columns">
	          <p><strong>
	          <?php $ticketSender = get_user_by('id', $ticket->user_id); 
	          echo ucwords($ticketSender->user_nicename); ?></strong> <br /><?php 
				      $ticketData = unserialize($ticket->content); 
				      echo stripslashes(putLineBreaks(convertToExcerpt($ticketData['Desc'], 150))); ?></p>
	          <ul class="inline-list">
	            <li><a href="?ticket=<?php echo $ticket->id; ?>">View Details</a></li>
	            <!--<li><a href="?ticket=<?php //echo $ticket->id; ?>">Share</a></li>-->
	          </ul>
	          <?php
	          $replies = helpdesk_get_replies_for_ticket_normal_sort($ticket->id); 
	          if ($replies) {
	          ?>
	           <h6><?php echo count($replies);?> Comment<?php if (count($replies) > 1) echo 's'; ?></h6>

	          <?php
	          foreach($replies as $reply){
	          ?>
	         	<div class="row">
	            <div class="large-2 columns small-3"><img src="http://placehold.it/50x50" /></div>
	            <div class="large-10 columns"><p><?php echo stripslashes(putLineBreaks($reply->content)); ?></p></div>
	          </div>
	          <?php
	          }}
	          ?>
	        </div>
	      </div>
	      <!-- End Feed Entry -->
	      <hr />
				<?php
				}
				
				}else {
					echo "<span class=\"radius secondary label\">No tickets</span>";
				}
				?>
			</div>
			<?php } ?> 
    </div>   
    <!-- Right Sidebar -->
    <!-- On small devices this column is hidden -->
    <aside class="large-3 columns hide-for-small">
    	<div id="controls" class="todo mrm">
        <div class="todo-search">
          <input class="todo-search-field" type="search" value="" placeholder="Search" />
        </div>
        <ul>
          <li class="<?php if ($filter=='open') echo 'todo-done'; ?>">
            <div class="todo-content">
              <h4 class="todo-name">
                <strong><a href="?show=open">Open</a></strong>
              </h4>
            </div>
          </li>

          <li class="<?php if ($filter=='closed') echo 'todo-done'; ?>">
            <div class="todo-content">
              <h4 class="todo-name">
                <strong><a href="?show=closed">Closed</a></strong>
              </h4>
            </div>
          </li>

          <li class="<?php if ($filter=='all') echo 'todo-done'; ?>">
            <div class="todo-content">
              <h4 class="todo-name">
                <strong><a href="?show=all">All</a></strong>
              </h4>
            </div>
          </li>
        </ul>
      </div>
    </aside>

  </div>
  <div id="myModal" class="reveal-modal">
	  <h2>Create A New Ticket</h2>
	  <p class="lead"></p>
	  <?php get_template_part('partials/form', 'new'); ?>
	  <a id="myModalClose" class="close-reveal-modal">&#215;</a>
	</div>  
<?php get_footer(); ?>