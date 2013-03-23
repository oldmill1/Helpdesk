;(function ($, window, undefined){
  'use strict';
  
  // this script's job is to trigger a new ticket
  // and do nothing else!
  
  // hook into newTicket form's submit event loop
  $("#newTicket").submit(function(e){
  	e.preventDefault();
  	
  	var ticket = { 
  		"Website": $(this).find("#newTicket_Website").val(), 
  		"Desc": $(this).find("#newTicket_Desc").val(), 
  		"Link":  $(this).find("#newTicket_Link").val(),
  		"Priority": $(this).find("#newTicket_Priority").val(),
  	}; 
  	
  	// post request 
  	var data = { 
  		action: "newTicket",
  		ticket: ticket, 		
  	};
  	
  	$.post(ajaxurl, data)
		.done(function(res) {
		  if (res == 1){
        $("#newTicket").find("input[type=submit]").val("Hold Up").attr('disabled', 'disabled');
		  	$(".feed-place").load(document.URL +" .feed-place-inner", function(data){});
		  	$("#newTicket_WhatsBroken").val("");
		  	$("#newTicket_Desc").val("");
		  	$("#newTicket_Link").val("");
		  	$('a.close-reveal-modal').trigger('click');
		  }else{
		  }
		},'json'); 
			
  });
  
  $('a.close-reveal-modal').click(function(){
  	
  });

})( jQuery, this );

