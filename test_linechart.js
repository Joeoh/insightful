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

	// Add noise to overlapping points in the chart.
	for (var i = 0; i < data0.getNumberOfRows(); i++)
	{
		for (var j = 1; j < data0.getNumberOfColumns(); j++)
		{
			// For each element in a column,
			var k = data0.getValue(i, j);
			// Iterate through all the elements after it,
			while (++j < data0.getNumberOfColumns())
			{
				//If any other element overlaps with it,
				if (k === data0.getValue(i,j))
				{
					//Add noise(0.5) to that other element.
					data0.setValue(i, j, data0.getValue(i,j)+0.5);
				}
			}
		}
	}


	var options = {
		title: 'Trend of Satisfaction',
		curveType: 'function',
		legend: { position: 'right' },
		theme: 'material',
		pointSize: 5,
		width: 900,
		height: 500
	};

	function selectHandler(label, key)
	{
		$(".modal-body > p").text(label + ", " + key);
		$("#myModal").modal();
	}

    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
    //var chart = new google.charts.Line(document.getElementById('linechart_material'));
    google.visualization.events.addListener(chart, 'select', function() {
    	var selection = chart.getSelection();
    	if (selection[0] !== undefined && selection[0].row !== null)
    	{
    		//display the label of the datapoint selected
    		console.log(data0.getColumnLabel(selection[0].column));
    		console.log(data0.getValue(selection[0].row,0));
    		selectHandler(data0.getColumnLabel(selection[0].column),data0.getValue(selection[0].row,0))
    		//[selection[0].row+1][selection[0].column]
    	}
    	//window.open('http://jc.netsoc.ie/deadline', '_blank');
    });

    if (x == 0)
    {
    	chart.draw(data0, /*google.charts.Line.convertOptions(options)*/options);    
    }
    else if(x == 1)
    {
    	chart.draw(data1, /*google.charts.Line.convertOptions(options)*/options);
    }
}