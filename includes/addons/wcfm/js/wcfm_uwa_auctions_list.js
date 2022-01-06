$wcfm_uwa_auctions_table = '';
$auctions_status = '';	
$auctions_filter = '';	

jQuery(document).ready(function($){

$wcfm_bookings_table = $('#wcfm-uwa-auctions').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"bFilter"   : true,
		"responsive": true,		
		"columns"   : [
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 4 },
										{ responsivePriority: 3 },
										{ responsivePriority: 3 },
										
										
								],
		
		'ajax': {
			"type"   : "POST",
			"url"    : uwa_wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action       = 'wcfm_ajax_uwa_auction',				
				d.auctions_status = GetURLParameter( 'auction_status' ),
				d.auctions_filter = $auctions_filter,
				d.filter_date_form  = $filter_date_form,
				d.filter_date_to    = $filter_date_to
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-bookings table refresh complete
				//$( document.body ).trigger( 'updated_wcfm-bookings' );
			}
		}								
		
	} );


// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
 
});