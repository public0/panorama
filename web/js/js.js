$(function() {
	var previous;
	var id;
	var val;
	var _URL = window.URL || window.webkitURL;
	$("#project_images").change(function(e) {
	    var file, img;


	    if ((file = this.files[0])) {
	        img = new Image();
	        img.onload = function() {
	        	$('#project_width').val(this.width);
	        	$('#project_height').val(Math.floor(this.width / 6));
	        	$('#project_save').val('Upload');
//	        	console.log(Math.floor(this.width / 6));

//	            alert(this.width + " " + this.height);
	        };
	        img.onerror = function() {
	            alert( "not a valid file: " + file.type);
	        };
	        img.src = _URL.createObjectURL(file);


	    }

	});


	if (typeof Stripe !== 'undefined') {
		Stripe.setPublishableKey('pk_test_N0X4Jek9xE7dGCLyOGbGJJ2A');
	}
	$(".plan").on('focus', function () {
        previous = this.value;
        id = $(this).attr('id');

    }).change(function() {
		val = $(this).val();
		$(this).find('option[value='+previous+']').removeAttr('selected');
		$(this).find('option[value='+val+']').attr('selected', true);

		$('.plan[id!='+id+']').filter(function() {
			return $(this).val()===val;
		}).each(function(){
			$(this).find('option[value='+val+']').removeAttr('selected');
			$(this).find('option[value='+previous+']').attr('selected', true);
			$(this).val(previous);

		})   


//		console.log();
//		console.log($('.plan[value='+$(this).val()+']').val('2'));

	});

	$('.delete-row').click(function(e) {
		var input = $(this).prev();

		input.val(input.val()==1?0:1);

//		(input.val()==1)?$(this).removeClass('btn-danger').addClass('btn-success').html('Keep'):$(this).removeClass('btn-success').addClass('btn-danger').html('Discard');
		if(input.val()==1) {
			$(this).closest('tr').addClass('hidden');	
		}
		e.preventDefault();
	})

	$('.axaj-sub').click(function() {
//		var form = $(this).closest('form');
		var form = $('#ajax-update');

		$( "#loading" ).removeClass('hidden');
		$.post( "../../imageSave", form.serialize()).done(function( data ) {
		$( "#loading" ).addClass('hidden');
			switch(parseInt(data)) {
				case 0:
					$( ".result" ).html('<span class="alert alert-success glyphicon glyphicon-check">Updated</span>' );
				break;
				default:
					$( ".result" ).html('<span class="alert alert-warning glyphicon glyphicon-warning-sign">'+data+'</span>' );
				break;
			}
		});

		return false;
	})

	var $form = $('#payment-form');
		$form.submit(function(event) {
		// Disable the submit button to prevent repeated clicks:
		$form.find('.submit').prop('disabled', true);

		// Request a token from Stripe:
		if (typeof Stripe !== 'undefined') {
		Stripe.card.createToken($form, stripeResponseHandler);
		}
		// Prevent the form from being submitted:
		return false;
	});


	function stripeResponseHandler(status, response) {

		// Grab the form:
		var $form = $('#payment-form');
		if (response.error) { // Problem!
			$form.removeClass('alert alert-danger');

			// Show the errors on the form
			$form.find('.payment-errors').text(response.error.message).addClass('alert alert-danger');
			$form.find('button').prop('disabled', false); // Re-enable submission

		} else { // Token was created!

		// Get the token ID:
		var token = response.id;

		// Insert the token into the form so it gets submitted to the server:
		$form.append($('<input type="hidden" name="stripe-token" />').val(token));

		// Submit the form:
		$form.get(0).submit();

		}
	}

	$( "#confirmForm" ).submit(function( event ) {
		var form = this;
//		console.log(form);
		bootbox.confirm("<p>Delete project ?</p>", function(result){ 
			if(result) {
				form.submit();
			}
		});
		event.preventDefault();
	});	

	$('.square').each(function() {
		var image;
		if( typeof $(this).attr('data-image') !== 'undefined') {
			$(this).css('background-image', 'url('+$(this).attr('data-image')+')');
		}
	});

});