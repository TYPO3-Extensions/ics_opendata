jQuery.noConflict();

jQuery(document).ready(function(){
	// Lightbox
	jQuery('.tx-icsoddatastore-pi1 .tx_icsoddatastore_pi1_cgu').hide();
	jQuery('.tx-icsoddatastore-pi1 .tx_icsoddatastore_pi1_link_cgu a').click(function() {
		// Récupérer le fichier
		var href = jQuery(this).attr('rel');
		jQuery.ajax({
			type: "GET",
            url: href,
			success: function(data) {
				if (data) {
					// Ouvrir la lightbox
					var form = jQuery('.tx-icsoddatastore-pi1 .tx_icsoddatastore_pi1_cgu:first').html();
					form += data;
					form = '<div class="tx-icsoddatastore-pi1"><div class="tx_icsoddatastore_pi1_cgu">' + form + '</div></div>';
					jQuery(this).odlightbox({
						'contentHtml': form
					});
					jQuery('.od-lightbox-content .download').hide();
					// Action lorsque l'utilisateur coche la case
					jQuery('.od-lightbox-content form input:checkbox').change(function() {
						if (jQuery(this).attr('checked') == 'checked') {
							var link = jQuery(this).parent('form').attr('action');
							// Enregistrer les données de session
							jQuery.ajax({
								type: "POST",
								data: "tx_icsoddatastore_pi1[cgu]=on",
								url: link,
								success: function(data) {
									jQuery('.od-lightbox-content .download').show();
								}
							});
						}
					});
				}
			}
		});
		
		return false;
	});
});