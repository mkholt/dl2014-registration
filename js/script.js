var Registrations = (function($) {
	$(function() {
		calculateTotals();
	});

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
			var totals = [];
			var total = 0;

			$("#registration-update tbody tr").each(function() {
				total += $(".price", this).data('price');
				for (var i = 0; i < rates; i++)
				{
					totals[i] = (totals[i] || 0) + $(".rate[data-rate=" + (i+1) + "]", this).data('price');
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

	return {
		'calculateTotals': calculateTotals,
		'addCommas': addCommas,
		'convertCurrency': convertCurrency,
		'convertPrice': convertPrice
	}
})(jQuery);
