;(function ($, window, undefined){
  'use strict';
  
  $("#newReply").on("submit", function(e){
  	e.preventDefault(); 
  	
  	var reply = { 
  		"Content": $(this).find("#newReply_Content").val(), 
  		"Ticket": $(this).find("#newReply_Ticket").val(),
  		"ModifyFlag": $(this).find("#newReply_ModifyFlag").val()
  	};
  	
  	var data = { 
  		action: "newReply",
  		reply: reply
  	};	
  	
  	console.dir(reply); 
    
		$.post(ajaxurl, data, function(res) {
      if(res==1){
      	console.dir(res); 
 	 			$(".ticket").load(document.URL +" .ticket_internal", function(data){});
 	 			$("textarea").val("");
      }else{
        console.log("Error: Reply Error");
      }
		}, 'json');
  });
  
  $("#helpdesk_closeTicket").on("click", function(e){
  	e.preventDefault(); 
    
    var data = {
      action: "ticketActions",
      method: "close",
      ticket: $(this).attr('data-id')
    }
   	
    $.post(ajaxurl, data, function(res){ 
    	if (res.status=='fail'){
    		alert(res.error); 
    	}else{
      	alert("This ticket is closed. Replying to it will re-open it."); 
      }
      
    }, 'json');
  });
  
  $("#assignment_list").on('change', function(e){
  	e.preventDefault(); 
    
    var data = { 
      action: 'ticketActions', 
      method: "assignTo", 
      user: $(this).val(), 
      ticket: $(this).attr('data-id')
    };
    
    $.post(ajaxurl, data, function(res){
      console.log("From the server: " + res); 
      $("#Message").remove();
      
      if (res.state=='error'){
      	$("#new-reply-page").prepend('<div id="Message" class="updated"><p>'+res.message+'</p></div>');
      }
      
      if(res.state=='success'){
      	$("#new-reply-page").prepend('<div id="Message" class="updated"><p>This ticket has been re-assigned to '+res.data+'</p></div>');
      }
      
      if (!res){
        console.log("Error: Ticket assignment failed. The server didn't return any data.");	
      }
    }, 'json');
  });
  
  $("#doaction").click(function(e){
  	e.preventDefault(); 
  	
  	var action = $("#rest_actions").val(); 
  	if (action == 'delete'){ 
  		var ticket_id = 
  		//helpdesk_ticket_close();
  	}
  	
  });
  
})( jQuery, this );

