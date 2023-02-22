jQuery(document).ready(function($) {
	// Trigger when one of the buttons is clicked
	$('#custom_internal_select_all').click(function(){
		custom_epl_select_all( 'internal' );
	});
	$('#custom_external_select_all').click(function(){
		custom_epl_select_all( 'external' );
	});
	$('#custom_extras_select_all').click(function(){
		custom_epl_select_all( 'extras' );
	});
	
	// Check the checkboxes which match the ID
	function custom_epl_select_all( type ) {
		$('input:checkbox[id^=custom_'+type+'_]').each(function(){
			this.checked = true; 
		});
	}
});
	
	