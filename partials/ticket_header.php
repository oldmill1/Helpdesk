<?php
if ($ticket->state=="at-user") {
	echo "<p><span class='round alert label'>Awaiting Your Reply</span></p>";
} elseif($ticket->state=="at-owner"){
	echo "<p><span class='radius secondary label'>Waiting for Help Desk to respond.</span></p>";
} elseif($ticket->state=='closed'){
	echo '<p><span class="radius secondary label">This ticket is closed. Replying to it will re-open it.</span><p>'; 
}