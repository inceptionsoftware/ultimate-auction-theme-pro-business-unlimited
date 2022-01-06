
jQuery(document).ready(function($){


	/* Start Date */
	jQuery('#woo_ua_auction_start_date').datetimepicker({
		defaultDate: "",
		timeFormat: "HH:mm:ss", 
		dateFormat: "yy-mm-dd",
		minDate: 0 ,
		numberOfMonths: 1,		
		showMillisec : 0,
	}); 


	/* End Date */
	jQuery('#woo_ua_auction_end_date').datetimepicker({
		defaultDate: "",
		timeFormat: "HH:mm:ss", 
		dateFormat: "yy-mm-dd",
		minDate: 0 ,
		numberOfMonths: 1,		
		showMillisec : 0,
		/*beforeShow: function(){
			$("#woo_ua_auction_end_date").datetimepicker("option", {
				minDate: $("#woo_ua_auction_start_date").datetimepicker('getDate')
			});
		}*/
	});
	
	jQuery('#uwa_auction_proxy').on('change', function(){
	
		if(this.checked) {
			$('#uwa_auction_silent').prop('checked', false);		 
			$('.form-field.uwa_auction_silent_field  ').css("display", "none");
		} else {
			$('.form-field.uwa_auction_silent_field  ').css("display", "block");
		}	
	});

	jQuery('#uwa_auction_silent').on('change', function(){ 

		if(this.checked) {
			$('#uwa_auction_proxy').prop('checked', false);		 
			$('.form-field.uwa_auction_proxy_field').css("display", "none");
		} else {
			$('.form-field.uwa_auction_proxy_field').css("display", "block");
		}
	});
 
   jQuery('#uwa_auction_variable_bid_increment').on('change', function(){	
		if(this.checked) {			
			jQuery('p.uwa_variable_bid_increment_main').css("display", "block"); 
			jQuery('.uwa_custom_field_onwards_main').css("display", "block");			
			jQuery('.woo_ua_bid_increment').css("display", "none");
			jQuery('#woo_ua_bid_increment').css("display", "none");
			jQuery('#woo_ua_bid_increment').val("");			
			
		} else {
			jQuery('p.uwa_variable_bid_increment_main').css("display", "none");
			jQuery('.uwa_custom_field_onwards_main').css("display", "none");
			jQuery('.woo_ua_bid_increment').css("display", "inline-block");
			jQuery('#woo_ua_bid_increment').css("display", "inline-block");
		}	
	});
 
});