<div class="ticket_internal">
<?php
$ticketID = $_REQUEST['ticket'];
global $wpdb; 
global $user; 
$ticket = $wpdb->get_row("SELECT * FROM `helpdesk_tickets` WHERE `user_id` = $user->ID AND `id` = $ticketID;");?>
<?php 
if(!$ticket): 
	echo "That ticket doesn't exist."; 
else: ?>
	
	<?php include_once('get_thread.php'); ?>
	<table style="width:100%;">
		<thead>
		<tr>
		<?php
		$ticketData = unserialize($ticket->content);
		foreach($ticketData as $key => $value) { ?>
			<?php if($value) : ?>
			<th>
			<?php echo $key; ?>
			</th>
			<?php endif; ?>
		<?php } ?>
			<th>
				Date
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
		<?php
		$ticketData = unserialize($ticket->content);
		foreach($ticketData as $key => $value) { ?>
			<?php if ($value) : ?> 
			<td><?php echo $value; ?></td>
			<?php endif; ?>
		<?php } ?>
			<td><?php echo date('M jS', strtotime($ticket->dateCreated)); ?> &nbsp;<?php echo date('g:h a', strtotime($ticket->dateCreated)); ?></td>
		</tr>
		</tbody>
	</table>
	
  <?php include_once('ticket_header.php'); ?>
  <form name="newReply" id="newReply" method="POST" action="/">
    <textarea name="newReply_Content" id="newReply_Content"  cols="" rows="5" placeholder="Public Reply"></textarea><br />
    <input type="hidden" id="newReply_Ticket" class="newReply_Ticket" value="<?php echo $ticket->id; ?>" name="ticket" />
    <input type="hidden" id="newReply_ModifyFlag" class="newReply_ModifyFlag" value="send-to-owner" name="newReply_ModifyFlag" />
    <input type="submit" id="submit" value="Submit" class="button button-primary button-large" />
  </form>
<?php endif; ?>
</div>
































