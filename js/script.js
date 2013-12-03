(function($) {
	var open = null;

	$.blockUI.defaults.css = {
		border: 'none',
		padding: '15px', 
		backgroundColor: '#000', 
		'-webkit-border-radius': '10px', 
		'-moz-border-radius': '10px', 
		borderRadius: '10px',
		color: '#fff',
		top: '10px',
		left: '',
		right: '10px',
		textAlign: 'center',
		zIndex: 110011
	};
	$.blockUI.defaults.overlayCSS.zIndex = 110001;
	$.blockUI.defaults.centerY = 0;
	$.blockUI.defaults.message = '<img src="data:image/gif;base64,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" alt="" /><br/>Vent venligst...';

	$(function() {
		var dpSettings = {
			changeMonth: true,
			changeYear: true,
			dateFormat: "dd/mm-yy",
			yearRange: "-100:+0"
		};

		$("#preregistration-update .birthdate").datepicker(dpSettings);
		
		$("#preregistration-update").on('change', '.new', function() {
			var oldNewRow = $("#preregistration-update .new");
			var newRow = oldNewRow.clone();
			oldNewRow.removeClass("new");

			$("input[type=text], select", newRow).val("");
			$(".birthdate", newRow).removeAttr('id').removeClass('hasDatepicker');
			$(".remove", newRow).attr('disabled', false);

			$(this).closest("tbody").append(newRow);
			$(".birthdate", newRow).datepicker(dpSettings);
		});

		$("#preregistration-update").on('change', '.birthdate', function() {
			var bd = moment($(this).val(), "DD/MM-YYYY");

			// Get age as of "today"
			// var age = moment().diff(bd, 'years');
			
			// Get age as of the start of the camp
			var age = moment("2014-07-19", "YYYY-MM-DD").diff(bd, 'years');

			// Get age as of the end of the camp
			// var age = moment("2014-07-26", "YYYY-MM-DD").diff(bd, 'years');
			
			var row = $(this).closest("tr");
			var lengthSelector = $(".length", row);
			var ageSelector = $(".age", row);
			var agegroup = ageSelector.val();

			$.each(ages, function(k, v) {
				if (age >= v.age[0] && (age <= v.age[1] || v.age[1] == null))
				{
					// In this age group
					if (k != agegroup)
					{
						// The age group wasn't selected to fit this age, ask if we want to change it
						if (agegroup == "" || confirm("Vil du ændre aldersgruppen fra " + ages[agegroup].title + " til " + v.title + "?"))
						{
							lengthSelector.val(v.default);
							ageSelector.val(k).change();
						}
					}

					return false;
				}
			});
		});
		
		$("#preregistration-update").on('change', '.length, .age', function() {
			var row = $(this).closest("tr");
			var lengthSelector = $(".length", row);
			var ageSelector = $(".age", row);

			var price = ages[ageSelector.val()].price[lengthSelector.val()];
			var rates = ages[ageSelector.val()].rate[lengthSelector.val()];

			$(".price", row).data('price', price).text(convertPrice(price));
			for (var i = 0; i < rates.length; i++)
			{
				$(".rate[data-rate=" + (i+1) + "]", row).data('price', rates[i]).text(convertPrice(rates[i]));
			}

			calculateTotals();
		});

		$("#preregistration-update").on('click', '.remove', function() {
			var row = $(this).closest('tr');
			if (row.hasClass('new')) return false;

			var name = $(".name", row).val();

			if (name == '' || confirm("Er du sikker på at du vil fjerne tilmelding af " + name + "?"))
			{
				row.remove();
			}
		});

		$("#preregistration-update").on('submit', function()  {
			$.blockUI();

			var o = [];
			$("tbody tr:not(.new)", $(this)).each(function() {
				o.push({
					'name': $(".name", $(this)).val(),
					'birthdate': moment($(".birthdate", $(this)).val(), "DD/MM-YYYY").format("YYYY-MM-DD"),
					'needs': $(".needs", $(this)).val(),
					'length': $(".length", $(this)).val(),
					'age': $(".age", $(this)).val()
				});
			});

			// console.log(o); $.unblockUI(); return false;

			$("span.message").removeClass("success").removeClass("error").text("");
			$.post(page_url + "/tilmeldinger/save/" + $(this).data('userid'), {'registrations': o, 'email': $("#preregistration-update .email").val()}, function(data) {
				$("span.message").addClass(data.status ? "success" : "error").text(data.message);
				calculateTotals();
				$.unblockUI();
			}, 'json');

			return false;
		});

		$("#preregistration-overview td.group").hover(function() {
			$(".editWrapper", $(this)).show();
		}, function() {
			$(".editWrapper", $(this)).hide();
		});

		$("#preregistration-overview, #preregistration-overview #add-wrapper").on('click', '.editWrapper .edit, .editWrapper .delete, .add', function() {
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

						r.insertBefore($("#preregistration-overview tbody .new"));

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

		calculateTotals();
	});

	var calculateTotals = function() {
		if ($("tr.groupRow").length) {
			$("th.age").each(function() {
				var t = $(this);
				var a = t.data('age');
				var tpdValue = 0;

				$("td.age[data-age=" + a + "]").each(function() {
					tpdValue += parseInt($(this).text(), 10);
				});

				$("td.totalDays[data-age=" + a + "]").text(tpdValue);
			});

			var v = 0;
			$("tfoot td.totalDays").each(function() {
				v += parseInt($(this).text(), 10);
			});
			$("tfoot td.total").text(v);
		}
		else if ($(".totalhead").length)
		{
			var row = $(".totalhead").closest("tr");
			var rates = $(".rate", row).length;
			var totals = Array.apply(null, new Array(rates)).map(Number.prototype.valueOf, 0);
			var total = 0;

			$("#preregistration-update tbody tr").each(function() {
				total += $(".price", this).data('price');
				for (var i = 0; i < rates; i++)
				{
					totals[i] += $(".rate[data-rate=" + (i+1) + "]", this).data('price');
				}
			});

			$(".total.price", row).data('price', total).text(convertPrice(total));
			$("span.total").text(convertPrice(total) + "kr.");
			for (var i = 0; i < rates; i++)
			{
				$(".total.rate[data-rate=" + (i+1) + "]", row).data('price', totals[i]).text(convertPrice(totals[i]));
				$("span.rate[data-rate=" + (i+1) + "]").text(convertPrice(totals[i]) + "kr.");
			}
		}
	}

	// Courtesy of http://www.mredkj.com/javascript/numberFormat.html
	function addCommas(nStr) {
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}

	//Switches the dot and comma around
	function convertCurrency(str) {
		return str.replace(/,/g, '|').replace(/\./g, ',').replace(/\|/g, '.');
	}

	function convertPrice(price) {
		return convertCurrency(addCommas(price));
	}
})(jQuery);