
class ModalDelete {

	constructor(url, guids, itemText, extraHtml, callback) {

		$('#modal-delete').find('.modal-item').text(itemText);
		$('#modal-delete').find('#modal-extrahtml').html(extraHtml);

		$('#modal-delete').modal('show');

		$('#modal-delete').on('shown.bs.modal', function () {

			var input = $('#modal-delete').find('#modal-input-delete');
			var $confirmButton = $('#modal-delete').find('.modal-footer').find('#confirm-button').hide();

			input.focus();

			input.keyup(function(event){

				var upper = input.val().toUpperCase();

				input.val(upper);

				if(upper == "DELETE") {
					$confirmButton.fadeIn();
				} else if(upper != "DELETE") {
					$confirmButton.hide();
				}
			});

			$confirmButton.click(function() {
				
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					url: url,
					type: "POST",
					data : {
						guids : guids
					},
					success: function(response){
						callback(response);
					},
					error : function (xhr, ajaxOptions, thrownError) {
						callback("Error ModalDelete: " + xhr.status + " - " + xhr.responseText);
					}
				});
			});
		});

		$('#modal-delete').on('hide.bs.modal', function () {
			$('#modal-delete').find('#modal-input-delete').val('');
		});

	}

};

	   
	
	
	
	