<?php
$replies = helpdesk_get_replies_for_ticket_normal_sort($ticketID); 
$other_people_count = array(); 
if($replies){
	foreach($replies as $reply){
		if ($reply->reply_user_id!=$user->ID){
			$other_people_count[] = $reply; 
		} 
	}
}
if ($replies){
	foreach($replies as $r): 
	?>
	<div class="reply-thread">
		<ul>
			<?php 
			$thisUser = get_user_by('id', $r->reply_user_id); 
			?>
			<li>
				<strong><?php echo $thisUser->display_name; ?></strong>
				<p class="date floatRight"><?php echo date('M jS', strtotime($ticket->dateCreated)); ?> &nbsp;<?php echo date('g:h a', strtotime($ticket->dateCreated)); ?></p>	
			</li>
			<div class="clear:both;"></div>
			<li><p><?php echo stripslashes($r->content); ?></p></li>
		</ul>
	</div>
	<?php 
	endforeach; 
}else {
?>
<div class="panel">
  <h6>Waiting for a reply from Help Desk</h6>
  <p>You'll receive an email when someone responds to this ticket.</p>
</div>
<?php } 