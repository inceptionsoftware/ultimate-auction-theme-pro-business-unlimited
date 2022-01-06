jQuery(document).ready(function($){

 /* Start Date */
	jQuery('#uwa_relist_start_date').datetimepicker({
		defaultDate: "",
		timeFormat: "HH:mm:ss", 
		dateFormat: "yy-mm-dd",
		minDate: 0 ,
		numberOfMonths: 1,		
		showMillisec : 0,
	}); 


	/* End Date */
	jQuery('#uwa_relist_end_date').datetimepicker({
		defaultDate: "",
		timeFormat: "HH:mm:ss", 
		dateFormat: "yy-mm-dd",
		minDate: 0 ,
		numberOfMonths: 1,		
		showMillisec : 0,
		
	});
	/* End Bid Now */
	jQuery('.uwa_force_end_now').on('click', function(event){
		var auction_id = $(this).data('auction_id');		
			jQuery.ajax({
				type : "post",
				url : uwa_wcfm_params.ajax_url,
				data : {action: "uwa_admin_force_end_now", postid:auction_id },
				success: function(response) {
					var data = $.parseJSON( response ); 
					if( data.status == 1 ) {
						alert(data.success_message);
						window.location.reload();
					} else {
						alert(data.error_message);
						window.location.reload();
					}
				}

      		});
		event.preventDefault();
	});	

	/* Force make it Live */
	jQuery('.uwa_force_make_live').on('click', function(event){
		var auction_id = $(this).data('auction_id');
			jQuery.ajax({
				type : "post",
				url : uwa_wcfm_params.ajax_url,
				data : {action: "uwa_admin_force_make_live_now", auction_id:auction_id },
				success: function(response) {
					var data = $.parseJSON( response ); 
					if( data.status == 1 ) {
						alert(data.success_message);
						window.location.reload();
					} else {
						alert(data.error_message);
						window.location.reload();
					}
        		}

      		});
		event.preventDefault();
	});	
	
	/* Cancel Last bid */
	jQuery('.uwa-admin-table .bid_action a:not(.disabled)').on('click', function(event){
		var logid = $(this).data('id');
		var postid = $(this).data('postid');
		var curent = $(this);
		jQuery.ajax({
			type : "post",
			url : uwa_wcfm_params.ajax_url,
			data : {action: "admin_cancel_bid", logid : logid, postid: postid},
			success: function(response) {
				var data = $.parseJSON( response ); 
				if( data.status == 1 ) {
					alert(data.success_message);
					window.location.reload();
				} else {
			        alert(data.error_message);
					window.location.reload();
				}
			}
      	});
      	event.preventDefault();
	});
	/* Re-List Auction */
	jQuery('.uwa_force_relist').on('click', function(event){
			event.preventDefault();
			jQuery('.uwa_auction_relist_date_field').toggle();
	});
	
	/* Force make it Live */
	jQuery('.uwa_relist_now').on('click', function(event){
		var auction_id = $(this).data('auction_id');
		var start_date = jQuery('#uwa_relist_start_date').val();
		var end_date = jQuery('#uwa_relist_end_date').val();
		var valid=1;
		if (start_date=="") {
		   valid=0;
		  alert("Enter Start Date");
		}
		
		if (end_date=="") {
		   valid=0;
		  alert("Enter End Date");
		}
		if(valid==1)
            {
			jQuery.ajax({
				type : "post",
				url : uwa_wcfm_params.ajax_url,
				data : {action: "wcfm_uwa_admin_force_uwa_relist_now", auction_id:auction_id ,start_date:start_date,end_date:end_date },
				success: function(response) {
					var data = $.parseJSON( response ); 
					if( data.status == 1 ) {
						alert(data.success_message);
						window.location.reload();
					} else {
						alert(data.error_message);
						window.location.reload();
					}
        		}

      		});
		}
		event.preventDefault();
	});	
	
	/* Force choose winner */
	jQuery('.uwa_force_choose_winner').on('click', function(event){
		var is_confirm = confirm_choose_winner();

		if(is_confirm == true){

			var auction_id = $(this).data('auction_id');
			var bid_id = $(this).data('bid_id');
			var bid_user_id = $(this).data('bid_user_id');
			var bid_amount = $(this).data('bid_amount');

				jQuery.ajax({
					type : "post",
					url : uwa_wcfm_params.ajax_url,
					data : {
						action: "uwa_admin_force_choose_winner", 
						postid: auction_id,
						bid_id: bid_id,
						bid_user_id: bid_user_id,
						bid_amount: bid_amount,						
					},
					success: function(response) {
						var data = $.parseJSON( response ); 
						if( data.status == 1 ) {
							alert(data.success_message);
							window.location.reload();
						} else {
							alert(data.error_message);
							window.location.reload();
						}
					}

	      		});

			event.preventDefault();
		}
	});	


	/* Extra confirmation message on choose winner */
		function  confirm_choose_winner() {
			
			var confirm_message = "Do you really want to choose this bidder as a Winner ??";
			var result_conf = confirm(confirm_message);

			if(result_conf == false){
				event.preventDefault(); /* don't use return it reloads page */
			}
			else{
				return true;
			}	
			
		} /* end of function - confirm_choose_winner() */
	
});