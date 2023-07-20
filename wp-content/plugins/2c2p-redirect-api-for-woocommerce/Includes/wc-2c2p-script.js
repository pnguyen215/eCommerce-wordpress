jQuery(document).ready(function($) {

    //Hide remove button when user select first option from dropdown (l'll use my new card)
    $('#wc_2c2p_stored_card').live('change', function() {
        var value = $('#wc_2c2p_stored_card').val();

        if (value === "0") {
            $('#btn_2c2p_remove').hide();
        } else {
            $('#btn_2c2p_remove').show();
        }
    });

    //Button click event
    $('#btn_2c2p_remove').live('click', function() {
		if($("#wc_2c2p_stored_card").val() === "0"){
			alert("Please select card number to delete.");
			return;
		}

		var ajaxurl  = $("#ajax_url").val();
        var tokenId = {
            'token_id': $("#wc_2c2p_stored_card").val()
        };
				
		jQuery.ajax({
                type: 'POST',
            data: {
                action: 'paymentajax',
                data: tokenId
            },
                url: ajaxurl,
                cache: false,
                success: function (data) { 
                	if(data === "0"){
                		alert("Unable to remove your card. Please try again, and let us know if the problem persists.");
                		return;
                	}

                	var isdeleted = $("#wc_2c2p_stored_card option[value="+ tokenId.token_id + "]").remove();
                	if($("#wc_2c2p_stored_card").find("option").length <= 1){
                		$("#tblToken").hide();
                	}

                	if(isdeleted.length === 0){
                		alert("Unable to remove your card. Please try again, and let us know if the problem persists.")
                } else {
                    $('#btn_2c2p_remove').hide();
                		alert("Your card has been removed successfully.");	
                	}                	
                },
                error: function(data){                	
                	alert("error" + data);
                }
            });
	});
});