<?php
$filter = $_REQUEST['show'];
?>
<div class="row">
	<div class="twelve columns">
		<div class="three columns">
			<dl class="sub-nav">
			  <dd class="<?php if ($filter=='open' || !$filter) { echo 'active'; }?>"><a href="?show=open">Open</a></dd>
			  <dd class="<?php if ($filter=='closed') { echo 'active'; }?>"><a href="?show=closed">Closed</a></dd>
			</dl>
		</div>
		<div class="nine columns">
			<table class="twelve" style="width: 100%">
			  <thead>
			    <tr>
			      <th>Name</th>
			      <th>Date</th>
			      <th>Priority</th>
			    </tr>
			  </thead>
			  <tbody>
			  	<?php
					  global $wpdb; 
					  global $user; 
					  $userID = $user->ID; 
						if ($filter=='closed'){
            	$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `helpdesk_tickets` WHERE `user_id` = %d AND `state` = 'closed' ORDER BY dateCreated ASC", $userID));
            }else{
            	$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `helpdesk_tickets` WHERE `user_id` = %d AND `state` != 'closed' ORDER BY dateCreated ASC", $userID));
            }
					  if(!$tickets){
					  ?>
					  <div class="alert-box secondary">
						 	No tickets available.
						  <a href="" class="close">&times;</a>
						</div>
					  <?php 
					  } else { 
						foreach ($tickets as $ticket) {
					?>
			    <tr>
			      <td>
			      <a href="?ticket=<?php echo $ticket->id; ?>">
			      <?php 
			      $ticketData = unserialize($ticket->content); 
			      echo $ticketData['Desc']; ?>
			      </a>
			      </td>
			      <td>
			      <?php echo date('M jS', strtotime($ticket->dateCreated)); ?> &nbsp;<?php echo date('g:h a', strtotime($ticket->dateCreated)); ?>
			      </td>
			      <td><span class="secondary label"><?php echo ucwords($ticket->priority); ?></span></td>
			    </tr>
			    <?php } ?>
			    <?php } ?>
			  </tbody>
			</table>
		</div>
	</div>
</div>