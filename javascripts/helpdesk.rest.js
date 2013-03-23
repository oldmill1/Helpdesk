;(function ($, window, undefined){
  'use strict';
  	
 	function helpdesk_ticket_close(ticket_id)
 	{ 
 		var data = {
      action: "ticketActions",
      method: "close",
      ticket: ticket_id
    }
   	
    $.post(ajaxurl, data, function(res){ 
    	if (res.status=='fail'){
    		return res; 
    	}else{
      	return 1; 
      }
      
    }, 'json');
 	}
})( jQuery, this );

