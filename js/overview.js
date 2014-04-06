(function($) {
	$(function() {
		$("#registration-overview td.group").hover(function() {
			$(".editWrapper", $(this)).show();
		}, function() {
			$(".editWrapper", $(this)).hide();
		});

		$("#registration-overview, #registration-overview #add-wrapper").on('click', '.editWrapper .edit, .editWrapper .delete, .add', function() {
			if ($(this).hasClass('edit'))
			{
				var e = $(".editDialog.edit").clone();

				var r = $(this).closest("tr");
				$(".id", e).val(r.data('group'));
				$(".name", e).val(r.data('name'));
				$(".email", e).val(r.data('email'));
				$(".organization", e).val(r.data('organization'));
				$(".pass, .repeatPass", e).val("");
			}
			else if ($(this).hasClass('delete'))
			{
				var e = $(".editDialog.delete").clone();

				var r = $(this).closest("tr");
				$(".id", e).val(r.data('group'));
				$(".name", e).text(r.data('name') + " (" + r.data('organization').toUpperCase() + ")");
			}
			else if ($(this).hasClass('add'))
			{
				var e = $(".editDialog.add").clone();

				$(".name, .email, .pass, .repeatPass", e).val("");
				$(".organization", e).val($(".organization option:first").val());
			}
			else
			{
				return false;
			}

			$(".message", e).text("").removeClass("error").removeClass("success");
			$.fancybox({
				content: e,
				hideOnOverlayClick: false
			});

			return false;
		});

		$("body").on('click', '.editDialog .cancel', function() {
			$.fancybox.close();
		});

		$("#registration-overview #hide-wrapper").on('click', '.hide', function() {
			if ($(this).data('hidden'))
			{
				$("#registration-overview .empty").show();
				$(this).data('hidden', false).val('Skjul grupper').attr('title', 'Skjul grupper uden tilmeldinger');
			}
			else
			{
				$("#registration-overview .empty").hide();
				$(this).data('hidden', true).val('Vis grupper').attr('title', 'Vis grupper uden tilmeldinger');
			}
		})

		$("body").on('submit', '.editDialog.add form, .editDialog.edit form, .editDialog.delete form', function() {
			$.blockUI();

			var d = $(this).closest(".editDialog");
			if (d.hasClass('add'))
			{
				var o = {
					name: $(".name", $(this)).val(),
					email: $(".email", $(this)).val(),
					organization: $(".organization", $(this)).val(),
					password: $(".pass", $(this)).val(),
					repeatPass: $(".repeatPass", $(this)).val()
				};

				var d = $(this).closest("div");

				if (o.name == '')
				{
					$(".message", d).addClass('error').text('Du skal angive et navn');
					$.unblockUI();
					return false;
				}

				if (o.password == '')
				{
					$(".message", d).addClass('error').text('Du skal angive et kodeord');
					$.unblockUI();
					return false;
				}

				if (o.password != o.repeatPass)
				{
					$(".message", d).addClass('error').text('De to kodeord er ikke det samme');
					$.unblockUI();
					return false;
				}

				$.post(page_url + "/oversigt/add_user", o, function(data) {
					if (data.status)
					{
						var name = data.data['first_name'];
						var org = data.data['last_name'].substring(1, data.data['last_name'].length - 1).toLowerCase();

						var r = $("tr.groupRow.new").clone()
							.attr('data-login', data.data['user_login']).data('login', data.data['user_login'])
							.attr('data-group', data.data['id']).data('group', data.data['id'])
							.attr('data-name', data.data['first_name']).data('name', data.data['first_name'])
							.attr('data-email', data.data['user_email']).data('email', data.data['user_email'])
							.attr('data-organization', org).data('organization', org)
							.removeClass('new');

						$("a.registrations", r).attr('href', page_url + '/tilmeldinger/' + data.data['id']);
						$(".group .name", r).text(data.data['display_name']);

						r.insertBefore($("#registration-overview tbody .new"));

						$("td.group", r).hover(function() {
							$(".editWrapper", $(this)).show();
						}, function() {
							$(".editWrapper", $(this)).hide();
						});

						$.fancybox.close();
						$("span.message").addClass('success').text(data.message);
						$.unblockUI();
					}
					else
					{
						$(".message", d).addClass('error').text(data.message);
						$.unblockUI();
					}
				}, 'json');
			}
			else if (d.hasClass('edit'))
			{
				var o = {
					id: $(".id", $(this)).val(),
					name: $(".name", $(this)).val(),
					email: $(".email", $(this)).val(),
					organization: $(".organization", $(this)).val(),
					password: $(".pass", $(this)).val(),
					repeatPass: $(".repeatPass", $(this)).val()
				};

				var d = $(this).closest("div");

				if (o.password != o.repeatPass)
				{
					$(".message", d).addClass('error').text('De to kodeord er ikke det samme');
					$.unblockUI();
					return false;
				}

				$.post(page_url + "/oversigt/update_user", o, function(data) {
					if (data.status)
					{
						var name = data.data['first_name'];
						var email = data.data['user_email'];
						var org = data.data['last_name'].substring(1, data.data['last_name'].length - 1).toLowerCase();
						var r = $("tr.groupRow[data-group=" + o.id + "]")
							.attr('data-name', name).data('name', name)
							.attr('data-email', email).data('email', email)
							.attr('data-organization', org).data('organization', org);

						$(".group span.name", r).text(data.data['display_name']);

						$.fancybox.close();
						$("span.message").addClass('success').text(data.message);
						$.unblockUI();
					}
					else
					{
						$(".message", d).addClass('error').text(data.message);
						$.unblockUI();
					}
				}, 'json');
			}
			else if (d.hasClass('delete'))
			{
				var d = $(this).closest("div");
				var o = {
					id: $(".id", $(this)).val()
				};

				$.post(page_url + "/oversigt/delete_user", o, function(data) {
					if (data.status)
					{
						var r = $("tr.groupRow[data-group=" + o.id + "]").remove();

						$.fancybox.close();
						$("span.message").addClass('success').text(data.message);
						$.unblockUI();
					}
					else
					{
						$(".message", d).addClass('error').text(data.message);
						$.unblockUI();
					}
				}, 'json');
			}

			return false;
		});
	});
})(jQuery);
