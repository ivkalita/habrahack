$(function() {
	var ctx = document.getElementById('views-chart').getContext('2d');
	$.get('/api/v1/views', function(data) {
		var views = data.data,
			d = [],
			l = [];
		var previousHour = null;
		var previousDate = null;
		var previousHourCount = 0;
		for (var i = 0; i < views.length; i++) {
			var createdAt = new Date(views[i].created_at * 1000);
			if (previousDate === null || previousDate.getHours() != createdAt.getHours()) {
				if (previousHour !== null) {
					d.push({x: previousHour, y: previousHourCount});
				}
				createdAt.setMinutes(0);
				createdAt.setSeconds(0);
				previousDate = createdAt;
				previousHour = createdAt.toLocaleTimeString();
				l.push(previousHour);
				previousHourCount = 0;
			}
			previousHourCount++;
		}
		if (previousHourCount > 0) {
			d.push({x: previousHour, y: previousHourCount});
		}
		var viewsChart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: l,
				datasets: [{
					label: 'Количество просмотров',
					data: d,
					backgroundColor: 'rgba(230, 175, 75, 0.4)',
					borderColor: 'rgba(230, 175, 75, 1)'
				}]
			}
		});
	});
});