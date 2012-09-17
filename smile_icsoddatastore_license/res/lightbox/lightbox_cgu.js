jQuery.noConflict();

jQuery(document).ready(function(){
	jQuery('.tx-icsoddatastore-pi1 .tx_icsoddatastore_pi1_cgu').hide();
	jQuery('.tx-icsoddatastore-pi1 .tx_icsoddatastore_pi1_link_cgu a').click(function() {
		var form = jQuery('.tx-icsoddatastore-pi1 .tx_icsoddatastore_pi1_cgu:first').html();
		jQuery(this).odlightbox({
			'contentHtml': form
		});
		return false;
	});
	
});
