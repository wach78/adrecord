/**
 * 
 */
$( document ).ready(function() {
	var urlroot = $('#jsurlroot').val();
	var csrfToken = $('#csrf').find('input[name="csrf_token"]').val();

	$('.selectpicker').selectpicker({
		style: 'btn-info',
		size: 4,
		includeSelectAllOption: true,
		showSubtext: true,
		validateHiddenInputs: true,
		iconBase: 'fa',
	  });


	  $('body').on('click','#btncreatepost',function(e){
		$('#bpvalue').val('');
		$('#bpheading').val('');

		$('#modalcreatepost').modal('show');
	});

	$('body').on('click', '#sub', function (e) {

		let heading = $('#bpheading').val();
		let value = $('#bpvalue').val();
		let csrftoken = $('input[name="csrf_token"]').val();

		let origin = window.location.origin;
		$.ajax({
			url: origin+'/blog/blogs/ajaxcreatepost/',
			type: "POST",
			data:{
				value : value,
				heading : heading,
				csrf_token : csrftoken
			},
			success: function (response) {

				console.log(response);

				$('#modalcreatepost').modal('hide');
				location.reload(true);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});
	});

	$('body').on('click', '.delpost', function (e) {
	

		let id = $(this).val();
		let csrftoken = $('input[name="csrf_token"]').val();
		let origin = window.location.origin;
		$.ajax({
			url: origin+'/blog/blogs/ajaxdelpost/',
			type: "POST",
			data:{
				id : id,
				csrf_token : csrftoken
			},
			success: function (response) {

				console.log(response);
				window.location.reload(true);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});
	});

	var blogpostID;
	$('body').on('click','.comments',function(e){
		$('#bpcomment').val('');
		blogpostID = $(this).val();

		console.log(blogpostID);
	});

	$('body').on('click','.savecomment',function(e){

		let csrftoken = $('input[name="csrf_token"]').val();
		let comment = $('#bpcomment').val();

		let origin = window.location.origin;
		$.ajax({
			url: origin+'/blog/blogs/ajaxaddcomment/',
			type: "POST",
			data:{
				blogpostID : blogpostID,
				comment    : comment,
				csrf_token : csrftoken
			},
			success: function (response) {

				console.log(response);
				$('#mcc').modal('hide');
				location.reload(true);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});

	});

	$('body').on('click','.showcomments',function(e){
		let id = $(this).val();
		console.log(id);
		$("#togglecom"+ id).toggle();
	  });

	  $('body').on('click','.deltmpdata',function(){
		let div = $(this).closest('div');
		$(div).remove();
	});

	setTimeout(function(){
		if ($('#tmpdata').length > 0) {
		  $('#tmpdata').fadeOut(4000);
		}
	  }, 3000)

});

function ConfirmDelete()
{
  return  confirm("Är du säker på att du vill tabort?");
}

