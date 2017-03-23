google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() { var data = google.visualization.arrayToDataTable([
    ['Key Words', 'Most Positive'],
    ['Rooms',20],
    ['Staff',10],
    ['Food',15],
    ['Lounge',17],
    ['Facilities',19]
]);

    var options = {
        title: 'Positive key words'
    };

    var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));

    chart.draw(data, options);
}