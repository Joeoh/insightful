google.charts.load('current', {'packages':['corechart','line']});
google.charts.setOnLoadCallback(drawLineChart);

function drawLineChart() {
    // Alternative approach
    /*var data = google.visualization.arrayToDataTable([
     ['Month', 'Room', 'Food', 'Service'],
     ['Sep',  50, 60, 55],
     ['Oct',  65, 65, 45],
     ['Nov',  75, 77, 70],
     ['Dec',  60, 89, 69]
     ]);*/

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Month');
    data.addColumn('number', 'Overall Satisfaction');
    //data.addColumn('number', 'Food');
    //data.addColumn('number', 'Service');

    data.addRows([
        ['Sep',  35],//, 50, 95],
        ['Oct',  45],//, 65, 75],
        ['Nov',  55],// 60, 55],
        ['Dec',  75],// 50, 35]
    ]);


    var options = {
        //title: 'Trend of Satisfaction',
        curveType: 'function',
        legend: { position: 'right' },
        theme: 'material',
        //width: 900,
        height: 500
    };

    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
    //var chart = new google.charts.Line(document.getElementById('linechart_material'));

    chart.draw(data, /*google.charts.Line.convertOptions(options)*/options);

    $(window).smartresize(function () {
        chart.draw(data, options);
    });

}
