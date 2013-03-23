<?php
/*********************************************************************************\
*                                                                                                                                                                                                                                               
* This is the function.php for Help Desk                                                                                                                                       
* You are free to modify it to suit your needs. Please keep things tidy!                                                                                                                                                                                                                                                                                                                                                                                                                                                       *
\*********************************************************************************/


// Set-up env.
// -------------------------
//

date_default_timezone_set('America/Toronto');

// Define AJAX on the front end so all our AJAX calls work
// -------------------------
//

if ( !is_admin() ) :
  add_action('wp_head','throw_ajaxurl');
endif;

function throw_ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}


// Define AJAX calls
// -------------------------
//


add_action("wp_ajax_newTicket", "helpdesk_newTicket");
add_action("wp_ajax_nopriv_newTicket", "helpdesk_newTicket_login");

function helpdesk_newTicket()
{ 
	if (is_user_logged_in()){
		$user = wp_get_current_user();
		$userID = $user->ID; 
		$userEmail = $user->user_email; 
	}else{
		echo "Your must login!"; 
		die();
	}

	$data = $_POST; 
	$ticket = serialize($data['ticket']);
	global $wpdb; 
	$now = new DateTime(); 	

	// insert into DB 
	$insert = $wpdb->insert(
		"helpdesk_tickets",
		array(
			"content" => $ticket, 
			"dateCreated" => $now->format('Y-m-d H:i:s'), 
			"dateModified" => $now->format('Y-m-d H:i:s'),
			"priority" => $data['ticket']["Priority"],
			"state" => "new", 
			"user_id" => $userID, 
			"assigned_to" => 0,
		)
	);

	// get admin emails
	$people = get_users();
	foreach($people as $person){ 
	    if($person->allcaps['administrator']) {
	   		$emails[] = $person->user_email; 
	    }
	}

	// sending mail (not done in dev) 
	
	// send emails
	// $from = 'helpdesk@engage.com'; 
	// $to = implode(",", $emails); 
	// $ticketDesc = $data['ticket']['Desc']; 
	// $text = "New Ticket Created.";
	// $subject = 'Engage Help Desk - ' . substr($ticketDesc, 0, 75);   

	/*helpdesk_sendcommunication($to, $from, $data['ticket'], $subject, $text, $userID, 
		array(
		"New Ticket Created", 
		"A new ticket has been created. Please reply as soon as you can.",
		)); 
	*/ 
	
	/*
	helpdesk_sendcommunication($userEmail, $from, $data['ticket'], "Ticket recieved by Help Desk", "Ticket recieved by Help Desk", $userID, 
		array(
			"We've Got Your Message",
			"This message is to confirm that your ticket has been received by Help Desk."
		));
	*/ 
	
	exit(json_encode($insert)); 
}

function helpdesk_newTicket_login()
{
	echo "You must login!"; 
	die();
}

function helpdesk_sendcommunication($to, $from, $ticket, $subject, $text, $userID, $verbage){
	include_once 'Mail.php';
	include_once 'Mail/mime.php'; 	

	$crlf = "\n";

	$host = "smtp.mandrillapp.com"; 
	$username = "ataxali+mandrill@gmail.com"; 
	$password = "gReBqxPgtB7KqPgPz-H6Ow"; 

	$headers = array ('From' => $from,
			'To' => $to,
			'Subject' => $subject);

	$html = createHTMLMail($ticket, $userID, $verbage);

	$mime = new Mail_mime($crlf);
	$mime->setTXTBody($text);
	$mime->setHTMLBody($html);

	$body = $mime->get();
	$headers = $mime->headers($headers);
	$smtp = Mail::factory('smtp',
		array ('host' => $host,
		'auth' => true,
		'username' => $username,
		'password' => $password));

	$mail = $smtp->send($to, $headers, $body);

	return $mail;
} 
array(
	'foo' => 'bar', 
	'foo' => 'zoo', 
);

function helpdesk_getTicket($args)
{ 
	$userID = get_current_user_id();
	$defaults = array(
		'user' 				=> false, 
		'where' 			=> array(), 
		'limit'				=> array(),
		'orderby' 		=> array()  		
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	$query = 'select * from `helpdesk_tickets` '; 
	if(!empty($where)){ 
		$flag=1;
		foreach($where as $key => $value){ 
			if($flag){
				$query.="where $key = $value ";
			}else{
				$query.="and $key = $value ";
			}
			$flag=false;
		}
	}
	if ($user){ 
		if (empty($where)) { 
			$query.='where '; 
		}else{
			$query.='and ';
		}
		$query.='`user_id` = ' . $user;	
	}
	if(!empty($orderby)){
		$query.= "order by {$orderby[0]} {$orderby[1]}";
	}
	if (!empty($limit)){
		$query.=" limit {$limit[0]}, {$limit[1]} ";
	}
	$query = trim($query); 
	global $wpdb; 
	$results = $wpdb->get_results($query);
	return $results; 
} 

add_action("wp_ajax_ticketActions", "helpdesk_ticketActions");
add_action("wp_ajax_nopriv_ticketActions", "helpdesk_ticketActions_login");

function helpdesk_ticketActions()
{
	/** 
	 * Only logged in Users are allowed to acces this function 
	*/ 
	if (is_user_logged_in()){
		/** 
		 * Set UserID 
		*/ 
		$user = wp_get_current_user();
		$userID = $user->ID; 
	}else{
		echo "Your must login!"; 
		die();
	}
	global $wpdb; 
	$now = new DateTime(); 
	$data = $_POST;
  if($data['method']=='close'){
		$ticketID = $data['ticket'];
    $ticket = $wpdb->get_row("SELECT * FROM `helpdesk_tickets` WHERE `id` = $ticketID"); 
    if(!$ticket){
    	exit(json_encode('ticket-not-found'));
    }
   
    // only users OR ticket owners are allowed close tickets
   	if(!$ticket->assigned_to == $userID || !$ticket->user_id == $userID){
    	exit(json_encode(array('status' => 'fail', 'error' => 'You have to be assigned to a ticket to close it.')));
    }
	
    $update = $wpdb->update( 
     	'helpdesk_tickets', 
      array( 
        'state' => 'closed',
      ), 
      array( 'id' => $ticketID ), 
      array( 
        '%s',	// state type
      ), 
      array( '%d' ) 
    );
    if ($update){
    	exit(json_encode(array('status' => 'pass')));
    }else{
    	exit(json_encode(array('status' => 'fail', 'error' => 'Could not close ticket. This ticket might already be closed.')));
    }
    exit(json_encode($update)); 
  }elseif($data['method']=='assignTo'){
 	$ticketID = $data['ticket']; 
    $userID = $data['user']; 
    $currentTicketUserId = $wpdb->get_var($wpdb->prepare("SELECT `assigned_to` FROM `helpdesk_tickets` WHERE id = %d", $ticketID));
    if ($currentTicketUserId==$userID){
    	$response  = array(
      	'state' => 'error', 
        'message' => 'Already assigned to that user.'
      );
      exit(json_encode($response));
    }
    
    $update = $wpdb->update(
    	'helpdesk_tickets', 
      array( 'assigned_to' => $userID ),
      array( 'id' => $ticketID ),
      array( '%d' ), 
      array( '%d' )
    );
    
    $user = get_user_by('id', $userID); 
   	if ($update){
    	$response = array(
      	'state' => 'success', 
        'data' => $user->user_nicename
    		);
      exit(json_encode($response));
    }else{
    	exit(json_encode(0));
    }
 
  }
     
}


add_action("wp_ajax_newReply", "helpdesk_newReply");
add_action("wp_ajax_nopriv_newReply", "helpdesk_newReply_login");

function helpdesk_newReply()
{ 
	/** 
	 * Only logged in Users are allowed to acces this function 
	*/ 
	if (is_user_logged_in()){
		/** 
		 * Set UserID 
		*/ 
		$user = wp_get_current_user();
		$userID = $user->ID; 
	}else{
		echo "Your must login!"; 
		die();
	}
	
	global $wpdb; 
	$now = new DateTime(); 
	$data = $_POST;
	$insert = $wpdb->insert(
		'helpdesk_replies', 
		array(
			'content' => $data['reply']['Content'], 
			'dateCreated' => $now->format('Y-m-d H:i:s'),
			'dateModified' => $now->format('Y-m-d H:i:s'), 
			'reply_user_id' => $userID, 
			'ticket_id' => $data['reply']['Ticket'] 
		)
	);
	
	$tempTicket = $wpdb->get_row("SELECT * FROM `helpdesk_tickets` WHERE `id` = " . $data['reply']['Ticket']);
	$tempUser= get_user_by('id', $tempTicket->user_id); 
	$to = $tempUser->user_email; 
	$from = 'helpdesk@engage.com';
	$ticket = $data['reply'];
	$subject = 'Help Desk - ' . substr($data['reply']['Content'], 0, 75); 
	$text = 'Help Desk - ' . substr($data['reply']['Content'], 0, 75); 
	$verbage = 
		array(
			"New Reply From Help Desk",
			"One of your tickets has received a reply."
		);
  	helpdesk_sendcommunication($to, $from, $ticket, $subject, $text, $userID, $verbage); 
  	unset($tempUser); 
	unset($tempTicket); 
  	/** 
   		* Depending on the ModifyFlag, the Parent Ticket
   		* may be altered, usually the state
  	*/ 
	if ($data['reply']['ModifyFlag']=='send-to-user'){
		// modify the reply to ticket to reflect state change 
		// and assigned_to flag 
		$update = $wpdb->update( 
				'helpdesk_tickets', 
				array( 
					'assigned_to' => $userID,
					'state' => 'at-user', 
				), 
				array( 'id' => $data['reply']['Ticket'] ), 
				array( 
					'%d',	
					'%s'	
				), 
				array( '%d' ) 
		);
	}elseif($data['reply']['ModifyFlag']=='send-to-owner'){
  	$wpdb->update(
    	'helpdesk_tickets', 
      array(
      	'state' => 'at-owner', 
      ), 
      array('id' => $data['reply']['Ticket']),
      array(
        '%s'
      ), 
      array('%d')
    ); 
  }

	exit(json_encode($insert));  
}

function helpdesk_newReply_login()
{
	echo "You must login!"; 
	die();
}

function helpdesk_get_replies_for_ticket_normal_sort($ticket_id)
{
	global $wpdb; 
	$replies = $wpdb->get_results("SELECT * FROM `helpdesk_replies` WHERE `ticket_id` = $ticket_id ORDER BY dateCreated DESC LIMIT 0, 30;");
	return $replies;
}

function createHTMLMail($ticket, $userID, $verbage)
{
	$ticket_user = get_user_by('id', $userID);
	$ticket_username = $ticket_user->user_nicename; 
	$content = (!is_null($ticket['Desc'])) ? $ticket['Desc'] : $ticket['Content'];
	$html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"
\"http://www.w3.org/TR/html4/strict.dtd\"><html>
<head>
<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
</head>
<body>
<style>
	* { 
		font-family: Lato;
	}

	.heading {
		font-weight:bold;
	}

	em { 
		font-style: italic;
	}

	.heading {
		font-weight:bold;text-align:center;
	}

	.content {
		width:400px;margin: 0 auto;clear:both; overflow:hidden;
	}

	a {
		text-decoration: none;
	}
	p.center{
		text-align:center;
	}
</style>
<div style='font-family: \"Bodoni MT\", Didot, \"Didot LT STD\", \"Hoefler Text\", Garamond, \"Times New Roman\", serif; width:400px;margin: 0 auto;clear:both; overflow:hidden;' class='content'>
	<h1 style='font-weight:bold; text-align:center;' class='heading'>".$verbage[0]."</h1>
	<p>".$verbage[1]."</p>
	<p style='font-style: italic;'><em>Ticket Description:</em></p>
	<p>".$content."</p>
	<p><em>By</em> <span class='heading'>".$ticket_username."</span></em></p>
	<hr />
	<p style='text-align:center;' class='center'><a style='text-decoration: none;' href='http://192.81.209.140/helpdesk/wp-admin/'>View Tickets</a></p>
	<br />
	<p style='text-align:center;' class='center'><small>This email was generated by Help Desk. Please do not reply to it. <br />Made in Toronto.<br /><a href='https://twitter.com/ankutax' />@ankutax</a></small></p>
</div>
</body>
</html>";
	return $html; 
}

// Define Admin Menus
// -------------------------
//

/** Step 1. */
function helpdesk_replies_menu() 
{
	// all tickets
	add_menu_page( "Tickets", "Tickets", 'update_core', 'helpdesk-replies-menu', 'helpdesk_replies_menu_func', NULL, 3 );
	// reply
	add_submenu_page( "helpdesk-replies-menu", "Reply", "Reply", 'update_core', 'helpdesk-add-reply', 'helpdesk_reply_menu_func'); 
 
}

/** Step 2. */
add_action( 'admin_menu', 'helpdesk_replies_menu' );

/** Step 3. */
function helpdesk_replies_menu_func() 
{
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$filter = $_REQUEST['show'];
	echo '<div class="wrap">';
	?>
	<style>
	.tickets tr:hover {
		background:#f0f0f0;
		cursor: pointer;
	}
	</style>
	<div id="icon-edit-comments" class="icon32"></div><h2>Tickets</h2>
	<ul class="subsubsub">
		<li class="open">
			<a href="?page=helpdesk-replies-menu&show=open" class="<?php if ($filter=='open' || !$filter) { echo 'current'; }?>">Open <span class="count">(<?php echo returnOpenCount(); ?>)</span></a>
			 |
		</li>
		<li class="close">
			<a href="?page=helpdesk-replies-menu&show=closed" class="<?php if ($filter=='closed' ) { echo 'current'; }?>">Closed <span class="count">(<?php echo returnCounts("helpdesk_tickets", array('state' => 'closed')); ?>)</span></a>
			 |
		</li>
		<li class="all">
			<a href="?page=helpdesk-replies-menu&show=all" class="<?php if ($filter=='all') { echo 'current'; }?>">All <span class="count">(<?php echo returnCounts("helpdesk_tickets"); ?>)</span></a>
		</li>
	</ul>
	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action" id="rest_actions">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="close" class="hide-if-no-js">Close</option>
    		<option value="assign" class="hide-if-no-js">Assign</option>
    		<option value="delete" class="hide-if-no-js">Delete</option>
			</select>
			<input type="submit" name id="doaction" class="button action" value="Apply" />
		</div>
	</div>
	<table class="widefat tickets">
		<thead>
			<tr>
				<th><input type="checkbox" /></th>
				<th>User</th>
        <th>Content</th>       
        <th>Date Created</th>
        <th>Date Modified</th>
        <th>Priority</th>
        <th>State</th>
    		<th>Assigned To</th>
		  </tr>
		</thead>
		<tfoot>
			<tr>
				<th><input type="checkbox" /></th>
		    <th>User</th>
        <th>Content</th>       
        <th>Date Created</th>
        <th>Date Modified</th>
        <th>Priority</th>
        <th>State</th>
    		<th>Assigned To</th>
			</tr>
		</tfoot>
		<tbody>
			<?php 
			global $wpdb; 
  		if($filter=='closed'){
				$all_tickets = $wpdb->get_results("SELECT * FROM `helpdesk_tickets` WHERE `state` = 'closed' LIMIT 0, 20;");
      }elseif($filter=='open'){
      	$all_tickets = $wpdb->get_results("SELECT * FROM `helpdesk_tickets` WHERE `state` != 'closed' ORDER BY `dateCreated` DESC LIMIT 0, 20;");
      }elseif($filter=='all'){
      	$all_tickets = $wpdb->get_results("SELECT * FROM `helpdesk_tickets` ORDER BY `dateCreated` DESC LIMIT 0, 20;");
      } else {
      	$all_tickets = $wpdb->get_results("SELECT * FROM `helpdesk_tickets` WHERE `state` != 'closed' ORDER BY `dateCreated` DESC  LIMIT 0, 20;");
      }
			if ($all_tickets){
			foreach($all_tickets as $t){?>
			<tr>
				<th><input type="checkbox" /></th>
	    	<td><strong>
	    	<?php 
	    	$user = get_user_by('id',  $t->user_id); 
	    	echo $user->display_name;
	    	?>
	    	</strong>
	    	</td>
	    	<td>
	    	<a href="/wp-admin/admin.php?page=helpdesk-add-reply&ticket=<?php echo $t->id; ?>"><input type="submit" id="view" class="button action" name="view" value="View" /></a>&nbsp;
	    	<?php
	    	$ticketData = unserialize($t->content);
				echo stripslashes(putLineBreaks(convertToExcerpt($ticketData['Desc'])));
	    	?>
	    	</td>
	    	<td><?php echo date('M jS', strtotime($t->dateCreated)); ?> &nbsp;<?php echo date('g:h a', strtotime($t->dateCreated)); ?></td>
	    	<td><?php echo date('M jS', strtotime($t->dateModified)); ?></td>
	    	<td><span class="secondary label"><?php echo ucwords($t->priority); ?></span></td>
	    	<td><?php echo ucwords($t->state); ?></td>
        <td>
         	<?php 
          $assigned_to_user = get_user_by('id', $t->assigned_to); 
          if ($assigned_to_user)             
          	echo $assigned_to_user->display_name;
          ?>
        </td>
			</tr>
			<?php }} ?>
		</tbody>
	</table>
	<?php
	echo '</div>';
}

function helpdesk_reply_menu_func()
{ 
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	global $wpdb;
  $wpdb->show_errors(); 
	$ticketID = $_REQUEST['ticket'];
	$ticket =  $wpdb->get_row("SELECT * FROM `helpdesk_tickets` WHERE `id` = $ticketID");
	if ( !$ticket )  {
		wp_die( __( 'That ticket does not exist.' ) );
	}
	$user = get_user_by('id', $ticket->user_id);
	if ( !$user )  {
		wp_die( __( 'That user no longer exists and therefore cannot be replied to.' ) );
	}
	
	echo '<div class="wrap">';
	?>
  <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory') . '/stylesheets/helpdesk.reply.css'; ?>" />
	<div id="icon-edit-comments" class="icon32"></div><h2>Tickets</h2>
	<div class="helpdesk">
		<?php 
    		if($ticket->state=='closed'){
        	echo '<div id="Message" class="updated"><p>This ticket is closed. Replying to it will re-open it.</p></div>';
        }
    		?>
				<form name="newReply" id="newReply" method="POST" action="/">
					<ul class="subsubsub" style="float:none;">
					  <li class="open">
					    <a class="current" href="#">Public Reply</a>
					     |
					  </li>
					  <li class="close">
					    <a href="#">Internal Note</a>
					     |
					  </li>
					  <li class="all">
					    <a href="javascript:void(0);" id="helpdesk_closeTicket" data-id="<?php echo $ticket->id; ?>" class="">Close</a>
					    
					</ul> 
          <?php
						$users = get_users();
						$ticketID = $_REQUEST['ticket'];
						?>
						<select data-id="<?php echo $ticketID; ?>" id="assignment_list" name="assignment_list">
						  
						  <option value="-1" selected="selected">Delegate</option>
						
						  <?php
						  foreach($users as $user){ 
						    if($user->allcaps['administrator']) {?>
						    	<option value="<?php echo $user->ID; ?>" class="hide-if-no-js"><?php echo $user->user_nicename; ?></option>
						    <?php
						    }
						  }
						 	?>
					</select>
					<textarea name="newReply_Content" id="newReply_Content" style="width:99%" cols="10" rows="5" placeholder="Public Reply"></textarea>
          <input type="hidden" id="newReply_Ticket" class="newReply_Ticket" value="<?php echo $ticket->id; ?>" name="ticket" />
					<input type="hidden" id="newReply_ModifyFlag" value="send-to-user" />
					<input type="submit" id="submit" value="Submit" class="button button-primary button-large" />
				</form>
		<div class="ticket">
   	<div class="ticket_internal">
			<div id="new-reply-page" class="">
				<div class="replies">
					<?php 
					$replies = helpdesk_get_replies_for_ticket_normal_sort($ticketID);
					foreach($replies as $reply)
					{
					?>
					<div class="issue">
						<table class="widefat">
							<thead>
							<tr>
								<td><strong>
								<?php
								$reply_user = get_user_by('id', $reply->reply_user_id); 
								echo $reply_user->display_name;
								?>
								</strong>
								<div style="float:right">
								<?php echo date('M jS', strtotime($reply->dateCreated)); ?> &nbsp;<?php echo date('g:h a', strtotime($reply->dateCreated)); ?>
								</div>
								</td>
							</tr>
							</thead>
							<tbody>
							<tr>
							<td><?php echo stripslashes($reply->content); ?></td>
							</tr>
							</tbody>
						</table>
					</div>
					<?php
					}
					?>
				</div>
				<div class="oldschool">
					<p class="date"><?php echo date('M jS', strtotime($ticket->dateCreated)); ?> &nbsp;<?php echo date('g:h a', strtotime($ticket->dateCreated)); ?></p>
					<?php
					$ticketUser = get_user_by('id', $ticket->user_id); 
					echo "<span class='label'><strong>$ticketUser->display_name </strong></label>"
					?>
					<div class="issue">
						<table class="widefat">
							<thead>
							<tr>
							<?php
							$ticketData = unserialize($ticket->content);
							foreach($ticketData as $key => $value) { ?>
								<th><?php echo $key; ?></th>
							<?php } ?>
							</tr>
							</thead>
							<tbody>
							<tr>
							<?php
							$ticketData = unserialize($ticket->content);
							foreach($ticketData as $key => $value) { ?>
								<td><?php echo stripslashes(putLineBreaks($value)); ?></td>
							<?php } ?>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
      </div>
		</div>
		<!-- ticket end -->
	</div>
	<!-- helpdesk end -->	
	<?php
	echo '</div>';
}

add_action('admin_init','load_replynew_script');

function load_replynew_script() {
	wp_enqueue_script(
		'replynew',
		get_bloginfo('template_directory') . '/javascripts/helpdesk.replynew.js',
		array('jquery'), 
		NULL, 1
	);
	
	wp_enqueue_script(
		'rest',
		get_bloginfo('template_directory') . '/javascripts/helpdesk.rest.js',
		array('jquery'), 
		NULL, 1
	);
}

define( 'CONCATENATE_SCRIPTS', false );

function returnCounts($table, $where = "", $operator='and')
{
	global $wpdb; 
  $query = "select count(*) "; 
  $query.= "from `$table` "; 
  
  if ($where){
    $query .= " where ";
    while($where){
      foreach($where as $field => $value){
        $query.= "`$field`" . " = " . "'$value'"; 
        if (count($where)==1){
        	unset($where[$field]);
          break;
        }else{
        	$query.= " $operator ";
        }
        unset($where[$field]); 
      }
    }
  }
  $results = $wpdb->get_var($query); 
  return $results; 
}

function returnOpenCount()
{
	global $wpdb;
	$results = $wpdb->get_var("SELECT COUNT(*) FROM `helpdesk_tickets` WHERE `state` = 'at-owner' OR `state` = 'at-user' OR `state` = 'new';");
	return $results;  
}

function putLineBreaks($strData){ 
	$strReplaced = ""; 
	$newString = preg_replace("/\n/", "<br />", $strData);
	return $newString; 
} 

function convertToExcerpt($string = "", $charCount = 140)
{ 
	if (strlen($string) > $charCount){
		$newString = substr($string, 0, $charCount); 
		$newString.= "<br /><strong>…</strong>";
		$charRemaining = strlen($string) - $charCount; 
		$newString.= " <p></p><br /><em>($charRemaining characters more)</em>"; 
	return $newString; 
	} else { 
		// no modification needed
		return $string; 
	}
}




























