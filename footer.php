	<footer class="row">
    <div class="large-12 columns">
      <hr />
      <div class="row">
        <div class="large-12 columns">
          <h6 class="subheader">Built with &#9829; in Toronto</h6>
        </div>
        <div class="large-7 columns">
          <ul class="inline-list right">
          </ul>
        </div>
      </div>
    </div>
  </footer>
  <script>
  document.write('<script src=/helpdesk/wp-content/themes/helpdesk/javascripts/'
    + ('__proto__' in {} ? 'zepto' : 'jquery')
    + '.js><\/script>');
</script>
	<!-- Included JS Files (Compressed) -->
  <script src="<?php bloginfo('template_directory'); ?>/javascripts/jquery.js"></script>
  <script src="<?php bloginfo('template_directory'); ?>/javascripts/foundation.min.js"></script>
  <!-- Initialize JS Plugins -->
  <script src="<?php bloginfo('template_directory'); ?>/javascripts/app.js"></script>
  <script src="<?php bloginfo('template_directory'); ?>/javascripts/foundation.reveal.js"></script>
 	<script src="<?php bloginfo('template_directory'); ?>/javascripts/foundation.joyride.js"></script> 
  <script type="text/javascript">
 	$(document).foundation(); 
  </script>
  <!-- Help Desk Files -->
  <script src="<?php bloginfo('template_directory'); ?>/javascripts/helpdesk.submitnew.js"></script>
  <script src="<?php bloginfo('template_directory'); ?>/javascripts/helpdesk.replynew.js"></script>

  <!-- At the bottom of your page but inside of the body tag -->
	<ol class="joyride-list" data-joyride>
  	<li data-id="you" data-class="custom so-awesome" data-text="Next">
    	<h4>This is you!</h4>
    	<p>You can change your picture later, hottie.</p>
  	</li>
  	<li data-id="inbox" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
    	<h4>Your Inbox</h4>
    	<p>When you get replies, we'll notify you here.</p>
  	</li>
  	<li data-id="new-ticket" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
    	<h4>Create a new ticket</h4>
    	<p>Fill out the form and you're done.</p>
  	</li>
  	<li data-id="controls" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
    	<h4>Controls</h4>
    	<p>Stroll through memory lane by browsing past tickets.</p>
  	</li>
	  <li data-button="Next">
	    <h4>You're ready!</h4>
	    <p>Thanks for using Help Desk.</p>
	  </li>
	</ol>
	<?php 
	global $tourState;
	if ($tourState == 'show'){ 
		?>
		<script type="text/javascript">
  		$(document).foundation('joyride', 'start');
		</script>
		<?php
	}
	?>
	<?php wp_footer(); ?>
</body>
</html>