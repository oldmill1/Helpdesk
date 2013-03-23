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