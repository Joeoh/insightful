var x = 1;

google.charts.load('current', {'packages':['corechart','line']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
	if (x==0)
	{
		x = 1;
	}
	else
	{
		x = 0;
	}
  	// Alternative approach
    var data0 = google.visualization.arrayToDataTable([
    ['Month', 'Room', 'Food', 'Service'],
	['Sep',  10, 50, 70],
	['Oct',  20, 70, 40],
	['Nov',  40, 60, 90],
	['Dec',  80, 65, 80]
    ]);

	var data1 = new google.visualization.DataTable();
	data1.addColumn('string', 'Month');
	data1.addColumn('number', 'x1');
	data1.addColumn('number', 'x2');
	data1.addColumn('number', 'x3');

	data1.addRows([
		['Sep',  50, 60, 55],
		['Oct',  65, 65, 45],
		['Nov',  75, 77, 70],
		['Dec',  60, 89, 69]
		]);


	var options = {
		title: 'Trend of Satisfaction',
		curveType: 'function',
		legend: { position: 'right' },
		theme: 'material',
		width: 900,
		height: 500
	};

    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
    //var chart = new google.charts.Line(document.getElementById('linechart_material'));

    if (x == 0)
    {
    	chart.draw(data0, /*google.charts.Line.convertOptions(options)*/options);    
    }
    else if(x == 1)
    {
    	chart.draw(data1, /*google.charts.Line.convertOptions(options)*/options);
    }
}