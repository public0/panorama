$(function() {
	var previous;
	var id;
	var val;

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
		(input.val()==1)?$(this).removeClass('btn-danger').addClass('btn-success').html('Keep'):$(this).removeClass('btn-success').addClass('btn-danger').html('Delete');

		e.preventDefault();
	})

	$('.axaj-sub').click(function() {
		var form = $(this).closest('form');


	$.post( "../../imageSave", form.serialize()).done(function( data ) {
		$( ".result" ).html( data );
		console.log(data);
	});

		return false;
	})
});