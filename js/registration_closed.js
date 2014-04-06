(function($) {
	$(function() {
		$("#registration-update").on('submit', function(e)  {
			$.blockUI();

			$("span.message").removeClass("success").removeClass("error").text("");
			$("#email").removeClass('error');
			$.post(page_url + "/tilmeldinger/save/" + $(this).data('userid'), {'email': $("#registration-update .email").val()}, function(data) {
				$("span.message").addClass(data.status === true ? "success" : "error").text(data.message);
				if (data.status == -2) $("#email").addClass('error');
				$.unblockUI();
			}, 'json');

			e.preventDefault();
		});
	});
})(jQuery);
