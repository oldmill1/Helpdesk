<?php 
echo "<pre>";
$args = array('where' => array('user_id' => '2'), 'orderby' => array('dateCreated', 'desc')); 
print_r(helpdesk_getTicket($args)); 
echo "</pre>"; 