(function($) {
	$(function() {
		var dpSettings = {
			changeMonth: true,
			changeYear: true,
			dateFormat: "dd/mm-yy",
			yearRange: "-100:+0"
		};

        var changeAge = function() {
            var row = $(this).closest("tr");
            var lengthSelector = $(".length", row);
            var ageSelector = $(".age", row);

            if (ageSelector.val() == '')
            {
                $(".price", row).data('price', 0).text(Registrations.convertPrice(0));
                for (var i = 0; i < rate_count; i++)
                {
                    $(".rate[data-rate=" + (i+1) + "]", row).data('price', 0).text(Registrations.convertPrice(0));
                }
            }
            else
            {
                var price = ages[ageSelector.val()]['price'][lengthSelector.val()];
                var rates = ages[ageSelector.val()]['rate'][lengthSelector.val()];

                $(".price", row).data('price', price).text(Registrations.convertPrice(price));
                for (var i = 0; i < rates.length; i++)
                {
                    $(".rate[data-rate=" + (i+1) + "]", row).data('price', rates[i]).text(Registrations.convertPrice(rates[i]));
                }
            }

            Registrations.calculateTotals();
        };

		$("#registration-update .birthdate").datepicker(dpSettings);
		
		$("#registration-update")
            .on('change', '.new', function() {
                var oldNewRow = $("#registration-update .new");
                var newRow = oldNewRow.clone();
                oldNewRow.removeClass("new");

                $("input[type=text], select", newRow).val("");
                $(".birthdate", newRow).removeAttr('id').removeClass('hasDatepicker');
                $(".remove", newRow).prop('disabled', false);

                $(this).closest("tbody").append(newRow);
                $(".birthdate", newRow).datepicker(dpSettings);
                changeAge.apply($(".age", newRow));
            })
            .on('change', '.birthdate', function() {
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
                            if (agegroup == "" || confirm("Vil du ændre aldersgruppen fra " + ages[agegroup]['title'] + " til " + v['title'] + "?"))
                            {
                                lengthSelector.val(v['default']);
                                ageSelector.val(k).change();
                            }
                        }

                        return false;
                    }
                });
            })
            .on('change', '.length, .age', changeAge)
            .on('click', '#markFinal', function() {
                $.blockUI();

                $("span.message").removeClass("success").removeClass("error").text("");
                var userId = $(this).closest('form').data('userid');
                $.post(page_url + "/tilmeldinger/finalize/" + userId, function(data) {
                    $("span.message").addClass(data.status === true ? "success" : "error").text(data.message);

                    $(".register-status").load(page_url + "/tilmeldinger/table/" + userId, function(data) {
                        $.unblockUI();
                    });
                });
            })
            .on('click', '#unmarkFinal', function() {
                $.blockUI();

                $("span.message").removeClass("success").removeClass("error").text("");
                var userId = $(this).closest('form').data('userid');
                $.post(page_url + "/tilmeldinger/unfinalize/" + userId, function(data) {
                    $("span.message").addClass(data.status === true ? "success" : "error").text(data.message);

                    $(".register-status").load(page_url + "/tilmeldinger/table/" + userId, function(data) {
                        $.unblockUI();
                    });
                });
            })
            .on('click', '.remove', function() {
                var row = $(this).closest('tr');
                if (row.hasClass('new')) return false;

                var name = $(".name", row).val();

                if (name == '' || confirm("Er du sikker på at du vil fjerne tilmeldingen af " + name + "?"))
                {
                    row.remove();
                    Registrations.calculateTotals();
                }
            })
            .on('submit', function(e)  {
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

                $("span.message").removeClass("success").removeClass("error").text("");
                $("#email").removeClass('error');
                $.post(page_url + "/tilmeldinger/save/" + $(this).data('userid'), {'registrations': o, 'email': $("#registration-update .email").val()}, function(data) {
                    $("span.message").addClass(data.status === true ? "success" : "error").text(data.message);
                    if (data.status == -2) $("#email").addClass('error');
                    Registrations.calculateTotals();
                    $.unblockUI();
                }, 'json');

                e.preventDefault();
            });
	});
})(jQuery);
